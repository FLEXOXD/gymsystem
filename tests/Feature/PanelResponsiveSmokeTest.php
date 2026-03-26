<?php

use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Subscription;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeResponsiveGym(string $label): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$label,
        'slug' => 'gym-'.$label.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

function makeResponsiveOwner(Gym $gym, string $email): User
{
    return User::query()->create([
        'name' => 'Owner '.$gym->name,
        'email' => $email,
        'password' => 'password',
        'gym_id' => $gym->id,
        'role' => User::ROLE_OWNER,
    ]);
}

function ensureActiveSubscription(Gym $gym): void
{
    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'status' => 'active',
            'starts_at' => now()->subDay()->toDateString(),
            'ends_at' => now()->addDays(30)->toDateString(),
        ]);
}

it('renders panel layout shell for owner without responsive regressions', function () {
    $gym = makeResponsiveGym('panel-shell');
    $owner = makeResponsiveOwner($gym, 'panel-shell-owner@example.test');
    ensureActiveSubscription($gym);
    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('panel.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('theme-mobile-nav', false)
        ->assertSee('panel-toast-stack', false)
        ->assertSee('data-tour="sidebar-brand"', false)
        ->assertSee('Reportes')
        ->assertDontSee('Cajeros');
});

it('renders clients and reception routes with shared panel responsive shell', function () {
    $gym = makeResponsiveGym('ops-shell');
    $owner = makeResponsiveOwner($gym, 'ops-shell-owner@example.test');
    ensureActiveSubscription($gym);
    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
        ]);

    $clientsResponse = $this->actingAs($owner)
        ->get(route('clients.index', ['contextGym' => $gym->slug]));

    $clientsResponse
        ->assertOk()
        ->assertSee('theme-mobile-nav', false)
        ->assertSee('panel-toast-stack', false)
        ->assertSee('Plan Control / Clientes')
        ->assertSee('Nuevo cliente')
        ->assertSee('clients-toolbar-shell', false)
        ->assertSee('Reportes');

    $receptionResponse = $this->actingAs($owner)
        ->get(route('reception.index', ['contextGym' => $gym->slug]));

    $receptionResponse
        ->assertOk()
        ->assertSee('theme-mobile-nav', false)
        ->assertSee('panel-toast-stack', false)
        ->assertSee('Recepcion Control')
        ->assertSee('Plan Control / 1 sede')
        ->assertSee('reception-result-shell', false)
        ->assertSee('reception-history-shell', false)
        ->assertSee('reception-modal-panel', false)
        ->assertSee('reception-history-modal-table', false)
        ->assertDontSee('QR dinamico no habilitado');
});

it('renders plan control client detail layer for basic plan owner', function () {
    $gym = makeResponsiveGym('control-client-detail');
    $owner = makeResponsiveOwner($gym, 'control-client-detail-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
        ]);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Andrea',
        'last_name' => 'Control',
        'document_number' => 'CC-'.strtolower(Str::random(6)),
        'phone' => '0999999999',
        'status' => 'active',
    ]);

    $response = $this->actingAs($owner)
        ->get(route('clients.show', ['contextGym' => $gym->slug, 'client' => $client->id]));

    $response
        ->assertOk()
        ->assertSee('Plan Control / Ficha')
        ->assertSee('Ir a recepcion')
        ->assertSee('Reporte del cliente');
});

