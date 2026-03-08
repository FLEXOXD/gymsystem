<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('imports legacy dbcola clients into basic and premium gyms', function () {
    $sql = <<<'SQL'
INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `telefono`, `contrasena`, `tipo`, `foto`, `cedula`, `qr_path`) VALUES
(39, 'Administrador', 'admin@gym.com', '0999999999', '$2y$10$JQjpG8N2K1GlMLzmXYq0KOYGw1lzsnCQY/DIAaGEwTNzrV9OX46CC', 'admin', NULL, '', NULL),
(43, 'Ashley Vargas', 'ashleyvargas4321@gmail.com', '0963274047', '$2y$10$abcdefghijabcdefghijabcdefghijabcdefghijabcdefghijab', 'cliente', NULL, '1727907774', 'qr/qr_usuario_43.png');
INSERT INTO `mensualidades` (`id`, `cliente_id`, `fecha_pago`, `monto`, `fecha_fin`, `estado`, `renovada`, `archivado`, `avisado`, `meses_acumulados`) VALUES
(5, 43, '2026-02-18 17:51:17', 30.00, '2026-03-17 17:51:17', 'vigente', 0, 0, 0, 2);
SQL;

    $tempPath = tempnam(sys_get_temp_dir(), 'dbcola-import-');
    file_put_contents($tempPath, $sql);

    try {
        $this->artisan('legacy:import-dbcola-users', [
            'path' => $tempPath,
            '--basic-owner-email' => 'owner.basic@test.local',
            '--basic-owner-password' => 'BasicPass#2026',
            '--premium-owner-email' => 'owner.premium@test.local',
            '--premium-owner-password' => 'PremiumPass#2026',
        ])->assertSuccessful();

        $basicGym = Gym::query()->where('slug', 'dbcola-basico')->firstOrFail();
        $premiumGym = Gym::query()->where('slug', 'dbcola-premium')->firstOrFail();

        $basicOwner = User::query()->where('gym_id', (int) $basicGym->id)->where('role', User::ROLE_OWNER)->firstOrFail();
        $premiumOwner = User::query()->where('gym_id', (int) $premiumGym->id)->where('role', User::ROLE_OWNER)->firstOrFail();

        expect($basicOwner->email)->toBe('owner.basic@test.local')
            ->and($premiumOwner->email)->toBe('owner.premium@test.local');

        $basicClient = Client::query()->where('gym_id', (int) $basicGym->id)->where('document_number', '1727907774')->firstOrFail();
        $premiumClient = Client::query()->where('gym_id', (int) $premiumGym->id)->where('document_number', '1727907774')->firstOrFail();

        expect($basicClient->app_username)->toBeNull()
            ->and($basicClient->app_password)->toBeNull()
            ->and($premiumClient->app_username)->toBe('ashleyvargas4321.gmail.com')
            ->and($premiumClient->app_password)->toStartWith('$2y$');

        $basicPlan = Plan::query()->where('gym_id', (int) $basicGym->id)->where('name', 'Legacy 2 meses')->firstOrFail();
        $premiumPlan = Plan::query()->where('gym_id', (int) $premiumGym->id)->where('name', 'Legacy 2 meses')->firstOrFail();

        expect((int) $basicPlan->duration_months)->toBe(2)
            ->and((float) $basicPlan->price)->toBe(30.0)
            ->and((int) $premiumPlan->duration_months)->toBe(2);

        $basicMembership = Membership::query()->where('gym_id', (int) $basicGym->id)->where('client_id', (int) $basicClient->id)->firstOrFail();
        $premiumMembership = Membership::query()->where('gym_id', (int) $premiumGym->id)->where('client_id', (int) $premiumClient->id)->firstOrFail();

        expect((int) $basicMembership->plan_id)->toBe((int) $basicPlan->id)
            ->and((string) $basicMembership->starts_at?->toDateString())->toBe('2026-02-18')
            ->and((string) $basicMembership->ends_at?->toDateString())->toBe('2026-03-17')
            ->and((string) $basicMembership->status)->toBe('active')
            ->and((float) $basicMembership->price)->toBe(30.0)
            ->and((int) $premiumMembership->plan_id)->toBe((int) $premiumPlan->id)
            ->and((string) $premiumMembership->status)->toBe('active');
    } finally {
        if (is_string($tempPath) && $tempPath !== '' && file_exists($tempPath)) {
            unlink($tempPath);
        }
    }
});
