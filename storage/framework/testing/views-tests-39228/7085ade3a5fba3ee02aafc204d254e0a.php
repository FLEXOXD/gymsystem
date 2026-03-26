<?php
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
    $describeReservation = static function (?string $status): array {
        return match (trim((string) $status)) {
            \App\Models\GymClassReservation::STATUS_RESERVED => [
                'label' => 'Reserva confirmada',
                'classes' => 'border-cyan-400/40 bg-cyan-500/15 text-cyan-100',
            ],
            \App\Models\GymClassReservation::STATUS_WAITLIST => [
                'label' => 'Lista de espera',
                'classes' => 'border-amber-400/40 bg-amber-500/15 text-amber-100',
            ],
            \App\Models\GymClassReservation::STATUS_ATTENDED => [
                'label' => 'Asistida',
                'classes' => 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100',
            ],
            default => [
                'label' => 'Sin estado',
                'classes' => 'border-slate-400/30 bg-slate-500/10 text-slate-200',
            ],
        };
    };
    $todayGymClasses = collect($todayGymClasses ?? []);
    $upcomingGymClasses = collect($upcomingGymClasses ?? []);
    $myGymClassReservations = collect($myGymClassReservations ?? []);
    $todayClassIds = $todayGymClasses
        ->pluck('id')
        ->map(static fn ($id): int => (int) $id)
        ->all();
    $upcomingOnlyGymClasses = $upcomingGymClasses
        ->reject(static fn ($classItem): bool => in_array((int) ($classItem->id ?? 0), $todayClassIds, true))
        ->values();
    $nextReservationModel = $nextGymClassReservation ?? null;
    $nextReservationClass = $nextReservationModel?->gymClass;
?>

