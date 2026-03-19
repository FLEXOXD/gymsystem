<?php

use App\Models\Gym;
use App\Models\GymBranchLink;
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

it('blocks cashier legal modal acceptance because only principal owner can accept', function () {
    $gym = Gym::query()->create([
        'name' => 'Gym Cajero',
        'slug' => 'gym-cajero',
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);

    User::factory()->create([
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);

    $cashier = User::factory()->create([
        'gym_id' => $gym->id,
        'role' => User::ROLE_CASHIER,
    ]);

    $this->actingAs($cashier)
        ->from(route('panel.legacy'))
        ->post(route('legal.modal-acceptance.store'), [
            'accepted' => '1',
            'terms_version' => LegalTerms::VERSION,
            'location_permission' => 'skipped',
        ])
        ->assertRedirect(route('panel.legacy'))
        ->assertSessionHas('error', 'Solo el dueño principal de la sede principal puede aceptar condiciones legales.');

    expect(LegalAcceptance::query()->count())->toBe(0);
});

it('blocks branch owner legal acceptance because only principal gym can accept', function () {
    $hubGym = Gym::query()->create([
        'name' => 'Gym Principal',
        'slug' => 'gym-principal',
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);

    $branchGym = Gym::query()->create([
        'name' => 'Gym Sucursal',
        'slug' => 'gym-sucursal',
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);

    $hubOwner = User::factory()->create([
        'gym_id' => $hubGym->id,
        'role' => User::ROLE_OWNER,
    ]);

    $branchOwner = User::factory()->create([
        'gym_id' => $branchGym->id,
        'role' => User::ROLE_OWNER,
    ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'premium',
        'cash_managed_by_hub' => true,
        'status' => 'active',
        'created_by' => $hubOwner->id,
    ]);

    $this->actingAs($branchOwner)
        ->from(route('panel.legacy'))
        ->post(route('legal.modal-acceptance.store'), [
            'accepted' => '1',
            'terms_version' => LegalTerms::VERSION,
            'location_permission' => 'skipped',
        ])
        ->assertRedirect(route('panel.legacy'))
        ->assertSessionHas('error', 'Solo el dueño principal de la sede principal puede aceptar condiciones legales.');

    expect(LegalAcceptance::query()->count())->toBe(0);
});
