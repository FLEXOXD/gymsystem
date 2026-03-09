<?php

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientCredential;
use App\Models\ClientFitnessProfile;
use App\Models\ClientPushSubscription;
use App\Models\ClientTrainingSession;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\PresenceSession;
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

it('requires attendance and completed training session before updating progress sections', function () {
    $gym = phase6MakeGym('training-progress');
    $owner = phase6MakeOwner($gym, 'phase6-training-progress@example.test');
    phase6SetPlan($gym, 'premium');

    $client = phase6CreateActiveClientWithMembership($gym, 'P6-TRAIN-DOC-001', 'Training');
    ClientFitnessProfile::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'age' => 28,
        'sex' => 'masculino',
        'height_cm' => 175,
        'weight_kg' => 78,
        'goal' => 'ganar_musculo',
        'experience_level' => 'intermedio',
        'days_per_week' => 4,
        'session_minutes' => 45,
        'limitations' => ['ninguna'],
        'body_metrics' => ['bmi' => 25.5],
        'onboarding_completed_at' => now(),
    ]);

    $mobileSession = [
        'client_mobile' => [
            'client_id' => (int) $client->id,
            'gym_id' => (int) $gym->id,
            'login_at' => now()->toIso8601String(),
        ],
    ];

    $this->withSession($mobileSession)->postJson(route('client-mobile.training.start', [
        'gymSlug' => $gym->slug,
    ]))
        ->assertStatus(422)
        ->assertJsonPath('ok', false)
        ->assertJsonPath('training_status.can_start', false);

    $qrResponse = $this->actingAs($owner)->getJson(route('reception.mobile-qr', [
        'contextGym' => $gym->slug,
        'force' => 1,
        'rotate_seconds' => 60,
    ]));
    $token = (string) $qrResponse->json('token');
    expect($token)->not->toBe('');

    $this->withSession($mobileSession)->postJson(route('client-mobile.check-in', [
        'gymSlug' => $gym->slug,
    ]), [
        'token' => 'GYMSYS-MOBILE|'.$token,
    ])->assertOk()->assertJsonPath('ok', true);

    expect(PresenceSession::query()
        ->forGym($gym->id)
        ->where('client_id', $client->id)
        ->open()
        ->count())->toBe(1);

    $start = $this->withSession($mobileSession)->postJson(route('client-mobile.training.start', [
        'gymSlug' => $gym->slug,
    ]));
    $start->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('training_status.is_active', true)
        ->assertJsonPath('training_status.can_finish', true)
        ->assertJsonPath('training_status.progress_unlocked', true);

    $finish = $this->withSession($mobileSession)->postJson(route('client-mobile.training.finish', [
        'gymSlug' => $gym->slug,
    ]));
    $finish->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('training_status.completed_today', true)
        ->assertJsonPath('training_status.progress_unlocked', true);

    expect(PresenceSession::query()
        ->forGym($gym->id)
        ->where('client_id', $client->id)
        ->open()
        ->count())->toBe(0);

    $this->assertDatabaseHas('presence_sessions', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'check_out_method' => 'training',
        'check_out_reason' => 'training_manual',
    ]);

    $this->assertDatabaseHas('client_training_sessions', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'status' => 'completed',
        'finish_reason' => 'manual',
    ]);

    $progress = $this->withSession($mobileSession)->getJson(route('client-mobile.progress', [
        'gymSlug' => $gym->slug,
    ]));
    $progress->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('progress.training_status.completed_today', true)
        ->assertJsonPath('progress.training_status.progress_unlocked', true);

    $timeline = $progress->json('progress.last30_timeline');
    expect(is_array($timeline))->toBeTrue();
    $hasTrainingMark = collect($timeline)->contains(static function ($item): bool {
        if (! is_array($item) || trim((string) ($item['date'] ?? '')) === '') {
            return false;
        }

        return ($item['status'] ?? '') === 'trained'
            || (bool) ($item['attended'] ?? false);
    });
    expect($hasTrainingMark)->toBeTrue();
    $visibleDates = collect($timeline)
        ->filter(static function ($item): bool {
            if (! is_array($item)) {
                return false;
            }

            if ((bool) ($item['is_placeholder'] ?? false)) {
                return false;
            }

            return trim((string) ($item['date'] ?? '')) !== '';
        })
        ->pluck('date')
        ->map(static fn ($value): string => (string) $value)
        ->sort()
        ->values();
    expect($visibleDates->isNotEmpty())->toBeTrue();

    $expectedMonthStart = Carbon::now()->startOfMonth()->toDateString();
    expect((string) $visibleDates->first())->toBe($expectedMonthStart);
});

