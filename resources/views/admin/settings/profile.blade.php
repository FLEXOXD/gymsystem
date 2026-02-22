@extends('layouts.panel')

@section('title', __('ui.profile.title'))
@section('page-title', __('ui.profile.page_title'))
@push('styles')
    <style>
        .profile-country-menu {
            max-height: 260px;
            overflow-y: auto;
        }
        .profile-country-flag-preview {
            position: absolute;
            left: 0.45rem;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            height: 1.4rem;
            width: 2rem;
            align-items: center;
            justify-content: center;
            border-radius: 0.35rem;
            border: 1px solid rgb(148 163 184 / 0.35);
            background: rgb(15 23 42 / 0.55);
            overflow: hidden;
        }
        .profile-country-flag-preview img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .profile-country-flag-option {
            display: inline-flex;
            height: 1.1rem;
            width: 1.6rem;
            overflow: hidden;
            border-radius: 0.25rem;
            border: 1px solid rgb(148 163 184 / 0.35);
            background: rgb(15 23 42 / 0.45);
        }
        .profile-country-flag-option img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .profile-country-item[aria-selected="true"] {
            background: rgb(14 116 144 / 0.18);
        }
        .profile-country-item:hover {
            background: rgb(30 41 59 / 0.55);
        }
    </style>
@endpush

