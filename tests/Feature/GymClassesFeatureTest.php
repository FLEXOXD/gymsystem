<?php

use App\Models\Client;
use App\Models\Gym;
use App\Models\GymClass;
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
    config(['plan_features.access_cache_seconds' => 0]);
    Cache::flush();
});

function makeGymClassesGym(string $suffix): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$suffix,
        'slug' => 'gym-class-'.$suffix.'-'.strtolower((string) Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
        'timezone' => 'America/Guayaquil',
    ]);
}

function makeGymClassesOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner Classes',
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

function setGymClassesPlan(Gym $gym, string $planKey): void
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
            'price' => 39.99,
            ...$payload,
        ]);
    }

    Cache::forget('plan_access:gym:'.$gym->id.':plan_key');
}

function makeGymClassesClient(Gym $gym, string $suffix = 'demo'): Client
{
    return Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Cliente',
        'last_name' => 'Clases '.$suffix,
        'document_number' => 'CLASS-'.Str::upper(Str::random(6)),
        'app_username' => 'cliente.'.$suffix.'.'.strtolower((string) Str::random(4)),
        'app_password' => bcrypt('Clave12345'),
        'phone' => null,
        'photo_path' => null,
        'status' => 'active',
    ]);
}

function makeGymClassesMembership(Gym $gym, Client $client): void
{
    $plan = Plan::query()->create([
        'gym_id' => $gym->id,
        'name' => 'Mensual Clases',
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
        'ends_at' => Carbon::today()->addDays(20)->toDateString(),
        'status' => 'active',
    ]);
}

function makeScheduledGymClass(Gym $gym, ?User $owner = null, string $name = 'Boxeo elite', array $overrides = []): GymClass
{
    return GymClass::query()->create([
        'gym_id' => $gym->id,
        'created_by' => $owner?->id,
        'updated_by' => $owner?->id,
        'name' => $name,
        'category' => 'Box',
        'level' => 'Intermedio',
        'instructor_name' => 'Coach Mia',
        'room_name' => 'Sala 1',
        'description' => 'Clase guiada para tecnica, cardio y potencia.',
        'price' => 0,
        'starts_at' => Carbon::now('America/Guayaquil')->addDay()->setTime(19, 0),
        'ends_at' => Carbon::now('America/Guayaquil')->addDay()->setTime(20, 0),
        'capacity' => 12,
        'allow_waitlist' => true,
        'status' => GymClass::STATUS_SCHEDULED,
        ...$overrides,
    ]);
}

function gymClassesClientSession(Gym $gym, Client $client): array
{
    return [
        'client_mobile' => [
            'client_id' => $client->id,
            'gym_id' => $gym->id,
            'login_at' => now()->toIso8601String(),
        ],
    ];
}

it('renders the admin classes module for premium plans', function () {
    $gym = makeGymClassesGym('admin-premium');
    $owner = makeGymClassesOwner($gym, 'classes-admin-premium@example.test');
    setGymClassesPlan($gym, 'premium');

    $this->actingAs($owner)
        ->get(route('classes.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Clases y reservas')
        ->assertSee('Nueva clase');
});

it('blocks the admin classes module for plans without class booking', function () {
    $gym = makeGymClassesGym('admin-basic');
    $owner = makeGymClassesOwner($gym, 'classes-admin-basic@example.test');
    setGymClassesPlan($gym, 'basico');

    $this->actingAs($owner)
        ->get(route('classes.index', ['contextGym' => $gym->slug]))
        ->assertForbidden();
});

it('renders the classes screen in the client pwa for premium plans', function () {
    $gym = makeGymClassesGym('client-screen');
    $owner = makeGymClassesOwner($gym, 'classes-client-screen@example.test');
    setGymClassesPlan($gym, 'premium');

    $client = makeGymClassesClient($gym, 'screen');
    makeScheduledGymClass($gym, $owner);

    $this->withSession(gymClassesClientSession($gym, $client))
        ->get(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'classes']))
        ->assertOk()
        ->assertSee('Agenda del gimnasio')
        ->assertSee('Calendario interactivo')
        ->assertSee('Boxeo elite')
        ->assertSee('Reservar cupo');
});

it('blocks the client pwa when the plan does not include client accounts', function () {
    $gym = makeGymClassesGym('client-basic');
    setGymClassesPlan($gym, 'profesional');

    $client = makeGymClassesClient($gym, 'basic');

    $this->withSession(gymClassesClientSession($gym, $client))
        ->get(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'classes']))
        ->assertForbidden();
});

