<?php

use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Client;
use App\Models\Gym;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-03-10 16:00:00', 'America/Guayaquil'));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function cashierPrivacyMakeGym(string $suffix = 'main'): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) Str::random(8)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
        'timezone' => 'America/Guayaquil',
    ]);
}

function cashierPrivacyMakeUser(Gym $gym, string $email, string $role = User::ROLE_OWNER): User
{
    return User::query()->create([
        'name' => ucfirst($role).' Privacy',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => $role,
        'is_active' => true,
        'can_open_cash' => false,
        'can_close_cash' => false,
        'can_manage_cash_movements' => true,
    ]);
}

it('shows a cashier only their own income and movements in panel and cash', function () {
    $gym = cashierPrivacyMakeGym('panel');
    $owner = cashierPrivacyMakeUser($gym, 'owner-cashier-privacy-panel@example.test', User::ROLE_OWNER);
    $cashier = cashierPrivacyMakeUser($gym, 'cashier-cashier-privacy-panel@example.test', User::ROLE_CASHIER);

    $session = CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $owner->id,
        'opened_at' => now()->subHour(),
        'opening_balance' => 20,
        'status' => 'open',
    ]);

    CashMovement::query()->create([
        'gym_id' => $gym->id,
        'cash_session_id' => $session->id,
        'type' => 'income',
        'amount' => 111.11,
        'method' => 'cash',
        'created_by' => $owner->id,
        'description' => 'Cobro owner oculto',
        'occurred_at' => now()->subMinutes(20),
    ]);

    CashMovement::query()->create([
        'gym_id' => $gym->id,
        'cash_session_id' => $session->id,
        'type' => 'income',
        'amount' => 37.55,
        'method' => 'card',
        'created_by' => $cashier->id,
        'description' => 'Cobro cajero visible',
        'occurred_at' => now()->subMinutes(10),
    ]);

    $this->actingAs($cashier)
        ->get(route('panel.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Vista privada')
        ->assertSee('Cobro cajero visible')
        ->assertDontSee('Cobro owner oculto')
        ->assertSee('37.55')
        ->assertDontSee('111.11');

    $this->actingAs($cashier)
        ->get(route('cash.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Vista privada')
        ->assertSee('Cobro cajero visible')
        ->assertDontSee('Cobro owner oculto')
        ->assertSee('37.55')
        ->assertDontSee('111.11');
});

it('archives a cashier without deleting their clients or generated history', function () {
    $gym = cashierPrivacyMakeGym('archive');
    $owner = cashierPrivacyMakeUser($gym, 'owner-cashier-privacy-archive@example.test', User::ROLE_OWNER);
    $cashier = cashierPrivacyMakeUser($gym, 'cashier-cashier-privacy-archive@example.test', User::ROLE_CASHIER);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Privado',
        'document_number' => 'CLIENT-PRIVACY-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $session = CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $owner->id,
        'opened_at' => now()->subHour(),
        'opening_balance' => 15,
        'status' => 'open',
    ]);

    $movement = CashMovement::query()->create([
        'gym_id' => $gym->id,
        'cash_session_id' => $session->id,
        'type' => 'income',
        'amount' => 29.99,
        'method' => 'transfer',
        'created_by' => $cashier->id,
        'description' => 'Cobro archivado',
        'occurred_at' => now()->subMinutes(5),
    ]);

    $this->actingAs($owner)
        ->delete(route('staff.cashiers.destroy', [
            'contextGym' => $gym->slug,
            'cashier' => $cashier->id,
        ]))
        ->assertRedirect(route('staff.index', ['contextGym' => $gym->slug]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('users', [
        'id' => $cashier->id,
        'role' => User::ROLE_CASHIER,
        'is_active' => 0,
        'can_open_cash' => 0,
        'can_close_cash' => 0,
        'can_manage_cash_movements' => 0,
    ]);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'gym_id' => $gym->id,
    ]);

    $this->assertDatabaseHas('cash_movements', [
        'id' => $movement->id,
        'created_by' => $cashier->id,
        'description' => 'Cobro archivado',
    ]);
});

it('blocks cash history for cashiers without close permission and scopes it when allowed', function () {
    $gym = cashierPrivacyMakeGym('history');
    $owner = cashierPrivacyMakeUser($gym, 'owner-cashier-privacy-history@example.test', User::ROLE_OWNER);
    $cashier = cashierPrivacyMakeUser($gym, 'cashier-cashier-privacy-history@example.test', User::ROLE_CASHIER);

    $session = CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $owner->id,
        'closed_by' => $owner->id,
        'opened_at' => now()->subHours(3),
        'closed_at' => now()->subHour(),
        'opening_balance' => 10,
        'closing_balance' => 90,
        'expected_balance' => 90,
        'difference' => 0,
        'status' => 'closed',
        'close_source' => 'manual',
    ]);

    CashMovement::query()->create([
        'gym_id' => $gym->id,
        'cash_session_id' => $session->id,
        'type' => 'income',
        'amount' => 40,
        'method' => 'cash',
        'created_by' => $cashier->id,
        'description' => 'Cobro historial cajero',
        'occurred_at' => now()->subHours(2),
    ]);

    $this->actingAs($cashier)
        ->get(route('cash.sessions.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();

    $cashier->update(['can_close_cash' => true]);

    $this->actingAs($cashier)
        ->get(route('cash.sessions.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Sesiones donde participaste')
        ->assertDontSee('Esperado')
        ->assertDontSee('90.00');

    $this->actingAs($cashier)
        ->get(route('cash.sessions.show', [
            'contextGym' => $gym->slug,
            'session' => $session->id,
        ]))
        ->assertOk()
        ->assertSee('Tu detalle de caja')
        ->assertSee('40.00')
        ->assertDontSee('Diferencia:');
});
