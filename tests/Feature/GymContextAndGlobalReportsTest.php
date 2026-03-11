<?php

use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeContextGym(string $suffix): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) Str::random(8)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
        'timezone' => 'America/Guayaquil',
        'currency_code' => 'USD',
        'language_code' => 'es',
    ]);
}

function makeContextOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner '.$gym->id,
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
        'is_active' => true,
    ]);
}

function setContextPlan(Gym $gym, string $planKey): void
{
    $payload = [
        'plan_key' => $planKey,
        'feature_version' => 'v1',
        'status' => 'active',
        'starts_at' => Carbon::today()->subDay()->toDateString(),
        'ends_at' => Carbon::today()->addDays(30)->toDateString(),
    ];

    $updated = Subscription::query()
        ->where('gym_id', $gym->id)
        ->update($payload);

    if ($updated === 0) {
        Subscription::query()->create([
            'gym_id' => $gym->id,
            'plan_name' => ucfirst($planKey),
            'price' => 49.99,
            ...$payload,
        ]);
    }

    Cache::forget('plan_access:gym:'.$gym->id.':plan_key');
}

it('saves gym profile changes on the branch gym selected in context', function () {
    $hubGym = makeContextGym('hub-profile');
    $branchGym = makeContextGym('branch-profile');
    $owner = makeContextOwner($hubGym, 'hub-profile-owner@example.test');

    setContextPlan($hubGym, 'sucursales');
    setContextPlan($branchGym, 'premium');

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => true,
        'status' => 'active',
        'created_by' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->from(route('gym.settings.index', ['contextGym' => $branchGym->slug]))
        ->post(route('gym.settings.gym-profile.update', ['contextGym' => $branchGym->slug]), [
            'name' => 'Sucursal Machachi',
            'phone' => '0999999999',
            'address_country_code' => 'ec',
            'address_state' => 'Pichincha',
            'address_city' => 'Quito',
            'address_line' => 'Av. Los Volcanes',
            'currency_code' => 'USD',
            'language_code' => 'es',
            'timezone' => 'America/Guayaquil',
        ])
        ->assertRedirect(route('gym.settings.index', ['contextGym' => $branchGym->slug]))
        ->assertSessionHas('status');

    $branchGym->refresh();
    $hubGym->refresh();

    expect((string) $branchGym->name)->toBe('Sucursal Machachi')
        ->and((string) $branchGym->phone)->toBe('0999999999')
        ->and((string) $branchGym->address_country_code)->toBe('ec')
        ->and((string) $branchGym->address_state)->toBe('Pichincha')
        ->and((string) $branchGym->address_city)->toBe('Quito')
        ->and((string) $branchGym->address_line)->toBe('Av. Los Volcanes')
        ->and((string) $hubGym->name)->not->toBe('Sucursal Machachi');
});

it('preserves global scope on report exports and includes movements from both linked gyms', function () {
    $hubGym = makeContextGym('hub-report');
    $branchGym = makeContextGym('branch-report');
    $owner = makeContextOwner($hubGym, 'hub-report-owner@example.test');
    $branchOwner = makeContextOwner($branchGym, 'branch-report-owner@example.test');

    setContextPlan($hubGym, 'sucursales');
    setContextPlan($branchGym, 'premium');

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => true,
        'status' => 'active',
        'created_by' => $owner->id,
    ]);

    $hubSession = CashSession::query()->create([
        'gym_id' => $hubGym->id,
        'opened_by' => $owner->id,
        'opened_at' => Carbon::parse('2026-03-10 08:00:00', 'America/Guayaquil'),
        'opening_balance' => 30,
        'status' => 'open',
    ]);

    $branchSession = CashSession::query()->create([
        'gym_id' => $branchGym->id,
        'opened_by' => $branchOwner->id,
        'opened_at' => Carbon::parse('2026-03-10 09:00:00', 'America/Guayaquil'),
        'opening_balance' => 15,
        'status' => 'open',
    ]);

    CashMovement::query()->create([
        'gym_id' => $hubGym->id,
        'cash_session_id' => $hubSession->id,
        'type' => 'income',
        'amount' => 30,
        'method' => 'cash',
        'created_by' => $owner->id,
        'description' => 'Ingreso sede principal',
        'occurred_at' => Carbon::parse('2026-03-10 10:00:00', 'America/Guayaquil'),
    ]);

    CashMovement::query()->create([
        'gym_id' => $branchGym->id,
        'cash_session_id' => $branchSession->id,
        'type' => 'income',
        'amount' => 45,
        'method' => 'cash',
        'created_by' => $branchOwner->id,
        'description' => 'Ingreso sucursal norte',
        'occurred_at' => Carbon::parse('2026-03-10 11:00:00', 'America/Guayaquil'),
    ]);

    $from = '2026-03-09';
    $to = '2026-03-11';

    $this->actingAs($owner)
        ->get(route('reports.index', [
            'contextGym' => $hubGym->slug,
            'scope' => 'global',
            'from' => $from,
            'to' => $to,
        ]))
        ->assertOk()
        ->assertSee('Reporte global activo')
        ->assertSee('scope=global', false);

    $this->actingAs($owner)
        ->get(route('reports.export.csv', [
            'contextGym' => $hubGym->slug,
            'scope' => 'global',
            'from' => $from,
            'to' => $to,
        ]))
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8')
        ->assertSee('Global multi-sede')
        ->assertSee('Ingreso sede principal')
        ->assertSee('Ingreso sucursal norte');
});
