@php
    use App\Models\Gym;
    use App\Models\GymBranchLink;
    use App\Models\LandingContactMessage;
    use App\Models\PresenceSession;
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
    $panelDisplayTimezone = trim((string) ($user?->timezone ?? ''));
    if (
        $panelDisplayTimezone === ''
        || $panelDisplayTimezone === 'UTC'
        || ! in_array($panelDisplayTimezone, timezone_identifiers_list(), true)
    ) {
        $panelDisplayTimezone = 'America/Guayaquil';
    }
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
    $headerLiveClientsVisible = ! $isSuperAdmin && $activeGymId > 0;
    $headerLiveClientsCount = 0;
    $headerLiveClientsUrl = null;
    $headerLiveDate = now()->toDateString();
    if ($headerLiveClientsVisible) {
        $headerLiveClientsCount = PresenceSession::query()
            ->forGym($activeGymId)
            ->open()
            ->whereDate('check_in_at', $headerLiveDate)
            ->count();
        if (\Illuminate\Support\Facades\Route::has('panel.live-clients') && $activeGymSlug !== '') {
            $headerLiveClientsUrl = route('panel.live-clients', $gymRouteParams);
        }
    }
    $canViewReports = $isSuperAdmin || ($activeGymId > 0 ? $planAccessService->canForGym($activeGymId, 'reports_base') : false);
    $canUseSalesInventory = ! $isSuperAdmin && $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'sales_inventory');
    $canViewBranches = $canUseMultiBranch;
    $canInstallPwa = ! $isSuperAdmin && $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'pwa_install');
    $usePremiumPanelVisuals = ! $isSuperAdmin && $activeGymId > 0;
    $pushVapidPublicKey = trim((string) config('services.webpush.vapid.public_key', ''));
    $pushWebEnabled = (bool) config('services.webpush.enabled', false);
    $pwaUpgradeMessage = 'Sube de plan a Profesional, Premium o Sucursales para usar la app instalable (PWA).';
    $suppressGlobalValidationToast = request()->routeIs('clients.index') && (bool) old('_open_create_modal', false);

    $branchesRouteParams = $hubGymSlug !== ''
        ? ['contextGym' => $hubGymSlug]
            + ($isGlobalScope ? ['scope' => 'global'] : [])
            + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : [])
        : $gymRouteParams;
    $switchableRouteNames = ['panel.index', 'reception.index', 'clients.index', 'sales.index', 'products.index', 'plans.index', 'cash.index', 'reports.index', 'reports.client-earnings', 'reports.sales-inventory', 'branches.index', 'staff.index', 'client-portal.index'];
    $currentRouteName = (string) (\Illuminate\Support\Facades\Route::currentRouteName() ?? '');
    $baseSwitcherRoute = in_array($currentRouteName, $switchableRouteNames, true) ? $currentRouteName : 'panel.index';
    $showGlobalBranchOption = ! request()->routeIs('client-portal.*');

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
    if ($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.index')) {
        $salesNavItem = ['label' => __('ui.nav.sales_inventory'), 'route' => 'sales.index', 'params' => $gymRouteParams, 'active' => 'sales.*', 'icon' => 'sales_inventory'];
        $productsNavItem = ['label' => __('ui.nav.products'), 'route' => 'products.index', 'params' => $gymRouteParams, 'active' => 'products.*', 'icon' => 'products'];

        array_splice($gymNavItems, 3, 0, [$salesNavItem, $productsNavItem]);
    }
    if (! $isCashierMode && \Illuminate\Support\Facades\Route::has('staff.index')) {
        $gymNavItems[] = ['label' => 'Cajeros', 'route' => 'staff.index', 'params' => $gymRouteParams, 'active' => 'staff.*', 'icon' => 'staff'];
    }
    if (! $isCashierMode && $canViewBranches && \Illuminate\Support\Facades\Route::has('branches.index')) {
        $gymNavItems[] = ['label' => 'Sucursales', 'route' => 'branches.index', 'params' => $branchesRouteParams, 'active' => 'branches.*', 'icon' => 'branches'];
    }
    if (! $isCashierMode && $canViewReports) {
        $gymNavItems[] = ['label' => __('ui.nav.reports'), 'route' => 'reports.index', 'params' => $gymRouteParams, 'active' => 'reports.*', 'icon' => 'reports'];
    }
    if (
        ! $isSuperAdmin
        && $activeGymId > 0
        && $planAccessService->canForGym($activeGymId, 'client_accounts')
        && \Illuminate\Support\Facades\Route::has('client-portal.index')
    ) {
        $gymNavItems[] = [
            'label' => 'Portal cliente',
            'route' => 'client-portal.index',
            'params' => $gymRouteParams,
            'active' => 'client-portal.*',
            'icon' => 'client_portal',
            'highlight' => true,
        ];
    }

    $navItems = $isSuperAdmin
        ? [
            ['label' => __('ui.nav.panel'), 'route' => 'superadmin.dashboard', 'params' => [], 'active' => 'superadmin.dashboard', 'icon' => 'panel'],
            ['label' => 'Solicitudes de cotización', 'route' => 'superadmin.quotations.index', 'params' => [], 'active' => 'superadmin.quotations.*', 'icon' => 'quotations'],
            ['label' => 'Listado de gimnasios', 'route' => 'superadmin.gym-list.index', 'params' => [], 'active' => 'superadmin.gym-list.*', 'icon' => 'gym_directory'],
            ['label' => 'Gimnasios y Suscripciones', 'route' => 'superadmin.gyms.index', 'params' => [], 'active' => 'superadmin.gyms.*|superadmin.subscriptions.*', 'icon' => 'subscriptions_admin'],
            ['label' => 'Sucursales globales', 'route' => 'superadmin.branches.index', 'params' => [], 'active' => 'superadmin.branches.*', 'icon' => 'branches'],
            ['label' => 'Crear nuevo gimnasio', 'route' => 'superadmin.gym.index', 'params' => [], 'active' => 'superadmin.gym.*', 'icon' => 'gym_create'],
            ['label' => 'Planes', 'route' => 'superadmin.plan-templates.index', 'params' => [], 'active' => 'superadmin.plan-templates.*', 'icon' => 'plans'],
            ['label' => 'Mensajes web', 'route' => 'superadmin.inbox.index', 'params' => [], 'active' => 'superadmin.inbox.*', 'icon' => 'inbox'],
            ['label' => __('ui.nav.notifications'), 'route' => 'superadmin.notifications.index', 'params' => [], 'active' => 'superadmin.notifications.*', 'icon' => 'notifications'],
            ['label' => __('ui.nav.suggestions'), 'route' => 'superadmin.suggestions.index', 'params' => [], 'active' => 'superadmin.suggestions.*', 'icon' => 'suggestions'],
            ['label' => 'Aceptaciones legales', 'route' => 'superadmin.legal-acceptances.index', 'params' => [], 'active' => 'superadmin.legal-acceptances.*', 'icon' => 'legal_acceptances'],
            ['label' => 'Administrar página web', 'route' => 'superadmin.web-page.edit', 'params' => [], 'active' => 'superadmin.web-page.*', 'icon' => 'web'],
          ]
        : $gymNavItems;

    $sectionLabels = [
        'operation' => 'Operaci&oacute;n',
        'commercial' => 'Comercial',
        'management' => 'Administraci&oacute;n',
        'communication' => 'Comunicaci&oacute;n',
        'channels' => 'Canales',
        'platform' => 'Plataforma',
    ];

    $resolveNavSection = static function (array $item, bool $isSuperAdminMode): string {
        $icon = (string) ($item['icon'] ?? '');

        if ($isSuperAdminMode) {
            return match ($icon) {
                'panel' => 'operation',
                'quotations', 'inbox', 'notifications', 'suggestions', 'legal_acceptances' => 'communication',
                'gym_directory', 'subscriptions_admin', 'branches', 'gym_create', 'plans', 'web' => 'management',
                default => 'platform',
            };
        }

        return match ($icon) {
            'panel', 'reception', 'clients' => 'operation',
            'sales_inventory', 'products', 'plans', 'cash' => 'commercial',
            'staff', 'branches', 'reports' => 'management',
            'client_portal' => 'channels',
            default => 'platform',
        };
    };

    $navSectionsMap = [];
    foreach ($navItems as $navItem) {
        $sectionKey = $resolveNavSection($navItem, $isSuperAdmin);
        if (! isset($navSectionsMap[$sectionKey])) {
            $navSectionsMap[$sectionKey] = [
                'key' => $sectionKey,
                'label' => $sectionLabels[$sectionKey] ?? 'M&oacute;dulos',
                'items' => [],
            ];
        }
        $navSectionsMap[$sectionKey]['items'][] = $navItem;
    }
    $navSections = array_values($navSectionsMap);

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
    <meta name="pwa-events-url" content="{{ route('pwa.events.store') }}">
    <meta name="theme-color" content="#16c172">
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
    <meta name="apple-mobile-web-app-title" content="FlexGym">
    @if ($canInstallPwa)
        <link rel="manifest" href="{{ asset('manifest.webmanifest?v=20260322') }}">
    @endif
    <link rel="icon" href="{{ asset('favicon.ico?v=20260322') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/fg-favicon-32.png?v=20260322') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('pwa/fg-favicon-16.png?v=20260322') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/fg-favicon-32.png?v=20260322') }}">
    @if ($canInstallPwa)
        <link rel="apple-touch-icon" href="{{ asset('pwa/fg-favicon-180.png?v=20260322') }}">
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
                max-height: min(70vh, 30rem);
            }
        }
        .panel-premium-mode {
            --panel-shell-max-width: 88rem;
        }
        .panel-premium-mode #panel-header-shell,
        .panel-premium-mode .panel-view {
            max-width: min(var(--panel-shell-max-width), calc(100vw - 1.25rem));
        }
        .panel-premium-mode .panel-view {
            padding-top: 1.35rem;
            padding-bottom: 1.8rem;
        }
        @media (max-width: 1023px) {
            .panel-premium-mode #panel-header-shell,
            .panel-premium-mode .panel-view {
                max-width: 100%;
            }
            .panel-premium-mode .panel-view {
                padding-top: 1.1rem;
                padding-bottom: calc(1.35rem + env(safe-area-inset-bottom));
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
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.95rem;
        }
        .sidebar-nav-section {
            display: flex;
            flex-direction: column;
            gap: 0.32rem;
        }
        .sidebar-section-label {
            margin: 0;
            padding-left: 0.65rem;
            font-size: 0.64rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--sidebar-section-text) !important;
            opacity: 0.9;
        }
        .sidebar-nav-item {
            position: relative;
            overflow: hidden;
            min-height: 2.7rem;
            border-radius: 0.85rem;
        }
        .sidebar-nav-item .sidebar-icon {
            width: 1.95rem;
            height: 1.95rem;
            min-width: 1.95rem;
            min-height: 1.95rem;
            border-radius: 0.6rem;
            transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease;
        }
        .sidebar-nav-item .sidebar-link-badge {
            line-height: 1;
        }
        #panel-sidebar.sidebar-collapsed nav {
            overflow-x: visible;
        }
        #panel-sidebar.sidebar-collapsed .sidebar-nav {
            gap: 0.65rem;
        }
        #panel-sidebar.sidebar-collapsed .sidebar-nav-section {
            gap: 0.25rem;
        }
        #panel-sidebar.sidebar-collapsed .sidebar-section-label {
            display: none;
        }
        #panel-sidebar.sidebar-collapsed .sidebar-nav-item {
            justify-content: center;
            gap: 0;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        #panel-sidebar.sidebar-collapsed .sidebar-nav-item .sidebar-link-badge {
            display: none;
        }
        #panel-sidebar.sidebar-collapsed .sidebar-nav-item .sidebar-icon {
            width: 2.05rem;
            height: 2.05rem;
            min-width: 2.05rem;
            min-height: 2.05rem;
        }
        #sidebar-collapsed-tooltip {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 130;
            max-width: min(18rem, calc(100vw - 1.5rem));
            padding: 0.7rem 0.85rem;
            border-radius: 0.95rem;
            border: 1px solid color-mix(in srgb, var(--accent) 30%, var(--border));
            background: linear-gradient(135deg, color-mix(in srgb, var(--card) 94%, #020617), color-mix(in srgb, var(--card) 88%, var(--accent)));
            color: color-mix(in srgb, var(--text) 96%, #ffffff);
            box-shadow: 0 18px 38px rgb(2 6 23 / 0.34);
            font-size: 0.78rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: 0.02em;
            white-space: normal;
            pointer-events: none;
            opacity: 0;
            transform: translate3d(0, -50%, 0) scale(0.96);
            transform-origin: left center;
            transition: opacity 0.14s ease, transform 0.14s ease;
        }
        #sidebar-collapsed-tooltip[data-open="1"] {
            opacity: 1;
            transform: translate3d(0, -50%, 0) scale(1);
        }
        #sidebar-collapsed-tooltip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -0.38rem;
            width: 0.82rem;
            height: 0.82rem;
            border-left: 1px solid color-mix(in srgb, var(--accent) 30%, var(--border));
            border-bottom: 1px solid color-mix(in srgb, var(--accent) 30%, var(--border));
            background: color-mix(in srgb, var(--card) 94%, #020617);
            transform: translateY(-50%) rotate(45deg);
            border-radius: 0 0 0 0.2rem;
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
            flex-wrap: nowrap;
            overflow: visible;
        }
        #user-menu-root,
        #header-bell-root {
            position: relative;
            z-index: 45;
        }
        .header-live-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.48rem;
            min-height: 2.35rem;
            padding: 0.38rem 0.82rem 0.38rem 0.72rem;
            border-radius: 9999px;
            border: 1px solid rgb(16 185 129 / 0.52);
            background: linear-gradient(135deg, rgb(16 185 129 / 0.2), rgb(16 185 129 / 0.08) 38%, rgb(2 6 23 / 0.2));
            color: #d1fae5;
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.05), 0 0 0 1px rgb(16 185 129 / 0.2), 0 0 22px rgb(16 185 129 / 0.2);
            font-size: 0.69rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            line-height: 1;
            white-space: nowrap;
            transition: box-shadow 0.22s ease, transform 0.22s ease;
            animation: header-live-breathe 1.9s ease-in-out infinite;
        }
        .header-live-pill.is-updated {
            transform: translateY(-1px);
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.07), 0 0 0 1px rgb(16 185 129 / 0.34), 0 0 30px rgb(16 185 129 / 0.34);
        }
        .header-live-pill .live-core-dot {
            position: relative;
            width: 0.62rem;
            height: 0.62rem;
            border-radius: 9999px;
            background: #34d399;
            box-shadow: 0 0 0 1px rgb(5 150 105 / 0.5), 0 0 0 5px rgb(16 185 129 / 0.26), 0 0 14px rgb(52 211 153 / 0.85);
            flex: 0 0 auto;
        }
        .header-live-pill .live-core-dot::after {
            content: '';
            position: absolute;
            inset: -7px;
            border-radius: 9999px;
            border: 1px solid rgb(52 211 153 / 0.44);
            animation: header-live-pulse 1.75s ease-out infinite;
        }
        .header-live-pill .live-label {
            opacity: 0.96;
        }
        .header-live-pill .live-count {
            font-size: 1rem;
            line-height: 1;
            letter-spacing: 0;
            color: #ecfdf5;
            text-shadow: 0 0 14px rgb(16 185 129 / 0.45);
            min-width: 1.5ch;
            text-align: center;
            font-variant-numeric: tabular-nums;
        }
        .theme-light .header-live-pill {
            color: #065f46;
            border-color: rgb(16 185 129 / 0.45);
            background: linear-gradient(135deg, rgb(16 185 129 / 0.18), rgb(209 250 229 / 0.95));
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.55), 0 0 0 1px rgb(16 185 129 / 0.22), 0 0 18px rgb(16 185 129 / 0.14);
        }
        .theme-light .header-live-pill.is-updated {
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.65), 0 0 0 1px rgb(16 185 129 / 0.28), 0 0 22px rgb(16 185 129 / 0.22);
        }
        .theme-light .header-live-pill .live-count {
            color: #047857;
            text-shadow: none;
        }
        .theme-nav-link.nav-link-highlight {
            border-color: color-mix(in srgb, #22c55e 40%, var(--sidebar-border));
            background: color-mix(in srgb, #22c55e 16%, var(--sidebar));
            color: color-mix(in srgb, var(--sidebar-text) 94%, #ecfdf5);
            box-shadow: none;
        }
        .theme-nav-link.nav-link-highlight:hover {
            background: color-mix(in srgb, #22c55e 24%, var(--sidebar));
            border-color: color-mix(in srgb, #34d399 56%, var(--sidebar-border));
        }
        .theme-nav-active.nav-link-highlight {
            background: color-mix(in srgb, #22c55e 26%, var(--sidebar));
            border-color: color-mix(in srgb, #22c55e 62%, var(--sidebar-border));
            color: var(--sidebar-active-text);
            box-shadow: inset 3px 0 0 color-mix(in srgb, #34d399 70%, #ffffff);
        }
        .theme-nav-mobile-link.theme-nav-mobile-highlight {
            border: 1px solid color-mix(in srgb, #22c55e 42%, var(--border));
            background: linear-gradient(130deg, rgb(16 185 129 / 0.16), rgb(2 6 23 / 0.4));
            color: color-mix(in srgb, var(--text) 92%, #ecfdf5);
        }
        @keyframes header-live-breathe {
            0%, 100% {
                transform: translateY(0) scale(1);
                box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.05), 0 0 0 1px rgb(16 185 129 / 0.2), 0 0 18px rgb(16 185 129 / 0.18);
            }
            50% {
                transform: translateY(-1px) scale(1.018);
                box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.07), 0 0 0 1px rgb(16 185 129 / 0.34), 0 0 30px rgb(16 185 129 / 0.35);
            }
        }
        @keyframes header-live-pulse {
            0% { transform: scale(0.42); opacity: 0.9; }
            72% { opacity: 0.2; }
            100% { transform: scale(1.58); opacity: 0; }
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

            #panel-header-right {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 0.42rem;
            }

            #panel-header-right > * {
                flex: 0 0 auto;
            }

            .header-live-pill {
                min-height: 2.12rem;
                padding: 0.34rem 0.7rem 0.34rem 0.62rem;
                font-size: 0.64rem;
                letter-spacing: 0.11em;
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
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 0.42rem;
                overflow: visible;
            }
            #panel-header-right > * {
                flex: 0 0 auto;
            }
            #user-menu-root,
            #header-bell-root {
                position: static;
            }
            #user-menu-button {
                min-width: 3.5rem;
                justify-content: space-between;
                gap: 0.55rem;
            }
            #user-menu-dropdown,
            #header-bell-dropdown {
                border-radius: 1.1rem;
                box-shadow: 0 22px 50px rgb(2 6 23 / 0.42);
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
<body @class(['theme-body h-full ui-text', 'panel-premium-mode' => $usePremiumPanelVisuals])>
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

        <nav class="sidebar-nav px-3 py-4">
            @foreach ($navSections as $section)
                <section class="sidebar-nav-section" data-sidebar-section="{{ $section['key'] }}">
                    <p class="sidebar-section-label sidebar-label">{!! $section['label'] !!}</p>
                    @foreach ($section['items'] as $item)
                        @php
                            $activePatterns = explode('|', $item['active']);
                            $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                            $isHighlight = (bool) ($item['highlight'] ?? false);
                            $navClass = $isActive ? 'theme-nav-active' : 'theme-nav-link';
                            if ($isHighlight) {
                                $navClass .= ' nav-link-highlight';
                            }
                        @endphp
                        <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                           data-tour="nav-{{ $item['icon'] ?? 'item' }}"
                           data-sidebar-label="{{ $item['label'] }}"
                           aria-label="{{ $item['label'] }}"
                           class="sidebar-nav-item flex items-center gap-2.5 rounded-xl px-2.5 py-2 text-sm font-semibold transition {{ $navClass }}">
                            <span class="sidebar-icon inline-flex items-center justify-center">
                        @switch($item['icon'] ?? '')
                            @case('panel')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 4h8v8H3V4Zm10 0h8v5h-8V4ZM3 14h8v6H3v-6Zm10-3h8v9h-8v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('quotations')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 4h8l4 4v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M16 4v4h4M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('gym_directory')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="4" y="5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.8"/>
                                    <rect x="4" y="14" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M12.5 8h6.5M12.5 11h4.5M12.5 16h6.5M12.5 19h4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('subscriptions_admin')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="4" y="6" width="13" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M7 10h7M7 14h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <circle cx="18.5" cy="8.5" r="3.5" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M18.5 6.9v3.2M16.9 8.5h3.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
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
                                    <path d="M5 8.2A2.2 2.2 0 0 1 7.2 6h8.1a2 2 0 0 1 1.4.58l1.72 1.72a2 2 0 0 1 .58 1.41v7.1A2.2 2.2 0 0 1 16.8 19H8.1a2 2 0 0 1-1.41-.58L5.58 17.3A2 2 0 0 1 5 15.89V8.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <circle cx="9.2" cy="9.2" r="1.1" stroke="currentColor" stroke-width="1.8"/>
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
                                    <rect x="4" y="5" width="5" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                                    <rect x="15" y="5" width="5" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                                    <rect x="9.5" y="15" width="5" height="4" rx="1" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M6.5 9v2.5c0 .55.45 1 1 1H12m5.5-3.5v2.5c0 .55-.45 1-1 1H12m0 0V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
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
                            @case('gym_create')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 20V8.4a1.4 1.4 0 0 1 .68-1.2l6.6-4a1.4 1.4 0 0 1 1.44 0l6.6 4A1.4 1.4 0 0 1 20 8.4V14" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M9 20v-4.5h4V20M18 17v5M15.5 19.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
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
                            @case('client_portal')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="7.5" y="2.8" width="9" height="18.4" rx="2.3" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M11 18.3h2M9.4 6.7h5.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M3.8 10h2.3M18 10h2.2M5.3 6.3 6.9 7.9M18.7 6.3 17.1 7.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @case('sales_inventory')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 18.5h16M7.5 15V9.5M12 15V6.5M16.5 15v-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M6 5.5h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @break
                            @case('products')
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3 4.5 7 12 11l7.5-4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M4.5 7v10L12 21l7.5-4V7" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M12 11v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                @break
                            @default
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                        @endswitch
                    </span>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                    @if ($isHighlight)
                        <span class="sidebar-link-badge ml-auto rounded-full border border-emerald-300/45 bg-emerald-500/20 px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.1em] text-emerald-100">
                            Link
                        </span>
                    @endif
                </a>
                    @endforeach
                </section>
            @endforeach
        </nav>

    </aside>
    <div id="sidebar-collapsed-tooltip" data-open="0" aria-hidden="true"></div>

    <div class="flex-1 pb-24 md:pb-20 lg:pb-0">
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

                    @if ($headerLiveClientsVisible)
                        @hasSection('header-live-banner')
                            @yield('header-live-banner')
                        @else
                            <span id="header-live-banner"
                                  class="header-live-pill"
                                  data-live-url="{{ $headerLiveClientsUrl ?? '' }}">
                                <span class="live-core-dot" aria-hidden="true"></span>
                                <span class="live-label">PRESENTES</span>
                                <strong id="header-live-clients" class="live-count">{{ (int) $headerLiveClientsCount }}</strong>
                            </span>
                        @endif
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
                                            <p class="header-bell-time">{{ $bellMessage->created_at?->copy()->timezone($panelDisplayTimezone)?->format('d/m/Y H:i') }}</p>
                                        </a>
                                    @empty
                                        <p class="rounded-xl border border-dashed border-slate-300 px-3 py-6 text-center text-xs text-slate-500 dark:border-slate-700 dark:text-slate-300">
                                            Aún no hay mensajes nuevos desde la web.
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

                                @if (! $isSuperAdmin && ! $isDemoMode)
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
                        Acceso operativo habilitado para panel, recepción, clientes, membresías y cobros.
                    </p>
                </section>
            @endif

            @include('layouts.partials.panel.branch-context-switcher')

            @include('layouts.partials.panel.global-scope-banner')

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

@include('layouts.partials.panel.mobile-nav')

@include('layouts.partials.panel.legal-acceptance-modal')
<div id="ui-loading-overlay" class="ui-loading-overlay" data-open="0" aria-hidden="true">
    <div class="ui-loading-card" role="status" aria-live="polite">
        <span class="ui-loading-spin" aria-hidden="true"></span>
        <p class="ui-loading-title">Cargando</p>
        <p id="ui-loading-message" class="ui-loading-message">Procesando solicitud...</p>
    </div>
</div>

<script>
    (function () {
        const headerLiveBanner = document.getElementById('header-live-banner');
        const headerLiveClients = document.getElementById('header-live-clients');

        if (!headerLiveBanner || !headerLiveClients) {
            return;
        }

        const liveUrl = (headerLiveBanner.getAttribute('data-live-url') || '').trim();
        if (liveUrl === '') {
            return;
        }

        let inFlight = false;
        let pollTimer = null;
        let updateFlashTimer = null;
        let lastRenderedCount = Number(headerLiveClients.textContent || '0');

        async function refreshLiveClients() {
            if (inFlight) {
                return;
            }

            inFlight = true;
            try {
                const response = await fetch(liveUrl, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                if (payload && Object.prototype.hasOwnProperty.call(payload, 'count')) {
                    const nextCount = Number(payload.count);
                    const normalizedCount = Number.isFinite(nextCount) ? Math.max(0, Math.floor(nextCount)) : 0;
                    headerLiveClients.textContent = String(normalizedCount);

                    if (normalizedCount !== lastRenderedCount) {
                        lastRenderedCount = normalizedCount;
                        headerLiveBanner.classList.add('is-updated');
                        if (updateFlashTimer) {
                            clearTimeout(updateFlashTimer);
                        }
                        updateFlashTimer = window.setTimeout(function () {
                            headerLiveBanner.classList.remove('is-updated');
                            updateFlashTimer = null;
                        }, 760);
                    }
                }
            } catch (error) {
                // Ignore intermittent network failures.
            } finally {
                inFlight = false;
            }
        }

        refreshLiveClients();
        pollTimer = window.setInterval(refreshLiveClients, 12000);

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                refreshLiveClients();
            }
        });

        window.addEventListener('beforeunload', function () {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
            if (updateFlashTimer) {
                clearTimeout(updateFlashTimer);
                updateFlashTimer = null;
            }
        });
    })();
</script>

@include('layouts.partials.panel-inline-scripts')
@stack('scripts')
</body>
</html>

