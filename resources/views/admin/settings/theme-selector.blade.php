@extends('layouts.panel')

@section('title', 'Config')
@section('page-title', 'Configuracion')

@section('content')
    @php
        $gymInitials = '';
        $gymLogoUrl = null;
        $isSuperAdmin = auth()->user()?->gym_id === null;
        if ($gym) {
            $gymInitials = collect(explode(' ', trim($gym->name ?? '')))
                ->filter()
                ->map(fn ($word) => mb_substr($word, 0, 1))
                ->take(2)
                ->implode('');
            $gymInitials = $gymInitials !== '' ? mb_strtoupper($gymInitials) : 'GY';
            if (!empty($gym->logo_path)) {
                $gymLogoUrl = str_starts_with($gym->logo_path, 'http://') || str_starts_with($gym->logo_path, 'https://')
                    ? $gym->logo_path
                    : asset('storage/'.ltrim($gym->logo_path, '/'));
            }
        }
    @endphp

    <div id="theme-selector"
         class="space-y-6"
         data-current-theme="{{ $currentTheme }}"
         data-update-url="{{ route('settings.theme.update', [], false) }}"
         data-csrf="{{ csrf_token() }}">
        <x-card title="Theme Selector"
                subtitle="Personaliza IRON WILL con un look premium. El cambio es instantaneo y se guarda en tu cuenta.">
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
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Tema</p>
                                <h3 class="mt-1 text-sm font-black tracking-wide text-white">{{ $theme['name'] }}</h3>
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
                            <p>Peso maximo: 2MB</p>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('settings.gym-logo.update') }}"
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
                    <p class="ui-muted mt-1 text-sm">Actualiza nombre comercial, telefono y direccion principal.</p>

                    <form method="POST" action="{{ route('settings.gym-profile.update') }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre comercial</label>
                            <input type="text" name="name" value="{{ old('name', $gym->name) }}" class="ui-input" required>
                            @error('name')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Telefono</label>
                            <input type="text" name="phone" value="{{ old('phone', $gym->phone) }}" class="ui-input">
                            @error('phone')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Direccion</label>
                            <textarea name="address" rows="3" class="ui-input">{{ old('address', $gym->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="ui-button ui-button-primary">Guardar datos del gym</button>
                    </form>
                </section>
            </div>
        @else
            @if ($isSuperAdmin)
                <section class="ui-card">
                    <h3 class="ui-heading text-base">Configuracion SuperAdmin</h3>
                    <p class="ui-muted mt-1 text-sm">
                        Como SuperAdmin gestionas multiples gimnasios. El logo, telefono y direccion se administran por cada gym.
                    </p>
                    <a href="{{ route('superadmin.gyms.index') }}" class="ui-button ui-button-primary mt-4">
                        Ir a Gyms
                    </a>
                </section>
            @else
                <section class="ui-card">
                    <h3 class="ui-heading text-base">Configuracion del Gym</h3>
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
            const rawUpdateUrl = container.dataset.updateUrl || '/config/theme';
            const normalizedUrl = new URL(rawUpdateUrl, window.location.origin);
            const updateUrl = normalizedUrl.pathname + normalizedUrl.search;
            let currentTheme = container.dataset.currentTheme || 'iron_dark';
            let requestInFlight = false;

            const options = Array.from(container.querySelectorAll('[data-theme-option]'));
            const toastStack = document.getElementById('theme-toast-stack');

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
            };

            const pushToast = (message, type = 'success') => {
                if (!toastStack) return;

                const palette = type === 'success'
                    ? 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100'
                    : 'border-rose-400/45 bg-rose-500/15 text-rose-100';

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
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ theme }),
                });

                if (!response.ok) {
                    let reason = 'No fue posible guardar el tema.';
                    try {
                        const payload = await response.json();
                        reason = payload?.message || payload?.errors?.theme?.[0] || reason;
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

            applyTheme(currentTheme);
            setActiveCard(currentTheme);
        })();
    </script>
@endpush
