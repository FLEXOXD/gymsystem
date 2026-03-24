@php
    $quotePlanCatalog = [];

    foreach ((array) ($publicPlanCards ?? []) as $planCard) {
        $planKey = strtolower(trim((string) ($planCard['plan_key'] ?? '')));

        if ($planKey === '') {
            continue;
        }

        $quotePlanCatalog[$planKey] = [
            'label' => trim((string) ($planCard['name'] ?? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $planKey)))),
            'summary' => trim((string) ($planCard['summary'] ?? '')),
            'ideal_for' => trim((string) ($planCard['ideal_for'] ?? '')),
        ];
    }

    $quoteSelectedPlanKey = strtolower(trim((string) old('quote_requested_plan', $quoteSelectedPlan ?? '')));
    $quoteSelectedPlanMeta = $quoteSelectedPlanKey !== '' ? ($quotePlanCatalog[$quoteSelectedPlanKey] ?? null) : null;
    $quoteDefaultPlanLabel = 'Cotización general';
    $quoteDefaultPlanCopy = 'Te ayudamos a elegir el plan correcto según tu tamaño, operación y metas comerciales.';
    $quoteCurrentPlanLabel = trim((string) ($quoteSelectedPlanMeta['label'] ?? $quoteSelectedPlanLabel ?? ''));
    $quoteCurrentPlanLabel = $quoteCurrentPlanLabel !== '' ? $quoteCurrentPlanLabel : $quoteDefaultPlanLabel;
    $quoteCurrentPlanCopy = trim((string) ($quoteSelectedPlanMeta['summary'] ?? $quoteSelectedPlanMeta['ideal_for'] ?? ''));
    $quoteCurrentPlanCopy = $quoteCurrentPlanCopy !== '' ? $quoteCurrentPlanCopy : $quoteDefaultPlanCopy;
    $quoteDefaultFormTitle = 'Completa el formulario y recibe tu cotización personalizada';
    $quoteDefaultFormCopy = 'Cuéntanos cómo opera tu gimnasio hoy y te responderemos con una propuesta clara para tu negocio.';
    $quoteFormTitleText = $quoteSelectedPlanKey !== ''
        ? 'Solicita la cotización de '.$quoteCurrentPlanLabel
        : $quoteDefaultFormTitle;
    $quoteFormCopyText = $quoteSelectedPlanKey !== ''
        ? 'Completa tus datos y te contactaremos con una propuesta enfocada en '.$quoteCurrentPlanLabel.' y la operación real de tu gimnasio.'
        : $quoteDefaultFormCopy;
    $quoteDefaultSubmitLabel = 'Solicitar cotización personalizada';
    $quoteSubmitLabelText = $quoteSelectedPlanKey !== ''
        ? 'Solicitar cotización de '.$quoteCurrentPlanLabel
        : $quoteDefaultSubmitLabel;
    $quoteModalBrandLogoUrl = asset('pwa/flexgymlogo.png');
    $quoteVisualImage = '';

    foreach ([
        trim((string) ($homePageBackgroundUrls[0] ?? '')),
        trim((string) ($homePageBackgroundUrls[1] ?? '')),
        trim((string) ($homePageBackgroundUrls[2] ?? '')),
        trim((string) ($content['hero_slide_1_url'] ?? '')),
        trim((string) ($content['section_1_image_url'] ?? '')),
        trim((string) ($content['section_2_image_url'] ?? '')),
        trim((string) ($content['section_3_image_url'] ?? '')),
        'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1400&q=80',
    ] as $quoteImageCandidate) {
        if ($quoteImageCandidate !== '') {
            $quoteVisualImage = $quoteImageCandidate;
            break;
        }
    }
@endphp

