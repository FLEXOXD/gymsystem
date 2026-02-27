<?php

use App\Models\Gym;
use App\Models\LegalAcceptance;
use App\Models\User;
use App\Support\LegalTerms;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores first-login legal acceptance from modal and updates user legal flags', function () {
    $gym = Gym::query()->create([
        'name' => 'Gym Legal',
        'slug' => 'gym-legal',
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);

    $user = User::factory()->create([
        'gym_id' => $gym->id,
    ]);

    $this->actingAs($user)
        ->from(route('panel.legacy'))
        ->post(route('legal.modal-acceptance.store'), [
            'accepted' => '1',
            'terms_version' => LegalTerms::VERSION,
            'location_permission' => 'granted',
            'latitude' => '-2.170998',
            'longitude' => '-79.922359',
            'location_accuracy_m' => '21.4',
        ])
        ->assertRedirect(route('panel.legacy'))
        ->assertSessionHas('status');

    $acceptance = LegalAcceptance::query()->first();
    expect($acceptance)->not->toBeNull()
        ->and($acceptance->user_id)->toBe($user->id)
        ->and($acceptance->document_key)->toBe('all_terms')
        ->and($acceptance->legal_version)->toBe(LegalTerms::VERSION)
        ->and($acceptance->contract_code)->not->toBe('');

    $user->refresh();
    expect($user->legal_accepted_version)->toBe(LegalTerms::VERSION)
        ->and($user->legal_accepted_at)->not->toBeNull();
});

it('shows superadmin legal acceptances screen and streams contract pdf', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $acceptance = LegalAcceptance::query()->create([
        'user_id' => $superAdmin->id,
        'gym_id' => null,
        'full_name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'document_key' => 'all_terms',
        'document_label' => 'Aceptacion integral de condiciones legales',
        'legal_version' => LegalTerms::VERSION,
        'accepted' => true,
        'accepted_via' => 'login_modal',
        'session_id' => 'sess-demo',
        'source_url' => 'http://127.0.0.1:8000/superadmin/dashboard',
        'location_permission' => 'denied',
        'latitude' => null,
        'longitude' => null,
        'location_accuracy_m' => null,
        'contract_code' => 'ABCDEF1234567890ABCDEF1234567890',
        'accepted_at' => now(),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest Test Agent',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.legal-acceptances.index'))
        ->assertOk()
        ->assertSee('Aceptaciones legales');

    $this->actingAs($superAdmin)
        ->get(route('superadmin.legal-acceptances.contract.pdf', $acceptance->id))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

