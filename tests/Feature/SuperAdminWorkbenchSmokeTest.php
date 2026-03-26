<?php

use App\Models\LandingContactMessage;
use App\Models\LandingQuoteRequest;
use App\Models\SuperAdminPlanTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the refreshed superadmin inbox page', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    LandingContactMessage::query()->create([
        'first_name' => 'Andrea',
        'last_name' => 'Morales',
        'email' => 'andrea@example.com',
        'message' => 'Necesito ordenar caja y recepcion.',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.inbox.index'))
        ->assertOk()
        ->assertSee('Inbox comercial')
        ->assertSee('Bandeja de contacto web')
        ->assertSee('Andrea Morales');
});

it('renders the refreshed superadmin quotations page', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    LandingQuoteRequest::query()->create([
        'first_name' => 'Cliente',
        'last_name' => 'Lead',
        'email' => 'lead@example.com',
        'phone_country_code' => '+593',
        'phone_number' => '099000111',
        'country' => 'Ecuador',
        'professionals_count' => 12,
        'requested_plan' => 'sucursales',
        'source' => 'pricing_sucursales',
        'notes' => 'Quiere centralizar caja y reportes.',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.quotations.index'))
        ->assertOk()
        ->assertSee('Solicitudes de cotización')
        ->assertSee('Leads comerciales')
        ->assertSee('Cliente Lead');
});

it('renders the refreshed superadmin plans page', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    SuperAdminPlanTemplate::ensureDefaultCatalog();

    $this->actingAs($superAdmin)
        ->get(route('superadmin.plan-templates.index'))
        ->assertOk()
        ->assertSee('Planes base y promociones')
        ->assertSee('Promociones comerciales');
});

it('renders the refreshed superadmin notifications page', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.notifications.index'))
        ->assertOk()
        ->assertSee('Notificaciones')
        ->assertSee('Campañas push')
        ->assertSee('Bandeja de notificaciones');
});

it('renders the refreshed superadmin notifications history page', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.notifications.history'))
        ->assertOk()
        ->assertSee('Historial de notificaciones')
        ->assertSee('Historial');
});

it('renders the refreshed superadmin suggestions page', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.suggestions.index'))
        ->assertOk()
        ->assertSee('Sugerencias de gimnasios')
        ->assertSee('Feedback de gimnasios');
});
