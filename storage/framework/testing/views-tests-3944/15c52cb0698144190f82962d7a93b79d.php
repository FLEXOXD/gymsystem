<?php
    $showDescription = isset($showDescription) ? (bool) $showDescription : true;
    $classModalId = isset($classModalId) ? trim((string) $classModalId) : '';
    $formatClassMoment = static function ($value, string $format = 'D d M - H:i'): string {
        try {
            if ($value instanceof \DateTimeInterface) {
                return \Carbon\Carbon::instance($value)->translatedFormat($format);
            }

            $rawValue = trim((string) $value);
            if ($rawValue !== '') {
                return \Carbon\Carbon::parse($rawValue)->translatedFormat($format);
            }
        } catch (\Throwable) {
        }

        return '-';
    };
    $clientReservation = collect($classItem->reservations ?? [])->first();
    $reservationStatus = trim((string) ($clientReservation->status ?? ''));
    $reservedCount = max(0, (int) ($classItem->reserved_count ?? 0));
    $waitlistCount = max(0, (int) ($classItem->waitlist_count ?? 0));
    $capacity = max(1, (int) ($classItem->capacity ?? 1));
    $spotsLeft = max(0, $capacity - $reservedCount);
    $statusLabel = 'Disponible';
    $statusClasses = 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100';

    if ($reservationStatus === \App\Models\GymClassReservation::STATUS_RESERVED) {
        $statusLabel = 'Reservada';
        $statusClasses = 'border-cyan-400/40 bg-cyan-500/15 text-cyan-100';
    } elseif ($reservationStatus === \App\Models\GymClassReservation::STATUS_WAITLIST) {
        $statusLabel = 'En espera';
        $statusClasses = 'border-amber-400/40 bg-amber-500/15 text-amber-100';
    } elseif ($spotsLeft <= 0 && (bool) ($classItem->allow_waitlist ?? false)) {
        $statusLabel = 'Lista de espera';
        $statusClasses = 'border-amber-400/40 bg-amber-500/15 text-amber-100';
    } elseif ($spotsLeft <= 0) {
        $statusLabel = 'Llena';
        $statusClasses = 'border-rose-400/40 bg-rose-500/15 text-rose-100';
    } elseif ($spotsLeft <= 2) {
        $statusLabel = 'Ultimos cupos';
        $statusClasses = 'border-fuchsia-400/40 bg-fuchsia-500/15 text-fuchsia-100';
    }

    $classMeta = collect([
        trim((string) ($classItem->category ?? '')),
        trim((string) ($classItem->level ?? '')),
        trim((string) ($classItem->room_name ?? '')),
    ])->filter()->implode(' - ');
?>

<article class="rounded-3xl border border-white/10 bg-slate-950/40 p-4 space-y-3">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-base font-black text-white"><?php echo e((string) ($classItem->name ?? 'Clase')); ?></h3>
                <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] <?php echo e($statusClasses); ?>">
                    <?php echo e($statusLabel); ?>

                </span>
            </div>
            <p class="mt-1 text-xs text-slate-300">
                <?php echo e($formatClassMoment($classItem->starts_at ?? null, 'D d M')); ?>

                -
                <?php echo e($formatClassMoment($classItem->starts_at ?? null, 'H:i')); ?>

                a
                <?php echo e($formatClassMoment($classItem->ends_at ?? null, 'H:i')); ?>

            </p>
        </div>
        <div class="rounded-2xl border border-cyan-400/30 bg-cyan-500/10 px-3 py-2 text-right">
            <p class="text-[10px] font-black uppercase tracking-[.14em] text-cyan-100">Cupos</p>
            <p class="text-base font-black text-white"><?php echo e($reservedCount); ?>/<?php echo e($capacity); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300">
        <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Instructor</p>
            <p class="mt-1 font-semibold text-white"><?php echo e(trim((string) ($classItem->instructor_name ?? 'Por confirmar'))); ?></p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Detalle</p>
            <p class="mt-1 font-semibold text-white"><?php echo e($classMeta !== '' ? $classMeta : 'Sin detalle extra'); ?></p>
        </div>
    </div>

    <?php if($showDescription && trim((string) ($classItem->description ?? '')) !== ''): ?>
        <p class="text-xs leading-5 text-slate-300"><?php echo e(trim((string) $classItem->description)); ?></p>
    <?php endif; ?>

    <?php if($spotsLeft > 0): ?>
        <p class="text-[11px] text-emerald-100">Quedan <?php echo e($spotsLeft); ?> cupos disponibles.</p>
    <?php elseif($waitlistCount > 0): ?>
        <p class="text-[11px] text-amber-100">Hay <?php echo e($waitlistCount); ?> personas esperando un cupo.</p>
    <?php else: ?>
        <p class="text-[11px] text-slate-300">No quedan cupos disponibles por ahora.</p>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2">
        <?php if($reservationStatus === \App\Models\GymClassReservation::STATUS_RESERVED || $reservationStatus === \App\Models\GymClassReservation::STATUS_WAITLIST): ?>
            <form method="POST" action="<?php echo e(route('client-mobile.classes.cancel', ['gymSlug' => $gym->slug, 'gymClass' => $classItem->id])); ?>">
                <?php echo csrf_field(); ?>
                <?php if($classModalId !== ''): ?>
                    <input type="hidden" name="classes_modal_id" value="<?php echo e($classModalId); ?>">
                <?php endif; ?>
                <button type="submit" class="module-action module-action-secondary">
                    <?php echo e($reservationStatus === \App\Models\GymClassReservation::STATUS_RESERVED ? 'Cancelar reserva' : 'Salir de espera'); ?>

                </button>
            </form>
        <?php elseif($spotsLeft > 0): ?>
            <form method="POST" action="<?php echo e(route('client-mobile.classes.reserve', ['gymSlug' => $gym->slug, 'gymClass' => $classItem->id])); ?>">
                <?php echo csrf_field(); ?>
                <?php if($classModalId !== ''): ?>
                    <input type="hidden" name="classes_modal_id" value="<?php echo e($classModalId); ?>">
                <?php endif; ?>
                <button type="submit" class="module-action module-action-primary">Reservar cupo</button>
            </form>
        <?php elseif((bool) ($classItem->allow_waitlist ?? false)): ?>
            <form method="POST" action="<?php echo e(route('client-mobile.classes.reserve', ['gymSlug' => $gym->slug, 'gymClass' => $classItem->id])); ?>">
                <?php echo csrf_field(); ?>
                <?php if($classModalId !== ''): ?>
                    <input type="hidden" name="classes_modal_id" value="<?php echo e($classModalId); ?>">
                <?php endif; ?>
                <button type="submit" class="module-action module-action-primary">Entrar en espera</button>
            </form>
        <?php else: ?>
            <span class="inline-flex items-center rounded-full border border-rose-400/30 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-100">
                Clase llena
            </span>
        <?php endif; ?>
    </div>
</article>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/client-mobile/partials/class-card.blade.php ENDPATH**/ ?>