it('builds a weighted monthly discipline leaderboard for client mobile', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-14 12:00:00', 'America/Guayaquil'));

    try {
        $gym = phase6MakeGym('mobile-leaderboard');
        $owner = phase6MakeOwner($gym, 'phase6-mobile-leaderboard@example.test');
        phase6SetPlan($gym, 'premium');

        $alpha = phase6CreateActiveClientWithMembership($gym, 'P6-LB-001', 'Alpha');
        $bravo = phase6CreateActiveClientWithMembership($gym, 'P6-LB-002', 'Bravo');
        $charlie = phase6CreateActiveClientWithMembership($gym, 'P6-LB-003', 'Charlie');
        $delta = phase6CreateActiveClientWithMembership($gym, 'P6-LB-004', 'Delta');
        $echo = phase6CreateActiveClientWithMembership($gym, 'P6-LB-005', 'Echo');
        $foxtrot = phase6CreateActiveClientWithMembership($gym, 'P6-LB-006', 'Foxtrot');

        foreach ([
            [$alpha, 60],
            [$bravo, 45],
            [$charlie, 60],
            [$delta, 120],
            [$echo, 90],
            [$foxtrot, 120],
        ] as [$client, $minutes]) {
            ClientFitnessProfile::query()->create([
                'gym_id' => $gym->id,
                'client_id' => $client->id,
                'age' => 29,
                'sex' => 'masculino',
                'height_cm' => 175,
                'weight_kg' => 76,
                'goal' => 'mantener_forma',
                'experience_level' => 'intermedio',
                'days_per_week' => 4,
                'session_minutes' => $minutes,
                'limitations' => ['ninguna'],
                'body_metrics' => ['bmi' => 24.8],
                'onboarding_completed_at' => now(),
            ]);
        }

        $monthStart = Carbon::now()->startOfMonth()->startOfDay();
        $registerSession = function (Client $client, Carbon $day, int $minutes) use ($gym, $owner): void {
            $checkInAt = $day->copy()->setTime(8, 0);
            $attendance = Attendance::query()->create([
                'gym_id' => $gym->id,
                'client_id' => $client->id,
                'credential_id' => null,
                'date' => $day->toDateString(),
                'time' => $checkInAt->format('H:i:s'),
                'created_by' => $owner->id,
            ]);

            PresenceSession::query()->create([
                'gym_id' => $gym->id,
                'client_id' => $client->id,
                'check_in_attendance_id' => $attendance->id,
                'check_in_by' => $owner->id,
                'check_in_method' => 'document',
                'check_in_at' => $checkInAt,
                'check_out_by' => $owner->id,
                'check_out_method' => 'training',
                'check_out_at' => $checkInAt->copy()->addMinutes($minutes),
                'check_out_reason' => 'training_manual',
            ]);
        };

        for ($offset = 0; $offset < 5; $offset++) {
            $day = $monthStart->copy()->addDays($offset);
            $registerSession($alpha, $day, 60);
            $registerSession($bravo, $day, 45);
            $registerSession($charlie, $day, 30);

            if ($offset < 4) {
                $registerSession($delta, $day, 120);
                $registerSession($echo, $day, 90);
            }

            if ($offset < 3) {
                $registerSession($foxtrot, $day, 120);
            }
        }

        $mobileSession = [
            'client_mobile' => [
                'client_id' => (int) $foxtrot->id,
                'gym_id' => (int) $gym->id,
                'login_at' => now()->toIso8601String(),
            ],
        ];

        $progress = $this->withSession($mobileSession)->getJson(route('client-mobile.progress', [
            'gymSlug' => $gym->slug,
        ]));

        $progress->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonCount(5, 'progress.leaderboard.entries')
            ->assertJsonPath('progress.leaderboard.entries.0.client_id', $alpha->id)
            ->assertJsonPath('progress.leaderboard.entries.1.client_id', $bravo->id)
            ->assertJsonPath('progress.leaderboard.entries.2.client_id', $charlie->id)
            ->assertJsonPath('progress.leaderboard.entries.3.client_id', $delta->id)
            ->assertJsonPath('progress.leaderboard.entries.4.client_id', $echo->id)
            ->assertJsonPath('progress.leaderboard.current_client.rank', 6)
            ->assertJsonPath('progress.leaderboard.current_client.score_label', '45.0 pts')
            ->assertJsonPath('progress.leaderboard.window_label', 'Mes actual: 01 mar. - 31 mar.')
            ->assertJsonPath('progress.leaderboard.chip_label', 'Vas #6 del mes');

        $this->withSession($mobileSession)
            ->get(route('client-mobile.app', [
                'gymSlug' => $gym->slug,
            ]))
            ->assertOk()
            ->assertSee('id="leaderboard-toggle"', false)
            ->assertSee('id="leaderboard-modal"', false)
            ->assertSee('Top 5 del mes')
            ->assertSee('Corona mensual')
            ->assertSee('Mes actual', false);
    } finally {
        Carbon::setTestNow();
    }
});

