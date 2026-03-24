<?php

use App\Http\Controllers\MarketingController;
use App\Models\DemoSession;
use App\Models\Gym;
use App\Models\LegalAcceptance;
use App\Models\LandingContactMessage;
use App\Models\LandingQuoteRequest;
use App\Models\SiteSetting;
use App\Models\SuperAdminPlanTemplate;
use App\Models\User;
use App\Support\MarketingContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

uses(RefreshDatabase::class);

function makeLandingGym(string $label): Gym
{
    return Gym::query()->create([
        'name' => 'Gym '.$label,
        'slug' => 'landing-'.$label.'-'.strtolower(Str::random(6)),
        'phone' => null,
        'address' => null,
        'logo_path' => null,
    ]);
}

/**
 * @return array<string, string>
 */
function marketingPayload(array $overrides = []): array
{
    return array_merge([
        'hero_kicker' => 'Software premium',
        'hero_title' => 'Titulo comercial actualizado',
        'hero_subtitle' => 'Subtitulo actualizado para la landing comercial.',
        'demo_button_label' => 'Demo instantanea',
        'demo_guided_button_label' => 'Pedir demo guiada',
        'whatsapp_url' => 'https://wa.me/593999999999?text=hola',
        'final_cta_title' => 'CTA final editado',
        'final_cta_text' => 'Texto final editado desde superadmin.',
        'guide_title' => 'Guia editada',
        'guide_subtitle' => 'Subtitulo de guia editado.',
        'guide_step_1_title' => 'Paso 1',
        'guide_step_1_text' => 'Texto paso 1',
        'guide_step_2_title' => 'Paso 2',
        'guide_step_2_text' => 'Texto paso 2',
        'guide_step_3_title' => 'Paso 3',
        'guide_step_3_text' => 'Texto paso 3',
        'guide_step_4_title' => 'Paso 4',
        'guide_step_4_text' => 'Texto paso 4',
        'guide_step_5_title' => 'Paso 5',
        'guide_step_5_text' => 'Texto paso 5',
    ], $overrides);
}

it('shows the commercial landing for guests', function () {
    $request = Request::create(route('landing'), 'GET');
    $response = app(MarketingController::class)->index($request);

    expect($response)
        ->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('marketing.home')
        ->and($response->getData())->toHaveKey('stats');
});

it('keeps about and contact sections out of the home page', function () {
    $this->get(route('landing'))
        ->assertOk()
        ->assertDontSee('id="nosotros"', false)
        ->assertDontSee('id="contacto"', false);
});

it('shows dedicated pages for about and contact', function () {
    $this->get(route('landing.about'))
        ->assertOk()
        ->assertSee('id="nosotros"', false)
        ->assertDontSee('id="contacto"', false);

    $this->get(route('landing.contact'))
        ->assertOk()
        ->assertSee('id="contacto"', false)
        ->assertDontSee('id="nosotros"', false);
});

it('renders the simplified contact page in spanish without mojibake', function () {
    $this->get(route('landing.contact'))
        ->assertOk()
        ->assertSee('CONTACTANOS')
        ->assertSee('Machachi, canton Mejia, Pichincha, Ecuador')
        ->assertSee('flexjok.agencia@gmail.com')
        ->assertSee('De 9:00 AM a 7:00 PM')
        ->assertDontSee('ContÃ¡ctanos')
        ->assertDontSee('Â¿Cuántos');
});

it('shows dedicated legal pages', function () {
    $this->get(route('landing.legal.privacy'))
        ->assertOk()
        ->assertSee('id="legal-privacy"', false)
        ->assertDontSee('id="nosotros"', false)
        ->assertDontSee('id="contacto"', false);

    $this->get(route('landing.legal.service'))
        ->assertOk()
        ->assertSee('id="legal-service"', false)
        ->assertDontSee('id="nosotros"', false)
        ->assertDontSee('id="contacto"', false);

    $this->get(route('landing.legal.commercial'))
        ->assertOk()
        ->assertSee('id="legal-commercial"', false)
        ->assertDontSee('id="nosotros"', false)
        ->assertDontSee('id="contacto"', false);
});

it('does not render quote modal triggers on the landing', function () {
    $this->get(route('landing'))
        ->assertOk()
        ->assertDontSee('data-open-quote-modal', false)
        ->assertDontSee('quote-request-modal', false)
        ->assertSee('Contáctanos');
});

it('renders decimal prices from superadmin plans on the landing', function () {
    SuperAdminPlanTemplate::ensureDefaultCatalog();

    SuperAdminPlanTemplate::query()
        ->where('plan_key', 'basico')
        ->update(['price' => 14.99]);

    $this->get(route('landing'))
        ->assertOk()
        ->assertSee('$14.99', false);
});

