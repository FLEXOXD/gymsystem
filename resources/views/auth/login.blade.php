<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#090909">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FlexGym">
    <link rel="icon" href="{{ asset('favicon.ico?v=20260317') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/fg-favicon-32.png?v=20260317') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('pwa/fg-favicon-16.png?v=20260317') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/fg-favicon-32.png?v=20260317') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/fg-favicon-180.png?v=20260317') }}">
    <title>Iniciar sesi&oacute;n</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --login-bg: #090909;
            --login-card-bg: rgba(16, 16, 16, 0.9);
            --login-border: rgba(255, 255, 255, 0.08);
            --login-border-strong: rgba(184, 255, 31, 0.34);
            --login-text: #f3f1e9;
            --login-muted: #b8b5a8;
            --login-field: rgba(22, 22, 22, 0.9);
            --login-field-hover: rgba(28, 28, 28, 0.96);
            --login-primary: #b8ff1f;
            --login-primary-dark: #8ebd17;
            --login-secondary: #dbff73;
            --login-shadow: 0 44px 120px rgba(2, 6, 23, 0.64);
            --login-danger-bg: rgba(127, 29, 29, 0.2);
            --login-danger-border: rgba(252, 165, 165, 0.26);
            --login-danger-text: #fecaca;
            --login-heading: "Barlow Condensed", "Arial Narrow", sans-serif;
            --login-body: "Manrope", "Segoe UI", sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            width: 100%;
        }

        body {
            margin: 0;
            font-family: var(--login-body);
            color: var(--login-text);
            background:
                radial-gradient(circle at top, rgba(184, 255, 31, 0.12), transparent 32%),
                linear-gradient(160deg, #070707 0%, var(--login-bg) 45%, #0b0b0b 100%);
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(3, 7, 18, 0.72), rgba(3, 7, 18, 0.84)),
                var(--login-bg-image);
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            filter: saturate(0.86);
            transform: scale(1.01);
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(7, 7, 7, 0.08), rgba(7, 7, 7, 0.54)),
                radial-gradient(circle at 15% 20%, rgba(184, 255, 31, 0.14), transparent 26%),
                radial-gradient(circle at 85% 80%, rgba(184, 255, 31, 0.08), transparent 30%);
            z-index: 0;
            pointer-events: none;
        }

        .shell {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding:
                max(24px, env(safe-area-inset-top))
                max(18px, env(safe-area-inset-right))
                max(24px, env(safe-area-inset-bottom))
                max(18px, env(safe-area-inset-left));
        }

        .login-stage {
            position: relative;
            width: min(760px, 100%);
        }

        .login-stage::before,
        .login-stage::after {
            content: "";
            position: absolute;
            pointer-events: none;
            z-index: 0;
            filter: blur(56px);
        }

        .login-stage::before {
            inset: 12% -6% -16% 28%;
            background: radial-gradient(circle, rgba(184, 255, 31, 0.18), transparent 58%);
        }

        .login-stage::after {
            inset: -10% 18% 54% -12%;
            background: radial-gradient(circle, rgba(184, 255, 31, 0.1), transparent 52%);
        }

        .login-card {
            position: relative;
            z-index: 1;
            padding: clamp(24px, 5vw, 36px);
            border-radius: 30px;
            background:
                linear-gradient(180deg, rgba(17, 17, 17, 0.94), var(--login-card-bg));
            border: 1px solid rgba(184, 255, 31, 0.14);
            box-shadow: var(--login-shadow);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            overflow: hidden;
            animation: cardReveal 0.68s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .login-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.07), rgba(184, 255, 31, 0.18), rgba(255, 255, 255, 0.04));
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            pointer-events: none;
        }

        .login-card::after {
            content: "";
            position: absolute;
            left: 28px;
            right: 28px;
            top: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(184, 255, 31, 0.42), transparent);
            opacity: 0.65;
            pointer-events: none;
        }

        @keyframes cardReveal {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.985);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-inner {
            position: relative;
            z-index: 1;
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 36px;
            padding: 0 13px;
            border-radius: 999px;
            border: 1px solid rgba(184, 255, 31, 0.16);
            background: rgba(255, 255, 255, 0.03);
            color: var(--login-text);
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--login-primary);
            box-shadow: 0 0 0 4px rgba(184, 255, 31, 0.08);
        }

        .home-link {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.03);
            color: rgba(243, 241, 233, 0.86);
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            transition: color 0.18s ease, transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
        }

        .home-link:hover {
            color: #101106;
            transform: translateY(-1px);
            border-color: rgba(184, 255, 31, 0.28);
            background: linear-gradient(135deg, var(--login-primary), var(--login-secondary));
        }

        .home-link:focus-visible {
            outline: 2px solid rgba(184, 255, 31, 0.34);
            outline-offset: 4px;
            border-radius: 8px;
        }

        .brand {
            display: grid;
            gap: 12px;
            margin-bottom: 18px;
            padding: 0;
            align-items: center;
            text-align: left;
        }

        .auth-logo-wrap {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-height: 0;
            padding: 0;
        }

        .auth-logo {
            width: min(100%, 182px);
            max-width: none;
            max-height: 132px;
            object-fit: contain;
            filter: drop-shadow(0 10px 26px rgba(184, 255, 31, 0.08));
        }

        .brand-copy {
            display: grid;
            gap: 8px;
            justify-items: start;
            width: 100%;
        }

        .brand-kicker {
            color: var(--login-primary);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .brand-copy h1 {
            margin: 0;
            font-family: var(--login-heading);
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 0 0.12em;
            width: 100%;
            max-width: none;
            font-size: clamp(3.45rem, 5.35vw, 4.7rem);
            line-height: 0.84;
            font-weight: 800;
            letter-spacing: -0.025em;
            white-space: normal;
            text-transform: uppercase;
        }

        .brand-copy h1 span {
            display: block;
            color: #f5ffd8;
            text-shadow: 0 0 18px rgba(184, 255, 31, 0.12);
        }

        .brand-copy h1 span + span {
            margin-left: 0;
        }

        .brand-copy p {
            margin: 0;
            color: var(--login-muted);
            font-size: 0.95rem;
            line-height: 1.55;
            max-width: 34rem;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid var(--login-danger-border);
            background: var(--login-danger-bg);
            color: var(--login-danger-text);
            font-size: 0.92rem;
            line-height: 1.45;
        }

        .form {
            display: grid;
            gap: 18px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field label {
            color: var(--login-text);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(180deg, var(--login-field-hover), var(--login-field));
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease, transform 0.18s ease;
        }

        .input-wrap:hover {
            border-color: rgba(184, 255, 31, 0.16);
            background: linear-gradient(180deg, rgba(30, 30, 30, 0.98), rgba(22, 22, 22, 0.94));
        }

        .input-wrap:focus-within {
            border-color: var(--login-border-strong);
            box-shadow: 0 0 0 4px rgba(184, 255, 31, 0.1);
            transform: translateY(-1px);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            width: 18px;
            height: 18px;
            color: rgba(184, 181, 168, 0.66);
            pointer-events: none;
            transition: color 0.18s ease;
        }

        .input-wrap:focus-within .input-icon {
            color: var(--login-primary);
        }

        .input {
            width: 100%;
            min-height: 54px;
            border: 0;
            background: transparent;
            color: var(--login-text);
            padding: 0 16px 0 48px;
            outline: none;
            transition: color 0.18s ease;
        }

        .input::placeholder {
            color: rgba(184, 181, 168, 0.56);
        }

        .input:focus-visible {
            box-shadow: none;
        }

        .input.password {
            padding-right: 56px;
        }

        .toggle-password {
            position: absolute;
            inset: 0 0 0 auto;
            width: 52px;
            border: 0;
            border-radius: 0 18px 18px 0;
            background: transparent;
            color: rgba(184, 181, 168, 0.7);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.18s ease, background-color 0.18s ease;
        }

        .toggle-password:hover,
        .toggle-password:focus-visible {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.03);
            outline: none;
        }

        .is-hidden {
            display: none;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(243, 241, 233, 0.86);
            font-size: 0.94rem;
            width: fit-content;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--login-primary);
        }

        .submit {
            position: relative;
            overflow: hidden;
            min-height: 56px;
            border: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-secondary) 100%);
            color: #0d1004;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 16px 28px rgba(184, 255, 31, 0.16), inset 0 1px 0 rgba(255, 255, 255, 0.28);
            transition: transform 0.18s ease, filter 0.18s ease, box-shadow 0.18s ease;
        }

        .submit::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: -22%;
            width: 24%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.24), transparent);
            transform: skewX(-18deg);
            transition: transform 0.45s ease;
        }

        .submit:hover {
            transform: translateY(-1px);
            filter: brightness(1.03);
            box-shadow: 0 20px 36px rgba(184, 255, 31, 0.18);
        }

        .submit:hover::before {
            transform: translateX(520%) skewX(-18deg);
        }

        .submit:active {
            transform: translateY(0);
        }

        .submit:focus-visible {
            outline: 2px solid rgba(184, 255, 31, 0.34);
            outline-offset: 3px;
        }

        .login-note {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--login-muted);
            font-size: 0.9rem;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .login-note::before {
            content: "";
            width: 9px;
            height: 9px;
            margin-top: 0.32rem;
            border-radius: 999px;
            flex: 0 0 auto;
            background: linear-gradient(135deg, var(--login-primary), var(--login-secondary));
            box-shadow: 0 0 18px rgba(184, 255, 31, 0.12);
        }

        @media (min-width: 720px) {
            .brand {
                grid-template-columns: clamp(150px, 18vw, 196px) minmax(0, 1fr);
                gap: clamp(18px, 3vw, 28px);
            }

            .form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px 16px;
            }

            .remember,
            .submit,
            .login-note {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 719px) {
            .card-top {
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px;
            }

            .brand {
                justify-items: center;
                gap: 16px;
                margin-bottom: 14px;
                text-align: center;
            }

            .auth-logo-wrap {
                min-height: 0;
                padding: 0;
                justify-content: center;
            }

            .auth-logo {
                width: min(100%, 220px);
                max-width: none;
                max-height: 88px;
            }

            .brand-copy {
                justify-items: center;
                text-align: center;
            }

            .brand-copy h1 {
                justify-content: center;
                font-size: clamp(2.85rem, 12vw, 3.8rem);
                line-height: 0.8;
            }

            .brand-copy p {
                max-width: 28ch;
            }

            .brand-copy p,
            .login-note {
                font-size: 0.93rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
@php
    $branding = is_array($loginBranding ?? null) ? $loginBranding : [];
    $logoLightUrl = trim((string) ($branding['logo_light_url'] ?? ''));
    $logoDarkUrl = trim((string) ($branding['logo_dark_url'] ?? ''));
    $defaultWordmarkUrl = asset('pwa/flexgymlogo.png?v=20260317');
    $logoUrl = $logoDarkUrl !== '' ? $logoDarkUrl : ($logoLightUrl !== '' ? $logoLightUrl : $defaultWordmarkUrl);
    $backgroundImageFile = '20260324_2310_Neon Ecuador Outline_simple_compose_01kmhjzh42fnebkbadymqhymsx.png';
    $backgroundImage = asset('images/premium/' . rawurlencode($backgroundImageFile)) . '?v=20260324b';
@endphp
<body style="--login-bg-image: url('{{ $backgroundImage }}');">
<main class="shell">
    <section class="login-stage" aria-label="Acceso al sistema">
        <div class="login-card">
            <div class="card-inner">
                <div class="card-top">
                    <span class="eyebrow">Panel FlexGym</span>
                    <a href="{{ route('landing') }}" class="home-link">Volver al inicio</a>
                </div>

                <div class="brand">
                    @if ($logoUrl !== '')
                        <div class="auth-logo-wrap">
                            <img src="{{ $logoUrl }}" alt="Isotipo FlexGym" class="auth-logo">
                        </div>
                    @endif

                    <div class="brand-copy">
                        <span class="brand-kicker">Acceso administrativo</span>
                        <h1><span>Iniciar</span><span>sesi&oacute;n</span></h1>
                        <p>Accede al panel administrativo desde escritorio o m&oacute;vil.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert" role="alert">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="form" novalidate>
                    @csrf
                    <input type="hidden" name="pwa_mode" id="login-pwa-mode" value="browser">

                    <div class="field">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <span class="input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7.5h16v9A1.5 1.5 0 0 1 18.5 18h-13A1.5 1.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m5 8 7 5 7-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                required
                                autofocus
                                autocomplete="username"
                                value="{{ old('email') }}"
                                class="input"
                                placeholder="recepcion@gym.com">
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Contrase&ntilde;a</label>
                        <div class="input-wrap">
                            <span class="input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M7.5 10V8a4.5 4.5 0 1 1 9 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <rect x="5" y="10" width="14" height="10" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </span>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                data-password-toggle-ignore="1"
                                required
                                autocomplete="current-password"
                                class="input password"
                                placeholder="Ingresa tu contrase&ntilde;a">
                            <button
                                type="button"
                                id="toggle-password-visibility"
                                class="toggle-password"
                                aria-label="Mostrar contrase&ntilde;a"
                                aria-controls="password"
                                aria-pressed="false">
                                <svg id="password-eye-open" class="h-5 w-5 is-hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                <svg id="password-eye-closed" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M10.5 6.3A11 11 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-4.1 4.8M6.6 8.1C3.8 10 2 12 2 12s3.5 6 10 6a11 11 0 0 0 4.3-.8" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Recordarme</span>
                    </label>

                    <button type="submit" class="submit">Entrar</button>
                </form>

                <p class="login-note">Acceso para recepci&oacute;n, administraci&oacute;n y personal autorizado con conexi&oacute;n segura.</p>
            </div>
        </div>
    </section>
</main>

<script>
    (function () {
        const pwaModeInput = document.getElementById('login-pwa-mode');
        if (pwaModeInput) {
            const isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches)
                || (window.navigator && window.navigator.standalone === true);
            const mode = isStandalone ? 'standalone' : 'browser';
            pwaModeInput.value = mode;
            document.cookie = `gym_pwa_mode=${mode}; path=/; max-age=2592000; SameSite=Lax`;
        }

        const toggle = document.getElementById('toggle-password-visibility');
        const input = document.getElementById('password');
        const eyeOpen = document.getElementById('password-eye-open');
        const eyeClosed = document.getElementById('password-eye-closed');

        if (!toggle || !input || !eyeOpen || !eyeClosed) {
            return;
        }

        const syncPasswordIconState = function () {
            const isVisible = input.type === 'text';
            eyeOpen.classList.toggle('is-hidden', !isVisible);
            eyeClosed.classList.toggle('is-hidden', isVisible);
            toggle.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
            toggle.setAttribute('aria-label', isVisible ? 'Ocultar contrase\u00f1a' : 'Mostrar contrase\u00f1a');
        };

        syncPasswordIconState();

        toggle.addEventListener('click', function () {
            input.type = input.type === 'password' ? 'text' : 'password';
            syncPasswordIconState();
        });
    })();
</script>
</body>
</html>