it('accepts 120 minutes in fitness profile session duration', function () {
    $gym = phase6MakeGym('fitness-120');
    phase6SetPlan($gym, 'premium');
    $client = phase6CreateActiveClientWithMembership($gym, 'P6-FIT-120-001', 'Fit120');

    $mobileSession = [
        'client_mobile' => [
            'client_id' => (int) $client->id,
            'gym_id' => (int) $gym->id,
            'login_at' => now()->toIso8601String(),
        ],
    ];

    $response = $this->withSession($mobileSession)->post(route('client-mobile.fitness-profile.save', [
        'gymSlug' => $gym->slug,
    ]), [
        'age' => 29,
        'sex' => 'masculino',
        'height_cm' => 173,
        'weight_kg' => 77,
        'goal' => 'mantener_forma',
        'experience_level' => 'intermedio',
        'days_per_week' => 5,
        'session_minutes' => 120,
        'limitations' => ['ninguna'],
        'next_screen' => 'progress',
    ]);

    $response->assertRedirect(route('client-mobile.app', [
        'gymSlug' => $gym->slug,
        'screen' => 'progress',
    ]));

    $this->assertDatabaseHas('client_fitness_profiles', [
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'session_minutes' => 120,
    ]);
});

it('writes a dedupe cache key when training completion push is dispatched', function () {
    $gym = phase6MakeGym('training-push-dedupe');
    $owner = phase6MakeOwner($gym, 'phase6-training-push@example.test');
    phase6SetPlan($gym, 'premium');

    $client = phase6CreateActiveClientWithMembership($gym, 'P6-TRAIN-PUSH-001', 'Push');
    ClientFitnessProfile::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'age' => 30,
        'sex' => 'masculino',
        'height_cm' => 176,
        'weight_kg' => 79,
        'goal' => 'ganar_musculo',
        'experience_level' => 'intermedio',
        'days_per_week' => 4,
        'session_minutes' => 40,
        'limitations' => ['ninguna'],
        'body_metrics' => ['bmi' => 25.5],
        'onboarding_completed_at' => now(),
    ]);

    ClientPushSubscription::query()->create([
        'gym_id' => $gym->id,
        'client_id' => $client->id,
        'endpoint' => 'https://push.example.test/client/'.$client->id,
        'endpoint_hash' => hash('sha256', 'https://push.example.test/client/'.$gym->id.'-'.$client->id),
        'public_key' => 'test-public-key',
        'auth_token' => 'test-auth-token',
        'content_encoding' => 'aesgcm',
        'user_agent' => 'Pest/Feature',
        'device_name' => 'Test Device',
        'revoked_at' => null,
    ]);

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
    $token = (string) $qrResponse->json('token');
    expect($token)->not->toBe('');

    $this->withSession($mobileSession)->postJson(route('client-mobile.check-in', [
        'gymSlug' => $gym->slug,
    ]), [
        'token' => 'GYMSYS-MOBILE|'.$token,
    ])->assertOk()->assertJsonPath('ok', true);

    $this->withSession($mobileSession)->postJson(route('client-mobile.training.start', [
        'gymSlug' => $gym->slug,
    ]))->assertOk()->assertJsonPath('ok', true);

    $this->withSession($mobileSession)->postJson(route('client-mobile.training.finish', [
        'gymSlug' => $gym->slug,
    ]))->assertOk()->assertJsonPath('ok', true);

    $sessionDate = (string) (ClientTrainingSession::query()
        ->where('gym_id', $gym->id)
        ->where('client_id', $client->id)
        ->where('status', 'completed')
        ->value('session_date') ?? '');
    expect($sessionDate)->not->toBe('');
    $normalizedSessionDate = Carbon::parse($sessionDate)->toDateString();

    $cacheKey = 'client-mobile:push:training-completed:g'.$gym->id.':c'.$client->id.':d'.$normalizedSessionDate;
    expect(Cache::has($cacheKey))->toBeTrue();
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

it('redirects mobile scanner route to reception with modal flag regardless of client accounts plan feature', function () {
    $gym = phase6MakeGym('mobile-scanner-open');
    $owner = phase6MakeOwner($gym, 'phase6-mobile-scanner@example.test');
    phase6SetPlan($gym, 'basico');

    $this->actingAs($owner)
        ->get(route('reception.mobile-display', [
            'contextGym' => $gym->slug,
        ]))
        ->assertRedirect(route('reception.index', [
            'contextGym' => $gym->slug,
            'open_mobile_scanner' => 1,
        ]));
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
