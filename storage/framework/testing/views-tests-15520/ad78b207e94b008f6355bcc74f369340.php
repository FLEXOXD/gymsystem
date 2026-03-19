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
                    <h2 class="sa-title">Ve cobros, descuentos y crecimiento sin adivinar numeros.</h2>
                    <p class="sa-subtitle">
                        Este panel ahora separa lo que ya cobraste, lo que proyecta tu cartera activa y los nuevos gimnasios
                        que entran al sistema. Asi puedes leer mejor promociones, pagos adelantados y salud comercial.
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
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong><?php echo e(\App\Support\Currency::format($currentMonthRevenue, $appCurrencyCode)); ?> cobrados este mes</strong>
                            <span><?php echo e($chargeCountMonth); ?> movimientos registrados con descuentos ya aplicados.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong><?php echo e($newGymsMonth); ?> gimnasios nuevos este mes</strong>
                            <span><?php echo e($newGymsYear); ?> altas acumuladas en el ano para medir crecimiento comercial.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong><?php echo e($discountedSubscriptions); ?> suscripciones con descuento vigente</strong>
                            <span><?php echo e(\App\Support\Currency::format($currentCycleDiscount, $appCurrencyCode)); ?> descontados en la cartera activa actual.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total gimnasios</p>
                <p class="sa-stat-value"><?php echo e($totalGyms); ?></p>
                <p class="sa-stat-meta">Base total instalada dentro del sistema.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value"><?php echo e($activeGyms); ?></p>
                <p class="sa-stat-meta"><?php echo e($healthRate); ?>% de la cartera sigue operando sin corte.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">En gracia</p>
                <p class="sa-stat-value"><?php echo e($graceGyms); ?></p>
                <p class="sa-stat-meta"><?php echo e($graceToday); ?> requieren seguimiento inmediato hoy.</p>
            </article>
            <article class="sa-stat-card is-danger">
                <p class="sa-stat-label">Suspendidos</p>
                <p class="sa-stat-value"><?php echo e($suspendedGyms); ?></p>
                <p class="sa-stat-meta">Casos fuera de operacion comercial.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Cobro ciclo vigente</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($currentCycleRevenue, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Total del ciclo activo, respetando meses pagados y descuentos del cobro actual.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Cobrado este mes</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($currentMonthRevenue, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Ingreso registrado en el mes actual.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Cobrado este ano</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($currentYearRevenue, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">Acumulado anual segun historial de altas y renovaciones.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Proyeccion anual</p>
                <p class="sa-stat-value text-2xl"><?php echo e(\App\Support\Currency::format($annualRunRate, $appCurrencyCode)); ?></p>
                <p class="sa-stat-meta">MRR base de la cartera actual multiplicado por 12.</p>
            </article>
        </section>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.85fr)]">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Reporte comercial','subtitle' => 'Resumen de ingresos, descuentos y altas para que el panel sea util de verdad.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reporte comercial','subtitle' => 'Resumen de ingresos, descuentos y altas para que el panel sea util de verdad.']); ?>
                <div class="sa-mini-grid">
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($currentMonthDiscount, $appCurrencyCode)); ?> en descuentos del mes</strong>
                        <span>Te muestra cuanto cediste comercialmente en promociones activas durante este mes.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($currentYearDiscount, $appCurrencyCode)); ?> en descuentos del ano</strong>
                        <span>Lectura anual para no perder margen cuando ofreces planes por 3, 6 o 12 meses.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e($chargeCountYear); ?> cobros registrados este ano</strong>
                        <span>Incluye altas y renovaciones que quedaron guardadas como eventos comerciales.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($avgTicketMonth, $appCurrencyCode)); ?> ticket promedio del mes</strong>
                        <span>Ayuda a leer si estan entrando planes mas grandes o pagos por varios meses.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e(\App\Support\Currency::format($recurringMrr, $appCurrencyCode)); ?> MRR base</strong>
                        <span>Mensualidad recurrente proyectada despues de promociones temporales.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong><?php echo e($renewalsSoon); ?> renovaciones cercanas</strong>
                        <span>Gimnasios que vencen en los proximos 7 dias y ya merecen seguimiento.</span>
                    </article>
                </div>

                <div class="mt-5 overflow-x-auto">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Alertas y crecimiento','subtitle' => 'Indicadores rapidos para revisar cartera, altas nuevas y margen comercial.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alertas y crecimiento','subtitle' => 'Indicadores rapidos para revisar cartera, altas nuevas y margen comercial.']); ?>
                <ul class="sa-check-list">
                    <li><?php echo e($newGymsMonth); ?> gimnasios nuevos este mes.</li>
                    <li><?php echo e($newGymsYear); ?> gimnasios nuevos acumulados este ano.</li>
                    <li><?php echo e($chargeCountMonth); ?> cobros registrados durante el mes actual.</li>
                    <li><?php echo e(\App\Support\Currency::format($currentCycleDiscount, $appCurrencyCode)); ?> descontados dentro del ciclo vigente.</li>
                    <li><?php echo e($discountedSubscriptions); ?> cuentas activas estan operando con descuento en este momento.</li>
                    <li><?php echo e($renewalsSoon); ?> renovaciones proximas y <?php echo e($graceToday); ?> cuentas hoy en gracia.</li>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Actividad de admins principales','subtitle' => 'Solo se muestran los duenos/admins de cada gimnasio. No incluye SuperAdmin ni cajeros.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Actividad de admins principales','subtitle' => 'Solo se muestran los duenos/admins de cada gimnasio. No incluye SuperAdmin ni cajeros.']); ?>
            <div class="mb-4 sa-mini-grid">
                <article class="sa-mini-card">
                    <strong><?php echo e($ownersOnlineNow); ?> activos ahora mismo</strong>
                    <span>Se consideran activos si registraron actividad dentro de los ultimos 5 minutos.</span>
                </article>
                <article class="sa-mini-card">
                    <strong><?php echo e($ownerActivityRows->count()); ?> gimnasios monitoreados</strong>
                    <span>La tabla mezcla login real y uso del panel para cubrir web, Recuerdame y app instalada.</span>
                </article>
            </div>

            <div class="overflow-x-auto">
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
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e($statusIsOnline ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200'); ?>">
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Conteo por plan','subtitle' => 'Cuantos gimnasios hay hoy en cada uno de tus 4 planes comerciales.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Conteo por plan','subtitle' => 'Cuantos gimnasios hay hoy en cada uno de tus 4 planes comerciales.']); ?>
            <div class="sa-mini-grid">
                <?php $__empty_1 = true; $__currentLoopData = $planMix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="sa-mini-card">
                        <strong><?php echo e($row['name']); ?>: <?php echo e((int) ($row['count'] ?? 0)); ?></strong>
                        <span>Gimnasios que hoy operan con este plan base.</span>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <article class="sa-mini-card">
                        <strong>Sin datos</strong>
                        <span>Todavia no hay cartera suficiente para agrupar por plan.</span>
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
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>