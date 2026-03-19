<?php

use App\Models\Gym;
use App\Models\GymAdminActivityState;
use App\Models\GymAdminLoginEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores a login audit event only for gym owner accounts', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-19 10:30:00'));

    try {
        $gym = Gym::query()->create([
            'name' => 'Gym Login Audit',
            'slug' => 'gym-login-audit',
        ]);

        $owner = User::factory()->create([
            'gym_id' => $gym->id,
            'role' => User::ROLE_OWNER,
            'email' => 'owner-login-audit@example.test',
            'password' => bcrypt('password'),
        ]);

        $cashier = User::factory()->create([
            'gym_id' => $gym->id,
            'role' => User::ROLE_CASHIER,
            'email' => 'cashier-login-audit@example.test',
            'password' => bcrypt('password'),
        ]);

        $superAdmin = User::factory()->create([
            'gym_id' => null,
            'role' => User::ROLE_SUPERADMIN,
            'email' => 'superadmin-login-audit@example.test',
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login'), [
            'email' => $owner->email,
            'password' => 'password',
        ], [
            'REMOTE_ADDR' => '181.39.50.10',
            'HTTP_USER_AGENT' => 'GymOwnerBrowser/1.0',
        ])->assertRedirect(route('panel.index', [
            'contextGym' => $gym->slug,
        ]));

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $cashier->email,
            'password' => 'password',
        ], [
            'REMOTE_ADDR' => '181.39.50.11',
            'HTTP_USER_AGENT' => 'GymCashierBrowser/1.0',
        ])->assertRedirect(route('panel.index', [
            'contextGym' => $gym->slug,
        ]));

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $superAdmin->email,
            'password' => 'password',
        ], [
            'REMOTE_ADDR' => '181.39.50.12',
            'HTTP_USER_AGENT' => 'GymSuperAdminBrowser/1.0',
        ])->assertRedirect(route('superadmin.dashboard'));

        expect(GymAdminLoginEvent::query()->count())->toBe(1);

        $event = GymAdminLoginEvent::query()->firstOrFail();

        expect((int) $event->gym_id)->toBe((int) $gym->id)
            ->and((int) $event->user_id)->toBe((int) $owner->id)
            ->and((string) $event->gym_name)->toBe('Gym Login Audit')
            ->and((string) $event->user_email)->toBe('owner-login-audit@example.test')
            ->and((string) $event->ip_address)->toBe('181.39.50.10');
    } finally {
        Carbon::setTestNow();
    }
});

it('renders the latest owner login per gym on the superadmin dashboard', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-19 18:40:00'));

    try {
        $superAdmin = User::factory()->create([
            'gym_id' => null,
            'role' => User::ROLE_SUPERADMIN,
            'timezone' => 'America/Guayaquil',
        ]);

        $gym = Gym::query()->create([
            'name' => 'Gym Dashboard Login',
            'slug' => 'gym-dashboard-login',
        ]);

        $owner = User::factory()->create([
            'gym_id' => $gym->id,
            'role' => User::ROLE_OWNER,
            'name' => 'Admin Dashboard',
            'email' => 'admin-dashboard-login@example.test',
        ]);

        GymAdminLoginEvent::query()->create([
            'gym_id' => $gym->id,
            'user_id' => $owner->id,
            'gym_name' => $gym->name,
            'user_name' => $owner->name,
            'user_email' => $owner->email,
            'ip_address' => '186.71.22.44',
            'user_agent' => 'DashboardTest/1.0',
            'logged_in_at' => Carbon::parse('2026-03-19 23:40:00', 'UTC'),
        ]);

        GymAdminActivityState::query()->create([
            'gym_id' => $gym->id,
            'user_id' => $owner->id,
            'gym_name' => $gym->name,
            'user_name' => $owner->name,
            'user_email' => $owner->email,
            'last_login_at' => Carbon::parse('2026-03-19 23:40:00', 'UTC'),
            'last_activity_at' => Carbon::parse('2026-03-19 23:40:00', 'UTC'),
            'last_activity_signal' => 'login_manual',
            'last_channel' => 'web',
            'last_ip_address' => '186.71.22.44',
            'last_user_agent' => 'DashboardTest/1.0',
            'last_via_remember' => false,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Actividad de admins principales', false)
            ->assertSee('Gym Dashboard Login', false)
            ->assertSee('Admin Dashboard', false)
            ->assertSee('admin-dashboard-login@example.test', false)
            ->assertSee('186.71.22.44', false)
            ->assertSee('Activo ahora', false)
            ->assertSee('19/03/2026 18:40', false);
    } finally {
        Carbon::setTestNow();
    }
});
