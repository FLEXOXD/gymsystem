<?php $__env->startSection('title', 'SuperAdmin Gimnasios'); ?>
<?php $__env->startSection('page-title', 'Gimnasios y Suscripciones'); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
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
        $promotionTemplates = ($promotionTemplates ?? collect())->values();
    ?>

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Cartera global</span>
                    <h2 class="sa-title">Cartera global clara para renovar, suspender o mover planes sin perder contexto.</h2>
                    <p class="sa-subtitle">
                        Separo lectura comercial, filtros rapidos y acciones por gimnasio para que la tabla sirva de trabajo,
                        no de ruido.
                    </p>

                    <div class="sa-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.gym.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.gym.index'))]); ?>Crear gimnasio <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.gym-list.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.gym-list.index')),'variant' => 'secondary']); ?>Editar admins <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.plan-templates.index'),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.plan-templates.index')),'variant' => 'ghost']); ?>Revisar planes <?php echo $__env->renderComponent(); ?>
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

                <aside class="sa-note-card">
                    <p class="sa-note-label">Atencion hoy</p>
                    <div class="sa-note-list">
                        <?php $__empty_1 = true; $__currentLoopData = $attentionPreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preview): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $previewEnd = ! empty($preview->ends_at) ? \Carbon\Carbon::parse($preview->ends_at) : null;
                                $previewStatus = (string) ($preview->status ?? '');
                                $previewMessage = match ($previewStatus) {
                                    'grace' => ((int) ($preview->grace_left ?? 0)).' dias de gracia',
                                    'suspended' => 'Suscripcion suspendida',
                                    default => $previewEnd ? 'Vence '.$previewEnd->toDateString() : 'Requiere revision',
                                };
                            ?>
                            <div class="sa-note-item">
                                <strong><?php echo e((string) ($preview->gym_name ?? 'Gimnasio')); ?></strong>
                                <span><?php echo e((string) ($preview->plan_name ?? 'Sin plan')); ?> | <?php echo e($previewMessage); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="sa-note-item">
                                <strong>Sin alertas criticas hoy</strong>
                                <span>La cartera no tiene vencimientos cercanos visibles.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Gimnasios visibles</p>
                <p class="sa-stat-value"><?php echo e($totalGyms); ?></p>
                <p class="sa-stat-meta">Solo sedes principales e independientes.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value"><?php echo e($activeGyms); ?></p>
                <p class="sa-stat-meta"><?php echo e($portfolioHealth); ?>% de la cartera esta operando.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">En gracia</p>
                <p class="sa-stat-value"><?php echo e($graceGyms); ?></p>
                <p class="sa-stat-meta">Seguimiento prioritario para evitar suspensiones.</p>
            </article>
            <article class="sa-stat-card is-danger">
                <p class="sa-stat-label">Suspendidos</p>
                <p class="sa-stat-value"><?php echo e($suspendedGyms); ?></p>
                <p class="sa-stat-meta">Casos que necesitan reactivacion o cierre.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">MRR estimado</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format((float) $estimatedMrr, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Ingreso mensual proyectado sobre cartera activa.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Vencen en 7 días</p>
                <p class="sa-stat-value"><?php echo e($expiringSoon); ?></p>
                <p class="sa-stat-meta">Renovaciones que ya requieren accion.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Operación multisede</p>
                <p class="sa-stat-value"><?php echo e($multiSiteGyms); ?></p>
                <p class="sa-stat-meta">Gimnasios con estructura multisede.</p>
            </article>
        </section>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Suscripciones','subtitle' => 'Filtros rapidos, menos ruido y acciones agrupadas por gimnasio.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Suscripciones','subtitle' => 'Filtros rapidos, menos ruido y acciones agrupadas por gimnasio.']); ?>
            <?php if($errors->has('subscription')): ?>
                <div class="ui-alert ui-alert-warning mb-4 text-sm font-semibold">
                    <?php echo e($errors->first('subscription')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->has('custom_price')): ?>
                <div class="ui-alert ui-alert-warning mb-4 text-sm font-semibold">
                    <?php echo e($errors->first('custom_price')); ?>

                </div>
            <?php endif; ?>
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
                        <span class="sa-pill is-warning">Riesgo: <?php echo e($graceGyms + $expiringSoon + $suspendedGyms); ?></span>
                        <span class="sa-pill is-info" role="status" aria-live="polite">Visibles: <strong id="gym-visible-count"><?php echo e($totalGyms); ?></strong></span>
                        <button type="button" id="gym-filter-clear" class="ui-button ui-button-ghost">Limpiar filtros</button>
                    </div>
                </div>
                <p id="gym-filter-help" class="sa-filter-note mt-3">
                    Los filtros se aplican en tiempo real y cierran paneles abiertos cuando una fila sale del contexto.
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
                        <?php $__empty_1 = true; $__currentLoopData = $mainGyms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gym): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
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
                            ?>
                            <tr
                                data-gym-row
                                data-gym-id="<?php echo e((int) $gym->gym_id); ?>"
                                data-gym-search="<?php echo e($rowSearch); ?>"
                                data-gym-status="<?php echo e((string) ($gym->status ?? '')); ?>"
                                data-gym-model="<?php echo e($rowModel); ?>"
                            >
                                <td>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($gymName); ?></p>
                                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                                <span class="sa-pill is-neutral"><?php echo e($planName); ?></span>
                                                <span class="sa-pill <?php echo e($isMultiBranchPlan ? 'is-info' : 'is-neutral'); ?>">
                                                    <?php echo e($isMultiBranchPlan ? 'Multisede' : 'Una sede'); ?>

                                                </span>
                                                <?php if($isMultiBranchPlan): ?>
                                                    <button type="button"
                                                            data-branches-toggle="<?php echo e((int) $gym->gym_id); ?>"
                                                            aria-expanded="false"
                                                            class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-800 transition hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                                                        <span>Sucursales (<?php echo e($linkedBranchCount); ?>)</span>
                                                        <svg data-branches-caret="<?php echo e((int) $gym->gym_id); ?>" class="h-3.5 w-3.5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <p class="text-xs leading-5 text-slate-600 dark:text-slate-300">
                                            <?php echo e($isMultiBranchPlan
                                                ? ($linkedBranchCount > 0 ? $linkedBranchCount.' sucursal(es) vinculadas.' : 'Plan multisede sin sucursales aun.')
                                                : 'Operacion independiente.'); ?>

                                        </p>
                                    </div>
                                </td>

                                <td>
                                    <div class="space-y-3">
                                        <span class="<?php echo e($badgeClass); ?>">
                                            <?php echo e(match ($gym->status) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', default => $gym->status }); ?>

                                        </span>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-sm dark:border-slate-800 dark:bg-slate-950/50">
                                            <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($healthMessage); ?></p>
                                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                                                Fecha fin: <?php echo e($endDate->toDateString()); ?>

                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="space-y-3 text-sm">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Mensualidad</p>
                                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">
                                                <?php echo e(\App\Support\Currency::format((float) $gym->price, $appCurrencyCode)); ?>

                                            </p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">último pago</p>
                                            <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100"><?php echo e($lastPaymentLabel); ?></p>
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
                                                    Abre este panel solo cuando necesites renovar o mover plan.
                                                </p>
                                                <?php
                                                    $currentPlanTemplate = collect($planTemplates ?? collect())->first(function ($template) use ($gym) {
                                                        $templatePlanKey = strtolower(trim((string) ($template->slot_plan_key ?? $template->plan_key ?? '')));
                                                        $templateFeaturePlanKey = method_exists($template, 'resolvedFeaturePlanKey')
                                                            ? strtolower(trim((string) $template->resolvedFeaturePlanKey()))
                                                            : strtolower(trim((string) ($template->feature_plan_key ?? $template->plan_key ?? '')));
                                                        $gymPlanKey = strtolower(trim((string) ($gym->plan_key ?? '')));

                                                        if ($gym->plan_template_id !== null && (int) $gym->plan_template_id > 0) {
                                                            return (int) $template->id === (int) $gym->plan_template_id;
                                                        }

                                                        return $gymPlanKey !== '' && ($templatePlanKey === $gymPlanKey || $templateFeaturePlanKey === $gymPlanKey);
                                                    });
                                                    $currentPlanFeatureKey = strtolower(trim((string) (($currentPlanTemplate && method_exists($currentPlanTemplate, 'resolvedFeaturePlanKey'))
                                                        ? $currentPlanTemplate->resolvedFeaturePlanKey()
                                                        : ($currentPlanTemplate->feature_plan_key ?? $currentPlanTemplate->slot_plan_key ?? $currentPlanTemplate->plan_key ?? $gym->plan_key ?? ''))));
                                                    $currentPlanDisplayPrice = (float) ($currentPlanTemplate->price ?? $gym->price ?? 0);
                                                ?>
                                                <form method="POST"
                                                      action="<?php echo e(route('superadmin.subscriptions.renew', $gym->gym_id)); ?>"
                                                      class="grid gap-3"
                                                      data-current-plan-template-id="<?php echo e((int) ($currentPlanTemplate->id ?? 0)); ?>"
                                                      data-current-plan-name="<?php echo e((string) ($currentPlanTemplate->name ?? $gym->plan_name ?? 'Plan actual')); ?>"
                                                      data-current-plan-key="<?php echo e($currentPlanFeatureKey); ?>"
                                                      data-current-plan-price="<?php echo e(number_format($currentPlanDisplayPrice, 2, '.', '')); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                        Plan base
                                                        <select name="plan_template_id" class="ui-input js-plan-template-select">
                                                            <option value="">Mantener plan actual</option>
                                                            <?php $__currentLoopData = ($planTemplates ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $templateFeaturePlanKey = method_exists($template, 'resolvedFeaturePlanKey')
                                                                        ? (string) $template->resolvedFeaturePlanKey()
                                                                        : (string) ($template->feature_plan_key ?? $template->plan_key ?? 'basico');
                                                                ?>
                                                                <option value="<?php echo e($template->id); ?>"
                                                                        data-plan-template-id="<?php echo e((int) $template->id); ?>"
                                                                        data-plan-name="<?php echo e($template->name); ?>"
                                                                        data-feature-plan-key="<?php echo e($templateFeaturePlanKey); ?>"
                                                                        data-plan-price="<?php echo e(number_format((float) $template->price, 2, '.', '')); ?>">
                                                                    <?php echo e($template->name); ?> (<?php echo e(\App\Support\PlanDuration::label($template->duration_unit, (int) $template->duration_days, $template->duration_months)); ?>) - <?php echo e(\App\Support\Currency::format((float) $template->price, $appCurrencyCode)); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </label>

                                                    <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                        Promocion (opcional)
                                                        <select name="promotion_template_id" class="ui-input js-promotion-template-select">
                                                            <option value="">Sin promocion</option>
                                                            <?php $__currentLoopData = $promotionTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotionTemplate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php
                                                                    $promotionType = (string) ($promotionTemplate->type ?? '');
                                                                    $promotionValue = $promotionTemplate->value !== null ? (float) $promotionTemplate->value : null;
                                                                    $promotionDurationUnit = method_exists($promotionTemplate, 'resolvedDurationUnit')
                                                                        ? (string) $promotionTemplate->resolvedDurationUnit()
                                                                        : 'months';
                                                                    $promotionDurationMonths = $promotionDurationUnit === 'months' && $promotionTemplate->duration_months !== null
                                                                        ? (int) $promotionTemplate->duration_months
                                                                        : null;
                                                                    $promotionDurationDays = $promotionDurationUnit === 'days' && $promotionTemplate->duration_days !== null
                                                                        ? (int) $promotionTemplate->duration_days
                                                                        : null;
                                                                    $promotionDurationLabel = $promotionDurationUnit === 'days'
                                                                        ? ($promotionDurationDays !== null ? $promotionDurationDays.' '.($promotionDurationDays === 1 ? 'dia' : 'dias') : null)
                                                                        : ($promotionDurationMonths !== null ? $promotionDurationMonths.' '.($promotionDurationMonths === 1 ? 'mes' : 'meses') : null);
                                                                    $promotionSummary = match ($promotionType) {
                                                                        'percentage' => ($promotionValue !== null ? rtrim(rtrim(number_format($promotionValue, 2, '.', ''), '0'), '.') : '0').'%',
                                                                        'fixed' => '-'.\App\Support\Currency::format((float) ($promotionValue ?? 0), $appCurrencyCode),
                                                                        'final_price' => 'Total '.\App\Support\Currency::format((float) ($promotionValue ?? 0), $appCurrencyCode),
                                                                        'bonus_days' => '+'.(int) round((float) ($promotionValue ?? 0)).' dias',
                                                                        'two_for_one' => '2x1',
                                                                        'bring_friend' => 'Trae un amigo',
                                                                        default => 'Promo',
                                                                    };
                                                                ?>
                                                                <option value="<?php echo e((int) $promotionTemplate->id); ?>"
                                                                        data-promotion-id="<?php echo e((int) $promotionTemplate->id); ?>"
                                                                        data-promotion-name="<?php echo e((string) $promotionTemplate->name); ?>"
                                                                        data-promotion-type="<?php echo e($promotionType); ?>"
                                                                        data-promotion-value="<?php echo e($promotionValue !== null ? number_format($promotionValue, 2, '.', '') : ''); ?>"
                                                                        data-promotion-duration-unit="<?php echo e($promotionDurationUnit); ?>"
                                                                        data-promotion-duration-months="<?php echo e($promotionDurationMonths !== null ? $promotionDurationMonths : ''); ?>"
                                                                        data-promotion-duration-days="<?php echo e($promotionDurationDays !== null ? $promotionDurationDays : ''); ?>">
                                                                    <?php echo e((string) $promotionTemplate->name); ?><?php echo e($promotionDurationLabel !== null ? ' ('.$promotionDurationLabel.')' : ''); ?> - <?php echo e($promotionSummary); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                                                <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($method); ?>"><?php echo e(match ($method) { 'cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia', 'transferencia' => 'Transferencia', default => $method }); ?></option>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </select>
                                                        </label>
                                                    </div>

                                                    <div class="grid gap-3 md:grid-cols-2 md:items-end">
                                                        <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                                            Cobertura prepaga
                                                            <select name="months" class="ui-input js-months-select" required>
                                                                <?php for($monthOption = 1; $monthOption <= 12; $monthOption++): ?>
                                                                    <option value="<?php echo e($monthOption); ?>"><?php echo e($monthOption); ?> <?php echo e($monthOption === 1 ? 'mes' : 'meses'); ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </label>

                                                    </div>

                                                    <p class="text-xs leading-5 text-slate-600 dark:text-slate-300 js-renew-plan-feedback" role="status" aria-live="polite"></p>

                                                    <div class="flex justify-end">
                                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm']); ?>Aplicar renovación <?php echo $__env->renderComponent(); ?>
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
                                            </div>
                                        </details>

                                        <form method="POST"
                                              action="<?php echo e(route('superadmin.subscriptions.suspend', $gym->gym_id)); ?>"
                                              onsubmit="return confirm('Esta acción suspenderá la suscripción y el acceso del gimnasio. ¿Deseas continuar?');"
                                              class="sa-danger-zone">
                                            <?php echo csrf_field(); ?>
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-bold text-rose-900 dark:text-rose-100">Zona sensible</p>
                                                    <p class="mt-1 text-xs leading-5 text-rose-700 dark:text-rose-200">
                                                        Suspende el acceso solo si ya validaste pago pendiente o corte operativo.
                                                    </p>
                                                </div>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'danger','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'danger','size' => 'sm']); ?>Suspender <?php echo $__env->renderComponent(); ?>
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
                                    </div>
                                </td>
                            </tr>

                            <?php if($isMultiBranchPlan): ?>
                                <tr data-gym-detail-row data-parent-gym-id="<?php echo e((int) $gym->gym_id); ?>" class="hidden border-b border-slate-300/70 bg-slate-100/70 dark:border-slate-700 dark:bg-slate-900/40">
                                    <td colspan="4" class="px-4 py-4">
                                        <div class="rounded-2xl border border-slate-300 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                                <h4 class="text-sm font-black uppercase tracking-wide text-slate-900 dark:text-slate-100">
                                                    Sucursales vinculadas de <?php echo e($gymName); ?>

                                                </h4>
                                                <span class="sa-pill is-info"><?php echo e($linkedBranchCount); ?> total</span>
                                            </div>

                                            <?php if($linkedManagedBranches->isEmpty()): ?>
                                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                    Este plan multi sede todavía no tiene sucursales vinculadas.
                                                </div>
                                            <?php else: ?>
                                                <div class="grid gap-3 lg:grid-cols-2 xl:grid-cols-3">
                                                    <?php $__currentLoopData = $linkedManagedBranches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $branchStatus = (string) ($branch->status ?? '');
                                                            $branchBadgeClass = $statusClasses[$branchStatus] ?? 'ui-badge ui-badge-muted';
                                                            $branchEndsAt = \Carbon\Carbon::parse($branch->ends_at);
                                                            $branchDaysLeft = $branchStatus === 'active'
                                                                ? (int) ($branch->days_left ?? max(0, \Carbon\Carbon::today()->diffInDays($branchEndsAt, false)))
                                                                : null;
                                                        ?>
                                                        <article class="rounded-2xl border border-slate-300 bg-slate-50 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                            <div class="flex items-start justify-between gap-2">
                                                                <p class="text-sm font-bold text-slate-900 dark:text-slate-50"><?php echo e((string) ($branch->gym_name ?? 'Sucursal')); ?></p>
                                                                <span class="<?php echo e($branchBadgeClass); ?>">
                                                                    <?php echo e(match ($branchStatus) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', default => ($branchStatus !== '' ? $branchStatus : '-') }); ?>

                                                                </span>
                                                            </div>
                                                            <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-200"><?php echo e((string) ($branch->plan_name ?? '-')); ?></p>
                                                            <div class="mt-3 grid gap-2 text-xs">
                                                                <p class="rounded-xl border border-slate-300 bg-white px-2.5 py-2 text-slate-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                                    <span class="font-semibold">Vence:</span> <?php echo e($branchEndsAt->toDateString()); ?>

                                                                </p>
                                                                <p class="rounded-xl border border-slate-300 bg-white px-2.5 py-2 text-slate-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                                    <span class="font-semibold">Mensualidad:</span> <?php echo e(\App\Support\Currency::format((float) ($branch->price ?? 0), $appCurrencyCode)); ?>

                                                                </p>
                                                            </div>
                                                            <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                                                                Gestionada por sede principal.
                                                                <?php if($branchDaysLeft !== null): ?>
                                                                    Quedan <?php echo e($branchDaysLeft); ?> días.
                                                                <?php endif; ?>
                                                            </p>
                                                        </article>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="sa-empty-row">
                                    No hay gimnasios registrados.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if($mainGyms->isNotEmpty()): ?>
                            <tr id="gym-empty-state" class="hidden">
                                <td colspan="4" class="sa-empty-row">
                                    No se encontraron gimnasios con ese criterio.
                                </td>
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
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
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

        const formatUsd = function (value) {
            const numeric = Number(value);
            if (!Number.isFinite(numeric)) return '$0.00';
            return '$' + numeric.toFixed(2);
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
            const promotionSelect = form.querySelector('.js-promotion-template-select');
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
                const canUseCustomPrice = selectedPlanKey === 'sucursales';
                const promotionOption = promotionSelect ? promotionSelect.options[promotionSelect.selectedIndex] || null : null;
                const promotionSelected = promotionSelect ? String(promotionSelect.value || '').trim() !== '' : false;
                const promotionDurationUnit = String(promotionOption?.getAttribute('data-promotion-duration-unit') || 'months').toLowerCase();
                const promotionDurationMonths = Number(promotionOption?.getAttribute('data-promotion-duration-months') || '0');
                const promotionDurationDays = Number(promotionOption?.getAttribute('data-promotion-duration-days') || '0');
                const hasFixedMonthDuration = promotionSelected
                    && promotionDurationUnit === 'months'
                    && Number.isFinite(promotionDurationMonths)
                    && promotionDurationMonths > 0;
                const hasFixedDayDuration = promotionSelected
                    && promotionDurationUnit === 'days'
                    && Number.isFinite(promotionDurationDays)
                    && promotionDurationDays > 0;
                if (hasFixedMonthDuration) {
                    monthsSelect.value = String(Math.round(promotionDurationMonths));
                } else if (hasFixedDayDuration) {
                    monthsSelect.value = '1';
                }
                const selectedMonths = Math.max(1, Math.round(Number(monthsSelect.value || '1')));

                monthsSelect.disabled = hasFixedDayDuration;
                monthsSelect.classList.toggle('opacity-60', hasFixedDayDuration);
                monthsSelect.classList.toggle('cursor-not-allowed', hasFixedDayDuration);
                monthsSelect.title = hasFixedDayDuration
                    ? 'Esta promocion usa un plazo fijo de ' + Math.round(promotionDurationDays) + ' dia(s).'
                    : (hasFixedMonthDuration
                        ? 'Esta promocion usa un plazo fijo de ' + selectedMonths + ' mes(es).'
                        : (hasTemplate
                            ? 'Multiplica la cobertura del plan elegido.'
                            : 'Extiende la cobertura del plan actual.'));

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
                const selectedPromotion = promotionSelected
                    ? {
                        id: Number(promotionOption?.getAttribute('data-promotion-id') || '0'),
                        name: String(promotionOption?.getAttribute('data-promotion-name') || promotionOption?.textContent || 'Promocion'),
                        type: String(promotionOption?.getAttribute('data-promotion-type') || ''),
                        value: Number(promotionOption?.getAttribute('data-promotion-value') || '0'),
                        duration_unit: promotionDurationUnit,
                        duration_months: hasFixedMonthDuration ? Math.round(promotionDurationMonths) : null,
                        duration_days: hasFixedDayDuration ? Math.round(promotionDurationDays) : null,
                    }
                    : null;
                const pricing = resolvePromotionPricing(baseMonthlyPrice, selectedMonths, selectedPromotion);

                if (!feedback) {
                    return;
                }

                if (selectedTemplateId === '' && currentTemplateId === '') {
                    feedback.textContent = 'Selecciona un plan base para continuar.';
                    return;
                }

                if (!selectedPromotion) {
                    feedback.textContent = selectedPlanName + ': cobra ' + formatUsd(baseMonthlyPrice) + '/mes. Total ' + formatUsd(pricing.baseTotal) + ' por ' + selectedMonths + ' mes(es).';
                    return;
                }

                const promoName = String(selectedPromotion.name || 'Promocion');
                const discountText = pricing.discountAmount > 0
                    ? ' Descuento aplicado: ' + formatUsd(pricing.discountAmount) + '.'
                    : '';
                const coverageLabel = selectedPromotion.duration_unit === 'days' && Number.isFinite(selectedPromotion.duration_days) && selectedPromotion.duration_days > 0
                    ? (Math.round(selectedPromotion.duration_days) + ' dia(s)')
                    : (selectedMonths + ' mes(es)');
                feedback.textContent = selectedPlanName + ' + ' + promoName + ': total final ' + formatUsd(pricing.finalTotal) + ' por ' + coverageLabel + '.' + discountText;
            };

            planSelect.addEventListener('change', syncMode);
            promotionSelect?.addEventListener('change', syncMode);
            monthsSelect.addEventListener('change', syncMode);
            customPriceInput?.addEventListener('input', syncMode);
            syncMode();
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/gyms.blade.php ENDPATH**/ ?>