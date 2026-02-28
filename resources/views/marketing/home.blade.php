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
    $heroTitleText = trim((string) preg_replace('/\bsin\s+friccion\b\.?/i', '', $heroTitleText));
    $heroSlides = [];
    for ($slide = 1; $slide <= 3; $slide++) {
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
            'title' => trim((string) ($content['section_2_title'] ?? 'Mas control, menos caos operativo')),
            'text' => trim((string) ($content['section_2_text'] ?? 'Convierte tu operacion diaria en procesos claros, medibles y faciles de ejecutar.')),
            'items' => [
                trim((string) ($content['section_2_item_1'] ?? 'Flujo de recepcion optimizado')),
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

    $publicPlanCards = collect($publicPlanCards ?? [])->filter(fn ($card) => is_array($card))->values();
    $formatPlanMoney = static function (mixed $value): string {
        $amount = (float) $value;
        return number_format($amount, 0, '.', '');
    };
    $resolvePlanWhatsappUrl = static function (string $planKey) use ($content): string {
        $normalized = strtolower(trim($planKey));
        return match ($normalized) {
            'basico' => (string) ($content['whatsapp_url_plan_basico'] ?? ($content['whatsapp_url'] ?? '#')),
            'profesional' => (string) ($content['whatsapp_url_plan_profesional'] ?? ($content['whatsapp_url'] ?? '#')),
            'premium' => (string) ($content['whatsapp_url_plan_premium'] ?? ($content['whatsapp_url'] ?? '#')),
            'sucursales' => (string) ($content['whatsapp_url_plan_sucursales'] ?? ($content['whatsapp_url'] ?? '#')),
            default => (string) ($content['whatsapp_url'] ?? '#'),
        };
    };
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#05080f">
    <title>{{ $brandName }} | {{ $pageTitleSuffix }}</title>
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
        .shell { width: min(1240px, calc(100% - 2rem)); margin: 0 auto; }
        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: -10%;
            pointer-events: none;
            z-index: 0;
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

        .main { padding-bottom: 4rem; position: relative; z-index: 2; }
        .flash-stack { margin-top: 1rem; display: grid; gap: .6rem; }
        .flash {
            border: 1px solid #2e5940; border-radius: .82rem;
            background: rgba(12, 20, 15, .9); color: #e8fff0;
            padding: .72rem .9rem; font-size: .9rem; line-height: 1.45;
        }
        .flash-error { border-color: #5e3a40; background: rgba(43, 20, 26, .75); color: #ffd9df; }
        .hero {
            display: grid; grid-template-columns: minmax(0, 1.02fr) minmax(0, .98fr);
            gap: 2rem; align-items: center; padding-top: 2.8rem;
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

        .hero-panel {
            border-radius: 1.2rem; border: 1px solid #2b5a40;
            background: linear-gradient(160deg, #09130d, #102018 55%, #0b1711);
            padding: 1rem; box-shadow: 0 28px 56px rgba(3, 8, 22, .52); overflow: hidden;
            display: grid; gap: .72rem;
        }
        .hero-media-frame {
            position: relative;
            border: 1px solid #2f5f43;
            border-radius: 1rem;
            min-height: 320px;
            background: #050b08;
            overflow: hidden;
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
            min-height: 320px;
            display: block;
            object-fit: cover;
            filter: saturate(1.08) contrast(1.03);
        }
        .hero-media-placeholder {
            min-height: 320px;
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
            border: 1px solid #2f5f43;
            border-radius: .88rem;
            background: linear-gradient(145deg, rgba(10, 22, 14, .96), rgba(8, 18, 12, .92));
            padding: .7rem .78rem;
            position: relative;
            overflow: hidden;
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
            gap: .48rem;
            padding: 0;
        }
        .brand-chip-logo {
            width: 22px;
            height: 22px;
            object-fit: contain;
            background: transparent;
            flex: 0 0 22px;
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
            margin-top: .5rem;
            font-size: .88rem;
            color: #b6c9be;
            line-height: 1.45;
        }
        .plan-discount strong {
            color: #9dffb6;
            font-size: .96rem;
            font-weight: 800;
        }
        .price-old {
            text-decoration: line-through;
            color: #8da99b;
            margin-right: .26rem;
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
        .plan .plan-cta > a.btn {
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
        .about-grid {
            margin-top: 1.1rem;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 1rem;
        }
        .about-card {
            border: 1px solid #2b5a40;
            border-radius: 1rem;
            background: linear-gradient(145deg, #0b1510, #0f1c15);
            padding: 1rem;
        }
        .about-card h3 {
            margin: 0;
            font-size: 1.32rem;
            color: #ecfff2;
        }
        .about-card p {
            margin: .72rem 0 0;
            color: #b4c8bc;
            line-height: 1.62;
        }
        .about-highlights {
            margin: .85rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .44rem;
            color: #d9f2e1;
        }
        .about-highlights li {
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }
        .about-highlights li::before {
            content: "";
            width: .5rem;
            height: .5rem;
            margin-top: .38rem;
            border-radius: 999px;
            flex: 0 0 .5rem;
            background: #47ff6f;
            box-shadow: 0 0 10px rgba(71, 255, 111, .82);
        }
        .about-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .62rem;
        }
        .about-metric {
            border: 1px solid #2d6043;
            border-radius: .8rem;
            background: rgba(8, 17, 12, .88);
            padding: .8rem .82rem;
        }
        .about-metric strong {
            display: block;
            font-size: 1.34rem;
            color: #9dffb6;
        }
        .about-metric span {
            display: block;
            margin-top: .25rem;
            color: #b7cfc0;
            font-size: .86rem;
            text-transform: uppercase;
            letter-spacing: .06em;
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
            .nav-actions .btn-outline { display: none; }
            .nav-actions .btn {
                min-height: 40px;
                font-size: .8rem;
                padding: .52rem .78rem;
            }
            .hero { grid-template-columns: 1fr; }
            .service-grid,
            .pricing-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-grid .service:last-child { grid-column: 1 / -1; }
            .footer-panel { padding: 1.5rem 1.2rem 1rem; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .hero-insight-grid { grid-template-columns: 1fr 1fr; }
            .about-grid { grid-template-columns: 1fr; }
            .contact-info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .contact-info-grid .contact-info-card:last-child { grid-column: 1 / -1; }
            .contact-form-grid { grid-template-columns: 1fr; }
            .legal-grid { grid-template-columns: 1fr; }
            .about-card,
            .contact-shell,
            .legal-shell { padding: .95rem; }
            .heading h2 { font-size: clamp(1.5rem, 4.1vw, 2.5rem); }
            .service-media { min-height: 188px; }
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
                width: 116px;
                height: 36px;
                transform: scale(1.18);
            }
            .menu-links,
            .nav-actions { display: none; }
            .mobile-menu-toggle { display: inline-flex; }
            .mobile-nav-actions .btn { min-height: 42px; }
            .hero { gap: 1.25rem; padding-top: 2rem; }
            .hero h1 { line-height: 1.08; }
            .hero p,
            .heading p { font-size: .98rem; }
            .hero-media-frame,
            .hero-slide-image,
            .hero-media-placeholder { min-height: 220px; }
            .hero-carousel-control { width: 32px; height: 32px; }
            .brands-track { animation-duration: 32s; }
            .brands-row { gap: 1.45rem; padding-right: 1.45rem; }
            .brand-chip { min-width: 0; }
            .brand-chip span { font-size: .83rem; }
            .service-grid,
            .pricing-grid,
            .footer-grid,
            .hero-insight-grid,
            .about-metrics,
            .about-grid,
            .contact-info-grid,
            .contact-form-grid,
            .legal-grid { grid-template-columns: 1fr; }
            .service-grid .service:last-child,
            .pricing-grid .plan:last-child,
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
                width: 104px;
                height: 34px;
                transform: scale(1.1);
            }
            .top-nav { padding: .58rem .64rem; }
            .mobile-nav-actions { grid-template-columns: 1fr; }
            .hero-actions { gap: .5rem; }
            .hero-actions .btn,
            .hero-actions .inline-form { width: 100%; }
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
<body>

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
                        aria-label="Abrir menu">
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
                    <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                        @csrf
                        <button class="btn btn-demo" type="submit">{{ $demoCtaLabel }}</button>
                    </form>
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
                    <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                        @csrf
                        <button class="btn btn-demo" type="submit">{{ $demoCtaLabel }}</button>
                    </form>
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
                    <a class="btn btn-wa" href="{{ $content['whatsapp_url'] }}" target="_blank" rel="noreferrer" aria-label="Hablar por WhatsApp">
                        <svg class="btn-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <path d="M27.1 4.8A13.88 13.88 0 0 0 16.01.2C8.37.2 2.16 6.42 2.16 14.05c0 2.45.64 4.84 1.86 6.95L2 31.8l11.1-2.9a13.8 13.8 0 0 0 6.88 1.86h.01c7.63 0 13.85-6.22 13.85-13.85 0-3.7-1.44-7.19-4.74-10.1Z" fill="#25D366"/>
                            <path d="M20.83 18.44c-.27-.13-1.58-.78-1.82-.86-.24-.09-.41-.13-.58.13-.17.27-.67.86-.82 1.04-.15.18-.31.2-.58.07-.27-.13-1.12-.41-2.13-1.31-.79-.7-1.32-1.56-1.48-1.83-.15-.27-.02-.41.11-.54.12-.12.27-.31.4-.46.13-.16.18-.27.27-.45.09-.18.04-.33-.02-.46-.06-.13-.58-1.4-.79-1.92-.21-.5-.43-.43-.58-.44h-.49c-.17 0-.45.07-.68.33-.24.27-.9.88-.9 2.15s.92 2.5 1.04 2.67c.13.18 1.8 2.75 4.37 3.85.61.26 1.08.41 1.45.52.61.19 1.16.16 1.59.1.49-.07 1.58-.64 1.8-1.25.22-.61.22-1.13.15-1.25-.06-.11-.24-.17-.51-.3Z" fill="#fff"/>
                        </svg>
                        <span>Hablar por WhatsApp</span>
                    </a>
                </div>

                <p class="hero-note"><b>{{ $demoCtaLabel }}:</b> crea un acceso temporal de prueba. Si superas 3 intentos, espera 60 minutos para volver a solicitar. <b>{{ $loginLabel }}:</b> abre tus datos reales.</p>
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
                                <button type="button" class="hero-carousel-control prev" data-hero-prev aria-label="Slide anterior">‹</button>
                                <button type="button" class="hero-carousel-control next" data-hero-next aria-label="Slide siguiente">›</button>
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
                        <span class="hero-insight-label">Con suscripcion activa</span>
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
                            <div class="brand-chip" data-marquee-chip-index="{{ $item['index'] }}">
                                @if ($item['logo'] !== '')
                                    <img src="{{ $item['logo'] }}" alt="{{ $item['text'] }}" class="brand-chip-logo" data-marquee-logo-index="{{ $item['index'] }}">
                                @endif
                                <span data-marquee-text-index="{{ $item['index'] }}">{{ $item['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="brands-row" aria-hidden="true">
                        @foreach ($marqueeItems as $item)
                            <div class="brand-chip" data-marquee-chip-index="{{ $item['index'] }}">
                                @if ($item['logo'] !== '')
                                    <img src="{{ $item['logo'] }}" alt="" class="brand-chip-logo" data-marquee-logo-index="{{ $item['index'] }}">
                                @endif
                                <span data-marquee-text-index="{{ $item['index'] }}">{{ $item['text'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
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

        <section id="pricing" class="shell section">
            <header class="heading reveal">
                <small>Precios</small>
                <h2>Planes claros para cada etapa</h2>
                <p>Escala de una sede a operación multi-gym sin cambiar de plataforma.</p>
            </header>
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
                        $planWhatsappUrl = $resolvePlanWhatsappUrl($planKey);
                        $planCtaLabel = 'HABLAR POR WHATSAPP';
                    @endphp
                    <article class="plan reveal {{ $isFeatured ? 'popular' : '' }}">
                        @if ($isFeatured)
                            <span class="pop">Plan destacado</span>
                        @endif
                        <h3>{{ $planCard['name'] }}</h3>
                        @if ($isContactMode)
                            <div class="price">Personalizado<small>/Contacto</small></div>
                            <div class="plan-discount">
                                Primer mes con descuento:
                                @if ($discountPercent !== null && $discountPercent > 0)
                                    <strong>{{ $discountPercent }}% menos</strong> sobre el valor cotizado.
                                @elseif ($discountPrice !== null && $discountPrice > 0)
                                    <strong>${{ $formatPlanMoney($discountPrice) }}</strong> de referencia.
                                @else
                                    <strong>segun cotizacion</strong>.
                                @endif
                            </div>
                        @else
                            <div class="price">${{ $formatPlanMoney($price) }}<small>/Mes</small></div>
                            <div class="plan-discount">
                                Primer mes con descuento:
                                @if ($discountPrice !== null && $discountPrice < $price)
                                    <span class="price-old">${{ $formatPlanMoney($price) }}</span>
                                    <strong>${{ $formatPlanMoney($discountPrice) }}</strong>
                                @else
                                    <strong>Sin descuento</strong>
                                @endif
                            </div>
                        @endif
                        <p>{{ $planCard['summary'] }}</p>
                        <ul>
                            @foreach ($planFeatures as $feature)
                                @php
                                    $isHighlightedFeature = str_contains(mb_strtolower((string) $feature), 'muy pronto');
                                @endphp
                                <li class="{{ $isHighlightedFeature ? 'plan-highlight' : '' }}">{{ $feature }}</li>
                            @endforeach
                        </ul>
                        <div class="plan-cta">
                            <a class="btn {{ $isContactMode ? 'btn-wa' : ($isFeatured ? 'btn-demo' : 'btn-outline') }}"
                               href="{{ $planWhatsappUrl }}"
                               target="_blank"
                               rel="noreferrer"
                               data-plan-cta-key="{{ $planKey }}">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12.04 2C6.57 2 2.12 6.45 2.12 11.92c0 1.75.46 3.46 1.33 4.96L2 22l5.28-1.38a9.86 9.86 0 0 0 4.76 1.21h.01c5.47 0 9.92-4.45 9.92-9.92C21.96 6.45 17.51 2 12.04 2Zm0 18.16h-.01a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.13.82.84-3.05-.2-.31a8.17 8.17 0 0 1-1.26-4.37c0-4.53 3.69-8.22 8.23-8.22 2.2 0 4.27.86 5.83 2.41a8.18 8.18 0 0 1 2.41 5.82c0 4.53-3.69 8.22-8.23 8.22Zm4.5-6.17c-.25-.12-1.49-.74-1.72-.82-.23-.09-.4-.13-.57.12-.17.25-.65.82-.8.99-.15.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.25-.74-.66-1.24-1.48-1.39-1.73-.15-.25-.02-.38.11-.5.11-.11.25-.29.37-.44.12-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.57-1.37-.78-1.88-.21-.49-.43-.42-.57-.42h-.49c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.23.9 2.42 1.03 2.58.12.17 1.77 2.7 4.28 3.78.6.26 1.07.41 1.43.52.6.19 1.14.16 1.57.1.48-.07 1.49-.61 1.7-1.2.21-.59.21-1.09.15-1.2-.06-.11-.23-.17-.48-.29Z"/>
                                </svg>
                                <span>{{ $planCtaLabel }}</span>
                            </a>
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
        @endif

        @if ($showAboutSection)
        <section id="nosotros" class="shell section">
            <header class="heading reveal">
                <small>Sobre nosotros</small>
                <h2>Construido para operar gimnasios reales</h2>
                <p>{{ $brandName }} es un sistema creado de forma independiente para resolver el trabajo diario de recepción, membresías, caja y control administrativo sin complicaciones.</p>
            </header>
            <div class="about-grid">
                <article class="about-card reveal">
                    <h3>Producto hecho con enfoque operativo</h3>
                    <p>Este proyecto nace de la necesidad de tener un flujo claro para gimnasios en Ecuador: rápido, estable y fácil de usar por equipos pequeños o multisede.</p>
                    <ul class="about-highlights">
                        <li>Recepcion agil con control de asistencia.</li>
                        <li>Membresias, vencimientos y caja diaria en un mismo sistema.</li>
                        <li>Panel SuperAdmin para administrar varios gimnasios.</li>
                        <li>Base PWA para uso en celular, tablet y computadora.</li>
                    </ul>
                </article>
                <aside class="about-card reveal">
                    <h3>Estado actual</h3>
                    <div class="about-metrics">
                        <article class="about-metric">
                            <strong>{{ number_format((int) ($stats['gyms'] ?? 0)) }}</strong>
                            <span>Gimnasios registrados</span>
                        </article>
                        <article class="about-metric">
                            <strong>{{ number_format((int) ($stats['active_sessions'] ?? 0)) }}</strong>
                            <span>Con suscripcion activa</span>
                        </article>
                        <article class="about-metric">
                            <strong>1</strong>
                            <span>Fundador operador</span>
                        </article>
                        <article class="about-metric">
                            <strong>24/7</strong>
                            <span>Evolucion continua</span>
                        </article>
                    </div>
                    <p>La prioridad es mantener el sistema estable y seguir sumando mejoras practicas segun necesidades reales de cada gimnasio.</p>
                </aside>
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

    <div id="demo-limit-backdrop" class="modal-backdrop {{ $demoLimitSeconds > 0 ? 'is-open' : '' }}"></div>

    <div id="demo-limit-modal"
         class="modal {{ $demoLimitSeconds > 0 ? 'is-open' : '' }}"
         role="dialog"
         aria-modal="true"
         aria-labelledby="demo-limit-title"
         data-retry-seconds="{{ $demoLimitSeconds }}">
        <h4 id="demo-limit-title">Limite de demos alcanzado</h4>
        <p>Alcanzaste el maximo de 3 intentos por hora. Debes esperar antes de volver a solicitar tu demo.</p>
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
                    if (document.body.classList.contains('perf-lite')) {
                        return;
                    }

                    if (timer) {
                        window.clearInterval(timer);
                    }

                    timer = window.setInterval(function () {
                        activate(activeIndex + 1);
                    }, 4200);
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
                window.addEventListener('codex:perf-lite', stop);
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