it('stores quote requests from the landing modal', function () {
    $this->from(route('landing'))
        ->post(route('landing.quote.store'), [
            'quote_first_name' => 'David',
            'quote_last_name' => 'Quintana',
            'quote_email' => 'ventas@example.com',
            'quote_phone_country_code' => '+593',
            'quote_phone_number' => '0987654321',
            'quote_country' => 'Ecuador',
            'quote_professionals_count' => 8,
            'quote_requested_plan' => 'sucursales',
            'quote_source' => 'hero_secondary',
            'quote_notes' => 'Necesito manejar una sede principal y dos sucursales.',
            'quote_privacy_accepted' => '1',
        ])
        ->assertRedirect(route('landing'))
        ->assertSessionHas('quote_status');

    $quote = LandingQuoteRequest::query()->first();

    expect($quote)->not->toBeNull()
        ->and($quote->first_name)->toBe('David')
        ->and($quote->last_name)->toBe('Quintana')
        ->and($quote->email)->toBe('ventas@example.com')
        ->and($quote->phone_country_code)->toBe('+593')
        ->and($quote->phone_number)->toBe('0987654321')
        ->and($quote->country)->toBe('Ecuador')
        ->and($quote->professionals_count)->toBe(8)
        ->and($quote->requested_plan)->toBe('sucursales')
        ->and($quote->source)->toBe('hero_secondary');
});

it('stores contact messages from the public contact page', function () {
    $this->from(route('landing.contact'))
        ->post(route('landing.contact.store'), [
            'first_name' => 'Andrea',
            'last_name' => 'Morales',
            'email' => 'andrea@example.com',
            'message' => 'Necesito ordenar caja, recepción y membresías en mi gimnasio.',
        ])
        ->assertRedirect(route('landing.contact').'#contacto')
        ->assertSessionHas('contact_status');

    $contact = LandingContactMessage::query()->first();

    expect($contact)->not->toBeNull()
        ->and($contact->first_name)->toBe('Andrea')
        ->and($contact->last_name)->toBe('Morales')
        ->and($contact->email)->toBe('andrea@example.com')
        ->and($contact->message)->toBe('Necesito ordenar caja, recepción y membresías en mi gimnasio.');
});

it('upgrades legacy whatsapp numbers to the current contact number', function () {
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_phone',
        'value' => '+593 99 106 6303',
    ]);
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_url',
        'value' => 'https://api.whatsapp.com/send?phone=593991066303&text=hola',
    ]);

    $content = MarketingContent::load();

    expect($content['whatsapp_phone'])->toBe('593995142566')
        ->and($content['whatsapp_url'])->toContain('phone=593995142566');
});

it('stores quote request timestamps using superadmin public timezone', function () {
    User::factory()->create([
        'gym_id' => null,
        'timezone' => 'America/Guayaquil',
    ]);

    config(['app.timezone' => 'UTC']);
    date_default_timezone_set('UTC');

    Carbon::setTestNow(Carbon::create(2026, 3, 8, 10, 42, 0, 'America/Guayaquil'));

    try {
        $this->post(route('landing.quote.store'), [
            'quote_first_name' => 'Evelyn',
            'quote_last_name' => 'Quintana',
            'quote_email' => 'evelyn@example.com',
            'quote_phone_country_code' => '+593',
            'quote_phone_number' => '0995142566',
            'quote_country' => 'Ecuador',
            'quote_professionals_count' => 2,
            'quote_notes' => 'Horario correcto Ecuador.',
            'quote_privacy_accepted' => '1',
        ])->assertSessionHas('quote_status');

        $quote = LandingQuoteRequest::query()->firstOrFail();

        expect((string) $quote->getRawOriginal('created_at'))->toStartWith('2026-03-08 10:42:00');
    } finally {
        Carbon::setTestNow();
    }
});

it('shows stored quote requests in superadmin panel', function () {
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
        ->assertSee('Cliente Lead')
        ->assertSee('lead@example.com')
        ->assertSee('Selecciona una solicitud para ver el detalle.');
});

it('shows quote request detail only after selecting a request', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $quote = LandingQuoteRequest::query()->create([
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
        ->get(route('superadmin.quotations.show', $quote->id))
        ->assertOk()
        ->assertSee('Cliente Lead')
        ->assertSee('Quiere centralizar caja y reportes.')
        ->assertSee('Marcar como revisada');
});

it('does not mark quote requests as reviewed when only opening the detail', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $quote = LandingQuoteRequest::query()->create([
        'first_name' => 'Marco',
        'last_name' => 'Tenelema',
        'email' => 'marco@example.com',
        'phone_country_code' => '+593',
        'phone_number' => '099100200',
        'country' => 'Ecuador',
        'professionals_count' => 2,
        'notes' => 'No debe marcarse sola.',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('superadmin.quotations.show', $quote->id))
        ->assertOk()
        ->assertSee('Marco Tenelema');

    expect($quote->fresh()->read_at)->toBeNull();
});

it('stores legal acceptance evidence with validation', function () {
    $this->from(route('landing.legal.privacy'))
        ->post(route('landing.legal.accept'), [
            'full_name' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'document_key' => 'privacy_policy',
            'accepted' => '1',
        ])
        ->assertRedirect(route('landing.legal.privacy'))
        ->assertSessionHas('status');

    $acceptance = LegalAcceptance::query()->first();
    expect($acceptance)->not->toBeNull()
        ->and($acceptance->document_key)->toBe('privacy_policy')
        ->and($acceptance->email)->toBe('cliente@example.com')
        ->and($acceptance->accepted)->toBeTrue()
        ->and($acceptance->legal_version)->toBe('2026-02-26');
});

it('requires checkbox acceptance for legal registration', function () {
    $this->from(route('landing.legal.service'))
        ->post(route('landing.legal.accept'), [
            'full_name' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'document_key' => 'service_terms',
        ])
        ->assertRedirect(route('landing.legal.service'))
        ->assertSessionHasErrors(['accepted']);

    expect(LegalAcceptance::query()->count())->toBe(0);
});

it('shows demo and guided demo pages for guests', function () {
    $this->get(route('demo'))
        ->assertOk()
        ->assertSee('FlexGym Demo');

    $this->get(route('demo.guide'))
        ->assertOk()
        ->assertSee('Demo guiada');
});

it('redirects gym users from demo routes to their panel context', function () {
    $gym = makeLandingGym('tenant');
    $user = User::factory()->create([
        'gym_id' => $gym->id,
    ]);

    $this->actingAs($user)
        ->get(route('demo'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('demo.guide'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));
});

it('redirects superadmin users from demo routes to dashboard', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('demo'))
        ->assertRedirect(route('superadmin.dashboard'));

    $this->actingAs($superAdmin)
        ->get(route('demo.guide'))
        ->assertRedirect(route('superadmin.dashboard'));
});

