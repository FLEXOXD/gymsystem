@php
    $contactWhatsappUrl = trim((string) ($content['whatsapp_url'] ?? '#'));
    $contactWhatsappPhoneRaw = trim((string) ($content['whatsapp_phone'] ?? ''));
    $formatMarketingPhone = static function (string $rawPhone): string {
        $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '593') && strlen($digits) === 12) {
            return '+593 '.substr($digits, 3, 3).' '.substr($digits, 6, 3).' '.substr($digits, 9, 3);
        }

        return '+'.$digits;
    };
    $contactWhatsappDisplay = $formatMarketingPhone($contactWhatsappPhoneRaw);
    $contactStatusTitle = $contactModalType === 'error'
        ? 'Revisemos tu solicitud'
        : 'Recibimos tu mensaje';
    $contactStatusFallback = $contactModalType === 'error'
        ? 'Completa los campos pendientes y vuelve a intentarlo. Así podremos responderte con el contexto correcto.'
        : 'Gracias por escribirnos. Nuestro equipo revisará tu solicitud y te responderá con el siguiente paso recomendado para tu gimnasio.';
@endphp

<section id="contacto" class="shell section">
    <div class="contact-shell">
        <div class="contact-hero reveal">
            <div class="contact-hero-copy">
                <p class="contact-kicker">Equipo comercial FlexGym</p>
                <h1 class="contact-hero-title">Conversemos sobre el crecimiento de tu gimnasio.</h1>
                <p class="contact-hero-subtitle">
                    Cuéntanos cómo operas hoy y te ayudamos a definir una propuesta clara para recepción, caja, membresías y control diario, con atención remota para Ecuador y Latinoamérica.
                </p>

                <div class="contact-highlight-list" aria-label="Ventajas de contactarnos">
                    <span class="contact-highlight"><span class="contact-highlight-dot" aria-hidden="true"></span>Respuesta rápida</span>
                    <span class="contact-highlight"><span class="contact-highlight-dot" aria-hidden="true"></span>Atención por WhatsApp</span>
                    <span class="contact-highlight"><span class="contact-highlight-dot" aria-hidden="true"></span>Propuesta personalizada</span>
                </div>

                <div class="contact-hero-actions">
                    @if ($contactWhatsappUrl !== '#')
                        <a href="{{ $contactWhatsappUrl }}" target="_blank" rel="noreferrer" class="btn btn-wa">Hablar por WhatsApp</a>
                    @endif
                    <button class="btn btn-demo"
                            type="button"
                            data-open-quote-modal
                            data-quote-source="contact_hero"
                            aria-controls="quote-request-modal">
                        Solicitar cotización
                    </button>
                </div>

                <p class="contact-hero-note">
                    Tu solicitud llega a un equipo comercial con experiencia en gimnasios de Ecuador y Latinoamérica.
                </p>
            </div>

            <aside class="contact-stage-panel" aria-hidden="true">
                <span class="contact-stage-badge">Ruta comercial clara</span>

                <article class="contact-stage-card contact-stage-card--primary">
                    <span>Desde el primer contacto</span>
                    <strong>Te orientamos según el tamaño y la operación real de tu gimnasio.</strong>
                    <p>Menos respuestas genéricas, más contexto para recomendarte el siguiente paso correcto.</p>
                </article>

                <div class="contact-stage-timeline">
                    <article class="contact-stage-step">
                        <em>01</em>
                        <div>
                            <strong>Compartes tu contexto</strong>
                            <p>Nos dices qué quieres ordenar primero y cómo está operando hoy tu gimnasio.</p>
                        </div>
                    </article>
                    <article class="contact-stage-step">
                        <em>02</em>
                        <div>
                            <strong>Revisamos tu solicitud</strong>
                            <p>Un asesor define el mejor canal para responderte con información útil y accionable.</p>
                        </div>
                    </article>
                    <article class="contact-stage-step">
                        <em>03</em>
                        <div>
                            <strong>Recibes una propuesta clara</strong>
                            <p>Te orientamos sobre plan, alcance y siguiente paso según tu operación real.</p>
                        </div>
                    </article>
                </div>

                <div class="contact-stage-grid">
                    <article class="contact-stage-card">
                        <span>Cobertura</span>
                        <strong>Ecuador y Latinoamérica</strong>
                    </article>
                    <article class="contact-stage-card">
                        <span>Canales</span>
                        <strong>Correo, WhatsApp y cotización guiada</strong>
                    </article>
                </div>
            </aside>
        </div>

        <div class="contact-content-grid">
            <div class="contact-stack reveal">
                <section class="contact-panel">
                    <header class="contact-panel-header">
                        <small>Canales disponibles</small>
                        <h2>Contacta al equipo de FlexGym desde el canal que te resulte más cómodo.</h2>
                        <p>Si necesitas una respuesta inmediata, te recomendamos WhatsApp. Si prefieres detallar tu operación, usa el formulario y te responderemos con contexto.</p>
                    </header>

                    <div class="contact-info-grid">
                        <article class="contact-info-card">
                            <span class="contact-icon contact-icon--wa" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M6 19.2 7 15.8a8 8 0 1 1 3.1 2.8L6 19.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M10.4 9.6c-.2-.4-.5-.4-.7-.4h-.6c-.2 0-.5 0-.7.2-.3.3-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.7 2.8 4.2 3.7 2 .8 2.4.7 2.8.6.4 0 1.3-.5 1.5-1 .2-.5.2-1 .1-1s-.4-.2-.8-.4-1.3-.7-1.5-.8c-.2-.1-.4-.2-.6.2-.2.3-.6.8-.7 1-.1.2-.3.2-.5.1a6.2 6.2 0 0 1-1.8-1.1 7.1 7.1 0 0 1-1.3-1.7c-.1-.2 0-.4.1-.5l.5-.6c.2-.2.2-.4.3-.5 0-.2 0-.4 0-.5l-.4-1Z" fill="currentColor" stroke="none"/>
                                </svg>
                            </span>
                            <div class="contact-info-copy">
                                <span class="contact-info-eyebrow">WhatsApp</span>
                                <h3 class="contact-info-title">{{ $contactWhatsappDisplay !== '' ? $contactWhatsappDisplay : 'Atención directa' }}</h3>
                                <p class="contact-info-text">Ideal para resolver dudas rápidas sobre planes, demo guiada o propuesta comercial.</p>
                                @if ($contactWhatsappUrl !== '#')
                                    <a href="{{ $contactWhatsappUrl }}" target="_blank" rel="noreferrer" class="contact-info-link">Abrir WhatsApp</a>
                                @endif
                            </div>
                        </article>

                        <article class="contact-info-card">
                            <span class="contact-icon contact-icon--mail" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect x="3" y="6" width="18" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m4 8 8 5 8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div class="contact-info-copy">
                                <span class="contact-info-eyebrow">Correo</span>
                                <h3 class="contact-info-title">{{ $footerContactEmail }}</h3>
                                <p class="contact-info-text">Perfecto para solicitudes con más detalle sobre sedes, equipo, procesos o necesidades especiales.</p>
                                <a href="mailto:{{ $footerContactEmail }}" class="contact-info-link">Enviar correo</a>
                            </div>
                        </article>
                        <article class="contact-info-card">
                            <span class="contact-icon contact-icon--map" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </span>
                            <div class="contact-info-copy">
                                <span class="contact-info-eyebrow">Cobertura</span>
                                <h3 class="contact-info-title">Ecuador y Latinoamérica</h3>
                                <p class="contact-info-text">Acompañamos gimnasios, boxes y centros de entrenamiento con atención remota y orientación comercial clara.</p>
                            </div>
                        </article>

                        <article class="contact-info-card">
                            <span class="contact-icon contact-icon--calendar" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect x="4" y="5" width="16" height="15" rx="3" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <div class="contact-info-copy">
                                <span class="contact-info-eyebrow">Disponibilidad</span>
                                <h3 class="contact-info-title">Respuesta en horario comercial</h3>
                                <p class="contact-info-text">Coordinamos seguimiento por correo, WhatsApp o cotización según el tipo de gimnasio y el momento de compra.</p>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="contact-panel contact-panel--trust">
                    <header class="contact-panel-header">
                        <small>Por qué escribirnos aquí</small>
                        <h2>La conversación empieza con contexto, no con respuestas automáticas.</h2>
                    </header>

                    <ul class="contact-trust-list">
                        <li class="contact-trust-item">
                            <strong>Atención especializada en gimnasios.</strong>
                            <span>Entendemos procesos de recepción, caja, membresías, accesos y crecimiento por sedes.</span>
                        </li>
                        <li class="contact-trust-item">
                            <strong>Propuesta ajustada a tu operación.</strong>
                            <span>No enviamos una respuesta genérica: orientamos plan, alcance y siguiente paso según tu contexto real.</span>
                        </li>
                        <li class="contact-trust-item">
                            <strong>Canal claro para seguir la conversación.</strong>
                            <span>Podemos continuar por correo, WhatsApp o cotización guiada, según la urgencia y el nivel de detalle que necesites.</span>
                        </li>
                    </ul>

                    <div class="contact-trust-chip-row" aria-label="Áreas que podemos ayudarte a ordenar">
                        <span class="contact-trust-chip">Recepción</span>
                        <span class="contact-trust-chip">Caja</span>
                        <span class="contact-trust-chip">Membresías</span>
                        <span class="contact-trust-chip">Accesos</span>
                        <span class="contact-trust-chip">Sucursales</span>
                    </div>
                </section>
            </div>

            <section class="contact-form-card reveal">
                <header class="contact-form-header">
                    <small>Formulario comercial</small>
                    <h2>Cuéntanos lo esencial y te responderemos con el siguiente paso recomendado.</h2>
                    <p>Mientras más claro sea tu contexto, más útil será nuestra primera respuesta.</p>
                </header>

                <div class="contact-form-mini-note">
                    <strong>Tip para recibir una mejor orientación.</strong>
                    Incluye en tu mensaje la ciudad, el tamaño de tu equipo y el proceso que quieres mejorar primero.
                </div>

                <form id="landing-contact-form"
                      class="contact-form-grid"
                      method="POST"
                      action="{{ route('landing.contact.store') }}">
                    @csrf

                    <label class="contact-form-field">
                        <span class="contact-label">Nombre <em>*</em></span>
                        <input type="text"
                               class="contact-input @error('first_name', 'landingContact') is-invalid @enderror"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               placeholder="Ej: Andrea"
                               autocomplete="given-name"
                               required>
                        @error('first_name', 'landingContact')
                            <span class="contact-feedback">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="contact-form-field">
                        <span class="contact-label">Apellido <em>*</em></span>
                        <input type="text"
                               class="contact-input @error('last_name', 'landingContact') is-invalid @enderror"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               placeholder="Ej: Morales"
                               autocomplete="family-name"
                               required>
                        @error('last_name', 'landingContact')
                            <span class="contact-feedback">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="contact-form-field contact-form-field--full">
                        <span class="contact-label">Correo de contacto <em>*</em></span>
                        <input type="email"
                               class="contact-input @error('email', 'landingContact') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="gerencia@tugimnasio.com"
                               autocomplete="email"
                               required>
                        <span class="contact-helper">Usaremos este correo para responderte con seguimiento comercial y propuesta inicial.</span>
                        @error('email', 'landingContact')
                            <span class="contact-feedback">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="contact-form-field contact-form-field--full">
                        <span class="contact-label">¿Qué necesitas resolver? <em>*</em></span>
                        <textarea class="contact-input @error('message', 'landingContact') is-invalid @enderror"
                                  name="message"
                                  rows="6"
                                  placeholder="Cuéntanos cuántas personas trabajan en tu gimnasio, si manejas una o varias sedes y qué proceso quieres ordenar primero."
                                  required>{{ old('message') }}</textarea>
                        <span class="contact-helper">Ejemplo: control de membresías, caja, recepción, accesos o crecimiento por sedes.</span>
                        @error('message', 'landingContact')
                            <span class="contact-feedback">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="contact-submit">
                        <button type="submit" class="btn btn-demo">Quiero que me contacten</button>
                        <p class="contact-submit-note">Usaremos esta información únicamente para responder tu solicitud comercial y coordinar el mejor siguiente paso.</p>
                    </div>
                </form>
            </section>
        </div>
    </div>
