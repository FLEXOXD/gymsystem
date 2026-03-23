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
            <div>
                <p class="quote-modal-kicker">Cotización personalizada</p>
                <h3 class="quote-modal-title">Recibe una propuesta alineada a la operación real de tu gimnasio.</h3>
                <p class="quote-modal-copy">
                    Analizamos equipo, sedes, recepción, cobros y metas de crecimiento para recomendarte el plan correcto y un siguiente paso claro.
                </p>
            </div>

            <div class="quote-modal-visual" aria-hidden="true">
                <div class="quote-visual-frame">
                    <div class="quote-visual-chip-row">
                        <span class="quote-visual-chip">Recepción</span>
                        <span class="quote-visual-chip">Caja</span>
                        <span class="quote-visual-chip">Membresías</span>
                        <span class="quote-visual-chip">Sucursales</span>
                    </div>

                    <div class="quote-visual-dashboard">
                        <article class="quote-visual-card is-accent">
                            <span class="quote-visual-card-label">Diagnóstico inicial</span>
                            <strong class="quote-visual-card-value">Propuesta clara, sin respuestas genéricas.</strong>
                            <p>Entendemos primero tu contexto para recomendarte el mejor siguiente paso.</p>
                        </article>
                        <article class="quote-visual-card">
                            <span class="quote-visual-card-label">Resultado esperado</span>
                            <strong class="quote-visual-card-value">Plan, alcance y prioridad comercial.</strong>
                            <p>Te guiamos según el tamaño de tu operación y el momento de compra.</p>
                        </article>
                    </div>

                    <ul class="quote-visual-list">
                        <li>Respuesta por correo o WhatsApp, según el canal más conveniente.</li>
                        <li>Recomendación basada en número de colaboradores, país y nivel operativo.</li>
                        <li>Seguimiento pensado para gimnasios, boxes y centros de entrenamiento.</li>
                    </ul>
                </div>

                <div class="quote-modal-stat-grid">
                    <article class="quote-modal-stat-card">
                        <span>Tiempo de respuesta</span>
                        <strong>Primer contacto en horario comercial y con seguimiento humano.</strong>
                    </article>
                    <article class="quote-modal-stat-card">
                        <span>Enfoque</span>
                        <strong>Operación diaria, cobros, membresías, accesos y crecimiento por sedes.</strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="quote-modal-form-panel">
            <header class="quote-form-header">
                <p class="quote-modal-kicker">Solicitud comercial</p>
                <h3 id="quote-request-title">Cuéntanos cómo opera tu gimnasio hoy</h3>
                <p>Con esta información preparamos una propuesta más precisa sobre plan, alcance y acompañamiento inicial.</p>
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

            <div id="quote-plan-pill" class="quote-plan-pill {{ $quoteSelectedPlanLabel !== '' ? 'is-visible' : '' }}">
                <span>Interés principal</span>
                <strong data-quote-plan-label>{{ $quoteSelectedPlanLabel !== '' ? $quoteSelectedPlanLabel : 'General' }}</strong>
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

                <button type="submit" class="btn btn-demo quote-form-submit">Quiero mi propuesta personalizada</button>
            </form>
        </section>
    </div>
</div>