it('redirects gym users from landing to their panel context', function () {
    $gym = makeLandingGym('tenant');
    $user = User::factory()->create([
        'gym_id' => $gym->id,
    ]);

    $this->actingAs($user)
        ->get(route('landing'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('landing.about'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('landing.contact'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('landing.legal.privacy'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('landing.legal.service'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));

    $this->actingAs($user)
        ->get(route('landing.legal.commercial'))
        ->assertRedirect(route('panel.index', ['contextGym' => $gym->slug]));
});

it('redirects superadmin users from landing to dashboard', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('landing'))
        ->assertRedirect(route('superadmin.dashboard'));

    $this->actingAs($superAdmin)
        ->get(route('landing.about'))
        ->assertRedirect(route('superadmin.dashboard'));

    $this->actingAs($superAdmin)
        ->get(route('landing.contact'))
        ->assertRedirect(route('superadmin.dashboard'));

    $this->actingAs($superAdmin)
        ->get(route('landing.legal.privacy'))
        ->assertRedirect(route('superadmin.dashboard'));

    $this->actingAs($superAdmin)
        ->get(route('landing.legal.service'))
        ->assertRedirect(route('superadmin.dashboard'));

    $this->actingAs($superAdmin)
        ->get(route('landing.legal.commercial'))
        ->assertRedirect(route('superadmin.dashboard'));
});

it('allows superadmin to edit web page content and persists settings', function () {
    $superAdmin = User::factory()->create([
        'gym_id' => null,
    ]);

    $payload = marketingPayload();

    $this->actingAs($superAdmin)
        ->get(route('superadmin.web-page.edit'))
        ->assertOk()
        ->assertSee('Contenido comercial');

    $this->actingAs($superAdmin)
        ->post(route('superadmin.web-page.update'), $payload)
        ->assertRedirect(route('superadmin.web-page.edit'))
        ->assertSessionHas('status');

    expect(SiteSetting::query()->where('key', 'marketing.hero_title')->value('value'))
        ->toBe('Titulo comercial actualizado');

    $this->get(route('landing'))
        ->assertOk()
        ->assertSee('Titulo comercial actualizado');
});

it('redirects guests trying to access web page admin', function () {
    $this->get(route('superadmin.web-page.edit'))
        ->assertRedirect(route('login'));
});

it('creates a temporary demo account and redirects to panel', function () {
    $response = $this->post(route('demo.request'));

    $session = DemoSession::query()->first();

    expect($session)->not->toBeNull();

    $response->assertRedirect(route('panel.index', ['contextGym' => $session->gym->slug]));

    $this->assertAuthenticatedAs($session->user);
});

it('loads panel successfully after requesting a demo session', function () {
    $response = $this->post(route('demo.request'));

    $session = DemoSession::query()->first();
    expect($session)->not->toBeNull();

    $response->assertRedirect(route('panel.index', ['contextGym' => $session->gym->slug]));

    $this->actingAs($session->user)
        ->get(route('panel.index', ['contextGym' => $session->gym->slug]))
        ->assertOk();
});

it('ends demo session and deletes demo data immediately', function () {
    $this->post(route('demo.request'));

    $session = DemoSession::query()->firstOrFail();
    $demoSessionId = (int) $session->id;
    $demoGymId = (int) $session->gym_id;
    $demoUserId = (int) $session->user_id;

    $this->actingAs($session->user)
        ->post(route('demo.end'))
        ->assertRedirect(route('landing'));

    $this->assertGuest();
    $this->assertDatabaseMissing('demo_sessions', ['id' => $demoSessionId]);
    $this->assertDatabaseMissing('gyms', ['id' => $demoGymId]);
    $this->assertDatabaseMissing('users', ['id' => $demoUserId]);
});
