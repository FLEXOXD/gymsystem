<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Suscripcion suspendida</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background: radial-gradient(circle at 15% 20%, #eef4ff 0%, #edf2f7 35%, #e2e8f0 100%);
            color: #0f172a;
            min-height: 100vh;
            min-height: 100dvh;
        }
        .wrap {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: max(20px, env(safe-area-inset-top)) max(20px, env(safe-area-inset-right)) max(20px, env(safe-area-inset-bottom)) max(20px, env(safe-area-inset-left));
        }
        .card {
            width: min(820px, 100%);
            background: #ffffff;
            border: 1px solid #f1c8c8;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.12);
        }
        .top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
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
            min-width: 160px;
            height: auto;
            object-fit: contain;
            object-position: left center;
            display: block;
        }
        .brand-meta p {
            margin: 0;
            color: #475569;
            font-size: 13px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid #f59e9e;
            background: #fef2f2;
            color: #991b1b;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
        }
        .title {
            margin: 0 0 8px;
            color: #9f1d1d;
            font-size: clamp(30px, 5vw, 48px);
            line-height: 1;
        }
        .msg {
            margin: 0;
            color: #334155;
            font-size: 18px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
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
        }
        .line strong {
            color: #0f172a;
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
        }
        .btn:hover { filter: brightness(0.95); }
        .btn-ghost { background: #475569; }
        .btn-email { background: #0f766e; }
        .btn-wa { background: #15803d; }
        .btn-link { background: #7c3aed; }

        @media (max-width: 820px) {
            .grid { grid-template-columns: 1fr; }
            .title { font-size: clamp(28px, 10vw, 40px); }
            .msg { font-size: 16px; }
            .logo { max-width: 220px; min-width: 130px; }
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
        <p class="msg">Tu acceso al panel esta temporalmente bloqueado por falta de pago. Regulariza tu suscripcion y pulsa "Actualizar" para reingresar.</p>

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
                <p class="line">1) Contacta al soporte para confirmar pago.</p>
                <p class="line">2) Espera la reactivacion.</p>
                <p class="line">3) Pulsa "Actualizar" para volver al panel.</p>
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
