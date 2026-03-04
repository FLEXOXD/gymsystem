


<?php $__env->startSection('title', 'Panel de control'); ?>
<?php $__env->startSection('page-title', 'Panel de control'); ?>
<?php $__env->startSection('header-live-banner'); ?>
    <div id="header-live-clients"
         class="header-live-pill inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full border border-emerald-300/70 bg-emerald-500/10 px-3 py-1.5 shadow-[0_0_20px_rgba(16,185,129,0.22)] max-[420px]:gap-1 max-[420px]:px-2"
         data-live-url="<?php echo e(route('panel.live-clients')); ?>">
        <span class="relative inline-flex h-2.5 w-2.5">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="live-core-dot relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
        </span>
        <span class="text-[10px] font-black uppercase tracking-[0.14em] text-emerald-200 max-[420px]:text-[9px]">PRESENTES</span>
        <span id="header-live-count" class="text-sm font-black text-white max-[420px]:text-xs"><?php echo e((int) $liveClientsNow); ?></span>
        <span class="text-xs font-semibold text-emerald-100/90 max-[420px]:hidden">ahora</span>
    </div>
<?php $__env->stopSection(); ?>

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
        $clientShowUrl = static fn (int $clientId): string => route('clients.show', ['client' => $clientId] + ($isGlobalScope ? ['scope' => 'global'] : []));
    ?>

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px] xl:items-start">
    <div class="space-y-4">
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['id' => 'tour-panel-summary','title' => 'Resumen del día','subtitle' => 'Indicadores clave para tomar decisiones rápidas.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-summary','title' => 'Resumen del día','subtitle' => 'Indicadores clave para tomar decisiones rápidas.']); ?>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-slate-500 dark:text-slate-300">Clientes</p>
                <p class="mt-1 text-2xl font-black leading-none text-slate-900 dark:text-slate-100"><?php echo e($totalClients); ?></p>
                <p class="min-h-[16px] text-xs text-slate-500 dark:text-slate-300">Base registrada</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-emerald-700 dark:text-emerald-200">Membresías activas</p>
                <p class="mt-1 text-2xl font-black leading-none text-emerald-800 dark:text-emerald-100"><?php echo e($activeMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-emerald-700 dark:text-emerald-200">Vigentes hoy</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-amber-700 dark:text-amber-200">Por vencer</p>
                <p class="mt-1 text-2xl font-black leading-none text-amber-800 dark:text-amber-100"><?php echo e($expiringSoonMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-amber-700 dark:text-amber-200">Próximas 48 horas</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-rose-700 dark:text-rose-200">Vencid@s</p>
                <p class="mt-1 text-2xl font-black leading-none text-rose-800 dark:text-rose-100"><?php echo e($expiredMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-rose-700 dark:text-rose-200">Requieren renovación</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins hoy</p>
                <p class="mt-1 text-2xl font-black leading-none text-cyan-800 dark:text-cyan-100"><?php echo e((int) $checkinsToday); ?></p>
                <p class="min-h-[16px] text-xs text-cyan-700 dark:text-cyan-200">Se reinicia 12:00 AM</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-violet-700 dark:text-violet-200">Planes activos</p>
                <p class="mt-1 text-2xl font-black leading-none text-violet-800 dark:text-violet-100"><?php echo e($activePlans); ?></p>
                <p class="min-h-[16px] text-xs text-violet-700 dark:text-violet-200">Oferta vigente</p>
            </article>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reception.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reception.index')),'variant' => 'primary']); ?>Ir a recepción <?php echo $__env->renderComponent(); ?>
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
<?php $component->withAttributes(['id' => 'tour-panel-go-clients','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index')),'variant' => 'secondary']); ?>Gestionar clientes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Comparativo mensual','subtitle' => 'Si las ventas van mejor o peor vs el mes anterior.','class' => 'xl:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Comparativo mensual','subtitle' => 'Si las ventas van mejor o peor vs el mes anterior.','class' => 'xl:col-span-2']); ?>
            <div class="grid gap-3 md:grid-cols-3">
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200"><?php echo e($monthCurrentLabel); ?></p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) $incomeCurrentMonth, $appCurrencyCode)); ?></p>
                    <p class="text-xs text-cyan-700 dark:text-cyan-200">Ingresos del mes</p>
                </article>
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"><?php echo e($monthPreviousLabel); ?></p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $incomePreviousMonth, $appCurrencyCode)); ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-300">Mes anterior</p>
                </article>
                <article class="rounded-xl border p-3 <?php echo e($monthlyIncomeDiff >= 0 ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15'); ?>">
                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200'); ?>">Variación</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Caja y ventas hoy','class' => 'xl:col-span-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Caja y ventas hoy','class' => 'xl:col-span-1']); ?>
            <div class="space-y-3">
                <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Ingresos hoy</p>
                    <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $incomeToday, $appCurrencyCode)); ?></p>
                </article>
                <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Egresos hoy</p>
                    <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $expenseToday, $appCurrencyCode)); ?></p>
                </article>
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Balance hoy</p>
                    <p class="mt-1 text-2xl font-black <?php echo e((float) $todayBalance >= 0 ? 'text-cyan-800 dark:text-cyan-100' : 'text-rose-800 dark:text-rose-100'); ?>"><?php echo e($currencyFormatter::format((float) $todayBalance, $appCurrencyCode)); ?></p>
                </article>
                <article class="rounded-xl border p-3 <?php echo e((float) $netYearToDate >= 0 ? 'border-violet-200 bg-violet-50 dark:border-violet-400/40 dark:bg-violet-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15'); ?>">
                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200'); ?>">Ganancia del año</p>
                    <p class="mt-1 text-2xl font-black <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-800 dark:text-violet-100' : 'text-rose-800 dark:text-rose-100'); ?>"><?php echo e($currencyFormatter::format((float) $netYearToDate, $appCurrencyCode)); ?></p>
                    <p class="text-xs <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200'); ?>">Ingresos - egresos acumulados del año</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Estado de caja actual','subtitle' => 'Control rápido del turno activo.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Estado de caja actual','subtitle' => 'Control rápido del turno activo.']); ?>
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
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'secondary']); ?>Ir a caja por turno <?php echo $__env->renderComponent(); ?>
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
                    <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Movimientos de hoy</p>
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
                <h3 id="modalMovementsTitle" class="ui-heading text-lg">Últimos movimientos de caja</h3>
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
                            <td colspan="<?php echo e($isGlobalScope ? 7 : 6); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay movimientos registrados aún.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .header-live-pill {
        animation: headerLiveGlow 1.9s ease-in-out infinite;
    }

    .header-live-pill .live-core-dot {
        animation: headerLiveBeat 1.15s ease-in-out infinite;
    }

    @keyframes headerLiveGlow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.12), 0 0 18px rgba(16, 185, 129, 0.22);
        }
        50% {
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.22), 0 0 28px rgba(16, 185, 129, 0.36);
        }
    }

    @keyframes headerLiveBeat {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.22);
        }
    }