it('renders plan control reports layer for basic plan owner', function () {
    $gym = makeResponsiveGym('control-reports');
    $owner = makeResponsiveOwner($gym, 'control-reports-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('reports.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Control / Reportes')
        ->assertSee('Detalle ingresos')
        ->assertSee('Estado membresias')
        ->assertDontSee('Disponible en planes Profesional, Premium y Sucursales.');
});

it('renders plan control cash layer for basic plan owner', function () {
    $gym = makeResponsiveGym('control-cash');
    $owner = makeResponsiveOwner($gym, 'control-cash-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('cash.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Control / Caja')
        ->assertSee('Abrir turno')
        ->assertSee('cash-surface-card', false)
        ->assertSee('cash-table-shell', false)
        ->assertSee('Ir a clientes');
});

it('renders plan control plans layer for basic plan owner', function () {
    $gym = makeResponsiveGym('control-plans');
    $owner = makeResponsiveOwner($gym, 'control-plans-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'basico',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('plans.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Control / Planes')
        ->assertSee('Nuevo plan')
        ->assertSee('Ir a caja')
        ->assertSee('plans-surface-card', false)
        ->assertSee('plans-table-shell', false)
        ->assertSee('plans-modal-form', false)
        ->assertDontSee('Este módulo no está disponible en tu plan actual. Para habilitar promociones cambia al Plan profesional o superior.');
});

it('renders professional commercial radar for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-shell');
    $owner = makeResponsiveOwner($gym, 'professional-shell-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('panel.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Profesional / Crecimiento')
        ->assertSee('Ventas e inventario')
        ->assertSee('Ver reportes');
});

it('renders professional client focus layer for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-clients');
    $owner = makeResponsiveOwner($gym, 'professional-clients-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('clients.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Profesional / Clientes')
        ->assertSee('Ver por vencer')
        ->assertSee('Planes y promos');
});

it('renders professional sales and products shells for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-sales-products');
    $owner = makeResponsiveOwner($gym, 'professional-sales-products-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $productsResponse = $this->actingAs($owner)
        ->get(route('products.index', ['contextGym' => $gym->slug]));

    $productsResponse
        ->assertOk()
        ->assertSee('Navegacion del modulo')
        ->assertSee('Catalogo de productos')
        ->assertSee('products-surface-card', false)
        ->assertSee('products-table-shell', false);

    $salesResponse = $this->actingAs($owner)
        ->get(route('sales.index', ['contextGym' => $gym->slug]));

    $salesResponse
        ->assertOk()
        ->assertSee('Centro comercial del gimnasio')
        ->assertSee('Control rapido de inventario')
        ->assertSee('sales-surface-card', false)
        ->assertSee('sales-table-shell', false);
});
it('renders professional sales inventory report shell for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-sales-report');
    $owner = makeResponsiveOwner($gym, 'professional-sales-report-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('reports.sales-inventory', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Comportamiento diario')
        ->assertSee('report-surface-card', false)
        ->assertSee('report-table-shell', false);
});

it('renders professional client earnings report shell for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-client-earnings');
    $owner = makeResponsiveOwner($gym, 'professional-client-earnings-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('reports.client-earnings', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Filtro de facturación por cliente')
        ->assertSee('Detalle por cliente')
        ->assertSee('report-client-surface-card', false)
        ->assertSee('report-client-table-shell', false)
        ->assertSee('report-client-filter-actions', false);
});

it('renders professional reports growth radar for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-reports');
    $owner = makeResponsiveOwner($gym, 'professional-reports-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('reports.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Profesional / Reportes')
        ->assertSee('Ganancias de clientes')
        ->assertSee('Planes y promos')
        ->assertSee('report-filter-toolbar', false)
        ->assertSee('report-chart-surface', false)
        ->assertSee('report-nav-actions', false)
        ->assertSee('data-tone="income"', false);
});

it('renders professional client commercial detail layer for professional plan owner', function () {
    $gym = makeResponsiveGym('professional-client-detail');
    $owner = makeResponsiveOwner($gym, 'professional-client-detail-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'profesional',
            'feature_version' => 'v1',
        ]);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Carlos',
        'last_name' => 'Cliente',
        'document_number' => 'CC-'.strtolower(Str::random(6)),
        'phone' => '0999999999',
        'status' => 'active',
    ]);

    $response = $this->actingAs($owner)
        ->get(route('clients.show', ['contextGym' => $gym->slug, 'client' => $client->id]));

    $response
        ->assertOk()
        ->assertSee('Plan Profesional / Cliente')
        ->assertSee('Cobrar membresia')
        ->assertSee('Planes y promos');
});

