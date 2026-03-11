<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Escaner remoto</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #020617;
            --panel: rgba(15, 23, 42, 0.82);
            --line: rgba(125, 211, 252, 0.26);
            --text-0: #f8fafc;
            --text-1: rgba(226, 232, 240, 0.84);
            --ok: #10b981;
            --warn: #f59e0b;
            --bad: #fb7185;
            --accent: #22d3ee;
            --accent-2: #3b82f6;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
            background: var(--bg);
            font-family: ui-sans-serif, system-ui, sans-serif;
            color: var(--text-0);
            overflow: hidden;
        }

        .scanner-shell {
            position: relative;
            min-height: 100vh;
            background:
                radial-gradient(circle at top, rgba(34, 211, 238, 0.16), transparent 32%),
                linear-gradient(180deg, #0b1730, #020617);
        }

        .scanner-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #020617;
        }

        .scanner-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: calc(14px + env(safe-area-inset-top, 0px)) 14px calc(14px + env(safe-area-inset-bottom, 0px));
            background:
                linear-gradient(180deg, rgba(2, 6, 23, 0.7), rgba(2, 6, 23, 0.18) 28%, rgba(2, 6, 23, 0.46) 100%);
        }

        .scanner-top {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
        }

        .scanner-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(2, 6, 23, 0.62);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            backdrop-filter: blur(16px);
        }

        .scanner-status {
            margin-top: 10px;
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(2, 6, 23, 0.72);
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.35;
            backdrop-filter: blur(18px);
        }

        .scanner-status[data-tone="ok"] {
            border-color: rgba(16, 185, 129, 0.45);
            color: #d1fae5;
        }

        .scanner-status[data-tone="warn"] {
            border-color: rgba(245, 158, 11, 0.45);
            color: #fde68a;
        }

        .scanner-status[data-tone="bad"] {
            border-color: rgba(251, 113, 133, 0.45);
            color: #fecdd3;
        }

        .scanner-target {
            position: relative;
            align-self: center;
            width: min(78vw, 300px);
            height: min(44vw, 180px);
            border-radius: 24px;
            border: 2px solid rgba(34, 211, 238, 0.92);
            box-shadow: 0 0 0 999px rgba(2, 6, 23, 0.22);
        }

        .scanner-target::before,
        .scanner-target::after {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(34, 211, 238, 0.92), transparent);
            animation: scanner-line 2.2s linear infinite;
        }

        .scanner-target::before {
            top: 16px;
        }

        .scanner-target::after {
            bottom: 16px;
            animation-delay: 1.1s;
        }

        @keyframes scanner-line {
            0% { transform: translateY(0); opacity: 0.32; }
            50% { transform: translateY(112px); opacity: 1; }
            100% { transform: translateY(0); opacity: 0.32; }
        }

        .scanner-bottom {
            display: grid;
            gap: 10px;
        }

        .scanner-last {
            margin: 0;
            padding: 12px 14px;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(2, 6, 23, 0.64);
            color: var(--text-1);
            font-size: 13px;
            backdrop-filter: blur(16px);
        }

        .scanner-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .scanner-button {
            appearance: none;
            border: 0;
            min-height: 52px;
            border-radius: 18px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
        }

        .scanner-button-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #082f49;
        }

        .scanner-button-secondary {
            background: rgba(2, 6, 23, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: var(--text-0);
        }

        details.scanner-manual {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(2, 6, 23, 0.68);
            backdrop-filter: blur(16px);
            overflow: hidden;
        }

        details.scanner-manual summary {
            list-style: none;
            cursor: pointer;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 800;
            color: var(--text-1);
        }

        details.scanner-manual summary::-webkit-details-marker {
            display: none;
        }

        .scanner-manual__body {
            display: grid;
            gap: 10px;
            padding: 0 14px 14px;
        }

        .scanner-input {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.92);
            color: var(--text-0);
            padding: 12px 14px;
            font-size: 16px;
            outline: none;
        }

        @media (max-width: 420px) {
            .scanner-pill {
                font-size: 11px;
                letter-spacing: 0.12em;
            }

            .scanner-target {
                width: min(84vw, 300px);
                height: min(52vw, 190px);
            }
        }
    </style>
