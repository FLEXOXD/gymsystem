<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16c172">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GymSystem">
    <link rel="manifest" href="{{ route('client-mobile.manifest', ['gymSlug' => $gym->slug]) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <title>Acceso cliente - {{ (string) $gym->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg-0: #01070f;
            --bg-1: #031426;
            --neon-green: #22c55e;
            --neon-cyan: #22d3ee;
            --text-soft: #d4f6ef;
        }
        .mobile-guard { display: none; }
        .mobile-shell {
            min-height: 100vh;
            background:
                linear-gradient(rgba(1,7,15,.42), rgba(1,7,15,.70)),
                radial-gradient(circle at 10% 12%, rgba(34,197,94,.25), transparent 38%),
                radial-gradient(circle at 88% 6%, rgba(34,211,238,.20), transparent 42%),
                linear-gradient(90deg, rgba(1,7,15,.16) 0%, rgba(1,7,15,.36) 100%),
                url('https://drive.google.com/thumbnail?id=1chSsW0bDbahHFg6e2ttH4wKkg6CrZKI0&sz=w2000') center center / cover no-repeat,
                url('https://st3.depositphotos.com/3383955/33157/i/950/depositphotos_331574238-stock-photo-sporty-couple-workout-dumbbells-muscular.jpg') center center / cover no-repeat,
                linear-gradient(165deg, var(--bg-1), var(--bg-0) 65%);
            color: #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        .mobile-shell::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(34,197,94,.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,197,94,.08) 1px, transparent 1px);
            background-size: 26px 26px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.8), rgba(0,0,0,.15));
            pointer-events: none;
        }
        .hero-panel {
            position: relative;
            border: 0;
            background: transparent;
            box-shadow: none;
            backdrop-filter: none;
        }
        .login-card {
            border: 0;
            background: transparent;
            box-shadow: none;
            backdrop-filter: none;
        }
        .field-label {
            color: #d8f3ff;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .02em;
        }
        .login-input {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(56,189,248,.28);
            background: rgba(2,6,23,.78);
            color: #e2e8f0;
            padding: 11px 13px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .login-input::placeholder { color: rgba(148,163,184,.85); }
        .login-input:focus {
            border-color: rgba(34,197,94,.75);
            box-shadow: 0 0 0 3px rgba(34,197,94,.22);
        }
        .login-submit {
            width: 100%;
            border: 0;
            border-radius: 13px;
            padding: 12px 14px;
            background: linear-gradient(120deg, #1d4ed8, #06b6d4 55%, #16a34a);
            color: #f8fafc;
            font-weight: 800;
            letter-spacing: .01em;
            transition: transform .14s ease, box-shadow .2s ease, opacity .18s ease;
            box-shadow: 0 16px 36px rgba(8,47,73,.42);
        }
        .login-submit:active { transform: translateY(1px) scale(.995); }
        .login-submit[disabled] { opacity: .7; cursor: wait; }
        .login-install {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(56,189,248,.42);
            background: rgba(2,6,23,.72);
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .01em;
            padding: 10px 12px;
            transition: transform .12s ease, border-color .18s ease;
        }
        .login-install:active { transform: translateY(1px); }
        .login-install:hover { border-color: rgba(34,197,94,.62); }
        .login-install.hidden { display: none; }
        .login-install-note {
            color: #a7f3d0;
            font-size: 11px;
            line-height: 1.35;
            text-align: center;
        }
        .login-install-note.hidden { display: none; }
        .submit-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,.28);
            border-top-color: #ffffff;
            border-radius: 9999px;
            display: inline-block;
            animation: loginSpin .7s linear infinite;
            vertical-align: -2px;
            margin-right: 8px;
        }
        .login-layout {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px 0;
        }
        .login-stack {
            width: min(100%, 460px);
        }
        @supports not (height: 100dvh) {
            .login-layout { min-height: 100vh; }
        }
        @keyframes loginSpin { to { transform: rotate(360deg); } }
        @media (min-width: 900px) and (pointer:fine) {
            .mobile-shell { display: none; }
            .mobile-guard {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
                background: #020617;
                color: #cbd5e1;
            }
        }
    </style>
</head>
<body>
<div class="mobile-guard">
    <div class="max-w-xl rounded-2xl border border-slate-700 bg-slate-900/80 p-6 text-center">
        <h1 class="text-2xl font-black text-white">Interfaz exclusiva para celulares</h1>
        <p class="mt-3 text-sm text-slate-300">Ingresa desde tu teléfono para usar check-in móvil.</p>
    </div>
</div>

