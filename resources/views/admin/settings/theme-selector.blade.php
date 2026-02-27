@extends('layouts.panel')

@section('title', __('ui.settings'))
@section('page-title', __('ui.settings'))

@section('content')
    @php
        $gymInitials = '';
        $gymLogoUrl = null;
        $gymAvatarUrls = ['male' => null, 'female' => null, 'neutral' => null];
        $gymTimezone = 'UTC';
        $isGymContextRoute = request()->routeIs('gym.settings.*');
        $themeUpdateUrl = route('settings.theme.update');
        $gymProfileUpdateUrl = route('settings.gym-profile.update');
        $gymLogoUpdateUrl = route('settings.gym-logo.update');
        $gymAvatarsUpdateUrl = route('settings.gym-avatars.update');
        $gymCurrencyCode = old('currency_code', $gym->currency_code ?? 'USD');
        $gymLanguageCode = old('language_code', $gym->language_code ?? 'es');
        $gymAddressCountry = '-';
        $gymAddressState = '-';
        $gymAddressCity = '-';
        $gymAddressLine = '-';
        $avatarCards = [
            'male' => ['label' => 'Avatar hombre', 'field' => 'avatar_male'],
            'female' => ['label' => 'Avatar mujer', 'field' => 'avatar_female'],
            'neutral' => ['label' => 'Avatar neutral', 'field' => 'avatar_neutral'],
        ];
        $isSuperAdmin = auth()->user()?->gym_id === null;
        if ($gym) {
            if ($isGymContextRoute && trim((string) ($gym->slug ?? '')) !== '') {
                $contextRouteParams = ['contextGym' => $gym->slug];
                $themeUpdateUrl = route('gym.settings.theme.update', $contextRouteParams);
                $gymProfileUpdateUrl = route('gym.settings.gym-profile.update', $contextRouteParams);
                $gymLogoUpdateUrl = route('gym.settings.gym-logo.update', $contextRouteParams);
                $gymAvatarsUpdateUrl = route('gym.settings.gym-avatars.update', $contextRouteParams);
            }

            $resolveMediaUrl = function (?string $path): ?string {
                $rawPath = trim((string) $path);
                if ($rawPath === '') {
                    return null;
                }
                if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
                    return $rawPath;
                }

                $normalized = str_replace('\\', '/', ltrim($rawPath, '/'));

                $publicStorageMarker = '/storage/app/public/';
                $markerPos = strpos($normalized, $publicStorageMarker);
                if ($markerPos !== false) {
                    $normalized = substr($normalized, $markerPos + strlen($publicStorageMarker));
                }

                if (str_starts_with($normalized, 'public/')) {
                    $normalized = substr($normalized, strlen('public/'));
                }

                if (str_starts_with($normalized, 'storage/')) {
                    $normalized = substr($normalized, strlen('storage/'));
                }

                $normalized = ltrim($normalized, '/');
                if ($normalized === '') {
                    return null;
                }

                return asset('storage/'.$normalized);
            };

            $gymInitials = collect(explode(' ', trim($gym->name ?? '')))
                ->filter()
                ->map(fn ($word) => mb_substr($word, 0, 1))
                ->take(2)
                ->implode('');
            $gymInitials = $gymInitials !== '' ? mb_strtoupper($gymInitials) : 'GY';
            $gymLogoUrl = $resolveMediaUrl($gym->logo_path);
            $gymAvatarUrls = [
                'male' => $resolveMediaUrl($gym->avatar_male_path),
                'female' => $resolveMediaUrl($gym->avatar_female_path),
                'neutral' => $resolveMediaUrl($gym->avatar_neutral_path),
            ];
            $gymTimezone = old('timezone', $gym->timezone ?? 'UTC');

            $gymAddressCountry = trim((string) ($gym->address_country_name ?? ''));
            $gymAddressState = trim((string) ($gym->address_state ?? ''));
            $gymAddressCity = trim((string) ($gym->address_city ?? ''));
            $gymAddressLine = trim((string) ($gym->address_line ?? ''));

            if (($gymAddressCountry === '' || $gymAddressState === '' || $gymAddressCity === '') && !empty($gym->address)) {
                $addressParts = array_values(array_filter(array_map(
                    static fn ($piece): string => trim((string) $piece),
                    explode(',', (string) $gym->address)
                ), static fn ($piece): bool => $piece !== ''));
                if (count($addressParts) >= 3) {
                    if ($gymAddressCountry === '') {
                        $gymAddressCountry = $addressParts[count($addressParts) - 1];
                    }
                    if ($gymAddressState === '') {
                        $gymAddressState = $addressParts[count($addressParts) - 2];
                    }
                    if ($gymAddressCity === '') {
                        $gymAddressCity = $addressParts[count($addressParts) - 3];
                    }
                    if ($gymAddressLine === '' && count($addressParts) > 3) {
                        $gymAddressLine = implode(', ', array_slice($addressParts, 0, -3));
                    }
                }
            }

            $gymAddressCountry = $gymAddressCountry !== '' ? $gymAddressCountry : '-';
            $gymAddressState = $gymAddressState !== '' ? $gymAddressState : '-';
            $gymAddressCity = $gymAddressCity !== '' ? $gymAddressCity : '-';
            $gymAddressLine = $gymAddressLine !== '' ? $gymAddressLine : '-';
        }
    @endphp

    <div id="theme-selector"
         class="space-y-6"
         data-current-theme="{{ $currentTheme }}"
         data-update-url="{{ $themeUpdateUrl }}"
         data-csrf="{{ csrf_token() }}">
        <x-card title="Selector de tema"
                subtitle="Personaliza IRON WILL con una apariencia premium. El cambio es instantáneo y se guarda en tu cuenta.">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($themes as $themeKey => $theme)
                    <button type="button"
                            data-theme-option="{{ $themeKey }}"
                            class="theme-option-card group relative w-full rounded-2xl p-4 text-left">
                        <span data-selected-icon
                              class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-sm font-black text-slate-900 opacity-0 shadow transition">
                            &#10003;
                        </span>

                        <div class="mb-3 flex items-start justify-between gap-2">
                            <div>
                                <p class="ui-muted text-xs font-bold uppercase tracking-[0.16em]">Tema</p>
                                <h3 class="ui-heading mt-1 text-sm font-black tracking-wide">{{ $theme['name'] }}</h3>
                            </div>
                            <span data-selected-badge
                                  class="theme-pill-inactive inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
                                Seleccionar
                            </span>
                        </div>

                        <div class="overflow-hidden rounded-xl border border-white/10">
                            <div class="flex h-28">
                                <div class="w-[28%] space-y-1.5 p-2" style="background-color: {{ $theme['sidebar'] }};">
                                    <div class="h-2 w-9 rounded-full bg-white/40"></div>
                                    <div class="h-1.5 rounded bg-white/25"></div>
                                    <div class="h-1.5 rounded bg-white/20"></div>
                                    <div class="h-1.5 w-3/4 rounded bg-white/20"></div>
                                </div>
                                <div class="flex-1 p-2.5" style="background-color: {{ $theme['bg'] }};">
                                    <div class="h-2 w-20 rounded-full bg-white/20"></div>
                                    <div class="mt-2 rounded-md border border-white/10 p-2">
                                        <div class="h-2 w-12 rounded-full" style="background-color: {{ $theme['primary'] }};"></div>
                                        <div class="mt-2 h-1.5 w-full rounded bg-white/15"></div>
                                        <div class="mt-1.5 h-1.5 w-3/4 rounded bg-white/10"></div>
                                    </div>
                                    <div class="mt-2 flex gap-1.5">
                                        <span class="h-2 w-8 rounded-full" style="background-color: {{ $theme['primary'] }};"></span>
                                        <span class="h-2 w-8 rounded-full" style="background-color: {{ $theme['accent'] }};"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </x-card>

        @if ($gym)
            <div class="grid gap-4 lg:grid-cols-2">
                <section class="ui-card">
                    <h3 class="ui-heading text-base">Editar Logo</h3>
                    <p class="ui-muted mt-1 text-sm">Sube una imagen cuadrada para una mejor vista en sidebar y credenciales.</p>

                    <div class="mt-4 flex items-center gap-4">
                        <div class="theme-logo-badge flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl text-xl font-black">
                            @if ($gymLogoUrl)
                                <img src="{{ $gymLogoUrl }}" alt="Logo actual" class="h-full w-full object-contain" style="transform: scale(1.55); transform-origin: center;">
                            @else
                                {{ $gymInitials }}
                            @endif
                        </div>
                        <div class="ui-muted text-xs">
                            <p>Formatos: JPG, PNG, WEBP</p>
                            <p>Peso máximo: 2MB</p>
                        </div>
                    </div>

                    <form id="gym-logo-form"
                          method="POST"
                          action="{{ $gymLogoUpdateUrl }}"
                          enctype="multipart/form-data"
                          class="mt-4 space-y-3">
                        @csrf
                        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="ui-input">
                        @error('logo')
                            <p class="text-sm font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="ui-button ui-button-primary">Actualizar logo</button>
                    </form>
                </section>

                <section class="ui-card">
                    <h3 class="ui-heading text-base">Datos del Gym</h3>
                    <p class="ui-muted mt-1 text-sm">Actualiza nombre comercial y teléfono. Ubicación definida por SuperAdmin (solo lectura).</p>

                    <form id="gym-profile-form" method="POST" action="{{ $gymProfileUpdateUrl }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre comercial</label>
                            <input type="text" name="name" value="{{ old('name', $gym->name) }}" class="ui-input" required>
                            @error('name')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone', $gym->phone) }}" class="ui-input">
                            @error('phone')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">País (solo lectura)</label>
                                <input type="text" class="ui-input" value="{{ $gymAddressCountry }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Provincia / Estado (solo lectura)</label>
                                <input type="text" class="ui-input" value="{{ $gymAddressState }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Ciudad (solo lectura)</label>
                                <input type="text" class="ui-input" value="{{ $gymAddressCity }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Dirección línea (solo lectura)</label>
                                <input type="text" class="ui-input" value="{{ $gymAddressLine }}" readonly>
                            </div>
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Moneda</label>
                            <select name="currency_code" class="ui-input" required>
                                @foreach ($currencyOptions as $currencyCode => $currencyMeta)
                                    <option value="{{ $currencyCode }}" @selected($gymCurrencyCode === $currencyCode)>
                                        {{ $currencyCode }} - {{ $currencyMeta['name'] }} ({{ $currencyMeta['symbol'] }})
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_code')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Idioma</label>
                            <select name="language_code" class="ui-input" required>
                                @foreach ($languageOptions as $langCode => $langLabel)
                                    <option value="{{ $langCode }}" @selected($gymLanguageCode === $langCode)>{{ $langLabel }}</option>
                                @endforeach
                            </select>
                            @error('language_code')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Zona horaria</label>
                            <div class="theme-surface-light space-y-2 rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <input id="timezone-search"
                                           type="text"
                                           class="ui-input min-w-[220px] flex-1"
                                           placeholder="Buscar por país, ciudad o zona (ej: ecuador, bogotá, méxico)">
                                    <button id="timezone-detect-btn" type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold">
                                        Usar navegador
                                    </button>
                                </div>
                                <p id="timezone-detect-hint" class="ui-muted text-xs"></p>
                                <select id="timezone-select" name="timezone" class="ui-input" required>
                                    @foreach ($timezoneOptions as $timezoneValue => $timezoneLabel)
                                        <option value="{{ $timezoneValue }}" @selected($gymTimezone === $timezoneValue)>
                                            {{ $timezoneLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                <p id="timezone-current" class="ui-muted text-xs"></p>
                            </div>
                            <p class="ui-muted mt-1 text-xs">Esta zona se usa para horas de check-in, reportes y panel.</p>
                            @error('timezone')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="ui-button ui-button-primary">Guardar datos del gym</button>
                    </form>
                </section>
            </div>

            <section class="ui-card">
                <h3 class="ui-heading text-base">Avatares de recepción (fallback)</h3>
                <p class="ui-muted mt-1 text-sm">
                    Se usan cuando el cliente no tiene foto propia. Puedes subir PNG/JPG/WEBP en formato vertical.
                </p>

                <form id="gym-avatars-form"
                      method="POST"
                      action="{{ $gymAvatarsUpdateUrl }}"
                      enctype="multipart/form-data"
                      class="mt-4 space-y-4">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ($avatarCards as $avatarKey => $avatarMeta)
                            @php
                                $avatarUrl = $gymAvatarUrls[$avatarKey] ?? null;
                            @endphp
                            <div class="theme-surface-light rounded-2xl border border-slate-300/70 bg-slate-50/80 p-3">
                                <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ $avatarMeta['label'] }}</p>
                                <div class="mt-2 overflow-hidden rounded-xl border border-slate-300/70 bg-slate-100" style="aspect-ratio: 4/5;">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $avatarMeta['label'] }}" class="h-full w-full object-cover object-top">
                                    @else
                                        <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-center">
                                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-slate-700">Sin avatar</span>
                                            <span class="text-[10px] uppercase tracking-[0.25em] text-slate-500">{{ strtoupper($avatarKey) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <input type="file"
                                       name="{{ $avatarMeta['field'] }}"
                                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                       class="ui-input mt-3">
                                @error($avatarMeta['field'])
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    @error('avatar_files')
                        <p class="text-sm font-semibold text-rose-300">{{ $message }}</p>
                    @enderror

                    <div class="ui-muted text-xs">
                        <p>Recomendado: 900x1200 px o similar (formato vertical).</p>
                        <p>Peso máximo por archivo: 4MB.</p>
                    </div>

                    <button type="submit" class="ui-button ui-button-primary">Guardar avatares</button>
                </form>
            </section>
        @else
            @if ($isSuperAdmin)
                <section class="ui-card">
                    <h3 class="ui-heading text-base">Configuración SuperAdmin</h3>
                    <p class="ui-muted mt-1 text-sm">
                        Como SuperAdmin gestionas múltiples gimnasios. El logo, teléfono y dirección se administran por cada gym.
                    </p>
                    <a href="{{ route('superadmin.gyms.index') }}" class="ui-button ui-button-primary mt-4">
                        Ir a Gimnasios
                    </a>
                </section>
            @else
                <section class="ui-card">
                    <h3 class="ui-heading text-base">Configuración del Gym</h3>
                    <p class="ui-muted mt-1 text-sm">Este usuario no tiene un gym asignado actualmente.</p>
                </section>
            @endif
        @endif

        <div id="theme-toast-stack" class="pointer-events-none fixed right-5 top-20 z-50 space-y-2"></div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const container = document.getElementById('theme-selector');
            if (!container) return;

            const root = document.documentElement;
            const csrf = container.dataset.csrf;
            const rawUpdateUrl = String(container.dataset.updateUrl || '').trim();
            const fallbackThemeUrl = (() => {
                const path = String(window.location.pathname || '').replace(/\/+$/, '');
                if (path.endsWith('/config')) {
                    return `${path}/theme`;
                }
                return '/config/theme';
            })();
            const updateUrl = rawUpdateUrl !== '' ? rawUpdateUrl : fallbackThemeUrl;
            const darkThemes = new Set(['iron_dark', 'power_red', 'energy_green', 'gold_elite']);
            let currentTheme = container.dataset.currentTheme || 'iron_dark';
            let requestInFlight = false;

            const options = Array.from(container.querySelectorAll('[data-theme-option]'));
            const toastStack = document.getElementById('theme-toast-stack');
            const timezoneSelect = document.getElementById('timezone-select');
            const timezoneSearch = document.getElementById('timezone-search');
            const timezoneDetectBtn = document.getElementById('timezone-detect-btn');
            const timezoneDetectHint = document.getElementById('timezone-detect-hint');
            const timezoneCurrent = document.getElementById('timezone-current');

            const favoriteTimezoneIds = [
                'America/Guayaquil',
                'America/Bogota',
                'America/Lima',
                'America/La_Paz',
                'America/Santiago',
                'America/Caracas',
                'America/Mexico_City',
                'America/Panama',
                'America/Argentina/Buenos_Aires',
                'America/New_York',
                'America/Los_Angeles',
                'Europe/Madrid',
            ];

            const timezoneAliasLabels = {
                'America/Guayaquil': 'Ecuador - Guayaquil',
                'America/Bogota': 'Colombia - Bogota',
                'America/Lima': 'Peru - Lima',
                'America/La_Paz': 'Bolivia - La Paz',
                'America/Santiago': 'Chile - Santiago',
                'America/Caracas': 'Venezuela - Caracas',
                'America/Mexico_City': 'Mexico - Ciudad de Mexico',
                'America/Panama': 'Panama - Ciudad de Panama',
                'America/Argentina/Buenos_Aires': 'Argentina - Buenos Aires',
                'America/New_York': 'Estados Unidos - New York',
                'America/Los_Angeles': 'Estados Unidos - Los Angeles',
                'Europe/Madrid': 'Espana - Madrid',
            };

            const timezoneAliasKeywords = {
                'America/Guayaquil': 'ecuador quito guayaquil',
                'America/Bogota': 'colombia bogota medellin',
                'America/Lima': 'peru lima',
                'America/La_Paz': 'bolivia la paz',
                'America/Santiago': 'chile santiago',
                'America/Caracas': 'venezuela caracas',
                'America/Mexico_City': 'mexico ciudad de mexico cdmx',
                'America/Panama': 'panama',
                'America/Argentina/Buenos_Aires': 'argentina buenos aires',
                'America/New_York': 'usa estados unidos new york',
                'America/Los_Angeles': 'usa estados unidos california',
                'Europe/Madrid': 'espana madrid',
            };

            const normalizeSearch = (value) => String(value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            const timezoneOffsetFromLabel = (label) => {
                const matched = String(label || '').match(/\((UTC[+-]\d{2}:\d{2})\)/i);
                return matched ? matched[1].toUpperCase() : 'UTC';
            };

            const timezoneGeoLabel = (identifier) => {
                if (timezoneAliasLabels[identifier]) {
                    return timezoneAliasLabels[identifier];
                }

                const parts = String(identifier || '').split('/');
                if (parts.length === 1) {
                    return parts[0].replace(/_/g, ' ');
                }

                const region = parts.shift();
                return `${region} - ${parts.join(' / ').replace(/_/g, ' ')}`;
            };

            const favoriteTimezoneSet = new Set(favoriteTimezoneIds);

            let timezoneItems = [];
            let timezoneMap = new Map();

            const rebuildTimezoneIndex = () => {
                if (!timezoneSelect) return;

                timezoneItems = Array.from(timezoneSelect.options).map((option) => {
                    const value = String(option.value || '');
                    const rawLabel = String(option.textContent || '');
                    const geo = timezoneGeoLabel(value);
                    const offset = timezoneOffsetFromLabel(rawLabel);
                    const searchBlob = normalizeSearch([
                        value,
                        rawLabel,
                        geo,
                        timezoneAliasKeywords[value] || '',
                    ].join(' '));

                    return {
                        value,
                        geo,
                        offset,
                        searchBlob,
                    };
                });

                timezoneMap = new Map(timezoneItems.map((item) => [item.value, item]));
            };

            const selectedTimezoneText = () => {
                if (!timezoneSelect || !timezoneCurrent) return;

                const selected = timezoneMap.get(timezoneSelect.value);
                if (!selected) {
                    timezoneCurrent.textContent = 'Seleccionada: -';
                    return;
                }

                timezoneCurrent.textContent = `Seleccionada: ${selected.geo} (${selected.offset})`;
            };

            const renderTimezoneOptions = (query = '', preferredValue = null) => {
                if (!timezoneSelect) return;

                const normalizedQuery = normalizeSearch(query);
                const currentValue = preferredValue || timezoneSelect.value;
                const filtered = timezoneItems
                    .filter((item) => normalizedQuery === '' || item.searchBlob.includes(normalizedQuery))
                    .sort((a, b) => {
                        const aFav = favoriteTimezoneSet.has(a.value) ? 0 : 1;
                        const bFav = favoriteTimezoneSet.has(b.value) ? 0 : 1;
                        if (aFav !== bFav) return aFav - bFav;

                        return a.geo.localeCompare(b.geo, 'es', { sensitivity: 'base' });
                    });

                timezoneSelect.innerHTML = '';

                if (filtered.length === 0) {
                    const empty = document.createElement('option');
                    empty.value = '';
                    empty.textContent = 'Sin coincidencias para tu busqueda';
                    empty.disabled = true;
                    empty.selected = true;
                    timezoneSelect.appendChild(empty);
                    selectedTimezoneText();
                    return;
                }

                filtered.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.textContent = `${favoriteTimezoneSet.has(item.value) ? '* ' : ''}${item.geo} (${item.offset})`;
                    timezoneSelect.appendChild(option);
                });

                const hasCurrent = filtered.some((item) => item.value === currentValue);
                timezoneSelect.value = hasCurrent ? currentValue : filtered[0].value;
                selectedTimezoneText();
            };

            const detectTimezone = () => {
                try {
                    return Intl.DateTimeFormat().resolvedOptions().timeZone || '';
                } catch (error) {
                    return '';
                }
            };

            const initTimezonePicker = () => {
                if (!timezoneSelect) return;

                rebuildTimezoneIndex();
                renderTimezoneOptions('');

                const detectedTimezone = detectTimezone();
                const detectedInfo = timezoneMap.get(detectedTimezone);

                if (timezoneDetectHint) {
                    timezoneDetectHint.textContent = detectedInfo
                        ? `Detectada en este equipo: ${detectedInfo.geo} (${detectedInfo.offset})`
                        : 'No se pudo detectar automaticamente la zona horaria del navegador.';
                }

                if (timezoneDetectBtn) {
                    timezoneDetectBtn.disabled = !detectedInfo;
                    timezoneDetectBtn.addEventListener('click', () => {
                        if (!detectedInfo) return;
                        if (timezoneSearch) {
                            timezoneSearch.value = '';
                        }
                        renderTimezoneOptions('', detectedInfo.value);
                    });
                }

                if (timezoneSearch) {
                    timezoneSearch.addEventListener('input', (event) => {
                        const value = event.target && typeof event.target.value === 'string'
                            ? event.target.value
                            : '';
                        renderTimezoneOptions(value);
                    });
                }

                timezoneSelect.addEventListener('change', () => {
                    selectedTimezoneText();
                });

                if (timezoneSelect.value === 'UTC' && detectedInfo && detectedInfo.value !== 'UTC') {
                    renderTimezoneOptions('', detectedInfo.value);
                }
            };

            const setActiveCard = (theme) => {
                options.forEach((card) => {
                    const isActive = card.dataset.themeOption === theme;
                    card.classList.toggle('is-active', isActive);

                    const badge = card.querySelector('[data-selected-badge]');
                    if (badge) {
                        badge.textContent = isActive ? 'Seleccionado' : 'Seleccionar';
                        badge.classList.toggle('theme-pill-active', isActive);
                        badge.classList.toggle('theme-pill-inactive', !isActive);
                    }

                    const check = card.querySelector('[data-selected-icon]');
                    if (check) {
                        check.classList.toggle('opacity-100', isActive);
                        check.classList.toggle('opacity-0', !isActive);
                    }
                });
            };

            const applyTheme = (theme) => {
                root.setAttribute('data-theme', theme);
                const isDarkTheme = darkThemes.has(theme);
                root.classList.toggle('dark', isDarkTheme);
                root.classList.toggle('theme-dark', isDarkTheme);
                root.classList.toggle('theme-light', !isDarkTheme);
            };

            const pushToast = (message, type = 'success') => {
                if (!toastStack) return;

                const palette = type === 'success'
                    ? 'border-emerald-300 bg-emerald-100 text-emerald-800'
                    : 'border-rose-300 bg-rose-100 text-rose-800';

                const toast = document.createElement('div');
                toast.className = `pointer-events-auto w-72 rounded-xl border px-4 py-3 text-sm font-semibold shadow-xl backdrop-blur transition ${palette}`;
                toast.textContent = message;
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(6px)';
                toastStack.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                });

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(6px)';
                    setTimeout(() => toast.remove(), 220);
                }, 2600);
            };

            const saveTheme = async (theme) => {
                const response = await fetch(updateUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ theme }),
                });

                if (!response.ok) {
                    let reason = `No fue posible guardar el tema (HTTP ${response.status}).`;
                    const contentType = String(response.headers.get('content-type') || '').toLowerCase();

                    try {
                        if (contentType.includes('application/json')) {
                            const payload = await response.json();
                            reason = payload?.message || payload?.errors?.theme?.[0] || reason;
                        } else {
                            const responseText = await response.text();
                            if (/csrf|token/i.test(responseText)) {
                                reason = 'Sesión expirada. Recarga la página e intenta otra vez.';
                            }
                        }
                    } catch (error) {}

                    throw new Error(reason);
                }

                const payload = await response.json();
                return payload.theme || theme;
            };

            const onSelectTheme = async (theme) => {
                if (theme === currentTheme || requestInFlight) return;
                requestInFlight = true;

                const previousTheme = currentTheme;
                currentTheme = theme;

                applyTheme(theme);
                setActiveCard(theme);

                try {
                    const savedTheme = await saveTheme(theme);
                    currentTheme = savedTheme;
                    applyTheme(savedTheme);
                    setActiveCard(savedTheme);
                    pushToast('Tema guardado correctamente.', 'success');
                } catch (error) {
                    currentTheme = previousTheme;
                    applyTheme(previousTheme);
                    setActiveCard(previousTheme);
                    pushToast(error.message || 'Error al guardar el tema.', 'error');
                } finally {
                    requestInFlight = false;
                }
            };

            options.forEach((card) => {
                card.addEventListener('click', () => onSelectTheme(card.dataset.themeOption));
            });

            initTimezonePicker();
            applyTheme(currentTheme);
            setActiveCard(currentTheme);
        })();
    </script>
@endpush
