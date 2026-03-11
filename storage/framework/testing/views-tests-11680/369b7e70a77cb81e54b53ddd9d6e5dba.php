

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
        $isCashAdmin = (bool) ($isCashAdmin ?? ($user && ($user->gym_id === null || str_contains(strtolower((string) $user->name), 'admin'))));
        $canApproveDifference = (bool) ($canApproveCashDifference ?? $isCashAdmin);

        $routeHasVoidMovement = \Illuminate\Support\Facades\Route::has('cash.movements.void');
        $voidRouteTemplate = $routeHasVoidMovement ? route('cash.movements.void', ['movement' => '__MOVEMENT__']) : '';

        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $isCurrentCashView = ! $isGlobalScope && array_key_exists('openSession', get_defined_vars());
        $openSession = $openSession ?? null;
    ?>

    <div class="cash-page space-y-4">
        <?php if($isCurrentCashView): ?>
            <?php if(! $openSession): ?>
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
            <?php else: ?>
                <?php
                    $activeSummary = $summary ?? ['income_total' => 0, 'expense_total' => 0, 'expected_balance' => 0, 'movements_count' => 0];
                    $activeMethodTotals = $methodTotals ?? collect();
                    $activeMovements = $latestMovements ?? collect();

                    $methodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($activeMethodTotals) {
                        $row = $activeMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $expectedCash = (float) $openSession->opening_balance + (float) ($methodMap->get('cash')->income_total ?? 0) - (float) ($methodMap->get('cash')->expense_total ?? 0);
                    $expectedCard = (float) ($methodMap->get('card')->income_total ?? 0) - (float) ($methodMap->get('card')->expense_total ?? 0);
                    $expectedTransfer = (float) ($methodMap->get('transfer')->income_total ?? 0) - (float) ($methodMap->get('transfer')->expense_total ?? 0);
                    $expectedTotal = round($expectedCash + $expectedCard + $expectedTransfer, 2);
                ?>

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Turno activo #'.e($openSession->id).'','subtitle' => 'Apertura '.e($openSession->opened_at?->format('Y-m-d H:i')).' por '.e($openSession->openedBy?->name ?? 'N/D').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Turno activo #'.e($openSession->id).'','subtitle' => 'Apertura '.e($openSession->opened_at?->format('Y-m-d H:i')).' por '.e($openSession->openedBy?->name ?? 'N/D').'']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Cerrar turno','subtitle' => 'Conteo por método y control de diferencias.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cerrar turno','subtitle' => 'Conteo por método y control de diferencias.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'últimos 10 movimientos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'últimos 10 movimientos']); ?>
                    <div class="overflow-x-auto">
                        <table class="ui-table min-w-[1180px]">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Cliente</th>
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
                                    <td colspan="9" class="text-center text-sm text-slate-500 dark:text-slate-300">Aún no hay movimientos en este turno.</td>
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
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.sessions.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.sessions.index')),'variant' => 'secondary']); ?>Ver historial de caja <?php echo $__env->renderComponent(); ?>
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

                <div id="high-amount-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="highAmountTitle">
                    <div class="ui-modal-panel max-w-md">
                        <h3 id="highAmountTitle" class="ui-heading text-lg">Confirmar monto alto</h3>
                        <p class="mt-2 text-sm ui-muted">Estas registrando un movimiento alto: <strong id="high-amount-value"><?php echo e($currencySymbol); ?>0.00</strong></p>
                        <p class="mt-1 text-xs ui-muted">Verifica tipo y método antes de continuar.</p>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-close-high-modal>Cancelar</button>
                            <button type="button" class="ui-button ui-button-primary" id="confirm-high-amount">Confirmar y guardar</button>
                        </div>
                    </div>
                </div>

                <div id="difference-approval-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="differenceApprovalTitle">
                    <div class="ui-modal-panel max-w-md">
                        <h3 id="differenceApprovalTitle" class="ui-heading text-lg">Aprobación supervisor</h3>
                        <p class="mt-2 text-sm ui-muted">El cierre tiene diferencia. Solo Admin puede aprobarlo.</p>
                        <label class="mt-3 block space-y-1 text-sm font-semibold ui-muted">
                            <span>Password/PIN admin</span>
                            <input id="difference-approval-password" type="password" class="ui-input" autocomplete="new-password">
                        </label>
                        <p id="difference-approval-error" class="mt-2 hidden text-xs font-semibold text-rose-600 dark:text-rose-300">Ingresa password/PIN para continuar.</p>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-close-difference-modal>Cancelar</button>
                            <button type="button" class="ui-button ui-button-danger" id="confirm-close-with-diff">Aprobar cierre</button>
                        </div>
                    </div>
                </div>

                <div id="void-movement-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="voidMovementTitle">
                    <div class="ui-modal-panel max-w-md">
                        <h3 id="voidMovementTitle" class="ui-heading text-lg">Anular movimiento</h3>
                        <p class="mt-2 text-sm ui-muted">Movimiento: <strong id="void-movement-label">-</strong></p>

                        <form id="void-movement-form" method="POST" action="<?php echo e($voidRouteTemplate); ?>" class="mt-3 space-y-3">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Motivo de anulación (obligatorio)</span>
                                <textarea name="void_reason" id="void-reason" rows="3" required class="ui-input" placeholder="Ej: ingreso duplicado o error de caja."></textarea>
                            </label>

                            <?php if(! $routeHasVoidMovement): ?>
                                <p class="ui-alert ui-alert-danger text-xs">Falta route `cash.movements.void` en backend.</p>
                            <?php endif; ?>

                            <div class="flex justify-end gap-2">
                                <button type="button" class="ui-button ui-button-ghost" data-close-void-modal>Cancelar</button>
                                <button type="submit" class="ui-button ui-button-danger" <?php if(! $routeHasVoidMovement): echo 'disabled'; endif; ?>>Anular movimiento</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php
                $historyRows = $sessions ?? collect();
            ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Sesiones donde participaste' : 'Historial de caja').'','subtitle' => ''.e($isCashierScoped ? 'Se conserva el historial, pero se ocultan los montos globales del gimnasio.' : 'Revisión de cierres, diferencias y responsables.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Sesiones donde participaste' : 'Historial de caja').'','subtitle' => ''.e($isCashierScoped ? 'Se conserva el historial, pero se ocultan los montos globales del gimnasio.' : 'Revisión de cierres, diferencias y responsables.').'']); ?>
                <div class="overflow-x-auto">
                    <table class="ui-table <?php echo e($isCashierScoped ? 'min-w-[1120px]' : 'min-w-[1480px]'); ?>">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Apertura por</th>
                            <th>Cierre por</th>
                            <th>Tipo</th>
                            <th>Mensaje</th>
                            <?php if (! ($isCashierScoped)): ?>
                                <th>Esperado</th>
                                <th>Cierre</th>
                                <th>Diferencia</th>
                            <?php endif; ?>
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
                                <td><?php echo e($session->opened_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td><?php echo e($session->closed_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td><?php echo e($session->openedBy?->name ?? '-'); ?></td>
                                <td><?php echo e($session->wasAutoClosedAtMidnight() ? 'Sistema' : ($session->closedBy?->name ?? '-')); ?></td>
                                <td><?php echo e($session->closeSourceLabel()); ?></td>
                                <td><?php echo e($session->closeMessage()); ?></td>
                                <?php if (! ($isCashierScoped)): ?>
                                    <td><?php echo e($currencyFormatter::format((float) ($session->expected_balance ?? 0), $currencyCode)); ?></td>
                                    <td><?php echo e($session->closing_balance !== null ? $currencyFormatter::format((float) $session->closing_balance, $currencyCode) : '-'); ?></td>
                                    <td class="font-bold <?php echo e($difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200')); ?>"><?php echo e($currencyFormatter::format($difference, $currencyCode)); ?></td>
                                <?php endif; ?>
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
                                <td colspan="<?php echo e($isCashierScoped ? 11 : 14); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay sesiones registradas.</td>
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

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const currencySymbol = <?php echo json_encode($currencySymbol, 15, 512) ?>;

        function formatMoney(value) {
            const num = Number(value || 0);
            return (num < 0 ? '-' : '') + currencySymbol + Math.abs(num).toFixed(2);
        }

        function openModal(el) {
            if (!el) return;
            el.classList.remove('hidden');
        }

        function closeModal(el) {
            if (!el) return;
            el.classList.add('hidden');
        }

        const movementForm = document.getElementById('cash-movement-form');
        const movementType = document.getElementById('movement-type');
        const movementDescription = document.getElementById('movement-description');
        const movementDescriptionLabel = document.getElementById('movement-description-label');
        const movementCategoryWrap = document.getElementById('movement-expense-category-wrap');
        const movementSubmit = document.getElementById('movement-submit');
        const movementAmount = document.getElementById('movement-amount');
        const highConfirmed = document.getElementById('movement-high-confirmed');
        const guardAlert = document.getElementById('movement-guard-alert');

        const highAmountModal = document.getElementById('high-amount-modal');
        const highAmountValue = document.getElementById('high-amount-value');
        const highAmountConfirm = document.getElementById('confirm-high-amount');

        const closeForm = document.getElementById('cash-close-form');
        const closeStatusText = document.getElementById('close-status-text');
        const differenceCash = document.getElementById('difference-cash');
        const differenceCard = document.getElementById('difference-card');
        const differenceTransfer = document.getElementById('difference-transfer');
        const differenceTotal = document.getElementById('difference-total');
        const differenceReason = document.getElementById('difference-reason');
        const closeBalanceInput = document.getElementById('close-closing-balance');
        const closeAlert = document.getElementById('close-form-alert');
        const differenceApproved = document.getElementById('close-difference-approved');

        const differenceApprovalModal = document.getElementById('difference-approval-modal');
        const differenceApprovalPassword = document.getElementById('difference-approval-password');
        const differenceApprovalError = document.getElementById('difference-approval-error');
        const confirmCloseWithDiff = document.getElementById('confirm-close-with-diff');

        const voidModal = document.getElementById('void-movement-modal');
        const voidLabel = document.getElementById('void-movement-label');
        const voidForm = document.getElementById('void-movement-form');
        const voidRouteTemplate = <?php echo json_encode($voidRouteTemplate, 15, 512) ?>;

        function setTone(el, value) {
            if (!el) return;
            const tone = value > 0 ? 'warn' : (value < 0 ? 'bad' : 'ok');
            el.setAttribute('data-tone', tone);
        }

        function updateMovementMode() {
            if (!movementType) return;
            const isExpense = movementType.value === 'expense';

            if (movementDescription) {
                movementDescription.required = true;
                movementDescription.placeholder = isExpense ? 'Motivo obligatorio del egreso.' : 'Ingresa descripción obligatoria.';
            }

            if (movementDescriptionLabel) {
                movementDescriptionLabel.textContent = 'Descripción (obligatoria)';
            }

            if (movementCategoryWrap) {
                movementCategoryWrap.classList.toggle('hidden', !isExpense);
            }

            if (movementSubmit) {
                movementSubmit.textContent = isExpense ? 'Registrar egreso' : 'Registrar ingreso';
                movementSubmit.classList.toggle('ui-button-danger', isExpense);
                movementSubmit.classList.toggle('ui-button-success', !isExpense);
            }

            if (guardAlert) {
                if (isExpense) {
                    guardAlert.classList.remove('hidden');
                    guardAlert.textContent = 'Egreso requiere descripción obligatoria para auditoría.';
                } else {
                    guardAlert.classList.add('hidden');
                    guardAlert.textContent = '';
                }
            }
        }

        movementType?.addEventListener('change', updateMovementMode);
        updateMovementMode();

        movementForm?.addEventListener('submit', function (event) {
            const amount = Number(movementAmount?.value || 0);
            const threshold = Number(movementForm.dataset.highThreshold || 100);
            const alreadyConfirmed = highConfirmed?.value === '1';
            const descriptionValue = (movementDescription?.value || '').trim();

            if (descriptionValue === '') {
                event.preventDefault();
                movementDescription?.focus();
                if (guardAlert) {
                    guardAlert.classList.remove('hidden');
                    guardAlert.textContent = 'Ingresa descripción obligatoria.';
                }
                return;
            }

            if (amount <= 0) {
                event.preventDefault();
                if (guardAlert) {
                    guardAlert.classList.remove('hidden');
                    guardAlert.textContent = 'Monto debe ser mayor a 0.';
                }
                return;
            }

            if (amount > threshold && !alreadyConfirmed) {
                event.preventDefault();
                if (highAmountValue) highAmountValue.textContent = formatMoney(amount);
                openModal(highAmountModal);
            }
        });

        highAmountConfirm?.addEventListener('click', function () {
            if (highConfirmed) highConfirmed.value = '1';
            closeModal(highAmountModal);
            movementForm?.submit();
        });

        document.querySelectorAll('[data-close-high-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(highAmountModal);
            });
        });

        function updateCloseMath() {
            if (!closeForm) return { totalDiff: 0, totalCounted: 0 };

            const expectedCash = Number(closeForm.dataset.expectedCash || 0);
            const expectedCard = Number(closeForm.dataset.expectedCard || 0);
            const expectedTransfer = Number(closeForm.dataset.expectedTransfer || 0);

            const countedCash = Number((document.getElementById('counted-cash') || {}).value || 0);
            const countedCard = Number((document.getElementById('counted-card') || {}).value || 0);
            const countedTransfer = Number((document.getElementById('counted-transfer') || {}).value || 0);

            const diffCash = Math.round((countedCash - expectedCash) * 100) / 100;
            const diffCard = Math.round((countedCard - expectedCard) * 100) / 100;
            const diffTransfer = Math.round((countedTransfer - expectedTransfer) * 100) / 100;
            const totalDiff = Math.round((diffCash + diffCard + diffTransfer) * 100) / 100;
            const totalCounted = Math.round((countedCash + countedCard + countedTransfer) * 100) / 100;

            if (differenceCash) {
                differenceCash.textContent = formatMoney(diffCash);
                setTone(differenceCash, diffCash);
            }
            if (differenceCard) {
                differenceCard.textContent = formatMoney(diffCard);
                setTone(differenceCard, diffCard);
            }
            if (differenceTransfer) {
                differenceTransfer.textContent = formatMoney(diffTransfer);
                setTone(differenceTransfer, diffTransfer);
            }
            if (differenceTotal) {
                differenceTotal.textContent = formatMoney(totalDiff);
                setTone(differenceTotal, totalDiff);
            }

            if (closeStatusText) {
                if (totalDiff === 0) {
                    closeStatusText.textContent = 'CUADRA';
                    closeStatusText.setAttribute('data-tone', 'ok');
                } else if (totalDiff > 0) {
                    closeStatusText.textContent = 'SOBRANTE ' + formatMoney(totalDiff);
                    closeStatusText.setAttribute('data-tone', 'warn');
                } else {
                    closeStatusText.textContent = 'FALTANTE ' + formatMoney(totalDiff);
                    closeStatusText.setAttribute('data-tone', 'bad');
                }
            }

            if (differenceReason) {
                differenceReason.required = totalDiff !== 0;
            }

            if (closeBalanceInput) {
                closeBalanceInput.value = totalCounted.toFixed(2);
            }

            return { totalDiff, totalCounted };
        }

        ['counted-cash', 'counted-card', 'counted-transfer'].forEach(function (id) {
            document.getElementById(id)?.addEventListener('input', updateCloseMath);
        });
        updateCloseMath();

        closeForm?.addEventListener('submit', function (event) {
            const calc = updateCloseMath();
            const canApprove = (closeForm.dataset.canApproveDifference || '0') === '1';
            const hasDifference = Math.abs(calc.totalDiff) > 0.0001;

            if (closeAlert) {
                closeAlert.classList.add('hidden');
                closeAlert.textContent = '';
            }

            if (!hasDifference) {
                if (differenceApproved) differenceApproved.value = '0';
                return;
            }

            if (!differenceReason || differenceReason.value.trim() === '') {
                event.preventDefault();
                if (closeAlert) {
                    closeAlert.classList.remove('hidden');
                    closeAlert.textContent = 'Debes ingresar un motivo porque el cierre no cuadra.';
                }
                return;
            }

            if (!canApprove) {
                event.preventDefault();
                if (closeAlert) {
                    closeAlert.classList.remove('hidden');
                    closeAlert.textContent = 'Solo Admin puede confirmar cierre con diferencia.';
                }
                return;
            }

            if (differenceApproved && differenceApproved.value !== '1') {
                event.preventDefault();
                if (differenceApprovalError) differenceApprovalError.classList.add('hidden');
                if (differenceApprovalPassword) differenceApprovalPassword.value = '';
                openModal(differenceApprovalModal);
            }
        });

        confirmCloseWithDiff?.addEventListener('click', function () {
            if (!differenceApprovalPassword || differenceApprovalPassword.value.trim() === '') {
                if (differenceApprovalError) differenceApprovalError.classList.remove('hidden');
                return;
            }

            if (differenceApproved) differenceApproved.value = '1';

            const oldHidden = closeForm?.querySelector('input[name="supervisor_password"]');
            if (oldHidden) {
                oldHidden.value = differenceApprovalPassword.value;
            } else if (closeForm) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'supervisor_password';
                hidden.value = differenceApprovalPassword.value;
                closeForm.appendChild(hidden);
            }

            closeModal(differenceApprovalModal);
            closeForm?.submit();
        });

        document.querySelectorAll('[data-close-difference-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(differenceApprovalModal);
            });
        });

        document.querySelectorAll('.js-open-void-modal').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!voidForm || !voidRouteTemplate) return;

                const movementId = button.getAttribute('data-movement-id') || '';
                const movementLabel = button.getAttribute('data-movement-label') || '-';
                voidForm.action = voidRouteTemplate.replace('__MOVEMENT__', movementId);
                if (voidLabel) voidLabel.textContent = movementLabel;

                const reason = document.getElementById('void-reason');
                if (reason) reason.value = '';

                openModal(voidModal);
            });
        });

        document.querySelectorAll('[data-close-void-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(voidModal);
            });
        });

    })();
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/cash/sessions.blade.php ENDPATH**/ ?>