@php
    $scanContext = (string) ($scanContext ?? 'sales');
    $contextGym = (string) ($contextGym ?? request()->route('contextGym'));
    $schemaReady = (bool) ($schemaReady ?? false);
    $remoteScannerReady = (bool) ($remoteScannerReady ?? (
        \Illuminate\Support\Facades\Schema::hasTable('remote_scan_sessions')
        && \Illuminate\Support\Facades\Schema::hasTable('remote_scan_events')
    ));
    $isGlobalScope = (bool) ($isGlobalScope ?? false);
    $fabTitle = $scanContext === 'products' ? 'Escaner para productos' : 'Escaner para ventas';
    $fabSubtitle = $scanContext === 'products'
        ? 'Escanea desde el celular y refleja el codigo en el formulario de producto.'
        : 'Escanea desde el celular y manda el producto directo a la venta en la computadora.';
    $rotationMessage = 'Este enlace se actualiza el primer día de cada mes. Si el mes termina en 28, 30 o 31, el día 1 se genera uno nuevo.';
@endphp

@if ($schemaReady && $remoteScannerReady && ! $isGlobalScope)
    @push('styles')
    <style>
        .remote-scan-fab {
            position: fixed;
            right: clamp(16px, 2.4vw, 28px);
            bottom: calc(24px + env(safe-area-inset-bottom, 0px));
            z-index: 65;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            padding: 0;
            border-radius: 999px;
            border: 1px solid rgba(34, 211, 238, 0.38);
            background: linear-gradient(180deg, rgba(8, 145, 178, 0.96), rgba(8, 47, 73, 0.98));
            color: #ecfeff;
            box-shadow: 0 16px 42px rgba(6, 24, 44, 0.32);
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .remote-scan-fab::before {
            content: "";
            position: absolute;
            inset: 7px;
            border-radius: inherit;
            border: 1px solid rgba(207, 250, 254, 0.34);
            pointer-events: none;
        }

        .remote-scan-fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 48px rgba(8, 47, 73, 0.42);
        }

        .remote-scan-fab__icon {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 30% 30%, #e0f2fe, #67e8f9 45%, #0891b2 78%);
            color: #042f2e;
            box-shadow: inset 0 0 0 4px rgba(240, 253, 250, 0.78);
            flex-shrink: 0;
        }

        .remote-scan-fab__copy {
            display: none;
        }

        .remote-scan-modal {
            position: fixed;
            inset: 0;
            z-index: 85;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(2, 6, 23, 0.78);
            padding: 18px;
        }

        .remote-scan-modal.is-open {
            display: flex;
        }

        .remote-scan-modal__card {
            width: min(100%, 960px);
            max-height: min(88vh, 820px);
            border-radius: 30px;
            border: 1px solid rgba(34, 211, 238, 0.24);
            background: linear-gradient(180deg, rgba(7, 18, 33, 0.98), rgba(10, 25, 45, 0.98));
            color: #f8fafc;
            box-shadow: 0 28px 70px rgba(2, 6, 23, 0.52);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .remote-scan-modal__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            padding: 22px 22px 16px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        }

        .remote-scan-modal__eyebrow {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(34, 211, 238, 0.28);
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 900;
            color: #a5f3fc;
        }

        .remote-scan-modal__body {
            display: grid;
            gap: 18px;
            padding: 20px 22px 24px;
            grid-template-columns: minmax(280px, 320px) minmax(0, 1fr);
            overflow: auto;
            min-width: 0;
        }

        .remote-scan-modal__panel {
            min-width: 0;
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(15, 23, 42, 0.58);
            padding: 16px;
        }

        .remote-scan-modal__qr {
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.98);
            padding: 18px;
            display: grid;
            place-items: center;
            min-height: 288px;
        }

        .remote-scan-modal__qr svg {
            width: 100%;
            max-width: 236px;
            height: auto;
        }

        .remote-scan-modal__status {
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(15, 23, 42, 0.84);
            padding: 16px;
            font-size: 14px;
            font-weight: 700;
            margin-top: 14px;
        }

        .remote-scan-modal__status[data-tone='ok'] {
            border-color: rgba(52, 211, 153, 0.42);
            color: #d1fae5;
        }

        .remote-scan-modal__status[data-tone='warn'] {
            border-color: rgba(251, 191, 36, 0.42);
            color: #fde68a;
        }

        .remote-scan-modal__status[data-tone='bad'] {
            border-color: rgba(251, 113, 133, 0.42);
            color: #fecdd3;
        }

        .remote-scan-modal__actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .remote-scan-modal__actions .ui-button {
            width: 100%;
            justify-content: center;
        }

        .remote-scan-modal__meta {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 16px;
        }

        .remote-scan-modal__meta article {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(15, 23, 42, 0.72);
            padding: 14px;
            min-width: 0;
        }

        .remote-scan-modal__meta article.is-wide {
            grid-column: 1 / -1;
        }

        .remote-scan-modal__meta span {
            display: block;
            color: #94a3b8;
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            font-weight: 900;
        }

        .remote-scan-modal__meta strong {
            display: block;
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.3;
            word-break: break-word;
        }

        .remote-scan-modal__link {
            width: 100%;
            margin-top: 10px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(2, 6, 23, 0.52);
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .remote-scan-modal__last {
            margin-top: 8px;
            color: #cbd5e1;
            font-size: 13px;
        }

        @media (max-width: 860px) {
            .remote-scan-modal__body {
                grid-template-columns: 1fr;
            }

            .remote-scan-modal__actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .remote-scan-fab {
                width: 68px;
                height: 68px;
                bottom: calc(18px + env(safe-area-inset-bottom, 0px));
            }

            .remote-scan-fab__icon {
                width: 50px;
                height: 50px;
            }

            .remote-scan-modal {
                padding: 12px;
            }

            .remote-scan-modal__card {
                max-height: min(90vh, 860px);
                border-radius: 24px;
            }

            .remote-scan-modal__header {
                padding: 18px 18px 14px;
            }

            .remote-scan-modal__body {
                padding: 16px 18px 20px;
            }

            .remote-scan-modal__qr {
                min-height: 220px;
            }

            .remote-scan-modal__actions,
            .remote-scan-modal__meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    <button type="button"
            id="remote-scan-fab"
            class="remote-scan-fab"
            aria-label="Escanear con celular">
        <span class="remote-scan-fab__icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M4 8V6a2 2 0 0 1 2-2h2M20 8V6a2 2 0 0 0-2-2h-2M4 16v2a2 2 0 0 0 2 2h2M20 16v2a2 2 0 0 1-2 2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M7 12h10M9 9h1M9 15h1M13 9h1M13 15h1M17 9h0M17 15h0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </span>
        <span class="sr-only">Escanear con celular</span>
    </button>

    <div id="remote-scan-modal" class="remote-scan-modal" aria-hidden="true">
        <div class="remote-scan-modal__card">
            <div class="remote-scan-modal__header">
                <div>
                    <span class="remote-scan-modal__eyebrow">Escaner remoto</span>
                    <h2 class="mt-3 text-2xl font-black">{{ $fabTitle }}</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300">{{ $fabSubtitle }}</p>
                </div>
                <button type="button" id="remote-scan-close" class="ui-button ui-button-ghost px-3 py-2 text-sm font-semibold">Cerrar</button>
            </div>

            <div class="remote-scan-modal__body">
                <div class="remote-scan-modal__panel">
                    <div id="remote-scan-qr" class="remote-scan-modal__qr">
                        <p class="text-center text-sm text-slate-500">Generando QR...</p>
                    </div>
                    <div id="remote-scan-status" class="remote-scan-modal__status" data-tone="warn">
                        Genera el QR y escanea desde el celular. La lectura se reflejará en esta pantalla.
                    </div>
                </div>

                <div class="remote-scan-modal__panel">
                    <div class="remote-scan-modal__actions">
                        <button type="button" id="remote-scan-start-session" class="ui-button ui-button-primary px-4 py-2 text-sm font-bold">Generar enlace nuevo</button>
                        <button type="button" id="remote-scan-copy-link" class="ui-button ui-button-secondary px-4 py-2 text-sm font-semibold">Copiar link</button>
                        <button type="button" id="remote-scan-open-link" class="ui-button ui-button-ghost px-4 py-2 text-sm font-semibold">Abrir en celular</button>
                    </div>

                    <div class="remote-scan-modal__meta">
                        <article>
                            <span>Codigo corto</span>
                            <strong id="remote-scan-short-code">-</strong>
                        </article>
                        <article>
                            <span>Expira</span>
                            <strong id="remote-scan-expires">-</strong>
                        </article>
                        <article class="is-wide">
                            <span>Enlace movil</span>
                            <input id="remote-scan-mobile-url" class="remote-scan-modal__link" type="text" readonly value="">
                            <p id="remote-scan-rotation-note" class="remote-scan-modal__last">{{ $rotationMessage }}</p>
                        </article>
                        <article class="is-wide">
                            <span>Ultimo codigo</span>
                            <strong id="remote-scan-last-code">Ninguno aun</strong>
                            <p id="remote-scan-last-time" class="remote-scan-modal__last">Esperando lectura desde el celular.</p>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const fab = document.getElementById('remote-scan-fab');
            const modal = document.getElementById('remote-scan-modal');
            const closeButton = document.getElementById('remote-scan-close');
            const startButton = document.getElementById('remote-scan-start-session');
            const copyButton = document.getElementById('remote-scan-copy-link');
            const openButton = document.getElementById('remote-scan-open-link');
            const qrContainer = document.getElementById('remote-scan-qr');
            const statusEl = document.getElementById('remote-scan-status');
            const shortCodeEl = document.getElementById('remote-scan-short-code');
            const expiresEl = document.getElementById('remote-scan-expires');
            const mobileUrlEl = document.getElementById('remote-scan-mobile-url');
            const rotationNoteEl = document.getElementById('remote-scan-rotation-note');
            const lastCodeEl = document.getElementById('remote-scan-last-code');
            const lastTimeEl = document.getElementById('remote-scan-last-time');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const createUrl = @json(route('remote-scanner.sessions.store', ['contextGym' => $contextGym]));
            const scanContext = @json($scanContext);

            if (!fab || !modal || !statusEl) {
                return;
            }

            let sessionState = null;
            let stream = null;
            let bootstrapped = false;
            let lastDispatchedEventId = 0;
            let lastDispatchedCode = '';
            let lastDispatchedAt = 0;

            function setStatus(message, tone) {
                statusEl.textContent = message;
                statusEl.setAttribute('data-tone', tone || 'warn');
            }

            function setModalOpen(isOpen) {
                modal.classList.toggle('is-open', isOpen);
                modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                document.body.style.overflow = isOpen ? 'hidden' : '';
            }

            async function closeSession() {
                if (!sessionState?.close_url) {
                    return;
                }

                try {
                    await fetch(sessionState.close_url, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        credentials: 'same-origin',
                    });
                } catch (error) {
                    // Ignore close failures for short-lived sessions.
                }
            }

            function stopStream() {
                if (stream) {
                    stream.close();
                    stream = null;
                }
            }

            function resetUi() {
                qrContainer.innerHTML = '<p class="text-center text-sm text-slate-500">Generando QR...</p>';
                shortCodeEl.textContent = '-';
                expiresEl.textContent = '-';
                mobileUrlEl.value = '';
                if (rotationNoteEl) {
                    rotationNoteEl.textContent = @json($rotationMessage);
                }
                lastCodeEl.textContent = 'Ninguno aun';
                lastTimeEl.textContent = 'Esperando lectura desde el celular.';
            }

            function applySession(payload) {
                sessionState = payload;
                qrContainer.innerHTML = payload.qr_svg || '<p class="text-center text-sm text-slate-500">Sin QR disponible.</p>';
                shortCodeEl.textContent = payload.short_code || '-';
                expiresEl.textContent = payload.expires_label || '-';
                mobileUrlEl.value = payload.mobile_url || '';
                if (rotationNoteEl) {
                    rotationNoteEl.textContent = payload.rotation_note || @json($rotationMessage);
                }
                setStatus('Sesión activa del mes. Escanea desde el celular y se reflejará aquí en vivo.', 'ok');
            }

            function attachStream(streamUrl) {
                stopStream();
                stream = new EventSource(streamUrl, { withCredentials: true });

                stream.addEventListener('connected', function () {
                    setStatus('Canal en vivo conectado. Esperando escaneos.', 'ok');
                });

                stream.addEventListener('scan', function (event) {
                    const payload = JSON.parse(event.data || '{}');
                    const eventId = Number(payload.id || 0);
                    const code = (payload.code || '').toString().trim();
                    if (code === '') {
                        return;
                    }

                    const now = Date.now();
                    if (eventId > 0 && eventId <= lastDispatchedEventId) {
                        return;
                    }
                    if (code === lastDispatchedCode && (now - lastDispatchedAt) < 1200) {
                        return;
                    }
                    if (eventId > 0) {
                        lastDispatchedEventId = eventId;
                    }
                    lastDispatchedCode = code;
                    lastDispatchedAt = now;

                    lastCodeEl.textContent = code;
                    lastTimeEl.textContent = 'Ultima lectura: ' + new Date().toLocaleTimeString();
                    setStatus('Codigo recibido en vivo: ' + code, 'ok');
                    window.dispatchEvent(new CustomEvent('remote-scanner:scan', {
                        detail: payload,
                    }));
                });

                stream.addEventListener('close', function () {
                    setStatus('La sesión se cerró o llegó al fin de mes. Genera un enlace nuevo.', 'warn');
                    sessionState = null;
                    stopStream();
                });

                stream.onerror = function () {
                    setStatus('Reconectando canal en vivo...', 'warn');
                };
            }

            async function createSession(forceNew, silent) {
                if (!silent) {
                    setStatus('Abriendo sesion remota...', 'warn');
                }

                try {
                    const response = await fetch(createUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        credentials: 'same-origin',
                        body: new URLSearchParams({
                            context: scanContext,
                            force: forceNew ? '1' : '0',
                        }),
                    });

                    const payload = await response.json().catch(function () { return {}; });
                    if (!response.ok || !payload.ok || !payload.session) {
                        if (!silent) {
                            setStatus(payload.message || 'No pude abrir la sesion remota.', 'bad');
                        }
                        return;
                    }

                    applySession(payload.session);
                    attachStream(payload.session.stream_url);
                } catch (error) {
                    if (!silent) {
                        setStatus('Error creando la sesion remota.', 'bad');
                    }
                }
            }

            fab.addEventListener('click', function () {
                setModalOpen(true);
                if (!sessionState) {
                    resetUi();
                    createSession(false, false);
                    return;
                }

                if (!stream && sessionState.stream_url) {
                    attachStream(sessionState.stream_url);
                }
            });

            closeButton?.addEventListener('click', function () {
                setModalOpen(false);
            });

            modal.addEventListener('click', function (event) {
                if (event.target !== modal) {
                    return;
                }

                setModalOpen(false);
            });

            startButton?.addEventListener('click', function () {
                resetUi();
                createSession(true, false);
            });

            copyButton?.addEventListener('click', async function () {
                if (!sessionState?.mobile_url) {
                    setStatus('Abre primero una sesion para copiar el enlace.', 'warn');
                    return;
                }

                try {
                    await navigator.clipboard.writeText(sessionState.mobile_url);
                    setStatus('Enlace copiado. Abrelo en tu celular.', 'ok');
                } catch (error) {
                    setStatus('No pude copiar el enlace automaticamente.', 'bad');
                }
            });

            openButton?.addEventListener('click', function () {
                if (!sessionState?.mobile_url) {
                    setStatus('Abre primero una sesion para obtener el enlace movil.', 'warn');
                    return;
                }

                window.open(sessionState.mobile_url, '_blank', 'noopener');
            });

            window.addEventListener('beforeunload', function () {
                stopStream();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    setModalOpen(false);
                }
            });

            function bootstrapSessionInBackground() {
                if (bootstrapped) {
                    return;
                }

                bootstrapped = true;
                createSession(false, true);
            }

            bootstrapSessionInBackground();
        })();
    </script>
    @endpush
@endif
