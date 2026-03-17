@extends('layouts.panel')

@section('title', __('ui.settings'))
@section('page-title', __('ui.settings'))

@push('styles')
    <style>
        #theme-selector.settings-premium-shell {
            position: relative;
            isolation: isolate;
        }

        #theme-selector.settings-premium-shell::before {
            content: '';
            position: absolute;
            inset: -1rem -0.5rem auto -0.5rem;
            height: 12rem;
            z-index: -1;
            pointer-events: none;
            background:
                radial-gradient(70% 85% at 20% 10%, color-mix(in srgb, var(--primary) 24%, transparent), transparent 72%),
                radial-gradient(65% 80% at 82% 8%, color-mix(in srgb, var(--accent) 18%, transparent), transparent 74%);
            filter: blur(14px);
            opacity: 0.85;
        }

        #theme-selector .settings-premium-card,
        #theme-selector .ui-card {
            position: relative;
            overflow: hidden;
            border: 1px solid color-mix(in srgb, var(--border) 86%, transparent);
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--card) 88%, transparent) 0%, color-mix(in srgb, var(--card) 95%, #000000) 100%);
            box-shadow:
                0 16px 36px -26px rgb(0 0 0 / 0.72),
                inset 0 1px 0 rgb(255 255 255 / 0.06);
            transition: transform 0.2s ease, box-shadow 0.22s ease, border-color 0.2s ease;
        }

        #theme-selector .settings-premium-card::after,
        #theme-selector .ui-card::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 2px;
            pointer-events: none;
            background: linear-gradient(90deg, color-mix(in srgb, var(--primary) 72%, transparent) 0%, color-mix(in srgb, var(--accent) 72%, transparent) 100%);
            opacity: 0.48;
        }

        #theme-selector .settings-premium-card > *,
        #theme-selector .ui-card > * {
            position: relative;
            z-index: 1;
        }

        #theme-selector .settings-premium-card:hover,
        #theme-selector .ui-card:hover {
            border-color: color-mix(in srgb, var(--primary) 36%, var(--border));
            box-shadow:
                0 20px 42px -28px rgb(0 0 0 / 0.86),
                0 0 0 1px color-mix(in srgb, var(--primary) 22%, transparent),
                inset 0 1px 0 rgb(255 255 255 / 0.08);
        }

        #theme-selector .settings-theme-card {
            border-color: color-mix(in srgb, var(--accent) 25%, var(--border));
        }

        #theme-selector .theme-picker-grid {
            align-items: stretch;
        }

        #theme-selector .theme-option-card {
            display: flex;
            min-height: 100%;
            flex-direction: column;
            overflow: hidden;
            border-radius: 1rem;
            padding: 0.9rem;
            transform-origin: center;
            isolation: isolate;
        }

        #theme-selector .theme-option-card:hover {
            transform: translateY(-2px) scale(1.01);
        }

        #theme-selector .theme-option-card::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                linear-gradient(155deg, rgb(255 255 255 / 0.08) 0%, transparent 48%),
                radial-gradient(90% 60% at 100% 0%, color-mix(in srgb, var(--primary) 20%, transparent), transparent 72%);
            opacity: 0.35;
            transition: opacity 0.2s ease;
        }

        #theme-selector .theme-option-card:hover::before {
            opacity: 0.6;
        }

        #theme-selector .theme-option-card.is-active::before {
            opacity: 0.95;
        }

        #theme-selector .theme-option-card.is-active::after {
            content: '';
            position: absolute;
            inset: -35% auto -35% -52%;
            width: 46%;
            z-index: 0;
            pointer-events: none;
            background: linear-gradient(120deg, transparent, rgb(255 255 255 / 0.26), transparent);
            transform: rotate(18deg);
            animation: themeCardSweep 3.6s ease-in-out infinite;
        }

        #theme-selector .theme-selection-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
            gap: 0.55rem;
        }

        #theme-selector [data-selected-icon] {
            display: none;
        }

        #theme-selector .theme-option-card [data-selected-badge] {
            min-width: 5.5rem;
            justify-content: center;
            white-space: nowrap;
            font-size: 0.62rem;
            letter-spacing: 0.09em;
            border-width: 1px;
            backdrop-filter: blur(6px);
        }

        #theme-selector .theme-preview-shell {
            margin-top: auto;
            overflow: hidden;
            border-radius: 0.85rem;
            border: 1px solid rgb(148 163 184 / 0.22);
            box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.08);
        }

        #theme-selector .settings-upload-input::file-selector-button {
            margin-right: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.65rem;
            padding: 0.38rem 0.72rem;
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1.1;
            color: var(--text);
            background: color-mix(in srgb, var(--card-muted) 82%, transparent);
            transition: all 0.16s ease;
        }

        #theme-selector .settings-upload-input:hover::file-selector-button {
            border-color: color-mix(in srgb, var(--primary) 60%, transparent);
            background: color-mix(in srgb, var(--card-muted) 62%, transparent);
        }

        #theme-selector .settings-upload-input {
            border-color: color-mix(in srgb, var(--border) 88%, transparent);
            background: color-mix(in srgb, var(--card-muted) 82%, transparent);
        }

        #theme-selector .settings-upload-input:focus-visible {
            outline: none;
            border-color: color-mix(in srgb, var(--primary) 65%, transparent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 22%, transparent);
        }

        #theme-selector .settings-avatar-grid {
            align-items: start;
        }

        #theme-selector .settings-avatar-card {
            display: flex;
            min-height: 100%;
            flex-direction: column;
            gap: 0.7rem;
            border-radius: 1rem;
            padding: 0.75rem;
            border-color: color-mix(in srgb, var(--border) 90%, transparent);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        #theme-selector .settings-avatar-card:hover {
            transform: translateY(-2px);
            border-color: color-mix(in srgb, var(--primary) 38%, var(--border));
            box-shadow: 0 16px 28px -24px rgb(0 0 0 / 0.72);
        }

        #theme-selector .settings-avatar-caption {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        #theme-selector .settings-avatar-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid rgb(148 163 184 / 0.42);
            padding: 0.2rem 0.55rem;
            font-size: 0.62rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgb(148 163 184);
            background: rgb(15 23 42 / 0.65);
        }

        #theme-selector .settings-avatar-chip.is-ready {
            border-color: rgb(16 185 129 / 0.55);
            color: rgb(110 231 183);
            background: rgb(6 78 59 / 0.5);
        }

        #theme-selector .settings-avatar-preview {
            overflow: hidden;
            border-radius: 0.9rem;
            border: 1px solid rgb(148 163 184 / 0.32);
            background: rgb(241 245 249 / 0.88);
            height: clamp(260px, 34vw, 410px);
            box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.3);
        }

        #theme-selector .settings-avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
        }

        #theme-selector .settings-avatar-empty {
            display: flex;
            height: 100%;
            width: 100%;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            text-align: center;
            background: linear-gradient(160deg, rgb(226 232 240) 0%, rgb(241 245 249) 100%);
        }

        .theme-dark #theme-selector .settings-avatar-preview {
            border-color: rgb(71 85 105 / 0.45);
            background: linear-gradient(150deg, rgb(15 23 42 / 0.98) 0%, rgb(2 6 23 / 0.96) 100%);
            box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.08);
        }

        .theme-dark #theme-selector .settings-avatar-empty {
            background:
                radial-gradient(60% 80% at 50% 18%, rgb(30 64 175 / 0.22), transparent 70%),
                linear-gradient(160deg, rgb(15 23 42 / 0.98) 0%, rgb(3 7 18 / 0.95) 100%);
        }

        .theme-dark #theme-selector .settings-avatar-empty .text-slate-700 {
            color: rgb(226 232 240) !important;
        }

        .theme-dark #theme-selector .settings-avatar-empty .text-slate-500 {
            color: rgb(148 163 184) !important;
        }

        #theme-selector .settings-avatar-help {
            margin-top: 0.35rem;
            display: grid;
            gap: 0.2rem;
        }

        #theme-selector .ui-button-primary {
            border: 1px solid color-mix(in srgb, var(--primary) 50%, transparent);
            background: linear-gradient(135deg, color-mix(in srgb, var(--primary) 95%, #ffffff) 0%, color-mix(in srgb, var(--accent) 75%, #000000) 100%);
            box-shadow:
                0 10px 24px -16px color-mix(in srgb, var(--primary) 72%, transparent),
                inset 0 1px 0 rgb(255 255 255 / 0.2);
            transition: transform 0.16s ease, box-shadow 0.16s ease, filter 0.16s ease;
        }

        #theme-selector .ui-button-primary:hover {
            transform: translateY(-1px);
            box-shadow:
                0 14px 26px -16px color-mix(in srgb, var(--primary) 78%, transparent),
                0 0 0 1px color-mix(in srgb, var(--primary) 42%, transparent);
            filter: saturate(1.05);
        }

        #theme-selector .ui-button-primary:active {
            transform: translateY(0);
        }

        @keyframes themeCardSweep {
            0%, 18% { transform: translateX(0) rotate(18deg); opacity: 0; }
            24% { opacity: 0.28; }
            56% { transform: translateX(265%) rotate(18deg); opacity: 0; }
            100% { transform: translateX(265%) rotate(18deg); opacity: 0; }
        }

        @media (max-width: 1024px) {
            #theme-selector .theme-option-card [data-selected-badge] {
                min-width: 5.15rem;
                padding-inline: 0.48rem;
            }
        }

        @media (max-width: 768px) {
            #theme-selector .theme-option-card {
                padding: 0.78rem;
            }

            #theme-selector .settings-avatar-card {
                padding: 0.68rem;
            }

            #theme-selector .settings-avatar-preview {
                height: clamp(230px, 62vw, 360px);
            }
        }
    </style>
