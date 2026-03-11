

<?php $__env->startSection('title', 'Caja profesional'); ?>
<?php $__env->startSection('page-title', 'Caja por turno'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .cash-page [data-tone='ok'] { color: rgb(5 150 105); }
    .cash-page [data-tone='warn'] { color: rgb(217 119 6); }
    .cash-page [data-tone='bad'] { color: rgb(225 29 72); }
    .theme-dark .cash-page [data-tone='ok'] { color: rgb(110 231 183); }
    .theme-dark .cash-page [data-tone='warn'] { color: rgb(252 211 77); }
    .theme-dark .cash-page [data-tone='bad'] { color: rgb(251 113 133); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $currencyCode = $appCurrencyCode ?? null;
        $currencySymbol = trim((string) ($appCurrencySymbol ?? '$'));

        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $user = auth()->user();
        $isOwnerUser = (bool) ($user?->isOwner());
        $isCashierUser = (bool) ($user?->isCashier());
        $isCashAdmin = (bool) ($isCashAdmin ?? ($user && ($user->gym_id === null || $isOwnerUser)));
        $canApproveDifference = (bool) ($canApproveCashDifference ?? $isCashAdmin);

        $routeHasVoidMovement = \Illuminate\Support\Facades\Route::has('cash.movements.void');
        $voidRouteTemplate = $routeHasVoidMovement ? route('cash.movements.void', ['movement' => '__MOVEMENT__']) : '';

        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isCurrentCashView = ! $isGlobalScope && array_key_exists('openSession', get_defined_vars());
        $openSession = $openSession ?? null;
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $cashWriteBlocked = (bool) ($cashWriteBlocked ?? false);
        $cashWriteBlockedReason = trim((string) ($cashWriteBlockedReason ?? ''));
        $canOpenCash = (bool) ($canOpenCash ?? $isOwnerUser);
        $canCloseCash = (bool) ($canCloseCash ?? $isOwnerUser);
        $canManageMovements = (bool) ($canManageMovements ?? true);
        $recentClosedSessions = $recentClosedSessions ?? collect();
    ?>

    <div class="cash-page space-y-4"
         data-module="cash-index"
         data-currency-symbol="<?php echo e($currencySymbol); ?>"
         data-void-route-template="<?php echo e($voidRouteTemplate); ?>">
        <?php if($isCurrentCashView && $isCashierScoped): ?>
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Vista privada','subtitle' => 'Solo ves tus cobros, egresos y movimientos personales dentro del turno.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Vista privada','subtitle' => 'Solo ves tus cobros, egresos y movimientos personales dentro del turno.']); ?>
                <p class="ui-alert ui-alert-info">Los acumulados del gimnasio quedan ocultos para tu perfil.</p>
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
        <?php if($isCurrentCashView): ?>
            <?php if(! $openSession): ?>
                <?php if($cashWriteBlocked): ?>
                    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Caja en solo lectura','subtitle' => 'Operación administrada desde sede principal.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Caja en solo lectura','subtitle' => 'Operación administrada desde sede principal.']); ?>
                        <p class="ui-alert ui-alert-warning">
                            <?php echo e($cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'No tienes permisos para abrir o cerrar caja en esta sucursal.'); ?>

                        </p>
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
                <?php elseif(! $canOpenCash): ?>
                    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Apertura restringida','subtitle' => 'Solo usuarios autorizados pueden abrir caja.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Apertura restringida','subtitle' => 'Solo usuarios autorizados pueden abrir caja.']); ?>
                        <p class="ui-alert ui-alert-warning">
                            Tu perfil no tiene permiso para abrir caja. Solicita al dueño del gimnasio que abra el turno o te habilite este permiso.
                        </p>
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
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Abrir turno','subtitle' => 'Debes abrir caja para cobrar membresías o registrar movimientos.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Abrir turno','subtitle' => 'Debes abrir caja para cobrar membresías o registrar movimientos.']); ?>
                        <form method="POST" action="<?php echo e(route('cash.open')); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="space-y-1 text-sm font-semibold ui-muted">
                                    <span>Monto inicial (obligatorio)</span>
                                    <input type="number" name="opening_balance" step="0.01" min="0" value="<?php echo e(old('opening_balance')); ?>" required class="ui-input">
                                </label>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Notas</span>
                                    <textarea name="notes" rows="3" class="ui-input"><?php echo e(old('notes')); ?></textarea>
                                </label>
                            </div>

                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'success','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'success','size' => 'lg']); ?>Abrir turno <?php echo $__env->renderComponent(); ?>
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

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial reciente de cierres','subtitle' => 'Fecha, hora, motivo y notas del cierre.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial reciente de cierres','subtitle' => 'Fecha, hora, motivo y notas del cierre.']); ?>
                    <?php echo $__env->make('cash.partials.closure-history', [
                        'sessions' => $recentClosedSessions,
                        'currencyFormatter' => $currencyFormatter,
                        'currencyCode' => $currencyCode,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
            <?php else: ?>
                <?php
                    $activeSummary = $summary ?? ['income_total' => 0, 'expense_total' => 0, 'expected_balance' => 0, 'movements_count' => 0];
                    $activeMethodTotals = $methodTotals ?? collect();
                    $closingSummary = $closeSummary ?? $activeSummary;
                    $closingMethodTotals = $closeMethodTotals ?? $activeMethodTotals;
                    $activeMovements = $latestMovements ?? collect();
                    $scopedNetTotal = round((float) ($activeSummary['income_total'] ?? 0) - (float) ($activeSummary['expense_total'] ?? 0), 2);

                    $methodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($activeMethodTotals) {
                        $row = $activeMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $closeMethodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($closingMethodTotals) {
                        $row = $closingMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $expectedCash = (float) $openSession->opening_balance + (float) ($closeMethodMap->get('cash')->income_total ?? 0) - (float) ($closeMethodMap->get('cash')->expense_total ?? 0);
                    $expectedCard = (float) ($closeMethodMap->get('card')->income_total ?? 0) - (float) ($closeMethodMap->get('card')->expense_total ?? 0);
                    $expectedTransfer = (float) ($closeMethodMap->get('transfer')->income_total ?? 0) - (float) ($closeMethodMap->get('transfer')->expense_total ?? 0);
                    $expectedTotal = round($expectedCash + $expectedCard + $expectedTransfer, 2);
                ?>

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tu producciÃ³n en el turno #'.$openSession->id : 'Turno activo #'.$openSession->id).'','subtitle' => 'Apertura '.e($openSession->opened_at?->format('Y-m-d H:i')).' por '.e($openSession->openedBy?->name ?? 'N/D').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tu producciÃ³n en el turno #'.$openSession->id : 'Turno activo #'.$openSession->id).'','subtitle' => 'Apertura '.e($openSession->opened_at?->format('Y-m-d H:i')).' por '.e($openSession->openedBy?->name ?? 'N/D').'']); ?>
                    <?php if($isCashierScoped): ?>
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Tus ingresos</p>
                                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $activeSummary['income_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Tus egresos</p>
                                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $activeSummary['expense_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Tu balance</p>
                                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format($scopedNetTotal, $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Tus movimientos</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) $activeSummary['movements_count']); ?></p>
                            </article>
                        </div>
                    <?php else: ?>
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                            <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $openSession->opening_balance, $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Ingresos</p>
                                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $activeSummary['income_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Egresos</p>
                                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $activeSummary['expense_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperados</p>
                                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format($expectedTotal, $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Saldo actual</p>
                                <p class="mt-1 text-2xl font-black text-violet-800 dark:text-violet-100"><?php echo e($currencyFormatter::format($expectedTotal, $currencyCode)); ?></p>
                            </article>
                            <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) $activeSummary['movements_count']); ?></p>
                            </article>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        <?php $__currentLoopData = $methodMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodTotal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"><?php echo e($methodLabels[$methodTotal->method] ?? $methodTotal->method); ?></p>
                                <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">Movimientos: <strong><?php echo e($methodTotal->movements_count); ?></strong></p>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">+ <?php echo e($currencyFormatter::format((float) $methodTotal->income_total, $currencyCode, true)); ?></p>
                                <p class="text-sm text-rose-700 dark:text-rose-300">- <?php echo e($currencyFormatter::format((float) $methodTotal->expense_total, $currencyCode, true)); ?></p>
                            </article>
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

                <section class="grid gap-4 xl:grid-cols-3">
                    <?php if($cashWriteBlocked): ?>
                        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Operaciones de caja bloqueadas','class' => 'xl:col-span-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Operaciones de caja bloqueadas','class' => 'xl:col-span-3']); ?>
                            <p class="ui-alert ui-alert-warning">
                                <?php echo e($cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'Esta sucursal opera con caja controlada por sede principal.'); ?>

                            </p>
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
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Registrar movimiento','class' => 'xl:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Registrar movimiento','class' => 'xl:col-span-2']); ?>
                            <?php if(! $canManageMovements): ?>
                                <p class="ui-alert ui-alert-warning">
                                    Tu perfil no tiene permiso para registrar cobros o movimientos de caja.
                                </p>
                            <?php else: ?>
                                <form id="cash-movement-form" method="POST" action="<?php echo e(route('cash.movements.store')); ?>" class="space-y-4" data-high-threshold="100">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" id="movement-high-confirmed" name="high_amount_confirmed" value="0">

                                <div id="movement-guard-alert" class="hidden ui-alert ui-alert-warning"></div>

                                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Tipo</span>
                                        <select id="movement-type" name="type" required class="ui-input" aria-label="Tipo de movimiento">
                                            <option value="">Seleccione</option>
                                            <option value="income" <?php if(old('type') === 'income'): echo 'selected'; endif; ?>>Ingreso</option>
                                            <option value="expense" <?php if(old('type') === 'expense'): echo 'selected'; endif; ?>>Egreso</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Método</span>
                                        <select name="method" required class="ui-input" aria-label="Método de pago">
                                            <option value="">Seleccione</option>
                                            <option value="cash" <?php if(old('method') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                                            <option value="card" <?php if(old('method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                                            <option value="transfer" <?php if(old('method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Monto</span>
                                        <input id="movement-amount" type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required class="ui-input" aria-label="Monto">
                                    </label>

                                    <label id="movement-expense-category-wrap" class="hidden space-y-1 text-sm font-semibold ui-muted">
                                        <span>Categoria egreso (opcional)</span>
                                        <select id="movement-expense-category" name="expense_category" class="ui-input" aria-label="Categoria de egreso">
                                            <option value="">Sin categoria</option>
                                            <option value="insumos" <?php if(old('expense_category') === 'insumos'): echo 'selected'; endif; ?>>Insumos</option>
                                            <option value="servicios" <?php if(old('expense_category') === 'servicios'): echo 'selected'; endif; ?>>Servicios</option>
                                            <option value="mantenimiento" <?php if(old('expense_category') === 'mantenimiento'): echo 'selected'; endif; ?>>Mantenimiento</option>
                                            <option value="nomina" <?php if(old('expense_category') === 'nomina'): echo 'selected'; endif; ?>>Nomina</option>
                                            <option value="otros" <?php if(old('expense_category') === 'otros'): echo 'selected'; endif; ?>>Otros</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2 xl:col-span-4">
                                        <span id="movement-description-label">Descripción (obligatoria)</span>
                                        <textarea id="movement-description" name="description" rows="2" required class="ui-input" aria-label="Descripción" placeholder="Ingresa descripción obligatoria."><?php echo e(old('description')); ?></textarea>
                                    </label>
                                </div>

                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'movement-submit','type' => 'submit','variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'movement-submit','type' => 'submit','variant' => 'success']); ?>Registrar ingreso <?php echo $__env->renderComponent(); ?>
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
                            <?php endif; ?>

                            <div class="mt-6 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <div class="mb-3">
                                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Historial reciente de cierres</h3>
                                    <p class="text-sm ui-muted">Aqui puedes leer la fecha, hora, motivo y notas del ultimo cierre de caja.</p>
                                </div>

                                <?php echo $__env->make('cash.partials.closure-history', [
                                    'sessions' => $recentClosedSessions,
                                    'currencyFormatter' => $currencyFormatter,
                                    'currencyCode' => $currencyCode,
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($canCloseCash ? 'Cerrar turno' : 'Cierre restringido').'','subtitle' => ''.e($canCloseCash ? 'Conteo por método y control de diferencias.' : 'Solo usuarios autorizados pueden ver y ejecutar el cierre completo.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($canCloseCash ? 'Cerrar turno' : 'Cierre restringido').'','subtitle' => ''.e($canCloseCash ? 'Conteo por método y control de diferencias.' : 'Solo usuarios autorizados pueden ver y ejecutar el cierre completo.').'']); ?>
                            <?php if(! $canCloseCash): ?>
                                <p class="ui-alert ui-alert-warning mb-3">
                                    Tu perfil no tiene permiso para cerrar caja. Esta acción la realiza el dueño o un usuario autorizado.
                                </p>
                            <?php else: ?>

                            <div id="close-form-alert" class="hidden ui-alert ui-alert-warning"></div>

                            <div class="space-y-2 text-sm">
                                <p class="ui-muted">Esperado total: <strong><?php echo e($currencyFormatter::format($expectedTotal, $currencyCode)); ?></strong></p>
                                <p class="ui-muted">Estado de cierre: <strong id="close-status-text" data-tone="ok">CUADRA</strong></p>
                            </div>

                            <form id="cash-close-form" method="POST" action="<?php echo e(route('cash.close')); ?>" class="mt-4 space-y-4"
                                  data-expected-cash="<?php echo e(number_format($expectedCash, 2, '.', '')); ?>"
                                  data-expected-card="<?php echo e(number_format($expectedCard, 2, '.', '')); ?>"
                                  data-expected-transfer="<?php echo e(number_format($expectedTransfer, 2, '.', '')); ?>"
                                  data-can-approve-difference="<?php echo e($canApproveDifference ? '1' : '0'); ?>">
                                <?php echo csrf_field(); ?>
                                <input id="close-closing-balance" type="hidden" name="closing_balance" value="<?php echo e(old('closing_balance')); ?>">
                                <input id="close-difference-approved" type="hidden" name="difference_approved" value="0">

                            <div class="grid gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <div class="grid grid-cols-4 gap-2 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">
                                    <span>Método</span>
                                    <span class="text-right">Esperado</span>
                                    <span class="text-right">Contado</span>
                                    <span class="text-right">Diferencia</span>
                                </div>

                                <?php $__currentLoopData = ['cash' => 'efectivo_contado', 'card' => 'tarjeta_contado', 'transfer' => 'transferencia_contado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $fieldName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $expectedByMethod = $methodKey === 'cash' ? $expectedCash : ($methodKey === 'card' ? $expectedCard : $expectedTransfer);
                                    ?>
                                    <div class="grid grid-cols-4 items-center gap-2 text-sm">
                                        <span class="font-semibold text-slate-700 dark:text-slate-200"><?php echo e($methodLabels[$methodKey]); ?></span>
                                        <span class="text-right text-slate-700 dark:text-slate-200"><?php echo e($currencyFormatter::format($expectedByMethod, $currencyCode)); ?></span>
                                        <input id="counted-<?php echo e($methodKey); ?>" class="ui-input text-right" type="number" min="0" step="0.01" name="<?php echo e($fieldName); ?>" value="<?php echo e(old($fieldName, number_format($expectedByMethod, 2, '.', ''))); ?>" aria-label="Contado <?php echo e($methodLabels[$methodKey]); ?>">
                                        <span id="difference-<?php echo e($methodKey); ?>" class="text-right font-bold" data-tone="ok"><?php echo e($currencyFormatter::format(0, $currencyCode)); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Diferencia total</span>
                                    <span id="difference-total" class="text-lg font-black" data-tone="ok"><?php echo e($currencyFormatter::format(0, $currencyCode)); ?></span>
                                </div>
                            </div>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Motivo de diferencia (obligatorio si no cuadra)</span>
                                <textarea id="difference-reason" name="difference_reason" rows="2" class="ui-input" placeholder="Explica por qué hay diferencia."><?php echo e(old('difference_reason')); ?></textarea>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Notas de cierre</span>
                                <textarea name="notes" rows="3" class="ui-input"><?php echo e(old('notes')); ?></textarea>
                            </label>

                            <?php if(! $canApproveDifference): ?>
                                <p class="ui-alert ui-alert-warning text-xs">Solo Admin puede confirmar cierre con diferencia.</p>
                            <?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'close-submit','type' => 'submit','variant' => 'danger','size' => 'lg','class' => 'w-full justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'close-submit','type' => 'submit','variant' => 'danger','size' => 'lg','class' => 'w-full justify-center']); ?>Cerrar turno <?php echo $__env->renderComponent(); ?>
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
                    <?php endif; ?>
                </section>

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tus últimos 10 movimientos' : 'últimos 10 movimientos').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tus últimos 10 movimientos' : 'últimos 10 movimientos').'']); ?>
                    <div class="overflow-x-auto">
                        <table class="ui-table min-w-[1180px]">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Cliente</th>
                                <th>Alta cliente</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $activeMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $movementIsVoided = (string) ($movement->status ?? '') === 'voided' || !empty($movement->voided_at) || !empty($movement->void_reason);
                                ?>
                                <tr class="<?php echo e($movementIsVoided ? 'opacity-70' : ''); ?>">
                                    <td><?php echo e($movement->occurred_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                    <td><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $movement->type === 'income' ? 'success' : 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($movement->type === 'income' ? 'success' : 'danger')]); ?><?php echo e($movement->type); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                                    <td><?php echo e($methodLabels[$movement->method] ?? $movement->method); ?></td>
                                    <td class="font-semibold <?php echo e($movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'); ?>">
                                        <?php echo e($movement->type === 'income' ? '+' : '-'); ?><?php echo e($currencyFormatter::format((float) $movement->amount, $currencyCode, true)); ?>

                                    </td>
                                    <td><?php echo e($movement->membership?->client?->full_name ?? '-'); ?></td>
                                    <td><?php echo e(\App\Support\ClientAudit::actorDisplay((string) ($movement->membership?->client?->created_by_name_snapshot ?? ''), (string) ($movement->membership?->client?->created_by_role_snapshot ?? ''))); ?></td>
                                    <td><?php echo e($movement->createdBy?->name ?? '-'); ?></td>
                                    <td><?php echo e($movement->description ?: '-'); ?></td>
                                    <td>
                                        <?php if($movementIsVoided): ?>
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning']); ?>Anulado <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                        <?php else: ?>
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success']); ?>Activo <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if($movementIsVoided): ?>
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Sin acción</span>
                                        <?php elseif(! $isCashAdmin): ?>
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Solo Admin</span>
                                        <?php elseif(! $routeHasVoidMovement): ?>
                                            <button type="button" class="ui-button ui-button-muted px-2 py-1 text-xs" disabled title="Falta route cash.movements.void">Anular</button>
                                        <?php else: ?>
                                            <button type="button" class="ui-button ui-button-danger px-2 py-1 text-xs js-open-void-modal" data-movement-id="<?php echo e($movement->id); ?>" data-movement-label="#<?php echo e($movement->id); ?> <?php echo e($movement->type); ?> <?php echo e($currencyFormatter::format((float) $movement->amount, $currencyCode)); ?>" aria-label="Anular movimiento <?php echo e($movement->id); ?>" title="Anular movimiento">Anular</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-sm text-slate-500 dark:text-slate-300"><?php echo e($isCashierScoped ? 'Aún no tienes movimientos en este turno.' : 'Aún no hay movimientos en este turno.'); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index')),'variant' => 'primary']); ?>Cobrar membresía <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if(! $isCashierUser || $canCloseCash): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'cash-go-history','href' => route('cash.sessions.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'cash-go-history','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.sessions.index')),'variant' => 'secondary']); ?>Ver historial de caja <?php echo $__env->renderComponent(); ?>
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
                        <?php if(! $isCashierUser): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.income'),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.income')),'variant' => 'ghost']); ?>Ver reporte de ingresos <?php echo $__env->renderComponent(); ?>
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

                <?php echo $__env->make('cash.partials.session-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php
                $historyRows = $sessions ?? collect();
            ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial de caja','subtitle' => 'Revisión de cierres, diferencias y responsables.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de caja','subtitle' => 'Revisión de cierres, diferencias y responsables.']); ?>
                <?php if($isGlobalScope): ?>
                    <p class="mb-4 ui-alert ui-alert-info">
                        Modo global activo: historial consolidado de todas tus sedes en solo lectura.
                    </p>
                <?php endif; ?>
                <div class="overflow-x-auto">
                    <table class="ui-table min-w-[1480px]">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <?php if($isGlobalScope): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Apertura por</th>
                            <th>Cierre por</th>
                            <th>Tipo</th>
                            <th>Mensaje</th>
                            <th>Esperado</th>
                            <th>Cierre</th>
                            <th>Diferencia</th>
                            <th>Motivo</th>
                            <th>Notas</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $historyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $difference = (float) ($session->difference ?? 0);
                                $closedWithDifference = (string) $session->status === 'closed' && abs($difference) > 0.00001;
                            ?>
                            <tr>
                                <td><?php echo e($session->id); ?></td>
                                <?php if($isGlobalScope): ?>
                                    <td><?php echo e($session->gym?->name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td><?php echo e($session->opened_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td><?php echo e($session->closed_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td><?php echo e($session->openedBy?->name ?? '-'); ?></td>
                                <td><?php echo e($session->wasAutoClosedAtMidnight() ? 'Sistema' : ($session->closedBy?->name ?? '-')); ?></td>
                                <td><?php echo e($session->closeSourceLabel()); ?></td>
                                <td><?php echo e($session->closeMessage()); ?></td>
                                <td><?php echo e($currencyFormatter::format((float) ($session->expected_balance ?? 0), $currencyCode)); ?></td>
                                <td><?php echo e($session->closing_balance !== null ? $currencyFormatter::format((float) $session->closing_balance, $currencyCode) : '-'); ?></td>
                                <td class="font-bold <?php echo e($difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200')); ?>"><?php echo e($currencyFormatter::format($difference, $currencyCode)); ?></td>
                                <td><?php echo e($session->difference_reason ?: '-'); ?></td>
                                <td><?php echo e($session->closing_notes ?: '-'); ?></td>
                                <td>
                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => (string) $session->status === 'open' ? 'info' : 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $session->status === 'open' ? 'info' : 'success')]); ?><?php echo e($session->status); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    <?php if($closedWithDifference): ?>
                                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning']); ?>Cerro con diferencia <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.sessions.show', $session->id),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.sessions.show', $session->id)),'variant' => 'ghost','size' => 'sm']); ?>Detalle <?php echo $__env->renderComponent(); ?>
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
                                <td colspan="<?php echo e($isGlobalScope ? 15 : 14); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay sesiones registradas.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(method_exists($historyRows, 'links')): ?>
                    <div class="mt-4"><?php echo e($historyRows->links()); ?></div>
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
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/cash/index.blade.php ENDPATH**/ ?>