<?php

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BranchProvisioningService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeBranchHubGym(string $suffix = 'hub'): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) \Illuminate\Support\Str::random(8)),
        'phone' => null,
        'address' => null,
    ]);
}

it('creates branch gym, branch admin, managed subscription and branch link in one transaction', function () {
    $hubGym = makeBranchHubGym('sucursales');
    $hubUser = User::query()->create([
        'name' => 'Hub Admin',
        'email' => 'hub-admin@example.test',
        'password' => 'password',
        'gym_id' => $hubGym->id,
    ]);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(30)->toDateString(),
        ]);

    $service = app(BranchProvisioningService::class);
    $link = $service->createBranchForHub(
        hubGymId: (int) $hubGym->id,
        branchName: 'Sucursal Norte',
        branchPhone: '0999999999',
        countryCode: 'ec',
        state: 'Guayas',
        city: 'Guayaquil',
        addressLine: 'Av. Principal 123',
        branchPlanKey: 'profesional',
        branchAdminName: 'Admin Norte',
        branchAdminEmail: 'admin-norte@example.test',
        branchAdminPassword: 'password123',
        cashManagedByHub: true,
        createdByUserId: (int) $hubUser->id
    );

    $this->assertDatabaseHas('gym_branch_links', [
        'id' => (int) $link->id,
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $link->branch_gym_id,
        'branch_plan_key' => 'profesional',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $branchGymId = (int) $link->branch_gym_id;
    $branchGym = Gym::query()->findOrFail($branchGymId);

    expect($branchGym->name)->toBe('Sucursal Norte');

    $this->assertDatabaseHas('users', [
        'gym_id' => $branchGymId,
        'email' => 'admin-norte@example.test',
        'name' => 'Admin Norte',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'gym_id' => $branchGymId,
        'plan_key' => 'profesional',
        'price' => 0.00,
        'is_branch_managed' => 1,
        'billing_owner_gym_id' => $hubGym->id,
    ]);
});

it('blocks branch provisioning when hub gym has no multi-branch entitlement', function () {
    $hubGym = makeBranchHubGym('no-multibranch');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(30)->toDateString(),
        ]);

    $service = app(BranchProvisioningService::class);

    expect(function () use ($service, $hubGym): void {
        $service->createBranchForHub(
            hubGymId: (int) $hubGym->id,
            branchName: 'Sucursal Bloqueada',
            branchPhone: null,
            countryCode: 'ec',
            state: 'Guayas',
            city: 'Guayaquil',
            addressLine: 'Sector Norte',
            branchPlanKey: 'basico',
            branchAdminName: 'Admin Bloqueado',
            branchAdminEmail: 'blocked@example.test',
            branchAdminPassword: 'password123',
            cashManagedByHub: true,
            createdByUserId: null
        );
    })->toThrow(\InvalidArgumentException::class);
});

it('syncs managed branch subscription cycle with hub subscription', function () {
    $hubGym = makeBranchHubGym('sync-cycle');
    $hubUser = User::query()->create([
        'name' => 'Hub Sync Admin',
        'email' => 'hub-sync-admin@example.test',
        'password' => 'password',
        'gym_id' => $hubGym->id,
    ]);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(20)->toDateString(),
        ]);

    $branchProvisioning = app(BranchProvisioningService::class);
    $link = $branchProvisioning->createBranchForHub(
        hubGymId: (int) $hubGym->id,
        branchName: 'Sucursal Sync',
        branchPhone: '0999991111',
        countryCode: 'ec',
        state: 'Guayas',
        city: 'Guayaquil',
        addressLine: 'Av. Sync 101',
        branchPlanKey: 'premium',
        branchAdminName: 'Admin Sync',
        branchAdminEmail: 'admin-sync@example.test',
        branchAdminPassword: 'password123',
        cashManagedByHub: true,
        createdByUserId: (int) $hubUser->id
    );

    $hubSubscription = Subscription::query()->where('gym_id', (int) $hubGym->id)->firstOrFail();
    $branchSubscription = Subscription::query()->where('gym_id', (int) $link->branch_gym_id)->firstOrFail();

    expect($branchSubscription->is_branch_managed)->toBeTrue();
    expect((int) $branchSubscription->billing_owner_gym_id)->toBe((int) $hubGym->id);
    expect((string) $branchSubscription->plan_key)->toBe('premium');
    expect($branchSubscription->ends_at?->toDateString())->toBe($hubSubscription->ends_at?->toDateString());
    expect((string) $branchSubscription->status)->toBe((string) $hubSubscription->status);

    /** @var SubscriptionService $subscriptionService */
    $subscriptionService = app(SubscriptionService::class);
    $subscriptionService->renew((int) $hubGym->id, 3, 'efectivo');

    $hubSubscription = Subscription::query()->where('gym_id', (int) $hubGym->id)->firstOrFail();
    $branchSubscription = Subscription::query()->where('gym_id', (int) $link->branch_gym_id)->firstOrFail();

    expect($branchSubscription->ends_at?->toDateString())->toBe($hubSubscription->ends_at?->toDateString());
    expect((string) $branchSubscription->status)->toBe((string) $hubSubscription->status);
    expect((string) $branchSubscription->plan_key)->toBe('premium');

    $subscriptionService->suspend((int) $hubGym->id);

    $hubSubscription = Subscription::query()->where('gym_id', (int) $hubGym->id)->firstOrFail();
    $branchSubscription = Subscription::query()->where('gym_id', (int) $link->branch_gym_id)->firstOrFail();

    expect((string) $hubSubscription->status)->toBe('suspended');
    expect((string) $branchSubscription->status)->toBe('suspended');
    expect($branchSubscription->ends_at?->toDateString())->toBe($hubSubscription->ends_at?->toDateString());
});
