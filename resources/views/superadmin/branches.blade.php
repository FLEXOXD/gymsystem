@extends('layouts.panel')

@section('title', 'SuperAdmin Sucursales')
@section('page-title', 'Gestión multisucursal')
@push('styles')
<style>
    .sa-branch-pill {
        display: inline-flex;
        border-radius: 9999px;
        padding: 0.25rem 0.625rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        line-height: 1.2;
        border: 1px solid transparent;
    }

    .sa-branch-pill-plan { background: #e0f2fe; color: #0c4a6e; border-color: #bae6fd; }
    .sa-branch-pill-cash-hub { background: #fef3c7; color: #78350f; border-color: #fde68a; }
    .sa-branch-pill-cash-local { background: #dcfce7; color: #14532d; border-color: #bbf7d0; }
    .sa-branch-pill-status-active { background: #dcfce7; color: #14532d; border-color: #86efac; }
    .sa-branch-pill-status-other { background: #e2e8f0; color: #0f172a; border-color: #cbd5e1; }

    .theme-dark .sa-branch-pill-plan { background: #0c4a6e; color: #e0f2fe; border-color: #0369a1; }
    .theme-dark .sa-branch-pill-cash-hub { background: #78350f; color: #fef3c7; border-color: #92400e; }
    .theme-dark .sa-branch-pill-cash-local { background: #14532d; color: #dcfce7; border-color: #166534; }
    .theme-dark .sa-branch-pill-status-active { background: #166534; color: #dcfce7; border-color: #15803d; }
    .theme-dark .sa-branch-pill-status-other { background: #334155; color: #f8fafc; border-color: #475569; }

    [data-branch-row][hidden] {
        display: none !important;
    }
</style>
@endpush
@section('content')
    @php
        $links = $links ?? collect();
        $hubGyms = $hubGyms ?? collect();
        $kpis = $kpis ?? [];
        $locationCatalog = $locationCatalog ?? [];
        $branchPlanOptions = $branchPlanOptions ?? [];
        $hubGymAdminDomains = is_array($hubGymAdminDomains ?? null) ? $hubGymAdminDomains : [];
        $defaultBranchCountry = old('branch_country', $defaultBranchCountry ?? 'ec');
        $defaultBranchState = old('branch_state', '');
        $defaultBranchCity = old('branch_city', '');
        $statesForCountry = $locationCatalog[$defaultBranchCountry]['states'] ?? [];
        $citiesForState = $statesForCountry[$defaultBranchState] ?? [];
        $linkCollection = method_exists($links, 'getCollection') ? $links->getCollection() : collect($links);
        $linkStatuses = $linkCollection
            ->pluck('status')
            ->filter(static fn ($value): bool => filled($value))
            ->map(static fn ($value): string => (string) $value)
            ->unique()
            ->sort()
            ->values();
        $hubCount = $hubGyms->count();
        $visibleLinksCount = $linkCollection->count();
        $branchCount = (int) ($kpis['total_branches'] ?? 0);
        $averageBranchesPerHub = $hubCount > 0 ? round($branchCount / $hubCount, 1) : 0;
        $canCreateBranch = $hubCount > 0 && count($branchPlanOptions) > 0;

        $formatAddress = static function ($gym): string {
            if (! $gym) {
                return '-';
            }

            $parts = collect([
                trim((string) ($gym->address_line ?? '')),
                trim((string) ($gym->address_city ?? '')),
                trim((string) ($gym->address_state ?? '')),
            ])->filter(static fn (string $value): bool => $value !== '');

            if ($parts->isNotEmpty()) {
                return (string) $parts->implode(', ');
            }

            $fallback = trim((string) ($gym->address ?? ''));

            return $fallback !== '' ? $fallback : '-';
        };
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Red multisucursal</span>
                    <h2 class="sa-title">Red multisucursal clara para crear, leer y gestionar sedes sin enredos.</h2>
                    <p class="sa-subtitle">
                        Primero eliges la sede principal, luego plan, ubicacion y acceso. La caja queda claramente bajo control del hub.
                    </p>
                    <div class="sa-actions">
                        <a href="#branch-create-form" class="ui-button ui-button-primary">Crear nueva sucursal</a>
                        <a href="#branch-links-section" class="ui-button ui-button-ghost">Revisar vínculos actuales</a>
                        <span class="sa-pill is-info">Caja centralizada por la sede principal</span>
                    </div>
                </div>

                    <div class="sa-note-card">
                    <p class="sa-note-label">Claves del flujo</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Alta por bloques</strong>
                            <span>Hub, identidad, ubicacion y acceso en ese orden.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Desvinculacion contenida</strong>
                            <span>La accion critica queda separada de la lectura diaria.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Escaneo rapido</strong>
                            <span>Busqueda y filtros para leer la red antes de actuar.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Enlaces activos</p>
                <p class="sa-stat-value">{{ (int) ($kpis['total_links'] ?? 0) }}</p>
                <p class="sa-stat-meta">Relaciones vigentes entre sede principal y sucursal.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Sedes hub</p>
                <p class="sa-stat-value">{{ (int) ($kpis['total_hubs'] ?? $hubCount) }}</p>
                <p class="sa-stat-meta">Clientes habilitados para operar bajo esquema multisucursal.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Sucursales vinculadas</p>
                <p class="sa-stat-value">{{ $branchCount }}</p>
                <p class="sa-stat-meta">Sedes secundarias bajo administracion global.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Promedio por hub</p>
                <p class="sa-stat-value">{{ number_format($averageBranchesPerHub, 1) }}</p>
                <p class="sa-stat-meta">Referencia rapida para detectar hubs concentrados.</p>
            </article>
        </section>

        <section class="grid gap-6 2xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)]">
            <div id="branch-create-form" class="ui-card sa-form-card space-y-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="sa-section-title">Crear nueva sucursal</h3>
                        <p class="sa-section-copy">
                            Alta guiada para una sede aislada con usuario propio y vinculo directo a una sede principal.
                        </p>
                    </div>
                    <span class="sa-pill {{ $canCreateBranch ? 'is-success' : 'is-warning' }}">
                        {{ $canCreateBranch ? 'Flujo listo para crear' : 'Faltan hubs o planes disponibles' }}
                    </span>
                </div>

                @if ($errors->any())
                    <div class="ui-alert ui-alert-danger" role="alert" aria-labelledby="branch-form-errors-title">
                        <p id="branch-form-errors-title" class="font-semibold">Hay errores en el formulario de sucursal.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! $canCreateBranch)
                    <div class="ui-alert ui-alert-warning">
                        Necesitas al menos una sede principal con plan multisucursal y opciones de plan para sucursal antes de registrar nuevas sedes.
                    </div>
                @endif

                <form method="POST" action="{{ route('superadmin.branches.store') }}" class="sa-form-workbench space-y-6" aria-describedby="branch-form-help">
                    @csrf

                    <p id="branch-form-help" class="sr-only">
                        Formulario guiado para crear una sucursal, definir ubicación, credenciales administrativas y vincularla a una sede principal.
                    </p>

                    <section class="sa-form-section rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-950/50">
                        <div>
                            <h4 class="sa-section-title">1. Vinculación operativa</h4>
                            <p class="sa-section-copy">Selecciona la sede madre correcta y el plan operativo.</p>
                        </div>
                        <div class="grid gap-4 lg:grid-cols-2">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Sede principal (plan sucursales)
                                <select name="hub_gym_id" class="ui-input" required @disabled(! $canCreateBranch)>
                                    <option value="">Selecciona sede principal</option>
                                    @foreach ($hubGyms as $hubGym)
                                        <option value="{{ (int) $hubGym->id }}" @selected((int) old('hub_gym_id') === (int) $hubGym->id)>
                                            {{ (string) $hubGym->name }} | {{ (string) $hubGym->slug }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hub_gym_id')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Plan operativo de sucursal
                                <select name="branch_plan_key" class="ui-input" required @disabled(! $canCreateBranch)>
                                    <option value="">Selecciona plan</option>
                                    @foreach ($branchPlanOptions as $planKey => $planLabel)
                                        <option value="{{ $planKey }}" @selected((string) old('branch_plan_key') === (string) $planKey)>
                                            {{ $planLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_plan_key')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </section>

                    <section class="sa-form-section rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/70">
                        <div>
                            <h4 class="sa-section-title">2. Identidad y ubicación de la sucursal</h4>
                            <p class="sa-section-copy">Define nombre visible, telefono y direccion operativa.</p>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-2">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Nombre de la sucursal
                                <input type="text" id="branch-name" name="branch_name" value="{{ old('branch_name') }}" class="ui-input" placeholder="Ej: Sucursal Norte" required @disabled(! $canCreateBranch)>
                                @error('branch_name')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Teléfono de sucursal
                                <input type="text" name="branch_phone" value="{{ old('branch_phone') }}" class="ui-input" placeholder="Opcional" @disabled(! $canCreateBranch)>
                                @error('branch_phone')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-4">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                País
                                <select id="branch-country" name="branch_country" class="ui-input" required @disabled(! $canCreateBranch)>
                                    @foreach ($locationCatalog as $countryCode => $countryMeta)
                                        <option value="{{ $countryCode }}" @selected($defaultBranchCountry === $countryCode)>
                                            {{ $countryMeta['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_country')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Provincia / estado
                                <select id="branch-state" name="branch_state" class="ui-input" required @disabled(! $canCreateBranch)>
                                    <option value="">Selecciona provincia/estado</option>
                                    @foreach (array_keys($statesForCountry) as $stateName)
                                        <option value="{{ $stateName }}" @selected($defaultBranchState === $stateName)>{{ $stateName }}</option>
                                    @endforeach
                                </select>
                                @error('branch_state')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Ciudad
                                <select id="branch-city" name="branch_city" class="ui-input" required @disabled(! $canCreateBranch)>
                                    <option value="">Selecciona ciudad</option>
                                    @foreach ($citiesForState as $cityName)
                                        <option value="{{ $cityName }}" @selected($defaultBranchCity === $cityName)>{{ $cityName }}</option>
                                    @endforeach
                                </select>
                                @error('branch_city')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Dirección (línea)
                                <input type="text" name="branch_address_line" value="{{ old('branch_address_line') }}" class="ui-input" placeholder="Barrio, avenida, referencia" @disabled(! $canCreateBranch)>
                                @error('branch_address_line')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </section>

                    <section class="sa-form-section rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-950/50">
                        <div>
                            <h4 class="sa-section-title">3. Acceso administrativo de la sucursal</h4>
                            <p class="sa-section-copy">Crea un acceso separado para la sede. El correo se genera con el nombre del hub y la sucursal.</p>
                        </div>

                        <div class="grid gap-4 lg:grid-cols-2">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Nombre de usuario
                                <input type="text" name="branch_admin_name" value="{{ old('branch_admin_name') }}" class="ui-input" placeholder="Ej: Admin Norte" required @disabled(! $canCreateBranch)>
                                @error('branch_admin_name')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Correo login (autogenerado)
                                <input type="text" id="branch-admin-email-preview" value="{{ old('branch_admin_email') }}" class="ui-input" readonly>
                                <input type="hidden" id="branch-admin-email" name="branch_admin_email" value="{{ old('branch_admin_email') }}">
                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Se compone con el nombre del gimnasio y el nombre de la sucursal.</span>
                                @error('branch_admin_email')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Contraseña
                                <input type="password" name="branch_admin_password" class="ui-input" placeholder="Mínimo 8 caracteres" required @disabled(! $canCreateBranch)>
                                @error('branch_admin_password')
                                    <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Confirmar contraseña
                                <input type="password" name="branch_admin_password_confirmation" class="ui-input" placeholder="Repite la contraseña" required @disabled(! $canCreateBranch)>
                            </label>
                        </div>
                    </section>

                    <div class="flex flex-col gap-4 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 dark:border-amber-500/40 dark:bg-amber-900/20">
                        <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                            La caja de cada sucursal secundaria queda gestionada por el Admin global de la sede principal.
                        </p>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="text-xs font-semibold uppercase tracking-wide text-amber-800 dark:text-amber-200">Valida hub, slug y correo antes de crear.</span>
                            <x-ui.button type="submit" class="min-w-[240px]" :disabled="! $canCreateBranch">Crear sucursal y vincular</x-ui.button>
                        </div>
                    </div>
                </form>
            </div>

            <aside class="space-y-4 2xl:sticky 2xl:top-6">
                <div class="ui-card sa-form-review space-y-4">
                    <div>
                        <h3 class="sa-section-title">Checklist antes de crear</h3>
                        <p class="sa-section-copy">Validaciones cortas para evitar retrabajo.</p>
                    </div>
                    <ul class="sa-check-list">
                        <li>Confirma que la sede principal sea la correcta.</li>
                        <li>Usa un nombre de sucursal claro y consistente.</li>
                        <li>Verifica provincia y ciudad antes de guardar.</li>
                        <li>Revisa el correo autogenerado antes de entregar acceso.</li>
                    </ul>
                </div>

                <div class="ui-card sa-form-review space-y-4">
                    <div>
                        <h3 class="sa-section-title">Lo que produce este flujo</h3>
                        <p class="sa-section-copy">Resultado esperado despues del alta.</p>
                    </div>
                    <div class="sa-mini-grid md:grid-cols-1">
                        <div class="sa-mini-card">
                            <strong>Sucursal aislada</strong>
                            <span>Opera con identidad propia bajo la relacion administrativa del hub.</span>
                        </div>
                        <div class="sa-mini-card">
                            <strong>Acceso listo</strong>
                            <span>Se crea un admin con credenciales independientes.</span>
                        </div>
                        <div class="sa-mini-card">
                            <strong>Gobierno claro</strong>
                            <span>La politica de caja queda definida desde la creacion.</span>
                        </div>
                    </div>
                </div>
            </aside>
        </section>

        <section id="branch-links-section" class="space-y-4">
            <div class="sa-toolbar">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h3 class="sa-section-title">Vínculos actuales</h3>
                        <p class="sa-section-copy">Busca por sede principal, sucursal, slug o ubicacion. Filtra por plan o estado antes de tocar acciones criticas.</p>
                    </div>
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                        <label class="sa-search">
                            <span class="sr-only">Buscar vínculos por sede, sucursal o ubicación</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-slate-400">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.63 3.631a.75.75 0 1 0 1.06-1.06l-3.63-3.632A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0a4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                            </svg>
                            <input id="branch-link-search" type="search" placeholder="Buscar sede, slug o ubicación" aria-label="Buscar vínculos" aria-controls="branch-links-table-body">
                        </label>

                        <select id="branch-link-plan-filter" class="ui-input lg:w-[180px]" aria-label="Filtrar vínculos por plan">
                            <option value="">Todos los planes</option>
                            @foreach ($branchPlanOptions as $planKey => $planLabel)
                                <option value="{{ strtolower((string) $planKey) }}">{{ $planLabel }}</option>
                            @endforeach
                        </select>

                        <select id="branch-link-status-filter" class="ui-input lg:w-[180px]" aria-label="Filtrar vínculos por estado">
                            <option value="">Todos los estados</option>
                            @foreach ($linkStatuses as $status)
                                <option value="{{ strtolower($status) }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>

                        <button type="button" id="branch-link-clear" class="ui-button ui-button-ghost">Limpiar filtros</button>
                        <span id="branch-link-count" class="sa-pill is-neutral" role="status" aria-live="polite">{{ $visibleLinksCount }} resultados</span>
                    </div>
                </div>
            </div>

            <div class="ui-card sa-form-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="ui-table min-w-[1220px]" aria-describedby="branch-links-help">
                        <caption id="branch-links-help" class="sr-only">
                            Tabla de vínculos multisucursal con filtros por texto, plan y estado.
                        </caption>
                        <thead>
                            <tr>
                                <th>Sede principal</th>
                                <th>Sucursal</th>
                                <th>Plan sucursal</th>
                                <th>Caja</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="branch-links-table-body">
                            @forelse ($links as $link)
                                @php
                                    $branchPlanKey = strtolower((string) ($link->branch_plan_key ?? 'basico'));
                                    $branchPlanLabel = match ($branchPlanKey) {
                                        'basico' => 'Básico',
                                        'profesional' => 'Profesional',
                                        'premium' => 'Premium',
                                        default => ucfirst($branchPlanKey),
                                    };
                                    $statusValue = strtolower((string) ($link->status ?? 'active'));
                                    $rowSearch = trim(implode(' ', array_filter([
                                        (string) ($link->hubGym?->name ?? ''),
                                        (string) ($link->hubGym?->slug ?? ''),
                                        $formatAddress($link->hubGym),
                                        (string) ($link->branchGym?->name ?? ''),
                                        (string) ($link->branchGym?->slug ?? ''),
                                        $formatAddress($link->branchGym),
                                        (string) ($link->createdBy?->name ?? ''),
                                        (string) ($link->createdBy?->email ?? ''),
                                    ])));
                                @endphp
                                <tr data-branch-row data-search="{{ $rowSearch }}" data-plan="{{ $branchPlanKey }}" data-status="{{ $statusValue }}">
                                    <td>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">{{ (string) ($link->hubGym?->name ?? '-') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ (string) ($link->hubGym?->slug ?? '-') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ $formatAddress($link->hubGym) }}</p>
                                    </td>
                                    <td>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">{{ (string) ($link->branchGym?->name ?? '-') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ (string) ($link->branchGym?->slug ?? '-') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ $formatAddress($link->branchGym) }}</p>
                                    </td>
                                    <td>
                                        <span class="sa-branch-pill sa-branch-pill-plan">{{ $branchPlanLabel }}</span>
                                    </td>
                                    <td>
                                        <span class="sa-branch-pill {{ (bool) ($link->cash_managed_by_hub ?? true) ? 'sa-branch-pill-cash-hub' : 'sa-branch-pill-cash-local' }}">
                                            {{ (bool) ($link->cash_managed_by_hub ?? true) ? 'Control hub' : 'Caja local' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="sa-branch-pill {{ $statusValue === 'active' ? 'sa-branch-pill-status-active' : 'sa-branch-pill-status-other' }}">
                                            {{ $statusValue }}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="font-semibold text-slate-800 dark:text-slate-100">{{ (string) ($link->createdBy?->name ?? 'Sin usuario') }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ (string) ($link->createdBy?->email ?? '-') }}</p>
                                    </td>
                                    <td class="text-slate-700 dark:text-slate-200">
                                        {{ optional($link->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td>
                                        <details class="sa-disclosure min-w-[220px]">
                                            <summary>
                                                Gestionar vínculo
                                                <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Desvincular</span>
                                            </summary>
                                            <div class="space-y-3 px-4 py-4">
                                                <p class="text-xs leading-5 text-slate-600 dark:text-slate-300">
                                                    Usa esta accion solo si la sucursal ya no debe operar bajo este hub.
                                                </p>
                                                <form method="POST" action="{{ route('superadmin.branches.destroy', (int) $link->id) }}" onsubmit="return confirm('Esta acción desvinculara la sucursal del hub actual. Deseas continuar?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" size="sm" variant="danger" class="w-full justify-center">Desvincular sucursal</x-ui.button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                        Aún no hay vínculos configurados.
                                    </td>
                                </tr>
                            @endforelse
                            @if ($visibleLinksCount > 0)
                                <tr id="branch-links-empty-filter" hidden>
                                    <td colspan="8" class="px-3 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                        Ningún vínculo coincide con los filtros actuales.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if (method_exists($links, 'links'))
                    <div class="mt-4">
                        {{ $links->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const locationCatalog = @json($locationCatalog);
        const hubDomains = @json($hubGymAdminDomains);
        const countrySelect = document.getElementById('branch-country');
        const stateSelect = document.getElementById('branch-state');
        const citySelect = document.getElementById('branch-city');
        const hubSelect = document.querySelector('select[name="hub_gym_id"]');
        const branchNameInput = document.getElementById('branch-name');
        const branchEmailInput = document.getElementById('branch-admin-email');
        const branchEmailPreview = document.getElementById('branch-admin-email-preview');
        const searchInput = document.getElementById('branch-link-search');
        const planFilter = document.getElementById('branch-link-plan-filter');
        const statusFilter = document.getElementById('branch-link-status-filter');
        const clearFiltersButton = document.getElementById('branch-link-clear');
        const resultCount = document.getElementById('branch-link-count');
        const filterEmptyState = document.getElementById('branch-links-empty-filter');
        const branchRows = Array.from(document.querySelectorAll('[data-branch-row]'));

        const selectedState = @json($defaultBranchState);
        const selectedCity = @json($defaultBranchCity);

        function sanitizeToken(value, fallback) {
            const raw = String(value || '').trim();
            const normalized = raw.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            const slug = normalized.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            const compact = slug.replace(/-/g, '');
            return compact !== '' ? compact : fallback;
        }

        function normalizeText(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        }

        function resolveHubName() {
            if (!hubSelect) return '';
            const option = hubSelect.options[hubSelect.selectedIndex];
            if (!option) return '';
            const label = String(option.textContent || '');
            return label.split('|')[0]?.trim() || '';
        }

        function resolveDomain() {
            if (!hubSelect) return 'gymsystem.app';
            const gymId = String(hubSelect.value || '').trim();
            if (gymId === '') return 'gymsystem.app';
            const fromMap = String(hubDomains[gymId] || '').trim().toLowerCase();
            return fromMap !== '' ? fromMap : 'gymsystem.app';
        }

        function syncBranchLoginEmail() {
            if (!branchEmailInput || !branchEmailPreview) return;
            const gymToken = sanitizeToken(resolveHubName(), 'gym');
            const branchToken = sanitizeToken(branchNameInput?.value || '', 'sucursal');
            const domain = resolveDomain();
            const email = `${gymToken}.${branchToken}@${domain}`;
            branchEmailInput.value = email;
            branchEmailPreview.value = email;
        }

        function replaceOptions(select, values, placeholder) {
            if (!select) return;
            select.innerHTML = '';

            const first = document.createElement('option');
            first.value = '';
            first.textContent = placeholder;
            select.appendChild(first);

            values.forEach(function (value) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });
        }

        function statesForCountry(countryCode) {
            const country = locationCatalog[String(countryCode || '').toLowerCase()] || null;
            if (!country || !country.states) return [];
            return Object.keys(country.states);
        }

        function citiesForState(countryCode, stateName) {
            const country = locationCatalog[String(countryCode || '').toLowerCase()] || null;
            if (!country || !country.states || !country.states[stateName]) return [];
            return country.states[stateName];
        }

        function syncStates(preferredState, preferredCity) {
            if (!countrySelect || !stateSelect) return;
            const states = statesForCountry(countrySelect.value);
            replaceOptions(stateSelect, states, 'Selecciona provincia/estado');

            if (preferredState && states.includes(preferredState)) {
                stateSelect.value = preferredState;
            }

            syncCities(preferredCity);
        }

        function syncCities(preferredCity) {
            if (!countrySelect || !stateSelect || !citySelect) return;
            const cities = citiesForState(countrySelect.value, stateSelect.value);
            replaceOptions(citySelect, cities, 'Selecciona ciudad');

            if (preferredCity && cities.includes(preferredCity)) {
                citySelect.value = preferredCity;
            }
        }

        function applyTableFilters() {
            if (branchRows.length === 0) {
                return;
            }

            const query = normalizeText(searchInput?.value || '');
            const selectedPlan = normalizeText(planFilter?.value || '');
            const selectedStatus = normalizeText(statusFilter?.value || '');
            let visible = 0;

            branchRows.forEach(function (row) {
                const searchValue = normalizeText(row.getAttribute('data-search') || '');
                const rowPlan = normalizeText(row.getAttribute('data-plan') || '');
                const rowStatus = normalizeText(row.getAttribute('data-status') || '');
                const matchesQuery = query === '' || searchValue.includes(query);
                const matchesPlan = selectedPlan === '' || rowPlan === selectedPlan;
                const matchesStatus = selectedStatus === '' || rowStatus === selectedStatus;
                const shouldShow = matchesQuery && matchesPlan && matchesStatus;

                row.hidden = !shouldShow;
                if (shouldShow) {
                    visible += 1;
                }
            });

            if (resultCount) {
                resultCount.textContent = `${visible} resultado${visible === 1 ? '' : 's'}`;
            }

            if (filterEmptyState) {
                filterEmptyState.hidden = visible !== 0;
            }
        }

        countrySelect?.addEventListener('change', function () {
            syncStates('', '');
        });

        stateSelect?.addEventListener('change', function () {
            syncCities('');
        });

        hubSelect?.addEventListener('change', syncBranchLoginEmail);
        branchNameInput?.addEventListener('input', syncBranchLoginEmail);
        searchInput?.addEventListener('input', applyTableFilters);
        planFilter?.addEventListener('change', applyTableFilters);
        statusFilter?.addEventListener('change', applyTableFilters);
        clearFiltersButton?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (planFilter) planFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            applyTableFilters();
            searchInput?.focus();
        });

        syncStates(selectedState, selectedCity);
        syncBranchLoginEmail();
        applyTableFilters();
    })();
</script>
@endpush
