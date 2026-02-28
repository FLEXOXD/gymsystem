<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CashierQuotaService;
use App\Services\PlanAccessService;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GymStaffController extends Controller
{
    private const STAFF_SCHEMA_ERROR_MESSAGE = 'Faltan migraciones de staff/cajeros. Ejecuta: php artisan migrate --force';

    public function __construct(
        private readonly CashierQuotaService $cashierQuotaService,
        private readonly PlanAccessService $planAccessService
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $isGlobalScope = ActiveGymContext::isGlobal($request);

        if (! $this->isStaffSchemaReady()) {
            return $this->renderSchemaRequiredView($request);
        }

        if ($isGlobalScope) {
            $hubGymId = $this->resolveGymId($request);
            $scopeGymIds = ActiveGymContext::ids($request);
            $scopeGymCount = count($scopeGymIds);

            $cashiers = User::query()
                ->whereIn('gym_id', $scopeGymIds)
                ->where('role', User::ROLE_CASHIER)
                ->with(['gym:id,name,slug'])
                ->orderByDesc('is_active')
                ->orderBy('gym_id')
                ->orderByDesc('id')
                ->get([
                    'id',
                    'gym_id',
                    'name',
                    'email',
                    'created_at',
                    'last_login_at',
                    'role',
                    'is_active',
                    'can_open_cash',
                    'can_close_cash',
                    'can_manage_cash_movements',
                ]);

            $totalCashiers = (int) $cashiers->count();
            $activeCashiers = (int) $cashiers->where('is_active', true)->count();
            $inactiveCashiers = max(0, $totalCashiers - $activeCashiers);

            return view('staff.index', [
                'cashiers' => $cashiers,
                'currentPlanKey' => $this->planAccessService->currentPlanKeyForGym($hubGymId),
                'maxCashiers' => 0,
                'currentCashiers' => $activeCashiers,
                'remainingCashiers' => 0,
                'inactiveCashiers' => $inactiveCashiers,
                'roleSchemaReady' => true,
                'isGlobalStaffView' => true,
                'scopeGymCount' => $scopeGymCount,
                'totalCashiers' => $totalCashiers,
                'activeCashiers' => $activeCashiers,
            ]);
        }

        $gymId = $this->resolveGymId($request);
        $currentPlanKey = $this->planAccessService->currentPlanKeyForGym($gymId);
        $maxCashiers = $this->cashierQuotaService->maxForGym($gymId);
        $currentCashiers = $this->cashierQuotaService->countForGym($gymId);
        $remainingCashiers = max(0, $maxCashiers - $currentCashiers);

        $cashiers = User::query()
            ->where('gym_id', $gymId)
            ->where('role', User::ROLE_CASHIER)
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get([
                'id',
                'name',
                'email',
                'created_at',
                'last_login_at',
                'role',
                'is_active',
                'can_open_cash',
                'can_close_cash',
                'can_manage_cash_movements',
            ]);

        $inactiveCashiers = (int) $cashiers
            ->where('is_active', false)
            ->count();

        return view('staff.index', [
            'cashiers' => $cashiers,
            'currentPlanKey' => $currentPlanKey,
            'maxCashiers' => $maxCashiers,
            'currentCashiers' => $currentCashiers,
            'remainingCashiers' => $remainingCashiers,
            'inactiveCashiers' => $inactiveCashiers,
            'roleSchemaReady' => true,
            'isGlobalStaffView' => false,
        ]);
    }

    public function storeCashier(Request $request): RedirectResponse
    {
        if (! $this->isStaffSchemaReady()) {
            return redirect()
                ->route('panel.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => self::STAFF_SCHEMA_ERROR_MESSAGE]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'Selecciona una sede especifica para crear cajeros.']);
        }

        $gymId = $this->resolveGymId($request);
        $maxCashiers = $this->cashierQuotaService->maxForGym($gymId);
        if ($maxCashiers <= 0) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['cashier_quota' => 'Tu plan actual no permite crear cajeros.'])
                ->withInput();
        }

        if (! $this->cashierQuotaService->canCreateForGym($gymId)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['cashier_quota' => 'Ya alcanzaste el cupo maximo de cajeros para este plan.'])
                ->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ], [
            'name.required' => 'Ingresa el nombre del cajero.',
            'name.max' => 'El nombre no puede superar 120 caracteres.',
            'email.required' => 'Ingresa el correo del cajero.',
            'email.email' => 'Ingresa un correo electronico valido.',
            'email.max' => 'El correo no puede superar 150 caracteres.',
            'email.unique' => 'Ese correo ya esta registrado.',
            'password.required' => 'Ingresa la contrasena del cajero.',
            'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'password.max' => 'La contrasena no puede superar 72 caracteres.',
            'password.confirmed' => 'La confirmacion de contrasena no coincide.',
        ]);

        User::query()->create([
            'gym_id' => $gymId,
            'name' => trim((string) $data['name']),
            'email' => strtolower(trim((string) $data['email'])),
            'password' => (string) $data['password'],
            'role' => User::ROLE_CASHIER,
            'is_active' => true,
            'can_open_cash' => false,
            'can_close_cash' => false,
            'can_manage_cash_movements' => true,
        ]);

        return redirect()
            ->route('staff.index', $this->staffRouteParams($request))
            ->with('status', 'Cajero creado correctamente. Por defecto no puede abrir/cerrar caja.');
    }

    public function updateCashierPermissions(Request $request, string $contextGym, int $cashier): RedirectResponse
    {
        if (! $this->isStaffSchemaReady()) {
            return redirect()
                ->route('panel.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => self::STAFF_SCHEMA_ERROR_MESSAGE]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'Selecciona una sede especifica para actualizar permisos de cajeros.']);
        }

        $gymId = $this->resolveGymId($request);
        $cashierUser = $this->resolveCashierUser($gymId, $cashier);

        $data = $request->validate([
            'can_open_cash' => ['nullable', 'boolean'],
            'can_close_cash' => ['nullable', 'boolean'],
            'can_manage_cash_movements' => ['nullable', 'boolean'],
        ]);

        $cashierUser->update([
            'can_open_cash' => (bool) ($data['can_open_cash'] ?? false),
            'can_close_cash' => (bool) ($data['can_close_cash'] ?? false),
            'can_manage_cash_movements' => (bool) ($data['can_manage_cash_movements'] ?? false),
        ]);

        return redirect()
            ->route('staff.index', $this->staffRouteParams($request))
            ->with('status', 'Permisos de caja actualizados para el cajero.');
    }

    public function updateCashierPassword(Request $request, string $contextGym, int $cashier): RedirectResponse
    {
        if (! $this->isStaffSchemaReady()) {
            return redirect()
                ->route('panel.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => self::STAFF_SCHEMA_ERROR_MESSAGE]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'Selecciona una sede especifica para actualizar cajeros.']);
        }

        $gymId = $this->resolveGymId($request);
        $cashierUser = $this->resolveCashierUser($gymId, $cashier);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ], [
            'password.required' => 'Ingresa la nueva contrasena.',
            'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
            'password.max' => 'La contrasena no puede superar 72 caracteres.',
            'password.confirmed' => 'La confirmacion de contrasena no coincide.',
        ]);

        $cashierUser->update([
            'password' => (string) $data['password'],
            'remember_token' => null,
        ]);

        return redirect()
            ->route('staff.index', $this->staffRouteParams($request))
            ->with('status', 'Contrasena del cajero actualizada.');
    }

    public function disableCashier(Request $request, string $contextGym, int $cashier): RedirectResponse
    {
        if (! $this->isStaffSchemaReady()) {
            return redirect()
                ->route('panel.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => self::STAFF_SCHEMA_ERROR_MESSAGE]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'Selecciona una sede especifica para actualizar cajeros.']);
        }

        $gymId = $this->resolveGymId($request);
        $cashierUser = $this->resolveCashierUser($gymId, $cashier);

        if ((int) ($request->user()?->id ?? 0) === (int) $cashierUser->id) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'No puedes desactivar tu propio usuario desde esta pantalla.']);
        }

        $cashierUser->update([
            'is_active' => false,
            'remember_token' => null,
        ]);

        return redirect()
            ->route('staff.index', $this->staffRouteParams($request))
            ->with('status', 'Cajero desactivado. El cupo fue liberado.');
    }

    public function activateCashier(Request $request, string $contextGym, int $cashier): RedirectResponse
    {
        if (! $this->isStaffSchemaReady()) {
            return redirect()
                ->route('panel.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => self::STAFF_SCHEMA_ERROR_MESSAGE]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'Selecciona una sede especifica para actualizar cajeros.']);
        }

        $gymId = $this->resolveGymId($request);
        $cashierUser = $this->resolveCashierUser($gymId, $cashier);

        if ((bool) $cashierUser->is_active) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->with('status', 'El cajero ya estaba activo.');
        }

        if (! $this->cashierQuotaService->canCreateForGym($gymId)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['cashier_quota' => 'No hay cupo disponible para reactivar este cajero.']);
        }

        $cashierUser->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('staff.index', $this->staffRouteParams($request))
            ->with('status', 'Cajero reactivado correctamente.');
    }

    public function destroyCashier(Request $request, string $contextGym, int $cashier): RedirectResponse
    {
        if (! $this->isStaffSchemaReady()) {
            return redirect()
                ->route('panel.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => self::STAFF_SCHEMA_ERROR_MESSAGE]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'Selecciona una sede especifica para actualizar cajeros.']);
        }

        $gymId = $this->resolveGymId($request);
        $cashierUser = $this->resolveCashierUser($gymId, $cashier);

        if ((int) ($request->user()?->id ?? 0) === (int) $cashierUser->id) {
            return redirect()
                ->route('staff.index', $this->staffRouteParams($request))
                ->withErrors(['staff' => 'No puedes eliminar tu propio usuario desde esta pantalla.']);
        }

        DB::transaction(function () use ($gymId, $cashierUser): void {
            $systemUser = $this->resolveOrCreateSystemUserForGym($gymId);
            $this->reassignUserReferences(
                gymId: $gymId,
                fromUserId: (int) $cashierUser->id,
                toUserId: (int) $systemUser->id
            );

            $cashierUser->delete();
        });

        return redirect()
            ->route('staff.index', $this->staffRouteParams($request))
            ->with('status', 'Cajero eliminado permanentemente. Sus datos quedaron reasignados al sistema del gimnasio.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function resolveCashierUser(int $gymId, int $cashierId): User
    {
        return User::query()
            ->where('gym_id', $gymId)
            ->where('role', User::ROLE_CASHIER)
            ->findOrFail($cashierId);
    }

    /**
     * @return array{contextGym:string}
     */
    private function staffRouteParams(Request $request): array
    {
        $contextGym = trim((string) ($request->route('contextGym') ?? ''));
        if ($contextGym === '') {
            $contextGym = trim((string) ($request->user()?->gym?->slug ?? ''));
        }

        $params = ['contextGym' => $contextGym];
        if (ActiveGymContext::isGlobal($request)) {
            $params['scope'] = 'global';
        }

        $pwaMode = strtolower(trim((string) $request->query('pwa_mode', '')));
        if ($pwaMode === 'standalone') {
            $params['pwa_mode'] = 'standalone';
        }

        return $params;
    }

    private function isStaffSchemaReady(): bool
    {
        return Schema::hasColumns('users', [
            'role',
            'is_active',
            'can_open_cash',
            'can_close_cash',
            'can_manage_cash_movements',
        ]);
    }

    private function renderSchemaRequiredView(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $currentPlanKey = $this->planAccessService->currentPlanKeyForGym($gymId);

        return view('staff.index', [
            'cashiers' => new Collection(),
            'currentPlanKey' => $currentPlanKey,
            'maxCashiers' => 0,
            'currentCashiers' => 0,
            'remainingCashiers' => 0,
            'inactiveCashiers' => 0,
            'roleSchemaReady' => false,
            'schemaErrorMessage' => self::STAFF_SCHEMA_ERROR_MESSAGE,
            'isGlobalStaffView' => ActiveGymContext::isGlobal($request),
        ]);
    }

    private function resolveOrCreateSystemUserForGym(int $gymId): User
    {
        $systemEmail = 'sistema.gym'.$gymId.'@local.gymsystem.internal';

        $existing = User::query()
            ->where('gym_id', $gymId)
            ->whereRaw('LOWER(email) = ?', [strtolower($systemEmail)])
            ->first();

        if ($existing) {
            return $existing;
        }

        return User::query()->create([
            'gym_id' => $gymId,
            'name' => 'Sistema Gimnasio',
            'email' => $systemEmail,
            'password' => Str::random(64),
            'role' => User::ROLE_EMPLOYEE,
            'is_active' => false,
            'can_open_cash' => false,
            'can_close_cash' => false,
            'can_manage_cash_movements' => false,
        ]);
    }

    private function reassignUserReferences(int $gymId, int $fromUserId, int $toUserId): void
    {
        if ($fromUserId <= 0 || $toUserId <= 0 || $fromUserId === $toUserId) {
            return;
        }

        if (Schema::hasTable('cash_sessions')) {
            if (Schema::hasColumns('cash_sessions', ['gym_id', 'opened_by'])) {
                DB::table('cash_sessions')
                    ->where('gym_id', $gymId)
                    ->where('opened_by', $fromUserId)
                    ->update(['opened_by' => $toUserId]);
            }
            if (Schema::hasColumns('cash_sessions', ['gym_id', 'closed_by'])) {
                DB::table('cash_sessions')
                    ->where('gym_id', $gymId)
                    ->where('closed_by', $fromUserId)
                    ->update(['closed_by' => $toUserId]);
            }
        }

        if (Schema::hasTable('cash_movements') && Schema::hasColumns('cash_movements', ['gym_id', 'created_by'])) {
            DB::table('cash_movements')
                ->where('gym_id', $gymId)
                ->where('created_by', $fromUserId)
                ->update(['created_by' => $toUserId]);
        }

        if (Schema::hasTable('attendances') && Schema::hasColumns('attendances', ['gym_id', 'created_by'])) {
            DB::table('attendances')
                ->where('gym_id', $gymId)
                ->where('created_by', $fromUserId)
                ->update(['created_by' => $toUserId]);
        }

        if (Schema::hasTable('subscription_notifications') && Schema::hasColumns('subscription_notifications', ['gym_id', 'created_by'])) {
            DB::table('subscription_notifications')
                ->where('gym_id', $gymId)
                ->where('created_by', $fromUserId)
                ->update(['created_by' => $toUserId]);
        }

        if (Schema::hasTable('contact_suggestions')) {
            if (Schema::hasColumns('contact_suggestions', ['gym_id', 'user_id'])) {
                DB::table('contact_suggestions')
                    ->where('gym_id', $gymId)
                    ->where('user_id', $fromUserId)
                    ->update(['user_id' => $toUserId]);
            }
            if (Schema::hasColumns('contact_suggestions', ['gym_id', 'reviewed_by'])) {
                DB::table('contact_suggestions')
                    ->where('gym_id', $gymId)
                    ->where('reviewed_by', $fromUserId)
                    ->update(['reviewed_by' => $toUserId]);
            }
        }
    }
}