it('creates a reservation from the client pwa when the membership is active', function () {
    $gym = makeGymClassesGym('client-reserve');
    $owner = makeGymClassesOwner($gym, 'classes-client-reserve@example.test');
    setGymClassesPlan($gym, 'premium');

    $client = makeGymClassesClient($gym, 'reserve');
    makeGymClassesMembership($gym, $client);
    $gymClass = makeScheduledGymClass($gym, $owner, 'Funcional prime');

    $this->withSession(gymClassesClientSession($gym, $client))
        ->post(route('client-mobile.classes.reserve', [
            'gymSlug' => $gym->slug,
            'gymClass' => $gymClass->id,
        ]))
        ->assertRedirect(route('client-mobile.app', [
            'gymSlug' => $gym->slug,
            'screen' => 'classes',
        ]))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('gym_class_reservations', [
        'gym_id' => $gym->id,
        'gym_class_id' => $gymClass->id,
        'client_id' => $client->id,
        'status' => 'reserved',
    ]);
});

it('blocks overlapping reservations for the same client in the client pwa', function () {
    $gym = makeGymClassesGym('client-overlap');
    $owner = makeGymClassesOwner($gym, 'classes-client-overlap@example.test');
    setGymClassesPlan($gym, 'premium');

    $client = makeGymClassesClient($gym, 'overlap');
    makeGymClassesMembership($gym, $client);

    $baseDay = Carbon::now('America/Guayaquil')->addDay()->startOfDay();
    $firstClass = makeScheduledGymClass($gym, $owner, 'Calimpstemia', [
        'starts_at' => $baseDay->copy()->setTime(17, 0),
        'ends_at' => $baseDay->copy()->setTime(19, 0),
    ]);
    $secondClass = makeScheduledGymClass($gym, $owner, 'Bailo terapia', [
        'starts_at' => $baseDay->copy()->setTime(18, 0),
        'ends_at' => $baseDay->copy()->setTime(19, 0),
    ]);

    $this->withSession(gymClassesClientSession($gym, $client))
        ->post(route('client-mobile.classes.reserve', [
            'gymSlug' => $gym->slug,
            'gymClass' => $firstClass->id,
        ]))
        ->assertRedirect(route('client-mobile.app', [
            'gymSlug' => $gym->slug,
            'screen' => 'classes',
        ]))
        ->assertSessionHas('status');

    $response = $this->withSession(gymClassesClientSession($gym, $client))
        ->post(route('client-mobile.classes.reserve', [
            'gymSlug' => $gym->slug,
            'gymClass' => $secondClass->id,
        ]), [
            'classes_modal_id' => 'classes-day-'.$baseDay->toDateString(),
        ]);

    $response
        ->assertRedirect(route('client-mobile.app', [
            'gymSlug' => $gym->slug,
            'screen' => 'classes',
        ]))
        ->assertSessionHasErrors(['classes'])
        ->assertSessionHas('classes_modal_id', 'classes-day-'.$baseDay->toDateString());

    expect(session('errors')->first('classes'))
        ->toContain('Calimpstemia')
        ->toContain('17:00')
        ->toContain('19:00')
        ->toContain('coincide en ese horario');

    $this->assertDatabaseHas('gym_class_reservations', [
        'gym_id' => $gym->id,
        'gym_class_id' => $firstClass->id,
        'client_id' => $client->id,
        'status' => 'reserved',
    ]);

    $this->assertDatabaseMissing('gym_class_reservations', [
        'gym_id' => $gym->id,
        'gym_class_id' => $secondClass->id,
        'client_id' => $client->id,
    ]);
});