<section id="classes-view" class="space-y-4">
    <article class="glass-card rounded-3xl p-4 space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[.16em] text-cyan-100">Clases</p>
                <h2 class="mt-1 text-lg font-black text-white">Agenda del gimnasio</h2>
                <p class="mt-1 text-xs text-slate-300">Reserva cupos, revisa horarios y sigue tus proximas clases desde la app.</p>
            </div>
            <a href="<?php echo e(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home'])); ?>" class="module-action module-action-secondary">Inicio</a>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
                <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Hoy</p>
                <p class="mt-1 text-lg font-black text-white"><?php echo e($todayGymClasses->count()); ?></p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
                <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Agenda</p>
                <p class="mt-1 text-lg font-black text-white"><?php echo e($upcomingGymClasses->count()); ?></p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-3">
                <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Reservas</p>
                <p class="mt-1 text-lg font-black text-white"><?php echo e($myGymClassReservations->count()); ?></p>
            </div>
        </div>
    </article>

    <?php if($nextReservationModel && $nextReservationClass): ?>
        <?php
            $nextReservationMeta = $describeReservation($nextReservationModel->status ?? null);
        ?>
        <article class="glass-card rounded-3xl p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.14em] text-emerald-100">Tu siguiente clase</p>
                    <h3 class="mt-1 text-base font-black text-white"><?php echo e((string) ($nextReservationClass->name ?? 'Clase reservada')); ?></h3>
                    <p class="mt-1 text-xs text-slate-300">
                        <?php echo e($formatClassMoment($nextReservationClass->starts_at ?? null, 'D d M')); ?>

                        -
                        <?php echo e($formatClassMoment($nextReservationClass->starts_at ?? null, 'H:i')); ?>

                        a
                        <?php echo e($formatClassMoment($nextReservationClass->ends_at ?? null, 'H:i')); ?>

                    </p>
                </div>
                <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] <?php echo e($nextReservationMeta['classes']); ?>">
                    <?php echo e($nextReservationMeta['label']); ?>

                </span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300">
                <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                    <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Instructor</p>
                    <p class="mt-1 font-semibold text-white"><?php echo e(trim((string) ($nextReservationClass->instructor_name ?? 'Por confirmar'))); ?></p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                    <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Sala</p>
                    <p class="mt-1 font-semibold text-white"><?php echo e(trim((string) ($nextReservationClass->room_name ?? 'Por confirmar'))); ?></p>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('client-mobile.classes.cancel', ['gymSlug' => $gym->slug, 'gymClass' => $nextReservationClass->id])); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="module-action module-action-secondary">
                    <?php echo e(($nextReservationModel->status ?? null) === \App\Models\GymClassReservation::STATUS_RESERVED ? 'Cancelar reserva' : 'Salir de espera'); ?>

                </button>
            </form>
        </article>
    <?php endif; ?>

    <article class="glass-card rounded-3xl p-4 space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[.14em] text-cyan-100">Hoy</p>
                <h3 class="mt-1 text-base font-black text-white">Clases de hoy</h3>
            </div>
            <span class="text-xs font-semibold text-slate-300"><?php echo e($todayGymClasses->count()); ?> programadas</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $todayGymClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('client-mobile.partials.class-card', ['gym' => $gym, 'classItem' => $classItem], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-3xl border border-dashed border-white/15 bg-white/5 px-4 py-6 text-center">
                <p class="text-sm font-semibold text-white">No hay clases hoy.</p>
                <p class="mt-1 text-xs text-slate-300">Revisa la agenda para reservar una proxima sesion.</p>
            </div>
        <?php endif; ?>
    </article>

    <article class="glass-card rounded-3xl p-4 space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[.14em] text-fuchsia-100">Agenda</p>
                <h3 class="mt-1 text-base font-black text-white">Proximas clases</h3>
            </div>
            <span class="text-xs font-semibold text-slate-300"><?php echo e($upcomingOnlyGymClasses->count()); ?> disponibles</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $upcomingOnlyGymClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('client-mobile.partials.class-card', ['gym' => $gym, 'classItem' => $classItem, 'showDescription' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-3xl border border-dashed border-white/15 bg-white/5 px-4 py-6 text-center">
                <p class="text-sm font-semibold text-white">Aun no hay nuevas clases publicadas.</p>
                <p class="mt-1 text-xs text-slate-300">Cuando tu gimnasio publique mas horarios los veras aqui.</p>
            </div>
        <?php endif; ?>
    </article>

    <article class="glass-card rounded-3xl p-4 space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[.14em] text-amber-100">Mi agenda</p>
                <h3 class="mt-1 text-base font-black text-white">Mis reservas</h3>
            </div>
            <span class="text-xs font-semibold text-slate-300"><?php echo e($myGymClassReservations->count()); ?> activas</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $myGymClassReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $reservationClass = $reservation->gymClass;
                $reservationMeta = $describeReservation($reservation->status ?? null);
            ?>
            <article class="rounded-3xl border border-white/10 bg-slate-950/40 p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-black text-white">
                                <?php echo e($reservationClass ? (string) ($reservationClass->name ?? 'Clase') : 'Clase no disponible'); ?>

                            </h3>
                            <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] <?php echo e($reservationMeta['classes']); ?>">
                                <?php echo e($reservationMeta['label']); ?>

                            </span>
                        </div>
                        <?php if($reservationClass): ?>
                            <p class="mt-1 text-xs text-slate-300">
                                <?php echo e($formatClassMoment($reservationClass->starts_at ?? null, 'D d M')); ?>

                                -
                                <?php echo e($formatClassMoment($reservationClass->starts_at ?? null, 'H:i')); ?>

                                a
                                <?php echo e($formatClassMoment($reservationClass->ends_at ?? null, 'H:i')); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                    <?php if($reservationClass): ?>
                        <form method="POST" action="<?php echo e(route('client-mobile.classes.cancel', ['gymSlug' => $gym->slug, 'gymClass' => $reservationClass->id])); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="module-action module-action-secondary">
                                <?php echo e(($reservation->status ?? null) === \App\Models\GymClassReservation::STATUS_RESERVED ? 'Cancelar' : 'Salir'); ?>

                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if($reservationClass): ?>
                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                            <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Instructor</p>
                            <p class="mt-1 font-semibold text-white"><?php echo e(trim((string) ($reservationClass->instructor_name ?? 'Por confirmar'))); ?></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
                            <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Sala</p>
                            <p class="mt-1 font-semibold text-white"><?php echo e(trim((string) ($reservationClass->room_name ?? 'Por confirmar'))); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-slate-300">La informacion de esta clase ya no esta disponible.</p>
                <?php endif; ?>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-3xl border border-dashed border-white/15 bg-white/5 px-4 py-6 text-center">
                <p class="text-sm font-semibold text-white">Aun no tienes reservas activas.</p>
                <p class="mt-1 text-xs text-slate-300">Reserva una clase y aparecera aqui con su estado.</p>
            </div>
        <?php endif; ?>
    </article>
</section>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/client-mobile/partials/classes-screen.blade.php ENDPATH**/ ?>