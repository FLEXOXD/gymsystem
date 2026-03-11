<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Support\ClientAudit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function clientAuditMakeGym(string $suffix = 'main'): Gym
{
    $gym = Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) Str::random(8)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(30)->toDateString(),
        ]);

    return $gym;
}

function clientAuditMakeUser(Gym $gym, string $name, string $email, string $role = User::ROLE_OWNER): User
{
    return User::query()->create([
        'name' => $name,
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

it('preserves original client creator and keeps last management after archiving the cashier', function () {
    $gym = clientAuditMakeGym('audit-preserve');
    $owner = clientAuditMakeUser($gym, 'Owner Creator QA', 'owner-client-audit-preserve@example.test', User::ROLE_OWNER);
    $cashier = clientAuditMakeUser($gym, 'Cashier Manager QA', 'cashier-client-audit-preserve@example.test', User::ROLE_CASHIER);

    $this->actingAs($owner)
        ->post(route('clients.store', ['contextGym' => $gym->slug]), [
            'first_name' => 'Lucia',
            'last_name' => 'Control',
            'document_number' => 'AUD12345',
            'phone' => '0991234567',
            'gender' => 'female',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertSessionHas('status');

    $client = Client::query()
        ->where('gym_id', $gym->id)
        ->firstOrFail();

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'created_by' => $owner->id,
        'created_by_name_snapshot' => $owner->name,
        'created_by_role_snapshot' => User::ROLE_OWNER,
        'last_managed_by' => $owner->id,
        'last_managed_by_name_snapshot' => $owner->name,
        'last_managed_by_role_snapshot' => User::ROLE_OWNER,
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Auditoria',
        'duration_days' => 30,
        'price' => 39.99,
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->post(route('cash.open', ['contextGym' => $gym->slug]), [
            'opening_balance' => '20.00',
            'notes' => 'Apertura prueba auditoria',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $gym->slug]));

    $this->actingAs($cashier)
        ->post(route('memberships.store', ['contextGym' => $gym->slug]), [
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'starts_at' => Carbon::today()->toDateString(),
            'status' => 'active',
            'payment_method' => 'cash',
        ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'created_by' => $owner->id,
        'created_by_name_snapshot' => $owner->name,
        'created_by_role_snapshot' => User::ROLE_OWNER,
        'last_managed_by' => $cashier->id,
        'last_managed_by_name_snapshot' => $cashier->name,
        'last_managed_by_role_snapshot' => User::ROLE_CASHIER,
    ]);

    $this->actingAs($owner)
        ->delete(route('staff.cashiers.destroy', [
            'contextGym' => $gym->slug,
            'cashier' => $cashier->id,
        ]))
        ->assertRedirect(route('staff.index', ['contextGym' => $gym->slug]))
        ->assertSessionHas('status');

    $this->actingAs($owner)
        ->get(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]))
        ->assertOk()
        ->assertSee(ClientAudit::actorDisplay($owner->name, User::ROLE_OWNER))
        ->assertSee(ClientAudit::actorDisplay($cashier->name, User::ROLE_CASHIER))
        ->assertSee('Usuario archivado');
});

it('shows client creator separately from the cashier that generated the income', function () {
    $gym = clientAuditMakeGym('audit-report');
    $owner = clientAuditMakeUser($gym, 'Owner Report Creator QA', 'owner-client-audit-report@example.test', User::ROLE_OWNER);
    $cashier = clientAuditMakeUser($gym, 'Cashier Report Seller QA', 'cashier-client-audit-report@example.test', User::ROLE_CASHIER);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        ...ClientAudit::creationAttributesFromUser($owner),
        'first_name' => 'Mario',
        'last_name' => 'Seguimiento',
        'document_number' => 'REP12345',
        'phone' => '0997654321',
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Reporte',
        'duration_days' => 30,
        'price' => 45.00,
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->post(route('cash.open', ['contextGym' => $gym->slug]), [
            'opening_balance' => '15.00',
            'notes' => 'Apertura reporte auditoria',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $gym->slug]));

    $this->actingAs($cashier)
        ->post(route('memberships.store', ['contextGym' => $gym->slug]), [
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'starts_at' => Carbon::today()->toDateString(),
            'status' => 'active',
            'payment_method' => 'transfer',
        ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]));

    $this->actingAs($owner)
        ->get(route('reports.income', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Alta cliente')
        ->assertSee($client->full_name)
        ->assertSee(ClientAudit::actorDisplay($owner->name, User::ROLE_OWNER))
        ->assertSee($cashier->name);
});
