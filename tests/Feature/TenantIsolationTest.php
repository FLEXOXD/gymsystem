<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
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

it('does not leak duplicate document checks across gyms', function () {
    $gymA = makeIsolationGym('check-a');
    $gymB = makeIsolationGym('check-b');

    $userA = User::factory()->create([
        'gym_id' => $gymA->id,
    ]);

    Client::query()->create([
        'gym_id' => $gymB->id,
        'first_name' => 'Cliente',
        'last_name' => 'Oculto',
        'document_number' => 'DOC-UNICO-100',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $response = $this->actingAs($userA)
        ->getJson(route('clients.check-document', [
            'contextGym' => $gymA->slug,
            'document_number' => 'DOC-UNICO-100',
        ]));

    $response
        ->assertOk()
        ->assertJson([
            'exists' => false,
        ]);
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

it('redirects hub gym login to global scope when multi-branch is enabled and branches exist', function () {
    $hubGym = makeIsolationGym('hub-login');
    $branchGym = makeIsolationGym('branch-login');

    $hubUser = User::query()->create([
        'name' => 'Hub Login User',
        'email' => 'hub-login@example.test',
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

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'basico',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $this->post(route('login'), [
        'email' => $hubUser->email,
        'password' => 'password',
    ])->assertRedirect(route('panel.index', [
        'contextGym' => $hubGym->slug,
        'scope' => 'global',
    ]));
});

it('keeps normal login redirect for gym users without linked branches', function () {
    $gym = makeIsolationGym('single-login');
    $user = User::query()->create([
        'name' => 'Single Gym User',
        'email' => 'single-login@example.test',
        'password' => 'password',
        'gym_id' => $gym->id,
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('panel.index', [
        'contextGym' => $gym->slug,
    ]));
});
