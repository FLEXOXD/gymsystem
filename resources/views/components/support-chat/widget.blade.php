@props([
    'context' => 'landing',
    'stateUrl' => '#',
    'quickReplyUrl' => '#',
    'messageUrl' => '#',
    'csrfToken' => '',
    'gymName' => '',
    'gymLogoUrl' => '',
    'leadCapture' => false,
    'launcherTitle' => 'Soporte',
    'compactLauncher' => false,
])

@php
    $contextKey = trim((string) $context) !== '' ? trim((string) $context) : 'landing';
    $isGymPanelContext = $contextKey === 'gym_panel';
    $configContext = (array) config('support_chat.contexts.'.$contextKey, []);
    $assistantName = trim((string) ($configContext['assistant_name'] ?? 'Asistente GymSystem'));
    $assistantSubtitle = trim((string) ($configContext['assistant_subtitle'] ?? 'Soporte'));
    $welcomeMessage = trim((string) ($configContext['welcome_message'] ?? 'Hola, te ayudamos enseguida.'));
    $quickReplies = collect((array) ($configContext['quick_replies'] ?? []))
        ->map(static fn (array $item): array => [
            'key' => trim((string) ($item['key'] ?? '')),
            'label' => trim((string) ($item['label'] ?? '')),
        ])
        ->filter(static fn (array $item): bool => $item['key'] !== '' && $item['label'] !== '')
        ->values()
        ->all();
    $pollSeconds = max(3, (int) config('support_chat.polling_interval_seconds', 7));
    $widgetDomId = 'support-chat-'.preg_replace('/[^a-z0-9]+/i', '-', $contextKey).'-'.substr(md5($stateUrl.$messageUrl), 0, 10);
    $desktopBottomOffsetPx = $isGymPanelContext ? 20 : 16;
    $mobileBottomOffsetPx = $isGymPanelContext ? 92 : 10;
@endphp

<div id="{{ $widgetDomId }}"
     class="gs-support-chat-root"
     data-compact-launcher="{{ $compactLauncher ? '1' : '0' }}"
     data-context-type="{{ $isGymPanelContext ? 'gym_panel' : 'landing' }}"
     data-context="{{ $contextKey }}"
     data-state-url="{{ $stateUrl }}"
     data-quick-url="{{ $quickReplyUrl }}"
     data-message-url="{{ $messageUrl }}"
     data-csrf-token="{{ $csrfToken !== '' ? $csrfToken : csrf_token() }}"
     data-poll-seconds="{{ $pollSeconds }}"
     data-lead-capture="{{ $leadCapture ? '1' : '0' }}"
     data-initial-quick='@json($quickReplies)'
     data-welcome-message="{{ $welcomeMessage }}"
     data-assistant-name="{{ $assistantName }}"
     data-assistant-subtitle="{{ $assistantSubtitle }}"
     style="--gs-chat-bottom: {{ $desktopBottomOffsetPx }}px; --gs-chat-bottom-mobile: {{ $mobileBottomOffsetPx }}px;">
    <button type="button" class="gs-support-chat-launcher" aria-label="{{ $launcherTitle }}" data-chat-launcher>
        <span class="gs-support-chat-launcher__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.8"></circle>
                <path d="M5 18.5c0-3 3.2-5 7-5s7 2 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            </svg>
        </span>
        <span class="gs-support-chat-launcher__label">{{ $launcherTitle }}</span>
    </button>

    <section class="gs-support-chat-panel" hidden data-chat-panel>
        <header class="gs-support-chat-header">
            <div class="gs-support-chat-header__identity">
                <div class="gs-support-chat-header__avatar">
                    @if (trim((string) $gymLogoUrl) !== '')
                        <img src="{{ $gymLogoUrl }}" alt="{{ trim((string) $gymName) !== '' ? $gymName : $assistantName }}">
                    @else
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.8"></circle>
                            <path d="M5 18.5c0-3 3.2-5 7-5s7 2 7 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <p class="gs-support-chat-header__name">{{ $assistantName }}</p>
                    <p class="gs-support-chat-header__meta">{{ $assistantSubtitle }}</p>
                    <p class="gs-support-chat-header__presence" data-chat-presence>
                        <span class="dot"></span>
                        Verificando disponibilidad...
                    </p>
                </div>
            </div>
            <button type="button" class="gs-support-chat-close" aria-label="Cerrar chat" data-chat-close>&times;</button>
        </header>

        <div class="gs-support-chat-body">
            @if ($leadCapture)
                <div class="gs-support-chat-lead" data-chat-lead>
                    <p>Para soporte comercial, comparte estos datos:</p>
                    <div class="fields">
                        <input type="text" data-lead-name placeholder="Tu nombre (opcional)" maxlength="120">
                        <input type="email" data-lead-email placeholder="Correo de contacto" maxlength="150">
                        <input type="text" data-lead-gym placeholder="Nombre del gimnasio" maxlength="150">
                    </div>
                </div>
            @endif

            <div class="gs-support-chat-messages" data-chat-messages></div>
            <div class="gs-support-chat-quick" data-chat-quick></div>
            <p class="gs-support-chat-helper" data-chat-helper>{{ $welcomeMessage }}</p>
        </div>

        <footer class="gs-support-chat-footer">
            <input type="text" data-chat-input maxlength="1400" placeholder="Escribe tu mensaje..." autocomplete="off">
            <button type="button" data-chat-send>Enviar</button>
        </footer>
    </section>
