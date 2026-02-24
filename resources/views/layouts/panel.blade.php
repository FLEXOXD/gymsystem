@php
    use App\Models\Subscription;

    $resolvePublicMediaUrl = function (?string $path): ?string {
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

    $pageTitle = trim($__env->yieldContent('title')) ?: __('ui.panel');
    $user = auth()->user();
    $activeTheme = $user?->theme ?? 'iron_dark';
    $darkThemes = ['iron_dark', 'power_red', 'energy_green', 'gold_elite'];
    $isDarkTheme = in_array($activeTheme, $darkThemes, true);
    $themeClass = $isDarkTheme ? 'dark theme-dark' : 'theme-light';
    $isSuperAdmin = $user && $user->gym_id === null;
    $gym = $user?->gym;
    $gymSlug = trim((string) ($gym?->slug ?? ''));
    $gymRouteParams = $gymSlug !== '' ? ['contextGym' => $gymSlug] : [];
    $gymName = $isSuperAdmin ? __('ui.superadmin') : ($gym?->name ?? 'Gym');
    $gymLogo = $resolvePublicMediaUrl($gym?->logo_path);
    $gymInitials = collect(explode(' ', trim($gymName)))->filter()->map(fn ($word) => mb_substr($word, 0, 1))->take(2)->implode('');
    $gymInitials = $gymInitials !== '' ? mb_strtoupper($gymInitials) : 'GY';
    $userName = trim((string) ($user?->name ?? __('ui.guest')));
    $userEmail = trim((string) ($user?->email ?? ''));
    $userInitial = mb_strtoupper(mb_substr($userName !== '' ? $userName : 'U', 0, 1));
    $userPhotoPath = trim((string) ($user?->profile_photo_path ?? ''));
    $userPhotoUrl = $resolvePublicMediaUrl($userPhotoPath);
    if (! $isSuperAdmin && $gymSlug !== '' && \Illuminate\Support\Facades\Route::has('gym.settings.index')) {
        $settingsUrl = route('gym.settings.index', $gymRouteParams);
    } else {
        $settingsUrl = \Illuminate\Support\Facades\Route::has('settings.index') ? route('settings.index') : '#';
    }
    if (! $isSuperAdmin && $gymSlug !== '' && \Illuminate\Support\Facades\Route::has('gym.profile.index')) {
        $profileUrl = route('gym.profile.index', $gymRouteParams);
    } else {
        $profileUrl = \Illuminate\Support\Facades\Route::has('profile.index') ? route('profile.index') : '#';
    }
    if (! $isSuperAdmin && $gymSlug !== '' && \Illuminate\Support\Facades\Route::has('gym.contact.index')) {
        $contactUrl = route('gym.contact.index', $gymRouteParams);
    } else {
        $contactUrl = \Illuminate\Support\Facades\Route::has('contact.index') ? route('contact.index') : 'mailto:soporte@gymsystem.app?subject=Soporte%20GymSystem';
    }
    $brandHomeUrl = $isSuperAdmin
        ? route('superadmin.dashboard')
        : ($gymSlug !== '' ? route('panel.index', $gymRouteParams) : route('panel.legacy'));

    $gymSubscriptionStatus = null;
    if (!$isSuperAdmin && $user?->gym_id) {
        $gymSubscriptionStatus = Subscription::query()
            ->where('gym_id', (int) $user->gym_id)
            ->value('status');
    }

    $navItems = $isSuperAdmin
        ? [
            ['label' => __('ui.nav.panel'), 'route' => 'superadmin.dashboard', 'params' => [], 'active' => 'superadmin.dashboard', 'icon' => 'panel'],
            ['label' => __('ui.nav.gyms'), 'route' => 'superadmin.gyms.index', 'params' => [], 'active' => 'superadmin.gyms.*|superadmin.subscriptions.*', 'icon' => 'gyms'],
            ['label' => 'Crear nuevo gimnasio', 'route' => 'superadmin.gym.index', 'params' => [], 'active' => 'superadmin.gym.*', 'icon' => 'gym'],
            ['label' => 'Planes', 'route' => 'superadmin.plan-templates.index', 'params' => [], 'active' => 'superadmin.plan-templates.*', 'icon' => 'plans'],
            ['label' => __('ui.nav.notifications'), 'route' => 'superadmin.notifications.index', 'params' => [], 'active' => 'superadmin.notifications.*', 'icon' => 'notifications'],
            ['label' => __('ui.nav.suggestions'), 'route' => 'superadmin.suggestions.index', 'params' => [], 'active' => 'superadmin.suggestions.*', 'icon' => 'suggestions'],
          ]
        : [
            ['label' => __('ui.nav.panel'), 'route' => 'panel.index', 'params' => $gymRouteParams, 'active' => 'panel.*', 'icon' => 'panel'],
            ['label' => __('ui.nav.reception'), 'route' => 'reception.index', 'params' => $gymRouteParams, 'active' => 'reception.*', 'icon' => 'reception'],
            ['label' => __('ui.nav.clients'), 'route' => 'clients.index', 'params' => $gymRouteParams, 'active' => 'clients.*', 'icon' => 'clients'],
            ['label' => __('ui.nav.plans'), 'route' => 'plans.index', 'params' => $gymRouteParams, 'active' => 'plans.*', 'icon' => 'plans'],
            ['label' => __('ui.nav.cash'), 'route' => 'cash.index', 'params' => $gymRouteParams, 'active' => 'cash.*', 'icon' => 'cash'],
            ['label' => __('ui.nav.reports'), 'route' => 'reports.index', 'params' => $gymRouteParams, 'active' => 'reports.*', 'icon' => 'reports'],
          ];

    $statusVariant = match ($gymSubscriptionStatus) {
        'active' => 'success',
        'grace' => 'warning',
        'suspended' => 'danger',
        default => 'muted',
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased {{ $themeClass }}" data-theme="{{ $activeTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} - {{ config('app.name', 'GymSystem') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .smart-list-wrap {
            max-height: min(68vh, 720px);
            overflow: auto;
            border-radius: 0.75rem;
        }
        .smart-list-toolbar {
            display: flex;
            gap: 0.75rem;
            align-items: end;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }
        .smart-list-toolbar label {
            min-width: 240px;
            flex: 1 1 280px;
        }
        .smart-list-counter {
            font-size: 0.875rem;
            color: rgb(71 85 105);
        }
        .theme-dark .smart-list-counter {
            color: rgb(148 163 184);
        }
        .smart-list-wrap .ui-table thead th {
            position: sticky;
            top: 0;
            z-index: 6;
            background: rgb(241 245 249 / 0.95);
            backdrop-filter: blur(3px);
        }
        .theme-dark .smart-list-wrap .ui-table thead th {
            background: rgb(30 41 59 / 0.95);
        }
        .theme-dark .ui-table .text-emerald-700 {
            color: rgb(74 222 128) !important;
        }
        .theme-dark .ui-table .text-rose-700 {
            color: rgb(251 113 133) !important;
        }
        @media (max-width: 768px) {
            .smart-list-wrap {
                max-height: min(62vh, 560px);
            }
            .smart-list-toolbar {
                align-items: stretch;
            }
            .smart-list-toolbar .ui-input {
                width: 100%;
            }
        }
        @media (max-width: 640px) {
            .smart-list-toolbar label {
                min-width: 0;
                width: 100%;
                flex-basis: 100%;
            }
            #user-menu-dropdown {
                width: min(92vw, 20rem);
            }
        }
        #panel-sidebar {
            position: relative;
            z-index: 40;
        }
        #brand-home-link,
        #brand-home-link * {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
    </style>
    @stack('styles')
</head>
<body class="theme-body h-full ui-text">
<div class="min-h-screen overflow-x-clip lg:flex">
    <aside id="panel-sidebar" class="theme-sidebar relative z-40 hidden shrink-0 border-r transition-all lg:flex lg:w-64 lg:flex-col">
        <a id="brand-home-link"
           href="{{ $brandHomeUrl }}"
           data-home-url="{{ $brandHomeUrl }}"
           class="theme-divider relative z-50 flex cursor-pointer items-center gap-4 border-b px-4 py-4 transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60"
           style="pointer-events:auto;">
            @php
                $hasBrandImage = ($isSuperAdmin && !empty($userPhotoUrl)) || (!$isSuperAdmin && !empty($gymLogo));
            @endphp
            <div id="brand-logo-badge" @class([
                'flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl text-base font-black',
                'theme-logo-badge' => ! $hasBrandImage,
                'bg-transparent shadow-none' => $hasBrandImage,
            ])>
                @if ($isSuperAdmin && $userPhotoUrl)
                    <img src="{{ $userPhotoUrl }}" alt="{{ $userName }}" class="h-full w-full object-cover object-center">
                @elseif ($gymLogo)
                    <img src="{{ $gymLogo }}"
                         alt="Logo"
                         class="h-full w-full object-contain object-center"
                         data-fallback-src="{{ (!$isSuperAdmin && $userPhotoUrl) ? $userPhotoUrl : '' }}"
                         onerror="var fb=this.dataset.fallbackSrc||''; if(fb!=='' && this.src!==fb){ this.src=fb; this.classList.remove('object-contain'); this.classList.add('object-cover','object-center'); return; } this.style.display='none'; var fallback=this.parentNode.querySelector('[data-logo-fallback]'); if (fallback) { fallback.classList.remove('hidden'); }">
                    <span data-logo-fallback class="hidden text-lg font-black uppercase">{{ $gymInitials }}</span>
                @else
                    <span class="text-lg font-black uppercase">{{ $gymInitials }}</span>
                @endif
            </div>
            <div class="sidebar-label">
                <p class="ui-muted text-xs font-bold uppercase tracking-widest">GymSystem</p>
                <p class="ui-heading text-base">{{ $gymName }}</p>
            </div>
        </a>

        <nav class="space-y-1 px-3 py-4">
            @foreach ($navItems as $item)
                @php
                    $activePatterns = explode('|', $item['active']);
                    $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                @endphp
                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold transition {{ $isActive ? 'theme-nav-active' : 'theme-nav-link' }}">
                    <span class="theme-nav-dot inline-flex h-2.5 w-2.5 rounded-full {{ $isActive ? 'bg-white' : '' }}"></span>
                    <span class="sidebar-icon inline-flex h-4 w-4 items-center justify-center">
                        @switch($item['icon'] ?? '')
                            @case('panel')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 4h8v8H3V4Zm10 0h8v5h-8V4ZM3 14h8v6H3v-6Zm10-3h8v9h-8v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('reception')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 11h12M12 5v12m7-6a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('clients')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M16 18v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1m16 0v-1a4 4 0 0 0-3-3.87M13 6.13a4 4 0 1 1 0 7.75M9.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('plans')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('cash')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M3 10h18M8 14h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('reports')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 19V9m7 10V5m7 14v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('gyms')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 20V8l8-4 8 4v12M9 20v-5h6v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('gym')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="9" cy="8" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4.8 18.2v-1a4.2 4.2 0 0 1 8.4 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M16 8v6M13 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('notifications')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 10a6 6 0 1 1 12 0v4l2 2H4l2-2v-4Zm4 8a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('suggestions')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M6 5h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-6l-4 3v-3H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @default
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                        @endswitch
                    </span>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

    </aside>

    <div class="flex-1 pb-16 lg:pb-0">
        <header class="theme-header theme-divider sticky top-0 z-20 border-b backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 md:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-2">
                    <button id="sidebar-toggle" type="button"
                            class="hidden ui-button ui-button-ghost px-2.5 py-2 text-xs font-bold lg:inline-flex">
                        {{ __('ui.menu') }}
                    </button>
                    <div>
                        <p class="ui-muted text-xs font-bold uppercase tracking-widest">{{ __('ui.panel_operativo') }}</p>
                        <h1 class="ui-heading truncate text-lg md:text-xl">@yield('page-title', $pageTitle)</h1>
                    </div>
                </div>

                <div class="flex w-full items-center justify-end gap-2 sm:w-auto">
                    @if (!$isSuperAdmin)
                        <form method="GET" action="{{ route('clients.index', $gymRouteParams) }}" class="hidden items-center gap-2 md:flex">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.search_client') }}"
                                   class="ui-input w-52">
                            <button type="submit" class="ui-button ui-button-primary px-3 py-2 text-xs font-bold">{{ __('ui.search') }}</button>
                        </form>
                    @endif

                    @if (!$isSuperAdmin && $gymSubscriptionStatus)
                        <x-badge :variant="$statusVariant">{{ $gymSubscriptionStatus }}</x-badge>
                    @endif

                    <div id="user-menu-root" class="relative">
                        <button id="user-menu-button" type="button" class="ui-button ui-button-ghost flex items-center gap-2 px-2 py-1.5" aria-haspopup="true" aria-expanded="false" aria-controls="user-menu-dropdown">
                            @if ($userPhotoUrl)
                                <span class="inline-flex h-9 w-9 items-center justify-center overflow-hidden rounded-full">
                                    <img id="user-avatar-image" src="{{ $userPhotoUrl }}" alt="{{ $userName }}" class="h-full w-full object-cover object-center">
                                </span>
                            @else
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sm font-black text-sky-800 dark:bg-sky-900/45 dark:text-sky-200">{{ $userInitial }}</span>
                            @endif
                            <span class="hidden text-sm font-semibold text-slate-800 dark:text-slate-100 md:inline">{{ $userName }}</span>
                            <svg class="h-4 w-4 text-slate-600 dark:text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div id="user-menu-dropdown" class="absolute right-0 z-40 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                            <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-700">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $userName }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">{{ $userEmail !== '' ? $userEmail : __('ui.no_email') }}</p>
                            </div>

                            <div class="p-2">
                                <a href="{{ $profileUrl }}" class="flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('ui.view_profile') }}</a>
                                <a href="{{ $settingsUrl }}" class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('ui.settings') }}</a>
                                <a href="{{ $contactUrl }}" class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('ui.contact') }}</a>

                                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30">{{ __('ui.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="panel-view mx-auto w-full max-w-7xl space-y-4 overflow-x-clip px-4 py-6 md:px-6 lg:px-8">
            @if (!empty($subscription_grace))
                <x-toast type="warning" :autohide="false">{{ __('ui.toast.grace_subscription', ['days' => (int) ($subscription_grace_days ?? 3)]) }}</x-toast>
            @endif

            @if (session('status'))
                <x-toast type="success">{{ session('status') }}</x-toast>
            @endif
            @if (session('error'))
                <x-toast type="danger">{{ session('error') }}</x-toast>
            @endif

            @if ($errors->any())
                <x-toast type="danger">{{ $errors->first() }}</x-toast>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<nav class="theme-mobile-nav fixed inset-x-0 bottom-0 z-30 border-t p-2 backdrop-blur lg:hidden">
    <div class="mx-auto flex max-w-full gap-2 overflow-x-auto px-1 pb-1">
        @foreach ($navItems as $item)
            @php
                $activePatterns = explode('|', $item['active']);
                $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
            @endphp
            <a href="{{ route($item['route'], $item['params'] ?? []) }}"
               class="min-w-[84px] shrink-0 rounded-lg px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide {{ $isActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>

