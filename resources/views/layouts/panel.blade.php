@php
    use App\Models\Gym;
    use App\Models\GymBranchLink;
    use App\Models\LandingContactMessage;
    use App\Models\Subscription;
    use App\Services\LegalAcceptanceEligibilityService;
    use App\Services\PlanAccessService;
    use App\Support\LegalTerms;

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
    $isCashierMode = ! $isSuperAdmin && (bool) ($user?->isCashier());
    $gym = $user?->gym;
    $hubGymId = (int) ($user?->gym_id ?? 0);
    $hubGymSlug = trim((string) ($gym?->slug ?? ''));
    $contextGymSlug = trim((string) (request()->route('contextGym') ?? ''));
    $isGlobalScope = ! $isSuperAdmin && (bool) request()->attributes->get(
        'active_gym_is_global',
        strtolower(trim((string) request()->query('scope', ''))) === 'global'
    );
    if ($isCashierMode) {
        $isGlobalScope = false;
    }
    $isStandalonePwaMode = strtolower(trim((string) request()->query('pwa_mode', ''))) === 'standalone';
    $activeGym = null;
    if (! $isSuperAdmin) {
        $activeGym = request()->attributes->get('active_gym');
        if (! $activeGym instanceof Gym && $contextGymSlug !== '') {
            $activeGym = Gym::query()
                ->withoutDemoSessions()
                ->select([
                    'id',
                    'name',
                    'slug',
                    'address',
                    'address_state',
                    'address_city',
                    'address_line',
                    'logo_path',
                ])
                ->whereRaw('LOWER(slug) = ?', [mb_strtolower($contextGymSlug)])
                ->first();
        }
        if (! $activeGym instanceof Gym) {
            $activeGym = $gym;
        }
    }

    $activeGymId = (int) ($isSuperAdmin ? 0 : ($activeGym?->id ?? $hubGymId));
    $activeGymSlug = trim((string) ($isSuperAdmin ? '' : ($activeGym?->slug ?? ($contextGymSlug !== '' ? $contextGymSlug : $hubGymSlug))));
    $gymRouteParams = $activeGymSlug !== ''
        ? array_filter([
            'contextGym' => $activeGymSlug,
            'pwa_mode' => $isStandalonePwaMode ? 'standalone' : null,
        ], static fn ($value): bool => $value !== null && $value !== '')
        : [];
    $gymName = $isSuperAdmin ? __('ui.superadmin') : ($activeGym?->name ?? $gym?->name ?? 'Gym');
    $gymLogo = $resolvePublicMediaUrl($activeGym?->logo_path ?? $gym?->logo_path);
    $gymInitials = collect(explode(' ', trim($gymName)))->filter()->map(fn ($word) => mb_substr($word, 0, 1))->take(2)->implode('');
    $gymInitials = $gymInitials !== '' ? mb_strtoupper($gymInitials) : 'GY';
    $userName = trim((string) ($user?->name ?? __('ui.guest')));
    $userEmail = trim((string) ($user?->email ?? ''));
    $userInitial = mb_strtoupper(mb_substr($userName !== '' ? $userName : 'U', 0, 1));
    $userPhotoPath = trim((string) ($user?->profile_photo_path ?? ''));
    $userPhotoUrl = $resolvePublicMediaUrl($userPhotoPath);
    if (! $isSuperAdmin && $activeGymSlug !== '' && \Illuminate\Support\Facades\Route::has('gym.settings.index')) {
        $settingsUrl = route('gym.settings.index', $gymRouteParams);
    } else {
        $settingsUrl = \Illuminate\Support\Facades\Route::has('settings.index') ? route('settings.index') : '#';
    }
    if (! $isSuperAdmin && $activeGymSlug !== '' && \Illuminate\Support\Facades\Route::has('gym.profile.index')) {
        $profileUrl = route('gym.profile.index', $gymRouteParams);
    } else {
        $profileUrl = \Illuminate\Support\Facades\Route::has('profile.index') ? route('profile.index') : '#';
    }
    if (! $isSuperAdmin && $activeGymSlug !== '' && \Illuminate\Support\Facades\Route::has('gym.contact.index')) {
        $contactUrl = route('gym.contact.index', $gymRouteParams);
    } else {
        $contactUrl = \Illuminate\Support\Facades\Route::has('contact.index') ? route('contact.index') : 'mailto:soporte@gymsystem.app?subject=Soporte%20GymSystem';
    }
    $brandHomeUrl = $isSuperAdmin
        ? route('superadmin.dashboard')
        : ($activeGymSlug !== '' ? route('panel.index', $gymRouteParams) : route('panel.legacy'));

    $gymSubscriptionStatus = null;
    if (!$isSuperAdmin && $activeGymId > 0) {
        $gymSubscriptionStatus = Subscription::query()
            ->where('gym_id', $activeGymId)
            ->value('status');
    }
    $planAccessService = app(PlanAccessService::class);
    $canUseMultiBranchFromContext = (bool) request()->attributes->get('gym_context_can_use_multibranch', false);
    $canUseMultiBranch = $isSuperAdmin
        || (
            ! $isCashierMode
            && (
                $canUseMultiBranchFromContext
                || ($hubGymId > 0 && $planAccessService->canForGym($hubGymId, 'multi_branch') && ! GymBranchLink::query()->where('branch_gym_id', $hubGymId)->exists())
            )
        );
    if ($isGlobalScope && $canUseMultiBranch && $hubGymSlug !== '') {
        $gymRouteParams = [
            'contextGym' => $hubGymSlug,
            'scope' => 'global',
        ] + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : []);
        $activeGymSlug = $hubGymSlug;
    }
    $canViewReports = $isSuperAdmin || ($activeGymId > 0 ? $planAccessService->canForGym($activeGymId, 'reports_base') : false);
    $canViewBranches = $canUseMultiBranch;
    $canInstallPwa = ! $isSuperAdmin && $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'pwa_install');
    $pushVapidPublicKey = trim((string) config('services.webpush.vapid.public_key', ''));
    $pushWebEnabled = (bool) config('services.webpush.enabled', false);
    $pwaUpgradeMessage = 'Sube de plan a Profesional, Premium o Sucursales para usar la app instalable (PWA).';
    $suppressGlobalValidationToast = request()->routeIs('clients.index') && (bool) old('_open_create_modal', false);

    $branchesRouteParams = $hubGymSlug !== ''
        ? ['contextGym' => $hubGymSlug]
            + ($isGlobalScope ? ['scope' => 'global'] : [])
            + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : [])
        : $gymRouteParams;
    $switchableRouteNames = ['panel.index', 'reception.index', 'clients.index', 'plans.index', 'cash.index', 'reports.index', 'branches.index', 'staff.index'];
    $currentRouteName = (string) (\Illuminate\Support\Facades\Route::currentRouteName() ?? '');
    $baseSwitcherRoute = in_array($currentRouteName, $switchableRouteNames, true) ? $currentRouteName : 'panel.index';

    $buildGymAddress = static function (?Gym $item): string {
        if (! $item) {
            return '-';
        }

        $address = trim((string) ($item->address ?? ''));
        if ($address !== '') {
            return $address;
        }

        $parts = collect([
            trim((string) ($item->address_line ?? '')),
            trim((string) ($item->address_city ?? '')),
            trim((string) ($item->address_state ?? '')),
        ])->filter()->values();

        return $parts->isNotEmpty() ? $parts->implode(', ') : '-';
    };

    $branchContextOptions = collect();
    if (! $isSuperAdmin && $canUseMultiBranch && $hubGymId > 0 && $hubGymSlug !== '') {
        $linkedBranches = GymBranchLink::query()
            ->where('hub_gym_id', $hubGymId)
            ->with(['branchGym' => static function ($query): void {
                $query->withoutDemoSessions()
                    ->select(['id', 'name', 'slug', 'address', 'address_state', 'address_city', 'address_line']);
            }])
            ->orderBy('id')
            ->get()
            ->pluck('branchGym')
            ->filter(fn ($branchGym) => $branchGym instanceof Gym)
            ->values();

        foreach ($linkedBranches as $branchGym) {
            $branchContextOptions->push([
                'gym_id' => (int) $branchGym->id,
                'slug' => (string) $branchGym->slug,
                'name' => (string) $branchGym->name,
                'address' => $buildGymAddress($branchGym),
                'url' => route($baseSwitcherRoute, [
                    'contextGym' => (string) $branchGym->slug,
                ] + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : [])),
            ]);
        }

        $branchContextOptions = $branchContextOptions
            ->unique('slug')
            ->values();
    }

    $showBranchContextSwitcher = ! $isSuperAdmin
        && $canUseMultiBranch
        && $hubGymId > 0
        && $hubGymSlug !== ''
        && $branchContextOptions->count() > 0
        && ! request()->routeIs('branches.*');
    $activeBranchContextSlug = $activeGymSlug !== '' ? $activeGymSlug : $hubGymSlug;
    $activeBranchContext = $branchContextOptions
        ->firstWhere('slug', $activeBranchContextSlug)
        ?? $branchContextOptions->first();
    $isAdminGlobalContext = ! $isSuperAdmin && $isGlobalScope;
    $activeBranchContextTitle = $isAdminGlobalContext
        ? 'Admin global'
        : ((int) $activeGymId === $hubGymId ? 'Sede principal' : (string) ($activeBranchContext['name'] ?? 'Sucursal'));
    $activeBranchContextAddress = $isAdminGlobalContext
        ? 'Consolidado de todas las sucursales (solo lectura)'
        : ((int) $activeGymId === $hubGymId ? $buildGymAddress($gym) : (string) ($activeBranchContext['address'] ?? '-'));
    $globalContextUrl = $hubGymSlug !== '' && \Illuminate\Support\Facades\Route::has($baseSwitcherRoute)
        ? route($baseSwitcherRoute, [
            'contextGym' => $hubGymSlug,
            'scope' => 'global',
        ] + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : []))
        : '#';

    $gymNavItems = $isCashierMode
        ? [
            ['label' => __('ui.nav.panel'), 'route' => 'panel.index', 'params' => $gymRouteParams, 'active' => 'panel.*', 'icon' => 'panel'],
            ['label' => __('ui.nav.reception'), 'route' => 'reception.index', 'params' => $gymRouteParams, 'active' => 'reception.*', 'icon' => 'reception'],
            ['label' => __('ui.nav.clients'), 'route' => 'clients.index', 'params' => $gymRouteParams, 'active' => 'clients.*', 'icon' => 'clients'],
            ['label' => 'Cobros', 'route' => 'cash.index', 'params' => $gymRouteParams, 'active' => 'cash.*', 'icon' => 'cash'],
        ]
        : [
            ['label' => __('ui.nav.panel'), 'route' => 'panel.index', 'params' => $gymRouteParams, 'active' => 'panel.*', 'icon' => 'panel'],
            ['label' => __('ui.nav.reception'), 'route' => 'reception.index', 'params' => $gymRouteParams, 'active' => 'reception.*', 'icon' => 'reception'],
            ['label' => __('ui.nav.clients'), 'route' => 'clients.index', 'params' => $gymRouteParams, 'active' => 'clients.*', 'icon' => 'clients'],
            ['label' => __('ui.nav.plans'), 'route' => 'plans.index', 'params' => $gymRouteParams, 'active' => 'plans.*', 'icon' => 'plans'],
            ['label' => __('ui.nav.cash'), 'route' => 'cash.index', 'params' => $gymRouteParams, 'active' => 'cash.*', 'icon' => 'cash'],
        ];
    if (! $isCashierMode && \Illuminate\Support\Facades\Route::has('staff.index')) {
        $gymNavItems[] = ['label' => 'Cajeros', 'route' => 'staff.index', 'params' => $gymRouteParams, 'active' => 'staff.*', 'icon' => 'staff'];
    }
    if (! $isCashierMode && $canViewBranches && \Illuminate\Support\Facades\Route::has('branches.index')) {
        $gymNavItems[] = ['label' => 'Sucursales', 'route' => 'branches.index', 'params' => $branchesRouteParams, 'active' => 'branches.*', 'icon' => 'branches'];
    }
    if (! $isCashierMode && $canViewReports) {
        $gymNavItems[] = ['label' => __('ui.nav.reports'), 'route' => 'reports.index', 'params' => $gymRouteParams, 'active' => 'reports.*', 'icon' => 'reports'];
    }

    $navItems = $isSuperAdmin
        ? [
            ['label' => __('ui.nav.panel'), 'route' => 'superadmin.dashboard', 'params' => [], 'active' => 'superadmin.dashboard', 'icon' => 'panel'],
            ['label' => __('ui.nav.gyms'), 'route' => 'superadmin.gyms.index', 'params' => [], 'active' => 'superadmin.gyms.*|superadmin.subscriptions.*', 'icon' => 'gyms'],
            ['label' => 'Sucursales globales', 'route' => 'superadmin.branches.index', 'params' => [], 'active' => 'superadmin.branches.*', 'icon' => 'branches'],
            ['label' => 'Crear nuevo gimnasio', 'route' => 'superadmin.gym.index', 'params' => [], 'active' => 'superadmin.gym.*', 'icon' => 'gym'],
            ['label' => 'Planes', 'route' => 'superadmin.plan-templates.index', 'params' => [], 'active' => 'superadmin.plan-templates.*', 'icon' => 'plans'],
            ['label' => 'Mensajes web', 'route' => 'superadmin.inbox.index', 'params' => [], 'active' => 'superadmin.inbox.*', 'icon' => 'inbox'],
            ['label' => __('ui.nav.notifications'), 'route' => 'superadmin.notifications.index', 'params' => [], 'active' => 'superadmin.notifications.*', 'icon' => 'notifications'],
            ['label' => __('ui.nav.suggestions'), 'route' => 'superadmin.suggestions.index', 'params' => [], 'active' => 'superadmin.suggestions.*', 'icon' => 'suggestions'],
            ['label' => 'Aceptaciones legales', 'route' => 'superadmin.legal-acceptances.index', 'params' => [], 'active' => 'superadmin.legal-acceptances.*', 'icon' => 'legal_acceptances'],
            ['label' => 'Administrar página web', 'route' => 'superadmin.web-page.edit', 'params' => [], 'active' => 'superadmin.web-page.*', 'icon' => 'web'],
          ]
        : $gymNavItems;

    $statusVariant = match ($gymSubscriptionStatus) {
        'active' => 'success',
        'grace' => 'warning',
        'suspended' => 'danger',
        default => 'muted',
    };
    $inboxUrl = $isSuperAdmin && \Illuminate\Support\Facades\Route::has('superadmin.inbox.index')
        ? route('superadmin.inbox.index')
        : '#';
    $headerContactUnread = 0;
    $headerContactItems = collect();
    if ($isSuperAdmin && \Illuminate\Support\Facades\Schema::hasTable('landing_contact_messages')) {
        try {
            $headerContactUnread = LandingContactMessage::query()->withinBellWindow()->whereNull('read_at')->count();
            $headerContactItems = LandingContactMessage::query()
                ->withinBellWindow()
                ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(['id', 'first_name', 'last_name', 'email', 'read_at', 'created_at']);
        } catch (\Throwable $exception) {
            $headerContactUnread = 0;
            $headerContactItems = collect();
        }
    }
    $isDemoMode = (bool) ($demo_mode ?? false);
    $demoSessionToken = (string) ($demo_session_token ?? '');
    $demoExpiresAt = $demo_expires_at ?? null;
    $demoExpiresAtIso = trim((string) ($demo_expires_at_iso ?? ''));
    $demoServerNowIso = trim((string) ($demo_server_now_iso ?? ''));
    $demoGuideSteps = is_array($demo_guide_steps ?? null) ? array_values($demo_guide_steps) : [];
    $demoExpiresLabel = $demoExpiresAt ? $demoExpiresAt->timezone(config('app.timezone'))->format('d/m/Y H:i') : null;
    $legalCurrentVersion = LegalTerms::VERSION;
    $acceptedVersion = trim((string) ($user?->legal_accepted_version ?? ''));
    $legalAcceptanceColumnsReady = \Illuminate\Support\Facades\Schema::hasColumns('users', ['legal_accepted_at', 'legal_accepted_version']);
    $canAcceptLegalTerms = app(LegalAcceptanceEligibilityService::class)->canUserAccept($user);
    $legalAcceptanceRequired = (bool) $user
        && ! $isSuperAdmin
        && ! $isDemoMode
        && $legalAcceptanceColumnsReady
        && $canAcceptLegalTerms
        && (
            $user?->legal_accepted_at === null
            || $acceptedVersion === ''
            || $acceptedVersion !== $legalCurrentVersion
        );
    $legalTermsDocuments = LegalTerms::orderedDocuments();
    $legalAcceptancePostUrl = route('legal.modal-acceptance.store');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased {{ $themeClass }}" data-theme="{{ $activeTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f3cc9">
    <meta name="pwa-install-enabled" content="{{ $canInstallPwa ? '1' : '0' }}">
    <meta name="pwa-upgrade-message" content="{{ $pwaUpgradeMessage }}">
    @if (! $isSuperAdmin)
        <meta name="push-web-enabled" content="{{ $pushWebEnabled ? '1' : '0' }}">
        <meta name="push-vapid-public-key" content="{{ $pushVapidPublicKey }}">
        <meta name="push-subscribe-url" content="{{ route('notifications.push.subscribe') }}">
        <meta name="push-unsubscribe-url" content="{{ route('notifications.push.unsubscribe') }}">
        <meta name="push-status-url" content="{{ route('notifications.push.status') }}">
        <meta name="push-test-url" content="{{ route('notifications.push.test') }}">
    @endif
    <script>
        (function () {
            var isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches)
                || (window.navigator && window.navigator.standalone === true);
            var mode = isStandalone ? 'standalone' : 'browser';
            document.cookie = 'gym_pwa_mode=' + mode + '; path=/; max-age=2592000; SameSite=Lax';

            if (!isStandalone) {
                return;
            }

            var url = new URL(window.location.href);
            if (url.searchParams.get('pwa_mode') === 'standalone') {
                return;
            }

            url.searchParams.set('pwa_mode', 'standalone');
            window.location.replace(url.toString());
        })();
    </script>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    @if ($canInstallPwa)
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @endif
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    @if ($canInstallPwa)
        <link rel="apple-touch-icon" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    @endif
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
            #user-menu-dropdown,
            #header-bell-dropdown {
                width: min(92vw, 20rem);
            }
        }
        #panel-sidebar {
            position: relative;
            z-index: 40;
        }
        @media (min-width: 1024px) {
            #panel-sidebar {
                position: sticky;
                top: 0;
                align-self: flex-start;
                height: 100dvh;
            }
            #panel-sidebar nav {
                flex: 1 1 auto;
                min-height: 0;
                overflow-y: auto;
            }
        }
        #brand-home-link,
        #brand-home-link * {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        #brand-home-link {
            min-height: 6rem;
        }
        #brand-logo-badge {
            width: clamp(3.8rem, 5.6vw, 5.1rem);
            height: clamp(3.8rem, 5.6vw, 5.1rem);
            min-width: clamp(3.8rem, 5.6vw, 5.1rem);
            min-height: clamp(3.8rem, 5.6vw, 5.1rem);
        }
        #brand-logo-badge > img.brand-logo-media {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-position: center;
        }
        #brand-logo-badge > img.brand-logo-media.brand-logo-media-cover {
            object-fit: cover;
        }
        #brand-logo-badge > img.brand-logo-media.brand-logo-media-contain {
            object-fit: contain;
        }
        #panel-sidebar.sidebar-collapsed #brand-home-link {
            justify-content: center;
            gap: 0;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
            min-height: 6.2rem;
        }
        #panel-sidebar.sidebar-collapsed #brand-logo-badge {
            width: 4.25rem;
            height: 4.25rem;
            min-width: 4.25rem;
            min-height: 4.25rem;
        }
        #panel-sidebar.sidebar-collapsed #brand-logo-badge > img.brand-logo-media {
            object-position: center;
        }
        #panel-sidebar.sidebar-collapsed #brand-logo-badge > img.brand-logo-media.brand-logo-media-contain {
            object-fit: contain;
        }
        #mobile-brand-logo {
            width: 3.25rem;
            height: 3.25rem;
            min-width: 3.25rem;
            min-height: 3.25rem;
            border: 1px solid color-mix(in srgb, var(--border) 82%, transparent);
        }
        #mobile-brand-logo > img.brand-logo-media {
            display: block;
            width: 100%;
            height: 100%;
            object-position: center;
            object-fit: cover;
        }
        #mobile-brand-logo > img.brand-logo-media.brand-logo-media-contain {
            object-fit: contain;
        }
        #panel-header-shell {
            align-items: center;
        }
        #panel-header-left {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .panel-menu-trigger {
            display: none;
            min-height: 2.75rem;
            padding-left: 0.85rem;
            padding-right: 0.85rem;
            border-radius: 0.85rem;
            border-color: color-mix(in srgb, var(--border) 78%, transparent);
            background: color-mix(in srgb, var(--card) 90%, transparent);
            box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.08);
            align-items: center;
            gap: 0.55rem;
        }
        .panel-menu-trigger:hover {
            transform: translateY(-1px);
            border-color: color-mix(in srgb, var(--accent) 62%, var(--border));
        }
        .panel-menu-trigger-icon {
            width: 1.55rem;
            height: 1.55rem;
            border-radius: 0.48rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, color-mix(in srgb, var(--accent) 28%, transparent), color-mix(in srgb, var(--primary) 42%, transparent));
            border: 1px solid color-mix(in srgb, var(--accent) 45%, var(--border));
            color: color-mix(in srgb, var(--text) 90%, #fff);
            flex: 0 0 auto;
        }
        .panel-menu-trigger-label {
            font-size: 0.73rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .panel-header-title-stack {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.16rem;
        }
        .panel-header-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--muted) 92%, #ffffff);
        }
        .panel-header-kicker::before {
            content: '';
            width: 0.44rem;
            height: 0.44rem;
            border-radius: 9999px;
            background: linear-gradient(140deg, var(--accent), var(--primary));
            box-shadow: 0 0 0 2px color-mix(in srgb, var(--accent) 18%, transparent);
            flex: 0 0 auto;
        }
        .panel-header-main {
            margin: 0;
            line-height: 1.08;
            letter-spacing: -0.012em;
            font-size: clamp(1.4rem, 1.08rem + 0.46vw, 1.85rem);
            text-wrap: balance;
        }
        #panel-header-title {
            min-width: 0;
            line-height: 1.15;
        }
        #panel-header-right {
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        #user-menu-button {
            min-height: 2.85rem;
            border-radius: 0.85rem;
        }
        #user-menu-button .panel-user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            min-width: 2.5rem;
            min-height: 2.5rem;
        }
        #header-bell-button {
            position: relative;
            min-height: 2.75rem;
            min-width: 2.75rem;
            border-radius: 0.85rem;
            padding: 0.5rem;
        }
        #header-bell-button svg {
            width: 1.15rem;
            height: 1.15rem;
        }
        .header-bell-count {
            position: absolute;
            top: -0.2rem;
            right: -0.2rem;
            min-width: 1.15rem;
            height: 1.15rem;
            border-radius: 9999px;
            border: 2px solid var(--card);
            background: #22c55e;
            color: #03210f;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 900;
            line-height: 1;
            padding: 0 0.2rem;
        }
        #header-bell-dropdown {
            width: min(92vw, 22rem);
        }
        .header-bell-item {
            display: block;
            border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
            border-radius: 0.8rem;
            padding: 0.62rem 0.7rem;
            transition: background-color 0.18s ease, border-color 0.18s ease;
        }
        .header-bell-item:hover {
            background: color-mix(in srgb, var(--card) 80%, var(--accent) 20%);
            border-color: color-mix(in srgb, var(--accent) 58%, var(--border));
        }
        .header-bell-item.is-unread {
            border-color: color-mix(in srgb, var(--accent) 62%, var(--border));
            box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent) 30%, transparent);
        }
        .header-bell-name {
            margin: 0;
            font-size: 0.82rem;
            font-weight: 800;
            color: color-mix(in srgb, var(--text) 92%, #fff);
            line-height: 1.25;
        }
        .header-bell-mail {
            margin: 0.1rem 0 0;
            font-size: 0.73rem;
            color: color-mix(in srgb, var(--muted) 92%, #fff);
            line-height: 1.25;
        }
        .header-bell-time {
            margin: 0.16rem 0 0;
            font-size: 0.66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: color-mix(in srgb, var(--muted) 78%, #fff);
        }
        .panel-toast-stack {
            position: fixed;
            top: calc(5rem + env(safe-area-inset-top));
            right: max(1rem, env(safe-area-inset-right));
            z-index: 90;
            width: min(92vw, 30rem);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .panel-toast-stack [data-toast] {
            pointer-events: auto;
            box-shadow: 0 14px 36px rgb(2 6 23 / 0.35);
        }
        .ui-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 170;
            display: none;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 20% 20%, rgb(34 197 94 / 0.12), transparent 40%), rgb(2 6 23 / 0.72);
            backdrop-filter: blur(4px);
        }
        .ui-loading-overlay[data-open="1"] {
            display: flex;
        }
        .ui-loading-card {
            width: min(92vw, 19rem);
            border: 1px solid rgb(34 197 94 / 0.45);
            border-radius: 1rem;
            background: linear-gradient(165deg, rgb(2 6 23 / 0.94), rgb(3 14 10 / 0.96));
            box-shadow: 0 0 0 1px rgb(255 255 255 / 0.03), 0 18px 56px rgb(0 0 0 / 0.45), 0 0 30px rgb(34 197 94 / 0.2);
            padding: 1rem 1.1rem;
            color: #f8fafc;
            display: grid;
            justify-items: center;
            gap: 0.62rem;
        }
        .ui-loading-spin {
            width: 3.1rem;
            height: 3.1rem;
            border-radius: 9999px;
            border: 3px solid rgb(255 255 255 / 0.2);
            border-top-color: #22c55e;
            border-right-color: #86efac;
            border-bottom-color: #ffffff;
            animation: ui-spin-rotate 0.9s linear infinite;
            box-shadow: 0 0 18px rgb(34 197 94 / 0.5);
        }
        .ui-loading-title {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #86efac;
        }
        .ui-loading-message {
            margin: 0;
            text-align: center;
            font-size: 0.82rem;
            line-height: 1.35;
            color: rgb(248 250 252 / 0.92);
        }
        @keyframes ui-spin-rotate {
            to { transform: rotate(360deg); }
        }
        .demo-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            min-height: 2rem;
            padding: 0.3rem 0.62rem;
            border-radius: 0.7rem;
            border: 1px solid color-mix(in srgb, var(--accent) 45%, var(--border));
            background: color-mix(in srgb, var(--accent) 12%, transparent);
            color: color-mix(in srgb, var(--text) 94%, #fff);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .demo-header-badge strong {
            font-size: 0.78rem;
            letter-spacing: 0.01em;
        }
        .demo-tour-overlay {
            position: fixed;
            inset: 0;
            z-index: 75;
            background: rgb(2 6 23 / 0.72);
            backdrop-filter: blur(1.5px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.18s ease, visibility 0.18s ease;
            pointer-events: none;
        }
        .demo-tour-overlay[data-open="1"] {
            opacity: 1;
            visibility: visible;
        }
        [data-demo-tour-highlight="1"] {
            position: relative;
            z-index: 86 !important;
            border-radius: 0.7rem;
            outline: 2px solid color-mix(in srgb, var(--accent) 70%, #ffffff);
            outline-offset: 2px;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 62%, #fff), 0 0 28px rgb(14 165 233 / 0.45);
            transition: box-shadow 0.15s ease;
            scroll-margin-top: 7rem;
        }
        .demo-tour-popover {
            position: fixed;
            z-index: 90;
            width: min(92vw, 24rem);
            border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
            background: color-mix(in srgb, var(--card) 96%, transparent);
            border-radius: 1rem;
            box-shadow: 0 18px 44px rgb(2 6 23 / 0.45);
            backdrop-filter: blur(8px);
            padding: 0.92rem;
            opacity: 0;
            visibility: hidden;
            transform: translate3d(0, 6px, 0);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        }
        .demo-tour-popover[data-open="1"] {
            opacity: 1;
            visibility: visible;
            transform: translate3d(0, 0, 0);
        }
        .demo-tour-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.65rem;
        }
        .demo-tour-title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 800;
            line-height: 1.22;
            letter-spacing: -0.01em;
        }
        .demo-tour-dismiss {
            border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
            background: transparent;
            border-radius: 0.6rem;
            width: 1.9rem;
            height: 1.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex: 0 0 auto;
        }
        .demo-tour-dismiss:hover {
            background: color-mix(in srgb, var(--accent) 12%, transparent);
        }
        .legal-accept-overlay {
            position: fixed;
            inset: 0;
            z-index: 160;
            background: rgb(2 6 23 / 0.76);
            backdrop-filter: blur(6px);
            display: grid;
            place-items: center;
            padding: 1rem;
        }
        .legal-accept-dialog {
            width: min(960px, 100%);
            max-height: calc(100dvh - 2rem);
            overflow: auto;
            border-radius: 1rem;
            border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
            background: color-mix(in srgb, var(--card) 95%, transparent);
            box-shadow: 0 28px 52px rgb(2 6 23 / 0.42);
            padding: 1rem;
        }
        .legal-accept-header h2 {
            margin: 0;
            font-size: 1.28rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .legal-accept-header p {
            margin: 0.42rem 0 0;
            color: color-mix(in srgb, var(--muted) 94%, #fff);
            line-height: 1.5;
            font-size: 0.92rem;
        }
        .legal-accept-docs {
            margin-top: 0.8rem;
            display: grid;
            gap: 0.65rem;
        }
        .legal-accept-doc {
            border: 1px solid color-mix(in srgb, var(--border) 68%, transparent);
            border-radius: 0.86rem;
            background: color-mix(in srgb, var(--card-2) 92%, transparent);
            padding: 0.78rem;
        }
        .legal-accept-doc h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }
        .legal-accept-doc p {
            margin: 0.38rem 0 0;
            color: color-mix(in srgb, var(--muted) 95%, #fff);
            line-height: 1.45;
            font-size: 0.9rem;
        }
        .legal-accept-doc ul {
            margin: 0.5rem 0 0;
            padding-left: 1.15rem;
            display: grid;
            gap: 0.32rem;
        }
        .legal-accept-doc li {
            color: color-mix(in srgb, var(--muted) 92%, #fff);
            font-size: 0.86rem;
            line-height: 1.4;
        }
        .legal-accept-form {
            margin-top: 0.86rem;
            display: grid;
            gap: 0.65rem;
        }
        .legal-accept-check {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
            border-radius: 0.8rem;
            padding: 0.72rem;
            background: color-mix(in srgb, var(--card-2) 86%, transparent);
        }
        .legal-accept-check input {
            margin-top: 0.15rem;
            width: 1rem;
            height: 1rem;
            accent-color: color-mix(in srgb, var(--accent) 85%, #fff);
            flex: 0 0 auto;
        }
        .legal-accept-check span {
            font-size: 0.88rem;
            line-height: 1.44;
            color: color-mix(in srgb, var(--text) 90%, #fff);
        }
        .legal-accept-errors {
            border: 1px solid #8b2a3e;
            border-radius: 0.78rem;
            background: rgb(76 20 35 / 0.62);
            padding: 0.62rem 0.72rem;
            display: grid;
            gap: 0.22rem;
        }
        .legal-accept-errors p {
            margin: 0;
            font-size: 0.82rem;
            line-height: 1.35;
            color: #ffd7de;
        }
        .legal-accept-actions {
            display: flex;
            justify-content: flex-end;
        }
        .demo-tour-copy {
            margin-top: 0.56rem;
            font-size: 0.86rem;
            line-height: 1.45;
            color: color-mix(in srgb, var(--muted) 94%, #fff);
        }
        .demo-tour-progress {
            margin-top: 0.72rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: color-mix(in srgb, var(--muted) 86%, #fff);
        }
        .demo-tour-expiry {
            margin-top: 0.42rem;
            font-size: 0.78rem;
            color: color-mix(in srgb, var(--muted) 86%, #fff);
        }
        .demo-tour-expiry strong {
            font-weight: 800;
            color: color-mix(in srgb, var(--text) 95%, #fff);
        }
        .demo-tour-actions {
            margin-top: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .demo-tour-actions .ui-button {
            min-height: 2.2rem;
            padding: 0.45rem 0.75rem;
            font-size: 0.76rem;
        }
        @media (min-width: 1024px) {
            .panel-menu-trigger {
                display: inline-flex;
            }
        }
        @media (max-width: 768px) {
            .panel-toast-stack {
                top: calc(4.5rem + env(safe-area-inset-top));
            }
            .demo-tour-popover {
                right: max(0.6rem, env(safe-area-inset-right)) !important;
                left: max(0.6rem, env(safe-area-inset-left)) !important;
                width: auto;
                max-width: none;
                top: auto !important;
                bottom: calc(5.4rem + env(safe-area-inset-bottom)) !important;
            }

            #panel-header-shell {
                gap: 0.55rem;
                padding-top: 0.65rem;
                padding-bottom: 0.65rem;
            }
            .demo-header-badge {
                min-height: 1.9rem;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                font-size: 0.7rem;
            }
            .demo-header-badge strong {
                font-size: 0.73rem;
            }

            #panel-header-left {
                gap: 0.5rem;
            }

            .panel-menu-trigger {
                min-height: 2.6rem;
                padding-left: 0.7rem;
                padding-right: 0.7rem;
            }

            .panel-header-kicker {
                font-size: 0.64rem;
                letter-spacing: 0.12em;
            }

            .panel-header-main {
                font-size: clamp(1.24rem, 1rem + 0.55vw, 1.5rem);
            }

            #user-menu-button {
                max-width: min(76vw, 15.5rem);
                min-height: 2.7rem;
                padding-left: 0.45rem;
                padding-right: 0.45rem;
            }

            #user-menu-button .panel-user-avatar {
                width: 2.3rem;
                height: 2.3rem;
                min-width: 2.3rem;
                min-height: 2.3rem;
            }
        }
        @media (max-width: 640px) {
            #panel-header-shell {
                grid-template-columns: minmax(0, 1fr);
                row-gap: 0.5rem;
                padding-top: 0.6rem;
                padding-bottom: 0.6rem;
            }
            #panel-header-left {
                width: 100%;
            }
            #panel-header-title {
                min-width: 0;
            }
            #panel-header-right {
                width: 100%;
                justify-content: flex-end;
            }
            .panel-header-main {
                font-size: clamp(1.15rem, 0.98rem + 0.5vw, 1.38rem);
            }
            #mobile-brand-logo {
                width: 3rem;
                height: 3rem;
                min-width: 3rem;
                min-height: 3rem;
            }
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
           data-tour="sidebar-brand"
           class="theme-divider relative z-50 flex cursor-pointer items-center gap-4 border-b px-4 py-4 transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60"
           style="pointer-events:auto;">
            @php
                $hasBrandImage = ($isSuperAdmin && !empty($userPhotoUrl)) || (!$isSuperAdmin && !empty($gymLogo));
            @endphp
            <div id="brand-logo-badge" @class([
                'flex h-[4.75rem] w-[4.75rem] shrink-0 items-center justify-center overflow-hidden rounded-2xl text-base font-black',
                'theme-logo-badge' => ! $hasBrandImage,
                'bg-transparent shadow-none' => $hasBrandImage,
            ])>
                @if ($isSuperAdmin && $userPhotoUrl)
                    <img src="{{ $userPhotoUrl }}" alt="{{ $userName }}" class="brand-logo-media brand-logo-media-cover">
                @elseif ($gymLogo)
                    <img src="{{ $gymLogo }}"
                         alt="Logo"
                         class="brand-logo-media brand-logo-media-contain"
                         data-fallback-src="{{ (!$isSuperAdmin && $userPhotoUrl) ? $userPhotoUrl : '' }}"
                         onerror="var fb=this.dataset.fallbackSrc||''; if(fb!=='' && this.src!==fb){ this.src=fb; this.classList.remove('brand-logo-media-contain'); this.classList.add('brand-logo-media-cover'); return; } this.style.display='none'; var fallback=this.parentNode.querySelector('[data-logo-fallback]'); if (fallback) { fallback.classList.remove('hidden'); }">
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
                   data-tour="nav-{{ $item['icon'] ?? 'item' }}"
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
                            @case('inbox')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M5 8.5 12 13l7-4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
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
                            @case('branches')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 6h10M7 12h5M7 18h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M4 6a1 1 0 1 1 0 .01M4 12a1 1 0 1 1 0 .01M4 18a1 1 0 1 1 0 .01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('staff')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7.5 11a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 18v-1a3.5 3.5 0 0 1 7 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M16.5 11a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M13 18v-1a3.5 3.5 0 0 1 7 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
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
                            @case('legal_acceptances')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 4h6l4 4v12H8a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M14 4v4h4M10 13l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('web')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="16" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M3 8h18M8 4v16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
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
            <div id="panel-header-shell" class="mx-auto grid w-full max-w-7xl grid-cols-[minmax(0,1fr)_auto] items-center gap-3 px-4 py-3 md:px-6 lg:px-8">
                <div id="panel-header-left">
                    <button id="sidebar-toggle" type="button"
                            data-tour="sidebar-toggle"
                            class="panel-menu-trigger hidden ui-button ui-button-ghost px-2.5 py-2 text-xs font-bold lg:inline-flex"
                            aria-label="Ocultar menú"
                            title="Ocultar menú">
                        <span class="panel-menu-trigger-icon" aria-hidden="true">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6h16M4 12h10M4 18h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="panel-menu-trigger-label">Ocultar menú</span>
                    </button>
                    @php
                        $mobileBrandImage = $isSuperAdmin ? $userPhotoUrl : $gymLogo;
                        $mobileBrandImageClass = $isSuperAdmin ? 'brand-logo-media brand-logo-media-cover' : 'brand-logo-media brand-logo-media-contain';
                    @endphp
                    <span id="mobile-brand-logo" @class([
                        'mt-0.5 inline-flex items-center justify-center overflow-hidden rounded-xl text-xs font-black uppercase lg:hidden',
                        'theme-logo-badge' => empty($mobileBrandImage),
                        'bg-transparent shadow-none' => !empty($mobileBrandImage),
                    ])>
                        @if (!empty($mobileBrandImage))
                            <img src="{{ $mobileBrandImage }}"
                                 alt="Logo"
                                 class="{{ $mobileBrandImageClass }}"
                                 onerror="this.style.display='none'; var fallback=this.parentNode.querySelector('[data-mobile-logo-fallback]'); if (fallback) { fallback.classList.remove('hidden'); }">
                            <span data-mobile-logo-fallback class="hidden">{{ $gymInitials }}</span>
                        @else
                            {{ $gymInitials }}
                        @endif
                    </span>
                    <div id="panel-header-title" class="panel-header-title-stack">
                        <p class="panel-header-kicker">{{ __('ui.panel_operativo') }}</p>
                        <h1 class="panel-header-main ui-heading truncate">@yield('page-title', $pageTitle)</h1>
                    </div>
                </div>

                <div id="panel-header-right">
                    @if (!$isSuperAdmin)
                        <form method="GET" action="{{ route('clients.index', $gymRouteParams) }}" class="hidden items-center gap-2 lg:flex">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.search_client') }}"
                                   class="ui-input w-52"
                                   data-tour="header-search-client">
                            <button type="submit" class="ui-button ui-button-primary px-3 py-2 text-xs font-bold" data-tour="header-search-btn">{{ __('ui.search') }}</button>
                        </form>
                    @endif

                    @if ($isDemoMode)
                        <span class="demo-header-badge" title="Tiempo restante de la demo">
                            Demo:
                            <strong data-demo-global-countdown data-demo-countdown-target>{{ $demoExpiresLabel ?: 'calculando...' }}</strong>
                        </span>
                    @endif

                    @if (!$isSuperAdmin && $gymSubscriptionStatus)
                        <x-badge :variant="$statusVariant">{{ $gymSubscriptionStatus }}</x-badge>
                    @endif

                    @if (!$isSuperAdmin)
                        <button id="pwa-install-button"
                                type="button"
                                class="ui-button ui-button-ghost {{ $isStandalonePwaMode ? 'hidden' : 'hidden lg:inline-flex' }} px-3 py-2 text-xs font-bold"
                                data-pwa-enabled="{{ $canInstallPwa ? '1' : '0' }}"
                                title="{{ $canInstallPwa ? 'Instalar app' : $pwaUpgradeMessage }}">
                            {{ $canInstallPwa ? 'Instalar app' : 'PWA bloqueada' }}
                        </button>
                    @endif

                    @if ($isSuperAdmin)
                        <div id="header-bell-root" class="relative">
                            <button id="header-bell-button"
                                    type="button"
                                    class="ui-button ui-button-ghost"
                                    aria-label="Ver mensajes de contacto"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-controls="header-bell-dropdown"
                                    title="Mensajes de contacto web">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 10a6 6 0 1 1 12 0v4l2 2H4l2-2v-4Zm4 8a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @if ($headerContactUnread > 0)
                                    <span class="header-bell-count">{{ min(99, $headerContactUnread) }}</span>
                                @endif
                            </button>

                            <div id="header-bell-dropdown" class="absolute right-0 z-40 mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                                <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2 dark:border-slate-700">
                                    <p class="text-xs font-black uppercase tracking-wide text-slate-700 dark:text-slate-100">Notificaciones web</p>
                                    <a href="{{ $inboxUrl }}" class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Abrir bandeja</a>
                                </div>

                                <div class="grid gap-2 p-2">
                                    @forelse ($headerContactItems as $bellMessage)
                                        @php
                                            $bellName = trim($bellMessage->first_name.' '.$bellMessage->last_name);
                                            $bellUnread = $bellMessage->read_at === null;
                                        @endphp
                                        <a href="{{ route('superadmin.inbox.show', $bellMessage->id) }}"
                                           class="header-bell-item {{ $bellUnread ? 'is-unread' : '' }}">
                                            <p class="header-bell-name">{{ $bellName !== '' ? $bellName : 'Sin nombre' }}</p>
                                            <p class="header-bell-mail">{{ $bellMessage->email }}</p>
                                            <p class="header-bell-time">{{ $bellMessage->created_at?->format('d/m/Y H:i') }}</p>
                                        </a>
                                    @empty
                                        <p class="rounded-xl border border-dashed border-slate-300 px-3 py-6 text-center text-xs text-slate-500 dark:border-slate-700 dark:text-slate-300">
                                            Aun no hay mensajes nuevos desde la web.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="user-menu-root" class="relative">
                        <button id="user-menu-button" type="button" class="ui-button ui-button-ghost flex items-center gap-2 px-2 py-1.5" aria-haspopup="true" aria-expanded="false" aria-controls="user-menu-dropdown" data-tour="user-menu-button">
                            @if ($userPhotoUrl)
                                <span class="panel-user-avatar inline-flex items-center justify-center overflow-hidden rounded-full">
                                    <img id="user-avatar-image" src="{{ $userPhotoUrl }}" alt="{{ $userName }}" class="h-full w-full object-cover object-center">
                                </span>
                            @else
                                <span class="panel-user-avatar inline-flex items-center justify-center rounded-full bg-sky-100 text-sm font-black text-sky-800 dark:bg-sky-900/45 dark:text-sky-200">{{ $userInitial }}</span>
                            @endif
                            <span class="hidden text-sm font-semibold text-slate-800 dark:text-slate-100 lg:inline">{{ $userName }}</span>
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
                                @if (! $isCashierMode)
                                    <a href="{{ $profileUrl }}" class="flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('ui.view_profile') }}</a>
                                    <a href="{{ $settingsUrl }}" class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('ui.settings') }}</a>
                                    <a href="{{ $contactUrl }}" class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('ui.contact') }}</a>
                                @endif

                                @if (! $isSuperAdmin)
                                    <button id="push-notifications-button"
                                            type="button"
                                            class="mt-1 flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                                            data-push-enabled="{{ $pushWebEnabled ? '1' : '0' }}"
                                            title="Gestionar notificaciones push">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M6 10a6 6 0 1 1 12 0v4l2 2H4l2-2v-4Zm4 8a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span>Notificaciones</span>
                                        </span>
                                        <span id="push-notifications-state"
                                              class="inline-flex items-center rounded-full border border-slate-300/80 px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.08em] text-slate-600 dark:border-slate-600 dark:text-slate-300">
                                            Apagadas
                                        </span>
                                    </button>
                                @endif

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
            @if ($isDemoMode)
                <div id="demo-countdown-source"
                     class="hidden"
                     data-demo-expires-at="{{ $demoExpiresAtIso }}"
                     data-demo-server-now="{{ $demoServerNowIso }}"
                     data-demo-end-url="{{ route('demo.end') }}"
                     data-demo-expired-url="{{ route('landing') }}"
                     data-demo-expiry-fallback="{{ $demoExpiresLabel }}">
                </div>
            @endif

            @if (! $isSuperAdmin)
                <section id="pwa-access-alert" class="ui-alert ui-alert-warning hidden text-xs font-semibold"></section>
                <section id="push-access-alert" class="ui-alert ui-alert-info hidden text-xs font-semibold"></section>
            @endif

            @if ($isCashierMode)
                <section class="rounded-2xl border border-cyan-200 bg-cyan-50/90 px-4 py-3 text-sm text-cyan-900 shadow-sm dark:border-cyan-500/40 dark:bg-cyan-900/20 dark:text-cyan-100">
                    <p class="font-bold uppercase tracking-wide">Modo Cajero</p>
                    <p class="mt-1 text-xs">
                        Acceso operativo habilitado para panel, recepcion, clientes, membresias y cobros.
                    </p>
                </section>
            @endif

            @if ($showBranchContextSwitcher)
                <section class="rounded-2xl border border-slate-200/80 bg-white/90 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                    <details class="group" open>
                        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.16em] text-slate-500 dark:text-slate-300">Sucursales</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $activeBranchContextTitle }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">
                                    {{ $activeBranchContextAddress }}
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 transition group-open:text-cyan-700 dark:text-slate-300 dark:group-open:text-cyan-200">
                                Cambiar sede
                                <svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        </summary>
                        <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                            @php
                                $isGlobalContextCardActive = $isAdminGlobalContext || $isGlobalScope;
                            @endphp
                            <a href="{{ $globalContextUrl }}"
                               class="rounded-xl border px-3 py-2 transition {{ $isGlobalContextCardActive ? 'border-cyan-400 bg-cyan-50 text-cyan-900 shadow-sm dark:border-cyan-500/60 dark:bg-cyan-900/25 dark:text-cyan-100' : 'border-slate-200 bg-white text-slate-800 hover:border-cyan-300 hover:bg-cyan-50/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-cyan-500/50 dark:hover:bg-cyan-900/20' }}">
                                <p class="text-sm font-bold leading-tight">Admin global</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">Consolidado de todas las sucursales (solo lectura)</p>
                            </a>
                            @foreach ($branchContextOptions as $branchOption)
                                @php
                                    $isActiveBranchOption = (string) ($branchOption['slug'] ?? '') === $activeBranchContextSlug && ! $isGlobalContextCardActive;
                                @endphp
                                <a href="{{ (string) ($branchOption['url'] ?? '#') }}"
                                   class="rounded-xl border px-3 py-2 transition {{ $isActiveBranchOption ? 'border-cyan-400 bg-cyan-50 text-cyan-900 shadow-sm dark:border-cyan-500/60 dark:bg-cyan-900/25 dark:text-cyan-100' : 'border-slate-200 bg-white text-slate-800 hover:border-cyan-300 hover:bg-cyan-50/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-cyan-500/50 dark:hover:bg-cyan-900/20' }}">
                                    <p class="text-sm font-bold leading-tight">{{ (string) ($branchOption['name'] ?? 'Sucursal') }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">{{ (string) ($branchOption['address'] ?? '-') }}</p>
                                </a>
                            @endforeach
                        </div>
                    </details>
                </section>
            @endif

            @if ($isGlobalScope)
                <section class="rounded-2xl border border-cyan-200 bg-cyan-50/90 px-4 py-3 text-sm text-cyan-900 shadow-sm dark:border-cyan-500/40 dark:bg-cyan-900/20 dark:text-cyan-100">
                    <p class="font-bold uppercase tracking-wide">Modo Admin global</p>
                    <p class="mt-1 text-xs">
                        Vista consolidada y de solo lectura para recepcion, clientes, planes y caja.
                        Selecciona una sede especifica para crear o editar datos.
                    </p>
                </section>
            @endif

            <div class="panel-toast-stack" aria-live="polite" aria-atomic="true">
            @if (!empty($subscription_grace))
                <x-toast type="warning" :autohide="false">{{ __('ui.toast.grace_subscription', ['days' => (int) ($subscription_grace_days ?? 3)]) }}</x-toast>
            @endif

            @if (session('status'))
                <x-toast type="success">{{ session('status') }}</x-toast>
            @endif
            @if (session('error'))
                <x-toast type="danger" :autohide="false">{{ session('error') }}</x-toast>
            @endif

            @if ($errors->any() && ! $suppressGlobalValidationToast)
                <x-toast type="danger" :autohide="false">{{ $errors->first() }}</x-toast>
            @endif
            </div>

            @if ($isDemoMode && count($demoGuideSteps) > 0)
                <div id="demo-tour-overlay" class="demo-tour-overlay" data-open="0" aria-hidden="true"></div>

                <section id="demo-tour-popover"
                         class="demo-tour-popover"
                         data-open="0"
                         data-demo-token="{{ $demoSessionToken }}"
                         data-demo-steps='@json($demoGuideSteps)'
                         data-demo-expires-at="{{ $demoExpiresAtIso }}"
                         data-demo-server-now="{{ $demoServerNowIso }}"
                         data-demo-end-url="{{ route('demo.end') }}"
                         data-demo-expired-url="{{ route('landing') }}"
                         data-demo-expiry-fallback="{{ $demoExpiresLabel }}"
                         aria-live="polite"
                         aria-label="Guia guiada demo">
                    <div class="demo-tour-header">
                        <h2 class="demo-tour-title" data-demo-tour-title>Guia del sistema</h2>
                        <button type="button" class="demo-tour-dismiss" data-demo-tour-close aria-label="Cerrar guia">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M5 5L15 15M15 5L5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    <p class="demo-tour-copy" data-demo-tour-text></p>
                    <p class="demo-tour-progress" data-demo-tour-progress></p>
                    <p class="demo-tour-expiry">
                        Demo activa:
                        <strong data-demo-tour-countdown data-demo-countdown-target>{{ $demoExpiresLabel ?: 'calculando...' }}</strong>
                    </p>
                    <div class="demo-tour-actions">
                        <button type="button" class="ui-button ui-button-ghost" data-demo-tour-prev>Anterior</button>
                        <button type="button" class="ui-button ui-button-primary" data-demo-tour-next>Siguiente</button>
                        <button type="button" class="ui-button ui-button-secondary" data-demo-tour-open-route>Ir al paso</button>
                    </div>
                </section>
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

<div id="ui-loading-overlay" class="ui-loading-overlay" data-open="0" aria-hidden="true">
    <div class="ui-loading-card" role="status" aria-live="polite">
        <span class="ui-loading-spin" aria-hidden="true"></span>
        <p class="ui-loading-title">Cargando</p>
        <p id="ui-loading-message" class="ui-loading-message">Procesando solicitud...</p>
    </div>
</div>

@include('layouts.partials.panel-inline-scripts')
@stack('scripts')
</body>
</html>
