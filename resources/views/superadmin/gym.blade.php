@extends('layouts.panel')

@section('title', 'SuperAdmin Gimnasio')
@section('page-title', 'Crear gimnasio')

@section('content')
    @php
        $defaultTimezone = old('gym_timezone', $defaultTimezone ?? 'America/Guayaquil');
        $defaultCurrency = old('gym_currency_code', 'USD');
        $defaultLanguage = old('gym_language_code', 'es');
        $addressCountry = old('gym_address_country', 'ec');
        $addressState = old('gym_address_state', '');
        $addressCity = old('gym_address_city', '');
        $addressLine = old('gym_address_line', '');
        $defaultAdminGender = old('admin_gender', '');
        $defaultAdminBirthDate = old('admin_birth_date', '');
        $defaultAdminIdentificationType = old('admin_identification_type', '');
        $defaultAdminIdentificationNumber = old('admin_identification_number', '');
        $defaultAdminPhoneCountryDial = old('admin_phone_country_dial', '+593');
        $defaultAdminPhoneNumber = old('admin_phone_number', '');
        $planTemplates = $planTemplates ?? collect();
        $defaultPlanTemplateId = old('subscription_plan_template_id', (string) optional($planTemplates->first())->id);
        $defaultSubscriptionCustomPrice = old('subscription_custom_price', '');
        $defaultSubscriptionIntroDiscountByTemplate = old('subscription_intro_discount_percent', []);
        if (! is_array($defaultSubscriptionIntroDiscountByTemplate)) {
            $defaultSubscriptionIntroDiscountByTemplate = [];
        }
        $statesForCountry = $locationCatalog[$addressCountry]['states'] ?? [];
        $citiesForState = $statesForCountry[$addressState] ?? [];
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Alta de gimnasio</span>
                    <h2 class="sa-title">Crea una nueva cuenta con mejor contexto y menos errores de captura.</h2>
                    <p class="sa-subtitle">
                        Reordene el flujo en bloques de negocio: primero sede, después operación, luego plan y por último admin.
                        Así reduces omisiones y es más fácil revisar antes de guardar.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.gyms.index')" variant="secondary">Ver cartera</x-ui.button>
                        <x-ui.button :href="route('superadmin.plan-templates.index')" variant="ghost">Revisar planes</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Se creara automáticamente</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Slug público</strong>
                            <span>Se genera desde el nombre del gimnasio y se usa en rutas como /mi-gym/panel.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Admin principal</strong>
                            <span>Se crea con acceso inicial para operar el panel desde el primer ingreso.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Suscripción inicial</strong>
                            <span>El plan elegido define precio, alcance operativo y experiencia de inicio.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <form method="POST" action="{{ route('superadmin.gyms.store') }}" enctype="multipart/form-data" class="grid gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(300px,0.85fr)]" aria-describedby="gym-create-form-help">
            @csrf

            <p id="gym-create-form-help" class="sr-only">
                Formulario guiado para crear un gimnasio, asignar plan inicial, definir operación base y entregar acceso al admin principal.
            </p>

            @if ($errors->any())
                <div class="xl:col-span-2 ui-alert ui-alert-danger" role="alert" aria-labelledby="gym-create-errors-title">
                    <p id="gym-create-errors-title" class="font-semibold">Hay errores en el alta del gimnasio.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-5">
                <x-ui.card title="1. Datos del gimnasio" subtitle="Ubicación y datos base de la sede principal.">
                    <div class="grid gap-3 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre del gimnasio</label>
                            <input type="text" name="gym_name" value="{{ old('gym_name') }}" class="ui-input" placeholder="Ej: Titan Gym" required>
                            @error('gym_name')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Teléfono</label>
                            <input type="text" name="gym_phone" value="{{ old('gym_phone') }}" class="ui-input" placeholder="+593 999 999 999">
                            @error('gym_phone')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">País</label>
                            <select id="gym-address-country" name="gym_address_country" class="ui-input" required>
                                @foreach ($locationCatalog as $countryCode => $countryMeta)
                                    <option value="{{ $countryCode }}" @selected($addressCountry === $countryCode)>{{ $countryMeta['label'] }}</option>
                                @endforeach
                            </select>
                            @error('gym_address_country')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Provincia / Estado</label>
                            <select id="gym-address-state" name="gym_address_state" class="ui-input" required>
                                <option value="">Selecciona provincia/estado</option>
                                @foreach (array_keys($statesForCountry) as $stateName)
                                    <option value="{{ $stateName }}" @selected($addressState === $stateName)>{{ $stateName }}</option>
                                @endforeach
                            </select>
                            @error('gym_address_state')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Ciudad</label>
                            <select id="gym-address-city" name="gym_address_city" class="ui-input" required>
                                <option value="">Selecciona ciudad</option>
                                @foreach ($citiesForState as $cityName)
                                    <option value="{{ $cityName }}" @selected($addressCity === $cityName)>{{ $cityName }}</option>
                                @endforeach
                            </select>
                            @error('gym_address_city')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-3">
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Dirección (línea)</label>
                            <input type="text" name="gym_address_line" value="{{ $addressLine }}" class="ui-input" placeholder="Barrio, avenida, referencia">
                            @error('gym_address_line')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card title="2. Operación base" subtitle="Zona horaria, moneda e idioma para operar desde el primer día.">
                    <div class="grid gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/50">
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Zona horaria</label>
                            <div class="flex flex-wrap items-center gap-2">
                                <input id="gym-timezone-search" type="text" class="ui-input min-w-[240px] flex-1" placeholder="Buscar por país, ciudad o zona (ej: ecuador, bogota, mexico)">
                                <button id="gym-timezone-detect" type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold">Usar navegador</button>
                            </div>
                            <p id="gym-timezone-hint" class="ui-muted mt-1 text-xs"></p>

                            <select id="gym-timezone-select" name="gym_timezone" class="ui-input mt-2" required>
                                @foreach ($timezoneOptions as $timezoneValue => $timezoneLabel)
                                    <option value="{{ $timezoneValue }}" @selected($defaultTimezone === $timezoneValue)>{{ $timezoneLabel }}</option>
                                @endforeach
                            </select>
                            <p id="gym-timezone-selected" class="ui-muted mt-1 text-xs"></p>
                            @error('gym_timezone')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Moneda</label>
                                <select name="gym_currency_code" class="ui-input" required>
                                    @foreach ($currencyOptions as $currencyCode => $currencyMeta)
                                        <option value="{{ $currencyCode }}" @selected($defaultCurrency === $currencyCode)>{{ $currencyCode }} - {{ $currencyMeta['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('gym_currency_code')
                                    <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Idioma</label>
                                <select name="gym_language_code" class="ui-input" required>
                                    @foreach ($languageOptions as $langCode => $langLabel)
                                        <option value="{{ $langCode }}" @selected($defaultLanguage === $langCode)>{{ $langLabel }}</option>
                                    @endforeach
                                </select>
                                @error('gym_language_code')
                                    <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-ui.card>
                <x-ui.card title="3. Plan inicial" subtitle="Selecciona el paquete con el que inicia la cuenta.">
                    <div class="space-y-4">
                        <div class="grid gap-3 md:grid-cols-2">
                            @forelse ($planTemplates as $template)
                                @php
                                    $templateId = (int) $template->id;
                                    $templateBasePrice = (float) $template->price;
                                    $templateDiscountPercentDefault = 0;
                                    if ($template->discount_price !== null && $templateBasePrice > 0) {
                                        $templateDiscountPrice = (float) $template->discount_price;
                                        if ($templateDiscountPrice >= 0 && $templateDiscountPrice < $templateBasePrice) {
                                            $templateDiscountPercentDefault = (int) round((($templateBasePrice - $templateDiscountPrice) / $templateBasePrice) * 100);
                                        }
                                    }
                                    $introDiscountValue = $defaultSubscriptionIntroDiscountByTemplate[$templateId]
                                        ?? $defaultSubscriptionIntroDiscountByTemplate[(string) $templateId]
                                        ?? $templateDiscountPercentDefault;
                                @endphp
                                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/60 dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-cyan-500/40 dark:hover:bg-cyan-500/10">
                                    <input type="radio"
                                           name="subscription_plan_template_id"
                                           value="{{ $template->id }}"
                                           class="mt-1"
                                           data-plan-template-radio="1"
                                           data-plan-key="{{ (string) ($template->plan_key ?? '') }}"
                                           data-plan-price="{{ number_format((float) $template->price, 2, '.', '') }}"
                                           @checked((string) $defaultPlanTemplateId === (string) $template->id)
                                           required>
                                    <span class="block">
                                        <span class="block text-sm font-black text-slate-900 dark:text-slate-100">{{ $template->name }}</span>
                                        <span class="mt-1 block text-xs text-slate-600 dark:text-slate-300">
                                            {{ \App\Support\PlanDuration::label($template->duration_unit, (int) $template->duration_days, $template->duration_months) }}
                                        </span>
                                        <span class="mt-2 inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            {{ \App\Support\Currency::format((float) $template->price, $appCurrencyCode ?? 'USD') }}
                                        </span>
                                        @if ($template->discount_price !== null)
                                            <span class="mt-2 ml-2 inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                                                Desc. {{ \App\Support\Currency::format((float) $template->discount_price, $appCurrencyCode ?? 'USD') }}
                                            </span>
                                        @endif
                                        <span class="mt-3 block">
                                            <span class="ui-muted mb-1 block text-[11px] font-bold uppercase tracking-wide">Descuento primer mes (%)</span>
                                            <input type="number"
                                                   name="subscription_intro_discount_percent[{{ $templateId }}]"
                                                   value="{{ $introDiscountValue }}"
                                                   min="0"
                                                   max="100"
                                                   step="1"
                                                   class="ui-input h-9 text-sm"
                                                   data-intro-discount-input="1"
                                                   data-plan-template-id="{{ $templateId }}">
                                            <span class="ui-muted mt-1 block text-[11px]">0 sin descuento | 100 primer mes gratis.</span>
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <p class="ui-muted text-xs">No hay planes base activos disponibles.</p>
                            @endforelse
                        </div>
                        @error('subscription_plan_template_id')
                            <p class="text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                        @if ($errors->has('subscription_intro_discount_percent') || $errors->has('subscription_intro_discount_percent.*'))
                            <p class="text-xs font-semibold text-rose-600 dark:text-rose-300">
                                {{ $errors->first('subscription_intro_discount_percent') ?: $errors->first('subscription_intro_discount_percent.*') }}
                            </p>
                        @endif
                        <p id="subscription-plan-feedback" class="sa-filter-note" role="status" aria-live="polite"></p>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/50">
                            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)] md:items-end">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Precio personalizado (solo plan sucursales)</label>
                                    <input id="subscription-custom-price"
                                           type="number"
                                           name="subscription_custom_price"
                                           value="{{ $defaultSubscriptionCustomPrice }}"
                                           step="0.01"
                                           min="0"
                                           class="ui-input"
                                           placeholder="Ej: 149.99"
                                           title="Se aplica solo cuando seleccionas plan sucursales.">
                                    <p class="ui-muted mt-1 text-[11px]">Si eliges otro plan, este campo se ignora.</p>
                                    @error('subscription_custom_price')
                                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card title="4. Admin principal" subtitle="Datos de la persona que recibirá el acceso inicial del gimnasio.">
                    <div class="grid gap-3 lg:grid-cols-3">
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre del admin</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="ui-input" placeholder="Ej: Carlos Perez" required>
                            @error('admin_name')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Correo del admin</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="ui-input" placeholder="admin@gym.com" required>
                            @error('admin_email')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Género del admin</label>
                            <select name="admin_gender" class="ui-input">
                                <option value="" @selected($defaultAdminGender === '')>No especificado</option>
                                <option value="male" @selected($defaultAdminGender === 'male')>Hombre</option>
                                <option value="female" @selected($defaultAdminGender === 'female')>Mujer</option>
                                <option value="other" @selected($defaultAdminGender === 'other')>Otro</option>
                                <option value="prefer_not_say" @selected($defaultAdminGender === 'prefer_not_say')>Prefiero no decir</option>
                            </select>
                            @error('admin_gender')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Fecha de nacimiento</label>
                            <input type="date" name="admin_birth_date" value="{{ $defaultAdminBirthDate }}" class="ui-input" max="{{ now()->format('Y-m-d') }}">
                            @error('admin_birth_date')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Tipo de identificación</label>
                            <select name="admin_identification_type" class="ui-input">
                                <option value="" @selected($defaultAdminIdentificationType === '')>No especificado</option>
                                <option value="cédula" @selected($defaultAdminIdentificationType === 'cédula')>Cédula</option>
                                <option value="dni" @selected($defaultAdminIdentificationType === 'dni')>DNI</option>
                                <option value="passport" @selected($defaultAdminIdentificationType === 'passport')>Pasaporte</option>
                            </select>
                            @error('admin_identification_type')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Cédula / número de identificación</label>
                            <input type="text" name="admin_identification_number" value="{{ $defaultAdminIdentificationNumber }}" class="ui-input" placeholder="Ej: 1726309071">
                            @error('admin_identification_number')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Código de teléfono del admin</label>
                            <input type="text" name="admin_phone_country_dial" value="{{ $defaultAdminPhoneCountryDial }}" class="ui-input" placeholder="+593">
                            @error('admin_phone_country_dial')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Teléfono del admin</label>
                            <input type="text" name="admin_phone_number" value="{{ $defaultAdminPhoneNumber }}" class="ui-input" placeholder="0991234567">
                            @error('admin_phone_number')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-3">
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Foto de perfil del admin (opcional)</label>
                            <input type="file" name="admin_profile_photo" class="ui-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <p class="ui-muted mt-1 text-[11px]">JPG/PNG/WEBP, máximo 15MB.</p>
                            @error('admin_profile_photo')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>
                <x-ui.card title="5. Seguridad de acceso" subtitle="Credenciales iniciales para el primer ingreso del administrador.">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Contraseña del admin</label>
                            <input type="password" name="admin_password" class="ui-input" placeholder="Mínimo 8 caracteres" required>
                            @error('admin_password')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Confirmar contraseña</label>
                            <input type="password" name="admin_password_confirmation" class="ui-input" placeholder="Repite la contraseña" required>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <aside class="space-y-5">
                <x-ui.card title="Resumen operativo" subtitle="Puntos a revisar antes de confirmar la creacion.">
                    <ul class="sa-check-list">
                        <li>Verifica país, ciudad y zona horaria antes de crear el slug y la operación inicial.</li>
                        <li>Revisa si el plan requiere precio personalizado o descuento introductorio.</li>
                        <li>Confirma correo y teléfono del admin para evitar bloqueos en el primer acceso.</li>
                        <li>La cuenta nace lista para operar panel, recepción y cobros según el plan elegido.</li>
                    </ul>
                </x-ui.card>

                <div class="sa-danger-zone">
                    <p class="text-sm font-bold text-rose-900 dark:text-rose-100">Revisión final</p>
                    <p class="mt-2 text-sm leading-6 text-rose-800 dark:text-rose-200">
                        El slug se genera automáticamente y se usara en rutas como <span class="font-semibold">/mi-gym/panel</span>.
                    </p>
                    <div class="mt-4 flex flex-col gap-2">
                        <x-ui.button :href="route('superadmin.gyms.index')" variant="ghost">Cancelar y volver</x-ui.button>
                        <x-ui.button type="submit">Crear gimnasio</x-ui.button>
                    </div>
                </div>
            </aside>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const locationCatalog = @json($locationCatalog);
        const countrySelect = document.getElementById('gym-address-country');
        const stateSelect = document.getElementById('gym-address-state');
        const citySelect = document.getElementById('gym-address-city');

        const selectedStateValue = @json($addressState);
        const selectedCityValue = @json($addressCity);

        function replaceOptions(select, items, placeholder) {
            if (!select) return;
            select.innerHTML = '';

            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = placeholder;
            select.appendChild(placeholderOption);

            items.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                select.appendChild(option);
            });
        }

        function getStates(countryCode) {
            const country = locationCatalog[countryCode] || null;
            if (!country || !country.states) return [];
            return Object.keys(country.states);
        }

        function getCities(countryCode, stateName) {
            const country = locationCatalog[countryCode] || null;
            if (!country || !country.states || !country.states[stateName]) return [];
            return country.states[stateName];
        }

        function syncStates(preferredState, preferredCity) {
            if (!countrySelect || !stateSelect) return;
            const states = getStates(countrySelect.value);
            replaceOptions(stateSelect, states, 'Selecciona provincia/estado');

            if (preferredState && states.includes(preferredState)) {
                stateSelect.value = preferredState;
            }

            syncCities(preferredCity);
        }

        function syncCities(preferredCity) {
            if (!countrySelect || !stateSelect || !citySelect) return;
            const cities = getCities(countrySelect.value, stateSelect.value);
            replaceOptions(citySelect, cities, 'Selecciona ciudad');

            if (preferredCity && cities.includes(preferredCity)) {
                citySelect.value = preferredCity;
            }
        }

        countrySelect?.addEventListener('change', function () {
            syncStates('', '');
        });

        stateSelect?.addEventListener('change', function () {
            syncCities('');
        });

        syncStates(selectedStateValue, selectedCityValue);

        const planTemplateRadios = Array.from(document.querySelectorAll('input[data-plan-template-radio="1"]'));
        const customPriceInput = document.getElementById('subscription-custom-price');
        const introDiscountInputs = new Map();
        document.querySelectorAll('input[data-intro-discount-input="1"]').forEach(function (input) {
            const templateId = String(input.getAttribute('data-plan-template-id') || '').trim();
            if (templateId !== '') {
                introDiscountInputs.set(templateId, input);
            }
        });
        const subscriptionPlanFeedback = document.getElementById('subscription-plan-feedback');
        const formatUsd = function (value) {
            const numeric = Number(value);
            if (!Number.isFinite(numeric)) return '$0.00';
            return '$' + numeric.toFixed(2);
        };
        const syncCustomSubscriptionPriceState = function () {
            if (!customPriceInput) return;
            const selectedRadio = planTemplateRadios.find(function (radio) {
                return radio.checked;
            }) || null;
            const selectedPlanKey = String(selectedRadio?.getAttribute('data-plan-key') || '').toLowerCase();
            const selectedPlanPrice = Number(selectedRadio?.getAttribute('data-plan-price') || '0');
            const selectedTemplateId = String(selectedRadio?.value || '').trim();
            const selectedIntroInput = introDiscountInputs.get(selectedTemplateId) || null;
            let selectedIntroDiscountPercent = Number(selectedIntroInput?.value || '0');
            if (!Number.isFinite(selectedIntroDiscountPercent)) {
                selectedIntroDiscountPercent = 0;
            }
            selectedIntroDiscountPercent = Math.max(0, Math.min(100, Math.round(selectedIntroDiscountPercent)));
            const selectedPlanLabel = selectedRadio
                ? String(selectedRadio.closest('label')?.querySelector('.text-sm.font-black')?.textContent || 'Plan seleccionado').trim()
                : 'Selecciona un plan base para continuar.';
            const canUseCustomPrice = selectedPlanKey === 'sucursales';
            const selectedCustomPrice = Number(customPriceInput.value || '0');
            const effectivePlanPrice = canUseCustomPrice && Number.isFinite(selectedCustomPrice) && selectedCustomPrice > 0
                ? selectedCustomPrice
                : selectedPlanPrice;
            customPriceInput.disabled = !canUseCustomPrice;
            customPriceInput.required = false;
            customPriceInput.classList.toggle('opacity-60', !canUseCustomPrice);
            customPriceInput.classList.toggle('cursor-not-allowed', !canUseCustomPrice);
            customPriceInput.title = canUseCustomPrice
                ? 'Precio personalizado para este cliente con plan sucursales.'
                : 'Se habilita solo cuando eliges plan sucursales.';
            if (!canUseCustomPrice) {
                customPriceInput.value = '';
            }

            const firstMonthPrice = Math.max(0, effectivePlanPrice * (1 - (selectedIntroDiscountPercent / 100)));
            if (subscriptionPlanFeedback) {
                subscriptionPlanFeedback.textContent = selectedRadio
                    ? selectedPlanLabel + ': primer mes ' + formatUsd(firstMonthPrice) + ' (' + selectedIntroDiscountPercent + '% desc.). Desde mes 2: ' + formatUsd(effectivePlanPrice) + '.'
                    : selectedPlanLabel;
            }
        };
        planTemplateRadios.forEach(function (radio) {
            radio.addEventListener('change', syncCustomSubscriptionPriceState);
        });
        introDiscountInputs.forEach(function (input) {
            input.addEventListener('input', syncCustomSubscriptionPriceState);
        });
        customPriceInput?.addEventListener('input', syncCustomSubscriptionPriceState);
        syncCustomSubscriptionPriceState();
        const timezoneSearch = document.getElementById('gym-timezone-search');
        const timezoneSelect = document.getElementById('gym-timezone-select');
        const timezoneDetect = document.getElementById('gym-timezone-detect');
        const timezoneHint = document.getElementById('gym-timezone-hint');
        const timezoneSelected = document.getElementById('gym-timezone-selected');

        const timezoneSource = Array.from(timezoneSelect?.options || []).map(function (option) {
            return {
                value: option.value,
                label: option.textContent || option.value,
                search: (option.value + ' ' + (option.textContent || '')).toLowerCase(),
            };
        });

        function renderTimezoneOptions(query, preferredValue) {
            if (!timezoneSelect) return;
            const normalized = (query || '').trim().toLowerCase();
            const current = preferredValue || timezoneSelect.value;

            const filtered = timezoneSource.filter(function (item) {
                return normalized === '' || item.search.includes(normalized);
            });

            timezoneSelect.innerHTML = '';
            filtered.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                timezoneSelect.appendChild(option);
            });

            const hasCurrent = filtered.some(function (item) {
                return item.value === current;
            });
            if (hasCurrent) {
                timezoneSelect.value = current;
            }

            updateTimezoneLabel();
        }

        function updateTimezoneLabel() {
            if (!timezoneSelect || !timezoneSelected) return;
            const label = timezoneSelect.options[timezoneSelect.selectedIndex]?.textContent || '-';
            timezoneSelected.textContent = 'Seleccionada: ' + label;
        }

        function detectTimezone() {
            try {
                return Intl.DateTimeFormat().resolvedOptions().timeZone || '';
            } catch (error) {
                return '';
            }
        }

        timezoneSearch?.addEventListener('input', function () {
            renderTimezoneOptions(timezoneSearch.value, timezoneSelect?.value || '');
        });

        timezoneSelect?.addEventListener('change', updateTimezoneLabel);

        timezoneDetect?.addEventListener('click', function () {
            const detected = detectTimezone();
            if (!detected) {
                if (timezoneHint) timezoneHint.textContent = 'No se pudo detectar la zona horaria del navegador.';
                return;
            }

            if (timezoneHint) timezoneHint.textContent = 'Detectada en este equipo: ' + detected;
            renderTimezoneOptions('', detected);
        });

        const detectedOnLoad = detectTimezone();
        if (timezoneHint) {
            timezoneHint.textContent = detectedOnLoad
                ? 'Detectada en este equipo: ' + detectedOnLoad
                : 'No se pudo detectar automáticamente la zona horaria.';
        }

        renderTimezoneOptions('', timezoneSelect?.value || '');
    })();
</script>
@endpush