</div>

<style>
    .gs-support-chat-root {
        position: fixed;
        right: 16px;
        bottom: var(--gs-chat-bottom, 16px);
        z-index: 120;
        font-family: "Space Grotesk", "Segoe UI", system-ui, sans-serif;
    }
    .gs-support-chat-root[data-context-type="gym_panel"] {
        z-index: 110;
    }
    .gs-support-chat-launcher {
        border: 0;
        border-radius: 999px;
        padding: 0.62rem 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #f8fbff;
        background: linear-gradient(135deg, #0d6dfd 0%, #12a7ff 100%);
        box-shadow: 0 16px 34px rgba(3, 27, 78, 0.36);
        cursor: pointer;
    }
    .gs-support-chat-root[data-compact-launcher="1"] .gs-support-chat-launcher {
        width: 54px;
        height: 54px;
        border-radius: 999px;
        justify-content: center;
        padding: 0;
        gap: 0;
    }
    .gs-support-chat-root[data-compact-launcher="1"] .gs-support-chat-launcher__label {
        display: none;
    }
    .gs-support-chat-launcher:hover {
        transform: translateY(-1px);
    }
    .gs-support-chat-launcher__icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
    }
    .gs-support-chat-launcher__icon svg {
        width: 18px;
        height: 18px;
        color: #f8fbff;
    }
    .gs-support-chat-launcher__label {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .gs-support-chat-panel {
        width: min(380px, calc(100vw - 20px));
        height: min(76vh, 620px);
        margin-top: 10px;
        border-radius: 18px;
        border: 1px solid rgba(18, 32, 60, 0.16);
        background: #f7fbff;
        color: #0f172a;
        box-shadow: 0 20px 52px rgba(15, 23, 42, 0.35);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .gs-support-chat-header {
        padding: 0.8rem 0.9rem;
        display: flex;
        justify-content: space-between;
        gap: 0.6rem;
        align-items: flex-start;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: linear-gradient(145deg, #102b68 0%, #1664b8 45%, #1a91d8 100%);
        color: #f8fbff;
    }
    .gs-support-chat-header__identity {
        display: flex;
        gap: 0.6rem;
        align-items: flex-start;
    }
    .gs-support-chat-header__avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .gs-support-chat-header__avatar img,
    .gs-support-chat-header__avatar svg {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .gs-support-chat-header__avatar svg {
        width: 19px;
        height: 19px;
        color: #f8fbff;
    }
    .gs-support-chat-header__name {
        margin: 0;
        font-weight: 800;
        font-size: 0.92rem;
    }
    .gs-support-chat-header__meta {
        margin: 0.08rem 0 0;
        font-size: 0.74rem;
        opacity: 0.9;
    }
    .gs-support-chat-header__presence {
        margin: 0.14rem 0 0;
        font-size: 0.71rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        opacity: 0.94;
    }
    .gs-support-chat-header__presence .dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: #94a3b8;
    }
    .gs-support-chat-header__presence.is-online .dot {
        background: #22c55e;
    }
    .gs-support-chat-close {
        width: 28px;
        height: 28px;
        border-radius: 9px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.11);
        color: #f8fbff;
        font-size: 16px;
        cursor: pointer;
    }
    .gs-support-chat-body {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        padding: 0.72rem;
        gap: 0.5rem;
    }
    .gs-support-chat-lead {
        border: 1px solid #d5e4ff;
        background: #f0f6ff;
        border-radius: 12px;
        padding: 0.6rem;
    }
    .gs-support-chat-lead p {
        margin: 0 0 0.45rem;
        font-size: 0.73rem;
        color: #334155;
        font-weight: 600;
    }
    .gs-support-chat-lead .fields {
        display: grid;
        gap: 0.38rem;
    }
    .gs-support-chat-lead input {
        width: 100%;
        border: 1px solid #c6d8ff;
        background: #fff;
        border-radius: 8px;
        padding: 0.38rem 0.5rem;
        font-size: 0.74rem;
        color: #0f172a;
    }
    .gs-support-chat-messages {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding-right: 0.2rem;
    }
    .gs-support-chat-bubble {
        display: inline-flex;
        flex-direction: column;
        max-width: 86%;
        border-radius: 12px;
        padding: 0.5rem 0.62rem;
        font-size: 0.78rem;
        line-height: 1.35;
        gap: 0.25rem;
        border: 1px solid transparent;
    }
    .gs-support-chat-bubble small {
        font-size: 0.66rem;
        opacity: 0.72;
    }
    .gs-support-chat-bubble.is-mine {
        align-self: flex-end;
        background: #165dd8;
        color: #f8fbff;
    }
    .gs-support-chat-bubble.is-other {
        align-self: flex-start;
        background: #ffffff;
        border-color: #dbeafe;
        color: #0f172a;
    }
    .gs-support-chat-bubble.is-system {
        align-self: center;
        background: #ebf2ff;
        border-color: #bfd5ff;
        color: #1e3a8a;
        text-align: center;
        max-width: 94%;
    }
    .gs-support-chat-quick {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .gs-support-chat-quick button {
        border: 1px solid #2a69db;
        background: #ffffff;
        color: #1d4ed8;
        border-radius: 999px;
        padding: 0.35rem 0.58rem;
        font-size: 0.7rem;
        font-weight: 700;
        cursor: pointer;
    }
    .gs-support-chat-helper {
        margin: 0;
        color: #334155;
        font-size: 0.7rem;
        min-height: 1rem;
    }
    .gs-support-chat-footer {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.45rem;
        padding: 0.7rem;
        border-top: 1px solid rgba(15, 23, 42, 0.1);
        background: #ffffff;
    }
    .gs-support-chat-footer input {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0.55rem 0.65rem;
        font-size: 0.78rem;
        outline: none;
    }
    .gs-support-chat-footer button {
        border: 0;
        border-radius: 10px;
        padding: 0.52rem 0.78rem;
        background: #0f4bc6;
        color: #f8fbff;
        font-size: 0.74rem;
        font-weight: 800;
        cursor: pointer;
    }
    @media (max-width: 1023px) {
        .gs-support-chat-root[data-context-type="gym_panel"] {
            bottom: calc(var(--gs-chat-bottom-mobile, 92px) + env(safe-area-inset-bottom));
        }
    }
    @media (max-width: 640px) {
        .gs-support-chat-root {
            right: 10px;
            bottom: calc(var(--gs-chat-bottom-mobile, 10px) + env(safe-area-inset-bottom));
        }
        .gs-support-chat-launcher__label {
            display: none;
        }
        .gs-support-chat-panel {
            width: min(100vw - 10px, 390px);
            height: min(78vh, 640px);
        }
    }
</style>

<script>
    (function () {
        const root = document.getElementById(@json($widgetDomId));
        if (!root) return;

        const launcher = root.querySelector('[data-chat-launcher]');
        const panel = root.querySelector('[data-chat-panel]');
        const closeButton = root.querySelector('[data-chat-close]');
        const messagesWrap = root.querySelector('[data-chat-messages]');
        const quickWrap = root.querySelector('[data-chat-quick]');
        const helper = root.querySelector('[data-chat-helper]');
        const input = root.querySelector('[data-chat-input]');
        const sendButton = root.querySelector('[data-chat-send]');
        const presenceEl = root.querySelector('[data-chat-presence]');

        const leadNameInput = root.querySelector('[data-lead-name]');
        const leadEmailInput = root.querySelector('[data-lead-email]');
        const leadGymInput = root.querySelector('[data-lead-gym]');

        const context = String(root.getAttribute('data-context') || 'landing').trim();
        const stateUrl = String(root.getAttribute('data-state-url') || '').trim();
        const quickUrl = String(root.getAttribute('data-quick-url') || '').trim();
        const messageUrl = String(root.getAttribute('data-message-url') || '').trim();
        const csrfToken = String(root.getAttribute('data-csrf-token') || '').trim();
        const pollSeconds = Math.max(3, parseInt(root.getAttribute('data-poll-seconds') || '7', 10) || 7);
        const leadCaptureEnabled = root.getAttribute('data-lead-capture') === '1';
        const initialQuick = JSON.parse(root.getAttribute('data-initial-quick') || '[]');

        let pollTimer = null;
        let panelOpen = false;
        let loading = false;
        let messageCountRendered = 0;

        function setHelper(text, isError) {
            if (!helper) return;
            helper.textContent = String(text || '').trim();
            helper.style.color = isError ? '#b91c1c' : '#334155';
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function headers() {
            return {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            };
        }

        function collectLeadPayload() {
            if (!leadCaptureEnabled) {
                return {};
            }

            return {
                contact_name: leadNameInput ? String(leadNameInput.value || '').trim() : '',
                contact_email: leadEmailInput ? String(leadEmailInput.value || '').trim() : '',
                gym_name: leadGymInput ? String(leadGymInput.value || '').trim() : '',
            };
        }

        function updatePresence(online, representativeName) {
            if (!presenceEl) return;

            const safeName = String(representativeName || 'SuperAdmin').trim();
            if (online) {
                presenceEl.classList.add('is-online');
                presenceEl.innerHTML = '<span class="dot"></span>' + escapeHtml(safeName) + ' conectado';
                return;
            }

            presenceEl.classList.remove('is-online');
            presenceEl.innerHTML = '<span class="dot"></span>Representante no conectado';
        }

        function renderQuickButtons(items) {
            if (!quickWrap) return;

            const list = Array.isArray(items) && items.length > 0 ? items : initialQuick;
            quickWrap.innerHTML = '';

            list.forEach(function (item) {
                const key = String((item && item.key) || '').trim();
                const label = String((item && item.label) || '').trim();
                if (key === '' || label === '') {
                    return;
                }

                const button = document.createElement('button');
                button.type = 'button';
                button.setAttribute('data-action-key', key);
                button.textContent = label;
                quickWrap.appendChild(button);
            });
        }

        function renderMessages(messages) {
            if (!messagesWrap) return;

            const list = Array.isArray(messages) ? messages : [];
            messagesWrap.innerHTML = '';

            list.forEach(function (item) {
                const bubble = document.createElement('div');
                const senderType = String((item && item.sender_type) || '').trim();
                const mine = Boolean(item && item.mine);
                const time = String((item && item.created_at) || '').trim();
                const senderLabel = String((item && item.sender_label) || '').trim();
                const text = String((item && item.text) || '').trim();

                bubble.className = 'gs-support-chat-bubble ' + (mine ? 'is-mine' : 'is-other');
                if (senderType === 'system') {
                    bubble.className = 'gs-support-chat-bubble is-system';
                }

                const labelPart = senderLabel !== '' ? '<strong>' + escapeHtml(senderLabel) + '</strong><br>' : '';
                bubble.innerHTML = labelPart + escapeHtml(text) + (time !== '' ? '<small>' + escapeHtml(time) + '</small>' : '');
                messagesWrap.appendChild(bubble);
            });

            if (list.length !== messageCountRendered) {
                messagesWrap.scrollTop = messagesWrap.scrollHeight;
                messageCountRendered = list.length;
            }
        }

        function applyPayload(payload) {
            const data = payload && typeof payload === 'object' ? payload : {};
            updatePresence(Boolean(data.representative_online), data.representative_name || '');
            renderMessages(data.messages || []);
            renderQuickButtons(data.quick_replies || initialQuick);

            const conversation = data.conversation || null;
            if (!conversation) {
                setHelper(root.getAttribute('data-welcome-message') || 'Selecciona una opción o escribe un mensaje.', false);
                return;
            }

            const statusLabel = String(conversation.status_label || '').trim();
            if (statusLabel !== '') {
                setHelper('Estado: ' + statusLabel, false);
            }
        }

        async function requestJson(url, method, payload) {
            if (url === '') return null;

            const response = await fetch(url, {
                method: method,
                headers: headers(),
                credentials: 'same-origin',
                body: method === 'GET' ? null : JSON.stringify(payload || {}),
            });

            if (!response.ok) {
                return null;
            }

            return response.json();
        }

        async function loadState() {
            if (loading) return;
            loading = true;
            try {
                const payload = await requestJson(stateUrl, 'GET');
                if (!payload) {
                    setHelper('No pudimos cargar el estado del chat.', true);
                    return;
                }

                applyPayload(payload);
            } catch (error) {
                setHelper('Error de conexión. Reintentando...', true);
            } finally {
                loading = false;
            }
        }

        async function sendQuickReply(actionKey) {
            const key = String(actionKey || '').trim();
            if (key === '' || loading) return;

            if (leadCaptureEnabled && key === 'contact_representative') {
                const leadPayload = collectLeadPayload();
                if (String(leadPayload.contact_email || '').trim() === '' || String(leadPayload.gym_name || '').trim() === '') {
                    setHelper('Para pasarte con representante, completa correo y nombre del gimnasio.', true);
                    if (leadEmailInput) {
                        leadEmailInput.focus();
                    }
                    return;
                }
            }

            loading = true;
            try {
                const payload = await requestJson(quickUrl, 'POST', Object.assign({ action_key: key }, collectLeadPayload()));
                if (!payload) {
                    setHelper('No se pudo enviar la opción seleccionada.', true);
                    return;
                }
                applyPayload(payload);
            } catch (error) {
                setHelper('No se pudo enviar la opción. Intenta otra vez.', true);
            } finally {
                loading = false;
            }
        }

        async function sendTextMessage() {
            if (loading || !input) return;

            const text = String(input.value || '').trim();
            if (text === '') {
                return;
            }

            input.value = '';
            loading = true;
            try {
                const payload = await requestJson(
                    messageUrl,
                    'POST',
                    Object.assign({ message: text }, collectLeadPayload())
                );

                if (!payload) {
                    setHelper('No se pudo enviar el mensaje.', true);
                    return;
                }

                applyPayload(payload);
            } catch (error) {
                setHelper('No se pudo enviar. Verifica conexión.', true);
            } finally {
                loading = false;
            }
        }

        function startPolling() {
            stopPolling();
            pollTimer = window.setInterval(function () {
                if (!panelOpen) return;
                loadState();
            }, pollSeconds * 1000);
        }

        function stopPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        function openPanel() {
            if (!panel) return;
            panel.hidden = false;
            panelOpen = true;
            launcher.setAttribute('aria-expanded', 'true');
            loadState();
            startPolling();
        }

        function closePanel() {
            if (!panel) return;
            panel.hidden = true;
            panelOpen = false;
            launcher.setAttribute('aria-expanded', 'false');
        }

        launcher.addEventListener('click', function () {
            if (panelOpen) {
                closePanel();
                return;
            }

            openPanel();
        });

        if (closeButton) {
            closeButton.addEventListener('click', closePanel);
        }

        if (sendButton) {
            sendButton.addEventListener('click', sendTextMessage);
        }

        if (input) {
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    sendTextMessage();
                }
            });
        }

        if (quickWrap) {
            quickWrap.addEventListener('click', function (event) {
                const button = event.target instanceof HTMLElement
                    ? event.target.closest('button[data-action-key]')
                    : null;
                if (!button) return;
                sendQuickReply(button.getAttribute('data-action-key'));
            });
        }

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && panelOpen) {
                loadState();
            }
        });

        window.addEventListener('beforeunload', stopPolling);
        renderQuickButtons(initialQuick);
        loadState();
    })();
</script>