</style>
<?php $__env->stopPush(); ?>

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

        const headerLiveRoot = document.getElementById('header-live-clients');
        if (headerLiveRoot) {
            const headerLiveCount = document.getElementById('header-live-count');
            const liveUrl = String(headerLiveRoot.dataset.liveUrl || '').trim();
            let currentCount = Number(headerLiveCount?.textContent || 0);

            const animateCount = function (nextCount) {
                const target = Number.isFinite(nextCount) ? Math.max(0, Math.floor(nextCount)) : 0;
                if (!headerLiveCount) {
                    currentCount = target;
                    return;
                }
                if (target === currentCount) {
                    headerLiveCount.textContent = String(target);
                    return;
                }

                const from = currentCount;
                const diff = target - from;
                const steps = Math.min(16, Math.max(6, Math.abs(diff)));
                let frame = 0;
                const tick = function () {
                    frame += 1;
                    const progress = frame / steps;
                    const value = Math.round(from + diff * progress);
                    headerLiveCount.textContent = String(value);
                    if (frame < steps) {
                        window.requestAnimationFrame(tick);
                    } else {
                        currentCount = target;
                        headerLiveCount.textContent = String(target);
                    }
                };
                window.requestAnimationFrame(tick);
            };

            const refreshHeaderLive = async function () {
                if (liveUrl === '') return;
                try {
                    const response = await fetch(liveUrl, {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!response.ok) return;
                    const payload = await response.json();
                    if (!payload || payload.ok !== true) return;

                    animateCount(Number(payload.count || 0));
                } catch (error) {
                    // ignore transient network errors
                }
            };

            refreshHeaderLive();
            window.setInterval(refreshHeaderLive, 20000);
        }
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/panel/index.blade.php ENDPATH**/ ?>