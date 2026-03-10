<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCashMovementRequest;
use App\Http\Requests\CloseCashSessionRequest;
use App\Http\Requests\OpenCashSessionRequest;
use App\Modules\Cash\Actions\AddCashMovementAction;
use App\Modules\Cash\Actions\CloseCashSessionAction;
use App\Modules\Cash\Actions\OpenCashSessionAction;
use App\Modules\Cash\Services\CashSessionReadService;
use App\Models\CashSession;
use App\Models\GymBranchLink;
use App\Services\CashSessionService;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CashController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService,
        private readonly CashSessionReadService $cashSessionReadService,
        private readonly OpenCashSessionAction $openCashSessionAction,
        private readonly AddCashMovementAction $addCashMovementAction,
        private readonly CloseCashSessionAction $closeCashSessionAction
    ) {
    }

    /**
     * Cash main screen (open/operate/close current session).
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $cashGuard = $this->resolveCashGuard($request, $gymId);

        if ($isGlobalScope) {
            $this->autoCloseExpiredSessionsForGyms($gymIds);

            $sessions = CashSession::query()
                ->forGyms($gymIds)
                ->select([
                    'id',
                    'gym_id',
                    'opened_by',
                    'closed_by',
                    'opened_at',
                    'closed_at',
                    'opening_balance',
                    'closing_balance',
                    'expected_balance',
                    'difference',
                    'status',
                    'closing_notes',
                    'difference_reason',
                    'close_source',
                ])
                ->with(['gym:id,name,slug', 'openedBy:id,name', 'closedBy:id,name'])
                ->orderByDesc('opened_at')
                ->paginate(20)
                ->withQueryString();

            return view('cash.index', [
                'sessions' => $sessions,
            ]);
        }

        $openSession = $this->cashSessionService->getOpenSession($gymId);
        $user = $request->user();
        $canOpenCash = (bool) ($user?->canOpenCashBox());
        $canCloseCash = (bool) ($user?->canCloseCashBox());
        $canManageMovements = (bool) ($user?->canManageCashMovements());

        $summary = [
            'income_total' => 0.0,
            'expense_total' => 0.0,
            'expected_balance' => 0.0,
            'movements_count' => 0,
            'income_count' => 0,
            'expense_count' => 0,
        ];
        $methodTotals = collect();
        $latestMovements = collect();
        $recentClosedSessions = CashSession::query()
            ->forGym($gymId)
            ->where('status', 'closed')
            ->select([
                'id',
                'gym_id',
                'closed_by',
                'closed_at',
                'closing_balance',
                'difference',
                'closing_notes',
                'difference_reason',
                'close_source',
            ])
            ->with(['closedBy:id,name'])
            ->orderByDesc('closed_at')
            ->limit(8)
            ->get();

        if ($openSession) {
            $summary = $this->cashSessionReadService->buildSessionSummary($gymId, $openSession->id, (float) $openSession->opening_balance);
            $methodTotals = $this->cashSessionReadService->buildMethodTotals($gymId, $openSession->id);
            $latestMovements = $this->cashSessionReadService->latestMovements($gymId, $openSession->id);
        }

        return view('cash.index', [
            'openSession' => $openSession,
            'summary' => $summary,
            'methodTotals' => $methodTotals,
            'latestMovements' => $latestMovements,
            'recentClosedSessions' => $recentClosedSessions,
            'cashWriteBlocked' => (bool) ($cashGuard['blocked'] ?? false),
            'cashWriteBlockedReason' => (string) ($cashGuard['reason'] ?? ''),
            'canOpenCash' => $canOpenCash,
            'canCloseCash' => $canCloseCash,
            'canManageMovements' => $canManageMovements,
        ]);
    }

    /**
     * Open a new session for current gym.
     */
    public function open(OpenCashSessionRequest $request): RedirectResponse
    {
        try {
            $gymId = $this->resolveGymId($request);
            $this->assertCashWriteAccess($request, $gymId);
            $data = $request->validated();

            $this->openCashSessionAction->execute(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                data: $data
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cash.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('cash.index')
            ->with('status', 'Turno de caja abierto correctamente.');
    }

    /**
     * Add movement to current open session.
     */
    public function addMovement(AddCashMovementRequest $request): RedirectResponse
    {
        try {
            $gymId = $this->resolveGymId($request);
            $this->assertCashWriteAccess($request, $gymId);
            $data = $request->validated();

            $this->addCashMovementAction->execute(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                data: $data
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cash.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('cash.index')
            ->with('status', 'Movimiento registrado correctamente.');
    }

    /**
     * Close current open session.
     */
    public function close(CloseCashSessionRequest $request): RedirectResponse
    {
        try {
            $gymId = $this->resolveGymId($request);
            $this->assertCashWriteAccess($request, $gymId);
            $data = $request->validated();

            $session = $this->closeCashSessionAction->execute(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                data: $data
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cash.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('cash.sessions.show', $session->id)
            ->with('status', 'Turno de caja cerrado correctamente.');
    }

    /**
     * Show session history for current gym.
     */
    public function sessions(Request $request): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $this->autoCloseExpiredSessionsForGyms($gymIds);

        $sessions = CashSession::query()
            ->forGyms($gymIds)
            ->select([
                'id',
                'gym_id',
                'opened_by',
                'closed_by',
                'opened_at',
                'closed_at',
                'opening_balance',
                'closing_balance',
                'expected_balance',
                'difference',
                'status',
                'closing_notes',
                'difference_reason',
                'close_source',
            ])
            ->with(['gym:id,name,slug', 'openedBy:id,name', 'closedBy:id,name'])
            ->orderByDesc('opened_at')
            ->paginate(20);

        return view('cash.sessions', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * Show one session detail with movements and totals.
     */
    public function show(Request $request, string $contextGym, int $session): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $this->autoCloseExpiredSessionsForGyms($gymIds);

        $sessionModel = CashSession::query()
            ->forGyms($gymIds)
            ->select([
                'id',
                'gym_id',
                'opened_by',
                'closed_by',
                'opened_at',
                'closed_at',
                'opening_balance',
                'closing_balance',
                'expected_balance',
                'difference',
                'status',
                'notes',
                'closing_notes',
                'difference_reason',
                'close_source',
            ])
            ->with([
                'openedBy:id,name',
                'closedBy:id,name',
                'movements' => fn ($query) => $query
                    ->select([
                        'id',
                        'gym_id',
                        'cash_session_id',
                        'membership_id',
                        'created_by',
                        'type',
                        'amount',
                        'method',
                        'description',
                        'occurred_at',
                    ])
                    ->orderByDesc('occurred_at')
                    ->with([
                        'createdBy:id,name',
                        'membership:id,client_id',
                        'membership.client:id,first_name,last_name',
                    ]),
            ])
            ->findOrFail($session);

        $summary = $this->cashSessionReadService->buildSessionSummary((int) $sessionModel->gym_id, $sessionModel->id, (float) $sessionModel->opening_balance);
        $methodTotals = $this->cashSessionReadService->buildMethodTotals((int) $sessionModel->gym_id, $sessionModel->id);

        return view('cash.show', [
            'session' => $sessionModel,
            'summary' => $summary,
            'methodTotals' => $methodTotals,
        ]);
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    /**
     * @return array{blocked:bool,reason:string}
     */
    private function resolveCashGuard(Request $request, int $activeGymId): array
    {
        if (ActiveGymContext::isGlobal($request)) {
            return [
                'blocked' => true,
                'reason' => 'Selecciona una sucursal específica para operar caja.',
            ];
        }

        if ((bool) $request->attributes->get('gym_context_is_branch', false)) {
            return [
                'blocked' => true,
                'reason' => 'La caja de esta sucursal la gestiona la sede principal.',
            ];
        }

        $link = GymBranchLink::query()
            ->where('branch_gym_id', $activeGymId)
            ->where('status', 'active')
            ->first(['id', 'hub_gym_id', 'cash_managed_by_hub']);

        if (! $link || ! (bool) $link->cash_managed_by_hub) {
            return [
                'blocked' => false,
                'reason' => '',
            ];
        }

        $operatorGymId = (int) ($request->user()?->gym_id ?? 0);
        if ($operatorGymId === (int) $link->hub_gym_id) {
            return [
                'blocked' => false,
                'reason' => '',
            ];
        }

        return [
            'blocked' => true,
            'reason' => 'La caja de esta sucursal la gestiona la sede principal.',
        ];
    }

    private function assertCashWriteAccess(Request $request, int $activeGymId): void
    {
        $guard = $this->resolveCashGuard($request, $activeGymId);
        if ((bool) ($guard['blocked'] ?? false)) {
            throw new RuntimeException((string) ($guard['reason'] ?? 'No autorizado para operar caja en esta sucursal.'));
        }
    }

    /**
     * @param  array<int, int>  $gymIds
     */
    private function autoCloseExpiredSessionsForGyms(array $gymIds): void
    {
        foreach ($gymIds as $gymId) {
            $resolvedGymId = (int) $gymId;
            if ($resolvedGymId <= 0) {
                continue;
            }

            $this->cashSessionService->autoCloseExpiredSessions($resolvedGymId);
        }
    }
}