<main class="mobile-shell px-4">
    <section class="login-layout relative z-10">
        <div class="login-stack mx-auto max-w-md space-y-5">
        <header class="hero-panel rounded-3xl p-5 text-center">
            <p class="text-xs font-black uppercase tracking-[.2em] text-emerald-200">{{ (string) $gym->name }}</p>
            <h1 class="mt-2 text-3xl font-black text-white">Bienvenido a tu espacio fitness</h1>
            <p class="mt-2 text-sm text-cyan-100/90">Inicia sesión para registrar asistencias y seguir tu progreso en el gimnasio.</p>
        </header>

        @if ($errors->has('mobile_login'))
            <div class="rounded-xl border border-rose-500/40 bg-rose-500/15 px-3 py-2 text-sm text-rose-100">
                {{ $errors->first('mobile_login') }}
            </div>
        @endif

        <form id="client-login-form" method="POST" action="{{ route('client-mobile.authenticate', ['gymSlug' => $gym->slug]) }}" class="login-card rounded-3xl p-5 space-y-4">
            @csrf
            <label class="block space-y-1.5 text-sm">
                <span class="field-label">Usuario</span>
                <input type="text" name="username" required autocomplete="username" value="{{ old('username', '') }}" class="login-input" placeholder="usuario cliente">
            </label>

            <label class="block space-y-1.5 text-sm">
                <span class="field-label">Contraseña</span>
                <input type="password" name="password" required autocomplete="current-password" class="login-input" placeholder="********">
            </label>

            <button id="client-login-submit" type="submit" class="login-submit">
                <span class="submit-label">Entrar</span>
            </button>

            <button id="client-pwa-install" type="button" class="login-install hidden">Instalar app</button>
            <p id="client-pwa-install-note" class="login-install-note hidden">Para instalar: abre el menú del navegador y elige "Instalar app".</p>
        </form>

        <p class="px-1 text-center text-[11px] text-emerald-100/80">Tu sesión es privada y segura en este dispositivo.</p>
        </div>
    </section>
</main>

<script>
(function () {
    const form = document.getElementById('client-login-form');
    const submit = document.getElementById('client-login-submit');
    const installBtn = document.getElementById('client-pwa-install');
    const installNote = document.getElementById('client-pwa-install-note');
    let installPromptEvent = null;
    if (!form || !submit) return;

    function isStandaloneMode() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    function setPwaModeCookie(mode) {
        document.cookie = 'gym_pwa_mode=' + mode + '; path=/; max-age=2592000; SameSite=Lax';
    }

    async function registerPwaServiceWorker() {
        if (!('serviceWorker' in navigator) || !window.isSecureContext) return;

        try {
            await navigator.serviceWorker.register('/sw.js');
        } catch (_error) {
            // Keep silent.
        }
    }

    function showInstallManualHint() {
        if (!installNote) return;
        installNote.classList.remove('hidden');
    }

    function resetSubmitState() {
        submit.disabled = false;
        submit.innerHTML = '<span class="submit-label">Entrar</span>';
    }

    form.addEventListener('submit', function () {
        if (submit.disabled) return;
        submit.disabled = true;
        submit.innerHTML = '<span class="submit-spinner" aria-hidden="true"></span><span class="submit-label">Ingresando...</span>';
    });

    if (installBtn) {
        if (isStandaloneMode()) {
            installBtn.classList.add('hidden');
            installBtn.setAttribute('aria-hidden', 'true');
        }

        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            installPromptEvent = event;
            installBtn.classList.remove('hidden');
            installBtn.removeAttribute('aria-hidden');
            if (installNote) {
                installNote.classList.add('hidden');
            }
        });

        installBtn.addEventListener('click', async function () {
            if (!installPromptEvent) {
                showInstallManualHint();
                return;
            }

            installPromptEvent.prompt();
            try {
                await installPromptEvent.userChoice;
            } catch (_error) {
                // Keep silent.
            }
            installPromptEvent = null;
            installBtn.classList.add('hidden');
            installBtn.setAttribute('aria-hidden', 'true');
        });
    }

    window.addEventListener('appinstalled', function () {
        if (installBtn) {
            installBtn.classList.add('hidden');
            installBtn.setAttribute('aria-hidden', 'true');
        }
        if (installNote) {
            installNote.classList.add('hidden');
        }
    });

    setPwaModeCookie(isStandaloneMode() ? 'standalone' : 'browser');
    registerPwaServiceWorker();
    window.addEventListener('pageshow', resetSubmitState);
})();
</script>
</body>
</html>
