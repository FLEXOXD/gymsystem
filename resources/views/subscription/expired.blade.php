<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="icon" href="{{ asset('favicon.ico?v=20260322') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/fg-favicon-32.png?v=20260322') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('pwa/fg-favicon-16.png?v=20260322') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/fg-favicon-32.png?v=20260322') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/fg-favicon-180.png?v=20260322') }}">
    <title>Suscripción suspendida</title>
    <style>
        :root {
            color-scheme: light;
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
                max(12px, env(safe-area-inset-top))
                max(12px, env(safe-area-inset-right))
                max(12px, env(safe-area-inset-bottom))
                max(12px, env(safe-area-inset-left));
            position: relative;
            z-index: 1;
        }

        .card {
            width: min(1240px, 100%);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 16px 36px rgba(2, 6, 23, 0.22);
        }

        .top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: min(35vw, 230px);
            max-width: 230px;
            min-width: 140px;
            height: auto;
            object-fit: contain;
            object-position: left center;
            display: block;
        }

        .brand-meta p {
            margin: 0;
            color: var(--text-soft);
            font-size: 12px;
            line-height: 1.35;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid var(--danger-border);
            background: var(--danger-soft);
            color: #991b1b;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 12px;
            align-items: start;
        }

        .headline {
            margin: 0;
            color: var(--danger);
            font-size: clamp(26px, 3.2vw, 46px);
            line-height: 1;
        }

        .msg {
            margin: 6px 0 0;
            color: #334155;
            font-size: 18px;
            line-height: 1.35;
        }

        .meta-kpis {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .kpi {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid #dbe4ef;
            background: #f8fafc;
            padding: 6px 9px;
            font-size: 12px;
            color: #334155;
            font-weight: 700;
        }

        .alert {
            border-radius: 10px;
            padding: 9px 10px;
            margin-top: 10px;
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

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .box {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: #f8fafc;
            min-height: 100%;
        }

        .box h3 {
            margin: 0 0 8px;
            color: #0f172a;
            font-size: 17px;
        }

        .line {
            margin: 0 0 6px;
            color: #334155;
            font-size: 14px;
            line-height: 1.4;
        }

        .line strong { color: #0f172a; }

        .steps {
            margin: 0;
            padding-left: 16px;
            color: #334155;
            font-size: 14px;
        }

        .steps li { margin-bottom: 6px; }

        .request-box h3 {
            margin-top: 0;
            margin-bottom: 6px;
            font-size: 20px;
            color: #0f172a;
        }

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

        .request-form textarea,
        .request-form input[type="file"] {
            width: 100%;
            border-radius: 9px;
            border: 1px solid #cbd5e1;
            padding: 9px 10px;
            font: inherit;
            color: #0f172a;
            background: #fff;
        }

        .request-form textarea {
            min-height: 70px;
            resize: vertical;
        }

        .request-form textarea:focus,
        .request-form input[type="file"]:focus {
            outline: 2px solid rgba(14, 165, 233, 0.22);
            border-color: #0284c7;
        }

        .form-note {
            margin: 0;
            font-size: 12px;
            color: #64748b;
        }

        .form-error {
            margin: 0;
            font-size: 12px;
            color: #b91c1c;
            font-weight: 700;
        }

        .request-cta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 9px;
            padding: 9px 12px;
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
        .btn-request { background: #0ea5e9; }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .quick-actions .btn {
            width: 100%;
            padding: 10px 10px;
        }

        @media (max-width: 1120px) {
            .panel-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .card {
                padding: 12px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .msg {
                font-size: 16px;
            }

            .logo {
                max-width: 190px;
                min-width: 120px;
            }

            .quick-actions {
                grid-template-columns: 1fr 1fr;
            }
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
    $supportMessage = trim((string) ($contactData['message'] ?? 'Escríbenos para activar tu servicio.'));
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
                    <p>Última validación: {{ $nowLabel ?? now()->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <span class="badge">Pago pendiente</span>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="panel-grid">
            <div>
                <h1 class="headline">Suscripción suspendida</h1>
                <p class="msg">Tu acceso al panel está bloqueado por falta de pago. Regulariza tu suscripción y pulsa “Actualizar” para reingresar.</p>

                <div class="meta-kpis">
                    <span class="kpi">Estado: suspendida</span>
                    <span class="kpi">Canal rápido: solicitud directa al SuperAdmin</span>
                </div>

                <div class="info-grid">
                    <section class="box">
                        <h3>Soporte SuperAdmin</h3>
                        <p class="line"><strong>Mensaje:</strong> {{ $supportMessage }}</p>
                        @if ($supportEmail !== '')
                            <p class="line"><strong>Correo:</strong> {{ $supportEmail }}</p>
                        @endif
                        @if ($supportPhone !== '')
                            <p class="line"><strong>Teléfono:</strong> {{ $supportPhone }}</p>
                        @endif
                    </section>

                    <section class="box">
                        <h3>Qué hacer ahora</h3>
                        <ol class="steps">
                            <li>Realiza o confirma tu pago con soporte.</li>
                            <li>Envía la solicitud de activación desde aquí.</li>
                            <li>Espera confirmación del SuperAdmin.</li>
                            <li>Pulsa “Actualizar” para volver al panel.</li>
                        </ol>
                    </section>
                </div>
            </div>

            <section class="box request-box">
                <h3>Solicitar activación al SuperAdmin</h3>
                <p class="line">Si ya pagaste, envía esta solicitud para reactivar más rápido.</p>
                @if ($hasPendingReactivation)
                    <p class="line"><strong>Solicitud pendiente desde:</strong> {{ $pendingReactivationRequestAt }}</p>
                @endif

                <form id="reactivation-form" method="POST" action="{{ route('subscription.reactivation.request') }}" class="request-form" enctype="multipart/form-data">
                    @csrf

                    <label for="reactivation_message">Mensaje adicional (opcional)</label>
                    <textarea
                        id="reactivation_message"
                        name="reactivation_message"
                        maxlength="600"
                        placeholder="Ejemplo: Ya realicé el pago, por favor activar mi cuenta."
                    >{{ old('reactivation_message') }}</textarea>

                    <label for="reactivation_receipt">Comprobante de pago (opcional)</label>
                    <input
                        id="reactivation_receipt"
                        type="file"
                        name="reactivation_receipt"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >
                    <p class="form-note">Sube imagen de transferencia o recibo. Máximo 700 KB.</p>
                    <p id="reactivation-receipt-error" class="form-error" style="display:none;"></p>

                    <div class="request-cta-row">
                        <button class="btn btn-request" type="submit" @disabled($hasPendingReactivation)>
                            {{ $hasPendingReactivation ? 'Solicitud pendiente' : 'Solicitar activación' }}
                        </button>
                    </div>
                </form>

                <div class="quick-actions">
                    <a class="btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</a>
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
            </section>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </section>
</div>
<script>
    (function () {
        var form = document.getElementById('reactivation-form');
        var input = document.getElementById('reactivation_receipt');
        var errorEl = document.getElementById('reactivation-receipt-error');
        if (!form || !input || !errorEl) {
            return;
        }

        var MAX_BYTES = 700 * 1024;
        var processing = false;

        function setError(message) {
            errorEl.textContent = message || '';
            errorEl.style.display = message ? 'block' : 'none';
        }

        function replaceInputFile(file) {
            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        }

        function loadImage(file) {
            return new Promise(function (resolve, reject) {
                var reader = new FileReader();
                reader.onload = function () {
                    var img = new Image();
                    img.onload = function () { resolve(img); };
                    img.onerror = function () { reject(new Error('invalid-image')); };
                    img.src = String(reader.result || '');
                };
                reader.onerror = function () { reject(new Error('read-failed')); };
                reader.readAsDataURL(file);
            });
        }

        function canvasToBlob(canvas, type, quality) {
            return new Promise(function (resolve) {
                canvas.toBlob(function (blob) { resolve(blob); }, type, quality);
            });
        }

        async function shrinkImage(file) {
            if (!(file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/webp')) {
                return file;
            }
            if (file.size <= MAX_BYTES) {
                return file;
            }

            var img = await loadImage(file);
            var width = img.naturalWidth || img.width;
            var height = img.naturalHeight || img.height;
            var maxDimension = 1600;
            var ratio = Math.min(1, maxDimension / Math.max(width, height));
            var canvas = document.createElement('canvas');
            canvas.width = Math.max(1, Math.round(width * ratio));
            canvas.height = Math.max(1, Math.round(height * ratio));
            var ctx = canvas.getContext('2d');
            if (!ctx) {
                return file;
            }
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            var quality = 0.86;
            var blob = await canvasToBlob(canvas, 'image/webp', quality);
            while (blob && blob.size > MAX_BYTES && quality > 0.45) {
                quality -= 0.08;
                blob = await canvasToBlob(canvas, 'image/webp', quality);
            }
            if (!blob || blob.size > MAX_BYTES) {
                return null;
            }

            var safeBase = (file.name || 'comprobante').replace(/\.[^.]+$/, '');
            return new File([blob], safeBase + '.webp', {
                type: 'image/webp',
                lastModified: Date.now()
            });
        }

        async function normalizeSelectedFile() {
            setError('');
            var file = input.files && input.files[0] ? input.files[0] : null;
            if (!file) {
                return true;
            }

            if (file.size <= MAX_BYTES) {
                return true;
            }

            processing = true;
            try {
                var compactFile = await shrinkImage(file);
                if (!compactFile) {
                    setError('La imagen es muy pesada. Usa una menor a 700 KB.');
                    return false;
                }
                if (compactFile.size > MAX_BYTES) {
                    setError('La imagen sigue superando 700 KB. Reduce tamaño e intenta otra vez.');
                    return false;
                }
                replaceInputFile(compactFile);
                return true;
            } catch (error) {
                setError('No pudimos procesar la imagen. Intenta con JPG/PNG/WEBP más liviana.');
                return false;
            } finally {
                processing = false;
            }
        }

        input.addEventListener('change', function () {
            void normalizeSelectedFile();
        });

        form.addEventListener('submit', async function (event) {
            if (processing) {
                event.preventDefault();
                return;
            }
            var ok = await normalizeSelectedFile();
            if (!ok) {
                event.preventDefault();
            }
        });
    })();
</script>
</body>
</html>
