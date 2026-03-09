<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Client;
use App\Models\DemoSession;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSessionService
{
    public function startSession(): DemoSession
    {
        $this->cleanupExpired();

        return DB::transaction(function (): DemoSession {
            $gym = Gym::query()->create([
                'name' => 'Demo Gym '.Str::upper(Str::random(4)),
                'slug' => $this->generateDemoGymSlug(),
                'phone' => '+593000000000',
                'address' => 'Demo Street, Quito, Ecuador',
                'address_country_code' => 'EC',
                'address_country_name' => 'Ecuador',
                'address_state' => 'Pichincha',
                'address_city' => 'Quito',
                'address_line' => 'Demo Street',
                'timezone' => 'America/Guayaquil',
                'currency_code' => 'USD',
                'language_code' => 'es',
            ]);

            $user = User::query()->create([
                'gym_id' => $gym->id,
                'name' => 'Demo Operador',
                'email' => $this->generateDemoEmail(),
                'password' => Hash::make(Str::random(48)),
                'phone_country_iso' => 'EC',
                'phone_country_dial' => '+593',
                'phone_number' => '000000000',
                'role' => User::ROLE_OWNER,
                'theme' => 'iron_dark',
            ]);

            $this->seedDemoData($gym, $user);

            return DemoSession::query()->create([
                'user_id' => $user->id,
                'gym_id' => $gym->id,
                'token' => Str::random(48),
                'expires_at' => now()->addMinutes($this->ttlMinutes()),
                'last_activity_at' => now(),
            ])->fresh(['user', 'gym']);
        });
    }

    public function activeForUser(User $user): ?DemoSession
    {
        $session = DemoSession::query()
            ->where('user_id', $user->id)
            ->first();

        if (! $session) {
            return null;
        }

        if ($session->expires_at->isPast()) {
            $this->terminateSession($session);

            return null;
        }

        return $session;
    }

    public function touchActivity(DemoSession $session): void
    {
        $lastActivity = $session->last_activity_at;
        if ($lastActivity && $lastActivity->gt(now()->subMinute())) {
            return;
        }

        $session->forceFill([
            'last_activity_at' => now(),
        ])->save();
    }

    public function cleanupExpired(): void
    {
        DemoSession::query()
            ->where('expires_at', '<=', now())
            ->orderBy('id')
            ->chunkById(25, function ($sessions): void {
                foreach ($sessions as $session) {
                    $this->terminateSession($session);
                }
            });
    }

    public function terminateSession(DemoSession $session): void
    {
        $pathsToDelete = [];

        DB::transaction(function () use ($session, &$pathsToDelete): void {
            $session->refresh();
            $gymId = (int) $session->gym_id;
            $userId = (int) $session->user_id;
            $pathsToDelete = $this->collectDemoAssetPaths($gymId, $userId);

            DemoSession::query()
                ->whereKey($session->id)
                ->delete();

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')
                    ->where('user_id', $userId)
                    ->delete();
            }

            Gym::query()
                ->whereKey($gymId)
                ->delete();

            User::query()
                ->whereKey($userId)
                ->delete();
        });

        foreach (array_unique($pathsToDelete) as $path) {
            $this->deletePublicAssetIfLocal($path);
        }
    }

    /**
     * @return array<int, string>
     */
    private function collectDemoAssetPaths(int $gymId, int $userId): array
    {
        $paths = [];

        $gym = Gym::query()
            ->select(['id', 'logo_path', 'avatar_male_path', 'avatar_female_path', 'avatar_neutral_path'])
            ->find($gymId);

        if ($gym) {
            $paths[] = (string) ($gym->logo_path ?? '');
            $paths[] = (string) ($gym->avatar_male_path ?? '');
            $paths[] = (string) ($gym->avatar_female_path ?? '');
            $paths[] = (string) ($gym->avatar_neutral_path ?? '');
        }

        User::query()
            ->select([
                'id',
                'profile_photo_path',
                'support_contact_logo_path',
                'support_contact_logo_light_path',
                'support_contact_logo_dark_path',
            ])
            ->where(function ($query) use ($gymId, $userId): void {
                $query->where('gym_id', $gymId)->orWhere('id', $userId);
            })
            ->chunkById(100, function ($users) use (&$paths): void {
                foreach ($users as $user) {
                    $paths[] = (string) ($user->profile_photo_path ?? '');
                    $paths[] = (string) ($user->support_contact_logo_path ?? '');
                    $paths[] = (string) ($user->support_contact_logo_light_path ?? '');
                    $paths[] = (string) ($user->support_contact_logo_dark_path ?? '');
                }
            });

        Client::query()
            ->select(['id', 'photo_path'])
            ->where('gym_id', $gymId)
            ->chunkById(200, function ($clients) use (&$paths): void {
                foreach ($clients as $client) {
                    $paths[] = (string) ($client->photo_path ?? '');
                }
            });

        return array_values(array_filter(array_map('trim', $paths)));
    }

    private function deletePublicAssetIfLocal(?string $path): void
    {
        $assetPath = trim((string) $path);
        if (
            $assetPath === ''
            || str_starts_with($assetPath, 'http://')
            || str_starts_with($assetPath, 'https://')
            || str_contains($assetPath, '..')
        ) {
            return;
        }

        $normalized = ltrim($assetPath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }

        if ($normalized === '') {
            return;
        }

        Storage::disk('public')->delete($normalized);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function guideSteps(string $gymSlug): array
    {
        $panelRoute = route('panel.index', ['contextGym' => $gymSlug]);
        $clientsRoute = route('clients.index', ['contextGym' => $gymSlug]);
        $plansRoute = route('plans.index', ['contextGym' => $gymSlug]);
        $cashRoute = route('cash.index', ['contextGym' => $gymSlug]);
        $reportsRoute = route('reports.index', ['contextGym' => $gymSlug]);
        $receptionRoute = route('reception.index', ['contextGym' => $gymSlug]);
        $settingsRoute = route('gym.settings.index', ['contextGym' => $gymSlug]);

        return [
            [
                'title' => 'Menú lateral',
                'text' => 'Usa este botón para abrir/cerrar el menú del sistema.',
                'route' => $panelRoute,
                'selector' => '#sidebar-toggle',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Panel de control',
                'text' => 'Desde aquí ves indicadores del día y estado operativo del gimnasio.',
                'route' => $panelRoute,
                'selector' => '#tour-panel-summary',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Acceso rápido a clientes',
                'text' => 'Este acceso te lleva directo al módulo de clientes para registrar o editar datos.',
                'route' => $panelRoute,
                'selector' => '#tour-panel-go-clients',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Centro de seguimiento',
                'text' => 'Revisa renovaciones, check-ins y movimientos sin salir del panel.',
                'route' => $panelRoute,
                'selector' => '#tour-panel-tracking',
                'placement' => 'left',
            ],
            [
                'title' => 'Recepción: entrada',
                'text' => 'Escanea RFID/QR o escribe documento para registrar ingreso del cliente.',
                'route' => $receptionRoute,
                'selector' => '#value',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Recepción: enviar',
                'text' => 'Procesa el ingreso manualmente cuando no use autoescaneo.',
                'route' => $receptionRoute,
                'selector' => '#send-btn',
                'placement' => 'right',
            ],
            [
                'title' => 'Recepción: historial',
                'text' => 'Abre el historial de asistencias de los ultimos 2 meses.',
                'route' => $receptionRoute,
                'selector' => '#reception-open-history',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Clientes: nuevo cliente',
                'text' => 'Crea un cliente y, si quieres, inicia su membresía en el mismo flujo.',
                'route' => $clientsRoute,
                'selector' => '#clients-open-create',
                'placement' => 'left',
            ],
            [
                'title' => 'Clientes: buscar',
                'text' => 'Filtra por nombre, apellido o documento para ubicar rápido al cliente.',
                'route' => $clientsRoute,
                'selector' => '#clients-search',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Clientes: filtros rapidos',
                'text' => 'Cambia entre activos, por vencer, vencidos o asistieron hoy.',
                'route' => $clientsRoute,
                'selector' => '#clients-filter-chips',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Clientes: tabla operativa',
                'text' => 'Aquí controlas estado, vencimiento, pago y acción rápida por cliente.',
                'route' => $clientsRoute,
                'selector' => '#clients-table',
                'placement' => 'top',
            ],
            [
                'title' => 'Planes: crear plan',
                'text' => 'Define nombre, duracion y precio del plan comercial.',
                'route' => $plansRoute,
                'selector' => '#create-plan-form',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Planes: tipo de duracion',
                'text' => 'Elige si el plan se calcula por días exactos o meses calendario.',
                'route' => $plansRoute,
                'selector' => '#plan-duration-unit',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Planes: guardar plan',
                'text' => 'Guarda el plan para que quede disponible en ventas de membresías.',
                'route' => $plansRoute,
                'selector' => '#create-plan-submit',
                'placement' => 'top',
            ],
            [
                'title' => 'Planes: promociones',
                'text' => 'Crea promociones para descuentos, bonus o precio final.',
                'route' => $plansRoute,
                'selector' => '#open-promotion-modal-btn',
                'placement' => 'left',
            ],
            [
                'title' => 'Caja: registrar movimiento',
                'text' => 'Registra ingresos o egresos del turno activo.',
                'route' => $cashRoute,
                'selector' => '#cash-movement-form',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Caja: cierre de turno',
                'text' => 'Haz cuadre por método y cierra turno con control de diferencias.',
                'route' => $cashRoute,
                'selector' => '#cash-close-form',
                'placement' => 'left',
            ],
            [
                'title' => 'Caja: historial',
                'text' => 'Consulta turnos anteriores y auditoría de caja.',
                'route' => $cashRoute,
                'selector' => '#cash-go-history',
                'placement' => 'top',
            ],
            [
                'title' => 'Reportes: filtro',
                'text' => 'Selecciona rango de fechas para analizar operación y finanzas.',
                'route' => $reportsRoute,
                'selector' => '#reports-filter-form',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Reportes: exportar PDF',
                'text' => 'Genera reporte PDF para compartir con administración o dueños.',
                'route' => $reportsRoute,
                'selector' => '#reports-export-pdf',
                'placement' => 'left',
            ],
            [
                'title' => 'Reportes: detalle de ingresos',
                'text' => 'Entra al detalle de ingresos para revisar movimientos por método.',
                'route' => $reportsRoute,
                'selector' => '#reports-go-income',
                'placement' => 'top',
            ],
            [
                'title' => 'Configuración: selector de tema',
                'text' => 'Personaliza apariencia del panel del gimnasio.',
                'route' => $settingsRoute,
                'selector' => '#theme-selector',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Configuración: logo',
                'text' => 'Actualiza logo para sidebar, tarjetas y vistas operativas.',
                'route' => $settingsRoute,
                'selector' => '#gym-logo-form',
                'placement' => 'bottom',
            ],
            [
                'title' => 'Configuración: datos del gym',
                'text' => 'Edita nombre, teléfono, moneda e idioma de operación.',
                'route' => $settingsRoute,
                'selector' => '#gym-profile-form',
                'placement' => 'left',
            ],
            [
                'title' => 'Configuración: zona horaria',
                'text' => 'Define hora oficial para check-ins, caja y reportes.',
                'route' => $settingsRoute,
                'selector' => '#timezone-select',
                'placement' => 'top',
            ],
            [
                'title' => 'Configuración: avatares fallback',
                'text' => 'Sube avatares por género cuando un cliente no tenga foto.',
                'route' => $settingsRoute,
                'selector' => '#gym-avatars-form',
                'placement' => 'top',
            ],
        ];
    }

    private function ttlMinutes(): int
    {
        return 60;
    }

    private function generateDemoGymSlug(): string
    {
        do {
            $slug = 'demo-'.strtolower(Str::random(8));
        } while (Gym::query()->where('slug', $slug)->exists());

        return $slug;
    }

    private function generateDemoEmail(): string
    {
        do {
            $email = 'demo+'.strtolower(Str::random(14)).'@gymsystem.local';
        } while (User::query()->where('email', $email)->exists());

        return $email;
    }

    private function seedDemoData(Gym $gym, User $user): void
    {
        $plan = Plan::query()->create([
            'gym_id' => $gym->id,
            'name' => 'Plan Demo Mensual',
            'duration_days' => 30,
            'duration_unit' => 'days',
            'duration_months' => null,
            'price' => 30,
            'status' => 'active',
        ]);

        $clientActive = Client::query()->create([
            'gym_id' => $gym->id,
            'first_name' => 'Cliente',
            'last_name' => 'Demo',
            'document_number' => 'DEMO'.Str::upper(Str::random(6)),
            'phone' => '+593990000001',
            'gender' => 'male',
            'status' => 'active',
        ]);

        $clientProspect = Client::query()->create([
            'gym_id' => $gym->id,
            'first_name' => 'Invitado',
            'last_name' => 'Temporal',
            'document_number' => 'DEMO'.Str::upper(Str::random(6)),
            'phone' => '+593990000002',
            'gender' => 'female',
            'status' => 'inactive',
        ]);

        $membership = Membership::query()->create([
            'gym_id' => $gym->id,
            'client_id' => $clientActive->id,
            'plan_id' => $plan->id,
            'price' => 30,
            'starts_at' => now()->subDays(2)->toDateString(),
            'ends_at' => now()->addDays(28)->toDateString(),
            'status' => 'active',
        ]);

        Membership::query()->create([
            'gym_id' => $gym->id,
            'client_id' => $clientProspect->id,
            'plan_id' => $plan->id,
            'price' => 30,
            'starts_at' => now()->subDays(45)->toDateString(),
            'ends_at' => now()->subDays(15)->toDateString(),
            'status' => 'expired',
        ]);

        $cashSession = CashSession::query()->create([
            'gym_id' => $gym->id,
            'opened_by' => $user->id,
            'opened_at' => now()->subHours(1),
            'opening_balance' => 50,
            'status' => 'open',
        ]);

        CashMovement::query()->create([
            'gym_id' => $gym->id,
            'cash_session_id' => $cashSession->id,
            'type' => 'income',
            'amount' => 30,
            'method' => 'cash',
            'membership_id' => $membership->id,
            'created_by' => $user->id,
            'description' => 'Cobro demo',
            'occurred_at' => now()->subMinutes(20),
        ]);

        Attendance::query()->create([
            'gym_id' => $gym->id,
            'client_id' => $clientActive->id,
            'date' => Carbon::today()->toDateString(),
            'time' => now()->format('H:i:s'),
            'created_by' => $user->id,
        ]);
    }
}
