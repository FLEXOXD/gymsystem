<?php

use App\Models\Client;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\GymBranchLink;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\PresenceSession;
use App\Models\Promotion;
use App\Models\Subscription;
use App\Models\SuperAdminPlanTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

function makeGymUser(Gym $gym, string $email = 'gym@example.test', string $role = User::ROLE_OWNER): User
{
    return User::query()->create([
        'name' => 'Gym User',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => $role,
    ]);
}

function makeCashierUser(Gym $gym, string $email = 'cashier@example.test'): User
{
    return makeGymUser($gym, $email, User::ROLE_CASHIER);
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

    $response = $this->actingAs($user)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'DOC-001',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('message', 'Membresía no vigente o inactiva.');
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

    $first = $this->actingAs($user)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'DOC-002',
    ]);
    $first->assertOk()->assertJsonPath('ok', true);

    $second = $this->actingAs($user)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'DOC-002',
    ]);

    $second->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('message', 'Asistencia ya registrada hoy');
});

it('tracks live present clients with check-in and check-out', function () {
    $gym = makeGym('presence-live');
    $user = makeGymUser($gym, 'presence-live@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Pedro',
        'last_name' => 'Vera',
        'document_number' => 'DOC-PRESENCE-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Presencia',
        'duration_days' => 30,
        'price' => 35,
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

    $checkIn = $this->actingAs($user)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'DOC-PRESENCE-001',
    ]);
    $checkIn->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('event_type', 'checkin');

    $this->assertDatabaseHas('presence_sessions', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'check_out_at' => null,
    ]);

    $this->actingAs($user)
        ->getJson(route('panel.live-clients', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertJsonPath('count', 1);

    $checkOut = $this->actingAs($user)->postJson(route('reception.check-out', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'DOC-PRESENCE-001',
    ]);
    $checkOut->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('reason', 'checkout_success')
        ->assertJsonPath('event_type', 'checkout');

    expect(PresenceSession::query()
        ->forGym($gym->id)
        ->where('client_id', $client->id)
        ->open()
        ->count())
        ->toBe(0);

    $this->actingAs($user)
        ->getJson(route('panel.live-clients', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertJsonPath('count', 0);

    $this->actingAs($user)
        ->postJson(route('reception.check-out', [
            'contextGym' => $gym->slug,
        ]), [
            'value' => 'DOC-PRESENCE-001',
        ])
        ->assertStatus(422)
        ->assertJsonPath('reason', 'not_inside');
});

it('isolates live present count between gyms', function () {
    $gymA = makeGym('presence-a');
    $gymB = makeGym('presence-b');
    $userA = makeGymUser($gymA, 'presence-a@example.test');
    $userB = makeGymUser($gymB, 'presence-b@example.test');

    $clientA = Client::query()->create([
        'gym_id' => $gymA->id,
        'first_name' => 'Ariel',
        'last_name' => 'Uno',
        'document_number' => 'DOC-PRES-A',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $planA = Plan::query()->create([
        'gym_id' => $gymA->id,
        'name' => 'Plan A',
        'duration_days' => 30,
        'price' => 30,
        'status' => 'active',
    ]);

    Membership::query()->create([
        'gym_id' => $gymA->id,
        'client_id' => $clientA->id,
        'plan_id' => $planA->id,
        'starts_at' => Carbon::today()->toDateString(),
        'ends_at' => Carbon::today()->addDays(10)->toDateString(),
        'status' => 'active',
    ]);

    $this->actingAs($userA)->postJson(route('reception.check-in', [
        'contextGym' => $gymA->slug,
    ]), [
        'value' => 'DOC-PRES-A',
    ])->assertOk();

    $this->actingAs($userA)
        ->getJson(route('panel.live-clients', ['contextGym' => $gymA->slug]))
        ->assertOk()
        ->assertJsonPath('count', 1);

    $this->actingAs($userB)
        ->getJson(route('panel.live-clients', ['contextGym' => $gymB->slug]))
        ->assertOk()
        ->assertJsonPath('count', 0);
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

    $response = $this->actingAs($user)->post(route('memberships.store', [
        'contextGym' => $gym->slug,
    ]), [
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'starts_at' => Carbon::today()->toDateString(),
        'status' => 'active',
        'payment_method' => 'cash',
    ]);

    $response->assertRedirect(route('clients.show', [
        'contextGym' => $gym->slug,
        'client' => $client->id,
        'tab' => 'membership',
    ]));
    $response->assertSessionHasErrors(['cash']);

    $this->assertDatabaseCount('memberships', 0);
});

it('allows cashier to sell membership and charge on owner-opened cash session', function () {
    $gym = makeGym('cashier-membership-sale');
    $owner = makeGymUser($gym, 'owner-cashier-membership-sale@example.test', User::ROLE_OWNER);
    $cashier = makeCashierUser($gym, 'cashier-membership-sale@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Cajero',
        'last_name' => 'Cliente',
        'document_number' => 'CAJERO-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Cajero',
        'duration_days' => 30,
        'price' => 35,
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->post(route('cash.open', [
            'contextGym' => $gym->slug,
        ]), [
            'opening_balance' => '25.00',
            'notes' => 'Apertura dueno QA',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $gym->slug]));

    $response = $this->actingAs($cashier)
        ->post(route('memberships.store', [
            'contextGym' => $gym->slug,
        ]), [
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'starts_at' => Carbon::today()->toDateString(),
            'status' => 'active',
            'payment_method' => 'cash',
        ]);

    $response
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('memberships', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'status' => 'active',
    ]);

    $this->assertDatabaseHas('cash_movements', [
        'gym_id' => $gym->id,
        'type' => 'income',
        'method' => 'cash',
        'created_by' => $cashier->id,
    ]);
});

it('stores the real payment date and shows future memberships as scheduled in client detail', function () {
    $gym = makeGym('scheduled-membership-detail');
    $owner = makeGymUser($gym, 'scheduled-membership-detail@example.test', User::ROLE_OWNER);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Paula',
        'last_name' => 'Agenda',
        'document_number' => 'SCHEDULED-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Programado',
        'duration_days' => 30,
        'price' => 42,
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->post(route('cash.open', [
            'contextGym' => $gym->slug,
        ]), [
            'opening_balance' => '35.00',
            'notes' => 'Apertura QA programada',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $gym->slug]));

    $startsAt = Carbon::today()->addDays(5);
    $paymentDate = Carbon::today()->subDays(3);

    $this->actingAs($owner)
        ->post(route('memberships.store', [
            'contextGym' => $gym->slug,
        ]), [
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'starts_at' => $startsAt->toDateString(),
            'status' => 'active',
            'payment_method' => 'transfer',
            'payment_received_at' => $paymentDate->toDateString(),
            'active_tab' => 'membership',
            'membership_form_mode' => 'create',
        ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertSessionHas('status');

    $membership = Membership::query()
        ->where('gym_id', $gym->id)
        ->where('client_id', $client->id)
        ->firstOrFail();

    $movement = CashMovement::query()
        ->where('gym_id', $gym->id)
        ->where('membership_id', $membership->id)
        ->firstOrFail();

    expect($movement->occurred_at?->toDateString())->toBe($paymentDate->toDateString());

    $this->actingAs($owner)
        ->get(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertOk()
        ->assertSee('Programada')
        ->assertSee($startsAt->translatedFormat('d M Y'));
});

it('stores membership adjustments with separate history from client detail flow', function () {
    $gym = makeGym('membership-adjustment-history');
    $owner = makeGymUser($gym, 'membership-adjustment-history@example.test', User::ROLE_OWNER);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Camila',
        'last_name' => 'Ajuste',
        'document_number' => 'ADJUST-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Ajustable',
        'duration_days' => 30,
        'price' => 50,
        'status' => 'active',
    ]);

    $membership = Membership::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'price' => 50,
        'starts_at' => Carbon::today()->toDateString(),
        'ends_at' => Carbon::today()->addDays(29)->toDateString(),
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->patch(route('memberships.adjust', [
            'contextGym' => $gym->slug,
            'membership' => $membership->id,
        ]), [
            'client_id' => $client->id,
            'active_tab' => 'membership',
            'membership_form_mode' => 'adjustment',
            'adjust_membership_id' => $membership->id,
            'adjustment_type' => 'extend_access',
            'reason' => 'grace_period',
            'extra_days' => 7,
            'notes' => 'Cierre parcial por mantenimiento',
        ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertSessionHas('status');

    $membership->refresh();

    expect($membership->ends_at?->toDateString())->toBe(Carbon::today()->addDays(36)->toDateString());

    $this->assertDatabaseHas('membership_adjustments', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'membership_id' => $membership->id,
        'type' => 'extend_access',
        'reason' => 'grace_period',
        'days_delta' => 7,
    ]);

    $this->actingAs($owner)
        ->get(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertOk()
        ->assertSee('Historial de ajustes')
        ->assertSee('Sumar días al final')
        ->assertSee('Prórroga o permiso temporal');
});

it('rejects illogical reason combinations for membership adjustments', function () {
    $gym = makeGym('membership-adjust-invalid-reason');
    $owner = makeGymUser($gym, 'membership-adjust-invalid-reason@example.test', User::ROLE_OWNER);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Lucia',
        'last_name' => 'Regla',
        'document_number' => 'ADJUST-INVALID-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Reglas',
        'duration_days' => 30,
        'price' => 60,
        'status' => 'active',
    ]);

    $membership = Membership::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'price' => 60,
        'starts_at' => Carbon::today()->toDateString(),
        'ends_at' => Carbon::today()->addDays(29)->toDateString(),
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->from(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->patch(route('memberships.adjust', [
            'contextGym' => $gym->slug,
            'membership' => $membership->id,
        ]), [
            'client_id' => $client->id,
            'active_tab' => 'membership',
            'membership_form_mode' => 'adjustment',
            'adjust_membership_id' => $membership->id,
            'adjustment_type' => 'extend_access',
            'reason' => 'future_start_requested',
            'extra_days' => 5,
        ])
        ->assertRedirect(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertSessionHasErrors(['reason']);

    $membership->refresh();

    expect($membership->ends_at?->toDateString())->toBe(Carbon::today()->addDays(29)->toDateString());

    $this->assertDatabaseMissing('membership_adjustments', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'membership_id' => $membership->id,
        'type' => 'extend_access',
    ]);
});

it('blocks cashier from adjusting memberships and hides the action in client detail', function () {
    $gym = makeGym('cashier-membership-adjust-blocked');
    $owner = makeGymUser($gym, 'owner-membership-adjust-blocked@example.test', User::ROLE_OWNER);
    $cashier = makeCashierUser($gym, 'cashier-membership-adjust-blocked@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Diego',
        'last_name' => 'Control',
        'document_number' => 'ADJUST-BLOCK-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Seguro',
        'duration_days' => 30,
        'price' => 55,
        'status' => 'active',
    ]);

    $membership = Membership::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'price' => 55,
        'starts_at' => Carbon::today()->toDateString(),
        'ends_at' => Carbon::today()->addDays(29)->toDateString(),
        'status' => 'active',
    ]);

    $this->actingAs($cashier)
        ->get(route('clients.show', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
            'tab' => 'membership',
        ]))
        ->assertOk()
        ->assertDontSee('Ajustar membresía');

    $this->actingAs($cashier)
        ->patch(route('memberships.adjust', [
            'contextGym' => $gym->slug,
            'membership' => $membership->id,
        ]), [
            'client_id' => $client->id,
            'active_tab' => 'membership',
            'membership_form_mode' => 'adjustment',
            'adjust_membership_id' => $membership->id,
            'adjustment_type' => 'extend_access',
            'reason' => 'grace_period',
            'extra_days' => 3,
        ])
        ->assertStatus(403);

    $membership->refresh();

    expect($membership->ends_at?->toDateString())->toBe(Carbon::today()->addDays(29)->toDateString());

    $this->assertDatabaseMissing('membership_adjustments', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'membership_id' => $membership->id,
        'type' => 'extend_access',
    ]);
});

