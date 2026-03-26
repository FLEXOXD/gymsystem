


<?php $__env->startSection('title', 'Ganancias del gimnasio'); ?>
<?php $__env->startSection('page-title', 'Ganancias del gimnasio'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .panel-main-split {
        display: grid;
        gap: 1rem;
    }

    .panel-kpi-grid {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .panel-kpi-card {
        min-height: 7.5rem;
        border-radius: 0.75rem;
        padding: 0.75rem;
    }

    .panel-kpi-title {
        min-height: 1.75rem;
    }

    .panel-kpi-value {
        font-size: 1.5rem;
        line-height: 1;
        letter-spacing: 0;
    }

    .panel-cta-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .panel-cta-grid .ui-button {
        min-height: 2.5rem;
    }

    .panel-cash-today-grid {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .panel-control-shell {
        position: relative;
        overflow: hidden;
        border: 1px solid rgb(163 230 53 / 0.22);
        border-radius: 1.15rem;
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.14), transparent 34%),
            linear-gradient(145deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.96));
        box-shadow: 0 24px 44px -34px rgb(15 23 42 / 0.52);
        padding: 1rem;
    }

    .theme-dark .panel-control-shell,
    .dark .panel-control-shell {
        border-color: rgb(163 230 53 / 0.28);
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.12), transparent 34%),
            linear-gradient(152deg, rgb(3 7 18 / 0.95), rgb(11 18 32 / 0.9));
        box-shadow: 0 24px 48px -36px rgb(2 8 23 / 0.9);
    }

    .panel-control-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(90deg, transparent, rgb(163 230 53 / 0.04), transparent);
    }

    .panel-control-grid {
        display: grid;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    .panel-control-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgb(77 124 15 / 0.94);
    }

    .theme-dark .panel-control-kicker,
    .dark .panel-control-kicker {
        color: rgb(217 249 157 / 0.92);
    }

    .panel-control-kicker::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: rgb(132 204 22 / 0.92);
        box-shadow: 0 0 0 6px rgb(132 204 22 / 0.14);
    }

    .panel-control-heading {
        font-size: clamp(1.05rem, 1.8vw, 1.35rem);
        line-height: 1.08;
        letter-spacing: -0.03em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-control-heading,
    .dark .panel-control-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .panel-control-copy {
        max-width: 48rem;
    }

    .panel-control-copy p {
        margin-top: 0.45rem;
        font-size: 0.88rem;
        color: rgb(71 85 105 / 0.94);
    }

    .theme-dark .panel-control-copy p,
    .dark .panel-control-copy p {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-control-progress {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        border: 1px solid rgb(163 230 53 / 0.26);
        background: rgb(236 252 203 / 0.72);
        padding: 0.38rem 0.72rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(77 124 15 / 0.94);
    }

    .theme-dark .panel-control-progress,
    .dark .panel-control-progress {
        border-color: rgb(163 230 53 / 0.28);
        background: rgb(132 204 22 / 0.12);
        color: rgb(217 249 157 / 0.96);
    }

    .panel-control-priority-grid {
        display: grid;
        gap: 0.75rem;
    }

    .panel-control-priority {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: rgb(255 255 255 / 0.78);
        padding: 0.82rem 0.9rem;
    }

    .theme-dark .panel-control-priority,
    .dark .panel-control-priority {
        border-color: rgb(148 163 184 / 0.16);
        background: rgb(15 23 42 / 0.62);
    }

    .panel-control-priority::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .panel-control-priority[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }

    .panel-control-priority[data-tone='danger']::before {
        background: linear-gradient(90deg, rgb(244 63 94 / 0.9), rgb(244 63 94 / 0.24));
    }

    .panel-control-priority[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
    }

    .panel-control-priority[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(34 197 94 / 0.9), rgb(34 197 94 / 0.24));
    }

    .panel-control-priority-label {
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .panel-control-priority-label,
    .dark .panel-control-priority-label {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-control-priority-value {
        margin-top: 0.45rem;
        font-size: 1.4rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-control-priority-value,
    .dark .panel-control-priority-value {
        color: rgb(248 250 252 / 0.98);
    }

    .panel-control-priority-note {
        margin-top: 0.45rem;
        font-size: 0.76rem;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-control-priority-note,
    .dark .panel-control-priority-note {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-control-actions {
        display: grid;
        gap: 0.6rem;
        align-content: start;
    }

    .panel-control-actions .ui-button {
        min-height: 2.8rem;
    }

    .panel-control-disclosure {
        position: relative;
        z-index: 1;
        margin-top: 0.9rem;
        border-top: 1px solid rgb(148 163 184 / 0.18);
        padding-top: 0.9rem;
    }

    .theme-dark .panel-control-disclosure,
    .dark .panel-control-disclosure {
        border-top-color: rgb(148 163 184 / 0.12);
    }

    .panel-control-disclosure summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        cursor: pointer;
        list-style: none;
        font-size: 0.76rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.94);
    }

    .panel-control-disclosure summary::-webkit-details-marker {
        display: none;
    }

    .theme-dark .panel-control-disclosure summary,
    .dark .panel-control-disclosure summary {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-control-disclosure summary::after {
        content: '+';
        font-size: 1rem;
        font-weight: 900;
        color: rgb(132 204 22 / 0.92);
    }

    .panel-control-disclosure[open] summary::after {
        content: '-';
    }

    .panel-control-checklist {
        display: grid;
        gap: 0.7rem;
        margin-top: 0.85rem;
    }

    .panel-control-check-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.8rem;
        border-radius: 0.95rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: rgb(255 255 255 / 0.66);
        padding: 0.8rem 0.85rem;
    }

    .theme-dark .panel-control-check-item,
    .dark .panel-control-check-item {
        border-color: rgb(148 163 184 / 0.14);
        background: rgb(15 23 42 / 0.58);
    }

    .panel-control-check-item.is-complete {
        border-color: rgb(132 204 22 / 0.24);
        background: rgb(236 252 203 / 0.5);
    }

    .theme-dark .panel-control-check-item.is-complete,
    .dark .panel-control-check-item.is-complete {
        border-color: rgb(132 204 22 / 0.22);
        background: rgb(132 204 22 / 0.08);
    }

    .panel-control-check-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-control-check-title,
    .dark .panel-control-check-title {
        color: rgb(241 245 249 / 0.97);
    }

    .panel-control-check-copy {
        margin-top: 0.24rem;
        font-size: 0.76rem;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-control-check-copy,
    .dark .panel-control-check-copy {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-control-check-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.34rem 0.58rem;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .panel-control-check-badge.is-complete {
        background: rgb(190 242 100 / 0.78);
        color: rgb(54 83 20 / 0.96);
    }

    .theme-dark .panel-control-check-badge.is-complete,
    .dark .panel-control-check-badge.is-complete {
        background: rgb(132 204 22 / 0.18);
        color: rgb(217 249 157 / 0.96);
    }

    .panel-control-check-badge.is-pending {
        background: rgb(226 232 240 / 0.88);
        color: rgb(51 65 85 / 0.95);
    }

    .theme-dark .panel-control-check-badge.is-pending,
    .dark .panel-control-check-badge.is-pending {
        background: rgb(51 65 85 / 0.88);
        color: rgb(226 232 240 / 0.94);
    }

    .panel-control-summary-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .panel-control-summary-actions .ui-button {
        min-height: 2.6rem;
    }

    @media (min-width: 640px) {
        .panel-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-control-priority-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel-control-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .panel-kpi-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
    }

    .panel-premium-mode .panel-kpi-card {
        min-height: 7.9rem;
        border-radius: 0.9rem;
        padding: 0.85rem;
    }

    .panel-premium-mode .panel-kpi-title {
        min-height: 2rem;
    }

    .panel-premium-mode .panel-kpi-value {
        font-size: clamp(1.72rem, 2.4vw, 2.12rem);
        letter-spacing: -0.02em;
    }

    .panel-premium-mode .panel-cta-grid {
        display: grid;
        gap: 0.55rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .panel-premium-mode .panel-cta-grid .ui-button {
        width: 100%;
        min-height: 2.6rem;
    }

    .panel-premium-mode .panel-cash-today-grid {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    @media (min-width: 640px) {
        .panel-premium-mode .panel-cta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-today-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .panel-premium-mode .panel-main-split {
            grid-template-columns: minmax(0, 1fr) 320px;
        }

        .panel-control-grid {
            grid-template-columns: minmax(0, 1fr) 19rem;
            align-items: start;
        }

        .panel-control-actions {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-kpi-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cta-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-today-grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-session-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1536px) {
        .panel-premium-mode .panel-main-split {
            grid-template-columns: minmax(0, 1fr) 340px;
        }

        .panel-premium-mode .panel-kpi-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cta-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-today-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-session-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $monthCurrentLabel = now()->format('M Y');
        $monthPreviousLabel = now()->subMonthNoOverflow()->format('M Y');
        $monthlyBarsMax = max(1, (float) collect($incomeLast6Months)->max('income'));
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canUseSalesInventory = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'sales_inventory');
        $canManageCashiers = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'cashiers');
        $canViewReports = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'reports_base');
        $contextGym = (string) request()->route('contextGym');
        $panelRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $panelSessionSummary = $openSessionScopedSummary ?? [
            'opening_balance' => 0,
            'income_total' => 0,
            'expense_total' => 0,
            'net_total' => 0,
            'visible_total' => 0,
            'movements_count' => 0,
        ];
        $clientShowUrl = static fn (int $clientId): string => route('clients.show', ['client' => $clientId] + ($isGlobalScope ? ['scope' => 'global'] : []));
        $planControlDashboard = is_array($planControlDashboard ?? null) ? $planControlDashboard : null;
        $planProfessionalDashboard = is_array($planProfessionalDashboard ?? null) ? $planProfessionalDashboard : null;
    ?>

    <section class="panel-main-split xl:grid-cols-[minmax(0,1fr)_340px] xl:items-start">
    <div class="space-y-4">
    <?php if($planProfessionalDashboard): ?>
        <?php
            $professionalAlerts = collect($planProfessionalDashboard['alerts'] ?? [])->values();
        ?>
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Plan Profesional: radar comercial','subtitle' => 'Ventas, promociones, reportes y equipo en una sola lectura compacta.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Plan Profesional: radar comercial','subtitle' => 'Ventas, promociones, reportes y equipo en una sola lectura compacta.']); ?>
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
                <div>
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="max-w-3xl">
                            <p class="text-sm font-black text-slate-900 dark:text-slate-100"><?php echo e($planProfessionalDashboard['headline'] ?? 'Radar comercial activo'); ?></p>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300"><?php echo e($planProfessionalDashboard['summary'] ?? 'Usa esta vista para crecer con mas control.'); ?></p>
                        </div>
                        <span class="inline-flex rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-cyan-800 dark:border-cyan-400/40 dark:bg-cyan-500/15 dark:text-cyan-100">
                            Profesional
                        </span>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Total comercial mes</p>
                            <p class="mt-2 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) ($planProfessionalDashboard['commercial_month_total'] ?? 0), $appCurrencyCode)); ?></p>
                            <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-200">Membresias + productos registrados este mes.</p>
                        </article>

                        <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                            <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Cobros de membresias</p>
                            <p class="mt-2 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) ($planProfessionalDashboard['membership_income_current_month'] ?? 0), $appCurrencyCode)); ?></p>
                            <p class="mt-1 text-xs text-cyan-700 dark:text-cyan-200">Ingreso del mes desde membresias activadas o renovadas.</p>
                        </article>

                        <article class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200">Ventas de productos</p>
                            <p class="mt-2 text-2xl font-black text-amber-800 dark:text-amber-100"><?php echo e($currencyFormatter::format((float) ($planProfessionalDashboard['product_sales_income_current_month'] ?? 0), $appCurrencyCode)); ?></p>
                            <p class="mt-1 text-xs text-amber-700 dark:text-amber-200"><?php echo e((int) ($planProfessionalDashboard['product_sales_month_count'] ?? 0)); ?> ticket(s) | Ticket prom. <?php echo e($currencyFormatter::format((float) ($planProfessionalDashboard['average_product_ticket'] ?? 0), $appCurrencyCode, true)); ?></p>
                        </article>

                        <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                            <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Promos y equipo</p>
                            <p class="mt-2 text-2xl font-black text-violet-800 dark:text-violet-100"><?php echo e((int) ($planProfessionalDashboard['active_promotions_count'] ?? 0)); ?></p>
                            <p class="mt-1 text-xs text-violet-700 dark:text-violet-200">
                                Promos activas | Cajero <?php echo e((int) ($planProfessionalDashboard['active_cashiers'] ?? 0)); ?>/<?php echo e(max(0, (int) ($planProfessionalDashboard['max_cashiers'] ?? 0))); ?>

                            </p>
                        </article>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Oportunidades del dia</p>
                        <div class="mt-3 space-y-2">
                            <?php $__currentLoopData = $professionalAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $alertTone = (string) ($alert['tone'] ?? 'info');
                                    $alertCardClass = match ($alertTone) {
                                        'warning' => 'border-amber-200 bg-amber-50 dark:border-amber-400/40 dark:bg-amber-500/15',
                                        'success' => 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15',
                                        default => 'border-cyan-200 bg-cyan-50 dark:border-cyan-400/40 dark:bg-cyan-500/15',
                                    };
                                    $alertTextClass = match ($alertTone) {
                                        'warning' => 'text-amber-700 dark:text-amber-200',
                                        'success' => 'text-emerald-700 dark:text-emerald-200',
                                        default => 'text-cyan-700 dark:text-cyan-200',
                                    };
                                ?>
                                <article class="rounded-xl border p-3 <?php echo e($alertCardClass); ?>">
                                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e($alertTextClass); ?>"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                    <p class="mt-1 text-xs <?php echo e($alertTextClass); ?>"><?php echo e($alert['description'] ?? ''); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                        <?php if($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $panelRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $panelRouteParams)),'variant' => 'primary']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if($canViewReports && \Illuminate\Support\Facades\Route::has('reports.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $panelRouteParams)),'variant' => 'secondary']); ?>Ver reportes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $panelRouteParams)),'variant' => 'ghost']); ?>Planes y promos <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if($canManageCashiers && \Illuminate\Support\Facades\Route::has('staff.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('staff.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('staff.index', $panelRouteParams)),'variant' => 'ghost']); ?>Gestionar cajero <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('products.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('products.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index', $panelRouteParams)),'variant' => 'ghost']); ?>
                                <?php echo e(((int) ($planProfessionalDashboard['low_stock_products_count'] ?? 0)) > 0 ? 'Reponer stock bajo' : 'Ver productos'); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    <?php endif; ?>
    <?php if($planControlDashboard): ?>
        <?php
            $controlChecklist = collect($planControlDashboard['checklist'] ?? [])->values();
            $controlPriorities = collect($planControlDashboard['priorities'] ?? [])->take(3)->values();
            $controlPendingSteps = (int) $controlChecklist->where('completed', false)->count();
            $controlHeroTitle = $controlPendingSteps > 0
                ? 'Activa lo esencial y deja la sede lista para cobrar'
                : 'Opera una sola sede con caja, recepcion y vencidos en orden';
            $controlHeroSummary = $controlPendingSteps > 0
                ? 'Completa solo lo importante y deja el panel libre para operar. El resto queda guardado dentro del checklist.'
                : 'Tu base ya esta lista. Usa esta franja para decidir rapido que cobrar, que renovar y que revisar hoy.';
            $controlNextAction = (array) ($planControlDashboard['next_action'] ?? []);
            $controlNextActionUrl = (string) ($controlNextAction['action_url'] ?? route('reception.index', $panelRouteParams));
            $secondaryControlAction = str_contains($controlNextActionUrl, '/clients')
                ? ['label' => 'Ir a recepcion', 'url' => route('reception.index', $panelRouteParams)]
                : ['label' => 'Cobrar membresia', 'url' => route('clients.index', $panelRouteParams)];
        ?>
        <section class="panel-control-shell">
            <div class="panel-control-grid">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="panel-control-copy">
                            <span class="panel-control-kicker">Plan Control · 1 sede</span>
                            <h2 class="panel-control-heading mt-3"><?php echo e($controlHeroTitle); ?></h2>
                            <p><?php echo e($controlHeroSummary); ?></p>
                        </div>
                        <span class="panel-control-progress"><?php echo e($planControlDashboard['progress_label'] ?? '0 de 0 pasos'); ?></span>
                    </div>

                    <?php if($controlPriorities->isNotEmpty()): ?>
                        <div class="panel-control-priority-grid">
                            <?php $__currentLoopData = $controlPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="panel-control-priority" data-tone="<?php echo e($priority['tone'] ?? 'info'); ?>">
                                    <p class="panel-control-priority-label"><?php echo e($priority['label'] ?? 'Pendiente'); ?></p>
                                    <p class="panel-control-priority-value"><?php echo e($priority['value'] ?? '0'); ?></p>
                                    <p class="panel-control-priority-note"><?php echo e($priority['description'] ?? ''); ?></p>
                                    <?php if(! empty($priority['action_url'])): ?>
                                        <a href="<?php echo e((string) $priority['action_url']); ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-lime-700 transition hover:text-lime-800 dark:text-lime-300 dark:hover:text-lime-200">
                                            <?php echo e($priority['action_label'] ?? 'Abrir'); ?>

                                            <span aria-hidden="true">-></span>
                                        </a>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-4">
                    <div class="panel-control-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $controlNextActionUrl,'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($controlNextActionUrl),'variant' => 'primary']); ?>
                            <?php echo e($controlNextAction['action_label'] ?? 'Abrir panel'); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $secondaryControlAction['url'],'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($secondaryControlAction['url']),'variant' => 'secondary']); ?>
                            <?php echo e($secondaryControlAction['label']); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>

                    <details class="panel-control-disclosure">
                        <summary>Activacion inicial</summary>
                        <div class="panel-control-checklist">
                            <?php $__currentLoopData = $controlChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="panel-control-check-item <?php echo e(! empty($item['completed']) ? 'is-complete' : ''); ?>">
                                    <div class="min-w-0">
                                        <p class="panel-control-check-title"><?php echo e($item['label'] ?? 'Paso'); ?></p>
                                        <p class="panel-control-check-copy"><?php echo e($item['description'] ?? ''); ?></p>
                                        <?php if(empty($item['completed']) && ! empty($item['action_url'])): ?>
                                            <a href="<?php echo e((string) $item['action_url']); ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-slate-700 transition hover:text-slate-900 dark:text-slate-200 dark:hover:text-slate-50">
                                                <?php echo e($item['action_label'] ?? 'Abrir'); ?>

                                                <span aria-hidden="true">-></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <span class="panel-control-check-badge <?php echo e(! empty($item['completed']) ? 'is-complete' : 'is-pending'); ?>">
                                        <?php echo e(! empty($item['completed']) ? 'Listo' : 'Pendiente'); ?>

                                    </span>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </details>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['id' => 'tour-panel-summary','title' => 'Resumen del dia','subtitle' => ''.e($planControlDashboard ? 'Caja, vencidos y accesos en una sola vista para una sede.' : 'Indicadores clave para tomar decisiones rapidas.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-summary','title' => 'Resumen del dia','subtitle' => ''.e($planControlDashboard ? 'Caja, vencidos y accesos en una sola vista para una sede.' : 'Indicadores clave para tomar decisiones rapidas.').'']); ?>
        <?php if($isCashierScoped): ?>
            <p class="mb-4 ui-alert ui-alert-info">Vista privada: aqui solo ves tus cobros, movimientos y acumulados del mes actual.</p>
        <?php endif; ?>
        <div class="panel-kpi-grid">
            <article class="panel-kpi-card flex flex-col justify-between border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/75">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-slate-500 dark:text-slate-300">Clientes</p>
                <p class="panel-kpi-value mt-1 font-black text-slate-900 dark:text-slate-100"><?php echo e($totalClients); ?></p>
                <p class="min-h-[16px] text-xs text-slate-500 dark:text-slate-300">Base registrada</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-emerald-700 dark:text-emerald-200">Membresías activas</p>
                <p class="panel-kpi-value mt-1 font-black text-emerald-800 dark:text-emerald-100"><?php echo e($activeMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-emerald-700 dark:text-emerald-200">Vigentes hoy</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-amber-200 bg-amber-50 dark:border-amber-400/40 dark:bg-amber-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-amber-700 dark:text-amber-200">Por vencer</p>
                <p class="panel-kpi-value mt-1 font-black text-amber-800 dark:text-amber-100"><?php echo e($expiringSoonMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-amber-700 dark:text-amber-200">Próximas 48 horas</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-rose-700 dark:text-rose-200">Vencid@s</p>
                <p class="panel-kpi-value mt-1 font-black text-rose-800 dark:text-rose-100"><?php echo e($expiredMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-rose-700 dark:text-rose-200">Requieren renovación</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-cyan-200 bg-cyan-50 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins hoy</p>
                <p class="panel-kpi-value mt-1 font-black text-cyan-800 dark:text-cyan-100"><?php echo e((int) $checkinsToday); ?></p>
                <p class="min-h-[16px] text-xs text-cyan-700 dark:text-cyan-200">Se reinicia 12:00 AM</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-violet-200 bg-violet-50 dark:border-violet-400/40 dark:bg-violet-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-violet-700 dark:text-violet-200">Planes activos</p>
                <p class="panel-kpi-value mt-1 font-black text-violet-800 dark:text-violet-100"><?php echo e($activePlans); ?></p>
                <p class="min-h-[16px] text-xs text-violet-700 dark:text-violet-200">Oferta vigente</p>
            </article>
        </div>

        <?php if($planControlDashboard): ?>
            <div class="panel-control-summary-actions mt-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'tour-panel-go-clients','href' => route('clients.index', $panelRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-go-clients','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $panelRouteParams)),'variant' => 'primary']); ?>Cobrar membresia <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reception.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reception.index', $panelRouteParams)),'variant' => 'secondary']); ?>Ir a recepcion <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $panelRouteParams)),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $panelRouteParams)),'variant' => 'ghost']); ?>Planes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        <?php else: ?>
            <div class="panel-cta-grid mt-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reception.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reception.index')),'variant' => 'primary']); ?>Ir a recepcion <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'tour-panel-go-clients','href' => route('clients.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-go-clients','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index')),'variant' => 'secondary']); ?>Panel de clientes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.index')): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $panelRouteParams)),'variant' => 'secondary']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index'),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index')),'variant' => 'ghost']); ?>Ver planes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

    <section class="grid gap-4 xl:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Comparativo mensual personal' : 'Comparativo mensual').'','subtitle' => ''.e($isCashierScoped ? 'Compara tus cobros del mes actual contra tu mes anterior.' : 'Si las ventas van mejor o peor vs el mes anterior.').'','class' => 'xl:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Comparativo mensual personal' : 'Comparativo mensual').'','subtitle' => ''.e($isCashierScoped ? 'Compara tus cobros del mes actual contra tu mes anterior.' : 'Si las ventas van mejor o peor vs el mes anterior.').'','class' => 'xl:col-span-2']); ?>
            <div class="grid gap-3 md:grid-cols-3">
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200"><?php echo e($monthCurrentLabel); ?></p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) $incomeCurrentMonth, $appCurrencyCode)); ?></p>
                    <p class="text-xs text-cyan-700 dark:text-cyan-200"><?php echo e($isCashierScoped ? 'Tus ingresos del mes' : 'Ingresos del mes'); ?></p>
                </article>
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"><?php echo e($monthPreviousLabel); ?></p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $incomePreviousMonth, $appCurrencyCode)); ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-300"><?php echo e($isCashierScoped ? 'Tu mes anterior' : 'Mes anterior'); ?></p>
                </article>
                <article class="rounded-xl border p-3 <?php echo e($monthlyIncomeDiff >= 0 ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15'); ?>">
                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200'); ?>"><?php echo e($isCashierScoped ? 'Variación personal' : 'Variación'); ?></p>
                    <p class="mt-1 text-2xl font-black <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-800 dark:text-emerald-100' : 'text-rose-800 dark:text-rose-100'); ?>">
                        <?php echo e($monthlyIncomeDiff >= 0 ? '+' : ''); ?><?php echo e($currencyFormatter::format((float) $monthlyIncomeDiff, $appCurrencyCode, true)); ?>

                    </p>
                    <p class="text-xs <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200'); ?>">
                        <?php if($monthlyIncomePct !== null): ?>
                            <?php echo e($monthlyIncomePct >= 0 ? '+' : ''); ?><?php echo e(number_format((float) $monthlyIncomePct, 1)); ?>%
                        <?php else: ?>
                            Sin base de comparación
                        <?php endif; ?>
                    </p>
                </article>
            </div>

            <div class="mt-4 space-y-2">
                <?php $__currentLoopData = $incomeLast6Months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $barWidth = min(100, max(6, ($row['income'] / $monthlyBarsMax) * 100));
                    ?>
                    <div class="grid grid-cols-[68px_1fr_88px] items-center gap-2 text-xs sm:grid-cols-[84px_1fr_120px]">
                        <span class="font-semibold text-slate-600 dark:text-slate-300"><?php echo e($row['label']); ?></span>
                        <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-2 rounded-full bg-cyan-500 dark:bg-cyan-400" style="width: <?php echo e(number_format($barWidth, 2, '.', '')); ?>%;"></div>
                        </div>
                        <span class="text-right font-semibold text-slate-700 dark:text-slate-200"><?php echo e($currencyFormatter::format((float) $row['income'], $appCurrencyCode, true)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tu producción de hoy' : 'Caja y ventas hoy').'','class' => 'xl:col-span-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tu producción de hoy' : 'Caja y ventas hoy').'','class' => 'xl:col-span-1']); ?>
            <div class="panel-cash-today-grid">
                <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200"><?php echo e($isCashierScoped ? 'Tus ingresos hoy' : 'Ingresos hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $incomeToday, $appCurrencyCode)); ?></p>
                </article>
                <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200"><?php echo e($isCashierScoped ? 'Tus egresos hoy' : 'Egresos hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $expenseToday, $appCurrencyCode)); ?></p>
                </article>
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200"><?php echo e($isCashierScoped ? 'Tu balance hoy' : 'Balance hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black <?php echo e((float) $todayBalance >= 0 ? 'text-cyan-800 dark:text-cyan-100' : 'text-rose-800 dark:text-rose-100'); ?>"><?php echo e($currencyFormatter::format((float) $todayBalance, $appCurrencyCode)); ?></p>
                </article>
                <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200"><?php echo e($isCashierScoped ? 'Tus cobros de membresías' : 'Cobros de membresías'); ?></p>
                    <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) ($membershipIncomeToday ?? 0), $appCurrencyCode)); ?></p>
                    <p class="text-xs text-emerald-700 dark:text-emerald-200">Mes: <?php echo e($currencyFormatter::format((float) ($membershipIncomeCurrentMonth ?? 0), $appCurrencyCode, true)); ?></p>
                </article>
                <article class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200"><?php echo e($isCashierScoped ? 'Tus ventas de inventario' : 'Ventas de inventario'); ?></p>
                    <p class="mt-1 text-2xl font-black text-amber-800 dark:text-amber-100"><?php echo e($currencyFormatter::format((float) ($productSalesIncomeToday ?? 0), $appCurrencyCode)); ?></p>
                    <p class="text-xs text-amber-700 dark:text-amber-200">Mes: <?php echo e($currencyFormatter::format((float) ($productSalesIncomeCurrentMonth ?? 0), $appCurrencyCode, true)); ?></p>
                </article>
                <article class="rounded-xl border p-3 <?php echo e((float) $netYearToDate >= 0 ? 'border-violet-200 bg-violet-50 dark:border-violet-400/40 dark:bg-violet-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15'); ?>">
                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200'); ?>"><?php echo e($isCashierScoped ? 'Tu acumulado del año' : 'Ganancia del año'); ?></p>
                    <p class="mt-1 text-2xl font-black <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-800 dark:text-violet-100' : 'text-rose-800 dark:text-rose-100'); ?>"><?php echo e($currencyFormatter::format((float) $netYearToDate, $appCurrencyCode)); ?></p>
                    <p class="text-xs <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200'); ?>"><?php echo e($isCashierScoped ? 'Tus ingresos menos egresos en el año' : 'Ingresos - egresos acumulados del año'); ?></p>
                </article>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    </section>

    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tu actividad de caja actual' : 'Estado de caja actual').'','subtitle' => ''.e($isCashierScoped ? 'Resumen privado de tus movimientos dentro del turno activo.' : 'Control rápido del turno activo.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tu actividad de caja actual' : 'Estado de caja actual').'','subtitle' => ''.e($isCashierScoped ? 'Resumen privado de tus movimientos dentro del turno activo.' : 'Control rápido del turno activo.').'']); ?>
        <?php if($isGlobalScope): ?>
            <p class="ui-alert ui-alert-info">Modo global activo: esta vista consolida sedes y no permite abrir o cerrar turnos desde el panel.</p>
            <div class="mt-3">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'secondary']); ?>Ver consolidado de caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        <?php elseif($openSession): ?>
            <?php if($isCashierScoped): ?>
                <div class="panel-cash-session-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Turno</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">#<?php echo e($openSession->id); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Tus registros dentro del turno</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['opening_balance'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Monto inicial del turno</p>
                    </article>
                    <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Tus ingresos</p>
                        <p class="mt-1 text-xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['income_total'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-emerald-700 dark:text-emerald-200">Cobros del turno activo</p>
                    </article>
                    <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Tus egresos</p>
                        <p class="mt-1 text-xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['expense_total'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-rose-700 dark:text-rose-200">Salidas registradas por ti</p>
                    </article>
                    <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Saldo visible</p>
                        <p class="mt-1 text-xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['visible_total'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-cyan-700 dark:text-cyan-200">Apertura + tus movimientos</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Tus movimientos</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) ($panelSessionSummary['movements_count'] ?? 0)); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Registrados por tu usuario</p>
                    </article>
                </div>
            <?php else: ?>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Turno</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">#<?php echo e($openSession->id); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Abierto</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $openSession->opening_balance, $appCurrencyCode)); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300"><?php echo e($openSession->opened_at?->format('Y-m-d H:i')); ?></p>
                    </article>
                    <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperado actual</p>
                        <p class="mt-1 text-xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) ($openSessionExpected ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-cyan-700 dark:text-cyan-200">Caja operando</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Abierta por</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e($openSession->openedBy?->name ?? '-'); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Usuario responsable</p>
                    </article>
                </div>
            <?php endif; ?>
            <div class="mt-3">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'secondary']); ?><?php echo e($isCashierScoped ? 'Ir a tu caja' : 'Ir a caja por turno'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        <?php else: ?>
            <p class="ui-alert ui-alert-warning">No hay turno de caja abierto ahora mismo.</p>
            <div class="mt-3">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'primary']); ?>Abrir caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        <?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    </div>

    <div class="space-y-4">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['id' => 'tour-panel-tracking','title' => 'Centro de seguimiento','subtitle' => 'Abre detalle en modal para evitar saturar la pantalla.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-tracking','title' => 'Centro de seguimiento','subtitle' => 'Abre detalle en modal para evitar saturar la pantalla.']); ?>
            <div class="grid gap-3">
                <article class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200">Renovaciones 48h</p>
                    <p class="mt-1 text-2xl font-black text-amber-800 dark:text-amber-100"><?php echo e($upcomingRenewals->count()); ?></p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-renewals">Ver detalle</button>
                </article>
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins de hoy</p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e((int) $checkinsToday); ?></p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-checkins">Ver detalle</button>
                </article>
                <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200"><?php echo e($isCashierScoped ? 'Tus movimientos hoy' : 'Movimientos de hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black text-violet-800 dark:text-violet-100"><?php echo e($movementsTodayCount); ?></p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-movements">Ver detalle</button>
                </article>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Renovar vencid@s','subtitle' => 'Acciones rápidas para clientes con membresía vencida.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Renovar vencid@s','subtitle' => 'Acciones rápidas para clientes con membresía vencida.']); ?>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Total vencid@s</p>
                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($expiredMemberships); ?></p>
            </div>

            <?php if($expiredRenewalCandidates->isNotEmpty()): ?>
                <div class="mt-3 space-y-2">
                    <?php $__currentLoopData = $expiredRenewalCandidates->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expiredClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $expiredLabel = $expiredClient->days_expired === null
                                ? 'Sin fecha'
                                : ($expiredClient->days_expired <= 0 ? 'Hoy' : ($expiredClient->days_expired === 1 ? 'Hace 1 día' : 'Hace '.$expiredClient->days_expired.' días'));
                        ?>
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200/80 bg-slate-50/80 p-2 dark:border-slate-700 dark:bg-slate-900/70">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($expiredClient->client_name); ?></p>
                                <?php if($isGlobalScope): ?>
                                    <p class="mt-0.5 text-[11px] font-semibold text-cyan-700 dark:text-cyan-300"><?php echo e($expiredClient->gym_name ?? '-'); ?></p>
                                <?php endif; ?>
                                <p class="truncate text-xs text-slate-600 dark:text-slate-300"><?php echo e($expiredClient->plan_name); ?> · <?php echo e($expiredLabel); ?></p>
                            </div>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $expiredClient->client_id),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $expiredClient->client_id)),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']); ?><?php echo e($isGlobalScope ? 'Ver' : 'Renovar'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <p class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-800 dark:border-emerald-400/40 dark:bg-emerald-500/15 dark:text-emerald-200">
                    No hay vencid@s por renovar.
                </p>
            <?php endif; ?>

            <div class="mt-3 flex flex-wrap gap-2">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', ['filter' => 'expired']),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', ['filter' => 'expired'])),'variant' => 'ghost','size' => 'sm']); ?>Ver listado vencid@s <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-expired-renewals">Ver detalle</button>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    </div>
    </section>

    <div id="modal-renewals" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalRenewalsTitle">
        <div class="ui-modal-panel max-w-5xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalRenewalsTitle" class="ui-heading text-lg">Próximas renovaciones (48 horas)</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Días</th>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingRenewals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $daysLeft = (int) ($membership->days_left ?? 0);
                            $daysLabel = $daysLeft <= 0 ? 'Hoy' : ($daysLeft === 1 ? '1 día' : $daysLeft.' días');
                        ?>
                        <tr>
                            <td><?php echo e($membership->client_name); ?></td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($membership->gym_name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($membership->plan_name); ?></td>
                            <td><?php echo e($membership->ends_at?->format('Y-m-d') ?? '-'); ?></td>
                            <td><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $daysLeft <= 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($daysLeft <= 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info'))]); ?><?php echo e($daysLabel); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                            <td class="text-right"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $membership->client_id),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $membership->client_id)),'size' => 'sm','variant' => 'ghost']); ?>Ver cliente <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 6 : 5); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">Sin renovaciones en las próximas 48 horas.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-expired-renewals" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalExpiredRenewalsTitle">
        <div class="ui-modal-panel max-w-5xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalExpiredRenewalsTitle" class="ui-heading text-lg">Renovar vencid@s</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Plan</th>
                        <th>Vencio</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $expiredRenewalCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expiredClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $expiredStatusLabel = $expiredClient->membership_status === 'cancelled'
                                ? 'Cancelada'
                                : ($expiredClient->days_expired === null ? 'Sin fecha' : ($expiredClient->days_expired <= 0 ? 'Hoy' : ($expiredClient->days_expired === 1 ? 'Hace 1 día' : 'Hace '.$expiredClient->days_expired.' días')));
                        ?>
                        <tr>
                            <td><?php echo e($expiredClient->client_name); ?></td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($expiredClient->gym_name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($expiredClient->plan_name); ?></td>
                            <td><?php echo e($expiredClient->ends_at?->format('Y-m-d') ?? '-'); ?></td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $expiredClient->membership_status === 'cancelled' ? 'warning' : 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expiredClient->membership_status === 'cancelled' ? 'warning' : 'danger')]); ?>
                                    <?php echo e($expiredStatusLabel); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $expiredClient->client_id),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $expiredClient->client_id)),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']); ?><?php echo e($isGlobalScope ? 'Ver' : 'Renovar'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 6 : 5); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay clientes vencid@s para renovar.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex justify-end">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', ['filter' => 'expired']),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', ['filter' => 'expired'])),'variant' => 'ghost','size' => 'sm']); ?>Ir a clientes vencid@s <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        </div>
    </div>

    <div id="modal-checkins" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalCheckinsTitle">
        <div class="ui-modal-panel max-w-4xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalCheckinsTitle" class="ui-heading text-lg">Check-ins de hoy</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[640px]">
                    <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $todayAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($attendance->time); ?></td>
                            <td><?php echo e($attendance->client?->full_name ?? '-'); ?></td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($attendance->gym?->name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td class="text-right"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $attendance->client_id),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $attendance->client_id)),'size' => 'sm','variant' => 'ghost']); ?>Perfil <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 4 : 3); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">Aún no hay check-ins hoy.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-movements" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalMovementsTitle">
        <div class="ui-modal-panel max-w-6xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalMovementsTitle" class="ui-heading text-lg"><?php echo e($isCashierScoped ? 'Tus últimos movimientos de caja' : 'Últimos movimientos de caja'); ?></h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[940px]">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Usuario</th>
                        <th>Descripción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentCashMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($movement->occurred_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $movement->type === 'income' ? 'success' : 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($movement->type === 'income' ? 'success' : 'danger')]); ?>
                                    <?php echo e($movement->type === 'income' ? 'Ingreso' : 'Egreso'); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                            </td>
                            <td><?php echo e($methodLabels[$movement->method] ?? $movement->method); ?></td>
                            <td class="<?php echo e($movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'); ?> font-semibold">
                                <?php echo e($movement->type === 'income' ? '+' : '-'); ?><?php echo e($currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true)); ?>

                            </td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($movement->gym?->name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($movement->createdBy?->name ?? '-'); ?></td>
                            <td class="max-w-[340px] truncate" title="<?php echo e($movement->description ?: '-'); ?>"><?php echo e($movement->description ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 7 : 6); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300"><?php echo e($isCashierScoped ? 'Aún no tienes movimientos registrados.' : 'No hay movimientos registrados aún.'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        function closeAllPanelModals() {
            document.querySelectorAll('.panel-modal').forEach(function (modal) {
                modal.classList.add('hidden');
            });
            document.body.classList.remove('overflow-hidden');
        }

        document.querySelectorAll('[data-open-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                const modalId = button.getAttribute('data-open-modal');
                const modal = modalId ? document.getElementById(modalId) : null;
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', closeAllPanelModals);
        });

        document.querySelectorAll('.panel-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeAllPanelModals();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllPanelModals();
            }
        });

    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/panel/index.blade.php ENDPATH**/ ?>