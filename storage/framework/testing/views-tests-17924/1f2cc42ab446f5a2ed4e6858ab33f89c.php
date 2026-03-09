<?php
    $today = now()->startOfDay();

    $resolveMembershipMeta = static function ($membership) use ($today, $statusLabels): array {
        $startsAt = $membership->starts_at?->copy()->startOfDay();
        $endsAt = $membership->ends_at?->copy()->startOfDay();
        $isCancelled = (string) ($membership->status ?? '') === 'cancelled';
        $isScheduled = $startsAt !== null && $startsAt->greaterThan($today) && ! $isCancelled;
        $isExpired = $isCancelled || $endsAt === null || $endsAt->lt($today);
        $daysToStart = $startsAt ? $today->diffInDays($startsAt, false) : null;
        $daysToEnd = $endsAt ? $today->diffInDays($endsAt, false) : null;

        $state = match (true) {
            $isCancelled => 'cancelled',
            $isScheduled => 'scheduled',
            $isExpired => 'expired',
            default => 'active',
        };

        $variant = match ($state) {
            'active' => 'success',
            'scheduled' => 'info',
            'cancelled' => 'warning',
            default => 'danger',
        };

        $countdown = match ($state) {
            'scheduled' => $daysToStart === null
                ? 'Pendiente'
                : ($daysToStart === 0 ? 'Inicia hoy' : 'Empieza en '.$daysToStart.' días'),
            'active' => $daysToEnd === null
                ? 'Sin fecha'
                : ($daysToEnd === 0 ? 'Vence hoy' : 'Restan '.$daysToEnd.' días'),
            'cancelled' => 'Sin acceso',
            default => $daysToEnd === null
                ? 'Sin fecha'
                : (abs($daysToEnd) === 0 ? 'Venció hoy' : 'Venció hace '.abs($daysToEnd).' días'),
        };

        return [
            'state' => $state,
            'variant' => $variant,
            'label' => $statusLabels[$state] ?? ucfirst($state),
            'starts_at_label' => $startsAt?->translatedFormat('d M Y') ?? '-',
            'ends_at_label' => $endsAt?->translatedFormat('d M Y') ?? '-',
            'window_label' => ($startsAt?->translatedFormat('d M Y') ?? '-').' -> '.($endsAt?->translatedFormat('d M Y') ?? '-'),
            'countdown' => $countdown,
        ];
    };

    $resolveAdjustmentMeta = static function ($adjustment) use ($today): array {
        $startsAt = $adjustment->new_starts_at?->copy()->startOfDay();
        $endsAt = $adjustment->new_ends_at?->copy()->startOfDay();
        $state = match (true) {
            $startsAt !== null && $startsAt->greaterThan($today) => 'Programada',
            $endsAt === null || $endsAt->lt($today) => 'Vencida',
            default => 'Vigente',
        };

        $delta = (int) ($adjustment->days_delta ?? 0);
        $deltaVariant = match (true) {
            $delta > 0 => 'success',
            $delta < 0 => 'warning',
            default => 'muted',
        };

        $deltaLabel = match (true) {
            $delta > 0 => '+'.$delta.' días',
            $delta < 0 => $delta.' días',
            default => 'Sin cambio neto',
        };

        return [
            'state' => $state,
            'delta_variant' => $deltaVariant,
            'delta_label' => $deltaLabel,
            'previous_window' => ($adjustment->previous_starts_at?->translatedFormat('d M Y') ?? '-').' -> '.($adjustment->previous_ends_at?->translatedFormat('d M Y') ?? '-'),
            'new_window' => ($adjustment->new_starts_at?->translatedFormat('d M Y') ?? '-').' -> '.($adjustment->new_ends_at?->translatedFormat('d M Y') ?? '-'),
        ];
    };

    $latestMembershipMeta = $latestMembership ? $resolveMembershipMeta($latestMembership) : null;
?>