it('blocks cashier from opening and closing cash by default', function () {
    $gym = makeGym('cashier-open-close-blocked');
    $cashier = makeCashierUser($gym, 'cashier-open-close-blocked@example.test');

    $this->actingAs($cashier)
        ->from(route('cash.index', ['contextGym' => $gym->slug]))
        ->post(route('cash.open', [
            'contextGym' => $gym->slug,
        ]), [
            'opening_balance' => '30.00',
            'notes' => 'Intento apertura cajero',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $gym->slug]))
        ->assertSessionHasErrors(['cash']);

    $this->assertDatabaseCount('cash_sessions', 0);
});

it('blocks cashier from admin modules but allows operational modules', function () {
    $gym = makeGym('cashier-permissions');
    $cashier = makeCashierUser($gym, 'cashier-permissions@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(30)->toDateString(),
        ]);

    $this->actingAs($cashier)
        ->get(route('panel.index', ['contextGym' => $gym->slug]))
        ->assertOk();

    $this->actingAs($cashier)
        ->get(route('reception.index', ['contextGym' => $gym->slug]))
        ->assertOk();

    $this->actingAs($cashier)
        ->get(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertOk();

    $this->actingAs($cashier)
        ->get(route('cash.index', ['contextGym' => $gym->slug]))
        ->assertOk();

    $this->actingAs($cashier)
        ->get(route('plans.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();

    $this->actingAs($cashier)
        ->get(route('reports.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();

    $this->actingAs($cashier)
        ->get(route('branches.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();

    $this->actingAs($cashier)
        ->get(route('staff.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();
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

    $response = $this->actingAs($user)->get(route('clients.index', [
        'contextGym' => $gym->slug,
    ]));

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

it('allows superadmin to reset gym admin password', function () {
    $gym = makeGym('superadmin-password-reset');
    $gymAdmin = makeGymUser($gym, 'owner-password-reset@example.test');

    $superadmin = User::query()->create([
        'name' => 'Super Admin Password',
        'email' => 'superadmin-password-reset@example.test',
        'password' => 'password',
        'gym_id' => null,
    ]);

    $this->actingAs($superadmin)
        ->from(route('superadmin.gym.index'))
        ->patch(route('superadmin.gyms.admin-user.password.update', ['gym' => $gym->id]), [
            'reset_password_gym_id' => $gym->id,
            'reset_password_user_id' => $gymAdmin->id,
            'reset_password' => 'NuevaClave123!',
            'reset_password_confirmation' => 'NuevaClave123!',
        ])
        ->assertRedirect(route('superadmin.gym.index'))
        ->assertSessionHas('status', 'Contrasena del admin actualizada correctamente.');

    $gymAdmin->refresh();
    expect(Hash::check('NuevaClave123!', (string) $gymAdmin->password))->toBeTrue();
    expect($gymAdmin->remember_token)->toBeNull();
});

it('blocks gym users from resetting gym admin password', function () {
    $gym = makeGym('forbidden-password-reset');
    $gymAdmin = makeGymUser($gym, 'owner-forbidden-reset@example.test');
    $gymUser = makeGymUser($gym, 'staff-forbidden-reset@example.test');

    $this->actingAs($gymUser)
        ->patch(route('superadmin.gyms.admin-user.password.update', ['gym' => $gym->id]), [
            'reset_password_gym_id' => $gym->id,
            'reset_password_user_id' => $gymAdmin->id,
            'reset_password' => 'NuevaClave123!',
            'reset_password_confirmation' => 'NuevaClave123!',
        ])
        ->assertStatus(403);
});

it('allows custom renewal price for sucursales plan per gym', function () {
    SuperAdminPlanTemplate::ensureDefaultCatalog();

    $gym = makeGym('custom-sucursales-price');
    $superAdmin = User::query()->create([
        'name' => 'Super Admin Custom Price',
        'email' => 'superadmin-custom-price@example.test',
        'password' => 'password',
        'gym_id' => null,
    ]);

    $sucursalesTemplate = SuperAdminPlanTemplate::query()
        ->where('plan_key', 'sucursales')
        ->where('status', 'active')
        ->firstOrFail();

    $this->actingAs($superAdmin)
        ->post(route('superadmin.subscriptions.renew', ['gym' => $gym->id]), [
            'plan_template_id' => (int) $sucursalesTemplate->id,
            'payment_method' => 'efectivo',
            'custom_price' => '149.99',
        ])
        ->assertRedirect(route('superadmin.gyms.index'));

    $this->assertDatabaseHas('subscriptions', [
        'gym_id' => $gym->id,
        'plan_key' => 'sucursales',
        'price' => 149.99,
    ]);
});

it('applies 50 percent on first sucursales cycle and restores full price on next renewal', function () {
    SuperAdminPlanTemplate::ensureDefaultCatalog();

    $gym = makeGym('intro-sucursales-discount');
    $superAdmin = User::query()->create([
        'name' => 'Super Admin Intro',
        'email' => 'superadmin-intro-sucursales@example.test',
        'password' => 'password',
        'gym_id' => null,
    ]);

    $sucursalesTemplate = SuperAdminPlanTemplate::query()
        ->where('plan_key', 'sucursales')
        ->where('status', 'active')
        ->firstOrFail();

    $this->actingAs($superAdmin)
        ->post(route('superadmin.subscriptions.renew', ['gym' => $gym->id]), [
            'plan_template_id' => (int) $sucursalesTemplate->id,
            'payment_method' => 'efectivo',
            'custom_price' => '200.00',
            'apply_intro_50' => '1',
        ])
        ->assertRedirect(route('superadmin.gyms.index'));

    $firstCycleSubscription = Subscription::query()
        ->where('gym_id', $gym->id)
        ->firstOrFail();

    expect((string) $firstCycleSubscription->plan_key)->toBe('sucursales');
    expect((float) $firstCycleSubscription->price)->toBe(100.0);
    expect((bool) $firstCycleSubscription->sucursales_intro_pending)->toBeTrue();
    expect((float) $firstCycleSubscription->sucursales_base_price)->toBe(200.0);
    expect((int) $firstCycleSubscription->sucursales_intro_discount_percent)->toBe(50);

    $this->actingAs($superAdmin)
        ->post(route('superadmin.subscriptions.renew', ['gym' => $gym->id]), [
            'payment_method' => 'efectivo',
            'months' => 1,
        ])
        ->assertRedirect(route('superadmin.gyms.index'));

    $firstCycleSubscription->refresh();

    expect((float) $firstCycleSubscription->price)->toBe(200.0);
    expect((bool) $firstCycleSubscription->sucursales_intro_pending)->toBeFalse();
    expect($firstCycleSubscription->sucursales_base_price)->toBeNull();
    expect($firstCycleSubscription->sucursales_intro_discount_percent)->toBeNull();
});

it('blocks reports module when current plan does not include reports_base', function () {
    $gym = makeGym('reports-blocked');
    $user = makeGymUser($gym, 'reports-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($user)
        ->get(route('reports.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();
});

it('blocks reports export routes when current plan does not include reports_export', function () {
    $gym = makeGym('reports-export-blocked');
    $user = makeGymUser($gym, 'reports-export-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($user)
        ->get(route('reports.export.csv', ['contextGym' => $gym->slug]))
        ->assertForbidden();
});

it('returns json details when reports export is blocked by plan', function () {
    $gym = makeGym('reports-export-json-blocked');
    $user = makeGymUser($gym, 'reports-export-json-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($user)
        ->getJson(route('reports.export.csv', ['contextGym' => $gym->slug]))
        ->assertForbidden()
        ->assertJsonPath('ok', false)
        ->assertJsonPath('feature', 'reports_export')
        ->assertJsonPath('plan_key', 'basico');
});

it('allows reports export routes when current plan includes reports_export', function () {
    $gym = makeGym('reports-export-allowed');
    $user = makeGymUser($gym, 'reports-export-allowed@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $response = $this->actingAs($user)
        ->get(route('reports.export.csv', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

it('allows promotions endpoints when current plan includes promotions', function () {
    $gym = makeGym('promotions-allowed');
    $user = makeGymUser($gym, 'promotions-allowed@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $response = $this->actingAs($user)
        ->from(route('plans.index', ['contextGym' => $gym->slug]))
        ->post(route('plans.promotions.store', ['contextGym' => $gym->slug]), [
            // Missing required fields on purpose to validate middleware pass-through.
        ]);

    $response->assertStatus(302);
});

it('blocks promotions endpoints when current plan does not include promotions', function () {
    $gym = makeGym('promotions-blocked');
    $user = makeGymUser($gym, 'promotions-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($user)
        ->post(route('plans.promotions.store', ['contextGym' => $gym->slug]), [
            'name' => 'Promo no permitida',
            'type' => 'percentage',
            'value' => 10,
            'status' => 'active',
        ])
        ->assertForbidden();
});

it('blocks membership sale with promotion when current plan does not include promotions', function () {
    $gym = makeGym('membership-promo-blocked');
    $user = makeGymUser($gym, 'membership-promo-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Promo',
        'last_name' => 'Cliente',
        'document_number' => 'PROMO-CLI-001',
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

    $promotion = Promotion::query()->create([
        'gym_id' => $gym->id,
        'plan_id' => $plan->id,
        'name' => 'Promo bloqueada',
        'description' => null,
        'type' => 'percentage',
        'value' => 20,
        'starts_at' => Carbon::today()->subDay()->toDateString(),
        'ends_at' => Carbon::today()->addDays(7)->toDateString(),
        'status' => 'active',
        'max_uses' => null,
        'times_used' => 0,
    ]);

    $response = $this->actingAs($user)
        ->post(route('memberships.store', ['contextGym' => $gym->slug]), [
            'client_id' => $client->id,
            'plan_id' => $plan->id,
            'starts_at' => Carbon::today()->toDateString(),
            'status' => 'active',
            'payment_method' => 'cash',
            'promotion_id' => $promotion->id,
        ]);

    $response
        ->assertRedirect(route('clients.show', ['contextGym' => $gym->slug, 'client' => $client->id, 'tab' => 'membership']))
        ->assertSessionHasErrors(['promotion_id']);

    $this->assertDatabaseCount('memberships', 0);
});

it('blocks client onboarding membership promotion when current plan does not include promotions', function () {
    $gym = makeGym('client-onboarding-promo-blocked');
    $user = makeGymUser($gym, 'client-onboarding-promo-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan Base',
        'duration_days' => 30,
        'price' => 25,
        'status' => 'active',
    ]);

    $promotion = Promotion::query()->create([
        'gym_id' => $gym->id,
        'plan_id' => $plan->id,
        'name' => 'Promo onboarding bloqueada',
        'description' => null,
        'type' => 'percentage',
        'value' => 10,
        'starts_at' => Carbon::today()->subDay()->toDateString(),
        'ends_at' => Carbon::today()->addDays(7)->toDateString(),
        'status' => 'active',
        'max_uses' => null,
        'times_used' => 0,
    ]);

    CashSession::query()->create([
        'gym_id' => $gym->id,
        'opened_by' => $user->id,
        'opened_at' => now(),
        'opening_balance' => 100,
        'status' => 'open',
    ]);

    $this->actingAs($user)
        ->post(route('clients.store', ['contextGym' => $gym->slug]), [
            'first_name' => 'Nuevo',
            'last_name' => 'Cliente',
            'document_number' => 'ABC12345',
            'phone' => '0999999999',
            'gender' => 'neutral',
            'start_membership' => '1',
            'plan_id' => $plan->id,
            'membership_starts_at' => Carbon::today()->toDateString(),
            'membership_price' => 25,
            'promotion_id' => $promotion->id,
            'payment_method' => 'cash',
            'amount_paid' => 25,
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertSessionHasErrors(['promotion_id']);

    $this->assertDatabaseCount('clients', 0);
    $this->assertDatabaseCount('memberships', 0);
});

it('blocks branches module when current plan does not include multi_branch', function () {
    $gym = makeGym('branches-blocked');
    $user = makeGymUser($gym, 'branches-blocked@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($user)
        ->get(route('branches.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();
});

it('allows branches module view for multi_branch plan', function () {
    $gym = makeGym('branches-allowed-validation');
    $user = makeGymUser($gym, 'branches-allowed-validation@example.test');

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($user)
        ->get(route('branches.index', ['contextGym' => $gym->slug, 'scope' => 'global']))
        ->assertOk()
        ->assertSee('multisucursal');
});

it('creates and removes branch links from superadmin module', function () {
    $hubGym = makeGym('hub-branches');
    $superAdmin = User::query()->create([
        'name' => 'SuperAdmin',
        'email' => 'superadmin-branches@example.test',
        'password' => 'password',
        'gym_id' => null,
    ]);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    $this->actingAs($superAdmin)
        ->post(route('superadmin.branches.store'), [
            'hub_gym_id' => $hubGym->id,
            'branch_name' => 'Sucursal Nueva QA',
            'branch_phone' => '0991234567',
            'branch_country' => 'ec',
            'branch_state' => 'Guayas',
            'branch_city' => 'Guayaquil',
            'branch_address_line' => 'Av. Principal y Calle 1',
            'branch_plan_key' => 'premium',
            'branch_admin_name' => 'Admin Sucursal QA',
            'branch_admin_email' => 'admin-sucursal-qa@example.test',
            'branch_admin_password' => 'password123',
            'branch_admin_password_confirmation' => 'password123',
            'cash_managed_by_hub' => '1',
        ])
        ->assertRedirect(route('superadmin.branches.index'));

    $linkId = (int) GymBranchLink::query()
        ->where('hub_gym_id', $hubGym->id)
        ->latest('id')
        ->value('id');
    $branchGymId = (int) GymBranchLink::query()->where('id', $linkId)->value('branch_gym_id');

    $this->assertDatabaseHas('gym_branch_links', [
        'id' => $linkId,
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGymId,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
    ]);

    $this->assertDatabaseHas('subscriptions', [
        'gym_id' => $branchGymId,
        'plan_key' => 'premium',
        'billing_owner_gym_id' => $hubGym->id,
        'is_branch_managed' => 1,
    ]);

    $this->actingAs($superAdmin)
        ->delete(route('superadmin.branches.destroy', ['link' => $linkId]))
        ->assertRedirect(route('superadmin.branches.index'));

    $this->assertDatabaseMissing('gym_branch_links', [
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGymId,
    ]);
});

it('allows linked branch context and isolates clients module for hub user', function () {
    $hubGym = makeGym('hub-context-clients');
    $branchGym = makeGym('branch-context-clients');
    $hubUser = makeGymUser($hubGym, 'hub-context-clients@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    Client::query()->create([
        'gym_id' => $hubGym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Matriz',
        'document_number' => 'CTX-HUB-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    Client::query()->create([
        'gym_id' => $branchGym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Sucursal',
        'document_number' => 'CTX-BR-001',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($hubUser)
        ->getJson(route('clients.check-document', [
            'contextGym' => $branchGym->slug,
            'document_number' => 'CTX-BR-001',
        ]))
        ->assertOk()
        ->assertJsonPath('exists', true);

    $this->actingAs($hubUser)
        ->getJson(route('clients.check-document', [
            'contextGym' => $branchGym->slug,
            'document_number' => 'CTX-HUB-001',
        ]))
        ->assertOk()
        ->assertJsonPath('exists', false);
});

it('evaluates plan feature middleware using active branch context', function () {
    $hubGym = makeGym('hub-context-feature');
    $branchGym = makeGym('branch-context-feature');
    $hubUser = makeGymUser($hubGym, 'hub-context-feature@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($hubUser)
        ->get(route('reports.index', ['contextGym' => $branchGym->slug]))
        ->assertForbidden();
});

it('supports global scope lookups across linked branches', function () {
    $hubGym = makeGym('hub-global-scope');
    $branchGym = makeGym('branch-global-scope');
    $hubUser = makeGymUser($hubGym, 'hub-global-scope@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    Client::query()->create([
        'gym_id' => $hubGym->id,
        'first_name' => 'Hub',
        'last_name' => 'Cliente',
        'document_number' => 'DOC-HUB-GLB-1',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    Client::query()->create([
        'gym_id' => $branchGym->id,
        'first_name' => 'Branch',
        'last_name' => 'Cliente',
        'document_number' => 'DOC-BR-GLB-1',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($hubUser)
        ->getJson(route('clients.check-document', [
            'contextGym' => $hubGym->slug,
            'scope' => 'global',
            'document_number' => 'DOC-BR-GLB-1',
        ]))
        ->assertOk()
        ->assertJsonPath('exists', true);

    $this->actingAs($hubUser)
        ->getJson(route('clients.check-document', [
            'contextGym' => $hubGym->slug,
            'document_number' => 'DOC-BR-GLB-1',
        ]))
        ->assertOk()
        ->assertJsonPath('exists', false);
});

it('redirects global client detail view to the client branch context', function () {
    $hubGym = makeGym('hub-global-client-show');
    $branchGym = makeGym('branch-global-client-show');
    $hubUser = makeGymUser($hubGym, 'hub-global-client-show@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    $branchClient = Client::query()->create([
        'gym_id' => $branchGym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Sucursal',
        'document_number' => 'DOC-BR-SHOW-1',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($hubUser)
        ->get(route('clients.show', [
            'contextGym' => $hubGym->slug,
            'scope' => 'global',
            'client' => $branchClient->id,
        ]))
        ->assertRedirect(route('clients.show', [
            'contextGym' => $branchGym->slug,
            'client' => $branchClient->id,
        ]));
});

it('shows consolidated cashiers in global scope for hub owner', function () {
    $hubGym = makeGym('hub-global-staff');
    $branchGym = makeGym('branch-global-staff');
    $hubUser = makeGymUser($hubGym, 'hub-global-staff@example.test', User::ROLE_OWNER);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    $hubCashier = makeCashierUser($hubGym, 'hub-cashier-global-staff@example.test');
    $branchCashier = makeCashierUser($branchGym, 'branch-cashier-global-staff@example.test');
    $branchCashier->update(['is_active' => false]);

    $this->actingAs($hubUser)
        ->get(route('staff.index', [
            'contextGym' => $hubGym->slug,
            'scope' => 'global',
        ]))
        ->assertOk()
        ->assertSee('Resumen global de cajeros')
        ->assertSee('solo lectura')
        ->assertSee('Sede')
        ->assertSee($hubCashier->email)
        ->assertSee($branchCashier->email)
        ->assertDontSee('Crear cajero');
});

it('ignores global scope for branch users even when branch subscription is sucursales', function () {
    $hubGym = makeGym('hub-branch-global-guard');
    $branchGym = makeGym('branch-global-guard');
    $hubUser = makeGymUser($hubGym, 'hub-branch-global-guard@example.test');
    $branchUser = makeGymUser($branchGym, 'branch-global-guard@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    // Data anomaly: branch with sucursales too, should still not gain global/hub control.
    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    Client::query()->create([
        'gym_id' => $hubGym->id,
        'first_name' => 'Hub',
        'last_name' => 'Cliente',
        'document_number' => 'DOC-HUB-GUARD-1',
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($branchUser)
        ->getJson(route('clients.check-document', [
            'contextGym' => $branchGym->slug,
            'scope' => 'global',
            'document_number' => 'DOC-HUB-GUARD-1',
        ]))
        ->assertOk()
        ->assertJsonPath('exists', false);

    $this->actingAs($branchUser)
        ->get(route('branches.index', [
            'contextGym' => $branchGym->slug,
        ]))
        ->assertRedirect(route('panel.index', [
            'contextGym' => $branchGym->slug,
        ]))
        ->assertSessionHas('error');
});

it('blocks plan writes for branch users even when branch plan allows plans module', function () {
    $hubGym = makeGym('hub-branch-plan-write');
    $branchGym = makeGym('branch-plan-write');
    $hubUser = makeGymUser($hubGym, 'hub-branch-plan-write@example.test');
    $branchUser = makeGymUser($branchGym, 'branch-plan-write@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($branchUser)
        ->post(route('plans.store', [
            'contextGym' => $branchGym->slug,
        ]), [
            'name' => 'Plan no permitido',
            'duration_days' => 30,
            'price' => 20,
            'status' => 'active',
        ])
        ->assertForbidden();
});

it('blocks report exports for branch users even when branch plan includes exports', function () {
    $hubGym = makeGym('hub-branch-export');
    $branchGym = makeGym('branch-export');
    $hubUser = makeGymUser($hubGym, 'hub-branch-export@example.test');
    $branchUser = makeGymUser($branchGym, 'branch-export@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($branchUser)
        ->get(route('reports.export.csv', [
            'contextGym' => $branchGym->slug,
        ]))
        ->assertForbidden();
});

it('blocks reception check-in in global scope', function () {
    $hubGym = makeGym('hub-global-checkin');
    $branchGym = makeGym('branch-global-checkin');
    $hubUser = makeGymUser($hubGym, 'hub-global-checkin@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($hubUser)
        ->postJson(route('reception.check-in', [
            'contextGym' => $hubGym->slug,
            'scope' => 'global',
        ]), [
            'value' => 'DOC-ANY',
        ])
        ->assertStatus(409)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('reason', 'global_scope_blocked');
});

it('blocks cash opening for a branch user when cash is managed by hub', function () {
    $hubGym = makeGym('hub-cash-managed');
    $branchGym = makeGym('branch-cash-managed');
    $hubUser = makeGymUser($hubGym, 'hub-cash-managed@example.test');
    $branchUser = makeGymUser($branchGym, 'branch-cash-managed@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'basico',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $response = $this->actingAs($branchUser)->post(route('cash.open', [
        'contextGym' => $branchGym->slug,
    ]), [
        'opening_balance' => '50.00',
        'notes' => 'Intento de caja local',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseCount('cash_sessions', 0);
});

it('allows hub owner to open cash in linked branch when cash is managed by hub', function () {
    $hubGym = makeGym('hub-cash-open-branch');
    $branchGym = makeGym('branch-cash-open-branch');
    $hubUser = makeGymUser($hubGym, 'hub-cash-open-branch@example.test', User::ROLE_OWNER);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'basico',
        'cash_managed_by_hub' => 1,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($hubUser)
        ->post(route('cash.open', [
            'contextGym' => $branchGym->slug,
        ]), [
            'opening_balance' => '50.00',
            'notes' => 'Apertura sede principal',
        ])
        ->assertRedirect(route('cash.index', ['contextGym' => $branchGym->slug]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('cash_sessions', [
        'gym_id' => $branchGym->id,
        'opened_by' => $hubUser->id,
        'status' => 'open',
    ]);
});

it('updates gym avatars for the active context gym route', function () {
    Storage::fake('public');

    $hubGym = makeGym('hub-context-avatar-update');
    $branchGym = makeGym('branch-context-avatar-update');
    $hubUser = makeGymUser($hubGym, 'hub-context-avatar-update@example.test', User::ROLE_OWNER);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($hubUser)
        ->post(route('gym.settings.gym-avatars.update', [
            'contextGym' => $branchGym->slug,
        ]), [
            'avatar_male' => UploadedFile::fake()->image('branch-male.png', 900, 1200),
        ])
        ->assertRedirect();

    $hubGym->refresh();
    $branchGym->refresh();

    expect((string) ($hubGym->avatar_male_path ?? ''))->toBe('');
    expect((string) ($branchGym->avatar_male_path ?? ''))->not->toBe('');
    Storage::disk('public')->assertExists((string) $branchGym->avatar_male_path);
});

it('uses hub fallback avatars in branch reception when branch avatars are missing', function () {
    $hubGym = makeGym('hub-avatar-fallback');
    $branchGym = makeGym('branch-avatar-fallback');
    $hubUser = makeGymUser($hubGym, 'hub-avatar-fallback@example.test', User::ROLE_OWNER);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'created_by' => $hubUser->id,
    ]);

    $hubGym->update([
        'avatar_male_path' => 'gyms/avatars/hub-male-fallback.png',
        'avatar_female_path' => 'gyms/avatars/hub-female-fallback.png',
        'avatar_neutral_path' => 'gyms/avatars/hub-neutral-fallback.png',
    ]);

    $branchGym->update([
        'avatar_male_path' => null,
        'avatar_female_path' => null,
        'avatar_neutral_path' => null,
    ]);

    $this->actingAs($hubUser)
        ->get(route('reception.index', [
            'contextGym' => $branchGym->slug,
        ]))
        ->assertOk()
        ->assertSee('hub-male-fallback.png')
        ->assertSee('hub-female-fallback.png')
        ->assertSee('hub-neutral-fallback.png');
});

it('blocks cash opening for a branch user even when branch link allows local cash', function () {
    $hubGym = makeGym('hub-cash-local-flag');
    $branchGym = makeGym('branch-cash-local-flag');
    $hubUser = makeGymUser($hubGym, 'hub-cash-local-flag@example.test');
    $branchUser = makeGymUser($branchGym, 'branch-cash-local-flag@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => 0,
        'status' => 'active',
        'created_by' => $hubUser->id,
    ]);

    $this->actingAs($branchUser)
        ->post(route('cash.open', [
            'contextGym' => $branchGym->slug,
        ]), [
            'opening_balance' => '20.00',
            'notes' => 'Intento local con flag local',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('cash_sessions', 0);
});

it('blocks direct superadmin renew and suspend for managed branch subscriptions', function () {
    $hubGym = makeGym('hub-managed-subscription');
    $branchGym = makeGym('branch-managed-subscription');
    $superAdmin = User::query()->create([
        'name' => 'Super Admin Managed',
        'email' => 'superadmin-managed-subscription@example.test',
        'password' => 'password',
        'gym_id' => null,
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

    $originalEndsAt = Carbon::today()->addDays(20)->toDateString();
    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => $originalEndsAt,
            'billing_owner_gym_id' => $hubGym->id,
            'is_branch_managed' => 1,
        ]);

    $this->actingAs($superAdmin)
        ->post(route('superadmin.subscriptions.renew', ['gym' => $branchGym->id]), [
            'months' => 1,
            'payment_method' => 'efectivo',
        ])
        ->assertRedirect(route('superadmin.gyms.index'))
        ->assertSessionHasErrors(['subscription']);

    $this->actingAs($superAdmin)
        ->post(route('superadmin.subscriptions.suspend', ['gym' => $branchGym->id]))
        ->assertRedirect(route('superadmin.gyms.index'))
        ->assertSessionHasErrors(['subscription']);

    $this->assertDatabaseHas('subscriptions', [
        'gym_id' => $branchGym->id,
        'status' => 'active',
        'ends_at' => $originalEndsAt,
        'is_branch_managed' => 1,
        'billing_owner_gym_id' => $hubGym->id,
    ]);
});

it('allows owner to update a client basic profile', function () {
    $gym = makeGym('client-basic-update');
    $owner = makeGymUser($gym, 'client-basic-update@example.test', User::ROLE_OWNER);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Mario',
        'last_name' => 'Lopez',
        'document_number' => 'CLI-UPD-001',
        'phone' => '0990000001',
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->from(route('clients.index', ['contextGym' => $gym->slug]))
        ->patch(route('clients.basic.update', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]), [
            '_open_edit_modal' => '1',
            'edit_client_id' => $client->id,
            'edit_first_name' => 'Marco',
            'edit_last_name' => 'Quintana',
            'edit_phone' => '0991234567',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertSessionHas('status', 'Cliente actualizado correctamente.');

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'gym_id' => $gym->id,
        'first_name' => 'Marco',
        'last_name' => 'Quintana',
        'phone' => '0991234567',
    ]);
});

it('blocks cashier from editing and deleting clients', function () {
    $gym = makeGym('client-mutation-cashier');
    $cashier = makeCashierUser($gym, 'client-mutation-cashier@example.test');

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Caja',
        'document_number' => 'CLI-CASHIER-001',
        'phone' => '0991111111',
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($cashier)
        ->from(route('clients.index', ['contextGym' => $gym->slug]))
        ->patch(route('clients.basic.update', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]), [
            '_open_edit_modal' => '1',
            'edit_client_id' => $client->id,
            'edit_first_name' => 'No',
            'edit_last_name' => 'Permitido',
            'edit_phone' => '0992222222',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertSessionHasErrors(['clients']);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'first_name' => 'Cliente',
        'last_name' => 'Caja',
        'phone' => '0991111111',
    ]);

    $this->actingAs($cashier)
        ->from(route('clients.index', ['contextGym' => $gym->slug]))
        ->delete(route('clients.destroy', [
            'contextGym' => $gym->slug,
            'client' => $client->id,
        ]), [
            '_open_delete_modal' => '1',
            'delete_client_id' => $client->id,
            'owner_password' => 'password',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $gym->slug]))
        ->assertSessionHasErrors(['clients']);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'gym_id' => $gym->id,
    ]);
});

it('blocks branch local owner from editing and deleting branch clients', function () {
    $hubGym = makeGym('hub-branch-client-mutation');
    $branchGym = makeGym('branch-client-mutation');
    $hubOwner = makeGymUser($hubGym, 'hub-branch-client-mutation@example.test');
    $branchOwner = makeGymUser($branchGym, 'branch-client-mutation@example.test');

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'status' => 'active',
        'created_by' => $hubOwner->id,
    ]);

    $client = Client::query()->create([
        'gym_id' => $branchGym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Sucursal',
        'document_number' => 'CLI-BRANCH-OWNER-001',
        'phone' => '0993333333',
        'photo_path' => null,
        'status' => 'active',
    ]);

    $this->actingAs($branchOwner)
        ->from(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->patch(route('clients.basic.update', [
            'contextGym' => $branchGym->slug,
            'client' => $client->id,
        ]), [
            '_open_edit_modal' => '1',
            'edit_client_id' => $client->id,
            'edit_first_name' => 'Cambio',
            'edit_last_name' => 'Local',
            'edit_phone' => '0994444444',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->assertSessionHasErrors(['clients']);

    $this->actingAs($branchOwner)
        ->from(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->delete(route('clients.destroy', [
            'contextGym' => $branchGym->slug,
            'client' => $client->id,
        ]), [
            '_open_delete_modal' => '1',
            'delete_client_id' => $client->id,
            'owner_password' => 'password',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->assertSessionHasErrors(['clients']);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'gym_id' => $branchGym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Sucursal',
    ]);
});

it('requires the hub owner password to delete a branch client', function () {
    $hubGym = makeGym('hub-delete-branch-client');
    $branchGym = makeGym('branch-delete-branch-client');
    $hubOwner = makeGymUser($hubGym, 'hub-delete-branch-client@example.test');
    $branchOwner = makeGymUser($branchGym, 'branch-delete-branch-client@example.test');
    $hubOwner->update(['password' => 'HubSecret#2026']);
    $branchOwner->update(['password' => 'BranchSecret#2026']);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    Subscription::query()
        ->where('gym_id', $branchGym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
            'status' => 'active',
            'starts_at' => Carbon::today()->subDay()->toDateString(),
            'ends_at' => Carbon::today()->addDays(15)->toDateString(),
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'status' => 'active',
        'created_by' => $hubOwner->id,
    ]);

    $client = Client::query()->create([
        'gym_id' => $branchGym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Eliminar',
        'document_number' => 'CLI-BRANCH-DELETE-001',
        'phone' => '0995555555',
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $branchGym->id,
        'name' => 'Plan sucursal',
        'duration_days' => 30,
        'price' => 30,
        'status' => 'active',
    ]);

    $membership = Membership::query()->create([
        'gym_id' => $branchGym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'starts_at' => Carbon::today()->toDateString(),
        'ends_at' => Carbon::today()->addDays(30)->toDateString(),
        'status' => 'active',
    ]);

    $this->actingAs($hubOwner)
        ->from(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->delete(route('clients.destroy', [
            'contextGym' => $branchGym->slug,
            'client' => $client->id,
        ]), [
            '_open_delete_modal' => '1',
            'delete_client_id' => $client->id,
            'owner_password' => 'BranchSecret#2026',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->assertSessionHasErrors(['owner_password']);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'gym_id' => $branchGym->id,
    ]);

    $this->actingAs($hubOwner)
        ->from(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->delete(route('clients.destroy', [
            'contextGym' => $branchGym->slug,
            'client' => $client->id,
        ]), [
            '_open_delete_modal' => '1',
            'delete_client_id' => $client->id,
            'owner_password' => 'HubSecret#2026',
        ])
        ->assertRedirect(route('clients.index', ['contextGym' => $branchGym->slug]))
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('clients', [
        'id' => $client->id,
    ]);
    $this->assertDatabaseMissing('memberships', [
        'id' => $membership->id,
    ]);
});
