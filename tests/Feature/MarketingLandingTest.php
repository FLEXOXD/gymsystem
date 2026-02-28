<?php

use App\Http\Controllers\MarketingController;
use App\Models\DemoSession;
use App\Models\Gym;
use App\Models\LegalAcceptance;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
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

it('renders whatsapp urls per pricing plan using superadmin-configured messages', function () {
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_phone',
        'value' => '593111222333',
    ]);
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_message_plan_basico',
        'value' => 'msg_plan_basico',
    ]);
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_message_plan_profesional',
        'value' => 'msg_plan_profesional',
    ]);
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_message_plan_premium',
        'value' => 'msg_plan_premium',
    ]);
    SiteSetting::query()->create([
        'key' => 'marketing.whatsapp_message_plan_sucursales',
        'value' => 'msg_plan_sucursales',
    ]);

    $this->get(route('landing'))
        ->assertOk()
        ->assertSee('data-plan-cta-key="basico"', false)
        ->assertSee('data-plan-cta-key="profesional"', false)
        ->assertSee('data-plan-cta-key="premium"', false)
        ->assertSee('data-plan-cta-key="sucursales"', false)
        ->assertSee('https://api.whatsapp.com/send?phone=593111222333&amp;text=msg_plan_basico', false)
        ->assertSee('https://api.whatsapp.com/send?phone=593111222333&amp;text=msg_plan_profesional', false)
        ->assertSee('https://api.whatsapp.com/send?phone=593111222333&amp;text=msg_plan_premium', false)
        ->assertSee('https://api.whatsapp.com/send?phone=593111222333&amp;text=msg_plan_sucursales', false);
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
        ->assertSee('GymSystem Demo');

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