</section>

<div id="contact-status-backdrop" class="contact-status-backdrop {{ $contactModalMessage !== '' ? 'is-open' : '' }}"></div>
<div id="contact-status-modal"
     class="contact-status-modal {{ $contactModalMessage !== '' ? 'is-open' : '' }} {{ $contactModalType === 'error' ? 'is-error' : '' }}"
     data-variant="{{ $contactModalType }}"
     role="dialog"
     aria-modal="true"
     aria-labelledby="contact-status-title">
    <div class="contact-status-inner">
        <span class="contact-status-badge">{{ $contactModalType === 'error' ? 'Faltan datos por revisar' : 'Solicitud recibida' }}</span>
        <span class="contact-status-icon" aria-hidden="true">
            @if ($contactModalType === 'error')
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 8v5.5M12 17h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.72 3h16.92a2 2 0 0 0 1.72-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            @else
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="m7 12 3.2 3.2L17.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            @endif
        </span>
        <div>
            <h4 id="contact-status-title">{{ $contactStatusTitle }}</h4>
            <p>{{ $contactModalMessage !== '' ? $contactModalMessage : $contactStatusFallback }}</p>
        </div>
        <div class="contact-status-actions">
            @if ($contactModalType !== 'error' && $contactWhatsappUrl !== '#')
                <a class="contact-status-link" href="{{ $contactWhatsappUrl }}" target="_blank" rel="noreferrer">Continuar por WhatsApp</a>
            @endif
            <button id="contact-status-close" type="button" class="contact-status-close">{{ $contactModalType === 'error' ? 'Volver al formulario' : 'Entendido' }}</button>
        </div>
    </div>
</div>