it('renders premium elite panel layer for premium plan owner', function () {
    $gym = makeResponsiveGym('premium-panel');
    $owner = makeResponsiveOwner($gym, 'premium-panel-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('panel.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Elite / Panel')
        ->assertSee('Portal cliente')
        ->assertSee('Gestionar clientes');
});

it('renders premium elite clients layer for premium plan owner', function () {
    $gym = makeResponsiveGym('premium-clients');
    $owner = makeResponsiveOwner($gym, 'premium-clients-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('clients.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Elite / Clientes')
        ->assertSee('Portal cliente')
        ->assertSee('Nuevo cliente');
});

it('renders premium elite reports layer for premium plan owner', function () {
    $gym = makeResponsiveGym('premium-reports');
    $owner = makeResponsiveOwner($gym, 'premium-reports-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
        ]);

    $response = $this->actingAs($owner)
        ->get(route('reports.index', ['contextGym' => $gym->slug]));

    $response
        ->assertOk()
        ->assertSee('Plan Elite / Reportes')
        ->assertSee('Portal cliente')
        ->assertSee('Ganancias de clientes');
});

it('renders premium elite client detail layer for premium plan owner', function () {
    $gym = makeResponsiveGym('premium-client-detail');
    $owner = makeResponsiveOwner($gym, 'premium-client-detail-owner@example.test');
    ensureActiveSubscription($gym);

    Subscription::query()
        ->where('gym_id', $gym->id)
        ->update([
            'plan_key' => 'premium',
            'feature_version' => 'v1',
        ]);

    $client = Client::query()->create([
        'gym_id' => $gym->id,
        'first_name' => 'Lucia',
        'last_name' => 'Premium',
        'document_number' => 'CC-'.strtolower(Str::random(6)),
        'phone' => '0999999999',
        'status' => 'active',
    ]);

    $response = $this->actingAs($owner)
        ->get(route('clients.show', ['contextGym' => $gym->slug, 'client' => $client->id]));

    $response
        ->assertOk()
        ->assertSee('Plan Elite / Cliente')
        ->assertSee('Configurar acceso app')
        ->assertSee('Reporte del cliente');
});

it('renders sucursales layer and branches module shell for multibranch owner', function () {
    $hubGym = makeResponsiveGym('branches-shell-hub');
    $branchGym = makeResponsiveGym('branches-shell-branch');
    $owner = makeResponsiveOwner($hubGym, 'branches-shell-owner@example.test');

    ensureActiveSubscription($hubGym);
    ensureActiveSubscription($branchGym);

    Subscription::query()
        ->where('gym_id', $hubGym->id)
        ->update([
            'plan_key' => 'sucursales',
            'feature_version' => 'v1',
        ]);

    GymBranchLink::query()->create([
        'hub_gym_id' => $hubGym->id,
        'branch_gym_id' => $branchGym->id,
        'branch_plan_key' => 'basico',
    ]);

    $principalResponse = $this->actingAs($owner)
        ->get(route('panel.index', ['contextGym' => $hubGym->slug]));

    $principalResponse
        ->assertOk()
        ->assertSee('Estas operando la sede principal de la red multisucursal.')
        ->assertSee('Cambiar a admin global')
        ->assertSee('Abrir modulo sucursales')
        ->assertSee('Clientes sede principal');

    $panelResponse = $this->actingAs($owner)
        ->get(route('panel.index', ['contextGym' => $hubGym->slug, 'scope' => 'global']));

    $panelResponse
        ->assertOk()
        ->assertSee('Plan Sucursales / Red operativa')
        ->assertSee('Abrir modulo sucursales')
        ->assertSee('Abrir sede principal')
        ->assertSee('Admin global')
        ->assertSee($hubGym->name)
        ->assertSee($branchGym->name)
        ->assertSee('branch-context-card block', false)
        ->assertDontSee('<details class="group branch-context-details" open>', false);

    $branchesResponse = $this->actingAs($owner)
        ->get(route('branches.index', ['contextGym' => $hubGym->slug, 'scope' => 'global']));

    $branchesResponse
        ->assertOk()
        ->assertSee('Modulo multisucursal')
        ->assertSee('Resumen consolidado')
        ->assertSee('Atajos de contexto')
        ->assertSee('Panel global')
        ->assertSee('Abrir panel')
        ->assertSee('Clientes')
        ->assertSee('Reportes')
        ->assertDontSee('Solo SuperAdmin');
});
