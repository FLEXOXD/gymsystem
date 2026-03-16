@php
    $content = $content ?? \App\Support\MarketingContent::load();
    $demoCtaLabel = 'Demo gratis';
    $loginLabel = trim((string) ($content['login_button_label'] ?? 'Iniciar sesión'));
    $brandKicker = trim((string) ($content['brand_kicker'] ?? 'Sistema Operativo'));
    $brandName = trim((string) ($content['brand_name'] ?? 'GymSystem'));
    $brandLogoUrl = trim((string) ($content['brand_logo_url'] ?? ''));
    $brandInitials = trim((string) ($content['brand_initials'] ?? 'GS'));
    $footerText = trim((string) ($content['footer_text_resolved'] ?? ('GymSystem '.now()->year.' | Control operativo para gimnasios')));
    $footerContactEmail = trim((string) ($content['footer_contact_email'] ?? 'soporte@gymsystem.local'));
    $footerContactEmail = $footerContactEmail !== '' ? $footerContactEmail : 'soporte@gymsystem.local';
    $demoLimitModal = session('demo_limit_modal');
    $demoLimitSeconds = max(0, (int) data_get($demoLimitModal, 'retry_after_seconds', 0));
    $heroTitleText = trim((string) ($content['hero_title'] ?? 'Administra tu gimnasio en un solo sistema, rápido'));
    $heroTitleText = trim((string) preg_replace('/\bsin\s+fricción\b\.?/i', '', $heroTitleText));
    $heroSlides = [];
    for ($slide = 1; $slide <= 4; $slide++) {
        $slideUrl = trim((string) ($content['hero_slide_'.$slide.'_url'] ?? ''));
        if ($slideUrl !== '') {
            $heroSlides[] = $slideUrl;
        }
    }
    if (count($heroSlides) === 0) {
        foreach (['section_1_image_url', 'section_2_image_url', 'section_3_image_url'] as $fallbackImageKey) {
            $fallbackUrl = trim((string) ($content[$fallbackImageKey] ?? ''));
            if ($fallbackUrl !== '') {
                $heroSlides[] = $fallbackUrl;
            }
        }
    }
    $homePageBackgroundFallbacks = [
        'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=2400&q=80',
        'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=2400&q=80',
        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=2400&q=80',
        'https://images.unsplash.com/photo-1576678927484-cc907957088c?auto=format&fit=crop&w=2400&q=80',
        'https://images.unsplash.com/photo-1517344368193-41552b6ad3f5?auto=format&fit=crop&w=2400&q=80',
    ];
    $homePageBackgroundUrls = [];
    foreach ($homePageBackgroundFallbacks as $index => $fallbackUrl) {
        $configuredUrl = trim((string) ($content['home_bg_page_'.($index + 1).'_url'] ?? ''));
        $homePageBackgroundUrls[] = $configuredUrl !== '' ? $configuredUrl : $fallbackUrl;
    }
    $aboutInternetImages = [
        'hero' => 'https://images.unsplash.com/photo-1647456753452-e5d7cbf16df1?auto=format&fit=crop&w=2200&q=80',
        'story' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=1800&q=80',
        'team_1' => 'https://images.unsplash.com/photo-1683147779485-24912f480130?auto=format&fit=crop&w=1400&q=80',
        'team_2' => 'https://images.unsplash.com/photo-1737608749249-a39c697ae34a?auto=format&fit=crop&w=1400&q=80',
        'team_3' => 'https://images.unsplash.com/photo-1534367899781-0d696bebc1d2?auto=format&fit=crop&w=1400&q=80',
    ];
    $aboutHeroImage = $aboutInternetImages['hero'];
    $aboutStoryImage = $aboutInternetImages['story'];
    $aboutTeamImage1 = $aboutInternetImages['team_1'];
    $aboutTeamImage2 = $aboutInternetImages['team_2'];
    $aboutTeamImage3 = $aboutInternetImages['team_3'];
    $marqueeItems = [];
    for ($i = 1; $i <= 6; $i++) {
        $text = trim((string) ($content['marquee_item_'.$i.'_text'] ?? ''));
        $logo = trim((string) ($content['marquee_item_'.$i.'_logo_url'] ?? ''));
        $marqueeItems[] = [
            'index' => $i,
            'text' => $text !== '' ? $text : 'Item '.$i,
            'logo' => $logo,
        ];
    }
    $legalVersion = trim((string) ($legalVersion ?? '2026-02-26'));
    $legalVersion = $legalVersion !== '' ? $legalVersion : '2026-02-26';
    $pageMode = in_array(($pageMode ?? 'home'), ['home', 'about', 'contact', 'privacy', 'service', 'commercial'], true) ? $pageMode : 'home';
    $showPrimarySections = $pageMode === 'home';
    $showAboutSection = $pageMode === 'about';
    $showContactSection = $pageMode === 'contact';
    $showLegalSection = in_array($pageMode, ['privacy', 'service', 'commercial'], true);

    $homeBaseUrl = route('landing');
    $inicioHref = $showPrimarySections ? '#inicio' : $homeBaseUrl.'#inicio';
    $featuresHref = $showPrimarySections ? '#features' : $homeBaseUrl.'#features';
    $pricingHref = $showPrimarySections ? '#pricing' : $homeBaseUrl.'#pricing';
    $faqHref = $showPrimarySections ? '#faq' : $homeBaseUrl.'#faq';
    $aboutHref = route('landing.about');
    $contactHref = route('landing.contact');
    $privacyHref = route('landing.legal.privacy');
    $serviceTermsHref = route('landing.legal.service');
    $commercialTermsHref = route('landing.legal.commercial');

    $pageTitleSuffix = match ($pageMode) {
        'about' => 'Sobre nosotros',
        'contact' => 'Contáctanos',
        'privacy' => 'Política de privacidad',
        'service' => 'Condiciones de servicio',
        'commercial' => 'Términos comerciales',
        default => 'Software para gimnasios',
    };
    $tabIconUrl = asset('pwa/favicon-brand.png?v=20260302');