@section('content')
    @php
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser?->gym_id === null;
        $currentFullName = trim((string) ($currentUser?->name ?? ''));
        $nameParts = preg_split('/\s+/', $currentFullName) ?: [];
        $defaultFirstName = $nameParts[0] ?? '';
        $defaultLastName = count($nameParts) > 1 ? trim(implode(' ', array_slice($nameParts, 1))) : '';
        $profileFirstName = old('user_first_name', $defaultFirstName);
        $profileLastName = old('user_last_name', $defaultLastName);
        $profileCountryIso = old('user_country_iso', strtoupper((string) ($currentUser?->country_iso ?? $currentUser?->phone_country_iso ?? 'EC')));
        $profileCountryName = old('user_country_name', (string) ($currentUser?->country_name ?? 'Ecuador'));
        $profilePhoneIso = old('user_phone_country_iso', strtoupper((string) ($currentUser?->phone_country_iso ?? 'EC')));
        $profilePhoneDial = old('user_phone_country_dial', (string) ($currentUser?->phone_country_dial ?? '+593'));
        $profilePhoneNumber = old('user_phone_number', (string) ($currentUser?->phone_number ?? ''));
        $profileAddressState = trim((string) ($currentUser?->address_state ?? ''));
        $profileAddressCity = trim((string) ($currentUser?->address_city ?? ''));
        $profileAddressLine = trim((string) ($currentUser?->address_line ?? ''));
        $profileGender = old('user_gender', (string) ($currentUser?->gender ?? ''));
        $profileBirthDate = old('user_birth_date', $currentUser?->birth_date?->format('Y-m-d'));
        $profileIdentificationType = old('user_identification_type', (string) ($currentUser?->identification_type ?? ''));
        $profileIdentificationNumber = old('user_identification_number', (string) ($currentUser?->identification_number ?? ''));
        $profileTimezone = $currentUser?->gym?->timezone ?: config('app.timezone', 'UTC');
        if (! is_string($profileTimezone) || ! in_array($profileTimezone, timezone_identifiers_list(), true)) {
            $profileTimezone = 'UTC';
        }
        $profilePhotoUrl = null;
        if ($currentUser?->profile_photo_path) {
            $profilePhotoPath = ltrim((string) $currentUser->profile_photo_path, '/');
            $profilePhotoUrl = str_starts_with((string) $currentUser->profile_photo_path, 'http://') || str_starts_with((string) $currentUser->profile_photo_path, 'https://')
                ? (string) $currentUser->profile_photo_path
                : (str_starts_with($profilePhotoPath, 'storage/')
                    ? asset($profilePhotoPath)
                    : asset('storage/'.$profilePhotoPath));
        }
        $supportContactLabel = old('support_contact_label', (string) ($currentUser?->support_contact_label ?? ''));
        $supportContactEmail = old('support_contact_email', (string) ($currentUser?->support_contact_email ?? ''));
        $supportContactPhone = old('support_contact_phone', (string) ($currentUser?->support_contact_phone ?? ''));
        $supportContactWhatsapp = old('support_contact_whatsapp', (string) ($currentUser?->support_contact_whatsapp ?? ''));
        $supportContactLink = old('support_contact_link', (string) ($currentUser?->support_contact_link ?? ''));
        $supportContactMessage = old('support_contact_message', (string) ($currentUser?->support_contact_message ?? ''));
        $resolveSupportLogoUrl = static function (?string $path): ?string {
            $raw = trim((string) $path);
            if ($raw === '') {
                return null;
            }
            if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $raw) === 1) {
                return null;
            }
            if (str_starts_with($raw, '/tmp/') || str_starts_with($raw, 'tmp/')) {
                return null;
            }
            if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
                return $raw;
            }
            $clean = ltrim($raw, '/');
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, 8);
            }
            if ($clean === '' || str_contains($clean, '..')) {
                return null;
            }
            if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($clean)) {
                return null;
            }

            return asset('storage/'.$clean);
        };
        $supportContactLogoLightUrl = $resolveSupportLogoUrl((string) ($currentUser?->support_contact_logo_light_path ?? ''));
        $supportContactLogoDarkUrl = $resolveSupportLogoUrl((string) ($currentUser?->support_contact_logo_dark_path ?? ''));
        $legacySupportContactLogoUrl = $resolveSupportLogoUrl((string) ($currentUser?->support_contact_logo_path ?? ''));
        if (! $supportContactLogoLightUrl && $legacySupportContactLogoUrl) {
            $supportContactLogoLightUrl = $legacySupportContactLogoUrl;
        }
        if (! $supportContactLogoDarkUrl && $legacySupportContactLogoUrl) {
            $supportContactLogoDarkUrl = $legacySupportContactLogoUrl;
        }
        $profileDefaultPanel = 'personal';
        if ($errors->has('password') || $errors->has('current_password')) {
            $profileDefaultPanel = 'security';
        }
        if (
            $errors->has('support_contact_label')
            || $errors->has('support_contact_email')
            || $errors->has('support_contact_phone')
            || $errors->has('support_contact_whatsapp')
            || $errors->has('support_contact_link')
            || $errors->has('support_contact_message')
            || $errors->has('support_contact_logo_light')
            || $errors->has('support_contact_logo_dark')
        ) {
            $profileDefaultPanel = 'contact';
        }
        $profileCardSubtitle = $isSuperAdmin
            ? 'Cuenta maestra de SuperAdmin y canales publicos de soporte.'
            : __('ui.profile.card_subtitle');
        $profilePersonalReadOnly = ! $isSuperAdmin;
        $lastAccessRaw = $currentUser?->getRawOriginal('last_login_at');
        $lastAccessLabel = $lastAccessRaw
            ? \Illuminate\Support\Carbon::parse((string) $lastAccessRaw, 'UTC')->timezone($profileTimezone)->format('d/m/Y H:i:s').' ('.$profileTimezone.')'
            : __('ui.profile.no_records');
        $membershipStatusVariant = match ((string) ($membershipSummary['status'] ?? '')) {
            'active' => 'success',
            'grace' => 'warning',
            'suspended' => 'danger',
            default => 'muted',
        };
        $membershipStatusLabel = match ((string) ($membershipSummary['status'] ?? '')) {
            'active' => __('ui.profile.membership_status_active'),
            'grace' => __('ui.profile.membership_status_grace'),
            'suspended' => __('ui.profile.membership_status_suspended'),
            default => __('ui.profile.membership_status_unknown'),
        };
    @endphp

    <div class="space-y-6">
        <x-card id="profile-section" :title="__('ui.profile.card_title')" :subtitle="$profileCardSubtitle">
            <div id="profile-accordion" class="space-y-3" data-default-panel="{{ $profileDefaultPanel }}">
                <section class="ui-card overflow-hidden p-0">
                    <button type="button"
                            class="profile-accordion-trigger flex w-full items-center justify-between gap-3 px-4 py-3 text-left {{ $profileDefaultPanel === 'personal' ? 'bg-[var(--card-muted)]' : '' }}"
                            data-profile-trigger="personal"
                            aria-expanded="{{ $profileDefaultPanel === 'personal' ? 'true' : 'false' }}"
                            aria-controls="profile-panel-personal">
                        <span class="flex items-center gap-2">
                            <svg class="profile-chevron h-4 w-4 shrink-0 transition-transform duration-200 {{ $profileDefaultPanel === 'personal' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                            <span class="ui-heading text-base">{{ __('ui.profile.personal_info') }}</span>
                        </span>
                    </button>
                    <div id="profile-panel-personal" class="profile-accordion-panel border-t border-[var(--border)] px-4 py-4 {{ $profileDefaultPanel === 'personal' ? '' : 'hidden' }}">
                        <p class="ui-muted text-sm">{{ __('ui.profile.personal_info_hint') }}</p>

                        <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            @if ($profilePersonalReadOnly)
                                <p class="rounded-xl border border-amber-300/60 bg-amber-100/60 px-3 py-2 text-sm font-semibold text-amber-900 dark:border-amber-300/40 dark:bg-amber-300/10 dark:text-amber-100">
                                    Perfil en solo lectura. Estos datos se editan desde SuperAdmin.
                                </p>
                            @endif

                            <fieldset class="space-y-3" @disabled($profilePersonalReadOnly)>
                            <div class="rounded-xl border border-[var(--border)] p-3">
                                <label class="ui-muted mb-2 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.profile_photo') }}</label>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border border-[var(--border)] bg-slate-100 text-xs font-black uppercase text-slate-600 dark:bg-slate-900 dark:text-slate-300">
                                        @if ($profilePhotoUrl)
                                            <img src="{{ $profilePhotoUrl }}" alt="{{ __('ui.profile.profile_photo') }}" class="h-full w-full object-cover">
                                        @else
                                            {{ mb_substr((string) ($currentUser?->name ?? 'U'), 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="min-w-[220px] flex-1">
                                        <input type="file"
                                               name="user_profile_photo"
                                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                               class="ui-input">
                                        <p class="ui-muted mt-1 text-xs">{{ __('ui.profile.profile_photo_hint') }}</p>
                                    </div>
                                </div>
                                @error('user_profile_photo')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.first_name') }}</label>
                                    <input id="profile-first-name" type="text" name="user_first_name" value="{{ $profileFirstName }}" class="ui-input" required>
                                    @error('user_first_name')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.last_name') }}</label>
                                    <input id="profile-last-name" type="text" name="user_last_name" value="{{ $profileLastName }}" class="ui-input" required>
                                    @error('user_last_name')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.email') }}</label>
                                <input id="profile-email" type="email" name="user_email" value="{{ old('user_email', $currentUser?->email) }}" class="ui-input" required>
                                @error('user_email')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.gender') }}</label>
                                    <select name="user_gender" class="ui-input">
                                        <option value="">{{ __('ui.profile.gender_options.not_set') }}</option>
                                        <option value="male" @selected($profileGender === 'male')>{{ __('ui.profile.gender_options.male') }}</option>
                                        <option value="female" @selected($profileGender === 'female')>{{ __('ui.profile.gender_options.female') }}</option>
                                        <option value="other" @selected($profileGender === 'other')>{{ __('ui.profile.gender_options.other') }}</option>
                                        <option value="prefer_not_say" @selected($profileGender === 'prefer_not_say')>{{ __('ui.profile.gender_options.prefer_not_say') }}</option>
                                    </select>
                                    @error('user_gender')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.birth_date') }}</label>
                                    <input type="date" name="user_birth_date" value="{{ $profileBirthDate }}" class="ui-input">
                                    @error('user_birth_date')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.identification_type') }}</label>
                                    <select id="profile-identification-type" name="user_identification_type" class="ui-input">
                                        <option value="">{{ __('ui.profile.identification_options.not_set') }}</option>
                                        <option value="cedula" @selected($profileIdentificationType === 'cedula')>{{ __('ui.profile.identification_options.cedula') }}</option>
                                        <option value="dni" @selected($profileIdentificationType === 'dni')>{{ __('ui.profile.identification_options.dni') }}</option>
                                        <option value="passport" @selected($profileIdentificationType === 'passport')>{{ __('ui.profile.identification_options.passport') }}</option>
                                    </select>
                                    @error('user_identification_type')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.identification_number') }}</label>
                                    <input type="text"
                                           id="profile-identification-number"
                                           name="user_identification_number"
                                           value="{{ $profileIdentificationNumber }}"
                                           class="ui-input"
                                           placeholder="{{ __('ui.profile.identification_number_placeholder') }}">
                                    @error('user_identification_number')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)]">
                                <div class="relative">
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.country') }}</label>
                                    <div class="relative">
                                        <span id="profile-country-flag-preview" class="profile-country-flag-preview" aria-hidden="true"></span>
                                        <input id="profile-country-search"
                                               type="text"
                                               class="ui-input pl-12 pr-11"
                                               placeholder="{{ __('ui.profile.country_placeholder') }}"
                                               autocomplete="off"
                                               value="{{ $profileCountryName }}"
                                               required>
                                        <button id="profile-country-toggle"
                                                type="button"
                                                class="ui-button ui-button-ghost absolute right-1 top-1 px-3 py-2 text-xs"
                                                aria-label="{{ __('ui.profile.country_toggle_aria') }}">
                                            &#9662;
                                        </button>
                                    </div>
                                    <div id="profile-country-menu"
                                         class="profile-country-menu theme-surface-light absolute z-30 mt-1 hidden w-full rounded-xl border border-[var(--border)] bg-[var(--card)] p-1 shadow-2xl"></div>
                                    <input id="profile-country-iso" type="hidden" name="user_country_iso" value="{{ $profileCountryIso }}" required>
                                    <input id="profile-country-name-hidden" type="hidden" name="user_country_name" value="{{ $profileCountryName }}" required>
                                    <input id="profile-phone-country-iso" type="hidden" name="user_phone_country_iso" value="{{ $profilePhoneIso }}" required>
                                    @error('user_country_iso')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                    @error('user_country_name')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                    @error('user_phone_country_iso')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.phone_required') }}</label>
                                    <div class="grid grid-cols-[120px_minmax(0,1fr)] gap-2">
                                        <input id="profile-phone-dial" name="user_phone_country_dial" type="text" class="ui-input text-center font-bold" value="{{ $profilePhoneDial }}" readonly required>
                                        <input id="profile-phone-number" name="user_phone_number" type="text" inputmode="numeric" autocomplete="tel-national" placeholder="{{ __('ui.profile.phone_placeholder') }}" class="ui-input" value="{{ $profilePhoneNumber }}" required>
                                    </div>
                                    @error('user_phone_country_dial')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                    @error('user_phone_number')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-3">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Provincia / Estado</label>
                                    <input type="text" class="ui-input" value="{{ $profileAddressState !== '' ? $profileAddressState : '-' }}" readonly>
                                </div>
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Ciudad</label>
                                    <input type="text" class="ui-input" value="{{ $profileAddressCity !== '' ? $profileAddressCity : '-' }}" readonly>
                                </div>
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Direccion (linea)</label>
                                    <input type="text" class="ui-input" value="{{ $profileAddressLine !== '' ? $profileAddressLine : '-' }}" readonly>
                                </div>
                            </div>
                            </fieldset>

                            @if (! $profilePersonalReadOnly)
                                <button type="submit" class="ui-button ui-button-primary">{{ __('ui.profile.save_profile') }}</button>
                            @endif
                        </form>
                    </div>
                </section>

                @if ($isSuperAdmin)
                <section class="ui-card overflow-hidden p-0">
                    <button type="button"
                            class="profile-accordion-trigger flex w-full items-center justify-between gap-3 px-4 py-3 text-left {{ $profileDefaultPanel === 'contact' ? 'bg-[var(--card-muted)]' : '' }}"
                            data-profile-trigger="contact"
                            aria-expanded="{{ $profileDefaultPanel === 'contact' ? 'true' : 'false' }}"
                            aria-controls="profile-panel-contact">
                        <span class="flex items-center gap-2">
                            <svg class="profile-chevron h-4 w-4 shrink-0 transition-transform duration-200 {{ $profileDefaultPanel === 'contact' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                            <span class="ui-heading text-base">Contacto para clientes</span>
                        </span>
                    </button>
                    <div id="profile-panel-contact" class="profile-accordion-panel border-t border-[var(--border)] px-4 py-4 {{ $profileDefaultPanel === 'contact' ? '' : 'hidden' }}">
                        <p class="ui-muted text-sm">Estos datos se mostraran en la opcion "Contactarse" para todos los usuarios.</p>

                        <form method="POST" action="{{ route('settings.superadmin-contact.update') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Etiqueta</label>
                                    <input type="text" name="support_contact_label" value="{{ $supportContactLabel }}" class="ui-input" placeholder="Ej: Soporte GymSystem">
                                    @error('support_contact_label')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Correo de contacto</label>
                                    <input type="email" name="support_contact_email" value="{{ $supportContactEmail }}" class="ui-input" placeholder="soporte@tuempresa.com">
                                    @error('support_contact_email')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Telefono</label>
                                    <input type="text" name="support_contact_phone" value="{{ $supportContactPhone }}" class="ui-input" placeholder="+593 999 999 999">
                                    @error('support_contact_phone')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">WhatsApp</label>
                                    <input type="text" name="support_contact_whatsapp" value="{{ $supportContactWhatsapp }}" class="ui-input" placeholder="+593 999 999 999">
                                    @error('support_contact_whatsapp')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Enlace adicional</label>
                                <input type="url" name="support_contact_link" value="{{ $supportContactLink }}" class="ui-input" placeholder="https://tudominio.com/soporte">
                                @error('support_contact_link')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Mensaje para clientes</label>
                                <textarea name="support_contact_message" rows="3" class="ui-input" placeholder="Ej: Respuesta en horario laboral de 08:00 a 18:00.">{{ $supportContactMessage }}</textarea>
                                @error('support_contact_message')
                                    <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Logo para tema claro</label>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="flex h-16 w-24 items-center justify-center overflow-hidden rounded-lg border border-[var(--border)] bg-white">
                                            @if ($supportContactLogoLightUrl)
                                                <img src="{{ $supportContactLogoLightUrl }}" alt="Logo claro" class="h-full w-full object-contain p-1">
                                            @else
                                                <span class="text-[10px] font-bold uppercase text-slate-500">Sin logo</span>
                                            @endif
                                        </div>
                                        <div class="min-w-[220px] flex-1">
                                            <input type="file"
                                                   name="support_contact_logo_light"
                                                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                                   class="ui-input">
                                            <p class="ui-muted mt-1 text-xs">PNG recomendado. Maximo 4MB.</p>
                                        </div>
                                    </div>
                                    @error('support_contact_logo_light')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Logo para tema oscuro</label>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div class="flex h-16 w-24 items-center justify-center overflow-hidden rounded-lg border border-[var(--border)] bg-slate-950">
                                            @if ($supportContactLogoDarkUrl)
                                                <img src="{{ $supportContactLogoDarkUrl }}" alt="Logo oscuro" class="h-full w-full object-contain p-1">
                                            @else
                                                <span class="text-[10px] font-bold uppercase text-slate-300">Sin logo</span>
                                            @endif
                                        </div>
                                        <div class="min-w-[220px] flex-1">
                                            <input type="file"
                                                   name="support_contact_logo_dark"
                                                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                                   class="ui-input">
                                            <p class="ui-muted mt-1 text-xs">PNG recomendado. Maximo 4MB.</p>
                                        </div>
                                    </div>
                                    @error('support_contact_logo_dark')
                                        <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="submit" class="ui-button ui-button-primary">Guardar contacto de SuperAdmin</button>
                                <a href="{{ route('contact.index') }}" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold">Ver como lo ven tus clientes</a>
                            </div>
                        </form>
                    </div>
                </section>
                @endif

                @if (! $isSuperAdmin)
                <section class="ui-card overflow-hidden p-0">
                    <button type="button"
                            class="profile-accordion-trigger flex w-full items-center justify-between gap-3 px-4 py-3 text-left {{ $profileDefaultPanel === 'billing' ? 'bg-[var(--card-muted)]' : '' }}"
                            data-profile-trigger="billing"
                            aria-expanded="{{ $profileDefaultPanel === 'billing' ? 'true' : 'false' }}"
                            aria-controls="profile-panel-billing">
                        <span class="flex items-center gap-2">
                            <svg class="profile-chevron h-4 w-4 shrink-0 transition-transform duration-200 {{ $profileDefaultPanel === 'billing' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                            <span class="ui-heading text-base">{{ __('ui.profile.billing_data') }}</span>
                        </span>
                    </button>
                    <div id="profile-panel-billing" class="profile-accordion-panel border-t border-[var(--border)] px-4 py-4 {{ $profileDefaultPanel === 'billing' ? '' : 'hidden' }}">
                        <p class="ui-muted text-sm">{{ __('ui.profile.billing_hint') }}</p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.first_name') }}</label>
                                <input id="billing-first-name" type="text" class="ui-input" value="{{ $profileFirstName }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.last_name') }}</label>
                                <input id="billing-last-name" type="text" class="ui-input" value="{{ $profileLastName }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.identification_type') }}</label>
                                <input id="billing-identification-type" type="text" class="ui-input" value="{{ $profileIdentificationType !== '' ? __('ui.profile.identification_options.'.$profileIdentificationType) : __('ui.profile.identification_options.not_set') }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.identification_number') }}</label>
                                <input id="billing-identification-number" type="text" class="ui-input" value="{{ $profileIdentificationNumber }}" readonly>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.phone_required') }}</label>
                                <input id="billing-phone" type="text" class="ui-input" value="{{ trim($profilePhoneDial.' '.$profilePhoneNumber) }}" readonly>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.email') }}</label>
                                <input id="billing-email" type="text" class="ui-input" value="{{ old('user_email', $currentUser?->email) }}" readonly>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ui-card overflow-hidden p-0">
                    <button type="button"
                            class="profile-accordion-trigger flex w-full items-center justify-between gap-3 px-4 py-3 text-left {{ $profileDefaultPanel === 'membership' ? 'bg-[var(--card-muted)]' : '' }}"
                            data-profile-trigger="membership"
                            aria-expanded="{{ $profileDefaultPanel === 'membership' ? 'true' : 'false' }}"
                            aria-controls="profile-panel-membership">
                        <span class="flex items-center gap-2">
                            <svg class="profile-chevron h-4 w-4 shrink-0 transition-transform duration-200 {{ $profileDefaultPanel === 'membership' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                            <span class="ui-heading text-base">{{ __('ui.profile.active_membership') }}</span>
                        </span>
                    </button>
                    <div id="profile-panel-membership" class="profile-accordion-panel border-t border-[var(--border)] px-4 py-4 {{ $profileDefaultPanel === 'membership' ? '' : 'hidden' }}">
                        @if ($membershipSummary)
                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3 xl:col-span-2">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_plan') }}</p>
                                    <p class="mt-1 text-base font-semibold text-slate-900">{{ $membershipSummary['plan_name'] }}</p>
                                </div>
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_status') }}</p>
                                    <div class="mt-1"><x-badge :variant="$membershipStatusVariant">{{ $membershipStatusLabel }}</x-badge></div>
                                </div>
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_days_left') }}</p>
                                    <p class="mt-1 text-base font-semibold text-slate-900">
                                        @if ($membershipSummary['remaining_days'] === null)
                                            -
                                        @elseif ($membershipSummary['remaining_days'] >= 0)
                                            {{ $membershipSummary['remaining_days'] }} {{ __('ui.profile.days') }}
                                        @else
                                            {{ abs($membershipSummary['remaining_days']) }} {{ __('ui.profile.days_overdue') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_amount') }}</p>
                                    <p class="mt-1 text-base font-semibold text-slate-900">{{ \App\Support\Currency::format((float) ($membershipSummary['price'] ?? 0), $currentUser?->gym?->currency_code ?? 'USD') }}</p>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 md:grid-cols-3">
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_start') }}</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $membershipSummary['starts_at']?->format('Y-m-d') ?? '-' }}</p>
                                </div>
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_end') }}</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $membershipSummary['ends_at']?->format('Y-m-d') ?? '-' }}</p>
                                </div>
                                <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.membership_payment_method') }}</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $membershipSummary['payment_method'] !== '' ? $membershipSummary['payment_method'] : '-' }}</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="ui-heading text-sm">{{ __('ui.profile.membership_invoices') }}</h4>
                                <div class="mt-2 overflow-x-auto rounded-xl border border-slate-300/70 dark:border-slate-700">
                                    <table class="ui-table min-w-[700px]">
                                        <thead>
                                        <tr>
                                            <th>{{ __('ui.profile.invoice_period') }}</th>
                                            <th>{{ __('ui.profile.invoice_amount') }}</th>
                                            <th>{{ __('ui.profile.invoice_method') }}</th>
                                            <th>{{ __('ui.profile.invoice_status') }}</th>
                                            <th>{{ __('ui.profile.invoice_date') }}</th>
                                            <th>{{ __('ui.profile.invoice_action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($membershipInvoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice['period'] }}</td>
                                                <td>{{ \App\Support\Currency::format((float) ($invoice['amount'] ?? 0), $currentUser?->gym?->currency_code ?? 'USD') }}</td>
                                                <td>{{ $invoice['payment_method'] !== '' ? $invoice['payment_method'] : '-' }}</td>
                                                <td>
                                                    <x-badge :variant="$invoice['status'] === 'paid' ? 'success' : 'warning'">
                                                        {{ $invoice['status'] === 'paid' ? __('ui.profile.invoice_paid') : __('ui.profile.invoice_pending') }}
                                                    </x-badge>
                                                </td>
                                                <td>{{ $invoice['recorded_at']?->format('Y-m-d H:i') ?? '-' }}</td>
                                                <td>
                                                    <x-ui.button :href="route('profile.membership-invoice.pdf', ['subscription' => (int) ($invoice['id'] ?? 0)])" target="_blank" rel="noopener" size="sm" variant="ghost">
                                                        {{ __('ui.profile.invoice_download_pdf') }}
                                                    </x-ui.button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-sm text-slate-500 dark:text-slate-300">{{ __('ui.profile.no_invoices') }}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl border border-slate-300/70 bg-slate-50/80 p-4 text-center dark:border-slate-700 dark:bg-slate-900/65">
                                <p class="ui-heading text-base">{{ __('ui.profile.no_active_membership_title') }}</p>
                                <p class="ui-muted mt-1 text-sm">{{ __('ui.profile.no_active_membership_desc') }}</p>
                            </div>
                        @endif
                    </div>
                </section>
                @endif

                <section class="ui-card overflow-hidden p-0">
                    <button type="button"
                            class="profile-accordion-trigger flex w-full items-center justify-between gap-3 px-4 py-3 text-left {{ $profileDefaultPanel === 'security' ? 'bg-[var(--card-muted)]' : '' }}"
                            data-profile-trigger="security"
                            aria-expanded="{{ $profileDefaultPanel === 'security' ? 'true' : 'false' }}"
                            aria-controls="profile-panel-security">
                        <span class="flex items-center gap-2">
                            <svg class="profile-chevron h-4 w-4 shrink-0 transition-transform duration-200 {{ $profileDefaultPanel === 'security' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                            <span class="ui-heading text-base">{{ __('ui.profile.security') }}</span>
                        </span>
                    </button>
                    <div id="profile-panel-security" class="profile-accordion-panel border-t border-[var(--border)] px-4 py-4 {{ $profileDefaultPanel === 'security' ? '' : 'hidden' }}">
                        <p class="ui-muted text-sm">{{ __('ui.profile.security_hint') }}</p>

                        <form method="POST" action="{{ route('settings.profile.password.update') }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.current_password') }}</label>
                                <input type="password" name="current_password" class="ui-input" required>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.new_password') }}</label>
                                <input type="password" name="password" class="ui-input" required>
                            </div>
                            <div>
                                <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.confirm_password') }}</label>
                                <input type="password" name="password_confirmation" class="ui-input" required>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="ui-button ui-button-primary">{{ __('ui.profile.update_password') }}</button>
                        </form>

                        <form method="POST" action="{{ route('settings.profile.logout-others') }}" class="mt-4 space-y-3">
                            @csrf
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.current_password_confirm') }}</label>
                            <input type="password" name="current_password" class="ui-input" required>
                            @error('current_password')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="ui-button ui-button-danger">{{ __('ui.profile.logout_other_devices') }}</button>
                        </form>
                    </div>
                </section>

                <section class="ui-card overflow-hidden p-0">
                    <button type="button"
                            class="profile-accordion-trigger flex w-full items-center justify-between gap-3 px-4 py-3 text-left {{ $profileDefaultPanel === 'session' ? 'bg-[var(--card-muted)]' : '' }}"
                            data-profile-trigger="session"
                            aria-expanded="{{ $profileDefaultPanel === 'session' ? 'true' : 'false' }}"
                            aria-controls="profile-panel-session">
                        <span class="flex items-center gap-2">
                            <svg class="profile-chevron h-4 w-4 shrink-0 transition-transform duration-200 {{ $profileDefaultPanel === 'session' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 011.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                            <span class="ui-heading text-base">{{ __('ui.profile.session_data') }}</span>
                        </span>
                    </button>
                    <div id="profile-panel-session" class="profile-accordion-panel border-t border-[var(--border)] px-4 py-4 {{ $profileDefaultPanel === 'session' ? '' : 'hidden' }}">
                        <p class="ui-muted text-sm">{{ __('ui.profile.session_data_hint') }}</p>

                        <div class="mt-4 space-y-3">
                            <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.role') }}</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $userRoleLabel }}</p>
                            </div>

                            <div class="theme-surface-light rounded-xl border border-slate-300/70 bg-slate-50/80 p-3">
                                <p class="ui-muted text-xs font-bold uppercase tracking-wide">{{ __('ui.profile.last_access') }}</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $lastAccessLabel }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </x-card>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const fallbackCountries = @json($phoneCountryOptions);
            const countrySearchInput = document.getElementById('profile-country-search');
            const countryToggleButton = document.getElementById('profile-country-toggle');
            const countryMenu = document.getElementById('profile-country-menu');
            const countryFlagPreview = document.getElementById('profile-country-flag-preview');
            const countryIsoHidden = document.getElementById('profile-country-iso');
            const countryNameHidden = document.getElementById('profile-country-name-hidden');
            const phoneCountryIsoHidden = document.getElementById('profile-phone-country-iso');
            const profileFirstName = document.getElementById('profile-first-name');
            const profileLastName = document.getElementById('profile-last-name');
            const profileEmail = document.getElementById('profile-email');
            const profileIdentificationType = document.getElementById('profile-identification-type');
            const profileIdentificationNumber = document.getElementById('profile-identification-number');
            const profilePhoneDial = document.getElementById('profile-phone-dial');
            const profilePhoneNumber = document.getElementById('profile-phone-number');
            const billingFirstName = document.getElementById('billing-first-name');
            const billingLastName = document.getElementById('billing-last-name');
            const billingIdentificationType = document.getElementById('billing-identification-type');
            const billingIdentificationNumber = document.getElementById('billing-identification-number');
            const billingPhone = document.getElementById('billing-phone');
            const billingEmail = document.getElementById('billing-email');
            const activeLocale = (document.documentElement.getAttribute('lang') || 'es').toLowerCase();
            let countriesIndex = [];
            let isMenuOpen = false;
            let applyCountry = () => {};

            const normalize = (value) => String(value || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            const flagFromIso = (isoValue) => {
                const iso = String(isoValue || '').toUpperCase();
                if (!/^[A-Z]{2}$/.test(iso)) return '';
                const points = [...iso].map((char) => 127397 + char.charCodeAt(0));
                return String.fromCodePoint(...points);
            };

            const flagImageUrl = (isoValue) => {
                const iso = String(isoValue || '').toLowerCase();
                if (!/^[a-z]{2}$/.test(iso)) return '';
                return `https://flagcdn.com/w40/${iso}.png`;
            };

            const uniqueCountries = (rows) => {
                const map = new Map();
                rows.forEach((row) => {
                    const iso = String(row?.iso || '').toUpperCase();
                    if (!/^[A-Z]{2}$/.test(iso)) return;
                    if (map.has(iso)) return;
                    map.set(iso, {
                        iso,
                        name: String(row?.name || iso),
                        flag: String(row?.flag || flagFromIso(iso)),
                        dial: String(row?.dial || '+1'),
                    });
                });
                return Array.from(map.values()).sort((a, b) => a.name.localeCompare(b.name, activeLocale, { sensitivity: 'base' }));
            };

            const parseDial = (iddRoot, iddSuffixes) => {
                const root = String(iddRoot || '').trim();
                if (!root.startsWith('+')) return '+1';
                if (Array.isArray(iddSuffixes) && iddSuffixes.length > 0) {
                    const suffix = String(iddSuffixes[0] || '').replace(/[^\d]/g, '');
                    return suffix ? `${root}${suffix}` : root;
                }
                return root;
            };

            const loadCountriesFromApi = async () => {
                try {
                    const response = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2,flag,idd', { headers: { Accept: 'application/json' } });
                    if (!response.ok) return [];
                    const payload = await response.json();
                    if (!Array.isArray(payload)) return [];
                    return payload.map((row) => ({
                        iso: String(row?.cca2 || ''),
                        name: String(row?.name?.common || ''),
                        flag: String(row?.flag || ''),
                        dial: parseDial(row?.idd?.root, row?.idd?.suffixes),
                    }));
                } catch (error) {
                    return [];
                }
            };

            const findCountryByText = (countries, text) => {
                const q = normalize(text);
                if (q === '') return null;
                let exact = countries.find((country) => normalize(`${country.flag} ${country.name}`) === q);
                if (exact) return exact;
                exact = countries.find((country) => normalize(country.name) === q || normalize(country.iso) === q);
                if (exact) return exact;
                return countries.find((country) => {
                    const haystack = normalize(`${country.flag} ${country.name} ${country.iso} ${country.dial}`);
                    return haystack.includes(q);
                }) || null;
            };

            const openCountryMenu = () => {
                if (!countryMenu) return;
                countryMenu.classList.remove('hidden');
                isMenuOpen = true;
            };

            const closeCountryMenu = () => {
                if (!countryMenu) return;
                countryMenu.classList.add('hidden');
                isMenuOpen = false;
            };

            const filterCountries = (term) => {
                const q = normalize(term);
                if (q === '') return countriesIndex;
                return countriesIndex.filter((country) => {
                    const haystack = normalize(`${country.flag} ${country.name} ${country.iso} ${country.dial}`);
                    return haystack.includes(q);
                });
            };

            const renderCountryMenu = (items) => {
                if (!countryMenu) return;
                countryMenu.innerHTML = '';

                if (items.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'px-3 py-2 text-xs text-slate-500';
                    empty.textContent = @js(__('ui.profile.no_matches'));
                    countryMenu.appendChild(empty);
                    return;
                }

                items.forEach((country) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'profile-country-item flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm';
                    button.setAttribute('data-country-iso', country.iso);
                    button.setAttribute('aria-selected', String(countryIsoHidden?.value === country.iso));
                    button.innerHTML = `
                        <span class="min-w-0 truncate font-semibold flex items-center gap-2">
                            <span class="profile-country-flag-option"><img src="${flagImageUrl(country.iso)}" alt="${country.iso}" loading="lazy"></span>
                            <span>${country.flag} ${country.name}</span>
                        </span>
                        <span class="text-xs text-slate-500">${country.dial}</span>
                    `;
                    button.addEventListener('click', () => {
                        applyCountry(country);
                        closeCountryMenu();
                    });
                    countryMenu.appendChild(button);
                });
            };

            const initCountryAndPhonePicker = async () => {
                if (!countrySearchInput || !countryIsoHidden || !countryNameHidden || !phoneCountryIsoHidden || !profilePhoneDial || !countryMenu) return;

                const apiCountries = await loadCountriesFromApi();
                countriesIndex = uniqueCountries([...fallbackCountries, ...apiCountries]);

                applyCountry = (country) => {
                    if (!country) return;
                    countrySearchInput.value = `${country.flag} ${country.name}`.trim();
                    countryIsoHidden.value = country.iso;
                    countryNameHidden.value = country.name;
                    phoneCountryIsoHidden.value = country.iso;
                    profilePhoneDial.value = country.dial || '+1';
                    profilePhoneDial.dispatchEvent(new Event('input', { bubbles: true }));
                    if (countryFlagPreview) {
                        const url = flagImageUrl(country.iso);
                        countryFlagPreview.innerHTML = url
                            ? `<img src="${url}" alt="${country.iso}" loading="lazy">`
                            : `<span class="text-[10px] font-bold">${country.iso}</span>`;
                    }
                    renderCountryMenu(filterCountries(countrySearchInput.value));
                };

                const hydrateByCurrentValues = () => {
                    const baseIso = String(countryIsoHidden.value || phoneCountryIsoHidden.value || '').toUpperCase();
                    if (baseIso !== '') {
                        const matchByIso = countriesIndex.find((country) => country.iso === baseIso);
                        if (matchByIso) {
                            applyCountry(matchByIso);
                            return;
                        }
                    }

                    const textSeed = String(countrySearchInput.value || '');
                    const matchByText = findCountryByText(countriesIndex, textSeed);
                    if (matchByText) {
                        applyCountry(matchByText);
                    } else if (countriesIndex.length > 0) {
                        applyCountry(countriesIndex[0]);
                    }
                };

                const detectByIp = async () => {
                    if (String(countryIsoHidden.value || '').trim() !== '') return;
                    try {
                        const response = await fetch('https://ipapi.co/json/', { headers: { Accept: 'application/json' } });
                        if (!response.ok) return;
                        const payload = await response.json();
                        const ipIso = String(payload?.country_code || '').toUpperCase();
                        if (!ipIso) return;
                        const match = countriesIndex.find((country) => country.iso === ipIso);
                        if (match) applyCountry(match);
                    } catch (error) {}
                };

                const bindInputHandlers = () => {
                    const refreshMenu = () => {
                        renderCountryMenu(filterCountries(countrySearchInput.value));
                    };

                    countrySearchInput.addEventListener('focus', () => {
                        refreshMenu();
                        openCountryMenu();
                    });

                    countrySearchInput.addEventListener('input', () => {
                        refreshMenu();
                        openCountryMenu();
                    });

                    countrySearchInput.addEventListener('keydown', (event) => {
                        if (event.key === 'ArrowDown') {
                            event.preventDefault();
                            openCountryMenu();
                            const first = countryMenu.querySelector('button[data-country-iso]');
                            first?.focus();
                        } else if (event.key === 'Escape') {
                            closeCountryMenu();
                        } else if (event.key === 'Enter') {
                            const match = findCountryByText(countriesIndex, countrySearchInput.value);
                            if (match) {
                                event.preventDefault();
                                applyCountry(match);
                                closeCountryMenu();
                            }
                        }
                    });

                    countrySearchInput.addEventListener('blur', () => {
                        setTimeout(() => {
                            if (!countryMenu.contains(document.activeElement)) {
                                closeCountryMenu();
                                const match = findCountryByText(countriesIndex, countrySearchInput.value);
                                if (match) {
                                    applyCountry(match);
                                } else if (countryIsoHidden.value) {
                                    const current = countriesIndex.find((country) => country.iso === countryIsoHidden.value);
                                    if (current) applyCountry(current);
                                }
                            }
                        }, 120);
                    });

                    countryToggleButton?.addEventListener('click', (event) => {
                        event.preventDefault();
                        if (isMenuOpen) {
                            closeCountryMenu();
                            return;
                        }
                        refreshMenu();
                        openCountryMenu();
                        countrySearchInput.focus();
                    });

                    document.addEventListener('click', (event) => {
                        if (!(event.target instanceof Node)) return;
                        if (countryMenu.contains(event.target)) return;
                        if (countrySearchInput.contains(event.target)) return;
                        if (countryToggleButton && countryToggleButton.contains(event.target)) return;
                        closeCountryMenu();
                    });

                    countryMenu.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeCountryMenu();
                            countrySearchInput.focus();
                        }
                        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                            event.preventDefault();
                            const items = Array.from(countryMenu.querySelectorAll('button[data-country-iso]'));
                            const currentIndex = items.indexOf(document.activeElement);
                            const nextIndex = event.key === 'ArrowDown'
                                ? Math.min(items.length - 1, currentIndex + 1)
                                : Math.max(0, currentIndex - 1);
                            items[nextIndex]?.focus();
                        }
                    });
                };

                hydrateByCurrentValues();
                renderCountryMenu(filterCountries(countrySearchInput.value));
                bindInputHandlers();
                detectByIp();
            };

            const initProfilePhoneNumber = () => {
                const sanitizeNumber = () => {
                    if (!profilePhoneNumber) return;
                    profilePhoneNumber.value = String(profilePhoneNumber.value || '').replace(/\D+/g, '');
                };

                profilePhoneNumber?.addEventListener('input', sanitizeNumber);
                sanitizeNumber();
            };

            const initBillingMirror = () => {
                const identificationLabels = {
                    cedula: @js(__('ui.profile.identification_options.cedula')),
                    dni: @js(__('ui.profile.identification_options.dni')),
                    passport: @js(__('ui.profile.identification_options.passport')),
                };
                const defaultIdentification = @js(__('ui.profile.identification_options.not_set'));

                const sync = () => {
                    if (billingFirstName && profileFirstName) billingFirstName.value = profileFirstName.value || '';
                    if (billingLastName && profileLastName) billingLastName.value = profileLastName.value || '';
                    if (billingEmail && profileEmail) billingEmail.value = profileEmail.value || '';
                    if (billingIdentificationNumber && profileIdentificationNumber) billingIdentificationNumber.value = profileIdentificationNumber.value || '';

                    if (billingIdentificationType && profileIdentificationType) {
                        const key = String(profileIdentificationType.value || '').toLowerCase();
                        billingIdentificationType.value = identificationLabels[key] || defaultIdentification;
                    }

                    if (billingPhone) {
                        const dial = profilePhoneDial?.value || '';
                        const number = profilePhoneNumber?.value || '';
                        billingPhone.value = `${dial} ${number}`.trim();
                    }
                };

                [profileFirstName, profileLastName, profileEmail, profileIdentificationType, profileIdentificationNumber, profilePhoneDial, profilePhoneNumber]
                    .forEach((element) => element?.addEventListener('input', sync));
                [profileIdentificationType]
                    .forEach((element) => element?.addEventListener('change', sync));

                sync();
            };

            const initProfileAccordion = () => {
                const accordion = document.getElementById('profile-accordion');
                if (!accordion) return;

                const triggers = Array.from(accordion.querySelectorAll('[data-profile-trigger]'));
                if (triggers.length === 0) return;

                const setActive = (key) => {
                    triggers.forEach((trigger) => {
                        const isActive = trigger.dataset.profileTrigger === key;
                        trigger.setAttribute('aria-expanded', String(isActive));
                        trigger.classList.toggle('bg-[var(--card-muted)]', isActive);

                        const icon = trigger.querySelector('.profile-chevron');
                        icon?.classList.toggle('rotate-180', isActive);

                        const panelId = trigger.getAttribute('aria-controls');
                        const panel = panelId ? document.getElementById(panelId) : null;
                        panel?.classList.toggle('hidden', !isActive);
                    });
                };

                const defaultPanel = accordion.dataset.defaultPanel || triggers[0].dataset.profileTrigger;
                setActive(defaultPanel);

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        setActive(trigger.dataset.profileTrigger || defaultPanel);
                    });
                });
            };

            initCountryAndPhonePicker();
            initProfilePhoneNumber();
            initBillingMirror();
            initProfileAccordion();
        })();
    </script>
@endpush


