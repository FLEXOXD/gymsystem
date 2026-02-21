@extends('layouts.panel')

@section('title', 'Contactarse')
@section('page-title', 'Contactarse')
@push('styles')
    <style>
        .contact-brand-inline {
            width: min(34vw, 240px);
            max-height: 110px;
            flex-shrink: 0;
            pointer-events: none;
        }
        .contact-brand-inline img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .contact-brand-float {
            position: absolute;
            right: 0.3rem;
            bottom: 0.5rem;
            width: min(46vw, 320px);
            max-height: 160px;
            display: block;
            pointer-events: none;
            z-index: 1;
        }
        @media (min-width: 768px) {
            .contact-brand-float {
                right: 0.4rem;
                bottom: 0.4rem;
                width: min(32vw, 380px);
                max-height: 220px;
            }
        }
        .contact-brand-float img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
    </style>
@endpush

@section('content')
    @php
        $label = trim((string) ($contactData['label'] ?? 'Soporte'));
        $email = trim((string) ($contactData['email'] ?? ''));
        $phone = trim((string) ($contactData['phone'] ?? ''));
        $whatsapp = trim((string) ($contactData['whatsapp'] ?? ''));
        $link = trim((string) ($contactData['link'] ?? ''));
        $message = trim((string) ($contactData['message'] ?? ''));
        $logoLightUrl = trim((string) ($contactData['logo_light_url'] ?? ''));
        $logoDarkUrl = trim((string) ($contactData['logo_dark_url'] ?? ''));
        $whatsappDigits = preg_replace('/\D+/', '', $whatsapp ?? '');
        $whatsappUrl = $whatsappDigits !== '' ? 'https://wa.me/'.$whatsappDigits : '';
        $hasAnyChannel = $email !== '' || $phone !== '' || $whatsapp !== '' || $link !== '';
        $hasContactLogo = $logoLightUrl !== '' || $logoDarkUrl !== '';
        $suggestionSubject = old('subject', '');
        $suggestionMessage = old('message', '');
    @endphp

    <x-card title="Canales de contacto" subtitle="Informacion publica definida por SuperAdmin.">
        <div class="relative space-y-4 pb-28 md:pb-36">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--card-muted)] p-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-[220px] flex-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $label !== '' ? $label : 'Soporte' }}</p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">
                            {{ $message !== '' ? $message : 'Escribenos y te ayudamos con tu consulta.' }}
                        </p>
                    </div>

                    @if ($hasContactLogo)
                        <div class="contact-brand-inline" aria-hidden="true">
                            @if ($logoLightUrl !== '')
                                <img src="{{ $logoLightUrl }}" alt="Logo claro" class="dark:hidden">
                            @endif
                            @if ($logoDarkUrl !== '')
                                <img src="{{ $logoDarkUrl }}" alt="Logo oscuro" class="{{ $logoLightUrl !== '' ? 'hidden dark:block' : '' }}">
                            @elseif ($logoLightUrl !== '')
                                <img src="{{ $logoLightUrl }}" alt="Logo" class="hidden dark:block">
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($hasAnyChannel)
                <div class="grid gap-3 md:grid-cols-2">
                    @if ($email !== '')
                        <div class="rounded-xl border border-[var(--border)] p-4">
                            <p class="ui-muted flex items-center gap-2 text-xs font-bold uppercase tracking-wide">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.8" />
                                    <path d="m5 7 7 6 7-6" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                Correo
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $email }}</p>
                            <a class="mt-3 inline-flex ui-button ui-button-ghost px-3 py-2 text-xs font-bold" href="mailto:{{ $email }}">Enviar correo</a>
                        </div>
                    @endif

                    @if ($phone !== '')
                        <div class="rounded-xl border border-[var(--border)] p-4">
                            <p class="ui-muted flex items-center gap-2 text-xs font-bold uppercase tracking-wide">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7.5 4.5h3l1.5 4-2.2 1.3a14 14 0 0 0 4.4 4.4l1.3-2.2 4 1.5v3a2 2 0 0 1-2 2A14.5 14.5 0 0 1 5.5 6.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                Telefono
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $phone }}</p>
                            <a class="mt-3 inline-flex ui-button ui-button-ghost px-3 py-2 text-xs font-bold" href="tel:{{ preg_replace('/\s+/', '', $phone) }}">Llamar</a>
                        </div>
                    @endif

                    @if ($whatsapp !== '')
                        <div class="rounded-xl border border-[var(--border)] p-4">
                            <p class="ui-muted flex items-center gap-2 text-xs font-bold uppercase tracking-wide">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M20 11.5a8 8 0 1 1-14.8 4L4 20l4.7-1.2A8 8 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.8" />
                                    <path d="M9.2 9.1c.2-.4.4-.4.6-.4h.5c.2 0 .4 0 .5.3l.7 1.7c.1.2 0 .4-.1.5l-.3.4c-.1.1-.2.2-.1.4.2.4.7 1.1 1.6 1.7.9.5 1.3.6 1.6.5.2-.1.3-.3.4-.4l.4-.5c.2-.2.4-.2.6-.1l1.6.8c.2.1.3.3.2.6l-.2.9c-.1.3-.2.5-.5.6-.4.1-1 .2-1.7 0-1-.3-2.3-1-3.5-2.1-1.4-1.3-2.2-2.8-2.4-4-.2-.8-.1-1.4.1-1.8Z" fill="currentColor" />
                                </svg>
                                WhatsApp
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $whatsapp }}</p>
                            @if ($whatsappUrl !== '')
                                <a class="mt-3 inline-flex ui-button ui-button-ghost px-3 py-2 text-xs font-bold" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Abrir WhatsApp</a>
                            @endif
                        </div>
                    @endif

                    @if ($link !== '')
                        <div class="rounded-xl border border-[var(--border)] p-4">
                            <p class="ui-muted flex items-center gap-2 text-xs font-bold uppercase tracking-wide">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m10.8 13.2 2.4-2.4M9 15H7a4 4 0 0 1 0-8h3m5 0h2a4 4 0 0 1 0 8h-3" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                Enlace
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100 break-all">{{ $link }}</p>
                            <a class="mt-3 inline-flex ui-button ui-button-ghost px-3 py-2 text-xs font-bold" href="{{ $link }}" target="_blank" rel="noopener">Abrir enlace</a>
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-xl border border-dashed border-[var(--border)] p-4 text-sm text-slate-600 dark:text-slate-300">
                    Aun no hay canales de contacto configurados por SuperAdmin.
                </div>
            @endif

            @if (! $isSuperAdminViewer)
                <div class="rounded-xl border border-[var(--border)] bg-[var(--card-muted)] p-4">
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Caja de sugerencias</p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                        Envia mejoras para el sistema. Tu sugerencia llegara al SuperAdmin con los datos de tu gimnasio.
                    </p>

                    <form method="POST" action="{{ route('contact.suggestions.store') }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Asunto</label>
                            <input
                                type="text"
                                name="subject"
                                value="{{ $suggestionSubject }}"
                                maxlength="120"
                                required
                                class="ui-input"
                                placeholder="Ej: Mejorar reportes de asistencia"
                            >
                            @error('subject')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Sugerencia</label>
                            <textarea
                                name="message"
                                rows="4"
                                maxlength="2000"
                                required
                                class="ui-input"
                                placeholder="Describe claramente la mejora que necesitas...">{{ $suggestionMessage }}</textarea>
                            @error('message')
                                <p class="mt-1 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="ui-button ui-button-primary px-3 py-2 text-xs font-bold">Enviar sugerencia</button>
                    </form>
                </div>
            @endif

            @if ($isSuperAdminViewer)
                <div class="rounded-xl border border-sky-300/40 bg-sky-50/70 p-4 dark:border-sky-700/40 dark:bg-sky-900/20">
                    <p class="text-sm font-semibold text-sky-800 dark:text-sky-200">Eres SuperAdmin</p>
                    <p class="mt-1 text-sm text-sky-700 dark:text-sky-300">Puedes editar estos datos desde tu perfil en la seccion "Contacto para clientes".</p>
                    <a href="{{ route('profile.index') }}" class="mt-3 inline-flex ui-button ui-button-primary px-3 py-2 text-xs font-bold">Ir a mi perfil</a>
                </div>
            @endif

            @if ($hasContactLogo)
                <div class="contact-brand-float" aria-hidden="true">
                    @if ($logoLightUrl !== '')
                        <img src="{{ $logoLightUrl }}" alt="Logo claro" class="dark:hidden">
                    @endif
                    @if ($logoDarkUrl !== '')
                        <img src="{{ $logoDarkUrl }}" alt="Logo oscuro" class="{{ $logoLightUrl !== '' ? 'hidden dark:block' : '' }}">
                    @elseif ($logoLightUrl !== '')
                        <img src="{{ $logoLightUrl }}" alt="Logo" class="hidden dark:block">
                    @endif
                </div>
            @endif
        </div>
    </x-card>
@endsection
