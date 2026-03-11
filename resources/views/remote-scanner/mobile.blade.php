<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escaner remoto</title>
    <style>
        :root {
            color-scheme: dark;
            --bg-0: #06111d;
            --bg-1: #0b1730;
            --bg-2: #0e223f;
            --line: rgba(125, 211, 252, 0.22);
            --text-0: #f8fafc;
            --text-1: #cbd5e1;
            --ok: #34d399;
            --warn: #fbbf24;
            --bad: #fb7185;
            --accent: #22d3ee;
            --accent-2: #60a5fa;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at top, rgba(34, 211, 238, 0.16), transparent 32%),
                linear-gradient(180deg, var(--bg-1), var(--bg-0));
            color: var(--text-0);
        }

        .scanner-shell {
            width: min(100%, 720px);
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .scanner-card {
            border: 1px solid var(--line);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(11, 23, 48, 0.96), rgba(6, 17, 29, 0.98));
            box-shadow: 0 24px 70px rgba(2, 6, 23, 0.55);
            overflow: hidden;
        }

        .scanner-header {
            padding: 22px 20px 14px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        }

        .scanner-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(34, 211, 238, 0.34);
            color: #a5f3fc;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .scanner-title {
            margin: 14px 0 6px;
            font-size: clamp(28px, 7vw, 40px);
            line-height: 0.96;
            font-weight: 900;
        }

        .scanner-copy {
            margin: 0;
            color: var(--text-1);
            font-size: 15px;
            line-height: 1.55;
        }

        .scanner-grid {
            display: grid;
            gap: 14px;
            padding: 18px 20px 20px;
        }

        .scanner-meta {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .scanner-stat {
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.72);
            padding: 14px;
        }

        .scanner-stat span {
            display: block;
            color: #94a3b8;
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 800;
        }

        .scanner-stat strong {
            display: block;
            margin-top: 8px;
            font-size: 16px;
            line-height: 1.3;
        }

        .scanner-video-wrap {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            border: 1px solid rgba(96, 165, 250, 0.22);
            background: #020617;
            aspect-ratio: 3 / 4;
        }

        .scanner-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .scanner-frame {
            pointer-events: none;
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
        }

        .scanner-frame::before {
            content: "";
            width: min(72vw, 280px);
            height: min(44vw, 170px);
            border-radius: 22px;
            border: 2px solid rgba(34, 211, 238, 0.9);
            box-shadow: 0 0 0 999px rgba(2, 6, 23, 0.28);
        }

        .scanner-row {
            display: grid;
            gap: 10px;
        }

        .scanner-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .scanner-button {
            appearance: none;
            border: 0;
            border-radius: 16px;
            padding: 14px 16px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            transition: transform .18s ease, opacity .18s ease, box-shadow .18s ease;
        }

        .scanner-button:active { transform: scale(0.98); }
        .scanner-button-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #04233d;
            box-shadow: 0 14px 34px rgba(34, 211, 238, 0.25);
        }
        .scanner-button-secondary {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: var(--text-0);
        }

        .scanner-input {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.88);
            color: var(--text-0);
            padding: 14px 16px;
            font-size: 16px;
            outline: none;
        }

        .scanner-status {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 700;
            background: rgba(15, 23, 42, 0.85);
        }

        .scanner-status[data-tone="ok"] {
            border-color: rgba(52, 211, 153, 0.45);
            color: #d1fae5;
            background: rgba(6, 95, 70, 0.28);
        }

        .scanner-status[data-tone="warn"] {
            border-color: rgba(251, 191, 36, 0.45);
            color: #fde68a;
            background: rgba(120, 53, 15, 0.25);
        }

        .scanner-status[data-tone="bad"] {
            border-color: rgba(251, 113, 133, 0.45);
            color: #fecdd3;
            background: rgba(127, 29, 29, 0.26);
        }

        .scanner-last {
            color: var(--text-1);
            font-size: 13px;
        }

        @media (max-width: 640px) {
            .scanner-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $contextLabel = $session->context === 'sales' ? 'Ventas e inventario' : 'Productos';
    @endphp
    <main class="scanner-shell">
        <section class="scanner-card">
            <header class="scanner-header">
                <span class="scanner-badge">Escaner remoto</span>
                <h1 class="scanner-title">{{ $contextLabel }}</h1>
                <p class="scanner-copy">Usa la camara del celular para escanear productos y mandarlos en tiempo real a la computadora.</p>
            </header>

            <div class="scanner-grid">
                <div class="scanner-meta">
                    <article class="scanner-stat">
                        <span>Gimnasio</span>
                        <strong>{{ $session->gym?->name ?? 'GymSystem' }}</strong>
                    </article>
                    <article class="scanner-stat">
                        <span>Sesion</span>
                        <strong>{{ strtoupper(substr(str_replace('-', '', $session->channel_token), 0, 6)) }}</strong>
                    </article>
                </div>

                <div class="scanner-video-wrap">
                    <video id="remote-scan-video" class="scanner-video" autoplay muted playsinline></video>
                    <div class="scanner-frame"></div>
                </div>

                <div id="remote-scan-status" class="scanner-status" data-tone="warn">
                    Pulsa "Activar camara" para empezar a escanear.
                </div>

                <div class="scanner-actions">
                    <button type="button" id="remote-scan-start" class="scanner-button scanner-button-primary">Activar camara</button>
                    <button type="button" id="remote-scan-stop" class="scanner-button scanner-button-secondary">Detener</button>
                </div>

                <div class="scanner-row">
                    <input id="remote-scan-manual" type="text" class="scanner-input" placeholder="Codigo manual, SKU o barcode">
                    <button type="button" id="remote-scan-submit" class="scanner-button scanner-button-secondary">Enviar codigo manual</button>
                </div>

                <p id="remote-scan-last" class="scanner-last">Ultimo envio: ninguno.</p>
            </div>
        </section>
    </main>

    <script>
        (function () {
            const captureUrl = @json($captureUrl);
            const video = document.getElementById('remote-scan-video');
            const startButton = document.getElementById('remote-scan-start');
            const stopButton = document.getElementById('remote-scan-stop');
            const manualInput = document.getElementById('remote-scan-manual');
            const manualSubmit = document.getElementById('remote-scan-submit');
            const statusEl = document.getElementById('remote-scan-status');
            const lastEl = document.getElementById('remote-scan-last');

            let stream = null;
            let detector = null;
            let frame = null;
            let lockedUntil = 0;

            function setStatus(message, tone) {
                if (!statusEl) return;
                statusEl.textContent = message;
                statusEl.setAttribute('data-tone', tone || 'warn');
            }

            function setLast(message) {
                if (!lastEl) return;
                lastEl.textContent = message;
            }

            async function sendCode(code, source) {
                const normalized = (code || '').toString().trim().toUpperCase();
                if (normalized === '') {
                    setStatus('Escribe o escanea un codigo valido.', 'bad');
                    return false;
                }

                try {
                    const response = await fetch(captureUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                        },
                        body: new URLSearchParams({
                            code: normalized,
                            source: source || 'camera',
                        }),
                    });

                    const payload = await response.json().catch(function () { return {}; });
                    if (!response.ok || !payload.ok) {
                        setStatus(payload.message || 'No pude enviar el codigo a la computadora.', 'bad');
                        return false;
                    }

                    if (navigator.vibrate) {
                        navigator.vibrate(120);
                    }

                    setStatus('Codigo enviado en tiempo real a la computadora.', 'ok');
                    setLast('Ultimo envio: ' + normalized + ' | ' + new Date().toLocaleTimeString());
                    return true;
                } catch (error) {
                    setStatus('Error de red enviando el codigo. Intenta otra vez.', 'bad');
                    return false;
                }
            }

            async function scanLoop() {
                if (!detector || !video || video.readyState < 2) {
                    frame = requestAnimationFrame(scanLoop);
                    return;
                }

                if (Date.now() < lockedUntil) {
                    frame = requestAnimationFrame(scanLoop);
                    return;
                }

                try {
                    const results = await detector.detect(video);
                    if (results.length > 0) {
                        const value = (results[0].rawValue || '').trim();
                        if (value !== '') {
                            lockedUntil = Date.now() + 1100;
                            await sendCode(value, 'camera');
                        }
                    }
                } catch (error) {
                    setStatus('No pude leer el codigo. Ajusta el enfoque o la luz.', 'warn');
                }

                frame = requestAnimationFrame(scanLoop);
            }

            function stopScanner() {
                if (frame) {
                    cancelAnimationFrame(frame);
                    frame = null;
                }

                if (stream) {
                    stream.getTracks().forEach(function (track) { track.stop(); });
                    stream = null;
                }
            }

            async function startScanner() {
                if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) {
                    setStatus('Este navegador no soporta escaneo por camara. Usa el campo manual.', 'bad');
                    return;
                }

                try {
                    detector = new window.BarcodeDetector({
                        formats: ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39', 'itf', 'codabar'],
                    });
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                        },
                        audio: false,
                    });
                    video.srcObject = stream;
                    setStatus('Camara activa. Apunta al codigo del producto.', 'ok');
                    frame = requestAnimationFrame(scanLoop);
                } catch (error) {
                    stopScanner();
                    setStatus('No pude abrir la camara. Revisa permisos del navegador.', 'bad');
                }
            }

            startButton?.addEventListener('click', startScanner);
            stopButton?.addEventListener('click', function () {
                stopScanner();
                setStatus('Camara detenida.', 'warn');
            });
            manualSubmit?.addEventListener('click', function () {
                sendCode(manualInput?.value || '', 'manual');
            });
            manualInput?.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    sendCode(manualInput?.value || '', 'manual');
                }
            });

            window.addEventListener('beforeunload', stopScanner);
        })();
    </script>
</body>
</html>
