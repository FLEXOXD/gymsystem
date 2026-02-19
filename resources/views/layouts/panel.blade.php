@php
    use App\Models\Subscription;

    $pageTitle = trim($__env->yieldContent('title')) ?: 'Panel';
    $user = auth()->user();
    $activeTheme = $user?->theme ?? 'iron_dark';
    $darkThemes = ['iron_dark', 'power_red', 'energy_green', 'gold_elite'];
    $isDarkTheme = in_array($activeTheme, $darkThemes, true);
    $themeClass = $isDarkTheme ? 'dark theme-dark' : 'theme-light';
    $isSuperAdmin = $user && $user->gym_id === null;
    $gym = $user?->gym;
    $gymName = $isSuperAdmin ? 'SuperAdmin' : ($gym?->name ?? 'Gym');
    $gymLogo = $gym?->logo_path;
    if ($gymLogo && !str_starts_with($gymLogo, 'http://') && !str_starts_with($gymLogo, 'https://')) {
        $gymLogo = asset('storage/'.ltrim($gymLogo, '/'));
    }
    $gymInitials = collect(explode(' ', trim($gymName)))->filter()->map(fn ($word) => mb_substr($word, 0, 1))->take(2)->implode('');
    $gymInitials = $gymInitials !== '' ? mb_strtoupper($gymInitials) : 'GY';

    $gymSubscriptionStatus = null;
    if (!$isSuperAdmin && $user?->gym_id) {
        $gymSubscriptionStatus = Subscription::query()
            ->where('gym_id', (int) $user->gym_id)
            ->value('status');
    }

    $navItems = $isSuperAdmin
        ? [
            ['label' => 'Panel', 'route' => 'superadmin.dashboard', 'active' => 'superadmin.dashboard'],
            ['label' => 'Gimnasios', 'route' => 'superadmin.gyms.index', 'active' => 'superadmin.gyms.*|superadmin.subscriptions.*'],
            ['label' => 'Notificaciones', 'route' => 'superadmin.notifications.index', 'active' => 'superadmin.notifications.*'],
          ]
        : [
            ['label' => 'Recepcion', 'route' => 'reception.index', 'active' => 'reception.*'],
            ['label' => 'Clientes', 'route' => 'clients.index', 'active' => 'clients.*'],
            ['label' => 'Planes', 'route' => 'plans.index', 'active' => 'plans.*'],
            ['label' => 'Caja', 'route' => 'cash.index', 'active' => 'cash.*'],
            ['label' => 'Reportes', 'route' => 'reports.index', 'active' => 'reports.*'],
            ['label' => 'Configuracion', 'route' => 'settings.index', 'active' => 'settings.*'],
          ];

    $statusVariant = match ($gymSubscriptionStatus) {
        'active' => 'success',
        'grace' => 'warning',
        'suspended' => 'danger',
        default => 'muted',
    };
@endphp
<!DOCTYPE html>
<html lang="es" class="h-full antialiased {{ $themeClass }}" data-theme="{{ $activeTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} - {{ config('app.name', 'GymSystem') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="theme-body h-full ui-text">
<div class="min-h-screen lg:flex">
    <aside id="panel-sidebar" class="theme-sidebar hidden shrink-0 border-r transition-all lg:flex lg:w-64 lg:flex-col">
        <div class="theme-divider flex items-center gap-4 border-b px-4 py-4">
            <div class="theme-logo-badge flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl text-base font-black">
                @if ($gymLogo)
                    <img src="{{ $gymLogo }}" alt="Logo" class="h-full w-full object-contain" style="transform: scale(1.55); transform-origin: center;">
                @else
                    {{ $gymInitials }}
                @endif
            </div>
            <div class="sidebar-label">
                <p class="ui-muted text-xs font-bold uppercase tracking-widest">GymSystem</p>
                <p class="ui-heading text-base">{{ $gymName }}</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-4">
            @foreach ($navItems as $item)
                @php
                    $activePatterns = explode('|', $item['active']);
                    $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold transition {{ $isActive ? 'theme-nav-active' : 'theme-nav-link' }}">
                    <span class="theme-nav-dot inline-flex h-2.5 w-2.5 rounded-full {{ $isActive ? 'bg-white' : '' }}"></span>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="theme-divider border-t px-4 py-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ui-button ui-button-muted w-full">
                    <span class="sidebar-label">Cerrar sesion</span>
                    <span class="sidebar-icon hidden">Salir</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 pb-16 lg:pb-0">
        <header class="theme-header theme-divider sticky top-0 z-20 border-b backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 md:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <button id="sidebar-toggle" type="button"
                            class="hidden ui-button ui-button-ghost px-2.5 py-2 text-xs font-bold lg:inline-flex">
                        Menu
                    </button>
                    <div>
                        <p class="ui-muted text-xs font-bold uppercase tracking-widest">Panel operativo</p>
                        <h1 class="ui-heading text-lg md:text-xl">@yield('page-title', $pageTitle)</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if (!$isSuperAdmin)
                        <form method="GET" action="{{ route('clients.index') }}" class="hidden items-center gap-2 md:flex">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar cliente..."
                                   class="ui-input w-52">
                            <button type="submit" class="ui-button ui-button-primary px-3 py-2 text-xs font-bold">Buscar</button>
                        </form>
                    @endif

                    @if (!$isSuperAdmin && $gymSubscriptionStatus)
                        <x-badge :variant="$statusVariant">{{ $gymSubscriptionStatus }}</x-badge>
                    @endif

                    <a href="{{ route('settings.index') }}" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold">
                        Configuracion
                    </a>

                    <span class="theme-chip hidden rounded-full px-3 py-1 text-xs font-semibold md:inline-flex">
                        {{ $user?->name ?? 'Usuario' }}
                    </span>
                </div>
            </div>
        </header>

        <main class="panel-view mx-auto w-full max-w-7xl space-y-4 px-4 py-6 md:px-6 lg:px-8">
            @if (!empty($subscription_grace))
                <x-toast type="warning" :autohide="false">Su suscripcion ha vencido. Tiene {{ (int) ($subscription_grace_days ?? 3) }} dias de gracia para renovar.</x-toast>
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
    <div class="mx-auto grid max-w-xl grid-cols-5 gap-2">
        @foreach ($navItems as $item)
            @if ($loop->index < 5)
                @php
                    $activePatterns = explode('|', $item['active']);
                    $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide {{ $isActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link' }}">
                    {{ $item['label'] }}
                </a>
            @endif
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
            sidebar.querySelectorAll('.sidebar-icon').forEach(function (element) {
                element.classList.toggle('hidden', !collapsed);
            });
            localStorage.setItem('panel.sidebar_collapsed', collapsed ? '1' : '0');
        });

        if (sidebar && localStorage.getItem('panel.sidebar_collapsed') === '1') {
            sidebarToggle?.click();
        }

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
    })();
</script>
@stack('scripts')
</body>
</html>
