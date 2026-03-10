<?php

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientFitnessProfile;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeProgressGym(string $suffix): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
        'timezone' => 'America/Guayaquil',
    ]);
}

function makeProgressUser(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Progress User',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

it('renders the progress tab on client detail with computed metrics', function () {
    $gym = makeProgressGym('detail');
    $owner = makeProgressUser($gym, 'progress-detail@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Michael',
        'last_name' => 'Fajardo',
        'document_number' => 'PROG-001',
        'phone' => '099000111',
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Mensual',
        'duration_days' => 30,
        'price' => 35,
        'status' => 'active',
    ]);

    Membership::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'price' => 35,
        'starts_at' => Carbon::today()->subDays(5)->toDateString(),
        'ends_at' => Carbon::today()->addDays(24)->toDateString(),
        'status' => 'active',
    ]);

    foreach ([0, 2, 4] as $daysAgo) {
        Attendance::query()->create([
            'gym_id' => $gym->id,
            'client_id' => $client->id,
            'credential_id' => null,
            'date' => Carbon::today()->subDays($daysAgo)->toDateString(),
            'time' => '18:15:00',
            'created_by' => $owner->id,
        ]);
    }

    ClientFitnessProfile::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'age' => 31,
        'sex' => 'male',
        'height_cm' => 176,
        'weight_kg' => 82.5,
        'goal' => 'perder_grasa',
        'secondary_goal' => 'aumentar_fuerza',
        'experience_level' => 'intermedio',
        'days_per_week' => 4,
        'session_minutes' => 60,
        'limitations' => ['rodilla'],
    ]);

    $this->actingAs($owner)
        ->get(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'progress',
        ]))
        ->assertOk()
        ->assertSee('Ver rendimiento')
        ->assertSee('Analisis de rendimiento')
        ->assertSee('Meta semanal')
        ->assertSee('Estado del cuerpo')
        ->assertSee('Perfil fisico')
        ->assertSee('Perder grasa + Aumentar fuerza')
        ->assertSee('Secundario');
});

it('shows a progress shortcut on the clients index', function () {
    $gym = makeProgressGym('index');
    $owner = makeProgressUser($gym, 'progress-index@example.test');

    Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Diego',
        'last_name' => 'Vargas',
        'document_number' => 'PROG-INDEX-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->get(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Rendimiento')
        ->assertSee('tab=progress', false);
});
