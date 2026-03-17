<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Suscripcion suspendida</title>
    <style>
        :root {
            color-scheme: light;
            --bg-ink: #0f172a;
            --bg-deep: #020617;
            --bg-blue: #0c4a6e;
            --card-bg: #ffffff;
            --card-border: #f2c4c4;
            --text-main: #0f172a;
            --text-soft: #475569;
            --danger: #9f1d1d;
            --danger-soft: #fef2f2;
            --danger-border: #f59e9e;
            --success-bg: #ecfdf5;
            --success-border: #86efac;
            --success-text: #14532d;
            --error-bg: #fef2f2;
            --error-border: #fca5a5;
            --error-text: #7f1d1d;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: "Segoe UI", sans-serif;
            color: var(--text-main);
            background:
                radial-gradient(circle at 12% 18%, rgba(34, 197, 94, 0.20), rgba(34, 197, 94, 0) 34%),
                radial-gradient(circle at 86% 80%, rgba(56, 189, 248, 0.16), rgba(56, 189, 248, 0) 34%),
                linear-gradient(135deg, var(--bg-deep) 0%, #0b1224 52%, var(--bg-blue) 100%);
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                repeating-linear-gradient(90deg, rgba(148, 163, 184, 0.08) 0 1px, transparent 1px 64px),
                repeating-linear-gradient(0deg, rgba(148, 163, 184, 0.06) 0 1px, transparent 1px 64px);
            opacity: 0.3;
        }

        .wrap {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding:
                max(18px, env(safe-area-inset-top))
                max(18px, env(safe-area-inset-right))
                max(18px, env(safe-area-inset-bottom))
                max(18px, env(safe-area-inset-left));
            position: relative;
            z-index: 1;
        }

        .card {
            width: min(920px, 100%);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 18px 44px rgba(2, 6, 23, 0.24);
        }

        .top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: min(42vw, 300px);
            max-width: 300px;
            min-width: 150px;
            height: auto;
            object-fit: contain;
            object-position: left center;
            display: block;
        }

        .brand-meta p {
            margin: 0;
            color: var(--text-soft);
            font-size: 13px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid var(--danger-border);
            background: var(--danger-soft);
            color: #991b1b;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .title {
            margin: 0 0 6px;
            color: var(--danger);
            font-size: clamp(30px, 5vw, 47px);
            line-height: 1.02;
        }

        .msg {
            margin: 0;
            color: #334155;
            font-size: 18px;
        }

        .meta-kpis {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .kpi {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 7px 10px;
            font-size: 12px;
            color: #334155;
            font-weight: 700;
        }

        .alert {
            border-radius: 12px;
            padding: 10px 12px;
            margin-top: 14px;
            font-size: 14px;
            border: 1px solid transparent;
        }

        .alert-success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .alert-error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error-text);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .box {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            background: #f8fafc;
        }

        .box h3 {
            margin: 0 0 10px;
            color: #0f172a;
            font-size: 16px;
        }

        .line {
            margin: 0 0 8px;
            color: #334155;
            font-size: 14px;
            line-height: 1.45;
        }

        .line strong { color: #0f172a; }

        .steps {
            margin: 0;
            padding-left: 18px;
            color: #334155;
            font-size: 14px;
        }

        .steps li { margin-bottom: 8px; }

        .request-box { grid-column: 1 / -1; }

        .request-form {
            display: grid;
            gap: 8px;
            margin-top: 8px;
        }

        .request-form label {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .request-form textarea {
            width: 100%;
            min-height: 90px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            resize: vertical;
            font: inherit;
            color: #0f172a;
            background: #fff;
        }

        .request-form textarea:focus {
            outline: 2px solid rgba(14, 165, 233, 0.22);
            border-color: #0284c7;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            text-decoration: none;
            color: #fff;
            background: #0f5fbb;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn:hover { filter: brightness(0.95); }
        .btn:disabled { opacity: 0.62; cursor: not-allowed; filter: none; }

        .btn-ghost { background: #475569; }
        .btn-email { background: #0f766e; }
        .btn-wa { background: #15803d; }
        .btn-link { background: #7c3aed; }
        .btn-request { background: #0ea5e9; width: fit-content; }

        @media (max-width: 860px) {
            .grid { grid-template-columns: 1fr; }
            .request-box { grid-column: auto; }
            .title { font-size: clamp(28px, 10vw, 39px); }
            .msg { font-size: 16px; }
            .logo { max-width: 220px; min-width: 124px; }
        }
    </style>
</head>
<body>
@php
    $contactData = $contactData ?? [];
    $logoLightUrl = (string) ($contactData['logo_light_url'] ?? '');
    $logoDarkUrl = (string) ($contactData['logo_dark_url'] ?? '');
    $logoUrl = $logoLightUrl !== '' ? $logoLightUrl : $logoDarkUrl;
    $supportLabel = (string) ($contactData['label'] ?? 'Soporte');
    $supportEmail = trim((string) ($contactData['email'] ?? ''));
    $supportPhone = trim((string) ($contactData['phone'] ?? ''));
    $supportMessage = trim((string) ($contactData['message'] ?? 'Escribenos para activar tu servicio.'));
    $supportLink = trim((string) ($contactData['link'] ?? ''));
    $pendingReactivationRequestAt = trim((string) ($pendingReactivationRequestAt ?? ''));
    $hasPendingReactivation = $pendingReactivationRequestAt !== '';
@endphp
<div class="wrap">
    <section class="card">
        <div class="top">
            <div class="brand">
                @if ($logoUrl !== '')
                    <img class="logo" src="{{ $logoUrl }}" alt="Logo de soporte">
                @endif
                <div class="brand-meta">
                    <p><strong>{{ $supportLabel }}</strong></p>
                    <p>{{ $gymName ?? 'Gym' }}</p>
                    <p>Ultima validacion: {{ $nowLabel ?? now()->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <span class="badge">Pago pendiente</span>
        </div>

        <h1 class="title">Suscripcion suspendida</h1>
        <p class="msg">Tu acceso al panel esta bloqueado por falta de pago. Regulariza tu suscripcion y pulsa "Actualizar" para reingresar.</p>

        <div class="meta-kpis">
            <span class="kpi">Estado: suspendida</span>
            <span class="kpi">Canal rapido: solicitud directa al SuperAdmin</span>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid">
            <section class="box">
                <h3>Soporte SuperAdmin</h3>
                <p class="line"><strong>Mensaje:</strong> {{ $supportMessage }}</p>
                @if ($supportEmail !== '')
                    <p class="line"><strong>Correo:</strong> {{ $supportEmail }}</p>
                @endif
                @if ($supportPhone !== '')
                    <p class="line"><strong>Telefono:</strong> {{ $supportPhone }}</p>
                @endif
            </section>

            <section class="box">
                <h3>Que hacer ahora</h3>
                <ol class="steps">
                    <li>Realiza o confirma tu pago con soporte.</li>
                    <li>Envia la solicitud de activacion desde aqui.</li>
                    <li>Espera confirmacion del SuperAdmin.</li>
                    <li>Pulsa "Actualizar" para volver al panel.</li>
                </ol>
            </section>

            <section class="box request-box">
                <h3>Solicitar activacion al SuperAdmin</h3>
                <p class="line">Si ya pagaste, envia esta solicitud para que te reactiven mas rapido.</p>
                @if ($hasPendingReactivation)
                    <p class="line"><strong>Solicitud pendiente desde:</strong> {{ $pendingReactivationRequestAt }}</p>
                @endif

                <form method="POST" action="{{ route('subscription.reactivation.request') }}" class="request-form">
                    @csrf
                    <label for="reactivation_message">Mensaje adicional (opcional)</label>
                    <textarea
                        id="reactivation_message"
                        name="reactivation_message"
                        maxlength="600"
                        placeholder="Ejemplo: Ya realice el pago, por favor activar mi cuenta."
                    >{{ old('reactivation_message') }}</textarea>
                    <button class="btn btn-request" type="submit" @disabled($hasPendingReactivation)>
                        {{ $hasPendingReactivation ? 'Solicitud pendiente' : 'Solicitar activacion' }}
                    </button>
                </form>
            </section>
        </div>

        <div class="actions">
            <a class="btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesion</a>
            <a class="btn btn-ghost" href="{{ $updateUrl ?? route('login') }}">Actualizar</a>
            @if ($supportEmail !== '')
                <a class="btn btn-email" href="mailto:{{ $supportEmail }}">Enviar correo</a>
            @endif
            @if (! empty($whatsappUrl))
                <a class="btn btn-wa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">WhatsApp</a>
            @endif
            @if ($supportLink !== '')
                <a class="btn btn-link" href="{{ $supportLink }}" target="_blank" rel="noopener">Canal de soporte</a>
            @endif
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </section>
</div>
</body>
</html>
