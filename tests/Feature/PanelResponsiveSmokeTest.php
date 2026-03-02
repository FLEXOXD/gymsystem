<?php

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeResponsiveGym(string $label): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$label,
        'slug' => 'gym-'.$label.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

function makeResponsiveOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner '.$gym->name,
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

function ensureActiveSubscription(Gym $gym): void
{
    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'status' => 'active',
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addDays(30)->toDateString(),
        ]);
}

it('renders panel layout shell for owner without responsive regressions', function () {
    $gym = makeResponsiveGym('panel-shell');
    $owner = makeResponsiveOwner($gym, 'panel-shell-owner@example.test');
    ensureActiveSubscription($gym);

    $response = $this->actingAs($owner)
        ->get(route('panel.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('theme-mobile-nav', false)
        ->assertSee('panel-toast-stack', false)
        ->assertSee('data-tour="sidebar-brand"', false);
});

it('renders clients and reception routes with shared panel responsive shell', function () {
    $gym = makeResponsiveGym('ops-shell');
    $owner = makeResponsiveOwner($gym, 'ops-shell-owner@example.test');
    ensureActiveSubscription($gym);

    $clientsResponse = $this->actingAs($owner)
        ->get(route('clients.index', ['contextGym' => $gym->slug]));

    $clientsResponse
        ->assertOk()
        ->assertSee('theme-mobile-nav', false)
        ->assertSee('panel-toast-stack', false);

    $receptionResponse = $this->actingAs($owner)
        ->get(route('reception.index', ['contextGym' => $gym->slug]));

    $receptionResponse
        ->assertOk()
        ->assertSee('theme-mobile-nav', false)
        ->assertSee('panel-toast-stack', false);
});