</head>
<body>
    @php
        $contextLabel = $session->context === 'sales' ? 'Ventas' : 'Productos';
        $sessionCode = strtoupper(substr(str_replace('-', '', $session->channel_token), 0, 6));
    @endphp
    <main class="scanner-shell">
        <video id="remote-scan-video" class="scanner-video" autoplay muted playsinline></video>

        <div class="scanner-overlay">
            <div>
                <div class="scanner-top">
                    <span class="scanner-pill">Escaner en vivo</span>
                    <span class="scanner-pill">{{ $contextLabel }} | {{ $sessionCode }}</span>
                </div>

                <div id="remote-scan-status" class="scanner-status" data-tone="warn">
                    Preparando camara...
                </div>
            </div>

            <div class="scanner-target" aria-hidden="true"></div>

            <div class="scanner-bottom">
                <p id="remote-scan-last" class="scanner-last">Listo para leer codigos.</p>

                <div class="scanner-actions">
                    <button type="button" id="remote-scan-start" class="scanner-button scanner-button-primary">Activar</button>
                    <button type="button" id="remote-scan-stop" class="scanner-button scanner-button-secondary">Pausar</button>
                </div>

                <details id="remote-scan-manual-wrap" class="scanner-manual">
                    <summary>Codigo manual</summary>
                    <div class="scanner-manual__body">
                        <input id="remote-scan-manual" type="text" class="scanner-input" placeholder="SKU o codigo de barras">
                        <button type="button" id="remote-scan-submit" class="scanner-button scanner-button-secondary">Enviar codigo</button>
                    </div>
                </details>
            </div>
        </div>
    </main>

    <script>
        (function () {
            const captureUrl = @json($captureUrl);
            const video = document.getElementById('remote-scan-video');
            const startButton = document.getElementById('remote-scan-start');
            const stopButton = document.getElementById('remote-scan-stop');
            const manualWrap = document.getElementById('remote-scan-manual-wrap');
            const manualInput = document.getElementById('remote-scan-manual');
            const manualSubmit = document.getElementById('remote-scan-submit');
            const statusEl = document.getElementById('remote-scan-status');
            const lastEl = document.getElementById('remote-scan-last');

            let stream = null;
            let detector = null;
            let frame = null;
            let lockedUntil = 0;

            function setStatus(message, tone) {
                if (!statusEl) {
                    return;
                }

                statusEl.textContent = message;
                statusEl.setAttribute('data-tone', tone || 'warn');
            }

            function setLast(message) {
                if (!lastEl) {
                    return;
                }

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
                        if (response.status === 410) {
                            stopScanner();
                        }

                        setStatus(payload.message || 'No pude enviar el codigo.', 'bad');
                        return false;
                    }

                    if (navigator.vibrate) {
                        navigator.vibrate(90);
                    }

                    setStatus('Leido y enviado.', 'ok');
                    setLast('Ultimo codigo: ' + normalized + ' | ' + new Date().toLocaleTimeString());
                    return true;
                } catch (error) {
                    setStatus('Error de red enviando el codigo.', 'bad');
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
                            lockedUntil = Date.now() + 1200;
                            await sendCode(value, 'camera');
                        }
                    }
                } catch (error) {
                    setStatus('No pude leer el codigo. Ajusta enfoque o luz.', 'warn');
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
                    if (manualWrap) {
                        manualWrap.open = true;
                    }

                    setStatus('Tu navegador no soporta camara aqui. Usa codigo manual.', 'bad');
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
                    setStatus('Camara activa. Apunta al codigo.', 'ok');
                    setLast('Escaner listo para lecturas continuas.');
                    frame = requestAnimationFrame(scanLoop);
                } catch (error) {
                    if (manualWrap) {
                        manualWrap.open = true;
                    }

                    stopScanner();
                    setStatus('No pude abrir la camara. Toca Activar o usa codigo manual.', 'bad');
                }
            }

            startButton?.addEventListener('click', startScanner);
            stopButton?.addEventListener('click', function () {
                stopScanner();
                setStatus('Escaner en pausa.', 'warn');
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

            window.addEventListener('load', function () {
                window.setTimeout(function () {
                    startScanner();
                }, 220);
            });

            window.addEventListener('beforeunload', stopScanner);
            window.addEventListener('pagehide', stopScanner);
        })();
    </script>
</body>
</html>
