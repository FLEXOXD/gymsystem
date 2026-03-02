<?php

use App\Models\Gym;
use App\Models\PwaEvent;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makePwaGym(string $label): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$label,
        'slug' => 'gym-'.$label.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

function makePwaOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner '.$gym->name,
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

function setPwaSubscriptionActive(Gym $gym): void
{
    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'status' => 'active',
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addDays(30)->toDateString(),
        ]);
}

it('stores pwa telemetry events for authenticated gym users', function () {
    $gym = makePwaGym('metrics-store');
    $owner = makePwaOwner($gym, 'pwa-metrics-owner@example.test');
    setPwaSubscriptionActive($gym);

    $response = $this->actingAs($owner)
        ->postJson(route('pwa.events.store'), [
            'event_name' => 'sw_registered',
            'event_source' => 'web',
            'mode' => 'standalone',
            'context_gym_slug' => $gym->slug,
            'payload' => [
                'scope' => '/'.$gym->slug,
            ],
        ]);

    $response->assertOk()->assertJsonPath('ok', true);

    $event = PwaEvent::query()->first();
    expect($event)->not->toBeNull();
    expect((int) $event->gym_id)->toBe((int) $gym->id);
    expect((int) $event->user_id)->toBe((int) $owner->id);
    expect((string) $event->event_name)->toBe('sw_registered');
    expect((string) $event->mode)->toBe('standalone');
    expect((string) $event->context_gym_slug)->toBe($gym->slug);
});

it('rejects unsupported telemetry events', function () {
    $gym = makePwaGym('metrics-invalid');
    $owner = makePwaOwner($gym, 'pwa-metrics-invalid@example.test');
    setPwaSubscriptionActive($gym);

    $response = $this->actingAs($owner)
        ->postJson(route('pwa.events.store'), [
            'event_name' => 'unknown_event',
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['event_name']);

    $this->assertDatabaseCount('pwa_events', 0);
});

it('blocks guests from posting pwa telemetry', function () {
    $this->postJson(route('pwa.events.store'), [
        'event_name' => 'sw_registered',
    ])->assertStatus(401);
});
