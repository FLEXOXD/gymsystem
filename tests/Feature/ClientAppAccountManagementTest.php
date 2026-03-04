<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config(['plan_features.access_cache_seconds' => 0]);
    Cache::flush();
});

function makeClientAppGym(string $suffix): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) Str::random(8)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

function makeClientAppOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner App',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

function setClientAppPlan(Gym $gym, string $planKey): void
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
            'price' => 29.99,
            ...$payload,
        ]);
    }

    Cache::forget('plan_access:gym:'.$gym->id.':plan_key');
}

function makeClientAppProfile(Gym $gym, ?string $appUsername = 'cliente.demo', ?string $plainPassword = 'Clave12345'): Client
{
    return Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Cliente',
        'last_name' => 'App',
        'document_number' => 'DOC-APP-'.Str::upper(Str::random(6)),
        'app_username' => $appUsername,
        'app_password' => $plainPassword !== null ? Hash::make($plainPassword) : null,
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);
}

it('updates app username and resets app password for premium plan', function () {
    $gym = makeClientAppGym('app-manage');
    $owner = makeClientAppOwner($gym, 'app-manage@example.test');
    setClientAppPlan($gym, 'premium');
    $client = makeClientAppProfile($gym, 'old.user', 'OldClave123');

    $this->actingAs($owner)->patch(route('clients.app-account.update', [
        'contextGym' => $gym->slug,
        'client' => $client->id,
    ]), [
        'app_username' => 'nuevo.usuario',
        'active_tab' => 'app_access',
    ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'gym_id' => $gym->id,
        'app_username' => 'nuevo.usuario',
    ]);

    $this->actingAs($owner)->patch(route('clients.app-password.reset', [
        'contextGym' => $gym->slug,
        'client' => $client->id,
    ]), [
        'app_password' => 'NuevaClave123',
        'app_password_confirmation' => 'NuevaClave123',
        'active_tab' => 'app_access',
    ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]))
        ->assertSessionHas('status');

    $client->refresh();
    expect(Hash::check('NuevaClave123', (string) $client->app_password))->toBeTrue()
        ->and(Hash::check('OldClave123', (string) $client->app_password))->toBeFalse();
});

it('blocks app account management routes when plan has no client accounts feature', function () {
    $gym = makeClientAppGym('app-blocked');
    $owner = makeClientAppOwner($gym, 'app-blocked@example.test');
    setClientAppPlan($gym, 'basico');
    $client = makeClientAppProfile($gym);

    $this->actingAs($owner)->patch(route('clients.app-account.update', [
        'contextGym' => $gym->slug,
        'client' => $client->id,
    ]), [
        'app_username' => 'usuario.nuevo',
        'active_tab' => 'app_access',
    ])->assertForbidden();
});

it('returns 404 when trying to edit app account from another gym client', function () {
    $gymA = makeClientAppGym('app-tenant-a');
    $gymB = makeClientAppGym('app-tenant-b');
    $ownerA = makeClientAppOwner($gymA, 'app-tenant-a@example.test');

    setClientAppPlan($gymA, 'premium');
    setClientAppPlan($gymB, 'premium');

    $clientB = makeClientAppProfile($gymB, 'externo.user');

    $this->actingAs($ownerA)->patch(route('clients.app-account.update', [
        'contextGym' => $gymA->slug,
        'client' => $clientB->id,
    ]), [
        'app_username' => 'intruso.user',
        'active_tab' => 'app_access',
    ])->assertNotFound();
});

it('requires app username before resetting app password', function () {
    $gym = makeClientAppGym('app-reset-needs-user');
    $owner = makeClientAppOwner($gym, 'app-reset-needs-user@example.test');
    setClientAppPlan($gym, 'premium');
    $client = makeClientAppProfile($gym, null, null);

    $this->actingAs($owner)->patch(route('clients.app-password.reset', [
        'contextGym' => $gym->slug,
        'client' => $client->id,
    ]), [
        'app_password' => 'ClaveNueva456',
        'app_password_confirmation' => 'ClaveNueva456',
        'active_tab' => 'app_access',
    ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]))
        ->assertSessionHasErrors('app_username');
});

it('renders client detail with app access tab for premium plan', function () {
    $gym = makeClientAppGym('app-show-premium');
    $owner = makeClientAppOwner($gym, 'app-show-premium@example.test');
    setClientAppPlan($gym, 'premium');
    $client = makeClientAppProfile($gym, 'mostrar.usuario', 'ClaveShow123');

    $this->actingAs($owner)
        ->get(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]))
        ->assertOk()
        ->assertSeeText('Usuario app');
});
