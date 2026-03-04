<?php

use App\Models\Client;
use App\Models\ClientCredential;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Avoid stale plan lookups between tests that reuse gym IDs.
    config(['plan_features.access_cache_seconds' => 0]);
    Cache::flush();
});

function phase6MakeGym(string $suffix): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-'.$suffix.'-'.strtolower((string) Str::random(8)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

function phase6MakeOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Phase 6 Owner',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

function phase6SetPlan(Gym $gym, string $planKey): void
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

function phase6CreateActiveClientWithMembership(Gym $gym, string $documentNumber, string $namePrefix = 'Cliente'): Client
{
    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => $namePrefix,
        'last_name' => 'Phase6',
        'document_number' => $documentNumber,
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);

    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Plan '.$namePrefix.' '.Str::upper(Str::random(4)),
        'duration_days' => 30,
        'price' => 35,
        'status' => 'active',
    ]);

    Membership::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'price' => $plan->price,
        'starts_at' => Carbon::today()->subDay()->toDateString(),
        'ends_at' => Carbon::today()->addDays(10)->toDateString(),
        'status' => 'active',
    ]);

    return $client;
}

it('registers reception check-in using document method', function () {
    $gym = phase6MakeGym('doc-method');
    $owner = phase6MakeOwner($gym, 'phase6-doc@example.test');
    $client = phase6CreateActiveClientWithMembership($gym, 'P6-DOC-001', 'Doc');

    $response = $this->actingAs($owner)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'P6-DOC-001',
    ]);

    $response->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('reason', 'success')
        ->assertJsonPath('method', 'document');

    $this->assertDatabaseHas('attendances', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
    ]);
});

it('registers reception check-in using rfid method', function () {
    $gym = phase6MakeGym('rfid-method');
    $owner = phase6MakeOwner($gym, 'phase6-rfid@example.test');
    $client = phase6CreateActiveClientWithMembership($gym, 'P6-RFID-DOC-001', 'Rfid');

    $credential = ClientCredential::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'type' => 'rfid',
        'value' => 'P6-RFID-001',
        'status' => 'active',
    ]);

    $response = $this->actingAs($owner)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'P6-RFID-001',
    ]);

    $response->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('reason', 'success')
        ->assertJsonPath('method', 'rfid');

    $this->assertDatabaseHas('attendances', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'credential_id' => $credential->id,
    ]);
});

it('registers reception check-in using qr method', function () {
    $gym = phase6MakeGym('qr-method');
    $owner = phase6MakeOwner($gym, 'phase6-qr@example.test');
    $client = phase6CreateActiveClientWithMembership($gym, 'P6-QR-DOC-001', 'Qr');

    $credential = ClientCredential::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'type' => 'qr',
        'value' => 'P6-QR-001',
        'status' => 'active',
    ]);

    $response = $this->actingAs($owner)->postJson(route('reception.check-in', [
        'contextGym' => $gym->slug,
    ]), [
        'value' => 'P6-QR-001',
    ]);

    $response->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('reason', 'success')
        ->assertJsonPath('method', 'qr');

    $this->assertDatabaseHas('attendances', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'credential_id' => $credential->id,
    ]);
});

it('registers check-in from client app using dynamic qr token', function () {
    $gym = phase6MakeGym('dynamic-qr');
    $owner = phase6MakeOwner($gym, 'phase6-dynamic@example.test');
    phase6SetPlan($gym, 'premium');

    $client = phase6CreateActiveClientWithMembership($gym, 'P6-DYN-DOC-001', 'Dynamic');
    $mobileSession = [
        'client_mobile' => [
            'client_id' => (int) $client->id,
            'gym_id' => (int) $gym->id,
            'login_at' => now()->toIso8601String(),
        ],
    ];

    $qrResponse = $this->actingAs($owner)->getJson(route('reception.mobile-qr', [
        'contextGym' => $gym->slug,
        'force' => 1,
        'rotate_seconds' => 60,
    ]));

    $qrResponse->assertOk()
        ->assertJsonPath('ok', true);

    $token = (string) $qrResponse->json('token');
    expect($token)->not->toBe('');

    $checkIn = $this->withSession($mobileSession)->postJson(route('client-mobile.check-in', [
        'gymSlug' => $gym->slug,
    ]), [
        'token' => 'GYMSYS-MOBILE|'.$token,
    ]);

    $checkIn->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('reason', 'success')
        ->assertJsonPath('method', 'mobile_proximity');

    $this->assertDatabaseHas('attendances', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
    ]);

    $this->withSession($mobileSession)->postJson(route('client-mobile.check-in', [
        'gymSlug' => $gym->slug,
    ]), [
        'token' => $token,
    ])->assertStatus(422)
        ->assertJsonPath('reason', 'token_invalid');
});

