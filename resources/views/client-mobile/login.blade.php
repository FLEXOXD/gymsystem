<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso cliente - {{ (string) $gym->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .mobile-guard { display: none; }
        .mobile-shell { min-height: 100vh; background: radial-gradient(circle at 20% 10%, rgba(14,165,233,.20), transparent 45%), radial-gradient(circle at 90% 5%, rgba(34,197,94,.22), transparent 40%), #020617; color: #e2e8f0; }
        .mobile-card { border: 1px solid rgba(56,189,248,.35); background: rgba(2,6,23,.80); box-shadow: 0 20px 60px rgba(2,6,23,.65); }
        @media (min-width: 900px) and (pointer:fine) {
            .mobile-shell { display: none; }
            .mobile-guard { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #020617; color: #cbd5e1; }
        }
    </style>
</head>
<body>
<div class="mobile-guard">
    <div class="max-w-xl rounded-2xl border border-slate-700 bg-slate-900/80 p-6 text-center">
        <h1 class="text-2xl font-black text-white">Interfaz exclusiva para celulares</h1>
        <p class="mt-3 text-sm text-slate-300">Ingresa desde tu teléfono e instala la PWA para usar check-in móvil.</p>
    </div>
</div>

<main class="mobile-shell px-4 py-6">
    <section class="mx-auto max-w-md space-y-4">
        <header class="text-center">
            <p class="text-xs font-black uppercase tracking-[.18em] text-cyan-200">{{ (string) $gym->name }}</p>
            <h1 class="mt-2 text-3xl font-black text-white">Acceso cliente PWA</h1>
            <p class="mt-2 text-sm text-slate-300">{{ __('messages.client_mobile.login_hint') }}</p>
        </header>

        @if ($errors->has('mobile_login'))
            <div class="rounded-xl border border-rose-500/40 bg-rose-500/15 px-3 py-2 text-sm text-rose-100">
                {{ $errors->first('mobile_login') }}
            </div>
        @endif

        <form method="POST" action="{{ route('client-mobile.authenticate', ['gymSlug' => $gym->slug]) }}" class="mobile-card rounded-2xl p-4 space-y-4">
            @csrf
            <label class="block space-y-1 text-sm">
                <span class="font-semibold text-slate-200">Usuario</span>
                <input type="text" name="username" required autocomplete="username" value="{{ old('username', '') }}" class="ui-input" placeholder="usuario cliente">
            </label>

            <label class="block space-y-1 text-sm">
                <span class="font-semibold text-slate-200">Contraseña</span>
                <input type="password" name="password" required autocomplete="current-password" class="ui-input" placeholder="********">
            </label>

            <button type="submit" class="ui-button ui-button-primary w-full justify-center">Entrar</button>
        </form>
    </section>
</main>
</body>
</html>
