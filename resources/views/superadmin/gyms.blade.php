@extends('layouts.panel')

@section('title', 'SuperAdmin Gimnasios')
@section('page-title', 'Gimnasios y Suscripciones')

@push('styles')
<style>
    .theme-dark [data-branches-toggle] {
        background-color: rgba(15, 23, 42, 0.92);
        border-color: rgba(148, 163, 184, 0.45);
        color: rgb(226 232 240);
    }

    .theme-dark [data-branches-toggle]:hover {
        background-color: rgba(30, 41, 59, 0.98);
        border-color: rgba(56, 189, 248, 0.55);
        color: rgb(248 250 252);
    }

    .theme-dark [data-branches-toggle][aria-expanded='true'] {
        background-color: rgba(8, 47, 73, 0.65);
        border-color: rgba(34, 211, 238, 0.55);
        color: rgb(165 243 252);
    }

    .superadmin-gym-table details[open] [data-renew-summary-icon] {
        transform: rotate(180deg);
    }
</style>
@endpush

@section('content')
    @php
        $today = \Carbon\Carbon::today();
        $expiringLimit = $today->copy()->addDays(7);
        $managedBranchesByHub = $gyms
            ->filter(fn ($row) => (bool) ($row->is_branch_managed ?? false) && (int) ($row->billing_owner_gym_id ?? 0) > 0)
            ->groupBy(fn ($row) => (int) $row->billing_owner_gym_id);
        $mainGyms = $gyms
            ->reject(fn ($row) => (bool) ($row->is_branch_managed ?? false))
            ->values();
        $totalGyms = $mainGyms->count();
        $activeGyms = $mainGyms->where('status', 'active')->count();
        $graceGyms = $mainGyms->where('status', 'grace')->count();
        $suspendedGyms = $mainGyms->where('status', 'suspended')->count();
        $multiSiteGyms = $mainGyms->filter(function ($row) use ($managedBranchesByHub) {
            $planName = mb_strtolower((string) ($row->plan_name ?? ''));
            $branchCount = $managedBranchesByHub->get((int) ($row->gym_id ?? 0), collect())->count();

            return str_contains($planName, 'sucursal') || $branchCount > 0;
        })->count();
        $expiringSoon = $mainGyms->filter(function ($row) use ($today, $expiringLimit) {
            if (($row->status ?? null) === 'suspended' || empty($row->ends_at)) {
                return false;
            }

            return \Carbon\Carbon::parse($row->ends_at)->betweenIncluded($today, $expiringLimit);
        })->count();
        $estimatedMrr = $mainGyms
            ->filter(fn ($row) => in_array((string) ($row->status ?? ''), ['active', 'grace'], true))
            ->sum(fn ($row) => (float) ($row->price ?? 0));
        $attentionPreview = $mainGyms
            ->filter(function ($row) use ($today, $expiringLimit) {
                if (($row->status ?? null) === 'grace' || ($row->status ?? null) === 'suspended') {
                    return true;
                }

                if (($row->status ?? null) !== 'active' || empty($row->ends_at)) {
                    return false;
                }

                return \Carbon\Carbon::parse($row->ends_at)->betweenIncluded($today, $expiringLimit);
            })
            ->sortBy(function ($row) {
                return \Carbon\Carbon::parse($row->ends_at)->timestamp;
            })
            ->take(4)
            ->values();
        $portfolioHealth = $totalGyms > 0 ? (int) round(($activeGyms / $totalGyms) * 100) : 0;
        $planPromotionRules = is_array($planPromotionRules ?? null) ? $planPromotionRules : [];
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Cartera global</span>
                    <h2 class="sa-title">Administra renovaciones y riesgo sin perder contexto por gimnasio.</h2>
                    <p class="sa-subtitle">
                        La vista se reorganizo para priorizar: lectura de salud comercial, filtros rapidos,
                        detalle claro por gimnasio y formularios de renovación solo cuando realmente los necesitas.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.gym.index')">Crear gimnasio</x-ui.button>
                        <x-ui.button :href="route('superadmin.gym-list.index')" variant="secondary">Editar admins</x-ui.button>
                        <x-ui.button :href="route('superadmin.plan-templates.index')" variant="ghost">Revisar planes</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Atencion inmediata</p>
                    <div class="sa-note-list">
                        @forelse ($attentionPreview as $preview)
                            @php
                                $previewEnd = ! empty($preview->ends_at) ? \Carbon\Carbon::parse($preview->ends_at) : null;
                                $previewStatus = (string) ($preview->status ?? '');
                                $previewMessage = match ($previewStatus) {
                                    'grace' => ((int) ($preview->grace_left ?? 0)).' días de gracia restantes',
                                    'suspended' => 'Suscripción suspendida',
                                    default => $previewEnd ? 'Vence '.$previewEnd->toDateString() : 'Requiere revisión',
                                };
                            @endphp
                            <div class="sa-note-item">
                                <strong>{{ (string) ($preview->gym_name ?? 'Gimnasio') }}</strong>
                                <span>{{ (string) ($preview->plan_name ?? 'Sin plan') }} | {{ $previewMessage }}</span>
                            </div>
                        @empty
                            <div class="sa-note-item">
                                <strong>Sin alertas críticas hoy</strong>
                                <span>La cartera no tiene vencimientos cercanos ni estados de gracia visibles.</span>
                            </div>
                        @endforelse
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Gimnasios visibles</p>
                <p class="sa-stat-value">{{ $totalGyms }}</p>
                <p class="sa-stat-meta">Solo sedes principales e independientes para mantener la lectura limpia.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value">{{ $activeGyms }}</p>
                <p class="sa-stat-meta">{{ $portfolioHealth }}% de la cartera está operando sin alertas.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">En gracia</p>
                <p class="sa-stat-value">{{ $graceGyms }}</p>
                <p class="sa-stat-meta">Seguimiento comercial prioritario para evitar suspensiones.</p>
            </article>
            <article class="sa-stat-card is-danger">
                <p class="sa-stat-label">Suspendidos</p>
                <p class="sa-stat-value">{{ $suspendedGyms }}</p>
                <p class="sa-stat-meta">Casos que necesitan reactivación o cierre del ciclo comercial.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">MRR estimado</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format((float) $estimatedMrr, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">Ingreso mensual proyectado sobre cartera activa o en gracia.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Vencen en 7 días</p>
                <p class="sa-stat-value">{{ $expiringSoon }}</p>
                <p class="sa-stat-meta">Renovaciones que ya requieren una acción visible en la UI.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Operación multisede</p>
                <p class="sa-stat-value">{{ $multiSiteGyms }}</p>
                <p class="sa-stat-meta">Gimnasios con estructura de sucursales o plan multi sede.</p>
            </article>
        </section>

        <x-ui.card title="Gestión de suscripciones" subtitle="Menos columnas, mejor jerarquia visual y acciones agrupadas por contexto.">
            @if ($errors->has('subscription'))
                <div class="ui-alert ui-alert-warning mb-4 text-sm font-semibold">
                    {{ $errors->first('subscription') }}
                </div>
            @endif
            @if ($errors->has('custom_price'))
                <div class="ui-alert ui-alert-warning mb-4 text-sm font-semibold">
                    {{ $errors->first('custom_price') }}
                </div>
            @endif
            <div class="sa-toolbar mb-4">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                    <div class="grid flex-1 gap-3 md:grid-cols-[minmax(0,1.4fr)_190px_190px]">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Buscar gimnasio o plan
                            <span class="sa-search">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <input id="gym-table-filter" type="text" placeholder="Nombre, plan, sede o forma de pago">
                            </span>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Estado
                            <select id="gym-status-filter" class="ui-input">
                                <option value="all">Todos</option>
                                <option value="active">Activos</option>
                                <option value="grace">En gracia</option>
                                <option value="suspended">Suspendidos</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Modelo
                            <select id="gym-model-filter" class="ui-input">
                                <option value="all">Todos</option>
                                <option value="single">Una sede</option>
                                <option value="multi">Multisede</option>
                            </select>
                        </label>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 xl:justify-end">
                        <span class="sa-pill is-warning">Riesgo: {{ $graceGyms + $expiringSoon + $suspendedGyms }}</span>
                        <span class="sa-pill is-info" role="status" aria-live="polite">Visibles: <strong id="gym-visible-count">{{ $totalGyms }}</strong></span>
                        <button type="button" id="gym-filter-clear" class="ui-button ui-button-ghost">Limpiar filtros</button>
                    </div>
                </div>
                <p id="gym-filter-help" class="sa-filter-note mt-3">
                    Los filtros se aplican en tiempo real. Si una fila deja de coincidir, también se cierran sus paneles abiertos para evitar acciones fuera de contexto.
                </p>
            </div>

            <div class="overflow-x-auto superadmin-gym-table">
                <table class="ui-table min-w-[1100px]" aria-describedby="gym-filter-help gym-table-help">
                    <caption id="gym-table-help" class="sr-only">
                        Tabla de gimnasios con filtros por texto, estado y modelo operativo.
                    </caption>
                    <thead>
                        <tr>
                            <th>Gimnasio</th>
                            <th>Salud comercial</th>
                            <th>Facturación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mainGyms as $gym)
                            @php
                                $gymName = (string) ($gym->gym_name ?? '-');
                                $planName = (string) ($gym->plan_name ?? '-');
                                $linkedManagedBranches = $managedBranchesByHub->get((int) $gym->gym_id, collect());
                                $linkedBranchCount = $linkedManagedBranches->count();
                                $isMultiBranchPlan = str_contains(mb_strtolower($planName), 'sucursal') || $linkedBranchCount > 0;
                                $rowModel = $isMultiBranchPlan ? 'multi' : 'single';
                                $rowSearch = strtolower(trim(implode(' ', array_filter([
                                    $gymName,
                                    $planName,
                                    $gym->last_payment_method,
                                    $linkedManagedBranches->map(fn ($branch) => (string) ($branch->gym_name ?? ''))->implode(' '),
                                ]))));
                                $statusClasses = [
                                    'active' => 'ui-badge ui-badge-success',
                                    'grace' => 'ui-badge ui-badge-warning',
                                    'suspended' => 'ui-badge ui-badge-danger',
                                ];
                                $badgeClass = $statusClasses[$gym->status] ?? 'ui-badge ui-badge-muted';
                                $endDate = \Carbon\Carbon::parse($gym->ends_at);
                                $lastPaymentLabel = match ($gym->last_payment_method) {
                                    'cash' => 'Efectivo',
                                    'card' => 'Tarjeta',
                                    'transfer', 'transferencia' => 'Transferencia',
                                    null => 'Sin registro',
                                    default => (string) $gym->last_payment_method,
                                };
                                $healthMessage = match ((string) $gym->status) {
                                    'active' => ((int) ($gym->days_left ?? 0)).' días restantes',
                                    'grace' => ((int) ($gym->grace_left ?? 0)).' días de gracia',
                                    'suspended' => 'Sin acceso activo',
                                    default => 'Revisar estado',
                                };
                            @endphp
                            <tr
                                data-gym-row
                                data-gym-id="{{ (int) $gym->gym_id }}"
                                data-gym-search="{{ $rowSearch }}"
                                data-gym-status="{{ (string) ($gym->status ?? '') }}"
                                data-gym-model="{{ $rowModel }}"
                            >
                                <td>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $gymName }}</p>
                                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                                <span class="sa-pill is-neutral">{{ $planName }}</span>
                                                <span class="sa-pill {{ $isMultiBranchPlan ? 'is-info' : 'is-neutral' }}">
                                                    {{ $isMultiBranchPlan ? 'Multisede' : 'Una sede' }}
                                                </span>
                                                @if ($isMultiBranchPlan)
                                                    <button type="button"
                                                            data-branches-toggle="{{ (int) $gym->gym_id }}"
                                                            aria-expanded="false"
                                                            class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-800 transition hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                                                        <span>Sucursales ({{ $linkedBranchCount }})</span>
                                                        <svg data-branches-caret="{{ (int) $gym->gym_id }}" class="h-3.5 w-3.5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <p class="text-xs leading-5 text-slate-600 dark:text-slate-300">
                                            {{ $isMultiBranchPlan
                                                ? ($linkedBranchCount > 0 ? $linkedBranchCount.' sucursal(es) vinculadas y gestión centralizada.' : 'Plan multi sede activo, todavía sin sucursales vinculadas.')
                                                : 'Operación independiente con una sola sede.' }}
                                        </p>
                                    </div>
                                </td>

                                <td>
                                    <div class="space-y-3">
                                        <span class="{{ $badgeClass }}">
                                            {{ match ($gym->status) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', default => $gym->status } }}
                                        </span>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-sm dark:border-slate-800 dark:bg-slate-950/50">
                                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $healthMessage }}</p>
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                                                Fecha fin: {{ $endDate->toDateString() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="space-y-3 text-sm">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Mensualidad</p>
                                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                                {{ \App\Support\Currency::format((float) $gym->price, $appCurrencyCode) }}
                                            </p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">último pago</p>
                                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $lastPaymentLabel }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="w-[360px]">
                                    <div class="space-y-3">
                                        <details class="sa-disclosure" data-renew-panel>
                                            <summary>
                                                <span>Renovar o cambiar plan</span>
                                                <svg data-renew-summary-icon class="h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                                </svg>
                                            </summary>
                                            <div class="space-y-3 p-4">
                                                <p class="text-xs leading-5 text-slate-600 dark:text-slate-300">
                                                    Abre este panel solo cuando necesites renovar o migrar de plan. Así evitamos una tabla saturada.
                                                </p>
                                                @php
                                                    $currentPlanTemplate = collect($planTemplates ?? collect())->first(function ($template) use ($gym) {
                                                        $templatePlanKey = strtolower(trim((string) ($template->plan_key ?? '')));
                                                        $gymPlanKey = strtolower(trim((string) ($gym->plan_key ?? '')));

                                                        if ($gym->plan_template_id !== null && (int) $gym->plan_template_id > 0) {
                                                            return (int) $template->id === (int) $gym->plan_template_id;
                                                        }

                                                        return $gymPlanKey !== '' && $templatePlanKey === $gymPlanKey;
                                                    });
                                                @endphp
                                                <form method="POST"
                                                      action="{{ route('superadmin.subscriptions.renew', $gym->gym_id) }}"
                                                      class="grid gap-3"
                                                      data-current-plan-template-id="{{ (int) ($currentPlanTemplate->id ?? 0) }}"
                                                      data-current-plan-name="{{ (string) ($currentPlanTemplate->name ?? $gym->plan_name ?? 'Plan actual') }}"
                                                      data-current-plan-key="{{ strtolower(trim((string) ($currentPlanTemplate->plan_key ?? $gym->plan_key ?? ''))) }}"
                                                      data-current-plan-price="{{ number_format((float) ($currentPlanTemplate->price ?? $gym->price ?? 0), 2, '.', '') }}">
                                                    @csrf
                                                    <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                        Plantilla de plan
                                                        <select name="plan_template_id" class="ui-input js-plan-template-select">
                                                            <option value="">Mantener plan actual</option>
                                                            @foreach (($planTemplates ?? collect()) as $template)
                                                                @php
                                                                    $templateFeaturePlanKey = method_exists($template, 'resolvedFeaturePlanKey')
                                                                        ? (string) $template->resolvedFeaturePlanKey()
                                                                        : (string) ($template->feature_plan_key ?? $template->plan_key ?? 'basico');
                                                                @endphp
                                                                <option value="{{ $template->id }}"
                                                                        data-plan-template-id="{{ (int) $template->id }}"
                                                                        data-plan-name="{{ $template->name }}"
                                                                        data-feature-plan-key="{{ $templateFeaturePlanKey }}"
                                                                        data-plan-price="{{ number_format((float) $template->price, 2, '.', '') }}">
                                                                    {{ $template->name }} ({{ \App\Support\PlanDuration::label($template->duration_unit, (int) $template->duration_days, $template->duration_months) }}) - {{ \App\Support\Currency::format((float) $template->price, $appCurrencyCode) }}{{ $template->discount_price !== null ? ' | Desc. '.\App\Support\Currency::format((float) $template->discount_price, $appCurrencyCode) : '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </label>

                                                    <div class="grid gap-3 md:grid-cols-2">
                                                        <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                            Precio personalizado
                                                            <input type="number"
                                                                   name="custom_price"
                                                                   step="0.01"
                                                                   min="0"
                                                                   class="ui-input js-custom-price-input"
                                                                   placeholder="Solo plan sucursales"
                                                                   title="Disponible cuando eliges plan sucursales."
                                                                   disabled>
                                                        </label>

                                                        <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                            Método de pago
                                                            <select name="payment_method" class="ui-input" required>
                                                                @foreach ($paymentMethods as $method)
                                                                    <option value="{{ $method }}">{{ match ($method) { 'cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia', 'transferencia' => 'Transferencia', default => $method } }}</option>
                                                                @endforeach
                                                            </select>
                                                        </label>
                                                    </div>

                                                    <div class="grid gap-3 md:grid-cols-2 md:items-end">
                                                        <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                            Cobertura prepaga
                                                            <select name="months" class="ui-input js-months-select" required>
                                                                @for ($monthOption = 1; $monthOption <= 12; $monthOption++)
                                                                    <option value="{{ $monthOption }}">{{ $monthOption }} {{ $monthOption === 1 ? 'mes' : 'meses' }}</option>
                                                                @endfor
                                                            </select>
                                                        </label>

                                                    </div>

                                                    <p class="text-xs leading-5 text-slate-600 dark:text-slate-300 js-renew-plan-feedback" role="status" aria-live="polite"></p>

                                                    <div class="flex justify-end">
                                                        <x-ui.button type="submit" size="sm">Aplicar renovación</x-ui.button>
                                                    </div>
                                                </form>
                                            </div>
                                        </details>

                                        <form method="POST"
                                              action="{{ route('superadmin.subscriptions.suspend', $gym->gym_id) }}"
                                              onsubmit="return confirm('Esta acción suspenderá la suscripción y el acceso del gimnasio. ¿Deseas continuar?');"
                                              class="sa-danger-zone">
                                            @csrf
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-bold text-rose-900 dark:text-rose-100">Zona sensible</p>
                                                    <p class="mt-1 text-xs leading-5 text-rose-700 dark:text-rose-200">
                                                        Suspende el acceso solo si ya validaste pago pendiente o corte operativo.
                                                    </p>
                                                </div>
                                                <x-ui.button type="submit" variant="danger" size="sm">Suspender</x-ui.button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @if ($isMultiBranchPlan)
                                <tr data-gym-detail-row data-parent-gym-id="{{ (int) $gym->gym_id }}" class="hidden border-b border-slate-300/70 bg-slate-100/70 dark:border-slate-700 dark:bg-slate-900/40">
                                    <td colspan="4" class="px-4 py-4">
                                        <div class="rounded-2xl border border-slate-300 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                                <h4 class="text-sm font-black uppercase tracking-wide text-slate-900 dark:text-slate-100">
                                                    Sucursales vinculadas de {{ $gymName }}
                                                </h4>
                                                <span class="sa-pill is-info">{{ $linkedBranchCount }} total</span>
                                            </div>

                                            @if ($linkedManagedBranches->isEmpty())
                                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                    Este plan multi sede todavía no tiene sucursales vinculadas.
                                                </div>
                                            @else
                                                <div class="grid gap-3 lg:grid-cols-2 xl:grid-cols-3">
                                                    @foreach ($linkedManagedBranches as $branch)
                                                        @php
                                                            $branchStatus = (string) ($branch->status ?? '');
                                                            $branchBadgeClass = $statusClasses[$branchStatus] ?? 'ui-badge ui-badge-muted';
                                                            $branchEndsAt = \Carbon\Carbon::parse($branch->ends_at);
                                                            $branchDaysLeft = $branchStatus === 'active'
                                                                ? (int) ($branch->days_left ?? max(0, \Carbon\Carbon::today()->diffInDays($branchEndsAt, false)))
                                                                : null;
                                                        @endphp
                                                        <article class="rounded-2xl border border-slate-300 bg-slate-50 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                            <div class="flex items-start justify-between gap-2">
                                                                <p class="text-sm font-bold text-slate-900 dark:text-slate-50">{{ (string) ($branch->gym_name ?? 'Sucursal') }}</p>
                                                                <span class="{{ $branchBadgeClass }}">
                                                                    {{ match ($branchStatus) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', default => ($branchStatus !== '' ? $branchStatus : '-') } }}
                                                                </span>
                                                            </div>
                                                            <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-200">{{ (string) ($branch->plan_name ?? '-') }}</p>
                                                            <div class="mt-3 grid gap-2 text-xs">
                                                                <p class="rounded-xl border border-slate-300 bg-white px-2.5 py-2 text-slate-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                                    <span class="font-semibold">Vence:</span> {{ $branchEndsAt->toDateString() }}
                                                                </p>
                                                                <p class="rounded-xl border border-slate-300 bg-white px-2.5 py-2 text-slate-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                                    <span class="font-semibold">Mensualidad:</span> {{ \App\Support\Currency::format((float) ($branch->price ?? 0), $appCurrencyCode) }}
                                                                </p>
                                                            </div>
                                                            <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                                                                Gestionada por sede principal.
                                                                @if ($branchDaysLeft !== null)
                                                                    Quedan {{ $branchDaysLeft }} días.
                                                                @endif
                                                            </p>
                                                        </article>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="sa-empty-row">
                                    No hay gimnasios registrados.
                                </td>
                            </tr>
                        @endforelse

                        @if ($mainGyms->isNotEmpty())
                            <tr id="gym-empty-state" class="hidden">
                                <td colspan="4" class="sa-empty-row">
                                    No se encontraron gimnasios con ese criterio.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection
@push('scripts')
<script>
    (function () {
        const filterInput = document.getElementById('gym-table-filter');
        const statusFilter = document.getElementById('gym-status-filter');
        const modelFilter = document.getElementById('gym-model-filter');
        const visibleCount = document.getElementById('gym-visible-count');
        const clearButton = document.getElementById('gym-filter-clear');
        const emptyState = document.getElementById('gym-empty-state');
        const rows = Array.from(document.querySelectorAll('tr[data-gym-row]'));
        const detailRowsByGym = new Map();
        const detailRows = Array.from(document.querySelectorAll('tr[data-gym-detail-row]'));

        const normalizeText = function (value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        };

        detailRows.forEach(function (detailRow) {
            const gymId = String(detailRow.getAttribute('data-parent-gym-id') || '');
            if (gymId === '') {
                return;
            }

            if (!detailRowsByGym.has(gymId)) {
                detailRowsByGym.set(gymId, []);
            }

            detailRowsByGym.get(gymId).push(detailRow);
        });

        const setExpandedState = function (gymId, expanded) {
            const toggleButton = document.querySelector('[data-branches-toggle="' + gymId + '"]');
            const caret = document.querySelector('[data-branches-caret="' + gymId + '"]');
            const branchDetailRows = detailRowsByGym.get(String(gymId)) || [];

            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }

            if (caret) {
                caret.classList.toggle('rotate-180', expanded);
            }

            branchDetailRows.forEach(function (detailRow) {
                detailRow.classList.toggle('hidden', !expanded);
            });
        };

        document.querySelectorAll('[data-branches-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const gymId = String(button.getAttribute('data-branches-toggle') || '');
                if (gymId === '') {
                    return;
                }

                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                setExpandedState(gymId, !isExpanded);
            });
        });

        const applyFilter = function () {
            const term = normalizeText(filterInput?.value || '');
            const selectedStatus = String(statusFilter?.value || 'all').trim().toLowerCase();
            const selectedModel = String(modelFilter?.value || 'all').trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                const searchValue = normalizeText(row.getAttribute('data-gym-search') || '');
                const rowStatus = String(row.getAttribute('data-gym-status') || '').toLowerCase();
                const rowModel = String(row.getAttribute('data-gym-model') || '').toLowerCase();
                const matchesSearch = term === '' || searchValue.includes(term);
                const matchesStatus = selectedStatus === 'all' || rowStatus === selectedStatus;
                const matchesModel = selectedModel === 'all' || rowModel === selectedModel;
                const matches = matchesSearch && matchesStatus && matchesModel;
                const gymId = String(row.getAttribute('data-gym-id') || '');

                row.classList.toggle('hidden', !matches);

                if (!matches && gymId !== '') {
                    setExpandedState(gymId, false);
                    const renewDetails = row.querySelector('details[data-renew-panel]');
                    if (renewDetails instanceof HTMLDetailsElement) {
                        renewDetails.open = false;
                    }
                }

                if (matches) {
                    visible += 1;
                }
            });

            if (visibleCount) {
                visibleCount.textContent = String(visible);
            }

            if (emptyState) {
                emptyState.classList.toggle('hidden', visible !== 0);
            }
        };

        filterInput?.addEventListener('input', applyFilter);
        statusFilter?.addEventListener('change', applyFilter);
        modelFilter?.addEventListener('change', applyFilter);
        clearButton?.addEventListener('click', function () {
            if (filterInput) {
                filterInput.value = '';
            }
            if (statusFilter) {
                statusFilter.value = 'all';
            }
            if (modelFilter) {
                modelFilter.value = 'all';
            }
            applyFilter();
            filterInput?.focus();
        });
        applyFilter();

        const promotionRules = @json($planPromotionRules);
        const formatUsd = function (value) {
            const numeric = Number(value);
            if (!Number.isFinite(numeric)) return '$0.00';
            return '$' + numeric.toFixed(2);
        };
        const resolvePromotion = function (templateId, billingCycles) {
            const rules = promotionRules[String(templateId)] || [];
            if (!Array.isArray(rules)) {
                return null;
            }

            let fallbackRule = null;
            for (const rule of rules) {
                const duration = Number(rule?.duration_months || 0);
                if (Number.isFinite(duration) && duration > 0 && duration === billingCycles) {
                    return rule;
                }
                if (!fallbackRule && !rule?.duration_months) {
                    fallbackRule = rule;
                }
            }

            return fallbackRule;
        };
        const resolvePromotionPricing = function (baseMonthlyPrice, billingCycles, promotion) {
            const safeMonthlyPrice = Number.isFinite(baseMonthlyPrice) ? Math.max(0, baseMonthlyPrice) : 0;
            const safeBillingCycles = Math.max(1, Math.round(Number(billingCycles) || 1));
            const baseTotal = Number((safeMonthlyPrice * safeBillingCycles).toFixed(2));

            if (!promotion) {
                return {
                    baseTotal: baseTotal,
                    finalTotal: baseTotal,
                    discountAmount: 0,
                    effectiveMonthlyPrice: Number((baseTotal / safeBillingCycles).toFixed(2)),
                    bonusDays: 0,
                };
            }

            const type = String(promotion.type || '').trim().toLowerCase();
            const value = Number(promotion.value || 0);
            let finalTotal = baseTotal;
            let discountAmount = 0;
            let bonusDays = 0;

            if (type === 'percentage') {
                const percent = Math.max(0, Math.min(100, value));
                discountAmount = Number((baseTotal * (percent / 100)).toFixed(2));
                finalTotal = Number(Math.max(0, baseTotal - discountAmount).toFixed(2));
            } else if (type === 'fixed') {
                discountAmount = Number(Math.max(0, Math.min(baseTotal, value)).toFixed(2));
                finalTotal = Number(Math.max(0, baseTotal - discountAmount).toFixed(2));
            } else if (type === 'final_price') {
                finalTotal = Number(Math.max(0, value).toFixed(2));
                discountAmount = Number(Math.max(0, baseTotal - finalTotal).toFixed(2));
            } else if (type === 'bonus_days') {
                bonusDays = Math.max(0, Math.round(value));
            } else if (type === 'two_for_one' || type === 'bring_friend') {
                const percent = value > 0 ? Math.max(0, Math.min(100, value)) : 50;
                discountAmount = Number((baseTotal * (percent / 100)).toFixed(2));
                finalTotal = Number(Math.max(0, baseTotal - discountAmount).toFixed(2));
            }

            return {
                baseTotal: baseTotal,
                finalTotal: finalTotal,
                discountAmount: discountAmount,
                effectiveMonthlyPrice: Number((finalTotal / safeBillingCycles).toFixed(2)),
                bonusDays: bonusDays,
            };
        };

        document.querySelectorAll('form[action*="/subscriptions/"]').forEach(function (form) {
            const planSelect = form.querySelector('.js-plan-template-select');
            const monthsSelect = form.querySelector('.js-months-select');
            const customPriceInput = form.querySelector('.js-custom-price-input');
            const feedback = form.querySelector('.js-renew-plan-feedback');
            if (!planSelect || !monthsSelect) {
                return;
            }

            const syncMode = function () {
                const selectedOption = planSelect.options[planSelect.selectedIndex] || null;
                const hasTemplate = String(planSelect.value || '').trim() !== '';
                const currentTemplateId = String(form.getAttribute('data-current-plan-template-id') || '').trim();
                const currentPlanName = String(form.getAttribute('data-current-plan-name') || 'Plan actual').trim();
                const currentPlanKey = String(form.getAttribute('data-current-plan-key') || '').trim().toLowerCase();
                const currentPlanPrice = Number(form.getAttribute('data-current-plan-price') || '0');
                const selectedPlanKey = String(selectedOption?.getAttribute('data-feature-plan-key') || currentPlanKey).toLowerCase();
                const selectedTemplateId = String(selectedOption?.getAttribute('data-plan-template-id') || planSelect.value || currentTemplateId).trim();
                const selectedPlanName = hasTemplate
                    ? String(selectedOption?.getAttribute('data-plan-name') || selectedOption?.textContent || 'Plan seleccionado').trim()
                    : currentPlanName;
                const selectedPlanPrice = hasTemplate
                    ? Number(selectedOption?.getAttribute('data-plan-price') || '0')
                    : currentPlanPrice;
                const selectedMonths = Math.max(1, Math.round(Number(monthsSelect.value || '1')));
                const canUseCustomPrice = selectedPlanKey === 'sucursales';

                monthsSelect.disabled = false;
                monthsSelect.classList.remove('opacity-60', 'cursor-not-allowed');
                monthsSelect.title = hasTemplate
                    ? 'Multiplica la cobertura del plan elegido y busca la promo de ese plazo.'
                    : 'Extiende la cobertura del plan actual y busca la promo de ese plazo.';

                if (customPriceInput) {
                    customPriceInput.disabled = !canUseCustomPrice;
                    customPriceInput.required = false;
                    customPriceInput.classList.toggle('opacity-60', !canUseCustomPrice);
                    customPriceInput.classList.toggle('cursor-not-allowed', !canUseCustomPrice);
                    customPriceInput.title = canUseCustomPrice
                        ? 'Precio personalizado para este cliente con plan sucursales.'
                        : 'Disponible cuando eliges plan sucursales.';

                    if (!canUseCustomPrice) {
                        customPriceInput.value = '';
                    }
                }

                const selectedCustomPrice = Number(customPriceInput?.value || '0');
                const baseMonthlyPrice = canUseCustomPrice && Number.isFinite(selectedCustomPrice) && selectedCustomPrice > 0
                    ? selectedCustomPrice
                    : selectedPlanPrice;
                const selectedPromotion = resolvePromotion(selectedTemplateId, selectedMonths);
                const pricing = resolvePromotionPricing(baseMonthlyPrice, selectedMonths, selectedPromotion);

                if (!feedback) {
                    return;
                }

                if (selectedTemplateId === '' && currentTemplateId === '') {
                    feedback.textContent = 'Selecciona uno de los 4 planes base si quieres aplicar una promocion por plazo.';
                    return;
                }

                if (selectedPromotion) {
                    const promotionName = String(selectedPromotion.name || 'Promocion activa').trim();
                    const bonusDaysSuffix = pricing.bonusDays > 0 ? ' +' + pricing.bonusDays + ' dias.' : '.';
                    feedback.textContent = selectedPlanName + ': promo "' + promotionName + '" aplicada. Normal ' + formatUsd(pricing.baseTotal) + ', total ' + formatUsd(pricing.finalTotal) + ' (' + formatUsd(pricing.effectiveMonthlyPrice) + '/mes promedio)' + bonusDaysSuffix;
                    return;
                }

                feedback.textContent = selectedPlanName + ': sin promocion activa para ' + selectedMonths + ' mes(es). Total ' + formatUsd(pricing.baseTotal) + '.';
            };

            planSelect.addEventListener('change', syncMode);
            monthsSelect.addEventListener('change', syncMode);
            customPriceInput?.addEventListener('input', syncMode);
            syncMode();
        });
    })();
</script>
@endpush