it('shows separate open and edit actions in the agenda for owners', function () {
    $gym = makeGymClassesGym('admin-modal');
    $owner = makeGymClassesOwner($gym, 'classes-admin-modal@example.test');
    setGymClassesPlan($gym, 'premium');

    makeScheduledGymClass($gym, $owner, 'Ritmo power');

    $this->actingAs($owner)
        ->get(route('classes.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('class-edit-modal', false)
        ->assertSee('data-open-class-edit', false)
        ->assertSee('Abrir proxima clase')
        ->assertSee('Editar proxima clase')
        ->assertSee('data-open-class-edit', false);
});

it('renders the class detail without edit and notify cards', function () {
    $gym = makeGymClassesGym('detail-clean');
    $owner = makeGymClassesOwner($gym, 'classes-detail-clean@example.test');
    setGymClassesPlan($gym, 'premium');

    $gymClass = makeScheduledGymClass($gym, $owner, 'Calistenia focus');

    $this->actingAs($owner)
        ->get(route('classes.show', ['contextGym' => $gym->slug, 'gymClass' => $gymClass->id]))
        ->assertOk()
        ->assertSee('Reservas del cliente')
        ->assertDontSee('Editar clase')
        ->assertDontSee('Avisar a inscritos');
});

it('stores classes from separate date and time fields', function () {
    $gym = makeGymClassesGym('split-fields');
    $owner = makeGymClassesOwner($gym, 'classes-split-fields@example.test');
    setGymClassesPlan($gym, 'premium');

    $this->actingAs($owner)
        ->post(route('classes.store', ['contextGym' => $gym->slug]), [
            '_classes_form_mode' => 'create',
            'name' => 'Curso mensual',
            'category' => 'Yoga',
            'level' => 'Base',
            'instructor_name' => 'Coach Range',
            'room_name' => 'Sala larga',
            'description' => 'Clase extendida para probar rango.',
            'price' => '8.50',
            'capacity' => 18,
            'start_date' => '2026-03-26',
            'start_time' => '18:00',
            'end_date' => '2026-04-26',
            'end_time' => '19:00',
            'allow_waitlist' => '1',
            'status' => 'scheduled',
        ])
        ->assertRedirect(route('classes.index', ['contextGym' => $gym->slug]))
        ->assertSessionHas('status');

    $storedClass = GymClass::query()
        ->where('gym_id', $gym->id)
        ->where('name', 'Curso mensual')
        ->firstOrFail();

    expect($storedClass->starts_at?->format('Y-m-d H:i'))->toBe('2026-03-26 18:00');
    expect($storedClass->ends_at?->format('Y-m-d H:i'))->toBe('2026-04-26 19:00');
    expect((float) $storedClass->price)->toBe(8.5);
});

it('shows multi-day classes when the agenda is filtered inside their date range', function () {
    $gym = makeGymClassesGym('range-window');
    $owner = makeGymClassesOwner($gym, 'classes-range-window@example.test');
    setGymClassesPlan($gym, 'premium');

    makeScheduledGymClass($gym, $owner, 'Programa largo', [
        'starts_at' => Carbon::create(2026, 3, 26, 18, 0, 0, 'America/Guayaquil'),
        'ends_at' => Carbon::create(2026, 4, 26, 19, 0, 0, 'America/Guayaquil'),
    ]);

    $this->actingAs($owner)
        ->get(route('classes.index', [
            'contextGym' => $gym->slug,
            'date' => '2026-04-10',
        ]))
        ->assertOk()
        ->assertSee('Programa largo')
        ->assertSee('Fin 26/04/2026 | 19:00');
});

it('marks every day of a multi-day class in the client calendar', function () {
    Carbon::setTestNow(Carbon::create(2026, 3, 10, 9, 0, 0, 'America/Guayaquil'));

    try {
        $gym = makeGymClassesGym('calendar-range');
        $owner = makeGymClassesOwner($gym, 'classes-calendar-range@example.test');
        setGymClassesPlan($gym, 'premium');

        $client = makeGymClassesClient($gym, 'calendar-range');
        $start = Carbon::now('America/Guayaquil')->addDay()->setTime(18, 0);
        $middle = $start->copy()->addDay();
        $end = $start->copy()->addDays(2)->setTime(19, 0);

        makeScheduledGymClass($gym, $owner, 'Retiro intensivo', [
            'starts_at' => $start,
            'ends_at' => $end,
        ]);

        $this->withSession(gymClassesClientSession($gym, $client))
            ->get(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'classes']))
            ->assertOk()
            ->assertSee('3 dias activos')
            ->assertSee('classes-day-'.$start->toDateString(), false)
            ->assertSee('classes-day-'.$middle->toDateString(), false)
            ->assertSee('classes-day-'.$end->toDateString(), false);
    } finally {
        Carbon::setTestNow();
    }
});

