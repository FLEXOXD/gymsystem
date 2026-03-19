<?php

use App\Models\Gym;
use App\Models\GymAdminActivityState;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('updates owner activity state on manual login', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-19 12:15:00'));

    try {
        $gym = Gym::query()->create([
            'name' => 'Gym Activity Login',
            'slug' => 'gym-activity-login',
        ]);

        $owner = User::factory()->create([
            'gym_id' => $gym->id,
            'role' => User::ROLE_OWNER,
            'email' => 'owner-activity-login@example.test',
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login'), [
            'email' => $owner->email,
            'password' => 'password',
            'pwa_mode' => 'browser',
        ], [
            'REMOTE_ADDR' => '181.88.10.15',
            'HTTP_USER_AGENT' => 'BrowserActivity/1.0',
        ])->assertRedirect(route('panel.index', [
            'contextGym' => $gym->slug,
        ]));

        $state = GymAdminActivityState::query()->where('gym_id', $gym->id)->firstOrFail();

        expect((int) $state->user_id)->toBe((int) $owner->id)
            ->and((string) $state->last_channel)->toBe('web')
            ->and((string) $state->last_activity_signal)->toBe('login_manual')
            ->and((string) $state->last_ip_address)->toBe('181.88.10.15')
            ->and($state->last_login_at?->format('Y-m-d H:i:s'))->toBe('2026-03-19 12:15:00')
            ->and($state->last_activity_at?->format('Y-m-d H:i:s'))->toBe('2026-03-19 12:15:00');
    } finally {
        Carbon::setTestNow();
    }
});

it('tracks owner heartbeat activity for standalone app usage', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-19 18:40:00'));

    try {
        $gym = Gym::query()->create([
            'name' => 'Gym Activity Pwa',
            'slug' => 'gym-activity-pwa',
        ]);

        $owner = User::factory()->create([
            'gym_id' => $gym->id,
            'role' => User::ROLE_OWNER,
            'email' => 'owner-activity-pwa@example.test',
        ]);

        Subscription::query()
            ->where('gym_id', $gym->id)
            ->update([
                'plan_key' => 'premium',
                'feature_version' => 'v1',
                'status' => 'active',
                'starts_at' => Carbon::today()->subDay()->toDateString(),
                'ends_at' => Carbon::today()->addDays(30)->toDateString(),
            ]);

        $this->actingAs($owner)
            ->postJson(route('panel.owner-activity.heartbeat', [
                'contextGym' => $gym->slug,
                'pwa_mode' => 'standalone',
            ]), [
                'signal' => 'page_open',
                'channel' => 'app_instalada',
                'route_name' => 'panel.index',
                'path' => '/'.$gym->slug.'/panel?pwa_mode=standalone',
            ], [
                'REMOTE_ADDR' => '186.71.22.44',
                'HTTP_USER_AGENT' => 'StandaloneActivity/1.0',
                'X-PWA-Mode' => 'standalone',
            ])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'tracked' => true,
            ]);

        $state = GymAdminActivityState::query()->where('gym_id', $gym->id)->firstOrFail();

        expect((string) $state->last_channel)->toBe('app_instalada')
            ->and((string) $state->last_activity_signal)->toBe('page_open')
            ->and((string) $state->last_route_name)->toBe('panel.index')
            ->and((string) $state->last_path)->toBe('/'.$gym->slug.'/panel?pwa_mode=standalone')
            ->and((string) $state->last_ip_address)->toBe('186.71.22.44')
            ->and($state->last_activity_at?->format('Y-m-d H:i:s'))->toBe('2026-03-19 18:40:00');
    } finally {
        Carbon::setTestNow();
    }
});

it('renders owner activity state on the superadmin dashboard', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-19 23:40:00', 'UTC'));

    try {
        $superAdmin = User::factory()->create([
            'gym_id' => null,
            'role' => User::ROLE_SUPERADMIN,
            'timezone' => 'America/Guayaquil',
        ]);

        $gym = Gym::query()->create([
            'name' => 'Gym Dashboard Activity',
            'slug' => 'gym-dashboard-activity',
        ]);

        $owner = User::factory()->create([
            'gym_id' => $gym->id,
            'role' => User::ROLE_OWNER,
            'name' => 'Admin Activo',
            'email' => 'admin-activo@example.test',
        ]);

        GymAdminActivityState::query()->create([
            'gym_id' => $gym->id,
            'user_id' => $owner->id,
            'gym_name' => $gym->name,
            'user_name' => $owner->name,
            'user_email' => $owner->email,
            'last_login_at' => Carbon::parse('2026-03-19 22:30:00', 'UTC'),
            'last_activity_at' => Carbon::parse('2026-03-19 23:40:00', 'UTC'),
            'last_activity_signal' => 'page_open',
            'last_channel' => 'app_instalada',
            'last_route_name' => 'panel.index',
            'last_path' => '/'.$gym->slug.'/panel?pwa_mode=standalone',
            'last_ip_address' => '186.71.22.44',
            'last_user_agent' => 'DashboardActivity/1.0',
            'last_via_remember' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Actividad de admins principales', false)
            ->assertSee('Gym Dashboard Activity', false)
            ->assertSee('Admin Activo', false)
            ->assertSee('Activo ahora', false)
            ->assertSee('App instalada', false)
            ->assertSee('via Recuerdame', false)
            ->assertSee('186.71.22.44', false)
            ->assertSee('19/03/2026 18:40', false)
            ->assertSee('19/03/2026 17:30', false);
    } finally {
        Carbon::setTestNow();
    }
});