<div id="quote-request-backdrop" class="quote-modal-backdrop {{ $quoteModalOpen ? 'is-open' : '' }}"></div>
<div id="quote-request-modal"
     class="quote-modal {{ $quoteModalOpen ? 'is-open' : '' }}"
     role="dialog"
     aria-modal="true"
     aria-labelledby="quote-request-title">
    <button id="quote-request-close" type="button" class="quote-modal-close" aria-label="Cerrar formulario de cotización">
        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M5 5 15 15M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>

    <div class="quote-modal-shell">
        <section class="quote-modal-side">
            <div class="quote-modal-brand">
                @if ($quoteModalBrandLogoUrl !== '')
                    <span class="quote-modal-brand-logo-wrap">
                        <img src="{{ $quoteModalBrandLogoUrl }}" alt="{{ $brandName }}" class="quote-modal-brand-logo">
                    </span>
                @else
                    <span class="quote-modal-brand-mark">{{ $brandInitials }}</span>
                @endif

                <div>
                    <strong class="quote-modal-brand-name">{{ $brandName }}</strong>
                    <span class="quote-modal-brand-caption">Cotización comercial para gimnasios</span>
                </div>
            </div>

            <div>
                <p class="quote-modal-kicker">Cotización guiada</p>
                <h3 class="quote-modal-title">Una propuesta comercial hecha para la realidad de tu gimnasio.</h3>
                <p class="quote-modal-copy">
                    Cuéntanos si hoy necesitas ordenar caja, membresías, accesos o crecimiento por sedes y te recomendaremos el plan correcto.
                </p>
            </div>

            <div class="quote-modal-photo-frame"
                 @if ($quoteVisualImage !== '')
                     style="background-image: linear-gradient(180deg, rgba(7, 17, 31, 0.12), rgba(7, 17, 31, 0.74)), url('{{ $quoteVisualImage }}');"
                 @endif
                 aria-hidden="true">
                <div class="quote-modal-photo-badge">
                    <span>Atención comercial</span>
                    <strong>9:00 AM a 7:00 PM</strong>
                </div>

                <div class="quote-modal-photo-badge is-secondary">
                    <span>Ubicación</span>
                    <strong>Machachi, cantón Mejía</strong>
                </div>

                <article class="quote-modal-plan-spotlight">
                    <span class="quote-modal-plan-caption">Plan seleccionado</span>
                    <strong data-quote-plan-label data-default-label="{{ $quoteDefaultPlanLabel }}">{{ $quoteCurrentPlanLabel }}</strong>
                    <p data-quote-plan-copy data-default-copy="{{ $quoteDefaultPlanCopy }}">{{ $quoteCurrentPlanCopy }}</p>
                </article>
            </div>

            <ul class="quote-modal-feature-list">
                <li>Respuesta humana por correo, llamada o WhatsApp.</li>
                <li>Cotización ajustada a sedes, personal y nivel operativo.</li>
                <li>Recomendación clara para crecer con orden y mejor control.</li>
            </ul>

            <div class="quote-modal-stat-grid">
                <article class="quote-modal-stat-card">
                    <span>Canales</span>
                    <strong>Correo, llamada o WhatsApp según prefieras.</strong>
                </article>
                <article class="quote-modal-stat-card">
                    <span>Enfoque</span>
                    <strong>Recepción, caja, membresías, accesos y crecimiento.</strong>
                </article>
            </div>
        </section>

        <section class="quote-modal-form-panel">
            <header class="quote-form-header">
                <p class="quote-modal-kicker">Formulario de cotización</p>
                <h3 id="quote-request-title"
                    data-quote-form-title
                    data-default="{{ $quoteDefaultFormTitle }}">{{ $quoteFormTitleText }}</h3>
                <p data-quote-form-copy data-default="{{ $quoteDefaultFormCopy }}">{{ $quoteFormCopyText }}</p>
            </header>

            <div class="quote-form-hint-grid" aria-label="Ventajas de la solicitud comercial">
                <article class="quote-form-hint">
                    <strong>Respuesta humana</strong>
                    <span>Revisamos cada solicitud manualmente.</span>
                </article>
                <article class="quote-form-hint">
                    <strong>Sin compromiso</strong>
                    <span>Puedes cotizar antes de decidir tu siguiente paso.</span>
                </article>
                <article class="quote-form-hint">
                    <strong>Propuesta útil</strong>
                    <span>Adaptada al tamaño real de tu operación.</span>
                </article>
            </div>

            @if ($quoteModalMessage !== '')
                <div class="quote-form-alert {{ $quoteModalType === 'error' ? 'is-error' : 'is-success' }}">
                    <strong>{{ $quoteModalType === 'error' ? 'Revisa la información antes de enviarla.' : 'Tu solicitud ya fue recibida.' }}</strong>
                    <span>{{ $quoteModalMessage }}</span>
                </div>
            @endif

            <div id="quote-plan-pill" class="quote-plan-pill {{ $quoteSelectedPlanKey !== '' ? 'is-visible' : '' }}">
                <span>Interés principal</span>
                <strong data-quote-plan-label data-default-label="{{ $quoteDefaultPlanLabel }}">{{ $quoteCurrentPlanLabel }}</strong>
            </div>

            <form id="landing-quote-form" method="POST" action="{{ route('landing.quote.store') }}">
                @csrf
                <input type="hidden" name="quote_requested_plan" value="{{ old('quote_requested_plan') }}" data-quote-plan-input>
                <input type="hidden" name="quote_source" value="{{ old('quote_source', 'landing_'.$pageMode) }}" data-quote-source-input>

                <div class="quote-form-grid">
                    <label class="quote-form-field">
                        <span class="quote-form-label">Nombre <em>*</em></span>
                        <input type="text"
                               class="contact-input @error('quote_first_name', 'landingQuote') is-invalid @enderror"
                               name="quote_first_name"
                               value="{{ old('quote_first_name') }}"
                               placeholder="Ej: Andrea"
                               autocomplete="given-name"
                               required>
                        @error('quote_first_name', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="quote-form-field">
                        <span class="quote-form-label">Apellido <em>*</em></span>
                        <input type="text"
                               class="contact-input @error('quote_last_name', 'landingQuote') is-invalid @enderror"
                               name="quote_last_name"
                               value="{{ old('quote_last_name') }}"
                               placeholder="Ej: Morales"
                               autocomplete="family-name"
                               required>
                        @error('quote_last_name', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="quote-form-field">
                        <span class="quote-form-label">Teléfono de contacto <em>*</em></span>
                        <span class="quote-form-inline">
                            <select class="contact-input quote-form-prefix @error('quote_phone_country_code', 'landingQuote') is-invalid @enderror"
                                    name="quote_phone_country_code"
                                    data-quote-prefix-select
                                    required>
                                @foreach ($quotePhonePrefixes as $prefix)
                                    <option value="{{ $prefix }}" @selected(old('quote_phone_country_code', '+593') === $prefix)>{{ $prefix }}</option>
                                @endforeach
                            </select>
                            <input type="tel"
                                   class="contact-input @error('quote_phone_number', 'landingQuote') is-invalid @enderror"
                                   name="quote_phone_number"
                                   value="{{ old('quote_phone_number') }}"
                                   placeholder="099 123 4567"
                                   autocomplete="tel"
                                   required>
                        </span>
                        <span class="quote-form-help">Usaremos este número solo para responder a tu solicitud comercial.</span>
                        @error('quote_phone_country_code', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                        @error('quote_phone_number', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="quote-form-field">
                        <span class="quote-form-label">Correo de contacto <em>*</em></span>
                        <input type="email"
                               class="contact-input @error('quote_email', 'landingQuote') is-invalid @enderror"
                               name="quote_email"
                               value="{{ old('quote_email') }}"
                               placeholder="gerencia@tugimnasio.com"
                               autocomplete="email"
                               required>
                        @error('quote_email', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="quote-form-field quote-form-field--full">
                        <span class="quote-form-label">País de operación <em>*</em></span>
                        <select class="contact-input @error('quote_country', 'landingQuote') is-invalid @enderror"
                                name="quote_country"
                                data-quote-country-select
                                required>
                            <option value="">Selecciona tu país</option>
                            @foreach ($quoteCountryPrefixes as $country => $prefix)
                                <option value="{{ $country }}"
                                        data-phone-prefix="{{ $prefix ?? '' }}"
                                        @selected(old('quote_country') === $country)>{{ $country }}</option>
                            @endforeach
                        </select>
                        @error('quote_country', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="quote-form-field quote-form-field--full">
                        <span class="quote-form-label">¿Cuántas personas forman parte de la operación? <em>*</em></span>
                        <input type="number"
                               class="contact-input @error('quote_professionals_count', 'landingQuote') is-invalid @enderror"
                               name="quote_professionals_count"
                               value="{{ old('quote_professionals_count') }}"
                               min="1"
                               max="5000"
                               placeholder="Ej: 8 personas"
                               required>
                        <span class="quote-form-help">Incluye recepción, entrenadores, administración y personal operativo.</span>
                        @error('quote_professionals_count', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="quote-form-field quote-form-field--full">
                        <span class="quote-form-label">¿Qué te gustaría ordenar primero?</span>
                        <textarea class="contact-input @error('quote_notes', 'landingQuote') is-invalid @enderror"
                                  name="quote_notes"
                                  rows="4"
                                  placeholder="Ej: Tenemos una sede, dos turnos en recepción y queremos ordenar membresías, caja y accesos.">{{ old('quote_notes') }}</textarea>
                        @error('quote_notes', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="quote-form-checkbox quote-form-field--full">
                        <label>
                            <input type="checkbox" name="quote_privacy_accepted" value="1" @checked(old('quote_privacy_accepted'))>
                            <span>Acepto el tratamiento de mis datos para recibir mi cotización y seguimiento comercial.</span>
                        </label>
                        <p class="quote-form-legal">Usaremos esta información solo para preparar tu propuesta y coordinar una respuesta comercial personalizada.</p>
                        @error('quote_privacy_accepted', 'landingQuote')
                            <span class="quote-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                        class="btn btn-demo quote-form-submit"
                        data-quote-submit-label
                        data-default="{{ $quoteDefaultSubmitLabel }}">{{ $quoteSubmitLabelText }}</button>
            </form>
        </section>
    </div>
</div>