@endphp
@php
    $serviceCards = [
        [
            'index' => '1',
            'title' => trim((string) ($content['section_1_title'] ?? 'Operación diaria clara para recepción y administración')),
            'text' => trim((string) ($content['section_1_text'] ?? 'Todo lo que tu equipo necesita en una sola vista para cobrar y registrar ingresos de forma rápida.')),
            'items' => [
                trim((string) ($content['section_1_item_1'] ?? 'Ingreso y check-in con QR, RFID, pulseras y llaveros')),
                trim((string) ($content['section_1_item_2'] ?? 'Cobros de membresía con control por método de pago')),
                trim((string) ($content['section_1_item_3'] ?? 'Reportes de ingresos y asistencia en tiempo real')),
            ],
        ],
        [
            'index' => '2',
            'title' => trim((string) ($content['section_2_title'] ?? 'Más control, menos caos operativo')),
            'text' => trim((string) ($content['section_2_text'] ?? 'Convierte tu operación diaria en procesos claros, medibles y fáciles de ejecutar.')),
            'items' => [
                trim((string) ($content['section_2_item_1'] ?? 'Flujo de recepción optimizado')),
                trim((string) ($content['section_2_item_2'] ?? 'Caja por turnos con control real')),
                trim((string) ($content['section_2_item_3'] ?? 'Panel simple para ver ingresos, vencimientos y clientes activos')),
            ],
        ],
        [
            'index' => '3',
            'title' => trim((string) ($content['section_3_title'] ?? 'Experiencia móvil tipo app (PWA)')),
            'text' => trim((string) ($content['section_3_text'] ?? 'Instalable en celular y escritorio, lista para operar.')),
            'items' => [
                trim((string) ($content['section_3_item_1'] ?? 'Pantalla de inicio tipo aplicación')),
                trim((string) ($content['section_3_item_2'] ?? 'Caché de assets para carga rápida')),
                trim((string) ($content['section_3_item_3'] ?? 'Base lista para notificaciones y recordatorios')),
            ],
        ],
    ];

    $faqItems = [
        ['q' => '¿Qué incluye la demo gratuita?', 'a' => 'La demo crea un gimnasio temporal real para probar recepción, clientes, membresías, caja y reportes sin tocar datos de producción.'],
        ['q' => '¿La demo afecta mi sistema actual?', 'a' => 'No. La demo trabaja en un entorno temporal aislado y los registros se eliminan automáticamente al expirar la sesión.'],
        ['q' => '¿Puedo usar el sistema desde celular?', 'a' => 'Sí. La interfaz es responsive y cuenta con base PWA para operación diaria en móvil y escritorio.'],
        ['q' => '¿Cómo solicitar acompañamiento comercial?', 'a' => 'Puedes escribir por WhatsApp desde esta página y te ayudamos a definir la mejor implementación para tu gimnasio.'],
    ];

    $heroProofItems = [
        ['title' => 'Cobro diario ordenado', 'text' => 'Caja por turnos, membresías y movimientos en una sola operación.'],
        ['title' => 'Recepción más rápida', 'text' => 'Check-in con flujo claro para escritorio, tablet y móvil.'],
        ['title' => 'Listo para crecer', 'text' => 'Desde una sede hasta operación multi-gym sin cambiar de sistema.'],
    ];

    $outcomeCards = [
        [
            'eyebrow' => 'Recepción',
            'title' => 'Tu recepción trabaja más rápido',
            'text' => 'Check-in, membresías y cobros quedan a mano para atender mejor y perder menos tiempo.',
            'metric' => 'Menos filas',
        ],
        [
            'eyebrow' => 'Control',
            'title' => 'Ves lo importante en un solo lugar',
            'text' => 'Pagos, vencimientos, ingresos y clientes activos se revisan rápido sin buscar en varias pantallas.',
            'metric' => 'Control diario',
        ],
        [
            'eyebrow' => 'Crecimiento',
            'title' => 'Creces sin volver al desorden',
            'text' => 'Empiezas con una sede y, si abres más, sigues trabajando sobre el mismo sistema.',
            'metric' => 'Listo para crecer',
        ],
    ];

    $journeySteps = [
        [
            'step' => '01',
            'title' => 'Configuras tu gimnasio y das accesos',
            'text' => 'Dejas lista la sede, el plan y el usuario principal para empezar a trabajar sin enredos.',
        ],
        [
            'step' => '02',
            'title' => 'Tu equipo cobra y atiende mejor',
            'text' => 'Recepción, membresías y caja quedan conectadas para cometer menos errores y trabajar más rápido.',
        ],
        [
            'step' => '03',
            'title' => 'Sabes como va el gimnasio cada día',
            'text' => 'Ves vencimientos, ingresos y movimiento diario para tomar decisiones con más claridad.',
        ],
    ];

    $publicPlanCards = collect($publicPlanCards ?? [])->filter(fn ($card) => is_array($card))->values();
    $formatPlanMoney = static function (mixed $value): string {
        $amount = (float) $value;
        return number_format($amount, 0, '.', '');
    };
    $quoteCountryPrefixes = [
        'Ecuador' => '+593',
        'Colombia' => '+57',
        'Peru' => '+51',
        'Chile' => '+56',
        'Mexico' => '+52',
        'Argentina' => '+54',
        'Bolivia' => '+591',
        'Panama' => '+507',
        'Costa Rica' => '+506',
        'Otro' => null,
    ];
    $quoteCountries = array_keys($quoteCountryPrefixes);
    $quotePhonePrefixes = array_values(array_unique(array_filter(array_values($quoteCountryPrefixes))));
    $landingQuoteErrors = $errors->getBag('landingQuote');
    $quoteModalMessage = '';
    $quoteModalType = 'idle';
    if (session('quote_status')) {
        $quoteModalMessage = (string) session('quote_status');
        $quoteModalType = 'success';
    } elseif ($landingQuoteErrors->any()) {
        $quoteModalMessage = (string) $landingQuoteErrors->first();
        $quoteModalType = 'error';
    }
    $quoteModalOpen = $quoteModalType !== 'idle' || request()->boolean('quote');
    $quoteSelectedPlan = trim((string) old('quote_requested_plan', ''));
    $quoteSelectedPlanLabel = $quoteSelectedPlan !== ''
        ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $quoteSelectedPlan))
        : '';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#05080f">
    <title>{{ $brandName }} | {{ $pageTitleSuffix }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $tabIconUrl }}">
    <link rel="shortcut icon" href="{{ $tabIconUrl }}">
    <link rel="apple-touch-icon" href="{{ $tabIconUrl }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #030405;
            --bg-soft: #0a0c10;
            --card: #0f1419;
            --card-2: #131a22;
            --text: #f6fbff;
            --muted: #adc3b5;
            --neon: #47ff6f;
            --neon-soft: rgba(71, 255, 111, 0.32);
            --border: #254235;
            --home-page-bg-1: url('{{ $homePageBackgroundUrls[0] }}');
            --home-page-bg-2: url('{{ $homePageBackgroundUrls[1] }}');
            --home-page-bg-3: url('{{ $homePageBackgroundUrls[2] }}');
            --home-page-bg-4: url('{{ $homePageBackgroundUrls[3] }}');
            --home-page-bg-5: url('{{ $homePageBackgroundUrls[4] }}');
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        html { scroll-padding-top: 7.6rem; }
        body {
            font-family: "Space Grotesk", "Segoe UI", system-ui, sans-serif;
            color: var(--text);
            background: linear-gradient(180deg, #020905 0%, #03110a 55%, #020a06 100%);
            overflow-x: hidden;
            position: relative;
            isolation: isolate;
        }
        body.is-home {
            background:
                linear-gradient(180deg, rgba(2, 9, 5, .55) 0%, rgba(3, 17, 10, .45) 55%, rgba(2, 10, 6, .65) 100%);
        }
        .home-scroll-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .home-scroll-bg-layer {
            position: absolute;
            inset: 0;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            transform: scale(1.04);
            opacity: 0;
            transition: opacity .7s ease;
            will-change: opacity;
        }
        .home-scroll-bg-layer.is-a {
            background-image: var(--home-page-bg-1);
        }
        .home-scroll-bg-layer.is-active {
            opacity: 1;
        }
        .home-scroll-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(116deg, rgba(2, 11, 7, .86) 0%, rgba(2, 11, 7, .67) 44%, rgba(2, 11, 7, .82) 100%),
                radial-gradient(circle at 72% 18%, rgba(38, 255, 131, .12), transparent 36%);
        }
        .shell { width: min(1240px, calc(100% - 2rem)); margin: 0 auto; }
        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: -10%;
            pointer-events: none;
            z-index: 1;
        }
        body::before {
            background:
                radial-gradient(42% 34% at 22% 18%, rgba(57, 255, 141, .24), transparent 72%),
                radial-gradient(36% 30% at 78% 72%, rgba(29, 214, 116, .18), transparent 74%);
            filter: blur(14px);
            will-change: transform, opacity;
            animation: neonBreath 10s ease-in-out infinite alternate;
        }
        body::after {
            background:
                radial-gradient(30% 26% at 50% 40%, rgba(90, 255, 170, .12), transparent 76%);
            filter: blur(24px);
            will-change: transform, opacity;
            animation: neonDrift 16s ease-in-out infinite alternate;
        }
        @keyframes neonBreath {
            0% { opacity: .55; transform: scale(1) translate3d(0, 0, 0); }
            100% { opacity: .9; transform: scale(1.04) translate3d(0, -8px, 0); }
        }
        @keyframes neonDrift {
            0% { opacity: .35; transform: translate3d(-10px, 0, 0); }
            100% { opacity: .6; transform: translate3d(12px, -6px, 0); }
        }

        .top-wrap {
            position: sticky; top: 0; z-index: 40;
            padding-top: 0.55rem;
            backdrop-filter: blur(6px);
            background: linear-gradient(180deg, rgba(2, 11, 7, 0.92), rgba(2, 11, 7, 0.52));
        }
        .top-nav {
            display: flex; align-items: center; gap: 1rem;
            border: 1px solid #294437;
            border-radius: 1.1rem;
            background: rgba(8, 14, 10, 0.92);
            box-shadow: 0 16px 40px rgba(4, 18, 10, 0.5);
            padding: 0.75rem 1rem;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            width: 170px;
            min-width: 170px;
            overflow: visible;
        }
        .brand-logo {
            width: 165px;
            height: 52px;
            object-fit: contain;
            border: 0; border-radius: 0; background: transparent;
            filter: drop-shadow(0 0 13px rgba(60, 255, 60, 0.22));
            transform: scale(2.05);
            transform-origin: left center;
        }
        .brand-fallback {
            width: 3.1rem; height: 3.1rem; border-radius: .9rem;
            background: linear-gradient(145deg, #194126, #0c2413);
            border: 1px solid #2f7044;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .82rem; font-weight: 800; letter-spacing: .08em;
        }
        .menu-links {
            display: inline-flex; align-items: center; gap: .42rem;
            border: 1px solid #294237; border-radius: 999px;
            background: rgba(8, 14, 10, .86);
            padding: .25rem; margin-inline: auto;
        }
        .menu-links a {
            color: #d2e7d8; text-decoration: none; font-size: .84rem; font-weight: 700;
            padding: .45rem .95rem; border-radius: 999px; transition: .2s ease;
        }
        .menu-links a:hover { color: #fff; background: rgba(60, 255, 60, .12); }
        .menu-links a.is-active {
            color: #f8fffb;
            background: linear-gradient(140deg, rgba(68, 255, 125, 0.28), rgba(34, 180, 88, 0.22));
            border: 1px solid rgba(79, 255, 137, 0.55);
            box-shadow: 0 0 0 1px rgba(79, 255, 137, 0.2), 0 10px 20px rgba(4, 18, 10, 0.35);
        }

        .nav-actions { display: inline-flex; align-items: center; gap: .55rem; }
        .mobile-menu-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border: 1px solid #2f5a41;
            border-radius: .78rem;
            background: rgba(10, 18, 13, .92);
            color: #e8fff0;
            padding: 0;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 4px;
            cursor: pointer;
        }
        .mobile-menu-toggle span {
            display: block;
            width: 18px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            transition: transform .22s ease, opacity .2s ease;
        }
        .mobile-menu-toggle span + span { margin-top: 0; }
        .mobile-menu-toggle[aria-expanded="true"] span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
        .mobile-menu-toggle[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
        .mobile-menu-toggle[aria-expanded="true"] span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }
        .mobile-nav-panel {
            display: none;
            margin-top: .55rem;
            border: 1px solid #294437;
            border-radius: 1rem;
            background: rgba(8, 14, 10, .96);
            box-shadow: 0 16px 36px rgba(4, 18, 10, 0.45);
            padding: .78rem;
        }
        .mobile-nav-panel.is-open { display: block; }
        .mobile-nav-links {
            display: grid;
            gap: .42rem;
        }
        .mobile-nav-links a {
            text-decoration: none;
            color: #d6ebdc;
            font-size: .96rem;
            font-weight: 700;
            border: 1px solid #2b5a40;
            border-radius: .72rem;
            padding: .62rem .78rem;
            background: rgba(10, 18, 13, .74);
        }
        .mobile-nav-links a.is-active {
            color: #f8fffb;
            border-color: rgba(79, 255, 137, 0.62);
            background: linear-gradient(140deg, rgba(68, 255, 125, 0.24), rgba(34, 180, 88, 0.16));
            box-shadow: 0 0 0 1px rgba(79, 255, 137, 0.22);
        }
        .mobile-nav-actions {
            margin-top: .7rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .55rem;
        }
        .mobile-nav-actions .inline-form,
        .mobile-nav-actions .btn { width: 100%; }
        .inline-form { margin: 0; display: inline-flex; }
        .btn {
            border: 0; border-radius: .8rem; min-height: 44px;
            padding: .6rem 1.05rem; font-size: .86rem; font-weight: 800;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
            cursor: pointer; transition: transform .2s ease, box-shadow .2s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-outline { color: #effff3; border: 1px solid #2e5940; background: rgba(11,20,14,.9); }
        .btn-demo { color: #061209; background: linear-gradient(140deg, #57ff54, #20dc78); box-shadow: 0 16px 34px var(--neon-soft); }
        .btn-wa { color: #f4f9ff; border: 1px solid #2f4f3f; background: rgba(13,20,16,.86); }
        .btn-icon { width: 16px; height: 16px; flex: 0 0 16px; }
        .btn-quote-trigger .btn-icon {
            width: 18px;
            height: 18px;
            flex-basis: 18px;
        }
        .quote-cta-icon-badge {
            position: relative;
            width: 1.9rem;
            height: 1.9rem;
            border-radius: .62rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 1.9rem;
            background: linear-gradient(145deg, rgba(72, 255, 143, .2), rgba(18, 135, 83, .12));
            border: 1px solid rgba(90, 255, 155, .36);
            box-shadow: 0 10px 24px rgba(8, 31, 18, .2), inset 0 1px 0 rgba(255, 255, 255, .08);
            color: inherit;
        }
        .quote-cta-icon-badge::before {
            content: "";
            position: absolute;
            inset: 3px;
            border-radius: .48rem;
            background: linear-gradient(145deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, 0));
            pointer-events: none;
        }
        .quote-cta-icon-badge::after {
            content: "";
            position: absolute;
            top: -3px;
            right: -3px;
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
            background: radial-gradient(circle, #d8ff72 0%, #63ff6d 58%, #1ea14c 100%);
            box-shadow: 0 0 10px rgba(99, 255, 109, .6);
        }
        .btn-demo .quote-cta-icon-badge {
            background: linear-gradient(145deg, rgba(6, 18, 9, .12), rgba(6, 18, 9, .04));
            border-color: rgba(6, 18, 9, .16);
            box-shadow: 0 10px 24px rgba(14, 81, 36, .18), inset 0 1px 0 rgba(255, 255, 255, .28);
        }
        .btn-outline .quote-cta-icon-badge,
        .btn-wa .quote-cta-icon-badge {
            background: linear-gradient(145deg, rgba(71, 255, 111, .16), rgba(25, 46, 32, .28));
        }

        .main { padding-bottom: 4rem; position: relative; z-index: 2; }
        .flash-stack { margin-top: 1rem; display: grid; gap: .6rem; }
        .flash {
            border: 1px solid #2e5940; border-radius: .82rem;
            background: rgba(12, 20, 15, .9); color: #e8fff0;
            padding: .72rem .9rem; font-size: .9rem; line-height: 1.45;
        }
        .flash-error { border-color: #5e3a40; background: rgba(43, 20, 26, .75); color: #ffd9df; }
        .hero {
            display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr);
            gap: clamp(1.25rem, 2.4vw, 2.4rem); align-items: center; padding-top: 2.8rem;
            position: relative;
            overflow: visible;
            isolation: isolate;
        }
        .hero > * {
            position: relative;
            z-index: 2;
        }
        .hero-fx {
            position: absolute;
            inset: -34% -26% -24% -26%;
            pointer-events: none;
            z-index: 1;
            opacity: .48;
            will-change: transform, opacity;
        }
        .hero-fx::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 74% 20%, rgba(71,255,111,.14) 0, rgba(71,255,111,0) 34%),
                radial-gradient(circle at 90% 58%, rgba(71,255,111,.09) 0, rgba(71,255,111,0) 40%);
            animation: heroGlowDrift 12s ease-in-out infinite alternate;
        }
        .hero-fx-line {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, rgba(76,255,140,0), rgba(76,255,140,.25), rgba(76,255,140,0));
            opacity: .34;
            transform-origin: left center;
        }
        .hero-fx-line.l1 { width: 220px; left: 60%; top: 18%; transform: rotate(18deg); animation: heroLinePulse 5.6s ease-in-out infinite; }
        .hero-fx-line.l2 { width: 180px; left: 74%; top: 36%; transform: rotate(-22deg); animation: heroLinePulse 4.8s ease-in-out infinite 1.1s; }
        .hero-fx-line.l3 { width: 150px; left: 52%; top: 54%; transform: rotate(12deg); animation: heroLinePulse 6.2s ease-in-out infinite .6s; }
        .hero-fx-node {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: rgba(109, 255, 162, .52);
            box-shadow: 0 0 10px rgba(109, 255, 162, .34);
            animation: heroNodeFloat 4.8s ease-in-out infinite;
        }
        .hero-fx-node.n1 { left: 58%; top: 15%; animation-delay: .2s; }
        .hero-fx-node.n2 { left: 71%; top: 28%; animation-delay: 1.1s; }
        .hero-fx-node.n3 { left: 64%; top: 46%; animation-delay: .7s; }
        .hero-fx-node.n4 { left: 82%; top: 38%; animation-delay: 1.7s; }
        .hero-fx-node.n5 { left: 76%; top: 58%; animation-delay: .4s; }
        @keyframes heroGlowDrift {
            0% { transform: translateY(0); opacity: .85; }
            100% { transform: translateY(-10px); opacity: 1; }
        }
        @keyframes heroLinePulse {
            0%, 100% { opacity: .26; }
            50% { opacity: .65; }
        }
        @keyframes heroNodeFloat {
            0%, 100% { transform: translateY(0); opacity: .55; }
            50% { transform: translateY(-7px); opacity: 1; }
        }
        .kicker {
            margin: 0; display: inline-flex; align-items: center; gap: .45rem;
            color: #9ff7b2; text-transform: uppercase; font-size: .74rem; font-weight: 800; letter-spacing: .14em;
        }
        .kicker::before {
            content: ""; width: .5rem; height: .5rem; border-radius: 999px;
            background: #3cff3c; box-shadow: 0 0 14px rgba(60,255,60,.7);
        }
        .hero h1 {
            margin: .95rem 0 0; font-size: clamp(2.3rem, 4.2vw, 4.35rem);
            line-height: 1.03; letter-spacing: -.03em; max-width: 16ch;
        }
        .hero h1 .neon { color: #8fff8f; text-shadow: 0 0 34px rgba(71,255,111,.42); }
        .hero p { margin: 1.05rem 0 0; max-width: 62ch; color: var(--muted); font-size: 1.04rem; line-height: 1.58; }
        .hero-actions { margin-top: 1.25rem; display: flex; flex-wrap: wrap; gap: .6rem; }
        .hero-note { margin-top: .8rem; font-size: .87rem; color: #b4cfbf; line-height: 1.45; max-width: 64ch; }
        .hero-note b { color: #f1f7ff; }
        .hero-proof-strip {
            margin-top: 1.15rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .7rem;
            max-width: 70ch;
        }
        .hero-proof-card {
            border: 1px solid rgba(61, 110, 78, .7);
            border-radius: 1rem;
            background: linear-gradient(145deg, rgba(10, 19, 13, .96), rgba(8, 18, 12, .88));
            padding: .85rem .9rem;
            box-shadow: 0 18px 32px rgba(4, 18, 10, .22);
        }
        .hero-proof-title {
            margin: 0;
            color: #f6fff9;
            font-size: .9rem;
            font-weight: 800;
            line-height: 1.25;
        }
        .hero-proof-copy {
            margin: .45rem 0 0;
            color: #b5cabd;
            font-size: .82rem;
            line-height: 1.45;
        }

        .hero-panel {
            border-radius: 1.2rem; border: 0;
            background: transparent;
            padding: 0; box-shadow: none; overflow: visible;
            display: grid; gap: .72rem;
        }
        .hero-media-frame {
            position: relative;
            border: 0;
            border-radius: 1rem;
            min-height: clamp(360px, 38vw, 500px);
            background: #050b08;
            overflow: hidden;
            box-shadow: 0 24px 52px rgba(3, 8, 22, .44);
        }
        .hero-carousel {
            position: absolute;
            inset: 0;
        }
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .62s ease;
        }
        .hero-slide.is-active {
            opacity: 1;
        }
        .hero-slide-image {
            width: 100%;
            height: 100%;
            min-height: clamp(360px, 38vw, 500px);
            display: block;
            object-fit: cover;
            filter: saturate(1.1) contrast(1.05);
        }
        .hero-media-placeholder {
            min-height: clamp(360px, 38vw, 500px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
            color: #b7d2c2;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .hero-carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: 1px solid rgba(113, 255, 165, .45);
            background: rgba(8, 18, 12, .72);
            color: #d8ffe5;
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 4;
        }
        .hero-carousel-control.prev { left: .68rem; }
        .hero-carousel-control.next { right: .68rem; }
        .hero-carousel-dots {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: .58rem;
            display: inline-flex;
            align-items: center;
            gap: .44rem;
            z-index: 4;
        }
        .hero-carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            border: 0;
            background: rgba(196, 236, 214, .42);
            cursor: pointer;
            padding: 0;
        }
        .hero-carousel-dot.is-active { background: #58ff8a; box-shadow: 0 0 12px rgba(88,255,138,.65); }
        .hero-insight-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .62rem;
            margin-top: .18rem;
        }
        .hero-insight-card {
            border: 0;
            border-radius: .88rem;
            background: linear-gradient(145deg, rgba(10, 22, 14, .96), rgba(8, 18, 12, .92));
            padding: .7rem .78rem;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(89, 255, 142, .35);
        }
        .hero-insight-card::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 2px;
            background: linear-gradient(90deg, rgba(74, 255, 117, .15), rgba(74, 255, 117, .72), rgba(74, 255, 117, .15));
        }
        .hero-insight-label {
            display: block;
            color: #b7cfbe;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .hero-insight-value {
            margin-top: .25rem;
            display: block;
            color: #ecfff2;
            font-size: 1.64rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -.02em;
        }

        .brands-band {
            margin-top: 1.25rem;
        }
        .brands-marquee {
            border: 1px solid #234935;
            border-top: 1px solid #234935;
            border-bottom: 1px solid #234935;
            background: linear-gradient(90deg, rgba(4, 10, 7, .96), rgba(8, 18, 12, .96) 50%, rgba(4, 10, 7, .96));
            border-radius: .9rem;
            padding: .78rem .4rem;
            overflow: hidden;
            position: relative;
        }
        .brands-track {
            display: flex;
            width: max-content;
            animation: brands-marquee-scroll 44s linear infinite;
            will-change: transform;
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
        }
        .brands-row {
            display: flex;
            align-items: center;
            gap: 2.35rem;
            padding-right: 2.35rem;
        }
        .brand-chip {
            min-width: 0;
            width: auto;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            gap: .52rem;
            padding: 0;
            overflow: hidden;
            line-height: 1;
        }
        .brand-chip.brand-chip-has-logo {
            min-width: 0;
            justify-content: flex-start;
            gap: 2.1rem;
            padding-inline: 0;
        }
        .brand-chip-logo-box {
            width: 96px;
            height: 32px;
            flex: 0 0 96px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .brand-chip-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            background: transparent;
            flex: 0 0 auto;
            filter: drop-shadow(0 0 12px rgba(68, 255, 125, 0.28));
            transform: scale(2.8);
            transform-origin: center;
            will-change: transform;
        }
        .brand-chip span {
            color: #c0d2c8;
            font-size: .98rem;
            font-weight: 700;
            letter-spacing: .01em;
            opacity: .88;
            white-space: nowrap;
        }
        .brand-chip:hover span { color: #dbffe6; opacity: 1; }
        @keyframes brands-marquee-scroll {
            from { transform: translate3d(0, 0, 0); }
            to { transform: translate3d(-50%, 0, 0); }
        }

        .section {
            margin-top: 3rem;
            padding-top: 2.2rem;
            border-top: 1px solid #2b5a40;
        }
        main section[id] { scroll-margin-top: 7.6rem; }
        .heading { text-align: center; margin-bottom: 1.35rem; }
        .heading small {
            display: inline-block; color: #9cf7b1;
            text-transform: uppercase; letter-spacing: .11em; font-weight: 800; margin-bottom: .55rem;
        }
        .heading h2 { margin: 0; font-size: clamp(1.6rem, 2.8vw, 3rem); letter-spacing: -.02em; text-shadow: 0 0 22px rgba(71,255,111,.15); }
        .heading p { margin: .8rem auto 0; max-width: 64ch; color: #b4c6bc; line-height: 1.6; }

        .service-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .service {
            border: 1px solid #2d5d42; border-radius: 1.05rem;
            background: linear-gradient(145deg, #0b150f, #102017);
            padding: 1rem 1rem 1.2rem; min-height: 220px;
            display: flex;
            flex-direction: column;
            transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease;
        }
        .service:hover {
            transform: translateY(-4px);
            border-color: #49de73;
            box-shadow: 0 20px 34px rgba(6, 20, 11, .48);
        }
        .service i {
            width: 2.45rem; height: 2.45rem; border-radius: .7rem;
            display: inline-flex; align-items: center; justify-content: center;
            font-style: normal; font-weight: 900; color: #0d1f14;
            background: linear-gradient(145deg, #3cff3c, #22dc76);
            box-shadow: 0 8px 18px rgba(60,255,60,.24);
        }
        .service h3 { margin: .85rem 0 0; font-size: 1.42rem; line-height: 1.13; }
        .service p { margin: .65rem 0 0; color: #b4c6bc; line-height: 1.55; }
        .service ul { margin: .7rem 0 1rem; padding: 0; list-style: none; display: grid; gap: .42rem; color: #e0efe5; font-size: .92rem; }
        .service li { display: flex; align-items: flex-start; gap: .42rem; }
        .service li::before {
            content: "";
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
            background: #47ff6f;
            box-shadow: 0 0 12px rgba(71, 255, 111, .9), 0 0 22px rgba(71, 255, 111, .5);
            margin-top: .34rem;
            flex: 0 0 .5rem;
        }
        .service-media {
            margin-top: auto; border: 1px solid #2f5d43; border-radius: .75rem;
            background: rgba(8, 14, 10, .94); min-height: 210px; aspect-ratio: 16 / 10; overflow: hidden;
            display: flex; align-items: flex-start; justify-content: center; color: #b4c9bd;
            font-weight: 700; font-size: .88rem; padding: .45rem;
        }
        .service-media img {
            width: 100%;
            height: 100%;
            min-height: 0;
            object-fit: cover;
            object-position: top center;
            border-radius: .58rem;
            background: rgba(6, 11, 8, .98);
        }

        .outcome-band {
            margin-top: 1.5rem;
        }
        .outcome-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        .outcome-card {
            border: 1px solid #2c5b41;
            border-radius: 1rem;
            background: linear-gradient(145deg, rgba(9, 18, 12, .94), rgba(12, 25, 17, .96));
            padding: 1rem;
        }
        .outcome-card small {
            display: inline-block;
            color: #9cf7b1;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .outcome-card h3 {
            margin: .65rem 0 0;
            font-size: 1.2rem;
            line-height: 1.18;
        }
        .outcome-card p {
            margin: .7rem 0 0;
            color: #b6cabf;
            line-height: 1.58;
        }
        .outcome-metric {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(84, 255, 144, .36);
            background: rgba(76, 255, 140, .1);
            color: #dcffe7;
            padding: .28rem .72rem;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .workflow-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        .workflow-card {
            border: 1px solid #2f5d43;
            border-radius: 1rem;
            background: linear-gradient(145deg, rgba(10, 18, 13, .95), rgba(13, 25, 18, .98));
            padding: 1rem;
        }
        .workflow-step {
            width: 2.6rem;
            height: 2.6rem;
            border-radius: .8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #57ff54, #20dc78);
            color: #09120d;
            font-size: .82rem;
            font-weight: 900;
            letter-spacing: .08em;
        }
        .workflow-card h3 {
            margin: .8rem 0 0;
            font-size: 1.18rem;
        }
        .workflow-card p {
            margin: .65rem 0 0;
            color: #b6c9be;
            line-height: 1.58;
        }

        .pricing-summary-strip {
            margin: 1rem auto 0;
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .8rem;
        }
        .pricing-summary-item {
            border: 1px solid #2f5b41;
            border-radius: .95rem;
            background: rgba(9, 18, 12, .86);
            padding: .8rem .9rem;
        }
        .pricing-summary-item strong {
            display: block;
            color: #f5fff8;
            font-size: 1rem;
        }
        .pricing-summary-item span {
            display: block;
            margin-top: .35rem;
            color: #b4c8bd;
            line-height: 1.45;
            font-size: .85rem;
        }

        .plan-meta-grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .6rem;
        }
        .plan-meta-item {
            border: 1px solid rgba(57, 97, 71, .72);
            border-radius: .85rem;
            background: rgba(9, 18, 12, .82);
            padding: .7rem;
        }
        .plan-meta-item strong {
            display: block;
            color: #f4fff8;
            font-size: .84rem;
        }
        .plan-meta-item span {
            display: block;
            margin-top: .3rem;
            color: #b6cabe;
            line-height: 1.45;
            font-size: .78rem;
        }

        .cta-band {
            margin-top: 1.5rem;
            border: 1px solid #2d5c42;
            border-radius: 1.25rem;
            background:
                radial-gradient(circle at 90% 18%, rgba(87, 255, 84, .14), transparent 26%),
                linear-gradient(145deg, rgba(9, 18, 12, .96), rgba(11, 26, 17, .98));
            padding: 1.25rem;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) auto;
            gap: 1rem;
            align-items: center;
        }
        .cta-band h2 {
            margin: .55rem 0 0;
            font-size: clamp(1.45rem, 2.8vw, 2.35rem);
            line-height: 1.08;
        }
        .cta-band p {
            margin: .7rem 0 0;
            color: #b7ccbf;
            line-height: 1.58;
            max-width: 60ch;
        }
        .cta-band-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            justify-content: flex-end;
        }

        .pricing-grid { margin-top: 1.2rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .plan {
            position: relative;
            border: 1px solid #2f5c43;
            border-radius: 1rem;
            background: linear-gradient(145deg, #0b150f, #112118);
            padding: 1rem;
            min-height: 460px;
            display: flex;
            flex-direction: column;
            transition: border-color .24s ease, box-shadow .24s ease, transform .24s ease;
        }
        .plan.popular {
            border-color: #46df74;
            box-shadow: 0 0 0 1px rgba(70,223,116,.46), 0 28px 40px rgba(6,20,11,.45);
        }
        .plan:hover,
        .plan:focus-within {
            border-color: #46df74;
            box-shadow: 0 0 0 1px rgba(70,223,116,.46), 0 28px 40px rgba(6,20,11,.45);
            transform: translateY(-3px);
        }
        .pop {
            position: absolute; top: -.75rem; left: 50%; transform: translateX(-50%);
            font-size: .74rem; font-weight: 800; color: #0b190f;
            background: linear-gradient(140deg, #3cff3c, #25df72);
            border-radius: 999px; padding: .3rem .7rem; border: 1px solid #78f39f;
        }
        .plan h3 { margin: 0; font-size: 1.4rem; }
        .price { margin-top: .45rem; font-size: 2.5rem; font-weight: 900; line-height: 1; letter-spacing: -.03em; }
        .price small { font-size: 1rem; color: #b3cabd; font-weight: 700; margin-left: .2rem; }
        .plan-discount {
            margin-top: .58rem;
            font-size: .9rem;
            color: #d4e9dd;
            line-height: 1.4;
            border: 1px solid rgba(76, 255, 146, .28);
            background: linear-gradient(140deg, rgba(13, 40, 23, .56), rgba(8, 24, 15, .7));
            border-radius: .65rem;
            padding: .45rem .55rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .3rem;
        }
        .plan-discount-kicker {
            color: #cde3d7;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .01em;
        }
        .plan-discount-offer {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .14rem .4rem;
            border-radius: 999px;
            border: 1px solid rgba(74, 222, 128, .52);
            background: rgba(34, 197, 94, .15);
            color: #bbf7d0;
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
            line-height: 1;
        }
        .plan-discount-detail {
            color: #e2efe7;
            font-size: .9rem;
            font-weight: 600;
        }
        .plan-discount strong {
            color: #a7f3d0;
            font-size: 1.08rem;
            font-weight: 900;
            text-shadow: 0 0 14px rgba(74, 222, 128, .35);
        }
        .price-old {
            text-decoration: line-through;
            color: #8da99b;
            margin-right: .26rem;
            font-weight: 700;
        }
        .plan p { margin: .65rem 0 0; color: #b6c9be; line-height: 1.5; }
        .plan ul { margin: .8rem 0 0; padding: 0; list-style: none; display: grid; gap: .45rem; color: #e1efe5; font-size: .95rem; }
        .plan li { display: flex; align-items: flex-start; gap: .45rem; }
        .plan li::before {
            content: "";
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
            background: #47ff6f;
            box-shadow: 0 0 12px rgba(71, 255, 111, .9), 0 0 22px rgba(71, 255, 111, .5);
            margin-top: .34rem;
            flex: 0 0 .5rem;
        }
        .plan li.plan-highlight {
            margin-top: .3rem;
            border: 1px solid rgba(34, 197, 94, .45);
            border-radius: .62rem;
            background: linear-gradient(145deg, rgba(20, 83, 45, .42), rgba(6, 78, 59, .36));
            padding: .5rem .58rem;
            color: #bbf7d0;
            font-weight: 700;
        }
        .plan-highlight-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 74px;
            padding: .14rem .48rem;
            border-radius: 999px;
            border: 1px solid rgba(134, 239, 172, .48);
            background: rgba(12, 28, 17, .72);
            color: #dcfce7;
            font-size: .68rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            box-shadow: 0 0 0 1px rgba(34, 197, 94, .12), 0 8px 18px rgba(3, 14, 8, .28);
            flex: 0 0 auto;
        }
        .plan li.plan-highlight::before {
            width: .56rem;
            height: .56rem;
            margin-top: .27rem;
            background: #22c55e;
            box-shadow: 0 0 12px rgba(34, 197, 94, .85), 0 0 22px rgba(16, 185, 129, .45);
        }
        .plan .plan-cta {
            margin-top: auto;
            padding-top: .9rem;
        }
        .plan .inline-form {
            width: 100%;
            display: block;
            margin-top: 0;
        }
        .plan .inline-form .btn {
            width: 100%;
            justify-content: center;
            margin-top: 0;
        }
        .plan .plan-cta > .btn {
            width: 100%;
            justify-content: center;
            margin-top: 0;
        }

        .faq-list { width: min(880px, 100%); margin: 1.2rem auto 0; display: grid; gap: .65rem; }
        .faq-item {
            border: 1px solid #2f5e43; border-radius: .8rem;
            background: linear-gradient(145deg, #0d1711, #101d15);
            overflow: hidden;
        }
        .faq-btn {
            width: 100%; border: 0; background: transparent; color: #e8f0ff;
            font-size: 1rem; font-weight: 700;
            padding: .9rem 1rem; display: flex; align-items: center; justify-content: space-between;
            text-align: left; cursor: pointer;
        }
        .faq-btn span:last-child { color: #b2cabd; }
        .faq-content {
            max-height: 0; overflow: hidden; transition: max-height .28s ease;
            color: #b4c8bc; line-height: 1.55; padding: 0 1rem;
        }
        .faq-item.is-open .faq-content { max-height: 220px; padding: 0 1rem .9rem; }
        .about-section {
            border-top: 0;
            margin-top: 1.5rem;
            padding-top: .5rem;
        }
        .about-hero-band {
            position: relative;
            min-height: clamp(240px, 34vw, 420px);
            border-radius: 1.15rem;
            overflow: hidden;
            background:
                linear-gradient(112deg, rgba(4, 20, 13, .92), rgba(7, 36, 23, .84) 45%, rgba(4, 20, 13, .56)),
                var(--about-hero-image, linear-gradient(145deg, #08150f, #103724));
            background-size: cover;
            background-position: center;
            border: 0;
            box-shadow: 0 26px 48px rgba(2, 12, 8, .42);
            display: flex;
            align-items: center;
        }
        .about-hero-band::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(3, 15, 10, .84) 0%, rgba(3, 15, 10, .54) 45%, rgba(3, 15, 10, .18) 100%);
        }
        .about-hero-content {
            position: relative;
            z-index: 2;
            padding-top: 1.2rem;
            padding-bottom: 1.2rem;
        }
        .about-hero-kicker {
            margin: 0;
            font-size: .82rem;
            letter-spacing: .12em;
            font-weight: 800;
            text-transform: uppercase;
            color: #97ffc0;
        }
        .about-hero-title {
            margin: .4rem 0 0;
            font-size: clamp(2.15rem, 5.8vw, 4.35rem);
            line-height: .95;
            letter-spacing: .02em;
            text-transform: uppercase;
            color: #f6fbff;
        }
        .about-hero-breadcrumb {
            margin: .9rem 0 0;
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            color: #dff7e8;
            font-size: .86rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .about-hero-breadcrumb::before {
            content: "";
            width: 70px;
            height: 2px;
            background: #59ff95;
            opacity: .9;
        }
        .about-story {
            margin-top: 1.9rem;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 1rem;
            align-items: stretch;
        }
        .about-story-copy {
            border: 0;
            border-radius: 1rem;
            background: linear-gradient(145deg, #08160f, #0d2a1b);
            padding: 1.1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 32px rgba(2, 12, 8, .32);
        }
        .about-story-copy::before {
            content: "NOSOTROS";
            position: absolute;
            top: .25rem;
            left: .85rem;
            font-size: clamp(2.4rem, 7vw, 5.8rem);
            line-height: .9;
            font-weight: 900;
            letter-spacing: .03em;
            color: rgba(172, 240, 199, .08);
            pointer-events: none;
        }
        .about-story-title {
            margin: .3rem 0 0;
            position: relative;
            z-index: 1;
            font-size: clamp(1.6rem, 3.2vw, 2.5rem);
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #f2fff7;
        }
        .about-story-lead {
            margin: .68rem 0 0;
            position: relative;
            z-index: 1;
            color: #c8e9d5;
            line-height: 1.62;
            font-size: 1rem;
        }
        .about-highlights {
            margin: .88rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .48rem;
        }
        .about-highlights li {
            display: flex;
            align-items: flex-start;
            gap: .52rem;
            color: #e7faef;
            line-height: 1.48;
        }
        .about-highlights li::before {
            content: "";
            width: .48rem;
            height: .48rem;
            border-radius: 999px;
            margin-top: .34rem;
            flex: 0 0 .48rem;
            background: #5bff96;
            box-shadow: 0 0 10px rgba(91, 255, 150, .76);
        }
        .about-highlights.is-checks li::before {
            content: "?";
            width: auto;
            height: auto;
            margin-top: 0;
            border-radius: 0;
            flex: 0 0 auto;
            background: transparent;
            box-shadow: none;
            color: #9dffb6;
            font-weight: 900;
            line-height: 1.2;
        }
        .about-story-media {
            border: 0;
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
            min-height: 380px;
            background: linear-gradient(145deg, #0a1b12, #123623);
            box-shadow: 0 18px 34px rgba(4, 10, 7, .34);
        }
        .about-story-media img {
            width: 100%;
            height: 100%;
            min-height: 380px;
            object-fit: cover;
            display: block;
            filter: saturate(1.08) contrast(1.02);
        }
        .about-story-media-placeholder {
            min-height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
            color: #d9f2e3;
            font-weight: 700;
        }
        .about-year-badge {
            position: absolute;
            left: 1rem;
            bottom: 1rem;
            background: linear-gradient(145deg, #69ff9f, #21cf6d);
            color: #042312;
            border-radius: .7rem;
            padding: .7rem .88rem;
            min-width: 120px;
            box-shadow: 0 12px 26px rgba(2, 18, 9, .42);
        }
        .about-year-value {
            display: block;
            font-size: 1.85rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: .03em;
        }
        .about-year-label {
            display: block;
            margin-top: .2rem;
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
        }
        .about-proof {
            margin-top: 1.15rem;
            border: 0;
            border-radius: 1rem;
            background: linear-gradient(145deg, #061f13, #0b3922);
            padding: 1.15rem 1.2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 16px 30px rgba(2, 12, 8, .32);
        }
        .about-proof::before {
            content: "TESTIMONIO";
            position: absolute;
            right: .8rem;
            top: .3rem;
            font-size: clamp(1.9rem, 5vw, 4rem);
            font-weight: 900;
            line-height: .9;
            color: rgba(175, 243, 203, .08);
            letter-spacing: .02em;
            pointer-events: none;
        }
        .about-proof-kicker {
            margin: 0;
            color: #97ffc0;
            font-size: .8rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .about-proof-quote {
            margin: .56rem 0 0;
            font-size: 1.08rem;
            line-height: 1.64;
            color: #dcf6e7;
            max-width: 80ch;
        }
        .about-proof-author {
            margin-top: .9rem;
            border-left: 2px solid #5bff96;
            padding-left: .68rem;
            color: #f4fff8;
            font-weight: 700;
        }
        .about-proof-author span {
            display: block;
            margin-top: .18rem;
            color: #b7dbc6;
            font-weight: 600;
            font-size: .86rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .about-team {
            margin-top: 1.15rem;
        }
        .about-team-title {
            margin: 0;
            font-size: clamp(1.5rem, 3.3vw, 2.2rem);
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #f3fff8;
        }
        .about-team-subtitle {
            margin: .45rem 0 0;
            color: #bddfc8;
            line-height: 1.58;
            max-width: 66ch;
        }
        .about-team-grid {
            margin-top: .95rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .82rem;
        }
        .about-team-card {
            border: 0;
            border-radius: .9rem;
            background: linear-gradient(145deg, #0a1a11, #0e2c1c);
            overflow: hidden;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            box-shadow: 0 12px 24px rgba(2, 12, 8, .24);
        }
        .about-team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 30px rgba(3, 12, 8, .36);
        }
        .about-team-image-wrap {
            min-height: 220px;
            background: linear-gradient(145deg, #0b2216, #123726);
        }
        .about-team-image-wrap img {
            width: 100%;
            height: 100%;
            min-height: 220px;
            object-fit: cover;
            display: block;
        }
        .about-team-placeholder {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #dcf3e3;
            font-weight: 700;
            padding: 1rem;
        }
        .about-team-info {
            padding: .78rem .82rem .9rem;
        }
        .about-team-role {
            display: inline-flex;
            align-items: center;
            gap: .42rem;
            color: #98ffc0;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .about-team-role::before {
            content: "";
            width: 20px;
            height: 2px;
            background: #5bff96;
        }
        .about-team-name {
            margin: .52rem 0 0;
            color: #f3fff8;
            font-size: 1.26rem;
            line-height: 1.22;
            font-weight: 900;
        }
        .about-team-note {
            margin: .28rem 0 0;
            color: #bbdfc8;
            line-height: 1.45;
            font-size: .9rem;
        }
        .contact-shell {
            margin-top: 1.1rem;
            border: 1px solid #2b5a40;
            border-radius: 1rem;
            background: linear-gradient(145deg, #0b1510, #102019);
            padding: 1rem;
        }
        .contact-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .7rem;
        }
        .contact-info-card {
            border: 1px solid #2f5e43;
            border-radius: .85rem;
            background: rgba(8, 16, 11, .85);
            padding: .7rem .75rem;
            display: flex;
            align-items: center;
            gap: .58rem;
        }
        .contact-icon {
            width: 2.15rem;
            height: 2.15rem;
            border-radius: .58rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 2.15rem;
            border: 1px solid #3a7352;
            color: #a8ffbf;
        }
        .contact-icon svg {
            width: 1.2rem;
            height: 1.2rem;
        }
        .contact-icon--map {
            background: linear-gradient(145deg, rgba(34, 197, 94, .26), rgba(10, 61, 35, .92));
            border-color: rgba(74, 222, 128, .45);
        }
        .contact-icon--mail {
            background: linear-gradient(145deg, rgba(96, 165, 250, .28), rgba(22, 78, 99, .9));
            border-color: rgba(125, 211, 252, .5);
            color: #bfeaff;
        }
        .contact-icon--wa {
            background: linear-gradient(145deg, rgba(74, 222, 128, .3), rgba(20, 83, 45, .92));
            border-color: rgba(74, 222, 128, .5);
        }
        .contact-info-card p {
            margin: 0;
            color: #d8efe1;
            line-height: 1.45;
            font-size: .9rem;
        }
        .contact-form-grid {
            margin-top: .95rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .72rem;
        }
        .contact-form-grid label {
            display: grid;
            gap: .38rem;
            font-size: .8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #bde3cb;
        }
        .contact-form-grid label.full { grid-column: 1 / -1; }
        .contact-input {
            width: 100%;
            min-height: 46px;
            border-radius: .75rem;
            border: 1px solid #2d5c42;
            background: #09120d;
            color: #ecfff1;
            padding: .7rem .8rem;
            outline: none;
        }
        .contact-input:focus {
            border-color: #44e66f;
            box-shadow: 0 0 0 3px rgba(71, 255, 111, .14);
        }
        textarea.contact-input {
            min-height: 132px;
            resize: vertical;
        }
        .contact-submit {
            margin-top: .85rem;
        }
        .legal-shell {
            margin-top: 1.1rem;
            border: 1px solid #2b5a40;
            border-radius: 1rem;
            background: linear-gradient(145deg, #0b1510, #102019);
            padding: 1rem;
        }
        .legal-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .9rem;
        }
        .legal-card {
            border: 1px solid #2f5e43;
            border-radius: .9rem;
            background: rgba(8, 16, 11, .84);
            padding: .95rem;
        }
        .legal-block + .legal-block {
            margin-top: .85rem;
            padding-top: .85rem;
            border-top: 1px solid #2a523d;
        }
        .legal-block h3 {
            margin: 0;
            font-size: 1.02rem;
            color: #e8fff0;
        }
        .legal-block p {
            margin: .52rem 0 0;
            color: #c7e0d2;
            line-height: 1.58;
            font-size: .92rem;
        }
        .legal-points {
            margin: .55rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .32rem;
        }
        .legal-points li {
            color: #d8efe1;
            font-size: .88rem;
            line-height: 1.45;
            display: flex;
            gap: .45rem;
        }
        .legal-points li::before {
            content: "";
            width: .44rem;
            height: .44rem;
            margin-top: .33rem;
            border-radius: 999px;
            background: #47ff6f;
            box-shadow: 0 0 8px rgba(71, 255, 111, .72);
            flex: 0 0 .44rem;
        }
        .footer {
            margin-top: 3rem;
            padding: 0 0 1.1rem;
            position: relative;
            z-index: 2;
        }
        .footer-panel {
            border: 1px solid #2b5d40;
            border-radius: 1.15rem;
            padding: 2rem 2rem 1rem;
            background:
                radial-gradient(circle at 92% 24%, rgba(71, 255, 111, .2), transparent 42%),
                linear-gradient(145deg, #06120b, #08160f 56%, #060f0a);
        }
        .footer-grid { display: grid; grid-template-columns: 1.35fr .8fr .8fr .8fr; gap: 1.1rem; }
        .footer h4 { margin: 0; font-size: 1rem; color: #f0f6ff; }
        .footer-neon-title {
            color: #47ff6f;
            font-weight: 900;
            letter-spacing: .02em;
            text-shadow:
                0 0 10px rgba(71, 255, 111, .8),
                0 0 22px rgba(71, 255, 111, .55),
                0 0 34px rgba(71, 255, 111, .35);
            animation: footerNeonPulse 2.4s ease-in-out infinite;
        }
        @keyframes footerNeonPulse {
            0%, 100% {
                opacity: .78;
                text-shadow:
                    0 0 9px rgba(71, 255, 111, .55),
                    0 0 18px rgba(71, 255, 111, .34),
                    0 0 28px rgba(71, 255, 111, .2);
            }
            50% {
                opacity: 1;
                text-shadow:
                    0 0 12px rgba(71, 255, 111, .95),
                    0 0 26px rgba(71, 255, 111, .7),
                    0 0 42px rgba(71, 255, 111, .46);
            }
        }
        .footer-brand {
            padding-right: .5rem;
            overflow: visible;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer-brand-logo {
            width: clamp(170px, 18vw, 240px);
            height: 58px;
            object-fit: contain;
            border: 0;
            border-radius: 0;
            background: transparent;
            filter: drop-shadow(0 0 12px rgba(60, 255, 60, 0.18));
            display: block;
            transform: scale(2.2);
            transform-origin: center center;
            margin: .55rem auto 1.3rem;
        }
        .footer-lead { margin: .2rem 0 0; color: #b7cabf; line-height: 1.65; text-align: center; }
        .footer p,
        .footer li,
        .footer a { color: #b5c9be; line-height: 1.6; text-decoration: none; font-size: .94rem; }
        .footer ul { margin: .7rem 0 0; padding: 0; list-style: none; display: grid; gap: .24rem; }
        .footer a:hover { color: #eaf4ff; }
        .copy {
            margin-top: 1.2rem;
            padding-top: .85rem;
            border-top: 1px solid #2e5f43;
            text-align: center;
            color: #aecdba;
            font-size: .86rem;
        }

        .reveal { opacity: 0; transform: translateY(22px); transition: opacity .55s ease, transform .55s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }

        body.perf-lite .hero-fx,
        body.perf-lite .hero-fx-line,
        body.perf-lite .hero-fx-node {
            display: none !important;
        }
        body.perf-lite .brands-track {
            animation-duration: 64s;
        }
        body.perf-lite .reveal {
            opacity: 1;
            transform: none;
            transition: none;
        }
        body.perf-lite .top-wrap,
        body.perf-lite .modal-backdrop {
            backdrop-filter: none;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            .reveal {
                opacity: 1 !important;
                transform: none !important;
            }
            .brands-track {
                animation: none !important;
                transform: translate3d(0, 0, 0) !important;
            }
            .footer-neon-title {
                animation: none !important;
                opacity: 1 !important;
            }
        }

        .modal-backdrop {
            position: fixed; inset: 0; z-index: 90;
            background: rgba(2, 5, 12, .76);
            backdrop-filter: blur(3px);
            display: none;
        }
        .modal-backdrop.is-open { display: block; }
        .modal {
            position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%);
            width: min(460px, calc(100% - 1.2rem));
            border: 1px solid #34424e; border-radius: 1rem;
            background: #10171f; box-shadow: 0 26px 56px rgba(3,9,24,.62);
            color: #eaf2ff; padding: 1rem; z-index: 100; display: none;
        }
        .modal.is-open { display: block; }
        .modal h4 { margin: 0; font-size: 1.1rem; }
        .modal p { margin: .55rem 0 0; color: #b3c0cb; line-height: 1.45; }
        .modal-time { margin-top: .75rem; border: 1px solid #3d5a4e; border-radius: .75rem; background: rgba(14,24,20,.85); padding: .65rem .75rem; color: #dbecff; font-weight: 700; }
        .modal-actions { margin-top: .95rem; display: flex; justify-content: flex-end; }
        .modal-close { border: 1px solid #3a4b57; border-radius: .68rem; background: #16222d; color: #f3f8ff; min-height: 40px; padding: .45rem .85rem; font-weight: 800; cursor: pointer; }
        .contact-status-backdrop {
            position: fixed;
            inset: 0;
            z-index: 105;
            background: rgba(0, 0, 0, .72);
            backdrop-filter: blur(2px);
            display: none;
        }
        .contact-status-backdrop.is-open { display: block; }
        .contact-status-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(460px, calc(100% - 1.2rem));
            border: 2px solid #030303;
            border-radius: 1rem;
            background: radial-gradient(circle at 20% 15%, #0e2418, #08110d 70%);
            box-shadow: 0 0 0 2px rgba(0, 0, 0, .75), 0 0 24px rgba(74, 222, 128, .6), 0 20px 42px rgba(0, 0, 0, .62);
            color: #eafff0;
            padding: 1rem;
            z-index: 106;
            display: none;
        }
        .contact-status-modal.is-open { display: block; }
        .contact-status-modal h4 {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 900;
        }
        .contact-status-modal p {
            margin: .55rem 0 0;
            color: #ccefd8;
            line-height: 1.45;
        }
        .contact-status-modal.is-error {
            box-shadow: 0 0 0 2px rgba(0, 0, 0, .8), 0 0 20px rgba(248, 113, 113, .58), 0 20px 42px rgba(0, 0, 0, .62);
            background: radial-gradient(circle at 20% 15%, #2a1111, #130909 72%);
            color: #ffe9e9;
        }
        .contact-status-modal.is-error p {
            color: #ffd8d8;
        }
        .contact-status-actions {
            margin-top: .95rem;
            display: flex;
            justify-content: flex-end;
        }
        .contact-status-close {
            border: 1px solid #000;
            border-radius: .68rem;
            background: linear-gradient(145deg, #183224, #0a140f);
            color: #f0fff5;
            min-height: 40px;
            padding: .45rem .85rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 0 12px rgba(74, 222, 128, .28);
        }
        body.quote-modal-open {
            overflow: hidden;
        }
        .quote-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 108;
            background: rgba(2, 7, 6, .82);
            backdrop-filter: blur(5px);
            display: none;
        }
        .quote-modal-backdrop.is-open {
            display: block;
        }
        .quote-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(1080px, calc(100% - 1.4rem));
            height: min(92vh, 860px);
            max-height: min(92vh, 860px);
            border: 1px solid rgba(97, 255, 158, .26);
            border-radius: 1.6rem;
            background: #eaf4ef;
            box-shadow: 0 36px 80px rgba(0, 0, 0, .42);
            z-index: 109;
            display: none;
            overflow: hidden;
        }
        .quote-modal.is-open {
            display: block;
        }
        .quote-modal-shell {
            display: grid;
            grid-template-columns: minmax(0, .96fr) minmax(0, 1.04fr);
            height: 100%;
            min-height: 0;
        }
        .quote-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
            width: 44px;
            height: 44px;
            border: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, .9);
            color: #50606a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
        }
        .quote-modal-close svg {
            width: 18px;
            height: 18px;
        }
        .quote-modal-side {
            position: relative;
            overflow: hidden;
            padding: 2.2rem 2rem 1.8rem;
            background:
                radial-gradient(circle at 24% 18%, rgba(255, 255, 255, .14), transparent 30%),
                radial-gradient(circle at 82% 86%, rgba(93, 85, 255, .2), transparent 28%),
                linear-gradient(140deg, #0ea5d8 0%, #129fe2 42%, #14b2ec 100%);
            color: #f5fcff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .quote-modal-side::after {
            content: "";
            position: absolute;
            inset: auto -8% -16% auto;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, .42), transparent 64%);
            filter: blur(12px);
            pointer-events: none;
        }
        .quote-modal-kicker {
            margin: 0;
            font-size: .8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: rgba(16, 33, 43, .72);
        }
        .quote-modal-side .quote-modal-kicker {
            color: rgba(236, 250, 255, .78);
        }
        .quote-modal-title {
            margin: .85rem 0 0;
            max-width: 14ch;
            font-size: clamp(2rem, 4.2vw, 3.45rem);
            line-height: 1.02;
            letter-spacing: -.04em;
            font-weight: 900;
        }
        .quote-modal-copy {
            margin: 1rem 0 0;
            max-width: 34ch;
            color: rgba(240, 251, 255, .9);
            line-height: 1.65;
            font-size: 1rem;
        }
        .quote-modal-visual {
            position: relative;
            min-height: 340px;
            margin-top: 1.5rem;
        }
        .quote-modal-main-photo {
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-44%);
            width: min(340px, 76%);
            aspect-ratio: 4 / 5;
            border-radius: 999px 999px 1.6rem 1.6rem;
            overflow: hidden;
            border: 10px solid rgba(41, 111, 255, .16);
            box-shadow: 0 18px 40px rgba(2, 14, 40, .24);
            background: linear-gradient(180deg, rgba(255, 255, 255, .22), rgba(255, 255, 255, .04));
        }
        .quote-modal-main-photo img,
        .quote-modal-bubble img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .quote-modal-main-photo-fallback,
        .quote-modal-bubble-fallback {
            width: 100%;
            height: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(30, 64, 175, .28), rgba(16, 185, 129, .28));
            color: #f8fcff;
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: .08em;
        }
        .quote-modal-bubble {
            position: absolute;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            overflow: hidden;
            border: 8px solid rgba(0, 135, 235, .52);
            background: rgba(255, 255, 255, .16);
            box-shadow: 0 12px 28px rgba(5, 24, 46, .22);
        }
        .quote-modal-bubble.is-a {
            left: 0;
            top: 3rem;
        }
        .quote-modal-bubble.is-b {
            left: 2rem;
            bottom: 1.8rem;
            width: 88px;
            height: 88px;
        }
        .quote-modal-bubble.is-c {
            right: 1rem;
            bottom: 1rem;
            width: 116px;
            height: 116px;
        }
        .quote-modal-stat {
            position: absolute;
            right: 0;
            top: 1.4rem;
            min-width: 170px;
            padding: .85rem .95rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, .2);
            background: rgba(2, 22, 34, .18);
            backdrop-filter: blur(12px);
            box-shadow: 0 18px 34px rgba(2, 14, 40, .16);
        }
        .quote-modal-stat span {
            display: block;
            font-size: .76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: rgba(231, 248, 255, .78);
        }
        .quote-modal-stat strong {
            display: block;
            margin-top: .35rem;
            font-size: 1.08rem;
            line-height: 1.25;
        }
        .quote-modal-form-panel {
            padding: 2rem 2rem 1.7rem;
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, .08), transparent 22%),
                linear-gradient(180deg, #f8fbfa 0%, #eef6f2 100%);
            color: #16212a;
            min-height: 0;
            height: 100%;
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
            padding-right: 1.2rem;
        }
        .quote-modal-form-panel::-webkit-scrollbar {
            width: 10px;
        }
        .quote-modal-form-panel::-webkit-scrollbar-track {
            background: rgba(14, 22, 28, .08);
            border-radius: 999px;
        }
        .quote-modal-form-panel::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #21c8e7, #1098d1);
            border-radius: 999px;
            border: 2px solid rgba(248, 251, 250, .9);
        }
        .quote-modal-form-panel {
            scrollbar-width: thin;
            scrollbar-color: #1098d1 rgba(14, 22, 28, .08);
        }
        .quote-form-header h3 {
            margin: .5rem 0 0;
            font-size: clamp(1.55rem, 2.4vw, 2rem);
            line-height: 1.1;
            letter-spacing: -.03em;
            color: #0f1720;
        }
        .quote-form-header p {
            margin: .7rem 0 0;
            color: #52616b;
            line-height: 1.55;
        }
        .quote-form-alert {
            margin-top: 1rem;
            border-radius: 1rem;
            padding: .9rem 1rem;
            font-size: .92rem;
            line-height: 1.5;
            border: 1px solid transparent;
        }
        .quote-form-alert.is-success {
            background: rgba(16, 185, 129, .12);
            border-color: rgba(16, 185, 129, .26);
            color: #0f6b49;
        }
        .quote-form-alert.is-error {
            background: rgba(239, 68, 68, .1);
            border-color: rgba(239, 68, 68, .22);
            color: #aa2323;
        }
        .quote-plan-pill {
            margin-top: 1rem;
            display: none;
            align-items: center;
            gap: .45rem;
            width: fit-content;
            border-radius: 999px;
            background: rgba(12, 122, 255, .1);
            border: 1px solid rgba(12, 122, 255, .18);
            color: #1660b7;
            padding: .48rem .8rem;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .03em;
        }
        .quote-plan-pill.is-visible {
            display: inline-flex;
        }
        .quote-plan-pill strong {
            color: #0f1720;
        }
        .quote-form-grid {
            margin-top: 1.2rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }
        .quote-form-field {
            display: grid;
            gap: .38rem;
            font-size: .88rem;
            font-weight: 700;
            color: #22313a;
        }
        .quote-form-field--full {
            grid-column: 1 / -1;
        }
        .quote-form-inline {
            display: grid;
            grid-template-columns: 132px minmax(0, 1fr);
            gap: .6rem;
        }
        .quote-form-prefix {
            appearance: none;
        }
        .quote-form-field .contact-input {
            min-height: 50px;
            border-color: #c8d9d1;
            background: rgba(255, 255, 255, .96);
            color: #10212b;
            padding-inline: .9rem;
        }
        .quote-form-field .contact-input::placeholder {
            color: #7b8a94;
        }
        .quote-form-field .contact-input:focus {
            border-color: #18a1d9;
            box-shadow: 0 0 0 4px rgba(24, 161, 217, .12);
        }
        .quote-form-field textarea.contact-input {
            min-height: 96px;
        }
        .quote-form-help {
            font-size: .78rem;
            line-height: 1.45;
            color: #67808e;
        }
        .quote-form-error {
            font-size: .76rem;
            line-height: 1.4;
            color: #d12b2b;
            font-weight: 700;
        }
        .quote-form-checkbox {
            display: grid;
            gap: .45rem;
            margin-top: .05rem;
        }
        .quote-form-checkbox label {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            font-size: .88rem;
            font-weight: 700;
            color: #22313a;
        }
        .quote-form-checkbox input {
            margin-top: .22rem;
            width: 18px;
            height: 18px;
            accent-color: #1698d5;
        }
        .quote-form-legal {
            margin: 0;
            font-size: .77rem;
            line-height: 1.55;
            color: #6a7a85;
        }
        .quote-form-submit {
            margin-top: .45rem;
            width: 100%;
            min-height: 52px;
            font-size: .94rem;
        }
        @media (max-width: 980px) {
            .quote-modal {
                width: min(760px, calc(100% - 1rem));
                height: auto;
                max-height: 94vh;
                overflow-y: auto;
            }
            .quote-modal-shell {
                grid-template-columns: 1fr;
                height: auto;
            }
            .quote-modal-side {
                min-height: 320px;
                padding-bottom: 1rem;
            }
            .quote-modal-title {
                max-width: 12ch;
            }
            .quote-modal-copy {
                max-width: 40ch;
            }
            .quote-modal-visual {
                min-height: 250px;
            }
            .quote-modal-main-photo {
                width: min(260px, 52%);
                transform: translateX(-38%);
            }
            .quote-modal-bubble.is-a {
                left: 1rem;
                top: 1.8rem;
            }
            .quote-modal-bubble.is-c {
                right: 1.8rem;
                bottom: .6rem;
            }
            .quote-modal-stat {
                right: 1rem;
                top: 1rem;
            }
            .quote-modal-form-panel {
                height: auto;
                overflow: visible;
                padding-right: 1rem;
            }
        }

        @media (max-width: 1080px) {
            .top-nav { gap: .62rem; padding: .62rem .78rem; }
            .brand {
                width: 138px;
                min-width: 138px;
            }
            .brand-logo {
                width: 138px;
                height: 44px;
                transform: scale(1.36);
            }
            .menu-links {
                display: inline-flex;
                flex: 1 1 auto;
                justify-content: center;
                margin-inline: 0;
                overflow-x: auto;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }
            .menu-links::-webkit-scrollbar { display: none; }
            .menu-links a {
                font-size: .8rem;
                padding: .42rem .72rem;
                white-space: nowrap;
            }
            .nav-actions { gap: .42rem; }
            .nav-actions .btn-outline { display: inline-flex; }
            .nav-actions .btn {
                min-height: 40px;
                font-size: .8rem;
                padding: .52rem .78rem;
            }
            .hero { grid-template-columns: 1fr; }
            .hero-proof-strip,
            .outcome-grid,
            .workflow-grid,
            .plan-meta-grid,
            .service-grid,
            .pricing-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-grid .service:last-child { grid-column: 1 / -1; }
            .pricing-summary-strip { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .pricing-summary-strip .pricing-summary-item:last-child { grid-column: 1 / -1; }
            .footer-panel { padding: 1.5rem 1.2rem 1rem; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .hero-insight-grid { grid-template-columns: 1fr 1fr; }
            .about-story { grid-template-columns: 1fr; }
            .about-team-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .about-story-media,
            .about-story-media img,
            .about-story-media-placeholder { min-height: 300px; }
            .contact-info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .contact-info-grid .contact-info-card:last-child { grid-column: 1 / -1; }
            .contact-form-grid { grid-template-columns: 1fr; }
            .quote-form-grid { grid-template-columns: 1fr; }
            .quote-form-field--full { grid-column: auto; }
            .legal-grid { grid-template-columns: 1fr; }
            .contact-shell,
            .legal-shell { padding: .95rem; }
            .heading h2 { font-size: clamp(1.5rem, 4.1vw, 2.5rem); }
            .service-media { min-height: 188px; }
            .home-scroll-bg-layer { transform: none; }
        }
        @media (max-width: 820px) {
            .shell { width: min(1240px, calc(100% - 1rem)); }
            .top-nav {
                justify-content: space-between;
                padding: .68rem .74rem;
                gap: .6rem;
            }
            .brand {
                width: 120px;
                min-width: 120px;
            }
            .brand-logo {
                width: 150px;
                height: 46px;
                transform: scale(2.05);
                object-position: left center;
                margin-left: -12px;
            }
            .menu-links,
            .nav-actions { display: none; }
            .mobile-menu-toggle { display: inline-flex; }
            .mobile-nav-actions .btn { min-height: 42px; }
            .hero { gap: 1.25rem; padding-top: 2rem; }
            .hero h1 { line-height: 1.08; }
            .hero p,
            .heading p { font-size: .98rem; }
            .cta-band { grid-template-columns: 1fr; }
            .cta-band-actions { justify-content: flex-start; }
            .hero-media-frame,
            .hero-slide-image,
            .hero-media-placeholder { min-height: 300px; }
            .hero-carousel-control { width: 32px; height: 32px; }
            .about-hero-content { padding-top: .9rem; padding-bottom: .9rem; }
            .about-hero-breadcrumb::before { width: 42px; }
            .quote-modal-close {
                top: .75rem;
                right: .75rem;
            }
            .quote-modal-form-panel {
                padding: 1.2rem 1rem 1rem;
            }
            .quote-form-inline {
                grid-template-columns: 1fr;
            }
            .about-proof { padding: 1rem; }
            .about-proof-quote { font-size: .98rem; }
            .brands-track { animation-duration: 32s; }
            .brands-row { gap: 1.45rem; padding-right: 1.45rem; }
            .brand-chip { min-width: 0; }
            .brand-chip.brand-chip-has-logo {
                min-width: 0;
                gap: 1.65rem;
                padding-inline: 0;
            }
            .brand-chip-logo-box {
                width: 84px;
                height: 28px;
                flex-basis: 84px;
            }
            .brand-chip-logo {
                transform: scale(2.45);
            }
            .brand-chip span { font-size: .83rem; }
            .service-grid,
            .pricing-grid,
            .hero-proof-strip,
            .outcome-grid,
            .workflow-grid,
            .pricing-summary-strip,
            .plan-meta-grid,
            .footer-grid,
            .hero-insight-grid,
            .about-team-grid,
            .contact-info-grid,
            .contact-form-grid,
            .legal-grid { grid-template-columns: 1fr; }
            .service-grid .service:last-child,
            .pricing-grid .plan:last-child,
            .pricing-summary-strip .pricing-summary-item:last-child,
            .contact-info-grid .contact-info-card:last-child { grid-column: auto; }
            .plan,
            .service { min-height: auto; }
            .footer-grid { gap: 1.2rem; }
        }
        @media (max-width: 720px) {
            .shell { width: min(1240px, calc(100% - 1rem)); }
            .brand {
                width: 108px;
                min-width: 108px;
            }
            .brand-logo {
                width: 146px;
                height: 44px;
                transform: scale(2.25);
                object-position: left center;
                margin-left: -14px;
            }
            .top-nav { padding: .58rem .64rem; }
            .brand-chip.brand-chip-has-logo {
                min-width: 0;
                gap: 1.28rem;
                padding-inline: 0;
            }
            .brand-chip-logo-box {
                width: 74px;
                height: 24px;
                flex-basis: 74px;
            }
            .brand-chip-logo {
                transform: scale(2.2);
            }
            .mobile-nav-actions { grid-template-columns: 1fr; }
            .hero-actions { gap: .5rem; }
            .hero-actions .btn,
            .hero-actions .inline-form { width: 100%; }
            .cta-band-actions { width: 100%; }
            .cta-band-actions .btn,
            .cta-band-actions .inline-form { width: 100%; }
            .about-story-copy,
            .about-proof { padding: .88rem; }
            .about-year-badge {
                left: .7rem;
                bottom: .7rem;
                min-width: 104px;
                padding: .58rem .68rem;
            }
            .about-year-value { font-size: 1.48rem; }
            .service-media { min-height: 170px; padding: .4rem; }
            .footer-brand-logo {
                width: clamp(150px, 54vw, 210px);
                height: 52px;
                transform: scale(2);
                transform-origin: center center;
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>
</head>
<body class="{{ $showPrimarySections ? 'is-home' : 'is-subpage' }}">
    @if ($showPrimarySections)
        <div class="home-scroll-bg" data-home-scroll-bg data-bg-images='@json($homePageBackgroundUrls)' aria-hidden="true">
            <span class="home-scroll-bg-layer is-a is-active" data-home-bg-layer="0"></span>
            <span class="home-scroll-bg-layer is-b" data-home-bg-layer="1"></span>
        </div>
    @endif

    <header class="top-wrap">
        <div class="shell">
            <nav class="top-nav">
                <a class="brand" href="{{ route('landing') }}">
                    @if ($brandLogoUrl !== '')
                        <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="brand-logo">
                    @else
                        <span class="brand-fallback">{{ $brandInitials }}</span>
                    @endif
                </a>

                <button type="button"
                        class="mobile-menu-toggle"
                        data-mobile-menu-toggle
                        aria-expanded="false"
                        aria-controls="landing-mobile-nav"
                        aria-label="Abrir menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="menu-links">
                    <a href="{{ $inicioHref }}">Inicio</a>
                    <a href="{{ $featuresHref }}">Servicios</a>
                    <a href="{{ $pricingHref }}">Precios</a>
                    <a href="{{ $faqHref }}">Preguntas</a>
                    <a href="{{ $aboutHref }}">Nosotros</a>
                    <a href="{{ $contactHref }}">Contáctanos</a>
                </div>

                <div class="nav-actions">
                    <a class="btn btn-outline" href="{{ route('login') }}">{{ $loginLabel }}</a>
                    <button class="btn btn-demo"
                            type="button"
                            data-open-quote-modal
                            data-quote-source="nav_desktop"
                            aria-controls="quote-request-modal">
                        Solicita tu cotización
                    </button>
                </div>
            </nav>

            <div id="landing-mobile-nav" class="mobile-nav-panel" data-mobile-nav-panel hidden>
                <div class="mobile-nav-links">
                    <a href="{{ $inicioHref }}">Inicio</a>
                    <a href="{{ $featuresHref }}">Servicios</a>
                    <a href="{{ $pricingHref }}">Precios</a>
                    <a href="{{ $faqHref }}">Preguntas</a>
                    <a href="{{ $aboutHref }}">Nosotros</a>
                    <a href="{{ $contactHref }}">Contáctanos</a>
                </div>
                <div class="mobile-nav-actions">
                    <a class="btn btn-outline" href="{{ route('login') }}">{{ $loginLabel }}</a>
                    <button class="btn btn-demo"
                            type="button"
                            data-open-quote-modal
                            data-quote-source="nav_mobile"
                            aria-controls="quote-request-modal">
                        Solicita tu cotización
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <section class="shell flash-stack" aria-live="polite">
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif
        </section>

        @if ($showPrimarySections)
        <section id="inicio" class="shell hero">
            <div class="hero-fx" aria-hidden="true">
                <span class="hero-fx-line l1"></span>
                <span class="hero-fx-line l2"></span>
                <span class="hero-fx-line l3"></span>
                <span class="hero-fx-node n1"></span>
                <span class="hero-fx-node n2"></span>
                <span class="hero-fx-node n3"></span>
                <span class="hero-fx-node n4"></span>
                <span class="hero-fx-node n5"></span>
            </div>
            <div class="reveal">
                <p class="kicker">{{ $content['hero_kicker'] ?? 'Software para gimnasios' }}</p>
                <h1>{{ $heroTitleText }} <span class="neon">sin problemas</span></h1>
                <p>{{ $content['hero_subtitle'] ?? 'Gestiona recepción, clientes, membresías, caja y reportes desde una plataforma estable para escritorio y móvil.' }}</p>

                <div class="hero-actions">
                    <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                        @csrf
                        <button class="btn btn-demo" type="submit">{{ $demoCtaLabel }}</button>
                    </form>
                    <button class="btn btn-wa btn-quote-trigger"
                            type="button"
                            data-open-quote-modal
                            data-quote-source="hero_secondary"
                            aria-controls="quote-request-modal">
                        <span class="quote-cta-icon-badge" aria-hidden="true">
                            <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                                <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                                <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span>Solicita tu cotización</span>
                    </button>
                </div>

                <p class="hero-note"><b>{{ $demoCtaLabel }}:</b> crea un acceso temporal de prueba. Si superas 3 intentos, espera 60 minutos para volver a solicitar. <b>{{ $loginLabel }}:</b> abre tus datos reales.</p>

                <div class="hero-proof-strip">
                    @foreach ($heroProofItems as $item)
                        <article class="hero-proof-card">
                            <p class="hero-proof-title">{{ $item['title'] }}</p>
                            <p class="hero-proof-copy">{{ $item['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <article class="hero-panel reveal">
                <div class="hero-media-frame">
                    @if (count($heroSlides) > 0)
                        <div class="hero-carousel" data-hero-carousel>
                            @foreach ($heroSlides as $slideIndex => $slideUrl)
                                <figure class="hero-slide {{ $slideIndex === 0 ? 'is-active' : '' }}" data-hero-slide>
                                    <img src="{{ $slideUrl }}"
                                         alt="Slide hero {{ $slideIndex + 1 }}"
                                         class="hero-slide-image"
                                         data-hero-slide-index="{{ $slideIndex + 1 }}"
                                         loading="{{ $slideIndex === 0 ? 'eager' : 'lazy' }}"
                                         decoding="async"
                                         fetchpriority="{{ $slideIndex === 0 ? 'high' : 'auto' }}">
                                </figure>
                            @endforeach
                            @if (count($heroSlides) > 1)
                                <button type="button" class="hero-carousel-control prev" data-hero-prev aria-label="Slide anterior"><</button>
                                <button type="button" class="hero-carousel-control next" data-hero-next aria-label="Slide siguiente">></button>
                                <div class="hero-carousel-dots">
                                    @foreach ($heroSlides as $slideIndex => $slideUrl)
                                        <button type="button"
                                                class="hero-carousel-dot {{ $slideIndex === 0 ? 'is-active' : '' }}"
                                                data-hero-dot="{{ $slideIndex }}"
                                                aria-label="Ir al slide {{ $slideIndex + 1 }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="hero-media-placeholder">Sube imagenes del carrusel hero desde SuperAdmin para mostrar una demo visual en vivo.</div>
                    @endif
                </div>
                <div class="hero-insight-grid">
                    <article class="hero-insight-card">
                        <span class="hero-insight-label">Gimnasios registrados</span>
                        <span class="hero-insight-value">{{ number_format((int) ($stats['gyms'] ?? 0)) }}</span>
                    </article>
                    <article class="hero-insight-card">
                        <span class="hero-insight-label">Con suscripción activa</span>
                        <span class="hero-insight-value">{{ number_format((int) ($stats['active_sessions'] ?? 0)) }}</span>
                    </article>
                </div>
            </article>
        </section>

        <section class="shell brands-band reveal">
            <div class="brands-marquee">
                <div class="brands-track">
                    <div class="brands-row">
                        @foreach ($marqueeItems as $item)
                            <div @class([
                                'brand-chip',
                                'brand-chip-has-logo' => $item['logo'] !== '',
                            ]) data-marquee-chip-index="{{ $item['index'] }}">
                                @if ($item['logo'] !== '')
                                    <span class="brand-chip-logo-box">
                                        <img src="{{ $item['logo'] }}" alt="{{ $item['text'] }}" class="brand-chip-logo" data-marquee-logo-index="{{ $item['index'] }}">
                                    </span>
                                @endif
                                <span data-marquee-text-index="{{ $item['index'] }}">{{ $item['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="brands-row" aria-hidden="true">
                        @foreach ($marqueeItems as $item)
                            <div @class([
                                'brand-chip',
                                'brand-chip-has-logo' => $item['logo'] !== '',
                            ]) data-marquee-chip-index="{{ $item['index'] }}">
                                @if ($item['logo'] !== '')
                                    <span class="brand-chip-logo-box">
                                        <img src="{{ $item['logo'] }}" alt="" class="brand-chip-logo" data-marquee-logo-index="{{ $item['index'] }}">
                                    </span>
                                @endif
                                <span data-marquee-text-index="{{ $item['index'] }}">{{ $item['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="shell outcome-band">
            <header class="heading reveal">
                <small>Lo que ganas</small>
                <h2>Lo que tu gimnasio mejora desde el primer día</h2>
                <p>La página ahora explica mejor beneficios concretos: atender más rápido, controlar mejor y crecer sin desorden.</p>
            </header>
            <div class="outcome-grid">
                @foreach ($outcomeCards as $outcome)
                    <article class="outcome-card reveal">
                        <small>{{ $outcome['eyebrow'] }}</small>
                        <h3>{{ $outcome['title'] }}</h3>
                        <p>{{ $outcome['text'] }}</p>
                        <span class="outcome-metric">{{ $outcome['metric'] }}</span>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="features" class="shell section">
            <header class="heading reveal">
                <small>Servicios</small>
                <h2>Estructura completa para operar y crecer</h2>
                <p>Tres capas clave para recepción, control de datos y experiencia móvil de tu gimnasio.</p>
            </header>
            <div class="service-grid">
                @foreach ($serviceCards as $service)
                    <article class="service reveal">
                        <i>{{ $service['index'] }}</i>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['text'] }}</p>
                        <ul>
                            @foreach ($service['items'] as $item)
                                @if ($item !== '')
                                    <li>{{ $item }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="service-media">
                            @php
                                $imageKey = 'section_'.$service['index'].'_image_url';
                            @endphp
                            @if (!empty($content[$imageKey]))
                                <img src="{{ $content[$imageKey] }}" alt="{{ $service['title'] }}">
                            @else
                                <span>{{ $service['title'] }}</span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="shell section">
            <header class="heading reveal">
                <small>Como funciona</small>
                <h2>Así te ayuda en el día a día del gimnasio</h2>
                <p>El recorrido muestra de forma simple como pasas de configurar tu cuenta a cobrar, controlar y crecer con orden.</p>
            </header>
            <div class="workflow-grid">
                @foreach ($journeySteps as $journeyStep)
                    <article class="workflow-card reveal">
                        <span class="workflow-step">{{ $journeyStep['step'] }}</span>
                        <h3>{{ $journeyStep['title'] }}</h3>
                        <p>{{ $journeyStep['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="pricing" class="shell section">
            <header class="heading reveal">
                <small>Precios</small>
                <h2>Planes claros para cada etapa</h2>
                <p>Empieza con el plan que necesitas hoy y crece sin cambiar de sistema.</p>
            </header>
            <div class="pricing-summary-strip reveal">
                <article class="pricing-summary-item">
                    <strong>Planes fáciles de comparar</strong>
                    <span>Ves rápido cuál se ajusta mejor al tamaño y ritmo de tu gimnasio.</span>
                </article>
                <article class="pricing-summary-item">
                    <strong>Precios claros desde el inicio</strong>
                    <span>Entiendes cuánto pagas y qué incluye cada plan sin leer de más.</span>
                </article>
                <article class="pricing-summary-item">
                    <strong>Si creces, el sistema crece contigo</strong>
                    <span>Puedes empezar con una sede y luego pasar a un plan más completo o multi sede.</span>
                </article>
            </div>
            <div class="pricing-grid">
                @foreach ($publicPlanCards as $planCard)
                    @php
                        $planKey = strtolower(trim((string) ($planCard['plan_key'] ?? '')));
                        $isFeatured = (bool) ($planCard['featured'] ?? false);
                        $isContactMode = (bool) ($planCard['contact_mode'] ?? false);
                        $price = (float) ($planCard['price'] ?? 0);
                        $discountPriceRaw = $planCard['discount_price'] ?? null;
                        $discountPrice = $discountPriceRaw !== null ? (float) $discountPriceRaw : null;
                        $discountPercent = isset($planCard['discount_percent']) ? (int) $planCard['discount_percent'] : null;
                        $planFeatures = array_values(array_filter((array) ($planCard['features'] ?? []), fn ($item) => is_string($item) && trim($item) !== ''));
                        $planCtaLabel = 'SOLICITA TU Cotización';
                    @endphp
                    <article class="plan reveal {{ $isFeatured ? 'popular' : '' }}">
                        @if ($isFeatured)
                            <span class="pop">Plan destacado</span>
                        @endif
                        <h3>{{ $planCard['name'] }}</h3>
                        @if ($isContactMode)
                            <div class="price">Personalizado<small>/Contacto</small></div>
                            <div class="plan-discount">
                                <span class="plan-discount-kicker">Primer mes con</span>
                                <span class="plan-discount-offer">Oferta</span>
                                <span class="plan-discount-detail">
                                    @if ($discountPercent !== null && $discountPercent > 0)
                                        <strong>{{ $discountPercent }}% menos</strong> sobre el valor cotizado.
                                    @elseif ($discountPrice !== null && $discountPrice > 0)
                                        <strong>${{ $formatPlanMoney($discountPrice) }}</strong> de referencia.
                                    @else
                                        <strong>según cotización</strong>.
                                    @endif
                                </span>
                            </div>
                        @else
                            <div class="price">${{ $formatPlanMoney($price) }}<small>/Mes</small></div>
                            <div class="plan-discount">
                                <span class="plan-discount-kicker">Primer mes con</span>
                                <span class="plan-discount-offer">Oferta</span>
                                <span class="plan-discount-detail">
                                    @if ($discountPrice !== null && $discountPrice < $price)
                                        <span class="price-old">${{ $formatPlanMoney($price) }}</span>
                                        <strong>${{ $formatPlanMoney($discountPrice) }}</strong>
                                    @else
                                        <strong>Sin oferta</strong>
                                    @endif
                                </span>
                            </div>
                        @endif
                        <p>{{ $planCard['summary'] }}</p>
                        <div class="plan-meta-grid">
                            <article class="plan-meta-item">
                                <strong>Este plan es para ti si...</strong>
                                <span>{{ $planCard['ideal_for'] ?? 'Operación en crecimiento.' }}</span>
                            </article>
                            <article class="plan-meta-item">
                                <strong>Lo que más te ayuda</strong>
                                <span>{{ $planCard['ops_focus'] ?? 'Control operativo.' }}</span>
                            </article>
                            <article class="plan-meta-item">
                                <strong>Como empiezas</strong>
                                <span>{{ $planCard['setup_note'] ?? 'Configuración según necesidad.' }}</span>
                            </article>
                        </div>
                        <ul>
                            @foreach ($planFeatures as $feature)
                                @php
                                    $featureText = trim((string) $feature);
                                    $featureLower = mb_strtolower($featureText);
                                    $isHighlightedFeature = str_starts_with($featureLower, 'incluido:');
                                    $featureDisplay = $isHighlightedFeature
                                        ? trim((string) preg_replace('/^incluido:\s*/iu', '', $featureText))
                                        : $featureText;
                                @endphp
                                <li class="{{ $isHighlightedFeature ? 'plan-highlight' : '' }}">
                                    @if ($isHighlightedFeature)
                                        <span class="plan-highlight-tag">Incluido</span>
                                    @endif
                                    <span>{{ $featureDisplay }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="plan-cta">
                            <button class="btn btn-quote-trigger {{ $isContactMode ? 'btn-wa' : ($isFeatured ? 'btn-demo' : 'btn-outline') }}"
                                    type="button"
                                    data-plan-cta-key="{{ $planKey }}"
                                    data-open-quote-modal
                                    data-quote-plan="{{ $planKey }}"
                                    data-quote-source="pricing_{{ $planKey }}"
                                    aria-controls="quote-request-modal">
                                <span class="quote-cta-icon-badge" aria-hidden="true">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                                        <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <span>{{ $planCtaLabel }}</span>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="faq" class="shell section">
            <header class="heading reveal">
                <small>Preguntas</small>
                <h2>Preguntas frecuentes antes de arrancar</h2>
                <p>Respuestas rápidas para probar la demo y evaluar si el flujo encaja con tu operación.</p>
            </header>
            <div class="faq-list">
                @foreach ($faqItems as $faqIndex => $faq)
                    <article class="faq-item reveal {{ $faqIndex === 0 ? 'is-open' : '' }}" data-faq-item>
                        <button class="faq-btn" type="button" data-faq-button aria-expanded="{{ $faqIndex === 0 ? 'true' : 'false' }}">
                            <span>{{ $faq['q'] }}</span>
                            <span>+</span>
                        </button>
                        <div class="faq-content" data-faq-content>
                            <p>{{ $faq['a'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="shell cta-band reveal">
            <div>
                <small class="kicker">Siguiente paso</small>
                <h2>Prueba la demo o pide una cotización con contexto operativo real.</h2>
                <p>La auditoría de UX también simplificó el cierre: menos fricción para probar y un CTA más claro para equipos que necesitan asesoría comercial.</p>
            </div>
            <div class="cta-band-actions">
                <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                    @csrf
                    <button class="btn btn-demo" type="submit">{{ $demoCtaLabel }}</button>
                </form>
                <button class="btn btn-wa btn-quote-trigger"
                        type="button"
                        data-open-quote-modal
                        data-quote-source="cta_band"
                        aria-controls="quote-request-modal">
                    <span class="quote-cta-icon-badge" aria-hidden="true">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                            <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>Solicita tu cotización</span>
                </button>
                <a href="{{ $contactHref }}" class="btn btn-outline">Ir a contacto</a>
            </div>
        </section>
        @endif

        @if ($showAboutSection)
        <section id="nosotros" class="section about-section">
            <div class="about-hero-band reveal" @if ($aboutHeroImage !== '') style="--about-hero-image: url('{{ $aboutHeroImage }}');" @endif>
                <div class="shell about-hero-content">
                    <p class="about-hero-kicker">Sobre nosotros</p>
                    <h2 class="about-hero-title">FlexJok</h2>
                    <p class="about-hero-breadcrumb">Inicio / Sobre nosotros</p>
                </div>
            </div>

            <div class="shell about-story">
                <article class="about-story-copy reveal">
                    <h3 class="about-story-title">Tecnología diseñada para aumentar la rentabilidad de tu gimnasio</h3>
                    <p class="about-story-lead">En FlexJok desarrollamos soluciones digitales enfocadas en un objetivo claro: ayudar a los gimnasios a aumentar ingresos, reducir pérdidas y operar con mayor eficiencia.</p>
                    <ul class="about-highlights">
                        <li>Membresías vencidas que no se renuevan a tiempo.</li>
                        <li>Falta de control en caja diaria.</li>
                        <li>Procesos manuales que generan errores.</li>
                        <li>Dificultad para medir crecimiento real.</li>
                        <li>Falta de indicadores claros para tomar decisiones.</li>
                    </ul>
                    <p class="about-story-lead">Nuestro enfoque no es solo administrativo. Es estratégico.</p>
                </article>

                <aside class="about-story-media reveal">
                    @if ($aboutStoryImage !== '')
                        <img src="{{ $aboutStoryImage }}" alt="Equipo FlexJok">
                    @else
                        <div class="about-story-media-placeholder">Sube una imagen principal para la sección Sobre nosotros desde SuperAdmin.</div>
                    @endif
                    <div class="about-year-badge">
                        <span class="about-year-value">2025</span>
                        <span class="about-year-label">Desde</span>
                    </div>
                </aside>
            </div>

            <div class="shell about-proof reveal">
                <p class="about-proof-kicker">Fundador</p>
                <p class="about-proof-quote">"Con una visión orientada a la automatización comercial y el crecimiento empresarial, desarrolló GymSystem para transformar la gestión tradicional de gimnasios en un modelo más rentable, organizado y escalable. Cada función fue diseñada para mejorar el flujo de ingresos y facilitar la toma de decisiones."</p>
                <div class="about-proof-author">
                    David Israel Quintana Tapia
                    <span>Fundador de FlexJok y creador de GymSystem</span>
                </div>
            </div>

            <div class="shell about-story">
                <article class="about-story-copy reveal">
                    <h3 class="about-story-title">Cómo ayudamos a tu gimnasio a generar más ingresos</h3>
                    <p class="about-story-lead">Implementamos herramientas que impactan directamente en la rentabilidad:</p>
                    <ul class="about-highlights is-checks">
                        <li>Control automatizado de membresías y vencimientos.</li>
                        <li>Recordatorios estratégicos para renovaciones.</li>
                        <li>Seguimiento de pagos en tiempo real.</li>
                        <li>Reportes financieros claros y detallados.</li>
                        <li>Control de asistencia para medir retención.</li>
                        <li>Gestión multi-sucursal centralizada.</li>
                    </ul>
                    <p class="about-story-lead">Un gimnasio que mide, controla y automatiza es un gimnasio que crece.</p>
                </article>
                <article class="about-story-copy reveal">
                    <h3 class="about-story-title">Misión, visión y compromiso</h3>
                    <p class="about-story-lead">Nuestra misión es optimizar la operación interna de los gimnasios para que el propietario pueda enfocarse en captar más clientes, fidelizar miembros y escalar su negocio.</p>
                    <p class="about-story-lead">Nuestra visión es convertir GymSystem en la plataforma de gestión más confiable para gimnasios en Latinoamérica.</p>
                    <ul class="about-highlights">
                        <li>Eficiencia operativa.</li>
                        <li>Control financiero claro.</li>
                        <li>Crecimiento sostenible.</li>
                    </ul>
                    <p class="about-story-lead">Porque un sistema no debe ser un gasto. Debe ser una inversión que genere retorno.</p>
                </article>
            </div>

            <div class="shell about-team reveal">
                <h3 class="about-team-title">Nuestro equipo</h3>
                <p class="about-team-subtitle">Combinamos visión comercial, operación fitness y desarrollo de software para construir una plataforma que realmente ayuda a crecer a los gimnasios.</p>
                <div class="about-team-grid">
                    <article class="about-team-card">
                        <div class="about-team-image-wrap">
                            @if ($aboutTeamImage1 !== '')
                                <img src="{{ $aboutTeamImage1 }}" alt="Equipo FlexJok - Estrategia comercial">
                            @else
                                <div class="about-team-placeholder">Imagen de equipo</div>
                            @endif
                        </div>
                        <div class="about-team-info">
                            <span class="about-team-role">Dirección</span>
                            <h4 class="about-team-name">David Israel Quintana Tapia</h4>
                            <p class="about-team-note">Estrategia comercial, visión de producto y crecimiento.</p>
                        </div>
                    </article>
                    <article class="about-team-card">
                        <div class="about-team-image-wrap">
                            @if ($aboutTeamImage2 !== '')
                                <img src="{{ $aboutTeamImage2 }}" alt="Equipo FlexJok - Operaciones">
                            @else
                                <div class="about-team-placeholder">Imagen de operaciones</div>
                            @endif
                        </div>
                        <div class="about-team-info">
                            <span class="about-team-role">Operación</span>
                            <h4 class="about-team-name">Equipo FlexJok</h4>
                            <p class="about-team-note">Implementación, acompañamiento y mejora continua para gimnasios.</p>
                        </div>
                    </article>
                    <article class="about-team-card">
                        <div class="about-team-image-wrap">
                            @if ($aboutTeamImage3 !== '')
                                <img src="{{ $aboutTeamImage3 }}" alt="Equipo FlexJok - Tecnología">
                            @else
                                <div class="about-team-placeholder">Imagen de tecnología</div>
                            @endif
                        </div>
                        <div class="about-team-info">
                            <span class="about-team-role">Tecnología</span>
                            <h4 class="about-team-name">Desarrollo GymSystem</h4>
                            <p class="about-team-note">Automatización, reportes e inteligencia operativa para decisiones rápidas.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
        @endif

        @if ($showContactSection)
        @php
            $landingContactErrors = $errors->getBag('landingContact');
            $contactModalMessage = '';
            $contactModalType = 'success';
            if (session('contact_status')) {
                $contactModalMessage = (string) session('contact_status');
            } elseif ($landingContactErrors->any()) {
                $contactModalMessage = (string) $landingContactErrors->first();
                $contactModalType = 'error';
            }
        @endphp
        <section id="contacto" class="shell section">
            <header class="heading reveal">
                <small>Contáctanos</small>
                <h2>Hablemos de tu gimnasio</h2>
                <p>Envíanos tus datos y te respondemos por WhatsApp o correo con una propuesta clara para implementar el sistema.</p>
            </header>
            <div class="contact-shell reveal">
                <div class="contact-info-grid">
                    <article class="contact-info-card">
                        <span class="contact-icon contact-icon--map" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                        </span>
                        <p>Atención remota para gimnasios en Ecuador y Latam.</p>
                    </article>
                    <article class="contact-info-card">
                        <span class="contact-icon contact-icon--mail" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="6" width="18" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m4 8 8 5 8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <p>{{ $footerContactEmail }}</p>
                    </article>
                    <article class="contact-info-card">
                        <span class="contact-icon contact-icon--wa" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M6 19.2 7 15.8a8 8 0 1 1 3.1 2.8L6 19.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="M10.4 9.6c-.2-.4-.5-.4-.7-.4h-.6c-.2 0-.5 0-.7.2-.3.3-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.7 2.8 4.2 3.7 2 .8 2.4.7 2.8.6.4 0 1.3-.5 1.5-1 .2-.5.2-1 .1-1s-.4-.2-.8-.4-1.3-.7-1.5-.8c-.2-.1-.4-.2-.6.2-.2.3-.6.8-.7 1-.1.2-.3.2-.5.1a6.2 6.2 0 0 1-1.8-1.1 7.1 7.1 0 0 1-1.3-1.7c-.1-.2 0-.4.1-.5l.5-.6c.2-.2.2-.4.3-.5 0-.2 0-.4 0-.5l-.4-1Z" fill="currentColor" stroke="none"/>
                            </svg>
                        </span>
                        <p>Soporte comercial por WhatsApp</p>
                    </article>
                </div>

                <form id="landing-contact-form"
                      class="contact-form-grid"
                      method="POST"
                      action="{{ route('landing.contact.store') }}">
                    @csrf
                    <label>
                        Nombre
                        <input type="text" class="contact-input" name="first_name" value="{{ old('first_name') }}" required>
                    </label>
                    <label>
                        Apellido
                        <input type="text" class="contact-input" name="last_name" value="{{ old('last_name') }}" required>
                    </label>
                    <label class="full">
                        Correo electrónico
                        <input type="email" class="contact-input" name="email" value="{{ old('email') }}" required>
                    </label>
                    <label class="full">
                        Mensaje
                        <textarea class="contact-input" name="message" required>{{ old('message') }}</textarea>
                    </label>
                    <div class="contact-submit full">
                        <button type="submit" class="btn btn-demo">Enviar mensaje</button>
                    </div>
                </form>
            </div>
        </section>

        <div id="contact-status-backdrop" class="contact-status-backdrop {{ $contactModalMessage !== '' ? 'is-open' : '' }}"></div>
        <div id="contact-status-modal"
             class="contact-status-modal {{ $contactModalMessage !== '' ? 'is-open' : '' }} {{ $contactModalType === 'error' ? 'is-error' : '' }}"
             data-variant="{{ $contactModalType }}"
             role="dialog"
             aria-modal="true"
             aria-labelledby="contact-status-title">
            <h4 id="contact-status-title">{{ $contactModalType === 'error' ? 'Revisa tus datos' : 'Mensaje enviado' }}</h4>
            <p>{{ $contactModalMessage !== '' ? $contactModalMessage : 'Tu mensaje fue procesado.' }}</p>
            <div class="contact-status-actions">
                <button id="contact-status-close" type="button" class="contact-status-close">Cerrar</button>
            </div>
        </div>
        @endif

        @if ($showLegalSection)
        @php
            $legalPageConfig = match ($pageMode) {
                'privacy' => [
                    'section_id' => 'legal-privacy',
                    'small' => 'Política de privacidad',
                    'title' => 'Cómo tratamos y protegemos tus datos',
                    'lead' => 'Este documento explica qué datos usamos, para qué los usamos y cómo puedes solicitar cambios o eliminación.',
                    'document_key' => 'privacy_policy',
                    'document_title' => 'Política de privacidad',
                    'blocks' => [
                        [
                            'title' => '1) Datos que recopilamos',
                            'text' => 'Recopilamos datos de contacto y datos operativos estrictamente necesarios para brindar soporte comercial y técnico.',
                            'points' => [
                                'Nombre, correo y teléfono cuando nos escribes.',
                                'Datos técnicos mínimos como IP y navegador para seguridad.',
                                'No vendemos datos personales a terceros.',
                            ],
                        ],
                        [
                            'title' => '2) Uso de la información',
                            'text' => 'Usamos la información para responder solicitudes, mejorar el servicio y mantener trazabilidad legal.',
                            'points' => [
                                'Atención comercial y soporte operativo.',
                                'Prevención de fraude y abuso de la plataforma.',
                                'Cumplimiento de obligaciones legales aplicables.',
                            ],
                        ],
                        [
                            'title' => '3) Seguridad y derechos',
                            'text' => 'Mantenemos controles de acceso y registro de eventos. Puedes solicitar acceso, corrección o eliminación de tus datos.',
                            'points' => [
                                'Control de acceso por roles y contexto.',
                                'Registros de aceptación con fecha y versión legal.',
                                'La aceptación digital también se solicita en el primer ingreso autenticado al panel.',
                                'Solicitudes por correo a '.$footerContactEmail.'.',
                            ],
                        ],
                    ],
                ],
                'service' => [
                    'section_id' => 'legal-service',
                    'small' => 'Condiciones de servicio',
                    'title' => 'Reglas de uso del sistema GymSystem',
                    'lead' => 'Estas condiciones regulan el uso de la plataforma, responsabilidades de cada parte y límites del servicio.',
                    'document_key' => 'service_terms',
                    'document_title' => 'Condiciones de servicio',
                    'blocks' => [
                        [
                            'title' => '1) Alcance del servicio',
                            'text' => 'GymSystem ofrece herramientas operativas para gimnasios, sujetas a disponibilidad técnica y mantenimiento planificado.',
                            'points' => [
                                'Funcionalidades según el plan contratado.',
                                'Actualizaciones evolutivas y correctivas.',
                                'Canales de soporte definidos por el proveedor.',
                            ],
                        ],
                        [
                            'title' => '2) Uso correcto y responsabilidades',
                            'text' => 'El cliente es responsable de la veracidad de sus datos, control de accesos internos y uso legal de la plataforma.',
                            'points' => [
                                'No compartir credenciales con terceros no autorizados.',
                                'Cumplir leyes locales en tratamiento de datos.',
                                'Evitar actividades que afecten estabilidad o seguridad.',
                            ],
                        ],
                        [
                            'title' => '3) Disponibilidad y limitaciones',
                            'text' => 'Se aplican ventanas de mantenimiento y límites razonables de uso. Eventos de terceros pueden afectar disponibilidad.',
                            'points' => [
                                'Mantenimientos preventivos programados.',
                                'No se garantiza continuidad ante fuerza mayor.',
                                'En el primer inicio de sesión se exige aceptar condiciones legales vigentes.',
                                'Suspensión temporal en caso de uso indebido grave.',
                            ],
                        ],
                    ],
                ],
                default => [
                    'section_id' => 'legal-commercial',
                    'small' => 'Términos comerciales',
                    'title' => 'Condiciones de planes, pagos y renovaciones',
                    'lead' => 'Este documento define precios, renovaciones, suspensiones y lineamientos de facturación del servicio.',
                    'document_key' => 'commercial_terms',
                    'document_title' => 'Términos comerciales',
                    'blocks' => [
                        [
                            'title' => '1) Planes y pagos',
                            'text' => 'El servicio se activa según el plan vigente y se mantiene activo mientras los pagos se encuentren al día.',
                            'points' => [
                                'Precios y períodos según plan acordado.',
                                'Pagos por los medios habilitados por el proveedor.',
                                'Cambios de plan sujetos a confirmación comercial.',
                            ],
                        ],
                        [
                            'title' => '2) Renovación, gracia y suspensión',
                            'text' => 'La renovación puede incluir período de gracia. Si no hay pago confirmado, el servicio puede suspenderse.',
                            'points' => [
                                'Notificaciones previas de vencimiento.',
                                'Período de gracia configurado por política comercial.',
                                'Reactivación sujeta a regularización de pago.',
                            ],
                        ],
                        [
                            'title' => '3) Reembolsos y facturación',
                            'text' => 'Las condiciones de reembolso y facturación se rigen por el plan contratado y normativa local aplicable.',
                            'points' => [
                                'Facturación de acuerdo con datos proporcionados.',
                                'Reembolsos evaluados caso por caso.',
                                'Cada aceptación legal genera evidencia digital para respaldo administrativo.',
                                'Términos pueden actualizarse con aviso previo.',
                            ],
                        ],
                    ],
                ],
            };
        @endphp
        <section id="{{ $legalPageConfig['section_id'] }}" class="shell section">
            <header class="heading reveal">
                <small>{{ $legalPageConfig['small'] }}</small>
                <h2>{{ $legalPageConfig['title'] }}</h2>
                <p>{{ $legalPageConfig['lead'] }}</p>
            </header>

            <div class="legal-shell reveal">
                <div class="legal-grid">
                    <article class="legal-card">
                        @foreach ($legalPageConfig['blocks'] as $block)
                            <div class="legal-block">
                                <h3>{{ $block['title'] }}</h3>
                                <p>{{ $block['text'] }}</p>
                                <ul class="legal-points">
                                    @foreach ($block['points'] as $point)
                                        <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </article>
                </div>
            </div>
        </section>
        @endif

    </main>

    <footer class="footer">
        <div class="shell footer-panel">
            <div class="footer-grid">
            <section class="footer-brand">
                @if ($brandLogoUrl !== '')
                    <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="footer-brand-logo">
                @else
                    <h4>{{ $brandName }}</h4>
                @endif
                <p class="footer-lead">{{ $footerText }}</p>
            </section>
            <section>
                <h4 class="footer-neon-title">Compañía</h4>
                <ul>
                    <li><a href="{{ $inicioHref }}">Inicio</a></li>
                    <li><a href="{{ $featuresHref }}">Servicios</a></li>
                    <li><a href="{{ $pricingHref }}">Precios</a></li>
                    <li><a href="{{ $faqHref }}">Preguntas</a></li>
                    <li><a href="{{ $aboutHref }}">Nosotros</a></li>
                    <li><a href="{{ $contactHref }}">Contáctanos</a></li>
                </ul>
            </section>
            <section>
                <h4 class="footer-neon-title">Legal</h4>
                <ul>
                    <li><a href="{{ $privacyHref }}">Política de privacidad</a></li>
                    <li><a href="{{ $serviceTermsHref }}">Condiciones de servicio</a></li>
                    <li><a href="{{ $commercialTermsHref }}">Términos comerciales</a></li>
                </ul>
            </section>
            <section>
                <h4 class="footer-neon-title">Contacto</h4>
                <ul>
                    <li><a href="{{ $content['whatsapp_url'] }}" target="_blank" rel="noreferrer">WhatsApp comercial</a></li>
                    <li><a class="footer-contact-email" href="mailto:{{ $footerContactEmail }}">{{ $footerContactEmail }}</a></li>
                </ul>
            </section>
            </div>
            <div class="copy">{{ $footerText }}</div>
        </div>
    </footer>

    <div id="quote-request-backdrop" class="quote-modal-backdrop {{ $quoteModalOpen ? 'is-open' : '' }}"></div>
    <div id="quote-request-modal"
         class="quote-modal {{ $quoteModalOpen ? 'is-open' : '' }}"
         role="dialog"
         aria-modal="true"
         aria-labelledby="quote-request-title">
        <button id="quote-request-close" type="button" class="quote-modal-close" aria-label="Cerrar formulario de cotización">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5 5 15 15M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="quote-modal-shell">
            <section class="quote-modal-side">
                <div>
                    <p class="quote-modal-kicker">Cotización personalizada</p>
                    <h3 class="quote-modal-title">Completa el formulario y recibe una propuesta clara para tu gimnasio.</h3>
                    <p class="quote-modal-copy">Cuéntanos tu contexto operativo y te ayudamos a elegir el plan correcto para implementar GymSystem sin fricciones.</p>
                </div>

                <div class="quote-modal-visual" aria-hidden="true">
                    <div class="quote-modal-stat">
                        <span>Respuesta comercial</span>
                        <strong>Te contactamos por correo o teléfono.</strong>
                    </div>

                    <div class="quote-modal-bubble is-a">
                        @if ($aboutTeamImage1 !== '')
                            <img src="{{ $aboutTeamImage1 }}" alt="">
                        @else
                            <span class="quote-modal-bubble-fallback">{{ mb_substr($brandInitials, 0, 1) }}</span>
                        @endif
                    </div>

                    <div class="quote-modal-bubble is-b">
                        @if ($aboutTeamImage3 !== '')
                            <img src="{{ $aboutTeamImage3 }}" alt="">
                        @else
                            <span class="quote-modal-bubble-fallback">{{ mb_substr($brandInitials, 0, 1) }}</span>
                        @endif
                    </div>

                    <div class="quote-modal-main-photo">
                        @if ($aboutTeamImage2 !== '')
                            <img src="{{ $aboutTeamImage2 }}" alt="">
                        @else
                            <span class="quote-modal-main-photo-fallback">{{ $brandInitials }}</span>
                        @endif
                    </div>

                    <div class="quote-modal-bubble is-c">
                        @if ($heroSlides[0] ?? false)
                            <img src="{{ $heroSlides[0] }}" alt="">
                        @else
                            <span class="quote-modal-bubble-fallback">{{ mb_substr($brandInitials, 0, 1) }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="quote-modal-form-panel">
                <header class="quote-form-header">
                    <p class="quote-modal-kicker">Solicita tu cotización</p>
                    <h3 id="quote-request-title">Queremos entender tu operación actual</h3>
                    <p>Comparte tus datos y el tamaño de tu equipo para prepararte una cotización ajustada a tu gimnasio.</p>
                </header>

                @if ($quoteModalMessage !== '')
                    <div class="quote-form-alert {{ $quoteModalType === 'error' ? 'is-error' : 'is-success' }}">
                        {{ $quoteModalMessage }}
                    </div>
                @endif

                <div id="quote-plan-pill" class="quote-plan-pill {{ $quoteSelectedPlanLabel !== '' ? 'is-visible' : '' }}">
                    <span>Plan de interes:</span>
                    <strong data-quote-plan-label>{{ $quoteSelectedPlanLabel !== '' ? $quoteSelectedPlanLabel : 'General' }}</strong>
                </div>

                <form id="landing-quote-form" method="POST" action="{{ route('landing.quote.store') }}">
                    @csrf
                    <input type="hidden" name="quote_requested_plan" value="{{ old('quote_requested_plan') }}" data-quote-plan-input>
                    <input type="hidden" name="quote_source" value="{{ old('quote_source', 'landing_'.$pageMode) }}" data-quote-source-input>

                    <div class="quote-form-grid">
                        <label class="quote-form-field">
                            Nombre*
                            <input type="text" class="contact-input" name="quote_first_name" value="{{ old('quote_first_name') }}" placeholder="Nombre" required>
                            @error('quote_first_name', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="quote-form-field">
                            Apellido*
                            <input type="text" class="contact-input" name="quote_last_name" value="{{ old('quote_last_name') }}" placeholder="Apellido" required>
                            @error('quote_last_name', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="quote-form-field">
                            Teléfono de contacto*
                            <span class="quote-form-inline">
                                <select class="contact-input quote-form-prefix" name="quote_phone_country_code" data-quote-prefix-select required>
                                    @foreach ($quotePhonePrefixes as $prefix)
                                        <option value="{{ $prefix }}" @selected(old('quote_phone_country_code', '+593') === $prefix)>{{ $prefix }}</option>
                                    @endforeach
                                </select>
                                <input type="tel" class="contact-input" name="quote_phone_number" value="{{ old('quote_phone_number') }}" placeholder="987654321" required>
                            </span>
                            @error('quote_phone_country_code', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                            @error('quote_phone_number', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="quote-form-field">
                            Correo electrónico*
                            <input type="email" class="contact-input" name="quote_email" value="{{ old('quote_email') }}" placeholder="correo@tugym.com" required>
                            @error('quote_email', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="quote-form-field quote-form-field--full">
                            País de residencia*
                            <select class="contact-input" name="quote_country" data-quote-country-select required>
                                <option value="">Selecciona tu país</option>
                                @foreach ($quoteCountryPrefixes as $country => $prefix)
                                    <option value="{{ $country }}"
                                            data-phone-prefix="{{ $prefix ?? '' }}"
                                            @selected(old('quote_country') === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('quote_country', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="quote-form-field quote-form-field--full">
                            ¿Cuántos profesionales atienden en tu gimnasio?*
                            <input type="number" class="contact-input" name="quote_professionals_count" value="{{ old('quote_professionals_count') }}" min="1" max="5000" placeholder="Ej: 6" required>
                            <span class="quote-form-help">Considera recepción, entrenadores, administración y personal operativo.</span>
                            @error('quote_professionals_count', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="quote-form-field quote-form-field--full">
                            Comentarios adicionales
                            <textarea class="contact-input" name="quote_notes" placeholder="Opcional: cuéntanos si manejas varias sedes, caja, recepción o procesos especiales.">{{ old('quote_notes') }}</textarea>
                            @error('quote_notes', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="quote-form-checkbox quote-form-field--full">
                            <label>
                                <input type="checkbox" name="quote_privacy_accepted" value="1" @checked(old('quote_privacy_accepted'))>
                                <span>Acepto el tratamiento de mis datos para recibir mi cotización y seguimiento comercial.</span>
                            </label>
                            <p class="quote-form-legal">Usaremos esta información solo para responder tu solicitud y coordinar una propuesta personalizada.</p>
                            @error('quote_privacy_accepted', 'landingQuote')
                                <span class="quote-form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-demo quote-form-submit">Enviar solicitud</button>
                </form>
            </section>
        </div>
    </div>

    <div id="demo-limit-backdrop" class="modal-backdrop {{ $demoLimitSeconds > 0 ? 'is-open' : '' }}"></div>

    <div id="demo-limit-modal"
         class="modal {{ $demoLimitSeconds > 0 ? 'is-open' : '' }}"
         role="dialog"
         aria-modal="true"
         aria-labelledby="demo-limit-title"
         data-retry-seconds="{{ $demoLimitSeconds }}">
        <h4 id="demo-limit-title">Limite de demos alcanzado</h4>
        <p>Alcanzaste el máximo de 3 intentos por hora. Debes esperar antes de volver a solicitar tu demo.</p>
        <div class="modal-time">Tiempo restante: <span id="demo-limit-countdown">--:--</span></div>
        <div class="modal-actions">
            <button id="demo-limit-close" type="button" class="modal-close">Entendido</button>
        </div>
    </div>

    <script>
        (function () {
            const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const enablePerfLite = function () {
                if (!document.body.classList.contains('perf-lite')) {
                    document.body.classList.add('perf-lite');
                    window.dispatchEvent(new Event('codex:perf-lite'));
                }
            };

            if (!prefersReducedMotion && 'requestAnimationFrame' in window) {
                let frameCount = 0;
                let startAt = 0;
                let lastAt = 0;

                const sampleFps = function (timestamp) {
                    if (!startAt) {
                        startAt = timestamp;
                        lastAt = timestamp;
                    }

                    const frameDelta = timestamp - lastAt;
                    lastAt = timestamp;
                    if (frameDelta < 250) {
                        frameCount += 1;
                    }

                    const elapsed = timestamp - startAt;
                    if (elapsed < 1600) {
                        window.requestAnimationFrame(sampleFps);
                        return;
                    }

                    const fps = frameCount / (elapsed / 1000);
                    if (fps < 50) {
                        enablePerfLite();
                    }
                };

                window.requestAnimationFrame(sampleFps);
            } else if (prefersReducedMotion) {
                enablePerfLite();
            }

            const revealItems = Array.from(document.querySelectorAll('.reveal'));
            if (revealItems.length > 0) {
                if (!('IntersectionObserver' in window) || document.body.classList.contains('perf-lite')) {
                    revealItems.forEach(function (item) {
                        item.classList.add('is-visible');
                    });
                } else {
                    const observer = new IntersectionObserver(function (entries, instance) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) {
                                return;
                            }

                            entry.target.classList.add('is-visible');
                            instance.unobserve(entry.target);
                        });
                    }, {
                        threshold: 0.14,
                        rootMargin: '0px 0px -30px 0px'
                    });

                    revealItems.forEach(function (item) {
                        observer.observe(item);
                    });
                }
            }

            const homeScrollBackground = document.querySelector('[data-home-scroll-bg]');
            if (homeScrollBackground) {
                let images = [];
                try {
                    images = JSON.parse(homeScrollBackground.getAttribute('data-bg-images') || '[]');
                } catch (_error) {
                    images = [];
                }
                images = images.filter(function (item) {
                    return typeof item === 'string' && item.trim() !== '';
                });

                const layers = Array.from(homeScrollBackground.querySelectorAll('[data-home-bg-layer]'));
                if (images.length > 1 && layers.length >= 2) {
                    let activeLayerIndex = 0;
                    let activeImageIndex = 0;
                    let frameId = 0;

                    const sectionIds = ['#inicio', '#features', '#pricing', '#faq', '#contacto'];
                    const trackedSections = sectionIds
                        .map(function (selector) {
                            return document.querySelector(selector);
                        })
                        .filter(Boolean);

                    const toCssBackground = function (value) {
                        return 'url("' + String(value).replace(/"/g, '\\"') + '")';
                    };

                    const targetIndexFromViewport = function () {
                        if (trackedSections.length > 0) {
                            const marker = window.scrollY + (window.innerHeight * 0.38);
                            let sectionIndex = 0;
                            trackedSections.forEach(function (section, idx) {
                                if (marker >= section.offsetTop) {
                                    sectionIndex = idx;
                                }
                            });
                            return Math.min(images.length - 1, sectionIndex);
                        }

                        const maxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
                        const progress = Math.max(0, Math.min(1, window.scrollY / maxScroll));
                        return Math.min(images.length - 1, Math.floor(progress * images.length));
                    };

                    const switchBackground = function (nextImageIndex) {
                        if (nextImageIndex === activeImageIndex) {
                            return;
                        }
                        const nextLayerIndex = activeLayerIndex === 0 ? 1 : 0;
                        const activeLayer = layers[activeLayerIndex];
                        const nextLayer = layers[nextLayerIndex];
                        if (!activeLayer || !nextLayer) {
                            return;
                        }

                        nextLayer.style.backgroundImage = toCssBackground(images[nextImageIndex]);
                        nextLayer.classList.add('is-active');
                        activeLayer.classList.remove('is-active');

                        activeLayerIndex = nextLayerIndex;
                        activeImageIndex = nextImageIndex;
                    };

                    const syncScrollBackground = function () {
                        frameId = 0;
                        switchBackground(targetIndexFromViewport());
                    };

                    const requestSyncScrollBackground = function () {
                        if (frameId) {
                            return;
                        }
                        frameId = window.requestAnimationFrame(syncScrollBackground);
                    };

                    layers[0].style.backgroundImage = toCssBackground(images[0]);
                    requestSyncScrollBackground();
                    window.addEventListener('scroll', requestSyncScrollBackground, { passive: true });
                    window.addEventListener('resize', requestSyncScrollBackground);
                    window.addEventListener('load', requestSyncScrollBackground);
                }
            }

            const navLinks = Array.from(document.querySelectorAll('.menu-links a, .mobile-nav-links a'));
            if (navLinks.length > 0) {
                const activeStorageKey = 'landing.nav.active';
                const homePathname = @json(parse_url(route('landing'), PHP_URL_PATH) ?: '/');

                const normalizePath = function (value) {
                    const normalized = String(value || '').replace(/\/+$/, '');
                    return normalized === '' ? '/' : normalized;
                };

                const normalizeHash = function (value) {
                    const hash = String(value || '').trim();
                    if (hash === '' || hash === '#') {
                        return '';
                    }
                    return hash.startsWith('#') ? hash.toLowerCase() : ('#' + hash.toLowerCase());
                };

                const currentPath = function () {
                    return normalizePath(window.location.pathname);
                };

                const sectionCandidates = [];
                const linkMap = new Map();

                navLinks.forEach(function (link) {
                    const href = link.getAttribute('href') || '';
                    if (href.trim() === '') {
                        return;
                    }

                    let url;
                    try {
                        url = new URL(href, window.location.origin);
                    } catch (_error) {
                        return;
                    }

                    const key = normalizePath(url.pathname) + normalizeHash(url.hash);
                    const info = {
                        key: key,
                        path: normalizePath(url.pathname),
                        hash: normalizeHash(url.hash),
                        element: link,
                    };

                    if (!linkMap.has(key)) {
                        linkMap.set(key, []);
                    }
                    linkMap.get(key).push(info);

                    if (info.path === normalizePath(homePathname) && info.hash !== '') {
                        const sectionEl = document.querySelector(info.hash);
                        if (sectionEl) {
                            sectionCandidates.push({
                                key: key,
                                section: sectionEl,
                            });
                        }
                    }

                    link.addEventListener('click', function () {
                        try {
                            window.localStorage.setItem(activeStorageKey, key);
                        } catch (_error) {
                            // Keep silent if storage is unavailable.
                        }
                        setActiveByKey(key);
                    });
                });

                const setActiveByKey = function (key) {
                    const activeKey = String(key || '');
                    navLinks.forEach(function (link) {
                        let linkKey = '';
                        try {
                            const url = new URL(link.getAttribute('href') || '', window.location.origin);
                            linkKey = normalizePath(url.pathname) + normalizeHash(url.hash);
                        } catch (_error) {
                            linkKey = '';
                        }
                        const isActive = activeKey !== '' && linkKey === activeKey;
                        link.classList.toggle('is-active', isActive);
                        if (isActive) {
                            link.setAttribute('aria-current', 'page');
                        } else {
                            link.removeAttribute('aria-current');
                        }
                    });
                };

                const keyFromLocation = function () {
                    const path = currentPath();
                    const hash = normalizeHash(window.location.hash);
                    const exactKey = path + hash;
                    if (hash !== '' && linkMap.has(exactKey)) {
                        return exactKey;
                    }

                    const pathOnlyMatch = Array.from(linkMap.keys()).find(function (key) {
                        return key === path;
                    });
                    if (pathOnlyMatch) {
                        return pathOnlyMatch;
                    }

                    return '';
                };

                const keyFromVisibleSection = function () {
                    if (currentPath() !== normalizePath(homePathname) || sectionCandidates.length === 0) {
                        return '';
                    }

                    const topOffset = Math.max(120, Math.floor(window.innerHeight * 0.18));
                    let active = '';
                    let bestDistance = Number.POSITIVE_INFINITY;

                    sectionCandidates.forEach(function (candidate) {
                        const rect = candidate.section.getBoundingClientRect();
                        const isInRange = rect.top <= topOffset && rect.bottom >= (topOffset * 0.35);
                        if (isInRange) {
                            const distance = Math.abs(rect.top - topOffset);
                            if (distance < bestDistance) {
                                bestDistance = distance;
                                active = candidate.key;
                            }
                        }
                    });

                    if (active !== '') {
                        return active;
                    }

                    if (window.scrollY < 64) {
                        return normalizePath(homePathname) + '#inicio';
                    }

                    return '';
                };

                const syncActiveNav = function () {
                    const sectionKey = keyFromVisibleSection();
                    if (sectionKey !== '') {
                        setActiveByKey(sectionKey);
                        try {
                            window.localStorage.setItem(activeStorageKey, sectionKey);
                        } catch (_error) {
                            // Keep silent if storage is unavailable.
                        }
                        return;
                    }

                    const locationKey = keyFromLocation();
                    if (locationKey !== '') {
                        setActiveByKey(locationKey);
                        return;
                    }

                    try {
                        const storedKey = window.localStorage.getItem(activeStorageKey) || '';
                        if (storedKey !== '' && linkMap.has(storedKey)) {
                            setActiveByKey(storedKey);
                        }
                    } catch (_error) {
                        // no-op
                    }
                };

                let pendingSync = 0;
                const requestSync = function () {
                    if (pendingSync) {
                        return;
                    }
                    pendingSync = window.requestAnimationFrame(function () {
                        pendingSync = 0;
                        syncActiveNav();
                    });
                };

                syncActiveNav();
                window.addEventListener('hashchange', syncActiveNav);
                window.addEventListener('scroll', requestSync, { passive: true });
                window.addEventListener('resize', requestSync);
            }

            const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
            const mobileNavPanel = document.querySelector('[data-mobile-nav-panel]');
            if (mobileMenuToggle && mobileNavPanel) {
                const setMobileMenuState = function (open) {
                    mobileMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    mobileNavPanel.classList.toggle('is-open', open);
                    if (open) {
                        mobileNavPanel.removeAttribute('hidden');
                    } else {
                        mobileNavPanel.setAttribute('hidden', 'hidden');
                    }
                };

                const closeMobileMenu = function () {
                    setMobileMenuState(false);
                };

                mobileMenuToggle.addEventListener('click', function () {
                    const isOpen = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
                    setMobileMenuState(!isOpen);
                });

                mobileNavPanel.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', closeMobileMenu);
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth > 820) {
                        closeMobileMenu();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeMobileMenu();
                    }
                });
            }

            const heroCarousel = document.querySelector('[data-hero-carousel]');
            if (heroCarousel) {
                const slides = Array.from(heroCarousel.querySelectorAll('[data-hero-slide]'));
                const dots = Array.from(heroCarousel.querySelectorAll('[data-hero-dot]'));
                const prevBtn = heroCarousel.querySelector('[data-hero-prev]');
                const nextBtn = heroCarousel.querySelector('[data-hero-next]');
                let activeIndex = 0;
                let timer = null;

                const activate = function (index) {
                    if (slides.length === 0) {
                        return;
                    }

                    activeIndex = (index + slides.length) % slides.length;
                    slides.forEach(function (slide, idx) {
                        slide.classList.toggle('is-active', idx === activeIndex);
                    });
                    dots.forEach(function (dot, idx) {
                        dot.classList.toggle('is-active', idx === activeIndex);
                    });
                };

                const start = function () {
                    if (slides.length <= 1) {
                        return;
                    }
                    if (timer) {
                        window.clearInterval(timer);
                    }

                    timer = window.setInterval(function () {
                        activate(activeIndex + 1);
                    }, 3600);
                };

                const stop = function () {
                    if (timer) {
                        window.clearInterval(timer);
                        timer = null;
                    }
                };

                if (prevBtn) {
                    prevBtn.addEventListener('click', function () {
                        activate(activeIndex - 1);
                        start();
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function () {
                        activate(activeIndex + 1);
                        start();
                    });
                }

                dots.forEach(function (dot) {
                    dot.addEventListener('click', function () {
                        const to = parseInt(dot.getAttribute('data-hero-dot') || '0', 10);
                        activate(to);
                        start();
                    });
                });

                heroCarousel.addEventListener('mouseenter', stop);
                heroCarousel.addEventListener('mouseleave', start);
                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        stop();
                    } else {
                        start();
                    }
                });
                activate(0);
                start();
            }

            const faqItems = Array.from(document.querySelectorAll('[data-faq-item]'));
            faqItems.forEach(function (item) {
                const button = item.querySelector('[data-faq-button]');
                if (!button) {
                    return;
                }

                button.addEventListener('click', function () {
                    const willOpen = !item.classList.contains('is-open');
                    faqItems.forEach(function (other) {
                        const otherButton = other.querySelector('[data-faq-button]');
                        other.classList.remove('is-open');
                        if (otherButton) {
                            otherButton.setAttribute('aria-expanded', 'false');
                        }
                    });

                    if (willOpen) {
                        item.classList.add('is-open');
                        button.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            const quoteModal = document.getElementById('quote-request-modal');
            const quoteBackdrop = document.getElementById('quote-request-backdrop');
            const quoteCloseButton = document.getElementById('quote-request-close');
            const quoteForm = document.getElementById('landing-quote-form');
            const quotePlanInput = quoteForm ? quoteForm.querySelector('[data-quote-plan-input]') : null;
            const quoteSourceInput = quoteForm ? quoteForm.querySelector('[data-quote-source-input]') : null;
            const quotePlanPill = document.getElementById('quote-plan-pill');
            const quotePlanLabel = quotePlanPill ? quotePlanPill.querySelector('[data-quote-plan-label]') : null;
            const quoteTriggers = Array.from(document.querySelectorAll('[data-open-quote-modal]'));
            const firstQuoteInput = quoteForm ? quoteForm.querySelector('input[name="quote_first_name"]') : null;
            const quoteCountrySelect = quoteForm ? quoteForm.querySelector('[data-quote-country-select]') : null;
            const quotePrefixSelect = quoteForm ? quoteForm.querySelector('[data-quote-prefix-select]') : null;

            const humanizeQuotePlan = function (value) {
                return String(value || '')
                    .trim()
                    .replace(/[_-]+/g, ' ')
                    .replace(/\s+/g, ' ')
                    .replace(/\b\w/g, function (character) {
                        return character.toUpperCase();
                    });
            };

            const syncQuotePlan = function (value) {
                if (!(quotePlanInput instanceof HTMLInputElement)) {
                    return;
                }

                const normalized = String(value || '').trim();
                quotePlanInput.value = normalized;

                if (!quotePlanPill || !quotePlanLabel) {
                    return;
                }

                if (normalized === '') {
                    quotePlanPill.classList.remove('is-visible');
                    quotePlanLabel.textContent = 'General';
                    return;
                }

                quotePlanPill.classList.add('is-visible');
                quotePlanLabel.textContent = humanizeQuotePlan(normalized);
            };

            const syncQuotePrefixFromCountry = function () {
                if (!(quoteCountrySelect instanceof HTMLSelectElement) || !(quotePrefixSelect instanceof HTMLSelectElement)) {
                    return;
                }

                const selectedOption = quoteCountrySelect.selectedOptions.length > 0
                    ? quoteCountrySelect.selectedOptions[0]
                    : null;
                const prefix = selectedOption ? String(selectedOption.getAttribute('data-phone-prefix') || '').trim() : '';

                if (prefix === '') {
                    return;
                }

                const matchingOption = Array.from(quotePrefixSelect.options).find(function (option) {
                    return option.value === prefix;
                });

                if (matchingOption) {
                    quotePrefixSelect.value = prefix;
                }
            };

            const openQuoteModal = function (options) {
                if (!quoteModal || !quoteBackdrop) {
                    return;
                }

                const plan = options && Object.prototype.hasOwnProperty.call(options, 'plan')
                    ? options.plan
                    : (quotePlanInput instanceof HTMLInputElement ? quotePlanInput.value : '');
                const source = options && Object.prototype.hasOwnProperty.call(options, 'source')
                    ? options.source
                    : '';

                syncQuotePlan(plan);

                if (quoteSourceInput instanceof HTMLInputElement && String(source || '').trim() !== '') {
                    quoteSourceInput.value = String(source).trim();
                }

                quoteModal.classList.add('is-open');
                quoteBackdrop.classList.add('is-open');
                document.body.classList.add('quote-modal-open');

                window.setTimeout(function () {
                    if (firstQuoteInput instanceof HTMLElement) {
                        firstQuoteInput.focus();
                    }
                }, 40);
            };

            const closeQuoteModal = function () {
                if (!quoteModal || !quoteBackdrop) {
                    return;
                }

                quoteModal.classList.remove('is-open');
                quoteBackdrop.classList.remove('is-open');
                document.body.classList.remove('quote-modal-open');
            };

            quoteTriggers.forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    openQuoteModal({
                        plan: trigger.getAttribute('data-quote-plan') || '',
                        source: trigger.getAttribute('data-quote-source') || '',
                    });
                });
            });

            if (quoteCloseButton && quoteBackdrop) {
                quoteCloseButton.addEventListener('click', closeQuoteModal);
                quoteBackdrop.addEventListener('click', closeQuoteModal);
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && quoteModal && quoteModal.classList.contains('is-open')) {
                    closeQuoteModal();
                }
            });

            if (quoteModal && quoteModal.classList.contains('is-open')) {
                document.body.classList.add('quote-modal-open');
                syncQuotePlan(quotePlanInput instanceof HTMLInputElement ? quotePlanInput.value : '');
            }

            if (quoteCountrySelect instanceof HTMLSelectElement && quotePrefixSelect instanceof HTMLSelectElement) {
                quoteCountrySelect.addEventListener('change', syncQuotePrefixFromCountry);
                if (String(quoteCountrySelect.value || '').trim() !== '') {
                    syncQuotePrefixFromCountry();
                }
            }

            if (quoteForm) {
                quoteForm.addEventListener('submit', function () {
                    const submitButton = quoteForm.querySelector('button[type="submit"]');
                    if (submitButton instanceof HTMLButtonElement) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Enviando solicitud...';
                    }
                });
            }

            const contactForm = document.getElementById('landing-contact-form');
            if (contactForm) {
                contactForm.addEventListener('submit', function (event) {
                    const submitButton = contactForm.querySelector('button[type="submit"]');
                    if (submitButton instanceof HTMLButtonElement) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'Enviando...';
                    }
                });
            }
            const contactStatusModal = document.getElementById('contact-status-modal');
            const contactStatusBackdrop = document.getElementById('contact-status-backdrop');
            const contactStatusClose = document.getElementById('contact-status-close');
            if (contactStatusModal && contactStatusBackdrop && contactStatusClose) {
                const closeContactStatus = function () {
                    contactStatusModal.classList.remove('is-open');
                    contactStatusBackdrop.classList.remove('is-open');
                };

                contactStatusClose.addEventListener('click', closeContactStatus);
                contactStatusBackdrop.addEventListener('click', closeContactStatus);
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        if (quoteModal && quoteModal.classList.contains('is-open')) {
                            closeQuoteModal();
                            return;
                        }

                        closeContactStatus();
                    }
                });

                if (contactStatusModal.classList.contains('is-open') && contactStatusModal.dataset.variant === 'success') {
                    window.setTimeout(closeContactStatus, 4200);
                }
            }

            const modal = document.getElementById('demo-limit-modal');
            const backdrop = document.getElementById('demo-limit-backdrop');
            const closeBtn = document.getElementById('demo-limit-close');
            const countdownEl = document.getElementById('demo-limit-countdown');
            if (!modal || !backdrop || !closeBtn || !countdownEl) {
                return;
            }

            let remainingSeconds = parseInt(modal.getAttribute('data-retry-seconds') || '0', 10);
            if (!Number.isFinite(remainingSeconds) || remainingSeconds <= 0) {
                return;
            }

            const formatSeconds = function (value) {
                const safe = Math.max(0, value);
                const hours = Math.floor(safe / 3600);
                const minutes = Math.floor((safe % 3600) / 60);
                const seconds = safe % 60;
                const mm = String(minutes).padStart(2, '0');
                const ss = String(seconds).padStart(2, '0');

                if (hours <= 0) {
                    return mm + ':' + ss;
                }

                return String(hours).padStart(2, '0') + ':' + mm + ':' + ss;
            };

            const render = function () {
                countdownEl.textContent = formatSeconds(remainingSeconds);
            };

            render();
            const timer = window.setInterval(function () {
                remainingSeconds -= 1;
                render();
                if (remainingSeconds <= 0) {
                    window.clearInterval(timer);
                }
            }, 1000);

            const closeModal = function () {
                modal.classList.remove('is-open');
                backdrop.classList.remove('is-open');
            };

            closeBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', closeModal);
        })();
    </script>
</body>
</html>



