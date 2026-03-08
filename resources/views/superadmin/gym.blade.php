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
        $defaultSubscriptionApplyIntro50 = in_array((string) old('subscription_apply_intro_50', ''), ['1', 'true', 'on'], true);
        $statesForCountry = $locationCatalog[$addressCountry]['states'] ?? [];
        $citiesForState = $statesForCountry[$addressState] ?? [];
    @endphp

    <div class="space-y-5">
        <x-ui.card title="Crear nuevo gimnasio" subtitle="Se crea el gimnasio y su usuario administrador principal.">
            <form method="POST" action="{{ route('superadmin.gyms.store') }}" enctype="multipart/form-data" class="grid gap-3 lg:grid-cols-3">
                @csrf

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre del gimnasio</label>
                    <input type="text" name="gym_name" value="{{ old('gym_name') }}" class="ui-input" placeholder="Ej: Titan Gym" required>
                    @error('gym_name')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Telefono</label>
                    <input type="text" name="gym_phone" value="{{ old('gym_phone') }}" class="ui-input" placeholder="+593 999 999 999">
                    @error('gym_phone')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Pais</label>
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

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Direccion (linea)</label>
                    <input type="text" name="gym_address_line" value="{{ $addressLine }}" class="ui-input" placeholder="Barrio, avenida, referencia">
                    @error('gym_address_line')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-3 rounded-xl border border-[var(--border)] bg-[var(--card-muted)] p-3">
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Zona horaria</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <input id="gym-timezone-search" type="text" class="ui-input min-w-[240px] flex-1" placeholder="Buscar por pais, ciudad o zona (ej: ecuador, bogota, mexico)">
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

                <div class="lg:col-span-3 rounded-xl border border-[var(--border)] bg-[var(--card-muted)] p-3">
                    <label class="ui-muted mb-2 block text-xs font-bold uppercase tracking-wide">Plan inicial del gimnasio</label>
                    <div class="grid gap-2 md:grid-cols-2">
                        @forelse ($planTemplates as $template)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[var(--border)] bg-[var(--card)] p-2.5">
                                <input type="radio"
                                       name="subscription_plan_template_id"
                                       value="{{ $template->id }}"
                                       class="mt-0.5"
                                       data-plan-template-radio="1"
                                       data-plan-key="{{ (string) ($template->plan_key ?? '') }}"
                                       @checked((string) $defaultPlanTemplateId === (string) $template->id)
                                       required>
                                <span class="block">
                                    <span class="block text-sm font-black">{{ $template->name }}</span>
                                    <span class="ui-muted block text-xs">
                                        {{ \App\Support\PlanDuration::label($template->duration_unit, (int) $template->duration_days, $template->duration_months) }}
                                        - {{ \App\Support\Currency::format((float) $template->price, $appCurrencyCode ?? 'USD') }}
                                        @if ($template->discount_price !== null)
                                            | Desc. {{ \App\Support\Currency::format((float) $template->discount_price, $appCurrencyCode ?? 'USD') }}
                                        @endif
                                    </span>
                                </span>
                            </label>
                        @empty
                            <p class="ui-muted text-xs">No hay planes base activos disponibles.</p>
                        @endforelse
                    </div>
                    @error('subscription_plan_template_id')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror

                    <div class="mt-3">
                        <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">
                            Precio personalizado (solo plan sucursales)
                        </label>
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
                        <label class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                            <input id="subscription-apply-intro-50"
                                   type="checkbox"
                                   name="subscription_apply_intro_50"
                                   value="1"
                                   @checked($defaultSubscriptionApplyIntro50)>
                            Aplicar 50% de descuento solo el primer mes.
                        </label>
                        @error('subscription_custom_price')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                        @error('subscription_apply_intro_50')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

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
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Genero del admin</label>
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
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Tipo de identificacion</label>
                    <select name="admin_identification_type" class="ui-input">
                        <option value="" @selected($defaultAdminIdentificationType === '')>No especificado</option>
                        <option value="cedula" @selected($defaultAdminIdentificationType === 'cedula')>Cedula</option>
                        <option value="dni" @selected($defaultAdminIdentificationType === 'dni')>DNI</option>
                        <option value="passport" @selected($defaultAdminIdentificationType === 'passport')>Pasaporte</option>
                    </select>
                    @error('admin_identification_type')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Cedula / numero de identificacion</label>
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
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Telefono del admin</label>
                    <input type="text" name="admin_phone_number" value="{{ $defaultAdminPhoneNumber }}" class="ui-input" placeholder="0991234567">
                    @error('admin_phone_number')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Foto de perfil del admin (opcional)</label>
                    <input type="file" name="admin_profile_photo" class="ui-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <p class="ui-muted mt-1 text-[11px]">JPG/PNG/WEBP, maximo 15MB.</p>
                    @error('admin_profile_photo')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Contrasena del admin</label>
                    <input type="password" name="admin_password" class="ui-input" placeholder="Minimo 8 caracteres" required>
                    @error('admin_password')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Confirmar contrasena</label>
                    <input type="password" name="admin_password_confirmation" class="ui-input" placeholder="Repite la contraseña" required>
                </div>

                <div class="lg:col-span-3 flex items-center justify-between gap-3">
                    <p class="ui-muted text-xs">El slug se genera automaticamente y se usa en URLs tipo <span class="font-semibold">/mi-gym/panel</span>.</p>
                    <x-ui.button type="submit">Crear gimnasio</x-ui.button>
                </div>
            </form>
        </x-ui.card>

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
        const introDiscountCheckbox = document.getElementById('subscription-apply-intro-50');
        const syncCustomSubscriptionPriceState = function () {
            if (!customPriceInput) return;
            const selectedRadio = planTemplateRadios.find(function (radio) {
                return radio.checked;
            }) || null;
            const selectedPlanKey = String(selectedRadio?.getAttribute('data-plan-key') || '').toLowerCase();
            const canUseCustomPrice = selectedPlanKey === 'sucursales';
            customPriceInput.disabled = !canUseCustomPrice;
            customPriceInput.required = false;
            customPriceInput.classList.toggle('opacity-60', !canUseCustomPrice);
            customPriceInput.classList.toggle('cursor-not-allowed', !canUseCustomPrice);
            customPriceInput.title = canUseCustomPrice
                ? 'Precio personalizado para este cliente con plan sucursales.'
                : 'Se habilita solo cuando eliges plan sucursales.';
            if (introDiscountCheckbox) {
                introDiscountCheckbox.disabled = !canUseCustomPrice;
                introDiscountCheckbox.classList.toggle('cursor-not-allowed', !canUseCustomPrice);
                if (!canUseCustomPrice) {
                    introDiscountCheckbox.checked = false;
                }
            }
            if (!canUseCustomPrice) {
                customPriceInput.value = '';
            }
        };
        planTemplateRadios.forEach(function (radio) {
            radio.addEventListener('change', syncCustomSubscriptionPriceState);
        });
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
                : 'No se pudo detectar automaticamente la zona horaria.';
        }

        renderTimezoneOptions('', timezoneSelect?.value || '');

    })();
</script>
@endpush
