<?php $__env->startSection('title', 'Panel SuperAdmin'); ?>
<?php $__env->startSection('page-title', 'Panel Global'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $totalGyms = (int) ($kpis['total_gyms'] ?? 0);
        $activeGyms = (int) ($kpis['active_gyms'] ?? 0);
        $graceGyms = (int) ($kpis['grace_gyms'] ?? 0);
        $suspendedGyms = (int) ($kpis['suspended_gyms'] ?? 0);
        $currentCycleRevenue = (float) ($kpis['current_cycle_revenue'] ?? 0);
        $currentCycleDiscount = (float) ($kpis['current_cycle_discount'] ?? 0);
        $recurringMrr = (float) ($kpis['recurring_mrr'] ?? 0);
        $annualRunRate = (float) ($kpis['annual_run_rate'] ?? 0);
        $currentMonthRevenue = (float) ($kpis['current_month_revenue'] ?? 0);
        $currentYearRevenue = (float) ($kpis['current_year_revenue'] ?? 0);
        $currentMonthDiscount = (float) ($kpis['current_month_discount'] ?? 0);
        $currentYearDiscount = (float) ($kpis['current_year_discount'] ?? 0);
        $chargeCountMonth = (int) ($kpis['charges_this_month'] ?? 0);
        $chargeCountYear = (int) ($kpis['charges_this_year'] ?? 0);
        $newGymsMonth = (int) ($kpis['new_gyms_month'] ?? 0);
        $newGymsYear = (int) ($kpis['new_gyms_year'] ?? 0);
        $avgTicketMonth = (float) ($kpis['avg_ticket_month'] ?? 0);
        $discountedSubscriptions = (int) ($kpis['discounted_subscriptions'] ?? 0);
        $renewalsSoon = (int) ($kpis['vencen_en_7_dias'] ?? 0);
        $graceToday = (int) ($kpis['en_gracia_hoy'] ?? 0);
        $healthRate = $totalGyms > 0 ? (int) round(($activeGyms / $totalGyms) * 100) : 0;
        $planMix = collect($planMix ?? []);
        $monthlyRows = collect($reports['monthly_rows'] ?? []);
        $ownerActivityRows = collect($reports['owner_activity_rows'] ?? []);
        $ownersOnlineNow = $ownerActivityRows->where('status_key', 'online')->count();
        $dashboardTimezone = trim((string) (auth()->user()?->timezone ?? config('app.timezone', 'UTC')));
        if (
            $dashboardTimezone === ''
            || $dashboardTimezone === 'UTC'
            || ! in_array($dashboardTimezone, timezone_identifiers_list(), true)
        ) {
            $dashboardTimezone = 'America/Guayaquil';
        }
    ?>

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">SuperAdmin SaaS</span>
                    <h2 class="sa-title">Panel global para cobrar, seguir cartera y detectar riesgo rapido.</h2>
                    <p class="sa-subtitle">
                        Deja arriba solo lo que mueve decisiones: cartera activa, ingresos del ciclo, renovaciones cercanas
                        y actividad real de admins.
                    </p>

                    <div class="sa-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.gyms.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.gyms.index'))]); ?>Ver cartera global <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.gym.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.gym.index')),'variant' => 'secondary']); ?>Crear gimnasio <?php echo $__env->renderComponent(); ?>
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
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.plan-templates.index')),'variant' => 'ghost']); ?>Editar planes <?php echo $__env->renderComponent(); ?>
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
                    <p class="sa-note-label">Hoy</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong><?php echo e(\App\Support\Currency::format($currentMonthRevenue, $appCurrencyCode)); ?> cobrados</strong>
                            <span><?php echo e($chargeCountMonth); ?> cobros registrados este mes.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong><?php echo e($newGymsMonth); ?> altas nuevas</strong>
                            <span><?php echo e($newGymsYear); ?> gimnasios creados en el anio.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong><?php echo e($discountedSubscriptions); ?> cuentas con descuento</strong>
                            <span><?php echo e(\App\Support\Currency::format($currentCycleDiscount, $appCurrencyCode)); ?> descontados en el ciclo vigente.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total gimnasios</p>
                <p class="sa-stat-value"><?php echo e($totalGyms); ?></p>
                <p class="sa-stat-meta">Base total instalada.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value"><?php echo e($activeGyms); ?></p>
                <p class="sa-stat-meta"><?php echo e($healthRate); ?>% de la cartera sigue operando.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">En gracia</p>
                <p class="sa-stat-value"><?php echo e($graceGyms); ?></p>
                <p class="sa-stat-meta"><?php echo e($graceToday); ?> requieren seguimiento hoy.</p>
            </article>
            <article class="sa-stat-card is-danger">
                <p class="sa-stat-label">Suspendidos</p>
                <p class="sa-stat-value"><?php echo e($suspendedGyms); ?></p>
                <p class="sa-stat-meta">Casos fuera de operacion.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Cobro ciclo vigente</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($currentCycleRevenue, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Lectura del ciclo activo con descuentos aplicados.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Cobrado este mes</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($currentMonthRevenue, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Ingreso registrado este mes.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Cobrado este a&ntilde;o</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($currentYearRevenue, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Acumulado anual.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Proyeccion anual</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($annualRunRate, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">MRR actual proyectado a 12 meses.</p>
            </article>
        </section>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.85fr)]">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Reporte comercial','subtitle' => 'Ingreso, descuento y altas en una sola lectura.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reporte comercial','subtitle' => 'Ingreso, descuento y altas en una sola lectura.']); ?>
                <div class="sa-mini-grid">
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($currentMonthDiscount, $appCurrencyCode)); ?> en descuentos del mes</strong>
                        <span>Margen cedido por promociones activas del mes.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($currentYearDiscount, $appCurrencyCode)); ?> en descuentos del a&ntilde;o</strong>
                        <span>Lectura anual para vigilar margen comercial.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e($chargeCountYear); ?> cobros registrados este a&ntilde;o</strong>
                        <span>Altas y renovaciones guardadas en historial.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($avgTicketMonth, $appCurrencyCode)); ?> ticket promedio del mes</strong>
                        <span>Referencia rapida del valor medio por cobro.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($recurringMrr, $appCurrencyCode)); ?> MRR base</strong>
                        <span>Mensualidad recurrente de la cartera actual.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e($renewalsSoon); ?> renovaciones cercanas</strong>
                        <span>Renovaciones que ya merecen seguimiento.</span>
                    </article>
                </div>

                <div class="sa-table-shell mt-5 overflow-x-auto">
                    <table class="ui-table min-w-[760px]">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Cobrado</th>
                                <th>Descuento</th>
                                <th>Cobros</th>
                                <th>Gimnasios nuevos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $monthlyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="font-semibold text-slate-800 dark:text-slate-100"><?php echo e($row['month_label']); ?></td>
                                    <td><?php echo e(\App\Support\Currency::format((float) ($row['revenue'] ?? 0), $appCurrencyCode)); ?></td>
                                    <td><?php echo e(\App\Support\Currency::format((float) ($row['discount'] ?? 0), $appCurrencyCode)); ?></td>
                                    <td><?php echo e((int) ($row['charges'] ?? 0)); ?></td>
                                    <td><?php echo e((int) ($row['new_gyms'] ?? 0)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="sa-empty-row">Todavia no hay historial comercial para mostrar.</td>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Alertas y crecimiento','subtitle' => 'Atajos rapidos para revisar cartera y operaciones clave.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alertas y crecimiento','subtitle' => 'Atajos rapidos para revisar cartera y operaciones clave.']); ?>
                <ul class="sa-check-list">
                    <li><?php echo e($renewalsSoon); ?> renovaciones cercanas.</li>
                    <li><?php echo e($graceToday); ?> cuentas hoy en gracia.</li>
                    <li><?php echo e($newGymsMonth); ?> altas nuevas este mes.</li>
                    <li><?php echo e($chargeCountMonth); ?> cobros registrados este mes.</li>
                    <li><?php echo e(\App\Support\Currency::format($currentCycleDiscount, $appCurrencyCode)); ?> descontados en el ciclo vigente.</li>
                </ul>

                <div class="mt-4 flex flex-wrap gap-2">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.gym-list.index'),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.gym-list.index')),'size' => 'sm','variant' => 'ghost']); ?>Gestion de admins <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.branches.index'),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.branches.index')),'size' => 'sm','variant' => 'ghost']); ?>Sucursales <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('superadmin.quotations.index'),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('superadmin.quotations.index')),'size' => 'sm','variant' => 'ghost']); ?>Cotizaciones <?php echo $__env->renderComponent(); ?>
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
        </div>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Actividad de admins principales','subtitle' => 'Solo admins principales. No incluye SuperAdmin ni cajeros.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Actividad de admins principales','subtitle' => 'Solo admins principales. No incluye SuperAdmin ni cajeros.']); ?>
            <div class="mb-4 sa-mini-grid">
                <article class="sa-mini-card">
                    <strong><?php echo e($ownersOnlineNow); ?> activos ahora mismo</strong>
                        <span>Activos si registraron uso en los ultimos 5 minutos.</span>
                </article>
                <article class="sa-mini-card">
                    <strong><?php echo e($ownerActivityRows->count()); ?> gimnasios monitoreados</strong>
                        <span>Login y uso real del panel en una sola tabla.</span>
                </article>
            </div>

            <div class="sa-table-shell overflow-x-auto">
                <table class="ui-table min-w-[1240px]">
                    <thead>
                        <tr>
                            <th>Gimnasio</th>
                            <th>Admin principal</th>
                            <th>Estado</th>
                            <th>Ultima actividad</th>
                            <th>Ultimo login</th>
                            <th>Canal</th>
                            <th>IP</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $ownerActivityRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $lastActivityAt = $row['last_activity_at'] ?? null;
                                $lastActivityAtLocal = $lastActivityAt instanceof \Illuminate\Support\Carbon
                                    ? $lastActivityAt->copy()->timezone($dashboardTimezone)
                                    : null;
                                $lastLoginAt = $row['last_login_at'] ?? null;
                                $lastLoginAtLocal = $lastLoginAt instanceof \Illuminate\Support\Carbon
                                    ? $lastLoginAt->copy()->timezone($dashboardTimezone)
                                    : null;
                                $statusIsOnline = ($row['status_key'] ?? '') === 'online';
                            ?>
                            <tr>
                                <td class="font-semibold text-slate-800 dark:text-slate-100"><?php echo e($row['gym_name'] ?? 'Gym'); ?></td>
                                <td><?php echo e($row['user_name'] ?? 'Admin principal'); ?></td>
                                <td>
                                    <span class="<?php echo e($statusIsOnline ? 'sa-status-chip is-success' : 'sa-status-chip is-neutral'); ?>">
                                        <?php echo e($row['status_label'] ?? 'Inactivo'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="font-semibold text-slate-800 dark:text-slate-100"><?php echo e($lastActivityAtLocal?->format('d/m/Y H:i') ?? '-'); ?></div>
                                    <div class="text-xs text-slate-500 dark:text-slate-300">
                                        <?php if(($row['signal'] ?? 'activity') === 'login_manual'): ?>
                                            Login manual
                                        <?php elseif(($row['signal'] ?? 'activity') === 'sesion_recordada'): ?>
                                            Sesion recordada
                                        <?php else: ?>
                                            Uso del panel
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo e($lastLoginAtLocal?->format('d/m/Y H:i') ?? '-'); ?></td>
                                <td>
                                    <div><?php echo e($row['channel_label'] ?? 'Web'); ?></div>
                                    <?php if((bool) ($row['via_remember'] ?? false)): ?>
                                        <div class="text-xs text-slate-500 dark:text-slate-300">via Recuerdame</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($row['ip_address'] ?? '-'); ?></td>
                                <td><?php echo e($row['user_email'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="sa-empty-row">Todavia no hay actividad registrada de admins principales.</td>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Conteo por plan','subtitle' => 'Distribucion actual de la cartera por plan.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Conteo por plan','subtitle' => 'Distribucion actual de la cartera por plan.']); ?>
            <div class="sa-mini-grid">
                <?php $__empty_1 = true; $__currentLoopData = $planMix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="sa-mini-card">
                        <strong><?php echo e($row['name']); ?>: <?php echo e((int) ($row['count'] ?? 0)); ?></strong>
                        <span>Gimnasios operando con este plan hoy.</span>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <article class="sa-mini-card">
                        <strong>Sin datos</strong>
                        <span>Aun no hay cartera suficiente para agrupar.</span>
                    </article>
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

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'QR de pagina','subtitle' => 'Enlace fijo para abrir o descargar el acceso publico.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'QR de pagina','subtitle' => 'Enlace fijo para abrir o descargar el acceso publico.']); ?>
            <div class="sa-page-scanner-grid">
                <div class="sa-page-scanner-copy">
                    <p class="sa-page-scanner-label">Enlace fijo</p>
                    <p id="superadmin-page-qr-url" class="sa-page-scanner-url"><?php echo e($scannerPageUrl); ?></p>
                    <p class="sa-page-scanner-hint">
                        Este QR se genera siempre desde este mismo enlace dentro del panel, para que no dependas de una imagen vieja
                        o de un archivo externo que se dañe con el tiempo.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $scannerPageUrl,'target' => '_blank','rel' => 'noopener']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($scannerPageUrl),'target' => '_blank','rel' => 'noopener']); ?>Abrir pagina <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','id' => 'superadmin-page-qr-download']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','id' => 'superadmin-page-qr-download']); ?>Descargar QR <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','id' => 'superadmin-page-qr-copy']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','id' => 'superadmin-page-qr-copy']); ?>Copiar enlace <?php echo $__env->renderComponent(); ?>
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

                    <p id="superadmin-page-qr-feedback" class="sa-page-scanner-feedback">
                        Listo para escanear o descargar.
                    </p>
                </div>

                <div class="sa-page-scanner-preview">
                    <div id="superadmin-page-qr-svg" class="sa-page-scanner-frame">
                        <?php echo $scannerPageQrSvg; ?>

                    </div>
                    <p class="mt-3 text-xs ui-muted">
                        Escanealo con cualquier celular para abrir <strong>flexjok.duckdns.org</strong>.
                    </p>
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
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .sa-page-scanner-grid {
            display: grid;
            gap: 1rem;
            align-items: center;
            grid-template-columns: minmax(0, 1.15fr) minmax(260px, 0.85fr);
        }

        .sa-page-scanner-copy {
            min-width: 0;
        }

        .sa-page-scanner-label {
            margin: 0;
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.9);
        }

        .dark .sa-page-scanner-label {
            color: rgb(148 163 184 / 0.88);
        }

        .sa-page-scanner-url {
            margin: 0.55rem 0 0;
            font-size: clamp(1rem, 0.95rem + 0.22vw, 1.08rem);
            line-height: 1.45;
            font-weight: 800;
            color: rgb(15 23 42 / 0.96);
            word-break: break-word;
        }

        .dark .sa-page-scanner-url {
            color: rgb(241 245 249 / 0.96);
        }

        .sa-page-scanner-hint {
            margin: 0.75rem 0 0;
            max-width: 40rem;
            font-size: 0.92rem;
            line-height: 1.6;
            color: rgb(71 85 105 / 0.92);
        }

        .dark .sa-page-scanner-hint {
            color: rgb(148 163 184 / 0.92);
        }

        .sa-page-scanner-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .sa-page-scanner-frame {
            width: min(100%, 320px);
            border-radius: 1.35rem;
            border: 1px solid rgb(16 185 129 / 0.34);
            background: linear-gradient(145deg, rgb(255 255 255 / 0.98), rgb(241 245 249 / 0.95));
            padding: 1rem;
            box-shadow: 0 20px 40px -28px rgb(2 8 23 / 0.45);
        }

        .sa-page-scanner-frame svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .sa-page-scanner-feedback {
            min-height: 1.4rem;
            margin: 0.95rem 0 0;
            font-size: 0.84rem;
            font-weight: 700;
            color: rgb(5 150 105 / 0.98);
        }

        @media (max-width: 900px) {
            .sa-page-scanner-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const scannerPageUrl = <?php echo json_encode($scannerPageUrl, 15, 512) ?>;
            const qrContainer = document.getElementById('superadmin-page-qr-svg');
            const downloadButton = document.getElementById('superadmin-page-qr-download');
            const copyButton = document.getElementById('superadmin-page-qr-copy');
            const feedback = document.getElementById('superadmin-page-qr-feedback');

            function setFeedback(text, isError) {
                if (!feedback) {
                    return;
                }

                feedback.textContent = text;
                feedback.style.color = isError ? 'rgb(244 63 94)' : 'rgb(5 150 105)';
            }

            async function copyText(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                    return;
                }

                const helper = document.createElement('textarea');
                helper.value = text;
                helper.setAttribute('readonly', 'readonly');
                helper.style.position = 'fixed';
                helper.style.opacity = '0';
                document.body.appendChild(helper);
                helper.select();
                document.execCommand('copy');
                document.body.removeChild(helper);
            }

            copyButton?.addEventListener('click', async function () {
                try {
                    await copyText(scannerPageUrl);
                    setFeedback('Enlace copiado.', false);
                } catch (error) {
                    setFeedback('No se pudo copiar el enlace.', true);
                }
            });

            downloadButton?.addEventListener('click', function () {
                try {
                    const svg = qrContainer?.querySelector('svg');
                    if (!svg) {
                        setFeedback('No se encontro el QR para descargar.', true);
                        return;
                    }

                    const serialized = new XMLSerializer().serializeToString(svg);
                    const blob = new Blob([serialized], { type: 'image/svg+xml;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'scanner-pagina-flexjok.svg';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    setFeedback('QR descargado.', false);
                } catch (error) {
                    setFeedback('No se pudo descargar el QR.', true);
                }
            });
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>