@endpush

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
        $profilePhotoUpdateUrl = route('settings.profile-photo.update');
        $superAdminTimezoneUpdateUrl = route('settings.superadmin-timezone.update');
        $locationCatalog = is_array($locationCatalog ?? null) ? $locationCatalog : [];
        $gymCurrencyCode = old('currency_code', $gym->currency_code ?? 'USD');
        $gymLanguageCode = old('language_code', $gym->language_code ?? 'es');
        $superAdminTimezone = old('superadmin_timezone', auth()->user()?->timezone ?? config('app.timezone', 'UTC'));
        $gymAddressCountryCode = '';
        $gymAddressCountry = '-';
        $gymAddressState = '-';
        $gymAddressCity = '-';
        $gymAddressLine = '-';
        $statesForGymCountry = [];
        $citiesForGymState = [];
        $avatarCards = [
            'male' => ['label' => 'Avatar hombre', 'field' => 'avatar_male'],
            'female' => ['label' => 'Avatar mujer', 'field' => 'avatar_female'],
            'neutral' => ['label' => 'Avatar neutral', 'field' => 'avatar_neutral'],
        ];
        $viewer = auth()->user();
        $isSuperAdmin = $viewer?->gym_id === null;
        $isCashierMode = (bool) ($viewer?->isCashier() ?? false);
        $viewerInitial = mb_strtoupper(mb_substr(trim((string) ($viewer?->name ?? 'U')) ?: 'U', 0, 1));
        $viewerPhotoUrl = null;
        if ($gym) {
            if ($isGymContextRoute && trim((string) ($gym->slug ?? '')) !== '') {
                $contextRouteParams = ['contextGym' => $gym->slug];
                $themeUpdateUrl = route('gym.settings.theme.update', $contextRouteParams);
                $gymProfileUpdateUrl = route('gym.settings.gym-profile.update', $contextRouteParams);
                $gymLogoUpdateUrl = route('gym.settings.gym-logo.update', $contextRouteParams);
                $gymAvatarsUpdateUrl = route('gym.settings.gym-avatars.update', $contextRouteParams);
                $profilePhotoUpdateUrl = route('gym.settings.profile-photo.update', $contextRouteParams);
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
            $viewerPhotoUrl = $resolveMediaUrl((string) ($viewer?->profile_photo_path ?? ''));

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

            $gymAddressCountryCode = strtolower(trim((string) old('address_country_code', $gym->address_country_code ?? '')));
            if ($gymAddressCountryCode === '' || ! array_key_exists($gymAddressCountryCode, $locationCatalog)) {
                $matchedCountryCode = '';
                foreach ($locationCatalog as $countryCode => $countryMeta) {
                    if (strcasecmp(
                        trim((string) ($countryMeta['label'] ?? '')),
                        trim((string) ($gym->address_country_name ?? ''))
                    ) === 0) {
                        $matchedCountryCode = (string) $countryCode;
                        break;
                    }
                }

                $gymAddressCountryCode = $matchedCountryCode;
            }

            $gymAddressCountry = $gymAddressCountryCode !== '' && array_key_exists($gymAddressCountryCode, $locationCatalog)
                ? (string) ($locationCatalog[$gymAddressCountryCode]['label'] ?? $gym->address_country_name ?? '')
                : trim((string) ($gym->address_country_name ?? ''));
            $gymAddressState = trim((string) old('address_state', $gym->address_state ?? ''));
            $gymAddressCity = trim((string) old('address_city', $gym->address_city ?? ''));
            $gymAddressLine = trim((string) old('address_line', $gym->address_line ?? ''));
            $statesForGymCountry = $gymAddressCountryCode !== ''
                ? (array) ($locationCatalog[$gymAddressCountryCode]['states'] ?? [])
                : [];
            $citiesForGymState = $gymAddressState !== ''
                ? (array) ($statesForGymCountry[$gymAddressState] ?? [])
                : [];

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
        if (! is_string($superAdminTimezone) || ! in_array($superAdminTimezone, timezone_identifiers_list(), true)) {
            $superAdminTimezone = 'America/Guayaquil';
        }
    @endphp

    <div id="theme-selector"
         class="settings-premium-shell space-y-6"
         data-current-theme="{{ $currentTheme }}"
         data-update-url="{{ $themeUpdateUrl }}"
         data-csrf="{{ csrf_token() }}">
        <x-card class="settings-premium-card settings-theme-card" title="Selector de tema"
                subtitle="Personaliza IRON WILL con una apariencia premium. El cambio es instantáneo y se guarda en tu cuenta.">
            <div class="theme-picker-grid grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($themes as $themeKey => $theme)
                    <button type="button"
                            data-theme-option="{{ $themeKey }}"
                            class="theme-option-card group relative w-full text-left">
                        <span data-selected-icon
                              class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-sm font-black text-slate-900 opacity-0 shadow transition">
                            &#10003;
                        </span>

                        <div class="theme-selection-head mb-3">
                            <div>
                                <p class="ui-muted text-xs font-bold uppercase tracking-[0.16em]">Tema</p>
                                <h3 class="ui-heading mt-1 text-sm font-black tracking-wide">{{ $theme['name'] }}</h3>
                            </div>
                            <span data-selected-badge
                                  class="theme-pill-inactive inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
                                Elegir
                            </span>
                        </div>

                        <div class="theme-preview-shell">
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

        @if ($gym && $isCashierMode)
            <section class="ui-card settings-premium-card">
                <h3 class="ui-heading text-base">Foto de perfil</h3>
                <p class="ui-muted mt-1 text-sm">Como cajero puedes cambiar tu tema y tu foto personal. No tienes acceso a datos del gimnasio.</p>

                <div class="mt-4 flex items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl border border-slate-300/70 bg-slate-100 text-xl font-black text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        @if ($viewerPhotoUrl)
                            <img src="{{ $viewerPhotoUrl }}" alt="Foto de perfil" class="h-full w-full object-cover object-center">
                        @else
                            {{ $viewerInitial }}
                        @endif
                    </div>
                    <div class="ui-muted text-xs">
                        <p>Formatos: JPG, PNG, WEBP.</p>
                        <p>Peso máximo: 2MB.</p>
                    </div>
                </div>

                <form method="POST"
                      action="{{ $profilePhotoUpdateUrl }}"
                      enctype="multipart/form-data"
                      class="mt-4 space-y-3">
                    @csrf
                    <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="ui-input settings-upload-input" required>
                    @error('profile_photo')
                        <p class="text-sm font-semibold text-rose-300">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="ui-button ui-button-primary">Actualizar mi foto</button>
                </form>
            </section>
        @elseif ($gym)
            <div class="grid gap-4 lg:grid-cols-2">
                <section class="ui-card settings-premium-card">
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
                        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="ui-input settings-upload-input">
                        @error('logo')
                            <p class="text-sm font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="ui-button ui-button-primary">Actualizar logo</button>
                    </form>
                </section>

                <section class="ui-card settings-premium-card">
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
                                <select id="gym-address-country" name="address_country_code" class="ui-input">
                                    <option value="">Selecciona país</option>
                                    @foreach ($locationCatalog as $countryCode => $countryMeta)
                                        <option value="{{ $countryCode }}" @selected($gymAddressCountryCode === $countryCode)>{{ $countryMeta['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('address_country_code')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Provincia / Estado (solo lectura)</label>
                                <select id="gym-address-state" name="address_state" class="ui-input">
                                    <option value="">Selecciona provincia/estado</option>
                                    @foreach (array_keys($statesForGymCountry) as $stateName)
                                        <option value="{{ $stateName }}" @selected($gymAddressState === $stateName)>{{ $stateName }}</option>
                                    @endforeach
                                </select>
                                @error('address_state')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Ciudad (solo lectura)</label>
                                <select id="gym-address-city" name="address_city" class="ui-input">
                                    <option value="">Selecciona ciudad</option>
                                    @foreach ($citiesForGymState as $cityName)
                                        <option value="{{ $cityName }}" @selected($gymAddressCity === $cityName)>{{ $cityName }}</option>
                                    @endforeach
                                </select>
                                @error('address_city')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Dirección línea (solo lectura)</label>
                                <input type="text" name="address_line" class="ui-input" value="{{ $gymAddressLine !== '-' ? $gymAddressLine : '' }}" placeholder="Barrio, avenida, referencia">
                                @error('address_line')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
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

            <section class="ui-card settings-premium-card">
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

                    <div class="settings-avatar-grid grid gap-4 md:grid-cols-3">
                        @foreach ($avatarCards as $avatarKey => $avatarMeta)
                            @php
                                $avatarUrl = $gymAvatarUrls[$avatarKey] ?? null;
                            @endphp
                            <div class="settings-avatar-card theme-surface-light border border-slate-300/70 bg-slate-50/80">
                                <div class="settings-avatar-caption">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ $avatarMeta['label'] }}</p>
                                    <span @class([
                                        'settings-avatar-chip',
                                        'is-ready' => $avatarUrl,
                                    ])>
                                        {{ $avatarUrl ? 'Cargado' : 'Vacío' }}
                                    </span>
                                </div>

                                <div class="settings-avatar-preview">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $avatarMeta['label'] }}">
                                    @else
                                        <div class="settings-avatar-empty">
                                            <span class="text-xs font-bold uppercase tracking-[0.2em] text-slate-700">Sin avatar</span>
                                            <span class="text-[10px] uppercase tracking-[0.25em] text-slate-500">{{ strtoupper($avatarKey) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <input type="file"
                                       name="{{ $avatarMeta['field'] }}"
                                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                       class="ui-input settings-upload-input">
                                @error($avatarMeta['field'])
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    @error('avatar_files')
                        <p class="text-sm font-semibold text-rose-300">{{ $message }}</p>
                    @enderror

                    <div class="settings-avatar-help ui-muted text-xs">
                        <p>Recomendado: 900x1200 px o similar (formato vertical).</p>
                        <p>Peso máximo por archivo: 4MB.</p>
                    </div>

                    <button type="submit" class="ui-button ui-button-primary">Guardar avatares</button>
                </form>
            </section>
        @else
            @if ($isSuperAdmin)
                <section class="ui-card settings-premium-card">
                    <h3 class="ui-heading text-base">Configuración SuperAdmin</h3>
                    <p class="ui-muted mt-1 text-sm">
                        Como SuperAdmin gestionas múltiples gimnasios. El logo, teléfono y dirección se administran por cada gym.
                    </p>
                    <form method="POST" action="{{ $superAdminTimezoneUpdateUrl }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Zona horaria de SuperAdmin</label>
                            <div class="theme-surface-light space-y-2 rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <input id="timezone-search"
                                           type="text"
                                           class="ui-input min-w-[220px] flex-1"
                                           placeholder="Buscar por país, ciudad o zona (ej: ecuador, bogota, mexico)">
                                    <button id="timezone-detect-btn" type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold">
                                        Usar navegador
                                    </button>
                                </div>
                                <p id="timezone-detect-hint" class="ui-muted text-xs"></p>
                                <select id="timezone-select" name="superadmin_timezone" class="ui-input" required>
                                    @foreach ($timezoneOptions as $timezoneValue => $timezoneLabel)
                                        <option value="{{ $timezoneValue }}" @selected($superAdminTimezone === $timezoneValue)>
                                            {{ $timezoneLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                <p id="timezone-current" class="ui-muted text-xs"></p>
                            </div>
                            <p class="ui-muted mt-1 text-xs">Esta zona se usa para fechas y horas en paneles de SuperAdmin.</p>
                            @error('superadmin_timezone')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="ui-button ui-button-primary">Guardar zona horaria</button>
                            <a href="{{ route('superadmin.gyms.index') }}" class="ui-button ui-button-muted">Ir a Gimnasios</a>
                        </div>
                    </form>
                </section>
            @else
                <section class="ui-card settings-premium-card">
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
            const locationCatalog = @json($locationCatalog);
            const gymProfileForm = document.getElementById('gym-profile-form');
            const countrySelect = document.getElementById('gym-address-country');
            const stateSelect = document.getElementById('gym-address-state');
            const citySelect = document.getElementById('gym-address-city');
            const locationSectionHint = gymProfileForm?.closest('section')?.querySelector('p.ui-muted');

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
                    empty.textContent = 'Sin coincidencias para tu búsqueda';
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

            const replaceOptions = (select, values, placeholder) => {
                if (!select) return;
                select.innerHTML = '';

                const baseOption = document.createElement('option');
                baseOption.value = '';
                baseOption.textContent = placeholder;
                select.appendChild(baseOption);

                values.forEach((value) => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    select.appendChild(option);
                });
            };

            const statesForCountry = (countryCode) => {
                const country = locationCatalog[String(countryCode || '').toLowerCase()] || null;
                if (!country || !country.states) return [];

                return Object.keys(country.states);
            };

            const citiesForState = (countryCode, stateName) => {
                const country = locationCatalog[String(countryCode || '').toLowerCase()] || null;
                if (!country || !country.states || !country.states[stateName]) return [];

                return country.states[stateName];
            };

            const syncGymCities = (preferredCity = '') => {
                if (!countrySelect || !stateSelect || !citySelect) return;

                const cities = citiesForState(countrySelect.value, stateSelect.value);
                replaceOptions(citySelect, cities, 'Selecciona ciudad');

                if (preferredCity && cities.includes(preferredCity)) {
                    citySelect.value = preferredCity;
                }
            };

            const syncGymStates = (preferredState = '', preferredCity = '') => {
                if (!countrySelect || !stateSelect) return;

                const states = statesForCountry(countrySelect.value);
                replaceOptions(stateSelect, states, 'Selecciona provincia/estado');

                if (preferredState && states.includes(preferredState)) {
                    stateSelect.value = preferredState;
                }

                syncGymCities(preferredCity);
            };

            const initGymLocationPicker = () => {
                if (!countrySelect || !stateSelect || !citySelect) return;

                const selectedState = stateSelect.value;
                const selectedCity = citySelect.value;

                countrySelect.addEventListener('change', () => syncGymStates('', ''));
                stateSelect.addEventListener('change', () => syncGymCities(''));

                syncGymStates(selectedState, selectedCity);

                if (locationSectionHint) {
                    locationSectionHint.textContent = 'Actualiza nombre comercial, teléfono y ubicación del gimnasio.';
                }

                gymProfileForm?.querySelectorAll('label').forEach((label) => {
                    label.textContent = String(label.textContent || '').replace(/\s*\(solo lectura\)/i, '');
                });
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
                        : 'No se pudo detectar automáticamente la zona horaria del navegador.';
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
                        badge.textContent = isActive ? 'Activo' : 'Elegir';
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
            initGymLocationPicker();
            applyTheme(currentTheme);
            setActiveCard(currentTheme);
        })();
    </script>
@endpush
