<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeIsolationGym(string $label): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$label,
        'slug' => 'gym-'.$label.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

it('forbids access to a different gym route context', function () {
    $gymA = makeIsolationGym('a');
    $gymB = makeIsolationGym('b');

    $userA = User::factory()->create([
        'gym_id' => $gymA->id,
    ]);

    $response = $this->actingAs($userA)
        ->get(route('panel.index', ['contextGym' => $gymB->slug]));

    $response->assertForbidden();
});

it('returns 404 when trying to read a client from another gym', function () {
    $gymA = makeIsolationGym('owner');
    $gymB = makeIsolationGym('other');

    $userA = User::factory()->create([
        'gym_id' => $gymA->id,
    ]);

    $otherClient = Client::query()->create([
        'gym_id' => $gymB->id,
        'first_name' => 'Cliente',
        'last_name' => 'Externo',
        'document_number' => 'EXT-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $response = $this->actingAs($userA)
        ->get(route('clients.show', [
            'contextGym' => $gymA->slug,
            'client' => $otherClient->id,
        ]));

    $response->assertNotFound();
});

it('redirects gym users from global settings urls to context urls', function () {
    $gym = makeIsolationGym('redirect');
    $user = User::factory()->create([
        'gym_id' => $gym->id,
    ]);

    $this->actingAs($user)
        ->get(route('profile.index'))
        ->assertRedirect(route('gym.profile.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('contact.index'))
        ->assertRedirect(route('gym.contact.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('settings.index'))
        ->assertRedirect(route('gym.settings.index', ['contextGym' => $gym->slug]));
});

it('does not redirect superadmin to gym context on global profile invoice route', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('profile.membership-invoice.pdf', ['subscription' => 1]))
        ->assertForbidden();
});
