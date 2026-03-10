<?php

use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Gym;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeCashGym(string $suffix, string $timezone = 'America/Guayaquil'): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
        'timezone' => $timezone,
    ]);
}

function makeCashOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner Caja',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

it('auto closes open cash sessions when local midnight is reached', function () {
    $gym = makeCashGym('auto-midnight');
    $owner = makeCashOwner($gym, 'cash-auto-midnight@example.test');

    $session = CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $owner->id,
        'opened_at' => '2026-03-09 22:30:00',
        'opening_balance' => 100,
        'status' => 'open',
        'notes' => 'Apertura nocturna',
    ]);

    CashMovement::query()->create([
        'gym_id' => $gym->id,
        'cash_session_id' => $session->id,
        'type' => 'income',
        'amount' => 30,
        'method' => 'cash',
        'created_by' => $owner->id,
        'description' => 'Cobro de prueba',
        'occurred_at' => '2026-03-09 23:10:00',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-03-10 00:05:00', 'America/Guayaquil'));

    try {
        $this->artisan('cash:auto-close-midnight')
            ->assertSuccessful();
    } finally {
        Carbon::setTestNow();
    }

    $session->refresh();

    expect((string) $session->status)->toBe('closed');
    expect((string) $session->close_source)->toBe('auto_midnight');
    expect($session->closed_by)->toBeNull();
    expect((float) $session->closing_balance)->toBe(130.0);
    expect((float) $session->expected_balance)->toBe(130.0);
    expect((float) $session->difference)->toBe(0.0);
    expect((string) $session->getRawOriginal('closed_at'))->toBe('2026-03-10 00:00:00');
});

it('stores difference reason and closing notes on manual close', function () {
    $gym = makeCashGym('manual-close');
    $owner = makeCashOwner($gym, 'cash-manual-close@example.test');

    $this->actingAs($owner)
        ->post(route('cash.open', [
            'contextGym' => $gym->slug,
        ]), [
            'opening_balance' => '50.00',
            'notes' => 'Apertura inicial',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $gym->slug]));

    $response = $this->actingAs($owner)
        ->post(route('cash.close', [
            'contextGym' => $gym->slug,
        ]), [
            'closing_balance' => '55.00',
            'difference_reason' => 'Sobro efectivo por ajuste de vuelto',
            'notes' => 'Conteo revisado al final del turno',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');

    $session = CashSession::query()->where('gym_id', $gym->id)->firstOrFail();

    expect((string) $session->status)->toBe('closed');
    expect((string) $session->close_source)->toBe('manual');
    expect((string) $session->difference_reason)->toBe('Sobro efectivo por ajuste de vuelto');
    expect((string) $session->closing_notes)->toBe('Conteo revisado al final del turno');
    expect((string) $session->notes)->toBe('Apertura inicial');
});

it('shows recent closure history on the main cash screen', function () {
    $gym = makeCashGym('history-panel');
    $owner = makeCashOwner($gym, 'cash-history-panel@example.test');

    CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $owner->id,
        'opened_at' => '2026-03-09 08:00:00',
        'closed_at' => '2026-03-10 00:00:00',
        'opening_balance' => 40,
        'closing_balance' => 40,
        'expected_balance' => 40,
        'difference' => 0,
        'status' => 'closed',
        'notes' => 'Apertura anterior',
        'closing_notes' => 'Conteo revisado',
        'difference_reason' => 'Faltante por arqueo',
        'close_source' => 'auto_midnight',
    ]);

    CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $owner->id,
        'opened_at' => Carbon::now('America/Guayaquil')->format('Y-m-d 09:00:00'),
        'opening_balance' => 25,
        'status' => 'open',
        'notes' => 'Turno activo',
    ]);

    $this->actingAs($owner)
        ->get(route('cash.index', [
            'contextGym' => $gym->slug,
        ]))
        ->assertOk()
        ->assertSee('Historial reciente de cierres')
        ->assertSee('Caja cerrada automaticamente a medianoche')
        ->assertSee('Faltante por arqueo')
        ->assertSee('Conteo revisado')
        ->assertSee('Sistema');
});
