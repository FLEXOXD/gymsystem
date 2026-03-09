@if ($legalAcceptanceRequired)
    <section id="legal-acceptance-overlay" class="legal-accept-overlay" aria-modal="true" role="dialog" aria-labelledby="legal-accept-title">
        <div class="legal-accept-dialog">
            <header class="legal-accept-header">
                <h2 id="legal-accept-title">Aceptación de condiciones legales</h2>
                <p>Para continuar debes aceptar una sola vez las condiciones legales vigentes. Esta aceptación queda registrada como respaldo legal.</p>
                <p><strong>Versión vigente:</strong> {{ $legalCurrentVersion }}</p>
            </header>

            <div class="legal-accept-docs">
                @foreach ($legalTermsDocuments as $doc)
                    <article class="legal-accept-doc">
                        <h3>{{ $doc['label'] }}</h3>
                        <p>{{ $doc['summary'] }}</p>
                        <ul>
                            @foreach ($doc['points'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>

            <form id="legal-accept-form" method="POST" action="{{ $legalAcceptancePostUrl }}" class="legal-accept-form">
                @csrf
                <input type="hidden" name="accepted" value="1">
                <input type="hidden" name="terms_version" value="{{ $legalCurrentVersion }}">
                <input type="hidden" name="location_permission" id="legal-location-permission" value="skipped">
                <input type="hidden" name="latitude" id="legal-location-latitude" value="">
                <input type="hidden" name="longitude" id="legal-location-longitude" value="">
                <input type="hidden" name="location_accuracy_m" id="legal-location-accuracy" value="">

                @if ($errors->has('accepted') || $errors->has('terms_version'))
                    <div class="legal-accept-errors">
                        @if ($errors->has('accepted'))
                            <p>{{ $errors->first('accepted') }}</p>
                        @endif
                        @if ($errors->has('terms_version'))
                            <p>{{ $errors->first('terms_version') }}</p>
                        @endif
                    </div>
                @endif

                <label class="legal-accept-check">
                    <input type="checkbox" id="legal-accept-checkbox" required>
                    <span>Confirmo que leí y acepto la Política de privacidad, Condiciones de servicio y Términos comerciales versión {{ $legalCurrentVersion }}.</span>
                </label>

                <div class="legal-accept-actions">
                    <button id="legal-accept-submit" type="submit" class="ui-button ui-button-primary" disabled>Aceptar condiciones</button>
                </div>
            </form>
        </div>
    </section>
@endif

