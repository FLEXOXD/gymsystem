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
        $defaultAdminIdentificationType = old('admin_identification_type', '');
        $defaultAdminIdentificationNumber = old('admin_identification_number', '');
        $statesForCountry = $locationCatalog[$addressCountry]['states'] ?? [];
        $citiesForState = $statesForCountry[$addressState] ?? [];
        $gymsWithAdmins = $gymsWithAdmins ?? collect();
        $adminEditErrorKeys = [
            'admin_user_id',
            'admin_name',
            'admin_email',
            'admin_gender',
            'admin_birth_date',
            'admin_identification_type',
            'admin_identification_number',
            'admin_country_iso',
            'admin_address_state',
            'admin_address_city',
            'admin_address_line',
            'admin_phone_country_dial',
            'admin_phone_number',
            'admin_profile_photo',
        ];
        $adminEditHasErrors = $errors->hasAny($adminEditErrorKeys);
        $adminEditOldGymId = (int) old('admin_gym_id', 0);
        $adminEditRouteTemplate = route('superadmin.gyms.admin-user.update', ['gym' => '__GYM__']);
        $adminEditOldData = [
            'hasErrors' => $adminEditHasErrors,
            'gymId' => $adminEditOldGymId,
            'userId' => (int) old('admin_user_id', 0),
            'name' => (string) old('admin_name', ''),
            'email' => (string) old('admin_email', ''),
            'gender' => (string) old('admin_gender', ''),
            'birthDate' => (string) old('admin_birth_date', ''),
            'identificationType' => (string) old('admin_identification_type', ''),
            'identificationNumber' => (string) old('admin_identification_number', ''),
            'countryIso' => strtolower((string) old('admin_country_iso', '')),
            'addressState' => (string) old('admin_address_state', ''),
            'addressCity' => (string) old('admin_address_city', ''),
            'addressLine' => (string) old('admin_address_line', ''),
            'phoneCountryDial' => (string) old('admin_phone_country_dial', ''),
            'phoneNumber' => (string) old('admin_phone_number', ''),
        ];
    @endphp

    <div class="space-y-5">
        <x-ui.card title="Crear nuevo gimnasio" subtitle="Se crea el gimnasio y su usuario administrador principal.">
            <form method="POST" action="{{ route('superadmin.gyms.store') }}" class="grid gap-3 lg:grid-cols-3">
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
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Contrasena del admin</label>
                    <input type="password" name="admin_password" class="ui-input" placeholder="Minimo 8 caracteres" required>
                    @error('admin_password')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Confirmar contrasena</label>
                    <input type="password" name="admin_password_confirmation" class="ui-input" placeholder="Repite la contrasena" required>
                </div>

                <div class="lg:col-span-3 flex items-center justify-between gap-3">
                    <p class="ui-muted text-xs">El slug se genera automaticamente y se usa en URLs tipo <span class="font-semibold">/mi-gym/panel</span>.</p>
                    <x-ui.button type="submit">Crear gimnasio</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Usuarios de gimnasios" subtitle="Edita datos del usuario administrador por gimnasio o elimina el gimnasio completo.">
            <p class="mb-3 text-xs font-semibold text-rose-700 dark:text-rose-300">
                Eliminar gimnasio borrara todo: clientes, planes, membresias, caja, reportes y usuario del gimnasio.
            </p>
            @if ($adminEditHasErrors)
                <div class="mb-3 rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-sm font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200">
                    Revisa los datos del usuario. Hay campos con errores.
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="ui-table min-w-[980px]">
                    <thead>
                    <tr>
                        <th>Gimnasio</th>
                        <th>Admin</th>
                        <th>Correo</th>
                        <th>Genero</th>
                        <th>Cedula / ID</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($gymsWithAdmins as $gymRow)
                        @php
                            $adminUser = $gymRow->users->first();
                            $adminCountryIso = strtolower((string) ($adminUser?->country_iso ?? $gymRow->address_country_code ?? 'ec'));
                            $adminAddressState = (string) ($adminUser?->address_state ?? $gymRow->address_state ?? '');
                            $adminAddressCity = (string) ($adminUser?->address_city ?? $gymRow->address_city ?? '');
                            $adminAddressLine = (string) ($adminUser?->address_line ?? $gymRow->address_line ?? '');
                        @endphp
                        <tr>
                            <td>
                                <p class="font-semibold">{{ $gymRow->name }}</p>
                                <p class="ui-muted text-xs">/{{ $gymRow->slug }}/panel</p>
                            </td>
                            <td>{{ $adminUser?->name ?? '-' }}</td>
                            <td>{{ $adminUser?->email ?? '-' }}</td>
                            <td>{{ $adminUser?->gender ?? '-' }}</td>
                            <td>{{ $adminUser?->identification_number ?? '-' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if ($adminUser)
                                        <button
                                            type="button"
                                            class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold"
                                            data-edit-admin
                                            data-gym-id="{{ (int) $gymRow->id }}"
                                            data-user-id="{{ (int) $adminUser->id }}"
                                            data-admin-name="{{ (string) $adminUser->name }}"
                                            data-admin-email="{{ (string) $adminUser->email }}"
                                            data-admin-gender="{{ (string) ($adminUser->gender ?? '') }}"
                                            data-admin-birth-date="{{ optional($adminUser->birth_date)->format('Y-m-d') }}"
                                            data-admin-identification-type="{{ (string) ($adminUser->identification_type ?? '') }}"
                                            data-admin-identification-number="{{ (string) ($adminUser->identification_number ?? '') }}"
                                            data-admin-country-iso="{{ $adminCountryIso }}"
                                            data-admin-address-state="{{ $adminAddressState }}"
                                            data-admin-address-city="{{ $adminAddressCity }}"
                                            data-admin-address-line="{{ $adminAddressLine }}"
                                            data-admin-phone-country-dial="{{ (string) ($adminUser->phone_country_dial ?? '+593') }}"
                                            data-admin-phone-number="{{ (string) ($adminUser->phone_number ?? '') }}"
                                        >
                                            Editar usuario
                                        </button>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('superadmin.gyms.destroy', $gymRow->id) }}"
                                          onsubmit="return confirm('Se eliminara el gimnasio y todos sus datos. Deseas continuar?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="danger">Eliminar gimnasio</x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-sm text-slate-500">No hay gimnasios registrados.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div id="admin-edit-modal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60" data-admin-modal-close></div>
                <div class="relative z-[81] w-full max-w-5xl rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4 shadow-2xl">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="ui-heading text-lg">Editar usuario del gimnasio</h3>
                        <button type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold" data-admin-modal-close>Cerrar</button>
                    </div>

                    <form id="admin-edit-form" method="POST" action="#" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-admin-user-id" name="admin_user_id" value="{{ (int) old('admin_user_id', 0) }}">
                        <input type="hidden" id="modal-admin-gym-id" name="admin_gym_id" value="{{ $adminEditOldGymId > 0 ? $adminEditOldGymId : 0 }}">

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nombre
                            <input id="modal-admin-name" type="text" name="admin_name" class="ui-input" value="{{ old('admin_name') }}" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-2">
                            Correo
                            <input id="modal-admin-email" type="email" name="admin_email" class="ui-input" value="{{ old('admin_email') }}" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Genero
                            <select id="modal-admin-gender" name="admin_gender" class="ui-input">
                                <option value="">No especificado</option>
                                <option value="male">Hombre</option>
                                <option value="female">Mujer</option>
                                <option value="other">Otro</option>
                                <option value="prefer_not_say">Prefiero no decir</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nacimiento
                            <input id="modal-admin-birth-date" type="date" name="admin_birth_date" class="ui-input" value="{{ old('admin_birth_date') }}">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Tipo de identificacion
                            <select id="modal-admin-identification-type" name="admin_identification_type" class="ui-input">
                                <option value="">No especificado</option>
                                <option value="cedula">Cedula</option>
                                <option value="dni">DNI</option>
                                <option value="passport">Pasaporte</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Numero identificacion
                            <input id="modal-admin-identification-number" type="text" name="admin_identification_number" class="ui-input" value="{{ old('admin_identification_number') }}" placeholder="Ej: 1726309071">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Pais
                            <select id="modal-admin-country-iso" name="admin_country_iso" class="ui-input">
                                <option value="">No especificado</option>
                                @foreach ($locationCatalog as $countryCode => $countryMeta)
                                    <option value="{{ $countryCode }}">{{ $countryMeta['label'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Provincia / estado
                            <select id="modal-admin-address-state" name="admin_address_state" class="ui-input">
                                <option value="">Selecciona provincia/estado</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Ciudad
                            <select id="modal-admin-address-city" name="admin_address_city" class="ui-input">
                                <option value="">Selecciona ciudad</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                            Direccion (linea)
                            <input id="modal-admin-address-line" type="text" name="admin_address_line" class="ui-input" value="{{ old('admin_address_line') }}" placeholder="Barrio, avenida, referencia">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Codigo telefono
                            <input id="modal-admin-phone-country-dial" type="text" name="admin_phone_country_dial" class="ui-input" value="{{ old('admin_phone_country_dial') }}" placeholder="+593">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Telefono
                            <input id="modal-admin-phone-number" type="text" name="admin_phone_number" class="ui-input" value="{{ old('admin_phone_number') }}" placeholder="0991234567">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                            Foto de perfil (opcional)
                            <input type="file" name="admin_profile_photo" class="ui-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <p class="ui-muted text-[11px]">JPG/PNG/WEBP, maximo 15MB.</p>
                        </label>

                        @if ($adminEditHasErrors)
                            <div class="rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-xs font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200 md:col-span-3">
                                @foreach ($errors->getMessages() as $errorKey => $errorMessages)
                                    @if (in_array($errorKey, $adminEditErrorKeys, true))
                                        @foreach ($errorMessages as $errorMessage)
                                            <p>{{ $errorMessage }}</p>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-admin-modal-close>Cancelar</button>
                            <x-ui.button type="submit">Guardar cambios</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
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

        const adminEditModal = document.getElementById('admin-edit-modal');
        const adminEditForm = document.getElementById('admin-edit-form');
        const adminEditButtons = Array.from(document.querySelectorAll('[data-edit-admin]'));
        const adminModalCloseButtons = Array.from(document.querySelectorAll('[data-admin-modal-close]'));
        const adminUpdateRouteTemplate = @json($adminEditRouteTemplate);
        const adminEditOldData = @json($adminEditOldData);

        const modalFields = {
            userId: document.getElementById('modal-admin-user-id'),
            gymId: document.getElementById('modal-admin-gym-id'),
            name: document.getElementById('modal-admin-name'),
            email: document.getElementById('modal-admin-email'),
            gender: document.getElementById('modal-admin-gender'),
            birthDate: document.getElementById('modal-admin-birth-date'),
            identificationType: document.getElementById('modal-admin-identification-type'),
            identificationNumber: document.getElementById('modal-admin-identification-number'),
            countryIso: document.getElementById('modal-admin-country-iso'),
            addressState: document.getElementById('modal-admin-address-state'),
            addressCity: document.getElementById('modal-admin-address-city'),
            addressLine: document.getElementById('modal-admin-address-line'),
            phoneCountryDial: document.getElementById('modal-admin-phone-country-dial'),
            phoneNumber: document.getElementById('modal-admin-phone-number'),
        };

        function setModalValue(field, value) {
            if (!field) return;
            field.value = value || '';
        }

        function openAdminModal(data) {
            if (!adminEditModal || !adminEditForm) return;

            const gymId = String(data.gymId || '').trim();
            if (gymId === '') return;

            adminEditForm.action = adminUpdateRouteTemplate.replace('__GYM__', gymId);
            setModalValue(modalFields.userId, String(data.userId || ''));
            setModalValue(modalFields.gymId, gymId);
            setModalValue(modalFields.name, data.name || '');
            setModalValue(modalFields.email, data.email || '');
            setModalValue(modalFields.gender, data.gender || '');
            setModalValue(modalFields.birthDate, data.birthDate || '');
            setModalValue(modalFields.identificationType, data.identificationType || '');
            setModalValue(modalFields.identificationNumber, data.identificationNumber || '');
            setModalValue(modalFields.countryIso, (data.countryIso || '').toLowerCase());
            syncAdminModalStates((data.countryIso || '').toLowerCase(), data.addressState || '', data.addressCity || '');
            setModalValue(modalFields.addressLine, data.addressLine || '');
            setModalValue(modalFields.phoneCountryDial, data.phoneCountryDial || '');
            setModalValue(modalFields.phoneNumber, data.phoneNumber || '');

            adminEditModal.classList.remove('hidden');
            adminEditModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeAdminModal() {
            if (!adminEditModal) return;
            adminEditModal.classList.add('hidden');
            adminEditModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        adminEditButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openAdminModal({
                    gymId: button.dataset.gymId || '',
                    userId: button.dataset.userId || '',
                    name: button.dataset.adminName || '',
                    email: button.dataset.adminEmail || '',
                    gender: button.dataset.adminGender || '',
                    birthDate: button.dataset.adminBirthDate || '',
                    identificationType: button.dataset.adminIdentificationType || '',
                    identificationNumber: button.dataset.adminIdentificationNumber || '',
                    countryIso: button.dataset.adminCountryIso || '',
                    addressState: button.dataset.adminAddressState || '',
                    addressCity: button.dataset.adminAddressCity || '',
                    addressLine: button.dataset.adminAddressLine || '',
                    phoneCountryDial: button.dataset.adminPhoneCountryDial || '',
                    phoneNumber: button.dataset.adminPhoneNumber || '',
                });
            });
        });

        adminModalCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeAdminModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAdminModal();
            }
        });

        if (adminEditOldData.hasErrors && Number(adminEditOldData.gymId) > 0) {
            openAdminModal(adminEditOldData);
        }

        function statesForCountry(countryCode) {
            const code = String(countryCode || '').toLowerCase();
            const country = locationCatalog[code] || null;
            if (!country || !country.states) return [];
            return Object.keys(country.states);
        }

        function citiesForState(countryCode, stateName) {
            const code = String(countryCode || '').toLowerCase();
            const country = locationCatalog[code] || null;
            if (!country || !country.states) return [];
            return country.states[stateName] || [];
        }

        function syncAdminModalStates(countryCode, preferredState, preferredCity) {
            if (!modalFields.addressState) return;
            const states = statesForCountry(countryCode);
            replaceOptions(modalFields.addressState, states, 'Selecciona provincia/estado');
            if (preferredState && states.includes(preferredState)) {
                modalFields.addressState.value = preferredState;
            }
            syncAdminModalCities(countryCode, modalFields.addressState.value, preferredCity);
        }

        function syncAdminModalCities(countryCode, stateName, preferredCity) {
            if (!modalFields.addressCity) return;
            const cities = citiesForState(countryCode, stateName);
            replaceOptions(modalFields.addressCity, cities, 'Selecciona ciudad');
            if (preferredCity && cities.includes(preferredCity)) {
                modalFields.addressCity.value = preferredCity;
            }
        }

        modalFields.countryIso?.addEventListener('change', function () {
            syncAdminModalStates(modalFields.countryIso.value, '', '');
        });
        modalFields.addressState?.addEventListener('change', function () {
            syncAdminModalCities(modalFields.countryIso?.value || '', modalFields.addressState?.value || '', '');
        });
    })();
</script>
@endpush