it('blocks dynamic qr feature when plan does not include client accounts', function () {
    $gym = phase6MakeGym('plan-isolation');
    $owner = phase6MakeOwner($gym, 'phase6-plan@example.test');
    phase6SetPlan($gym, 'basico');

    $this->actingAs($owner)->getJson(route('reception.mobile-qr', [
        'contextGym' => $gym->slug,
        'force' => 1,
    ]))
        ->assertStatus(403)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('feature', 'client_accounts');
});

it('does not allow check-in with credential value from another gym', function () {
    $gymA = phase6MakeGym('tenant-a');
    $gymB = phase6MakeGym('tenant-b');
    $ownerA = phase6MakeOwner($gymA, 'phase6-tenant-a@example.test');
    $clientB = phase6CreateActiveClientWithMembership($gymB, 'P6-EXT-DOC-001', 'External');

    ClientCredential::query()->create([
        'gym_id' => $gymB->id,
        'client_id' => $clientB->id,
        'type' => 'rfid',
        'value' => 'P6-EXT-RFID-001',
        'status' => 'active',
    ]);

    $response = $this->actingAs($ownerA)->postJson(route('reception.check-in', [
        'contextGym' => $gymA->slug,
    ]), [
        'value' => 'P6-EXT-RFID-001',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('reason', 'not_found');

    $this->assertDatabaseCount('attendances', 0);
});

it('isolates check-in between hub and linked branch context', function () {
    $hubGym = phase6MakeGym('hub-checkin');
    $branchGym = phase6MakeGym('branch-checkin');
    $hubOwner = phase6MakeOwner($hubGym, 'phase6-hub-owner@example.test');

    phase6SetPlan($hubGym, 'sucursales');
    phase6SetPlan($branchGym, 'premium');

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => true,
        'status' => 'active',
        'created_by' => $hubOwner->id,
    ]);

    $branchClient = phase6CreateActiveClientWithMembership($branchGym, 'P6-BR-DOC-001', 'Branch');
    $hubClient = phase6CreateActiveClientWithMembership($hubGym, 'P6-HUB-DOC-001', 'Hub');

    $this->actingAs($hubOwner)->postJson(route('reception.check-in', [
        'contextGym' => $branchGym->slug,
    ]), [
        'value' => 'P6-BR-DOC-001',
    ])->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('method', 'document');

    $this->actingAs($hubOwner)->postJson(route('reception.check-in', [
        'contextGym' => $branchGym->slug,
    ]), [
        'value' => 'P6-HUB-DOC-001',
    ])->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('reason', 'not_found');

    $this->assertDatabaseHas('attendances', [
        'gym_id' => $branchGym->id,
        'client_id' => $branchClient->id,
    ]);
    $this->assertDatabaseMissing('attendances', [
        'gym_id' => $branchGym->id,
        'client_id' => $hubClient->id,
    ]);
});

it('rejects dynamic qr token generated for another gym', function () {
    $gymA = phase6MakeGym('dynamic-a');
    $gymB = phase6MakeGym('dynamic-b');
    $ownerA = phase6MakeOwner($gymA, 'phase6-dyn-a@example.test');

    phase6SetPlan($gymA, 'premium');
    phase6SetPlan($gymB, 'premium');

    $clientB = phase6CreateActiveClientWithMembership($gymB, 'P6-DYN-B-DOC-001', 'MobileB');
    $mobileSessionB = [
        'client_mobile' => [
            'client_id' => (int) $clientB->id,
            'gym_id' => (int) $gymB->id,
            'login_at' => now()->toIso8601String(),
        ],
    ];

    $qrResponse = $this->actingAs($ownerA)->getJson(route('reception.mobile-qr', [
        'contextGym' => $gymA->slug,
        'force' => 1,
        'rotate_seconds' => 60,
    ]));

    $qrResponse->assertOk()
        ->assertJsonPath('ok', true);

    $foreignToken = (string) $qrResponse->json('token');
    expect($foreignToken)->not->toBe('');

    $this->withSession($mobileSessionB)->postJson(route('client-mobile.check-in', [
        'gymSlug' => $gymB->slug,
    ]), [
        'token' => $foreignToken,
    ])->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('reason', 'token_gym_mismatch');
});
