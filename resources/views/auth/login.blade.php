<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#16c172">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FlexGym">
    <link rel="icon" href="{{ asset('favicon.ico?v=20260317') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/fg-favicon-32.png?v=20260317') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('pwa/fg-favicon-16.png?v=20260317') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/fg-favicon-32.png?v=20260317') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/fg-favicon-180.png?v=20260317') }}">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-0: #05070C;
            --bg-1: #071018;
            --neon-green: #39FF14;
            --electric-cyan: #00D4FF;
            --text-main: #EAF2FF;
            --text-muted: #A8B4C9;
            --glass-bg: rgba(10, 14, 24, 0.55);
            --glass-border: rgba(255, 255, 255, 0.14);
            --card-radius: 22px;
            --input-bg: rgba(8, 16, 30, 0.72);
            --danger-bg: rgba(127, 29, 29, 0.25);
            --danger-border: rgba(252, 165, 165, 0.35);
            --danger-text: #fecaca;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            margin: 0;
            color: var(--text-main);
            font-family: "Segoe UI", "Inter", system-ui, -apple-system, sans-serif;
            background: linear-gradient(130deg, var(--bg-0), var(--bg-1));
            overflow-x: hidden;
            position: relative;
        }

        /* Background image + dark cinematic overlays */
        body::before {
            content: "";
            position: fixed;
            inset: -2%;
            background:
                linear-gradient(120deg, rgba(5, 7, 12, 0.92), rgba(7, 16, 24, 0.88)),
                radial-gradient(circle at 20% 15%, rgba(57, 255, 20, 0.14), transparent 42%),
                radial-gradient(circle at 85% 80%, rgba(0, 212, 255, 0.16), transparent 45%),
                var(--login-bg-image);
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            filter: blur(2.6px) saturate(0.92);
            transform: scale(1.03);
            z-index: 0;
            pointer-events: none;
        }

        /* HUD grid */
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background:
                repeating-linear-gradient(90deg, rgba(138, 147, 166, 0.12) 0 1px, transparent 1px 74px),
                repeating-linear-gradient(0deg, rgba(138, 147, 166, 0.08) 0 1px, transparent 1px 74px);
            opacity: 0.22;
            z-index: 1;
            pointer-events: none;
        }

        .noise-layer {
            position: fixed;
            inset: 0;
            z-index: 2;
            opacity: 0.045;
            pointer-events: none;
            background-image: radial-gradient(rgba(255, 255, 255, 0.9) 0.6px, transparent 0.6px);
            background-size: 3px 3px;
        }

        .scan-line {
            position: fixed;
            left: 0;
            width: 100%;
            height: 74px;
            z-index: 3;
            pointer-events: none;
            opacity: 0.16;
            background: linear-gradient(
                to bottom,
                rgba(0, 212, 255, 0),
                rgba(0, 212, 255, 0.25),
                rgba(57, 255, 20, 0.16),
                rgba(0, 212, 255, 0)
            );
            animation: scanMove 8s linear infinite;
        }

        @keyframes scanMove {
            0% {
                top: -80px;
            }
            100% {
                top: calc(100% + 80px);
            }
        }

        .shell {
            position: relative;
            z-index: 4;
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: max(24px, env(safe-area-inset-top)) max(18px, env(safe-area-inset-right)) max(24px, env(safe-area-inset-bottom)) max(18px, env(safe-area-inset-left));
        }

        .layout {
            width: min(1220px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 24px;
            align-items: stretch;
        }

        .panel {
            border-radius: var(--card-radius);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid transparent;
            box-shadow: 0 22px 64px rgba(0, 0, 0, 0.48);
            position: relative;
            overflow: hidden;
        }

        .panel::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, rgba(57, 255, 20, 0.32), rgba(0, 212, 255, 0.28), rgba(255, 255, 255, 0.06));
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            pointer-events: none;
            z-index: 1;
        }

        .hero {
            padding: clamp(24px, 3.8vw, 40px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            isolation: isolate;
        }

        .hero::after,
        .hero::before {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            z-index: 0;
        }

        .hero::before {
            width: 370px;
            height: 370px;
            right: -160px;
            top: -140px;
            border: 1px solid rgba(0, 212, 255, 0.35);
            box-shadow: inset 0 0 0 34px rgba(0, 212, 255, 0.05);
        }

        .hero::after {
            width: 260px;
            height: 260px;
            left: -120px;
            bottom: -116px;
            border: 1px solid rgba(57, 255, 20, 0.42);
            box-shadow: inset 0 0 0 30px rgba(57, 255, 20, 0.06);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(0, 212, 255, 0.35);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b8e9ff;
            background: rgba(3, 10, 20, 0.45);
        }

        .hero-title {
            margin: 0;
            font-size: clamp(42px, 7.4vw, 88px);
            line-height: 0.92;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            font-weight: 900;
        }

        .hero-title .cyan {
            color: #8ee8ff;
            text-shadow: 0 0 24px rgba(0, 212, 255, 0.35);
        }

        .hero-title .solid {
            color: #e8f0ff;
        }

        .hero-subtitle {
            margin: 6px 0 0;
            max-width: 590px;
            color: var(--text-muted);
            font-size: clamp(15px, 1.8vw, 20px);
            line-height: 1.5;
            font-weight: 500;
        }

        .hero-modules {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(3, minmax(120px, 1fr));
            gap: 12px;
            max-width: 620px;
        }

        .module-chip {
            border: 1px solid rgba(138, 147, 166, 0.35);
            border-radius: 14px;
            padding: 12px 12px;
            background: rgba(7, 16, 30, 0.6);
            color: #e9f2ff;
            text-align: left;
            cursor: default;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .module-chip span {
            display: block;
            font-size: 11px;
            color: #97a7c4;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .module-chip strong {
            font-size: 29px;
            line-height: 1;
            margin-right: 8px;
            color: var(--neon-green);
            vertical-align: middle;
        }

        .module-chip b {
            font-size: 24px;
            line-height: 1;
            vertical-align: middle;
            font-weight: 800;
        }

        .module-chip:hover {
            transform: translateY(-2px);
            border-color: rgba(57, 255, 20, 0.58);
            box-shadow: 0 0 22px rgba(57, 255, 20, 0.22);
        }

        .quote-box {
            margin-top: 18px;
            border: 1px solid rgba(0, 212, 255, 0.28);
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(3, 12, 25, 0.58);
            color: #cfe9ff;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .mode-indicator {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #d7ffe5;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .mode-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--neon-green);
            box-shadow: 0 0 12px rgba(57, 255, 20, 0.7);
            animation: blinkDot 1.6s ease-in-out infinite;
        }

        @keyframes blinkDot {
            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.35);
                opacity: 0.75;
            }
        }

        .auth {
            padding: clamp(24px, 3.8vw, 38px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
            min-height: 100%;
        }

        .auth-home-link {
            position: absolute;
            top: 16px;
            right: 16px;
            z-index: 3;
            min-height: 40px;
            padding: 8px 12px;
            border-radius: 11px;
            border: 1px solid rgba(138, 147, 166, 0.38);
            background: rgba(7, 16, 30, 0.42);
            color: #c6d4ea;
            text-decoration: none;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .16s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease;
        }

        .auth-home-link:hover {
            transform: translateY(-1px);
            border-color: rgba(0, 212, 255, 0.5);
            box-shadow: 0 8px 18px rgba(0, 212, 255, 0.14);
            color: #e5f1ff;
        }

        .auth-home-link:focus-visible {
            outline: 2px solid rgba(57, 255, 20, 0.6);
            outline-offset: 2px;
        }

        .auth-logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }

        .auth-logo {
            width: min(64vw, 295px);
            max-width: 295px;
            min-width: 195px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 0 14px rgba(57, 255, 20, 0.24));
        }

        .auth h1 {
            margin: 0;
            font-size: clamp(38px, 5.2vw, 54px);
            line-height: 0.94;
            text-transform: uppercase;
            letter-spacing: 0.022em;
            font-weight: 880;
            color: var(--neon-green);
            text-shadow: 0 0 16px rgba(57, 255, 20, 0.28);
        }

        .auth p {
            margin: 0;
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 500;
            line-height: 1.48;
        }

        .alert {
            margin-top: 12px;
            border: 1px solid var(--danger-border);
            background: var(--danger-bg);
            color: var(--danger-text);
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .form {
            margin-top: 16px;
            display: grid;
            gap: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: #c9d7f0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .input-wrap {
            position: relative;
        }

        .input {
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: 1px solid rgba(138, 147, 166, 0.42);
            background: var(--input-bg);
            color: var(--text-main);
            padding: 0 14px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .14s ease;
        }

        .input::placeholder {
            color: #7f8ba5;
        }

        .input:focus-visible {
            border-color: rgba(57, 255, 20, 0.64);
            box-shadow:
                0 0 0 2px rgba(57, 255, 20, 0.16),
                0 0 10px rgba(57, 255, 20, 0.2);
            transform: translateY(-1px);
        }

        .input.password {
            padding-right: 54px;
        }

        .toggle-password {
            position: absolute;
            inset: 0 0 0 auto;
            width: 52px;
            border: 0;
            border-radius: 0 14px 14px 0;
            background: transparent;
            color: #8ea0bf;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color .16s ease;
        }

        .toggle-password:hover,
        .toggle-password:focus-visible {
            color: #e6f2ff;
            outline: none;
        }

        .is-hidden {
            display: none;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #d8e4fa;
            font-size: 14px;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--neon-green);
        }

        .submit {
            position: relative;
            overflow: hidden;
            height: 52px;
            border: 0;
            border-radius: 14px;
            cursor: pointer;
            color: #f8fbff;
            font-size: 30px;
            font-weight: 900;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: linear-gradient(90deg, #1aa2f2 0%, #2559f5 52%, #00D4FF 100%);
            box-shadow: 0 14px 30px rgba(0, 131, 255, 0.35);
            transition: transform .16s ease, filter .2s ease, box-shadow .2s ease;
        }

        .submit::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            width: 64px;
            left: -72px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.42), transparent);
            transition: transform .4s ease;
        }

        .submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.04);
            box-shadow: 0 18px 34px rgba(0, 131, 255, 0.4);
        }

        .submit:hover::before {
            transform: translateX(calc(100% + 140px));
        }

        .submit:active {
            transform: translateY(0);
        }

        .submit:focus-visible {
            outline: 2px solid rgba(0, 212, 255, 0.7);
            outline-offset: 2px;
        }
        @media (max-width: 1040px) {
            .layout {
                grid-template-columns: 1fr;
                max-width: 700px;
                gap: 18px;
            }

            .auth {
                order: 1;
                padding: 22px;
            }

            .hero {
                order: 2;
                padding: 22px;
            }

            .hero-title {
                font-size: clamp(34px, 11vw, 72px);
            }
        }

        @media (max-width: 680px) {
            .hero-modules {
                grid-template-columns: 1fr;
            }

            .hero-subtitle {
                font-size: 14px;
                line-height: 1.52;
            }

            .auth h1 {
                font-size: clamp(34px, 12vw, 44px);
            }

            .auth-home-link {
                top: 12px;
                right: 12px;
                min-height: 38px;
                padding: 7px 11px;
                font-size: 10px;
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
    $logoUrl = $logoDarkUrl !== '' ? $logoDarkUrl : $logoLightUrl;
    $backgroundImage = 'https://videos.openai.com/az/vg-assets/task_01kj21yc2xf5rsm5kr195mpk2t%2F1771743176_img_0.webp?se=2026-02-24T00%3A00%3A00Z&sp=r&sv=2026-02-06&sr=b&skoid=cfbc986b-d2bc-4088-8b71-4f962129715b&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2026-02-21T12%3A15%3A03Z&ske=2026-02-28T12%3A20%3A03Z&sks=b&skv=2026-02-06&sig=t1IBXGv9XZGrUs93PHs/m5fOl57KAgFzsy8sQaYyw6w%3D&ac=oaivgprodscus2';
@endphp
<body style="--login-bg-image: url('{{ $backgroundImage }}');">
<div class="noise-layer" aria-hidden="true"></div>
<div class="scan-line" aria-hidden="true"></div>

<main class="shell">
    <div class="layout">
        <section class="panel hero" aria-label="Panel de modo operativo">
            <div class="hero-content">
                <div class="hero-top">
                    <span class="hero-kicker">GymSystem Control</span>
                </div>

                <h2 class="hero-title"><span class="cyan">Modo</span> <span class="solid">Operativo</span></h2>

                <p class="hero-subtitle">Gestiona asistencia, membresías, caja y rendimiento del gimnasio desde un centro operativo rápido, seguro y en tiempo real.</p>

                <div class="hero-modules" aria-label="Módulos principales">
                    <button class="module-chip" type="button">
                        <span>Módulo</span>
                        <strong>01</strong><b>Recepción</b>
                    </button>
                    <button class="module-chip" type="button">
                        <span>Control</span>
                        <strong>02</strong><b>Accesos</b>
                    </button>
                    <button class="module-chip" type="button">
                        <span>Panel</span>
                        <strong>03</strong><b>Tiempo Real</b>
                    </button>
                </div>

                <div class="quote-box">"El poder comienza cuando pierdes el miedo."</div>
                <div class="mode-indicator">
                    <span class="mode-dot" aria-hidden="true"></span>
                    <span>Modo fuerza activo</span>
                </div>
            </div>
        </section>

        <section class="panel auth" aria-label="Panel de ingreso">
            <a href="{{ route('landing') }}" class="auth-home-link">Ir a página principal</a>
            @if ($logoUrl !== '')
                <div class="auth-logo-wrap">
                    <img src="{{ $logoUrl }}" alt="Logo FlexjoK" class="auth-logo">
                </div>
            @endif
            <h1>Ingreso</h1>
            <p>Accede con tu cuenta de recepción.</p>

            @if ($errors->any())
                <div class="alert" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="form" novalidate>
                @csrf
                <input type="hidden" name="pwa_mode" id="login-pwa-mode" value="browser">

                <div class="field">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        value="{{ old('email') }}"
                        class="input"
                        placeholder="recepción@gym.com">
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <div class="input-wrap">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            data-password-toggle-ignore="1"
                            required
                            autocomplete="current-password"
                            class="input password"
                            placeholder="Ingresa tu contraseña">
                        <button
                            type="button"
                            id="toggle-password-visibility"
                            class="toggle-password"
                            aria-label="Mostrar contraseña"
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
                    <span>Recu&eacute;rdame</span>
                </label>

                <button type="submit" class="submit">Entrar</button>
            </form>
        </section>
    </div>
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
            toggle.setAttribute('aria-label', isVisible ? 'Ocultar contraseña' : 'Mostrar contraseña');
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

