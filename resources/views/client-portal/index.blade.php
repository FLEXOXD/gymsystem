@extends('layouts.panel')

@section('title', 'Portal cliente')
@section('page-title', 'Portal cliente')

@section('content')
    @php
        $hostFriendlyUrl = preg_replace('#^https?://#i', '', $clientLoginUrl) ?? $clientLoginUrl;
    @endphp

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)]">
        <x-ui.card title="Enlace de acceso para clientes" subtitle="Compártelo para que cada cliente entre directo a su app móvil.">
            @if (!empty($isGlobalScope))
                <p class="ui-alert ui-alert-warning">
                    Estás en modo global. Este enlace apunta a la sede actual: <strong>{{ $gymName }}</strong>.
                </p>
            @endif

            <div class="portal-link-shell">
                <p class="portal-link-kicker">URL pública del portal cliente</p>
                <p id="portal-link-value" class="portal-link-value">{{ $clientLoginUrl }}</p>
                <p class="portal-link-hint">Ruta rápida: {{ $hostFriendlyUrl }}</p>
            </div>

            <div class="portal-actions-grid mt-4">
                <a href="{{ $clientLoginUrl }}" target="_blank" rel="noopener" class="ui-button ui-button-primary justify-center">Abrir enlace</a>
                <button id="portal-copy-link" type="button" class="ui-button ui-button-secondary justify-center">Copiar enlace</button>
                <button id="portal-native-share" type="button" class="ui-button ui-button-ghost justify-center">Compartir</button>
                <a href="{{ $whatsAppShareUrl }}" target="_blank" rel="noopener" class="ui-button ui-button-ghost justify-center">WhatsApp</a>
                <a href="{{ $facebookShareUrl }}" target="_blank" rel="noopener" class="ui-button ui-button-ghost justify-center">Facebook</a>
                <button id="portal-download-qr" type="button" class="ui-button ui-button-ghost justify-center">Descargar QR</button>
            </div>

            <p id="portal-feedback" class="portal-feedback mt-3">
                Listo para compartir con tus clientes.
            </p>
        </x-ui.card>

        <x-ui.card title="QR de ingreso" subtitle="El cliente lo escanea y abre la pantalla de login automáticamente.">
            <div class="portal-qr-wrap">
                <div id="portal-qr-svg" class="portal-qr-frame">
                    {!! $portalQrSvg !!}
                </div>
            </div>
            <p class="mt-3 text-sm ui-muted">
                Puedes mostrar este QR en recepción o enviarlo por redes.
            </p>
        </x-ui.card>
    </section>

    <x-ui.card title="Mensaje recomendado" subtitle="Texto rápido para enviar por chat, redes o correo.">
        <div class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-900/70">
            <p id="portal-share-message" class="text-sm leading-relaxed text-slate-800 dark:text-slate-100">{{ $shareMessage }}</p>
        </div>
    </x-ui.card>
@endsection

@push('styles')
<style>
    .portal-link-shell {
        border: 1px solid color-mix(in srgb, #22c55e 35%, var(--border));
        background: radial-gradient(circle at 15% 15%, rgb(16 185 129 / 0.18), transparent 45%), color-mix(in srgb, var(--card) 94%, #020617);
        border-radius: 1rem;
        padding: 0.9rem;
    }
    .portal-link-kicker {
        margin: 0 0 0.45rem;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: color-mix(in srgb, var(--muted) 88%, #cbd5e1);
    }
    .portal-link-value {
        margin: 0;
        font-size: clamp(0.9rem, 0.82rem + 0.35vw, 1rem);
        line-height: 1.4;
        font-weight: 800;
        color: color-mix(in srgb, var(--text) 97%, #ffffff);
        word-break: break-word;
    }
    .portal-link-hint {
        margin: 0.5rem 0 0;
        font-size: 0.76rem;
        color: color-mix(in srgb, var(--muted) 92%, #94a3b8);
        word-break: break-word;
    }
    .portal-actions-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.55rem;
    }
    .portal-feedback {
        min-height: 1.45rem;
        font-size: 0.84rem;
        font-weight: 700;
        color: color-mix(in srgb, #10b981 68%, var(--text));
    }
    .portal-qr-wrap {
        display: flex;
        justify-content: center;
    }
    .portal-qr-frame {
        width: min(100%, 320px);
        border-radius: 1rem;
        border: 1px solid color-mix(in srgb, #22c55e 42%, var(--border));
        background: linear-gradient(145deg, rgb(255 255 255 / 0.98), rgb(241 245 249 / 0.95));
        padding: 0.85rem;
        box-shadow: 0 14px 32px rgb(2 6 23 / 0.18);
    }
    .portal-qr-frame svg {
        width: 100%;
        height: auto;
        display: block;
    }
    @media (max-width: 900px) {
        .portal-actions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 560px) {
        .portal-actions-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const portalUrl = @json($clientLoginUrl);
        const shareMessage = @json($shareMessage);
        const gymName = @json($gymName);

        const copyButton = document.getElementById('portal-copy-link');
        const shareButton = document.getElementById('portal-native-share');
        const downloadQrButton = document.getElementById('portal-download-qr');
        const feedback = document.getElementById('portal-feedback');
        const qrContainer = document.getElementById('portal-qr-svg');

        function setFeedback(text, isError) {
            if (!feedback) return;
            feedback.textContent = text;
            feedback.style.color = isError
                ? 'rgb(244 63 94)'
                : 'color-mix(in srgb, #10b981 68%, var(--text))';
        }

        async function copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return;
            }

            const helper = document.createElement('textarea');
            helper.value = text;
            helper.setAttribute('readonly', 'readonly');
            helper.style.position = 'fixed';
            helper.style.opacity = '0';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            document.body.removeChild(helper);
        }

        copyButton?.addEventListener('click', async function () {
            try {
                await copyText(portalUrl);
                setFeedback('Enlace copiado al portapapeles.', false);
            } catch (error) {
                setFeedback('No se pudo copiar. Intenta manualmente.', true);
            }
        });

        if (typeof navigator.share !== 'function') {
            shareButton?.classList.add('hidden');
        } else {
            shareButton?.addEventListener('click', async function () {
                try {
                    await navigator.share({
                        title: 'App cliente - ' + gymName,
                        text: shareMessage,
                        url: portalUrl,
                    });
                    setFeedback('Compartido correctamente.', false);
                } catch (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                    setFeedback('No se pudo abrir el menú de compartir.', true);
                }
            });
        }

        downloadQrButton?.addEventListener('click', function () {
            try {
                const svg = qrContainer?.querySelector('svg');
                if (!svg) {
                    setFeedback('No se encontró el QR para descargar.', true);
                    return;
                }

                const serialized = new XMLSerializer().serializeToString(svg);
                const blob = new Blob([serialized], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'portal-cliente-' + String(gymName || 'gym').toLowerCase().replace(/\s+/g, '-') + '.svg';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                setFeedback('QR descargado.', false);
            } catch (error) {
                setFeedback('No se pudo descargar el QR.', true);
            }
        });
    })();
</script>
@endpush