it('allows reservations when a multi-day class does not overlap the daily time window', function () {
    Carbon::setTestNow(Carbon::create(2026, 3, 26, 9, 0, 0, 'America/Guayaquil'));

    try {
        $gym = makeGymClassesGym('range-no-overlap');
        $owner = makeGymClassesOwner($gym, 'classes-range-no-overlap@example.test');
        setGymClassesPlan($gym, 'premium');

        $client = makeGymClassesClient($gym, 'range-no-overlap');
        makeGymClassesMembership($gym, $client);

        $reservedClass = makeScheduledGymClass($gym, $owner, 'Bailo terapia', [
            'starts_at' => Carbon::create(2026, 3, 26, 18, 0, 0, 'America/Guayaquil'),
            'ends_at' => Carbon::create(2026, 3, 26, 19, 0, 0, 'America/Guayaquil'),
        ]);
        $rangeClass = makeScheduledGymClass($gym, $owner, 'Bailo calimstemico', [
            'starts_at' => Carbon::create(2026, 3, 26, 14, 0, 0, 'America/Guayaquil'),
            'ends_at' => Carbon::create(2026, 4, 26, 17, 0, 0, 'America/Guayaquil'),
        ]);

        $this->withSession(gymClassesClientSession($gym, $client))
            ->post(route('client-mobile.classes.reserve', [
                'gymSlug' => $gym->slug,
                'gymClass' => $reservedClass->id,
            ]))
            ->assertRedirect(route('client-mobile.app', [
                'gymSlug' => $gym->slug,
                'screen' => 'classes',
            ]))
            ->assertSessionHas('status');

        $this->withSession(gymClassesClientSession($gym, $client))
            ->post(route('client-mobile.classes.reserve', [
                'gymSlug' => $gym->slug,
                'gymClass' => $rangeClass->id,
            ]))
            ->assertRedirect(route('client-mobile.app', [
                'gymSlug' => $gym->slug,
                'screen' => 'classes',
            ]))
            ->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('gym_class_reservations', [
            'gym_id' => $gym->id,
            'gym_class_id' => $rangeClass->id,
            'client_id' => $client->id,
            'status' => 'reserved',
        ]);
    } finally {
        Carbon::setTestNow();
    }
});

it('formats multi-day schedules with separate date and time ranges in the client pwa', function () {
    Carbon::setTestNow(Carbon::create(2026, 3, 26, 9, 0, 0, 'America/Guayaquil'));

    try {
        $gym = makeGymClassesGym('range-format');
        $owner = makeGymClassesOwner($gym, 'classes-range-format@example.test');
        setGymClassesPlan($gym, 'premium');

        $client = makeGymClassesClient($gym, 'range-format');

        makeScheduledGymClass($gym, $owner, 'Bailo calimstemico', [
            'starts_at' => Carbon::create(2026, 3, 26, 14, 0, 0, 'America/Guayaquil'),
            'ends_at' => Carbon::create(2026, 4, 26, 17, 0, 0, 'America/Guayaquil'),
        ]);

        $this->withSession(gymClassesClientSession($gym, $client))
            ->get(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'classes']))
            ->assertOk()
            ->assertSee('jue. 26 mar. al dom. 26 abr. | 14:00 a 17:00');
    } finally {
        Carbon::setTestNow();
    }
});

it('shows free and paid price labels in admin and client class views', function () {
    $gym = makeGymClassesGym('price-badges');
    $owner = makeGymClassesOwner($gym, 'classes-price-badges@example.test');
    setGymClassesPlan($gym, 'premium');

    $client = makeGymClassesClient($gym, 'price-badges');

    makeScheduledGymClass($gym, $owner, 'Clase gratis', [
        'price' => 0,
    ]);
    makeScheduledGymClass($gym, $owner, 'Clase premium', [
        'price' => 12.5,
        'starts_at' => Carbon::now('America/Guayaquil')->addDays(2)->setTime(18, 0),
        'ends_at' => Carbon::now('America/Guayaquil')->addDays(2)->setTime(19, 0),
    ]);

    $this->actingAs($owner)
        ->get(route('classes.index', ['contextGym' => $gym->slug]))
        ->assertOk()
        ->assertSee('Gratis')
        ->assertSee('$12.50');

    $this->withSession(gymClassesClientSession($gym, $client))
        ->get(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'classes']))
        ->assertOk()
        ->assertSee('Gratis')
        ->assertSee('$12.50');
});
