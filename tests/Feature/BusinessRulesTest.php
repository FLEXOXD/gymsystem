<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeGym(string $suffix = 'main'): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) \Illuminate\Support\Str::random(8)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

function makeGymUser(Gym $gym, string $email = 'gym@example.test'): User
{
    return User::query()->create([
        'name' => 'Gym User',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
    ]);
}

it('does not allow check-in when membership is not active', function () {
    $gym = makeGym('checkin-no-membership');
    $user = makeGymUser($gym, 'checkin-no-membership@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Luis',
        'last_name' => 'Perez',
        'document_number' => 'DOC-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->postJson(route('reception.check-in'), [
        'value' => 'DOC-001',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('message', 'Membresia no vigente o inactiva.');
});

it('does not allow duplicate attendance on the same day', function () {
    $gym = makeGym('checkin-duplicate');
    $user = makeGymUser($gym, 'checkin-duplicate@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Ana',
        'last_name' => 'Lopez',
        'document_number' => 'DOC-002',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Mensual',
        'duration_days' => 30,
        'price' => 30,
        'status' => 'active',
    ]);

    Membership::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'starts_at' => Carbon::today()->toDateString(),
        'ends_at' => Carbon::today()->addDays(10)->toDateString(),
        'status' => 'active',
    ]);

    $first = $this->actingAs($user)->postJson(route('reception.check-in'), [
        'value' => 'DOC-002',
    ]);
    $first->assertOk()->assertJsonPath('ok', true);

    $second = $this->actingAs($user)->postJson(route('reception.check-in'), [
        'value' => 'DOC-002',
    ]);

    $second->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('message', 'Asistencia ya registrada hoy');
});

it('does not allow membership sale when cash session is closed', function () {
    $gym = makeGym('membership-cash');
    $user = makeGymUser($gym, 'membership-cash@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Mario',
        'last_name' => 'Rojas',
        'document_number' => 'DOC-003',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Base',
        'duration_days' => 30,
        'price' => 25,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->post(route('memberships.store'), [
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'starts_at' => Carbon::today()->toDateString(),
        'status' => 'active',
        'payment_method' => 'cash',
    ]);

    $response->assertRedirect(route('clients.show', $client->id));
    $response->assertSessionHasErrors(['cash']);

    $this->assertDatabaseCount('memberships', 0);
});

it('redirects gym users to subscription expired when subscription is suspended', function () {
    $gym = makeGym('suspended');
    $user = makeGymUser($gym, 'suspended@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'status' => 'suspended',
            'starts_at' => Carbon::today()->subMonth()->toDateString(),
            'ends_at' => Carbon::today()->subDays(5)->toDateString(),
        ]);

    $response = $this->actingAs($user)->get(route('clients.index'));

    $response->assertRedirect(route('subscription.expired'));
});

it('enforces separation between superadmin and gym users', function () {
    $gym = makeGym('tenant-role');
    $gymUser = makeGymUser($gym, 'tenant-role@example.test');

    $superadmin = User::query()->create([
        'name' => 'Super Admin',
        'email' => 'superadmin-role@example.test',
        'password' => 'password',
        'gym_id' => null,
    ]);

    $this->actingAs($superadmin)
        ->get(route('superadmin.subscriptions.index'))
        ->assertRedirect(route('superadmin.gyms.index'));

    $this->actingAs($gymUser)
        ->getJson(route('superadmin.subscriptions.index'))
        ->assertStatus(403);
});
