<?php $__env->startSection('title', 'Reporte de ventas e inventario'); ?>
<?php $__env->startSection('page-title', 'Reporte de ventas e inventario'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .report-sales-inventory .filter-form {
        align-items: end;
    }

    .report-sales-inventory .metric-card {
        min-height: 100%;
    }

    .report-sales-inventory .chart-shell {
        height: clamp(260px, 46vh, 430px);
    }

    .report-sales-inventory .chart-shell canvas {
        width: 100% !important;
        height: 100% !important;
    }

    .report-sales-inventory .table-wrap {
        border-radius: 0.85rem;
        border: 1px solid rgb(203 213 225);
        overflow: auto;
    }

    .theme-dark .report-sales-inventory .table-wrap {
        border-color: rgb(51 65 85 / 0.85);
    }

    .report-sales-inventory .table-wrap .ui-table thead th {
        position: sticky;
        top: 0;
        z-index: 4;
        background: rgb(241 245 249 / 0.95);
        backdrop-filter: blur(4px);
    }

    .theme-dark .report-sales-inventory .table-wrap .ui-table thead th {
        background: rgb(30 41 59 / 0.95);
    }

    @media (max-width: 768px) {
        .report-sales-inventory .ui-card {
            padding: 0.9rem;
        }

        .report-sales-inventory .filter-form {
            grid-template-columns: minmax(0, 1fr);
            gap: 0.65rem;
        }

        .report-sales-inventory .chart-shell {
            height: 220px;
        }
    }

    .report-sales-inventory .report-surface-card {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(148 163 184 / 0.26);
        background:
            radial-gradient(circle at top right, rgb(6 182 212 / 0.08), transparent 26%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 26px 44px -38px rgb(15 23 42 / 0.18);
    }

    .report-sales-inventory .report-surface-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.76), transparent);
        opacity: 0.82;
        pointer-events: none;
    }

    .report-sales-inventory .report-surface-card > * {
        position: relative;
        z-index: 1;
    }

    .report-sales-inventory .report-surface-card > header .ui-heading {
        font-size: clamp(1.16rem, 1.4vw, 1.42rem);
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .report-sales-inventory .report-surface-card > header .ui-muted {
        margin-top: 0.35rem;
        font-size: 0.88rem;
    }

    .report-sales-inventory .report-filter-shell,
    .report-sales-inventory .report-pagination-shell {
        border: 1px solid rgb(148 163 184 / 0.22);
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.82), rgb(248 250 252 / 0.74));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.8),
            0 18px 32px -34px rgb(15 23 42 / 0.14);
    }

    .report-sales-inventory .report-filter-shell {
        padding: 0.95rem 1rem;
    }

    .report-sales-inventory .report-pagination-shell {
        padding: 0.85rem 1rem;
    }

    .report-sales-inventory .report-filter-shell .ui-button {
        border-radius: 0.92rem;
    }

    .report-sales-inventory .report-metric-card {
        position: relative;
        overflow: hidden;
        min-height: 100%;
        border: 1px solid rgb(148 163 184 / 0.24);
        box-shadow: 0 18px 28px -30px rgb(15 23 42 / 0.18);
    }

    .report-sales-inventory .report-metric-card::before {
        content: '';
        position: absolute;
        left: 1rem;
        right: 1rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .report-sales-inventory .report-chart-card .chart-shell {
        border: 1px solid rgb(148 163 184 / 0.2);
        border-radius: 1rem;
        background: linear-gradient(180deg, rgb(255 255 255 / 0.7), rgb(248 250 252 / 0.55));
        padding: 0.85rem;
    }

    .report-sales-inventory .report-action-card {
        border-radius: 1rem;
        box-shadow: 0 18px 28px -30px rgb(15 23 42 / 0.16);
    }

    .report-sales-inventory .report-table-shell {
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.95), rgb(248 250 252 / 0.9));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.82),
            0 18px 32px -34px rgb(15 23 42 / 0.15);
    }

    .report-sales-inventory .report-empty-state {
        padding-top: 2.3rem;
        padding-bottom: 2.3rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .theme-light .report-sales-inventory .report-surface-card,
    .theme-light .report-sales-inventory .report-filter-shell,
    .theme-light .report-sales-inventory .report-pagination-shell,
    .theme-light .report-sales-inventory .report-metric-card,
    .theme-light .report-sales-inventory .report-chart-card .chart-shell,
    .theme-light .report-sales-inventory .report-table-shell {
        border-color: rgb(203 213 225 / 0.82);
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.9),
            0 18px 30px -32px rgb(15 23 42 / 0.1);
    }

    .theme-dark .report-sales-inventory .report-surface-card,
    .dark .report-sales-inventory .report-surface-card,
    .theme-dark .report-sales-inventory .report-filter-shell,
    .dark .report-sales-inventory .report-filter-shell,
    .theme-dark .report-sales-inventory .report-pagination-shell,
    .dark .report-sales-inventory .report-pagination-shell,
    .theme-dark .report-sales-inventory .report-metric-card,
    .dark .report-sales-inventory .report-metric-card,
    .theme-dark .report-sales-inventory .report-chart-card .chart-shell,
    .dark .report-sales-inventory .report-chart-card .chart-shell,
    .theme-dark .report-sales-inventory .report-table-shell,
    .dark .report-sales-inventory .report-table-shell {
        border-color: rgb(51 65 85 / 0.74);
        background:
            linear-gradient(180deg, rgb(15 23 42 / 0.94), rgb(17 24 39 / 0.9));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.04),
            0 18px 30px -32px rgb(2 8 23 / 0.74);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $contextGym = (string) request()->route('contextGym');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isBranchContext = (bool) request()->attributes->get('gym_context_is_branch', false);
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canExportReports = ! $isBranchContext
            && $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'reports_export');
        $chartLabels = collect($salesByDay ?? [])->map(fn ($row) => \Carbon\Carbon::parse((string) $row->date)->format('Y-m-d'))->values();
        $chartRevenue = collect($salesByDay ?? [])->map(fn ($row) => round((float) $row->total_revenue, 2))->values();
        $chartProfit = collect($salesByDay ?? [])->map(fn ($row) => round((float) $row->total_profit, 2))->values();
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
        $routeParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $exportRouteParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
    ?>

    <div class="report-sales-inventory space-y-4">
        <?php if(! $schemaReady): ?>
            <div class="ui-alert ui-alert-warning">Falta ejecutar <code>php artisan migrate</code> para habilitar el reporte de ventas e inventario.</div>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Filtro del módulo','subtitle' => 'Lee rendimiento comercial y rotación de inventario por período.','class' => 'report-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Filtro del módulo','subtitle' => 'Lee rendimiento comercial y rotación de inventario por período.','class' => 'report-surface-card']); ?>
            <form method="GET" action="<?php echo e(route('reports.sales-inventory', ['contextGym' => $contextGym])); ?>" class="report-filter-shell filter-form grid gap-3 md:grid-cols-5">
                <?php if($isGlobalScope): ?>
                    <input type="hidden" name="scope" value="global">
                <?php endif; ?>
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Desde</span>
                    <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Hasta</span>
                    <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="ui-input">
                </label>

                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?>Aplicar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>

                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <?php if($canExportReports && \Illuminate\Support\Facades\Route::has('reports.sales-inventory.export.csv')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.sales-inventory.export.csv', $exportRouteParams),'dataUiLoadingIgnore' => '1','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.sales-inventory.export.csv', $exportRouteParams)),'data-ui-loading-ignore' => '1','variant' => 'ghost']); ?>Exportar CSV <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Panel reportes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $routeParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $routeParams)),'variant' => 'ghost']); ?>Volver al módulo <?php echo $__env->renderComponent(); ?>
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
            </form>
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

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Ventas</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) ($salesSummary['total_sales'] ?? 0)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300"><?php echo e((int) ($salesSummary['units_sold'] ?? 0)); ?> unidades vendidas</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Ingreso total</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e($currencyFormatter::format((float) ($salesSummary['total_revenue'] ?? 0), $appCurrencyCode)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Ticket promedio <?php echo e($currencyFormatter::format((float) ($salesSummary['average_ticket'] ?? 0), $appCurrencyCode)); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Costo total</p>
                <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-300"><?php echo e($currencyFormatter::format((float) ($salesSummary['total_cost'] ?? 0), $appCurrencyCode)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Base para utilidad real</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Utilidad</p>
                <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300"><?php echo e($currencyFormatter::format((float) ($salesSummary['total_profit'] ?? 0), $appCurrencyCode)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Ingreso menos costo</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos de stock</p>
                <p class="mt-2 text-3xl font-black text-cyan-700 dark:text-cyan-300"><?php echo e((int) ($inventorySummary['movement_count'] ?? 0)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Rotación del período</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Unidades que entraron</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e((int) ($inventorySummary['units_in'] ?? 0)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Reposicion y carga inicial</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Unidades que salieron</p>
                <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-300"><?php echo e((int) ($inventorySummary['units_out'] ?? 0)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Ventas y ajustes negativos</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card report-metric-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card report-metric-card']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Ajustes manuales</p>
                <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300"><?php echo e((int) ($inventorySummary['manual_adjustments'] ?? 0)); ?></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Correcciones de inventario</p>
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

        <section class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Comportamiento diario','subtitle' => 'Ingreso y utilidad generados por ventas de productos.','class' => 'report-surface-card report-chart-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Comportamiento diario','subtitle' => 'Ingreso y utilidad generados por ventas de productos.','class' => 'report-surface-card report-chart-card']); ?>
                <div class="chart-shell">
                    <canvas id="salesInventoryChart"></canvas>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Atajos del módulo','subtitle' => 'Accesos directos para operación rápida.','class' => 'report-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Atajos del módulo','subtitle' => 'Accesos directos para operación rápida.','class' => 'report-surface-card']); ?>
                <div class="space-y-3">
                    <article class="report-action-card rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Panel comercial</p>
                        <p class="mt-1 text-sm text-cyan-800 dark:text-cyan-100">Vuelve al centro operativo para registrar ventas nuevas.</p>
                        <div class="mt-3">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $routeParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $routeParams)),'variant' => 'secondary']); ?>Abrir ventas e inventario <?php echo $__env->renderComponent(); ?>
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
                    </article>
                    <article class="report-action-card rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Productos</p>
                        <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-100">Ajusta stock, precios y catalogo sin mezclarlo con clientes.</p>
                        <div class="mt-3">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('products.index', $routeParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index', $routeParams)),'variant' => 'ghost']); ?>Ir a productos <?php echo $__env->renderComponent(); ?>
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
                    </article>
                    <article class="report-action-card rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Alertas activas</p>
                        <p class="mt-2 text-2xl font-black text-violet-800 dark:text-violet-100"><?php echo e($lowStockProducts->count()); ?></p>
                        <p class="text-xs text-violet-700 dark:text-violet-200">Productos activos en stock bajo.</p>
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

        <section class="grid gap-4 xl:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Top productos','subtitle' => 'Artículos con mejor salida y mejor ingreso del período.','class' => 'report-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Top productos','subtitle' => 'Artículos con mejor salida y mejor ingreso del período.','class' => 'report-surface-card']); ?>
                <div class="report-table-shell table-wrap table-mobile-stack">
                    <table class="ui-table w-full min-w-[720px] text-sm">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <?php if($showGymColumn): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Categoria</th>
                            <th>Unidades</th>
                            <th>Ingreso</th>
                            <th>Utilidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="Producto" class="font-semibold"><?php echo e($product->product_name); ?></td>
                                <?php if($showGymColumn): ?>
                                    <td data-label="Sede"><?php echo e($product->gym_name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td data-label="Categoria"><?php echo e($product->product_category ?: '-'); ?></td>
                                <td data-label="Unidades"><?php echo e((int) $product->units_sold); ?></td>
                                <td data-label="Ingreso" class="text-emerald-700 dark:text-emerald-300"><?php echo e($currencyFormatter::format((float) $product->total_revenue, $appCurrencyCode)); ?></td>
                                <td data-label="Utilidad" class="text-violet-700 dark:text-violet-300"><?php echo e($currencyFormatter::format((float) $product->total_profit, $appCurrencyCode)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($showGymColumn ? 6 : 5); ?>" class="report-empty-state py-8 text-center ui-muted">No hay ventas de productos en este rango.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Stock bajo','subtitle' => 'Productos activos que necesitan reposicion.','class' => 'report-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Stock bajo','subtitle' => 'Productos activos que necesitan reposicion.','class' => 'report-surface-card']); ?>
                <div class="report-table-shell table-wrap table-mobile-stack">
                    <table class="ui-table w-full min-w-[660px] text-sm">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <?php if($showGymColumn): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Categoria</th>
                            <th>Stock</th>
                            <th>Mínimo</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="Producto" class="font-semibold"><?php echo e($product->name); ?></td>
                                <?php if($showGymColumn): ?>
                                    <td data-label="Sede"><?php echo e($product->gym_name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td data-label="Categoria"><?php echo e($product->category ?: '-'); ?></td>
                                <td data-label="Stock" class="text-amber-700 dark:text-amber-300 font-bold"><?php echo e((int) $product->stock); ?></td>
                                <td data-label="Mínimo"><?php echo e((int) $product->min_stock); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($showGymColumn ? 5 : 4); ?>" class="report-empty-state py-8 text-center ui-muted">No hay alertas de stock bajo.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Detalle de ventas del período','class' => 'report-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Detalle de ventas del período','class' => 'report-surface-card']); ?>
            <?php if($recentSales): ?>
                <div class="report-table-shell table-wrap table-mobile-stack">
                    <table class="ui-table w-full min-w-[1180px] text-sm">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <?php if($showGymColumn): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Producto</th>
                            <th>Cliente</th>
                            <th>Usuario</th>
                            <th>Metodo</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Costo</th>
                            <th>Utilidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="Fecha"><?php echo e($sale->sold_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <?php if($showGymColumn): ?>
                                    <td data-label="Sede"><?php echo e($sale->gym?->name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td data-label="Producto">
                                    <div class="font-semibold"><?php echo e($sale->product?->name ?? '-'); ?></div>
                                    <div class="ui-muted text-xs"><?php echo e($sale->product?->category ?: 'Sin categoria'); ?></div>
                                </td>
                                <td data-label="Cliente"><?php echo e($sale->client?->full_name ?? 'Venta sin cliente'); ?></td>
                                <td data-label="Usuario"><?php echo e($sale->soldBy?->name ?? '-'); ?></td>
                                <td data-label="Metodo"><?php echo e($methodLabels[$sale->payment_method] ?? $sale->payment_method); ?></td>
                                <td data-label="Cantidad"><?php echo e((int) $sale->quantity); ?></td>
                                <td data-label="Total" class="text-emerald-700 dark:text-emerald-300 font-bold"><?php echo e($currencyFormatter::format((float) $sale->total_amount, $appCurrencyCode)); ?></td>
                                <td data-label="Costo" class="text-rose-700 dark:text-rose-300 font-bold"><?php echo e($currencyFormatter::format((float) $sale->total_cost, $appCurrencyCode)); ?></td>
                                <td data-label="Utilidad" class="text-violet-700 dark:text-violet-300 font-bold"><?php echo e($currencyFormatter::format((float) $sale->total_profit, $appCurrencyCode)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($showGymColumn ? 10 : 9); ?>" class="report-empty-state py-8 text-center ui-muted">No hay ventas dentro del rango seleccionado.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <?php echo e($recentSales->links()); ?>

                </div>
            <?php else: ?>
                <p class="ui-muted">El detalle estará disponible después de habilitar las tablas del módulo.</p>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const chartEl = document.getElementById('salesInventoryChart');
        if (!chartEl) return;

        const labels = <?php echo json_encode($chartLabels, 15, 512) ?>;
        const revenue = <?php echo json_encode($chartRevenue, 15, 512) ?>;
        const profit = <?php echo json_encode($chartProfit, 15, 512) ?>;

        new Chart(chartEl, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingreso',
                        data: revenue,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        fill: true,
                        tension: 0.28,
                    },
                    {
                        label: 'Utilidad',
                        data: profit,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        fill: true,
                        tension: 0.28,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/reports/sales-inventory.blade.php ENDPATH**/ ?>