<script>
    (function () {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('panel-sidebar');

        sidebarToggle?.addEventListener('click', function () {
            if (!sidebar) return;
            const collapsed = sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('lg:w-64', !collapsed);
            sidebar.classList.toggle('lg:w-20', collapsed);
            sidebar.querySelectorAll('.sidebar-label').forEach(function (element) {
                element.classList.toggle('hidden', collapsed);
            });
            localStorage.setItem('panel.sidebar_collapsed', collapsed ? '1' : '0');
        });

        if (sidebar && localStorage.getItem('panel.sidebar_collapsed') === '1') {
            sidebarToggle?.click();
        }

        const userMenuRoot = document.getElementById('user-menu-root');
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');

        function closeUserMenu() {
            if (!userMenuDropdown || !userMenuButton) return;
            userMenuDropdown.classList.add('hidden');
            userMenuButton.setAttribute('aria-expanded', 'false');
        }

        function openUserMenu() {
            if (!userMenuDropdown || !userMenuButton) return;
            userMenuDropdown.classList.remove('hidden');
            userMenuButton.setAttribute('aria-expanded', 'true');
        }

        userMenuButton?.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (userMenuDropdown?.classList.contains('hidden')) {
                openUserMenu();
            } else {
                closeUserMenu();
            }
        });

        document.addEventListener('click', function (event) {
            if (!userMenuRoot || !userMenuDropdown) return;
            const target = event.target;
            if (target instanceof Node && userMenuRoot.contains(target)) return;
            closeUserMenu();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeUserMenu();
            }
        });

        document.querySelectorAll('[data-toast]').forEach(function (toast) {
            const shouldHide = toast.getAttribute('data-autohide') === '1';
            if (!shouldHide) return;

            const delay = Number(toast.getAttribute('data-delay') || 4200);
            setTimeout(function () {
                toast.classList.add('opacity-0', 'translate-y-1', 'transition');
                setTimeout(function () {
                    toast.remove();
                }, 250);
            }, delay);
        });

        function normalizeText(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function getBodyRows(table) {
            const rows = table.querySelectorAll('tbody tr');
            return Array.from(rows).filter(function (row) {
                return row.querySelector('td') !== null;
            });
        }

        function enhanceSmartList(table, index) {
            if (table.hasAttribute('data-smart-list-manual')) return;
            if (table.dataset.smartListReady === '1') return;

            const rows = getBodyRows(table);
            if (rows.length <= 10) return;

            const wrapper = table.closest('.overflow-x-auto') || table.parentElement;
            if (!wrapper) return;

            table.dataset.smartListReady = '1';
            wrapper.classList.add('smart-list-wrap');

            const toolbar = document.createElement('div');
            toolbar.className = 'smart-list-toolbar';
            const smartListSearchLabel = @js(__('ui.smart_list.search_label'));
            const smartListSearchPlaceholder = @js(__('ui.smart_list.search_placeholder'));
            const smartListShowing = @js(__('ui.smart_list.showing'));
            const smartListOf = @js(__('ui.smart_list.of'));
            const smartListNoResults = @js(__('ui.smart_list.no_results'));
            toolbar.innerHTML =
                '<label class="space-y-1 text-sm font-semibold ui-muted">' +
                    '<span>' + smartListSearchLabel + '</span>' +
                    '<input type="text" class="ui-input js-smart-list-search" placeholder="' + smartListSearchPlaceholder + '" autocomplete="off">' +
                '</label>' +
                '<p class="smart-list-counter">' + smartListShowing + ' <strong class="js-smart-list-visible">' + rows.length + '</strong> ' + smartListOf + ' <strong>' + rows.length + '</strong></p>';

            wrapper.parentNode.insertBefore(toolbar, wrapper);

            const searchInput = toolbar.querySelector('.js-smart-list-search');
            const visibleEl = toolbar.querySelector('.js-smart-list-visible');

            const emptyRow = document.createElement('tr');
            emptyRow.className = 'hidden';
            emptyRow.innerHTML = '<td colspan="' + (table.querySelectorAll('thead th').length || 1) + '" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">' + smartListNoResults + '</td>';
            table.querySelector('tbody')?.appendChild(emptyRow);

            rows.forEach(function (row) {
                row.dataset.smartSearch = normalizeText(row.textContent || '');
                if (index % 2 === 0) {
                    row.classList.add('odd:bg-slate-50/45', 'dark:odd:bg-slate-900/30');
                }
                row.classList.add('hover:bg-cyan-50/70', 'dark:hover:bg-cyan-500/10');
            });

            function applyFilter() {
                const term = normalizeText(searchInput?.value || '');
                let visible = 0;

                rows.forEach(function (row) {
                    const matches = term === '' || (row.dataset.smartSearch || '').includes(term);
                    row.classList.toggle('hidden', !matches);
                    if (matches) visible += 1;
                });

                if (visibleEl) visibleEl.textContent = String(visible);
                emptyRow.classList.toggle('hidden', visible !== 0);
            }

            searchInput?.addEventListener('input', applyFilter);
            applyFilter();
        }

        document.querySelectorAll('table.ui-table').forEach(function (table, index) {
            enhanceSmartList(table, index);
        });

        const brandHomeLink = document.getElementById('brand-home-link');
        brandHomeLink?.addEventListener('click', function (event) {
            const targetUrl = brandHomeLink.getAttribute('data-home-url');
            if (!targetUrl) return;
            event.preventDefault();
            window.location.href = targetUrl;
        });

        // SuperAdmin-only visual sync:
        // if top-right avatar exists, mirror it into the sidebar brand badge.
        const isSuperAdminViewer = @json($isSuperAdmin);
        if (isSuperAdminViewer) {
            const brandLogoBadge = document.getElementById('brand-logo-badge');
            const topAvatarImage = document.getElementById('user-avatar-image') || document.querySelector('#user-menu-button img');
            const topAvatarSrc = topAvatarImage?.getAttribute('src') || '';
            if (brandLogoBadge && topAvatarSrc !== '') {
                brandLogoBadge.innerHTML = '';
                const img = document.createElement('img');
                img.src = topAvatarSrc;
                img.alt = topAvatarImage?.getAttribute('alt') || 'SuperAdmin';
                img.className = 'h-full w-full object-cover';
                brandLogoBadge.appendChild(img);
            }
        }
    })();
</script>
@stack('scripts')
</body>
</html>