<div class="space-y-6">
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Membresía actual','subtitle' => 'Cobro normal y ajustes sobre el detalle del cliente.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Membresía actual','subtitle' => 'Cobro normal y ajustes sobre el detalle del cliente.']); ?>
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="space-y-1">
                <p class="text-sm text-slate-600 dark:text-slate-400">Si el pago se recibió antes, registra la fecha real de cobro en el modal.</p>
                <p class="text-xs text-slate-500 dark:text-slate-500">Los ajustes quedan en historial separado para no mezclar la venta original con correcciones posteriores.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'success','size' => 'sm','xOn:click' => 'openMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'success','size' => 'sm','x-on:click' => 'openMembershipModal()']); ?>Cobrar / Renovar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if(! empty($canAdjustMemberships) && $latestMembership): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','size' => 'sm','xOn:click' => 'openMembershipAdjustmentModal('.e((int) $latestMembership->id).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','size' => 'sm','x-on:click' => 'openMembershipAdjustmentModal('.e((int) $latestMembership->id).')']); ?>
                        Ajustar membresía
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

        <?php if($latestMembership && $latestMembershipMeta): ?>
            <div class="mt-5 grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Plan</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e($latestMembership->plan?->name ?? 'Sin plan'); ?></p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Membresía #<?php echo e($latestMembership->id); ?></p>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Estado efectivo</p>
                    <div class="mt-2 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $latestMembershipMeta['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($latestMembershipMeta['variant'])]); ?><?php echo e($latestMembershipMeta['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        <span class="text-sm text-slate-700 dark:text-slate-300"><?php echo e($latestMembershipMeta['countdown']); ?></span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Ventana: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($latestMembershipMeta['window_label']); ?></span></p>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Precio registrado</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e(\App\Support\Currency::format((float) ($latestMembership->price ?? 0), $appCurrencyCode)); ?></p>
                    <?php if($latestMembership->promotion_name): ?>
                        <p class="mt-1 text-sm text-cyan-700 dark:text-cyan-300">Promo: <?php echo e($latestMembership->promotion_name); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="mt-5 rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                </div>
                <p class="font-semibold">Este cliente aún no tiene membresías.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Haz clic en "Cobrar / Renovar" para crear la primera membresía desde este detalle.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial de membresías','subtitle' => 'Ventanas de acceso y estado visible para operación.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de membresías','subtitle' => 'Ventanas de acceso y estado visible para operación.']); ?>
        <?php if($client->memberships->isNotEmpty()): ?>
            <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
                <table class="ui-table min-w-[900px]">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plan</th>
                        <th>Ventana</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Promo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $client->memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $membershipMeta = $resolveMembershipMeta($membership);
                        ?>
                        <tr>
                            <td>#<?php echo e($membership->id); ?></td>
                            <td><?php echo e($membership->plan?->name ?? '-'); ?></td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipMeta['window_label']); ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($membershipMeta['countdown']); ?></p>
                            </td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $membershipMeta['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($membershipMeta['variant'])]); ?><?php echo e($membershipMeta['label']); ?> <?php echo $__env->renderComponent(); ?>
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
                            <td><?php echo e(\App\Support\Currency::format((float) ($membership->price ?? 0), $appCurrencyCode)); ?></td>
                            <td><?php echo e($membership->promotion_name ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <p class="font-semibold">Sin historial de membresías todavía.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">La primera venta aparecerá aquí junto con su ventana original.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial de ajustes','subtitle' => 'Cada corrección deja trazabilidad separada de la venta original.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de ajustes','subtitle' => 'Cada corrección deja trazabilidad separada de la venta original.']); ?>
        <?php if($membershipAdjustments->isNotEmpty()): ?>
            <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
                <table class="ui-table min-w-[1080px]">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Membresía</th>
                        <th>Ajuste</th>
                        <th>Motivo</th>
                        <th>Antes</th>
                        <th>Después</th>
                        <th>Cambio</th>
                        <th>Registrado por</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $membershipAdjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adjustment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $adjustmentMeta = $resolveAdjustmentMeta($adjustment);
                        ?>
                        <tr>
                            <td><?php echo e($adjustment->created_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">#<?php echo e($adjustment->membership_id); ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($adjustment->membership?->plan?->name ?? '-'); ?></p>
                            </td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($adjustmentTypeLabels[$adjustment->type] ?? $adjustment->type); ?></p>
                                <?php if($adjustment->notes): ?>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?php echo e($adjustment->notes); ?></p>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($adjustmentReasonLabels[$adjustment->reason] ?? $adjustment->reason); ?></td>
                            <td><?php echo e($adjustmentMeta['previous_window']); ?></td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($adjustmentMeta['new_window']); ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($adjustmentMeta['state']); ?></p>
                            </td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $adjustmentMeta['delta_variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($adjustmentMeta['delta_variant'])]); ?><?php echo e($adjustmentMeta['delta_label']); ?> <?php echo $__env->renderComponent(); ?>
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
                            <td><?php echo e($adjustment->performedBy?->name ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <p class="font-semibold">No hay ajustes registrados.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cuando se reprograme inicio, se extienda acceso o se corrija una ventana, el historial aparecerá aquí.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Pagos relacionados','subtitle' => 'Movimientos de caja vinculados a membresías.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pagos relacionados','subtitle' => 'Movimientos de caja vinculados a membresías.']); ?>
        <?php if($recentMembershipPayments->isNotEmpty()): ?>
            <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
                <table class="ui-table min-w-[920px]">
                    <thead>
                    <tr>
                        <th>Fecha de pago</th>
                        <th>Membresía</th>
                        <th>Plan</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Registrado por</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $recentMembershipPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($movement->occurred_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td>#<?php echo e($movement->membership_id); ?></td>
                            <td><?php echo e($movement->membership?->plan?->name ?? '-'); ?></td>
                            <td><?php echo e($methodLabels[$movement->method] ?? $movement->method); ?></td>
                            <td class="font-semibold text-emerald-700 dark:text-emerald-300"><?php echo e(\App\Support\Currency::format((float) $movement->amount, $appCurrencyCode)); ?></td>
                            <td><?php echo e($movement->createdBy?->name ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16"/>
                        <path d="M4 12h16"/>
                        <path d="M4 17h10"/>
                    </svg>
                </div>
                <p class="font-semibold">No hay pagos registrados para este cliente.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Los pagos aparecerán aquí luego de cobrar una membresía.</p>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','class' => 'mt-3','xOn:click' => 'openMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','class' => 'mt-3','x-on:click' => 'openMembershipModal()']); ?>Cobrar ahora <?php echo $__env->renderComponent(); ?>
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
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_tab_membership_payments.blade.php ENDPATH**/ ?>