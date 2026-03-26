<?php $__env->startSection('title', 'Ganancias de clientes'); ?>
<?php $__env->startSection('page-title', 'Ganancias de clientes'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .report-client-earnings .report-client-surface-card {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border-color: rgb(148 163 184 / 0.22);
        background: linear-gradient(162deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 24px 42px -34px rgb(15 23 42 / 0.28), inset 0 1px 0 rgb(255 255 255 / 0.82);
        backdrop-filter: blur(10px);
    }

    .theme-dark .report-client-earnings .report-client-surface-card,
    .dark .report-client-earnings .report-client-surface-card {
        border-color: rgb(71 85 105 / 0.76);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
        box-shadow: 0 28px 46px -34px rgb(2 8 23 / 0.88), inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-client-earnings .report-client-surface-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, rgb(255 255 255 / 0.78), transparent 74%);
        opacity: 0.9;
        pointer-events: none;
    }

    .theme-dark .report-client-earnings .report-client-surface-card::before,
    .dark .report-client-earnings .report-client-surface-card::before {
        background: linear-gradient(90deg, rgb(255 255 255 / 0.08), transparent 74%);
    }

    .report-client-earnings .report-client-surface-card > * {
        position: relative;
        z-index: 1;
    }

    .report-client-earnings .filter-grid {
        align-items: end;
    }

    .report-client-earnings .report-client-filter-shell,
    .report-client-earnings .report-client-table-shell,
    .report-client-earnings .report-client-top-shell,
    .report-client-earnings .report-client-pagination-shell {
        border: 1px solid rgb(148 163 184 / 0.18);
        border-radius: 1rem;
        background: linear-gradient(160deg, rgb(255 255 255 / 0.76), rgb(241 245 249 / 0.84));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.72);
    }

    .theme-dark .report-client-earnings .report-client-filter-shell,
    .theme-dark .report-client-earnings .report-client-table-shell,
    .theme-dark .report-client-earnings .report-client-top-shell,
    .theme-dark .report-client-earnings .report-client-pagination-shell,
    .dark .report-client-earnings .report-client-filter-shell,
    .dark .report-client-earnings .report-client-table-shell,
    .dark .report-client-earnings .report-client-top-shell,
    .dark .report-client-earnings .report-client-pagination-shell {
        border-color: rgb(71 85 105 / 0.54);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.62), rgb(2 6 23 / 0.56));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-client-earnings .report-client-filter-shell,
    .report-client-earnings .report-client-top-shell {
        padding: 0.9rem;
    }

    .report-client-earnings .report-client-filter-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
        min-height: 2.85rem;
        padding: 0.72rem 0.8rem;
        border: 1px solid rgb(148 163 184 / 0.18);
        border-radius: 1rem;
        background: linear-gradient(160deg, rgb(255 255 255 / 0.72), rgb(241 245 249 / 0.82));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.72);
    }

    .theme-dark .report-client-earnings .report-client-filter-actions,
    .dark .report-client-earnings .report-client-filter-actions {
        border-color: rgb(71 85 105 / 0.52);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.62), rgb(2 6 23 / 0.56));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-client-earnings .metric-card {
        min-height: 100%;
        position: relative;
        overflow: hidden;
        border-color: rgb(148 163 184 / 0.22);
        background: linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.92));
        box-shadow: 0 20px 34px -30px rgb(15 23 42 / 0.24), inset 0 1px 0 rgb(255 255 255 / 0.8);
    }

    .theme-dark .report-client-earnings .metric-card,
    .dark .report-client-earnings .metric-card {
        border-color: rgb(71 85 105 / 0.72);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.9), rgb(2 6 23 / 0.82));
        box-shadow: 0 24px 38px -32px rgb(2 8 23 / 0.82), inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-client-earnings .metric-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 3px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.24);
    }

    .report-client-earnings .metric-card[data-tone='clients']::before {
        background: linear-gradient(90deg, rgb(59 130 246 / 0.92), rgb(59 130 246 / 0.24));
    }

    .report-client-earnings .metric-card[data-tone='revenue']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.92), rgb(16 185 129 / 0.24));
    }

    .report-client-earnings .metric-card[data-tone='operations']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / 0.92), rgb(6 182 212 / 0.24));
    }

    .report-client-earnings .metric-card[data-tone='average']::before {
        background: linear-gradient(90deg, rgb(139 92 246 / 0.92), rgb(139 92 246 / 0.24));
    }

    .report-client-earnings .top-client-wrap {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
    }

    .report-client-earnings .detail-table-wrap {
        border-radius: 0.85rem;
        overflow: auto;
    }

    .report-client-earnings .report-client-pagination-shell {
        margin-top: 1rem;
        padding: 0.72rem 0.85rem;
    }

    .report-client-earnings .detail-table-wrap .ui-table thead th {
        position: sticky;
        top: 0;
        z-index: 4;
        background: rgb(241 245 249 / 0.95);
        backdrop-filter: blur(4px);
    }

    .theme-dark .report-client-earnings .detail-table-wrap .ui-table thead th {
        background: rgb(30 41 59 / 0.95);
    }

    .report-client-earnings .report-client-empty-state {
        padding: 2rem 1rem;
        text-align: center;
        font-weight: 700;
        color: rgb(100 116 139);
    }

    .theme-dark .report-client-earnings .report-client-empty-state,
    .dark .report-client-earnings .report-client-empty-state {
        color: rgb(148 163 184);
    }

    @media (max-width: 640px) {
        .report-client-earnings .top-client-wrap {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 768px) {
        .report-client-earnings .ui-card {
            padding: 0.9rem;
        }

        .report-client-earnings .filter-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 0.65rem;
        }

        .report-client-earnings .report-client-filter-actions .ui-button {
            width: 100%;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $contextGym = (string) request()->route('contextGym');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $panelParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $baseRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $sourceFilter = (string) ($filters['source'] ?? 'all');
        $orderFilter = (string) ($filters['order'] ?? 'amount_desc');
        $userFilter = isset($filters['user_id']) ? (int) $filters['user_id'] : null;
        $resetFilterParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
    ?>

    <div class="report-client-earnings space-y-4">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-client-surface-card','title' => 'Filtro de facturación por cliente','subtitle' => 'Analiza cuánto se ha facturado por cliente en el rango seleccionado.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-client-surface-card','title' => 'Filtro de facturación por cliente','subtitle' => 'Analiza cuánto se ha facturado por cliente en el rango seleccionado.']); ?>
            <form method="GET" action="<?php echo e(route('reports.client-earnings', ['contextGym' => $contextGym])); ?>" class="report-client-filter-shell filter-grid grid gap-3 md:grid-cols-2 xl:grid-cols-6">
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

                <label class="space-y-1 text-sm font-semibold ui-muted xl:col-span-2">
                    <span>Cliente o documento</span>
                    <input type="text"
                           name="search"
                           value="<?php echo e((string) ($filters['search'] ?? '')); ?>"
                           class="ui-input"
                           placeholder="Nombre, apellido o documento">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Usuario</span>
                    <select name="user_id" class="ui-input">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e((int) $user->id); ?>" <?php if($userFilter === (int) $user->id): echo 'selected'; endif; ?>><?php echo e((string) ($user->name ?? 'Sin nombre')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Origen</span>
                    <select name="source" class="ui-input">
                        <option value="all" <?php if($sourceFilter === 'all'): echo 'selected'; endif; ?>>Todo</option>
                        <option value="membership" <?php if($sourceFilter === 'membership'): echo 'selected'; endif; ?>>Solo membresías</option>
                        <option value="sale" <?php if($sourceFilter === 'sale'): echo 'selected'; endif; ?>>Solo ventas de productos</option>
                        <option value="mixed" <?php if($sourceFilter === 'mixed'): echo 'selected'; endif; ?>>Clientes mixtos</option>
                    </select>
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Orden</span>
                    <select name="order" class="ui-input">
                        <option value="amount_desc" <?php if($orderFilter === 'amount_desc'): echo 'selected'; endif; ?>>Mayor facturación</option>
                        <option value="amount_asc" <?php if($orderFilter === 'amount_asc'): echo 'selected'; endif; ?>>Menor facturación</option>
                        <option value="last_desc" <?php if($orderFilter === 'last_desc'): echo 'selected'; endif; ?>>Última facturación reciente</option>
                        <option value="last_asc" <?php if($orderFilter === 'last_asc'): echo 'selected'; endif; ?>>Última facturación antigua</option>
                        <option value="name_asc" <?php if($orderFilter === 'name_asc'): echo 'selected'; endif; ?>>Nombre A-Z</option>
                        <option value="name_desc" <?php if($orderFilter === 'name_desc'): echo 'selected'; endif; ?>>Nombre Z-A</option>
                    </select>
                </label>

                <div class="report-client-filter-actions xl:col-span-5">
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
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.client-earnings', $resetFilterParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.client-earnings', $resetFilterParams)),'variant' => 'ghost']); ?>Limpiar filtros <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $panelParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $panelParams)),'variant' => 'ghost']); ?>Panel reportes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('panel.index', $baseRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('panel.index', $baseRouteParams)),'variant' => 'ghost']); ?>Volver al panel <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card','dataTone' => 'clients']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card','data-tone' => 'clients']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Clientes facturados</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) ($summary['billed_clients'] ?? 0)); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card','dataTone' => 'revenue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card','data-tone' => 'revenue']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Total facturado</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300"><?php echo e($currencyFormatter::format((float) ($summary['total_billed'] ?? 0), $appCurrencyCode)); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card','dataTone' => 'operations']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card','data-tone' => 'operations']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Operaciones</p>
                <p class="mt-2 text-3xl font-black text-cyan-700 dark:text-cyan-300"><?php echo e((int) ($summary['operations_count'] ?? 0)); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'metric-card','dataTone' => 'average']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'metric-card','data-tone' => 'average']); ?>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Promedio por cliente</p>
                <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300"><?php echo e($currencyFormatter::format((float) ($summary['average_per_client'] ?? 0), $appCurrencyCode)); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-client-surface-card','title' => 'Cliente con mayor facturación']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-client-surface-card','title' => 'Cliente con mayor facturación']); ?>
            <?php if($topClient): ?>
                <div class="report-client-top-shell">
                    <div class="top-client-wrap">
                        <div>
                            <p class="text-lg font-black"><?php echo e((string) ($topClient->client_name ?? 'Cliente')); ?></p>
                            <p class="text-sm ui-muted">Documento: <?php echo e((string) ($topClient->document_number ?? '-')); ?></p>
                            <?php if($showGymColumn): ?>
                                <p class="text-sm ui-muted">Sede: <?php echo e((string) ($topClient->gym_name ?? '-')); ?></p>
                            <?php endif; ?>
                            <p class="text-sm ui-muted">Última facturación: <?php echo e($topClient->last_billed_at ? \Carbon\Carbon::parse((string) $topClient->last_billed_at)->format('Y-m-d H:i') : '-'); ?></p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success']); ?><?php echo e($currencyFormatter::format((float) ($topClient->total_billed ?? 0), $appCurrencyCode)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="report-client-empty-state ui-muted">No hay clientes facturados con los filtros actuales.</p>
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

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-client-surface-card','title' => 'Detalle por cliente']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-client-surface-card','title' => 'Detalle por cliente']); ?>
            <p class="mb-3 text-sm ui-muted">
                Mostrando
                <strong><?php echo e($clients->firstItem() ?? 0); ?></strong>
                a
                <strong><?php echo e($clients->lastItem() ?? 0); ?></strong>
                de
                <strong><?php echo e($clients->total()); ?></strong>
                registros (50 por página).
            </p>

            <div class="report-client-table-shell detail-table-wrap table-mobile-stack">
                <table class="ui-table w-full min-w-[1080px] text-sm">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Documento</th>
                        <?php if($showGymColumn): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Total facturado</th>
                        <th>Membresías</th>
                        <th>Ventas de productos</th>
                        <th>Operaciones</th>
                        <th>Última facturación</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="Cliente" class="font-semibold"><?php echo e((string) ($client->client_name ?? '-')); ?></td>
                            <td data-label="Documento"><?php echo e((string) ($client->document_number ?? '-')); ?></td>
                            <?php if($showGymColumn): ?>
                                <td data-label="Sede"><?php echo e((string) ($client->gym_name ?? '-')); ?></td>
                            <?php endif; ?>
                            <td data-label="Total facturado" class="font-bold text-emerald-700 dark:text-emerald-300"><?php echo e($currencyFormatter::format((float) ($client->total_billed ?? 0), $appCurrencyCode)); ?></td>
                            <td data-label="Membresías"><?php echo e($currencyFormatter::format((float) ($client->memberships_billed ?? 0), $appCurrencyCode)); ?></td>
                            <td data-label="Ventas productos"><?php echo e($currencyFormatter::format((float) ($client->sales_billed ?? 0), $appCurrencyCode)); ?></td>
                            <td data-label="Operaciones"><?php echo e((int) ($client->operations_count ?? 0)); ?></td>
                            <td data-label="Última facturación"><?php echo e($client->last_billed_at ? \Carbon\Carbon::parse((string) $client->last_billed_at)->format('Y-m-d H:i') : '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($showGymColumn ? 8 : 7); ?>" class="report-client-empty-state py-8 text-center ui-muted">No hay datos para el rango y filtros seleccionados.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-client-pagination-shell mt-4">
                <?php echo e($clients->links()); ?>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/reports/client-earnings.blade.php ENDPATH**/ ?>