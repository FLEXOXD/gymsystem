<?php $__env->startSection('title', 'Productos'); ?>
<?php $__env->startSection('page-title', 'Productos'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .products-ops-nav {
        display: none;
    }

    .products-ops-grid {
        align-items: start;
    }

    @media (max-width: 1023px) {
        .products-ops-nav {
            position: sticky;
            top: calc(4.7rem + env(safe-area-inset-top));
            z-index: 14;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.45rem;
        }

        .products-ops-nav .ui-button {
            width: 100%;
            min-height: 2.45rem;
            padding: 0.55rem 0.65rem;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .products-ops-grid {
            gap: 0.75rem;
        }

        .products-ops-grid > :nth-child(2) {
            order: -1;
        }
    }

    @media (max-width: 640px) {
        .products-ops-nav {
            top: calc(4.35rem + env(safe-area-inset-top));
        }

        .products-stock-submit {
            position: sticky;
            bottom: calc(5.8rem + env(safe-area-inset-bottom));
            z-index: 10;
            padding-top: 0.25rem;
            background: linear-gradient(180deg, rgb(2 6 23 / 0), rgb(2 6 23 / 0.76) 42%, rgb(2 6 23 / 0.94));
        }

        .theme-light .products-stock-submit {
            background: linear-gradient(180deg, rgb(248 250 252 / 0), rgb(248 250 252 / 0.85) 42%, rgb(248 250 252 / 0.95));
        }
    }

    .products-page .products-surface-card {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(148 163 184 / 0.26);
        background:
            radial-gradient(circle at top right, rgb(34 197 94 / 0.08), transparent 28%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 26px 44px -38px rgb(15 23 42 / 0.18);
    }

    .products-page .products-surface-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.76), transparent);
        opacity: 0.82;
        pointer-events: none;
    }

    .products-page .products-surface-card > * {
        position: relative;
        z-index: 1;
    }

    .products-page .products-surface-card > header .ui-heading {
        font-size: clamp(1.16rem, 1.4vw, 1.42rem);
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .products-page .products-surface-card > header .ui-muted {
        margin-top: 0.35rem;
        font-size: 0.88rem;
    }

    .products-page .products-kpi-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgb(148 163 184 / 0.24);
        box-shadow: 0 18px 28px -30px rgb(15 23 42 / 0.2);
    }

    .products-page .products-kpi-card::before {
        content: '';
        position: absolute;
        left: 1rem;
        right: 1rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .products-page .products-nav-card .ui-button,
    .products-page .products-ops-nav .ui-button {
        border-radius: 0.92rem;
    }

    .products-page .products-form-shell {
        border: 1px solid rgb(148 163 184 / 0.22);
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.82), rgb(248 250 252 / 0.74));
        padding: 1rem;
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.8),
            0 18px 32px -34px rgb(15 23 42 / 0.16);
    }

    .products-page .ui-input.is-invalid,
    .products-page .ui-input[aria-invalid="true"] {
        border-color: rgb(244 63 94 / 0.82);
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.24),
            0 0 0 3px rgb(244 63 94 / 0.18);
    }

    .products-field-error {
        display: block;
        font-size: 0.78rem;
        font-weight: 700;
        color: rgb(190 24 93);
    }

    .theme-dark .products-field-error,
    .dark .products-field-error {
        color: rgb(251 207 232);
    }

    .products-page .products-filter-shell,
    .products-page .products-pagination-shell {
        border: 1px solid rgb(148 163 184 / 0.22);
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.8), rgb(248 250 252 / 0.72));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.8),
            0 16px 28px -32px rgb(15 23 42 / 0.14);
    }

    .products-page .products-filter-shell {
        padding: 0.9rem 1rem;
    }

    .products-page .products-pagination-shell {
        padding: 0.85rem 1rem;
    }

    .products-page .products-table-shell {
        border: 1px solid rgb(148 163 184 / 0.24);
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.94), rgb(248 250 252 / 0.88));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.82),
            0 18px 32px -34px rgb(15 23 42 / 0.16);
    }

    .products-page .products-empty-state {
        padding-top: 2.4rem;
        padding-bottom: 2.4rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .theme-light .products-page .products-surface-card,
    .theme-light .products-page .products-kpi-card,
    .theme-light .products-page .products-form-shell,
    .theme-light .products-page .products-filter-shell,
    .theme-light .products-page .products-pagination-shell,
    .theme-light .products-page .products-table-shell {
        border-color: rgb(203 213 225 / 0.82);
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.9),
            0 18px 30px -32px rgb(15 23 42 / 0.1);
    }

    .theme-dark .products-page .products-surface-card,
    .dark .products-page .products-surface-card,
    .theme-dark .products-page .products-kpi-card,
    .dark .products-page .products-kpi-card,
    .theme-dark .products-page .products-form-shell,
    .dark .products-page .products-form-shell,
    .theme-dark .products-page .products-filter-shell,
    .dark .products-page .products-filter-shell,
    .theme-dark .products-page .products-pagination-shell,
    .dark .products-page .products-pagination-shell,
    .theme-dark .products-page .products-table-shell,
    .dark .products-page .products-table-shell {
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
        $isGlobalScope = (bool) ($isGlobalScope ?? false);
        $activeGymId = (int) ($activeGymId ?? request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canViewSalesReports = auth()->user()?->isOwner()
            && $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'sales_inventory_reports');
        $indexRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $productsCollection = method_exists($products, 'getCollection') ? $products->getCollection() : collect($products);
        $stockProductId = (int) ($stockProductId ?? 0);
        $editingProduct = $editingProduct instanceof \App\Models\Product ? $editingProduct : null;
        $hasOpenCashSession = (bool) ($hasOpenCashSession ?? false);
        $productFieldErrors = ['name', 'sku', 'barcode', 'category', 'sale_price', 'cost_price', 'initial_stock', 'initial_payment_method', 'min_stock', 'status', 'description', 'products'];
        $hasProductFormErrors = collect($productFieldErrors)->contains(fn (string $key): bool => $errors->has($key));
        $stockFieldErrors = ['product_id', 'movement_type', 'quantity', 'unit_cost', 'payment_method', 'note', 'stock'];
        $hasStockFormErrors = collect($stockFieldErrors)->contains(fn (string $key): bool => $errors->has($key));
        $stockProductError = (string) $errors->first('product_id');
        $stockProductHelpText = $stockProductError !== '' ? $stockProductError : 'Selecciona el producto antes de guardar el movimiento.';
        $productModalRequestedMode = old('_product_form_mode');
        $productModalMode = in_array($productModalRequestedMode, ['create', 'edit'], true)
            ? $productModalRequestedMode
            : ($editingProduct ? 'edit' : 'create');
        $productEditId = (int) old('edit_product_id', $editingProduct?->id ?? 0);
        $productModalOpen = $schemaReady && ! $isGlobalScope && (in_array($productModalRequestedMode, ['create', 'edit'], true) || $editingProduct instanceof \App\Models\Product);
        $stockModalOpen = $schemaReady && ! $isGlobalScope && ($hasStockFormErrors || $stockProductId > 0 || (string) old('_stock_modal_open') === '1');
    ?>

    <div class="products-page space-y-5">
        <div class="space-y-2">
            <?php if(session('status')): ?>
                <div class="ui-alert ui-alert-success"><?php echo e(session('status')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="ui-alert ui-alert-danger"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>
            <?php if($isGlobalScope): ?>
                <div class="ui-alert ui-alert-info">Vista global activa: puedes analizar el catalogo consolidado, pero la carga y edicion se hacen desde una sede especifica.</div>
            <?php endif; ?>
            <?php if(! $schemaReady): ?>
                <div class="ui-alert ui-alert-warning">Falta ejecutar <code>php artisan migrate</code> para activar el modulo de productos.</div>
            <?php endif; ?>
            <?php if($schemaReady && ! $isGlobalScope && ! $hasOpenCashSession): ?>
                <div class="ui-alert ui-alert-warning">
                    Caja cerrada: las entradas de inventario con costo (compra/stock inicial) requieren caja abierta.
                    <a class="underline font-semibold" href="<?php echo e(route('cash.index', ['contextGym' => $contextGym])); ?>">Abrir caja</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Navegacion del modulo','subtitle' => 'Productos queda separado del panel financiero y del panel de clientes.','class' => 'products-surface-card products-nav-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Navegacion del modulo','subtitle' => 'Productos queda separado del panel financiero y del panel de clientes.','class' => 'products-surface-card products-nav-card']); ?>
            <div class="flex flex-wrap gap-2">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('panel.index', $indexRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('panel.index', $indexRouteParams)),'variant' => 'ghost']); ?>Ganancias del gimnasio <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $indexRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $indexRouteParams)),'variant' => 'ghost']); ?>Panel de clientes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $indexRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $indexRouteParams)),'variant' => 'secondary']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if($canViewSalesReports && \Illuminate\Support\Facades\Route::has('reports.sales-inventory')): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Reportes del modulo <?php echo $__env->renderComponent(); ?>
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

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'products-kpi-card','dataTone' => 'neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'products-kpi-card','data-tone' => 'neutral']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Productos</p>
                <p class="mt-2 text-3xl font-black"><?php echo e((int) ($productStats['total'] ?? 0)); ?></p>
                <p class="ui-muted mt-2 text-sm">Catalogo total disponible.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'products-kpi-card','dataTone' => 'neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'products-kpi-card','data-tone' => 'neutral']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Activos</p>
                <p class="mt-2 text-3xl font-black text-emerald-600"><?php echo e((int) ($productStats['active'] ?? 0)); ?></p>
                <p class="ui-muted mt-2 text-sm">Listos para vender.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'products-kpi-card','dataTone' => 'neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'products-kpi-card','data-tone' => 'neutral']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Unidades en stock</p>
                <p class="mt-2 text-3xl font-black text-cyan-600"><?php echo e((int) ($productStats['stock_units'] ?? 0)); ?></p>
                <p class="ui-muted mt-2 text-sm">Existencia total cargada.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'products-kpi-card','dataTone' => 'neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'products-kpi-card','data-tone' => 'neutral']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Stock bajo</p>
                <p class="mt-2 text-3xl font-black text-amber-600"><?php echo e((int) ($productStats['low_stock'] ?? 0)); ?></p>
                <p class="ui-muted mt-2 text-sm">Productos por reponer.</p>
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

        <?php if($schemaReady && ! $isGlobalScope): ?>
            <div class="products-ops-nav lg:hidden">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','size' => 'sm','class' => 'js-open-products-stock','ariaHaspopup' => 'dialog','ariaControls' => 'products-ops-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','size' => 'sm','class' => 'js-open-products-stock','aria-haspopup' => 'dialog','aria-controls' => 'products-ops-modal']); ?>Mover stock <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','class' => 'js-open-products-create','ariaHaspopup' => 'dialog','ariaControls' => 'products-ops-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','class' => 'js-open-products-create','aria-haspopup' => 'dialog','aria-controls' => 'products-ops-modal']); ?>Registrar producto <?php echo $__env->renderComponent(); ?>
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

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Acciones rapidas','subtitle' => 'Gestiona productos y stock sin recargar la pagina.','class' => 'products-surface-card products-nav-card hidden lg:block']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Acciones rapidas','subtitle' => 'Gestiona productos y stock sin recargar la pagina.','class' => 'products-surface-card products-nav-card hidden lg:block']); ?>
                <div class="flex flex-wrap gap-2">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','class' => 'js-open-products-stock','ariaHaspopup' => 'dialog','ariaControls' => 'products-ops-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','class' => 'js-open-products-stock','aria-haspopup' => 'dialog','aria-controls' => 'products-ops-modal']); ?>Mover stock <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','class' => 'js-open-products-create','ariaHaspopup' => 'dialog','ariaControls' => 'products-ops-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','class' => 'js-open-products-create','aria-haspopup' => 'dialog','aria-controls' => 'products-ops-modal']); ?>Registrar producto <?php echo $__env->renderComponent(); ?>
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

            <div id="products-ops-modal" class="ui-modal-backdrop <?php echo e($productModalOpen || $stockModalOpen ? '' : 'hidden'); ?>" role="dialog" aria-modal="true" aria-labelledby="products-ops-modal-title" aria-hidden="<?php echo e($productModalOpen || $stockModalOpen ? 'false' : 'true'); ?>">
                <div class="ui-modal-shell w-full max-w-6xl" data-products-ops-dialog tabindex="-1">
                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 id="products-ops-modal-title" class="text-xl font-black text-slate-100">Gestionar productos</h3>
                            <p class="mt-1 text-sm text-slate-400">Edita productos y mueve stock sin cambiar de pantalla.</p>
                        </div>
                        <button type="button" class="ui-button ui-button-ghost px-2 py-1 text-sm" data-close-products-ops aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body px-5 py-5">
                        <section class="products-ops-grid grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => $editingProduct ? 'Editar producto' : 'Registrar producto','subtitle' => 'Define precio, costo, categoría y stock mínimo del producto.','class' => 'products-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editingProduct ? 'Editar producto' : 'Registrar producto'),'subtitle' => 'Define precio, costo, categoría y stock mínimo del producto.','class' => 'products-surface-card']); ?>
                    <form id="product-create-form" method="POST" action="<?php echo e($productModalMode === 'edit' && $productEditId > 0 ? route('products.update', ['contextGym' => $contextGym, 'product' => $productEditId]) : route('products.store', ['contextGym' => $contextGym])); ?>" class="products-form-shell grid gap-4 md:grid-cols-2" data-store-action="<?php echo e(route('products.store', ['contextGym' => $contextGym])); ?>">
                        <?php echo csrf_field(); ?>
                        <input id="product-form-mode" type="hidden" name="_product_form_mode" value="<?php echo e($productModalMode); ?>">
                        <input id="product-edit-id" type="hidden" name="edit_product_id" value="<?php echo e($productEditId > 0 ? $productEditId : ''); ?>">
                        <input id="product-form-method" type="hidden" name="_method" value="PUT" <?php if(! ($editingProduct || ($productModalMode === 'edit' && $productEditId > 0))): ?> disabled <?php endif; ?>>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Nombre</span>
                            <input type="text" name="name" class="ui-input" required value="<?php echo e(old('name', $editingProduct?->name)); ?>" placeholder="Ej: Proteina whey 2lb">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>SKU interno</span>
                            <input type="text" name="sku" class="ui-input" value="<?php echo e(old('sku', $editingProduct?->sku)); ?>" placeholder="Dejalo vacio para generarlo">
                            <span class="block text-xs font-normal ui-muted">Se genera automatico segun categoria, nombre e ID si no lo escribes.</span>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Codigo de barras</span>
                            <input id="product-barcode-input" type="text" name="barcode" class="ui-input" value="<?php echo e(old('barcode', $editingProduct?->barcode)); ?>" placeholder="Escanea o dejalo vacio para generarlo">
                            <span class="block text-xs font-normal ui-muted">Puedes usar lector directo aqui o el boton flotante de escaneo en vivo.</span>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Categoria</span>
                            <input type="text" name="category" class="ui-input" value="<?php echo e(old('category', $editingProduct?->category)); ?>" placeholder="Bebidas, suplementos, accesorios">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Precio de venta</span>
                            <input type="number" step="0.01" min="0" name="sale_price" class="ui-input" required value="<?php echo e(old('sale_price', $editingProduct ? number_format((float) $editingProduct->sale_price, 2, '.', '') : '')); ?>">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Costo unitario</span>
                            <input type="number" step="0.01" min="0" name="cost_price" class="ui-input" value="<?php echo e(old('cost_price', $editingProduct ? number_format((float) $editingProduct->cost_price, 2, '.', '') : '')); ?>">
                        </label>

                        <?php if(! $editingProduct): ?>
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Stock inicial</span>
                                <input type="number" min="0" name="initial_stock" class="ui-input" value="<?php echo e(old('initial_stock', 0)); ?>">
                                <span class="block text-xs font-normal ui-muted">Si es mayor a 0 se registra compra de inventario y gasto en caja.</span>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Metodo pago stock inicial</span>
                                <select name="initial_payment_method" class="ui-input">
                                    <option value="cash" <?php if(old('initial_payment_method', 'cash') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                                    <option value="card" <?php if(old('initial_payment_method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                                    <option value="transfer" <?php if(old('initial_payment_method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                                </select>
                            </label>
                        <?php endif; ?>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Stock mínimo</span>
                            <input type="number" min="0" name="min_stock" class="ui-input" value="<?php echo e(old('min_stock', $editingProduct?->min_stock ?? 0)); ?>">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Estado</span>
                            <select name="status" class="ui-input">
                                <option value="active" <?php if(old('status', $editingProduct?->status ?? 'active') === 'active'): echo 'selected'; endif; ?>>Activo</option>
                                <option value="inactive" <?php if(old('status', $editingProduct?->status) === 'inactive'): echo 'selected'; endif; ?>>Inactivo</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Descripcion</span>
                            <textarea name="description" rows="3" class="ui-input" placeholder="Notas internas del producto"><?php echo e(old('description', $editingProduct?->description)); ?></textarea>
                        </label>

                        <div class="flex flex-wrap gap-2 md:col-span-2">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'product-form-submit','type' => 'submit','class' => 'w-full sm:w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'product-form-submit','type' => 'submit','class' => 'w-full sm:w-auto']); ?><?php echo e($editingProduct ? 'Guardar cambios' : 'Crear producto'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php if($editingProduct): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('products.index', $indexRouteParams),'variant' => 'ghost','class' => 'w-full sm:w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index', $indexRouteParams)),'variant' => 'ghost','class' => 'w-full sm:w-auto']); ?>Cancelar edicion <?php echo $__env->renderComponent(); ?>
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

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Mover stock','subtitle' => 'Registra entradas o ajustes manuales de inventario.','class' => 'products-surface-card self-start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Mover stock','subtitle' => 'Registra entradas o ajustes manuales de inventario.','class' => 'products-surface-card self-start']); ?>
                    <form id="stock-form" method="POST" action="<?php echo e(route('products.stock', ['contextGym' => $contextGym])); ?>" class="products-form-shell products-stock-form scroll-mt-28 space-y-4" novalidate>
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_stock_modal_open" value="1">

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Producto</span>
                            <select
                                id="stock-product-id"
                                name="product_id"
                                required
                                class="ui-input <?php echo e($stockProductError !== '' ? 'is-invalid' : ''); ?>"
                                aria-invalid="<?php echo e($stockProductError !== '' ? 'true' : 'false'); ?>"
                                aria-describedby="stock-product-help"
                            >
                                <option value="">Selecciona un producto</option>
                                <?php $__currentLoopData = $stockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($productOption->id); ?>" <?php if((int) old('product_id', $stockProductId) === (int) $productOption->id): echo 'selected'; endif; ?>>
                                        <?php echo e($productOption->name); ?> | SKU <?php echo e($productOption->sku ?: '---'); ?> | BAR <?php echo e($productOption->barcode ?: '---'); ?> | stock <?php echo e((int) $productOption->stock); ?> | <?php echo e((string) $productOption->status); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <span
                                id="stock-product-help"
                                class="block text-xs font-normal <?php echo e($stockProductError !== '' ? 'products-field-error' : 'ui-muted'); ?>"
                                data-default-text="Selecciona el producto antes de guardar el movimiento."
                            ><?php echo e($stockProductHelpText); ?></span>
                        </label>

                        <div class="products-form-shell grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Movimiento</span>
                                <select name="movement_type" class="ui-input">
                                    <option value="entry" <?php if(old('movement_type') === 'entry'): echo 'selected'; endif; ?>>Entrada</option>
                                    <option value="adjustment_add" <?php if(old('movement_type') === 'adjustment_add'): echo 'selected'; endif; ?>>Ajuste positivo</option>
                                    <option value="adjustment_remove" <?php if(old('movement_type') === 'adjustment_remove'): echo 'selected'; endif; ?>>Ajuste negativo</option>
                                </select>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Cantidad</span>
                                <input type="number" min="1" name="quantity" class="ui-input" value="<?php echo e(old('quantity', 1)); ?>">
                            </label>
                        </div>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Costo unitario opcional</span>
                            <input type="number" step="0.01" min="0" name="unit_cost" class="ui-input" value="<?php echo e(old('unit_cost')); ?>">
                            <span class="block text-xs font-normal ui-muted">Obligatorio para Entradas. Genera gasto en caja segun cantidad x costo.</span>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Metodo de pago</span>
                            <select name="payment_method" class="ui-input">
                                <option value="cash" <?php if(old('payment_method', 'cash') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                                <option value="card" <?php if(old('payment_method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                                <option value="transfer" <?php if(old('payment_method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                            </select>
                            <span class="block text-xs font-normal ui-muted">Se usa para Entradas; en ajustes se conserva para historial.</span>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Nota</span>
                            <textarea name="note" rows="3" class="ui-input" placeholder="Ej: reposicion semanal o ajuste por conteo"><?php echo e(old('note')); ?></textarea>
                            <span class="block text-xs font-normal ui-muted">Obligatoria para ajustes manuales (+/-).</span>
                        </label>

                        <div class="products-stock-submit">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','class' => 'w-full sm:w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'w-full sm:w-auto']); ?>Guardar movimiento <?php echo $__env->renderComponent(); ?>
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
                        </section>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Catalogo de productos','subtitle' => 'Vista operativa del inventario actual del gimnasio.','class' => 'products-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Catalogo de productos','subtitle' => 'Vista operativa del inventario actual del gimnasio.','class' => 'products-surface-card']); ?>
            <div class="products-filter-shell mb-4 flex flex-wrap items-end justify-between gap-3">
                <form method="GET" action="<?php echo e(route('products.index', ['contextGym' => $contextGym])); ?>" class="flex flex-wrap items-end gap-2">
                    <?php if($isGlobalScope): ?>
                        <input type="hidden" name="scope" value="global">
                    <?php endif; ?>
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Buscar</span>
                        <input type="text" name="q" class="ui-input min-w-[240px]" value="<?php echo e($search); ?>" placeholder="Nombre, SKU, codigo o categoria">
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
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?>Filtrar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </form>
            </div>

            <div class="products-table-shell smart-list-wrap">
                <table class="ui-table w-full min-w-[960px] text-sm">
                    <thead>
                        <tr>
                            <?php if($showGymColumn): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Producto</th>
                            <th>Codigo</th>
                            <th>Categoria</th>
                            <th>Venta</th>
                            <th>Costo</th>
                            <th>Stock</th>
                            <th>Minimo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $productsCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <?php if($showGymColumn): ?>
                                    <td><?php echo e($product->gym?->name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td>
                                    <div class="font-semibold"><?php echo e($product->name); ?></div>
                                    <div class="ui-muted text-xs"><?php echo e($product->description ? \Illuminate\Support\Str::limit($product->description, 56) : 'Sin descripcion'); ?></div>
                                </td>
                                <td>
                                    <div class="text-xs font-semibold">SKU: <?php echo e($product->sku ?: 'Automatico pendiente'); ?></div>
                                    <div class="ui-muted text-xs">BAR: <?php echo e($product->barcode ?: 'Automatico pendiente'); ?></div>
                                </td>
                                <td><?php echo e($product->category ?: '-'); ?></td>
                                <td><?php echo e($currencyFormatter::format((float) $product->sale_price, $appCurrencyCode)); ?></td>
                                <td><?php echo e($currencyFormatter::format((float) $product->cost_price, $appCurrencyCode)); ?></td>
                                <td class="<?php echo e((int) $product->stock <= (int) $product->min_stock ? 'text-amber-600 font-bold' : ''); ?>"><?php echo e((int) $product->stock); ?></td>
                                <td><?php echo e((int) $product->min_stock); ?></td>
                                <td>
                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => (string) $product->status === 'active' ? 'success' : 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $product->status === 'active' ? 'success' : 'muted')]); ?>
                                        <?php echo e((string) $product->status === 'active' ? 'Activo' : 'Inactivo'); ?>

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
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <?php if(! $isGlobalScope): ?>
                                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'js-product-edit-trigger','ariaHaspopup' => 'dialog','ariaControls' => 'products-ops-modal','dataProductId' => ''.e((int) $product->id).'','dataUpdateUrl' => ''.e(route('products.update', ['contextGym' => $contextGym, 'product' => $product->id])).'','dataProductName' => ''.e($product->name).'','dataProductSku' => ''.e($product->sku ?: '').'','dataProductBarcode' => ''.e($product->barcode ?: '').'','dataProductCategory' => ''.e($product->category ?: '').'','dataProductSalePrice' => ''.e(number_format((float) $product->sale_price, 2, '.', '')).'','dataProductCostPrice' => ''.e(number_format((float) $product->cost_price, 2, '.', '')).'','dataProductMinStock' => ''.e((int) $product->min_stock).'','dataProductStatus' => ''.e((string) $product->status).'','dataProductDescription' => ''.e($product->description ?: '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'js-product-edit-trigger','aria-haspopup' => 'dialog','aria-controls' => 'products-ops-modal','data-product-id' => ''.e((int) $product->id).'','data-update-url' => ''.e(route('products.update', ['contextGym' => $contextGym, 'product' => $product->id])).'','data-product-name' => ''.e($product->name).'','data-product-sku' => ''.e($product->sku ?: '').'','data-product-barcode' => ''.e($product->barcode ?: '').'','data-product-category' => ''.e($product->category ?: '').'','data-product-sale-price' => ''.e(number_format((float) $product->sale_price, 2, '.', '')).'','data-product-cost-price' => ''.e(number_format((float) $product->cost_price, 2, '.', '')).'','data-product-min-stock' => ''.e((int) $product->min_stock).'','data-product-status' => ''.e((string) $product->status).'','data-product-description' => ''.e($product->description ?: '').'']); ?>Editar <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'js-product-stock-trigger','ariaHaspopup' => 'dialog','ariaControls' => 'products-ops-modal','dataProductId' => ''.e((int) $product->id).'','dataProductName' => ''.e($product->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'js-product-stock-trigger','aria-haspopup' => 'dialog','aria-controls' => 'products-ops-modal','data-product-id' => ''.e((int) $product->id).'','data-product-name' => ''.e($product->name).'']); ?>Stock <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                            <form method="POST" action="<?php echo e(route('products.toggle', ['contextGym' => $contextGym, 'product' => $product->id])); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => (string) $product->status === 'active' ? 'danger' : 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $product->status === 'active' ? 'danger' : 'secondary')]); ?>
                                                    <?php echo e((string) $product->status === 'active' ? 'Desactivar' : 'Activar'); ?>

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
                                            </form>
                                        <?php else: ?>
                                            <span class="ui-muted text-xs">Solo lectura</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($showGymColumn ? 10 : 9); ?>" class="products-empty-state py-8 text-center ui-muted">Todavia no hay productos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if(method_exists($products, 'links')): ?>
                <div class="products-pagination-shell mt-4">
                    <?php echo e($products->links()); ?>

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

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Ultimos movimientos de stock','subtitle' => 'Historial reciente de entradas, ajustes y salidas por venta.','class' => 'products-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Ultimos movimientos de stock','subtitle' => 'Historial reciente de entradas, ajustes y salidas por venta.','class' => 'products-surface-card']); ?>
            <div class="products-table-shell smart-list-wrap">
                <table class="ui-table w-full min-w-[860px] text-sm">
                    <thead>
                        <tr>
                            <?php if($showGymColumn): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cambio</th>
                            <th>Antes</th>
                            <th>Despues</th>
                            <th>Caja</th>
                            <th>Usuario</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <?php if($showGymColumn): ?>
                                    <td><?php echo e($movement->gym?->name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td><?php echo e($movement->occurred_at?->format('Y-m-d H:i')); ?></td>
                                <td>
                                    <div class="font-semibold"><?php echo e($movement->product?->name ?? '-'); ?></div>
                                    <div class="ui-muted text-xs"><?php echo e($movement->product?->barcode ?: ($movement->product?->sku ?: 'Sin codigo')); ?></div>
                                </td>
                                <td><?php echo e($movement->type); ?></td>
                                <td class="<?php echo e((int) $movement->quantity_change >= 0 ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold'); ?>">
                                    <?php echo e((int) $movement->quantity_change >= 0 ? '+' : ''); ?><?php echo e((int) $movement->quantity_change); ?>

                                </td>
                                <td><?php echo e((int) $movement->stock_before); ?></td>
                                <td><?php echo e((int) $movement->stock_after); ?></td>
                                <td>
                                    <?php if($movement->cash_movement_id): ?>
                                        <span class="font-mono text-xs">#<?php echo e((int) $movement->cash_movement_id); ?></span>
                                    <?php else: ?>
                                        <span class="ui-muted text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($movement->user?->name ?? '-'); ?></td>
                                <td><?php echo e($movement->note ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($showGymColumn ? 10 : 9); ?>" class="products-empty-state py-8 text-center ui-muted">Aun no hay movimientos de stock.</td>
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
    </div>

    <?php echo $__env->make('sales_inventory.partials.remote-scanner-fab', [
        'scanContext' => 'products',
        'contextGym' => $contextGym,
        'schemaReady' => $schemaReady,
        'isGlobalScope' => $isGlobalScope,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const stockForm = document.getElementById('stock-form');
        const stockProductSelect = document.getElementById('stock-product-id');
        const stockProductHelp = document.getElementById('stock-product-help');
        const hasStockFormErrors = <?php echo json_encode($hasStockFormErrors, 15, 512) ?>;
        const barcodeInput = document.getElementById('product-barcode-input');
        const lastCodeStorageKey = 'remote_scan_last_code_products';

        if (stockForm && stockProductSelect && stockProductHelp) {
            const stockDefaultHelpText = stockProductHelp.dataset.defaultText || 'Selecciona el producto antes de guardar el movimiento.';

            const setStockProductError = function (message) {
                const nextMessage = (message || '').trim();
                const hasError = nextMessage !== '';

                stockProductSelect.classList.toggle('is-invalid', hasError);
                stockProductSelect.setAttribute('aria-invalid', hasError ? 'true' : 'false');
                stockProductSelect.setCustomValidity(nextMessage);
                stockProductHelp.textContent = hasError ? nextMessage : stockDefaultHelpText;
                stockProductHelp.classList.toggle('products-field-error', hasError);
                stockProductHelp.classList.toggle('ui-muted', !hasError);
            };

            stockProductSelect.addEventListener('change', function () {
                if (this.value) {
                    setStockProductError('');
                }
            });

            stockForm.addEventListener('submit', function (event) {
                if (stockProductSelect.value) {
                    setStockProductError('');
                    return;
                }

                event.preventDefault();
                setStockProductError('Selecciona un producto antes de guardar el movimiento.');
                stockProductSelect.reportValidity();
                stockProductSelect.focus();
            });

            if (hasStockFormErrors) {
                stockForm.dataset.hasErrors = '1';
            }
        }

        if (!barcodeInput) {
            return;
        }

        window.addEventListener('remote-scanner:scan', function (event) {
            const code = (event.detail?.code || '').toString().trim();
            if (code === '') {
                return;
            }

            barcodeInput.value = code;
            barcodeInput.focus();
            barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
            try {
                window.sessionStorage.setItem(lastCodeStorageKey, code);
            } catch (error) {
                // Ignore storage failures.
            }
        });

        try {
            const savedCode = window.sessionStorage.getItem(lastCodeStorageKey);
            if (savedCode && savedCode.trim() !== '' && (barcodeInput.value || '').trim() === '') {
                barcodeInput.value = savedCode;
                barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } catch (error) {
            // Ignore storage failures.
        }
    })();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const opsModal = document.getElementById('products-ops-modal');
        if (!opsModal) {
            return;
        }

        const opsDialog = opsModal.querySelector('[data-products-ops-dialog]');
        const productForm = document.getElementById('product-create-form');
        const stockForm = document.getElementById('stock-form');
        const productModeInput = document.getElementById('product-form-mode');
        const productEditIdInput = document.getElementById('product-edit-id');
        const productMethodInput = document.getElementById('product-form-method');
        const productSubmit = document.getElementById('product-form-submit');
        const productCard = productForm ? productForm.closest('.products-surface-card') : null;
        const productCardTitle = productCard ? productCard.querySelector('.ui-heading') : null;
        const productCardSubtitle = productCard ? productCard.querySelector('.ui-muted') : null;
        const productStoreAction = productForm ? (productForm.dataset.storeAction || productForm.getAttribute('action') || '') : '';
        const shouldOpenOnLoad = <?php echo json_encode($productModalOpen || $stockModalOpen, 15, 512) ?>;
        const initialProductMode = <?php echo json_encode($productModalMode, 15, 512) ?>;
        const initialProductEditId = <?php echo json_encode($productEditId, 15, 512) ?>;
        let lastFocusedElement = null;
        let previousBodyOverflow = '';

        const getField = function (form, name) {
            return form ? form.elements.namedItem(name) : null;
        };

        const productNameInput = getField(productForm, 'name');
        const stockProductSelect = getField(stockForm, 'product_id');
        const stockMovementType = getField(stockForm, 'movement_type');
        const stockQuantity = getField(stockForm, 'quantity');
        const stockUnitCost = getField(stockForm, 'unit_cost');
        const stockPaymentMethod = getField(stockForm, 'payment_method');
        const stockNote = getField(stockForm, 'note');
        const createOnlyFields = ['initial_stock', 'initial_payment_method']
            .map(function (name) {
                const field = getField(productForm, name);
                return field ? field.closest('label') : null;
            })
            .filter(Boolean);

        const getFocusableElements = function (container) {
            return Array.from(container.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'))
                .filter(function (element) {
                    return !element.hasAttribute('hidden') && element.offsetParent !== null;
                });
        };

        const trapFocus = function (event) {
            const focusable = getFocusableElements(opsModal);
            if (focusable.length === 0) {
                event.preventDefault();
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
                return;
            }

            if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        };

        const toggleCreateOnlyFields = function (show) {
            createOnlyFields.forEach(function (field) {
                field.classList.toggle('hidden', !show);
            });

            ['initial_stock', 'initial_payment_method'].forEach(function (name) {
                const field = getField(productForm, name);
                if (field) {
                    field.disabled = !show;
                }
            });
        };

        const setProductMode = function (mode, data) {
            const isEdit = mode === 'edit' && data && data.id;

            if (productModeInput) {
                productModeInput.value = isEdit ? 'edit' : 'create';
            }

            if (productEditIdInput) {
                productEditIdInput.value = isEdit ? String(data.id) : '';
            }

            if (productMethodInput) {
                productMethodInput.disabled = !isEdit;
            }

            if (productForm) {
                productForm.action = isEdit ? (data.updateUrl || productForm.getAttribute('action') || productStoreAction) : productStoreAction;
            }

            if (productSubmit) {
                productSubmit.textContent = isEdit ? 'Guardar cambios' : 'Crear producto';
            }

            if (productCardTitle) {
                productCardTitle.textContent = isEdit ? 'Editar producto' : 'Registrar producto';
            }

            if (productCardSubtitle) {
                productCardSubtitle.textContent = isEdit
                    ? 'Actualiza precio, costo, categoria y stock minimo del producto.'
                    : 'Define precio, costo, categoria y stock minimo del producto.';
            }

            toggleCreateOnlyFields(!isEdit);
        };

        const fillProductForm = function (data) {
            if (!productForm) {
                return;
            }

            const values = Object.assign({
                name: '',
                sku: '',
                barcode: '',
                category: '',
                sale_price: '',
                cost_price: '',
                min_stock: '0',
                status: 'active',
                description: '',
                initial_stock: '0',
                initial_payment_method: 'cash',
            }, data || {});

            Object.keys(values).forEach(function (name) {
                const field = getField(productForm, name);
                if (!field) {
                    return;
                }

                field.value = values[name];
            });
        };

        const resetStockForm = function (productId) {
            if (stockMovementType) {
                stockMovementType.value = 'entry';
            }

            if (stockQuantity) {
                stockQuantity.value = '1';
            }

            if (stockUnitCost) {
                stockUnitCost.value = '';
            }

            if (stockPaymentMethod) {
                stockPaymentMethod.value = 'cash';
            }

            if (stockNote) {
                stockNote.value = '';
            }

            if (stockProductSelect) {
                stockProductSelect.value = productId ? String(productId) : '';
                stockProductSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        const openModal = function (focusTarget) {
            lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            previousBodyOverflow = document.body.style.overflow;
            opsModal.classList.remove('hidden');
            opsModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            window.requestAnimationFrame(function () {
                const target = focusTarget || opsDialog || opsModal;
                if (target && typeof target.focus === 'function') {
                    target.focus();
                }
            });
        };

        const closeModal = function () {
            opsModal.classList.add('hidden');
            opsModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = previousBodyOverflow;

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        };

        document.querySelectorAll('[data-close-products-ops]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        opsModal.addEventListener('click', function (event) {
            if (event.target === opsModal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (opsModal.classList.contains('hidden')) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal();
                return;
            }

            if (event.key === 'Tab') {
                trapFocus(event);
            }
        });

        document.querySelectorAll('.js-open-products-create').forEach(function (button) {
            button.addEventListener('click', function () {
                setProductMode('create', null);
                fillProductForm(null);
                openModal(productNameInput);
            });
        });

        document.querySelectorAll('.js-open-products-stock').forEach(function (button) {
            button.addEventListener('click', function () {
                setProductMode('create', null);
                fillProductForm(null);
                resetStockForm('');
                openModal(stockProductSelect);
            });
        });

        document.querySelectorAll('.js-product-edit-trigger').forEach(function (button) {
            button.addEventListener('click', function () {
                const data = {
                    id: this.dataset.productId || '',
                    updateUrl: this.dataset.updateUrl || '',
                    name: this.dataset.productName || '',
                    sku: this.dataset.productSku || '',
                    barcode: this.dataset.productBarcode || '',
                    category: this.dataset.productCategory || '',
                    sale_price: this.dataset.productSalePrice || '',
                    cost_price: this.dataset.productCostPrice || '',
                    min_stock: this.dataset.productMinStock || '0',
                    status: this.dataset.productStatus || 'active',
                    description: this.dataset.productDescription || '',
                };

                setProductMode('edit', data);
                fillProductForm(data);
                openModal(productNameInput);
            });
        });

        document.querySelectorAll('.js-product-stock-trigger').forEach(function (button) {
            button.addEventListener('click', function () {
                setProductMode('create', null);
                fillProductForm(null);
                resetStockForm(this.dataset.productId || '');
                openModal(stockProductSelect);
            });
        });

        if (shouldOpenOnLoad) {
            if (initialProductMode === 'edit' && initialProductEditId > 0) {
                setProductMode('edit', {
                    id: initialProductEditId,
                    updateUrl: productForm ? productForm.getAttribute('action') : '',
                });
                openModal(productNameInput);
                return;
            }

            openModal(stockProductSelect || productNameInput);
            return;
        }

        setProductMode('create', null);
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/products/index.blade.php ENDPATH**/ ?>