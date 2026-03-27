<?php if (! $__env->hasRenderedOnce('dcba09b8-4508-4a00-b783-6917b3c17ce1')): $__env->markAsRenderedOnce('dcba09b8-4508-4a00-b783-6917b3c17ce1'); ?>
    <style>
        body.gym-classes-modal-open {
            overflow: hidden;
        }

        .gym-classes-scroll {
            display: grid;
            gap: 0.85rem;
        }

        .gym-classes-scroll.is-scrollable {
            max-height: 27rem;
            overflow-y: auto;
            padding-right: 0.2rem;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: y proximity;
        }

        .gym-classes-scroll.is-scrollable.gym-classes-reservations-scroll {
            max-height: min(42vh, 22rem);
            padding-right: 0.35rem;
        }

        .gym-classes-scroll.is-scrollable > * {
            scroll-snap-align: start;
        }

        .gym-classes-scroll.is-scrollable::-webkit-scrollbar {
            width: 6px;
        }

        .gym-classes-scroll.is-scrollable::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(34, 211, 238, 0.35);
        }

        .gym-classes-scroll.is-scrollable.gym-classes-reservations-scroll::-webkit-scrollbar-thumb {
            background: rgba(34, 211, 238, 0.58);
        }

        .gym-classes-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .gym-classes-kpi-card {
            min-height: 4.9rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.04);
            padding: 0.55rem 0.35rem;
            text-align: center;
        }

        .gym-classes-kpi-label {
            font-size: 0.58rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(148, 163, 184, 0.9);
        }

        .gym-classes-kpi-value {
            font-size: 1.4rem;
            line-height: 1;
            font-weight: 900;
            color: #fff;
        }

        .gym-classes-calendar-card {
            margin-top: 0.65rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 1.35rem;
            background: rgba(15, 23, 42, 0.56);
            padding: 0.8rem;
        }

        .gym-classes-weekdays {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.28rem;
            margin-top: 0.72rem;
        }

        .gym-classes-weekday {
            text-align: center;
            font-size: 0.58rem;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(148, 163, 184, 0.92);
        }

        .gym-classes-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.28rem;
            margin-top: 0.28rem;
        }

        .gym-classes-day {
            min-height: 0;
            aspect-ratio: 1 / 1.02;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.16rem;
            border-radius: 0.88rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(2, 6, 23, 0.5);
            padding: 0.32rem;
            color: #e2e8f0;
        }

        button.gym-classes-day {
            cursor: pointer;
            transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }

        button.gym-classes-day:active {
            transform: scale(0.98);
        }

        .gym-classes-day.is-outside {
            opacity: 0.42;
        }

        .gym-classes-day.is-today {
            border-color: rgba(45, 212, 191, 0.72);
            box-shadow: inset 0 0 0 1px rgba(45, 212, 191, 0.24);
        }

        .gym-classes-day.has-classes {
            border-color: rgba(34, 211, 238, 0.42);
            background: linear-gradient(180deg, rgba(8, 47, 73, 0.74), rgba(2, 6, 23, 0.78));
        }

        .gym-classes-day.has-classes.is-today {
            border-color: rgba(52, 211, 153, 0.92);
            background: linear-gradient(180deg, rgba(6, 78, 59, 0.82), rgba(8, 47, 73, 0.82));
        }

        .gym-classes-day-number {
            font-size: 0.9rem;
            font-weight: 900;
            line-height: 1;
            color: #f8fafc;
        }

        .gym-classes-day-copy {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .gym-classes-day-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #14b8a6);
            color: #f8fafc;
            font-size: 0.62rem;
            font-weight: 900;
        }

        .gym-classes-day-note {
            display: none;
            font-size: 0.56rem;
            line-height: 1.1;
            color: rgba(191, 219, 254, 0.92);
        }

        .gym-classes-day-empty {
            display: none;
            font-size: 0.54rem;
            line-height: 1;
            color: rgba(100, 116, 139, 0.95);
        }

        .gym-classes-modal {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .gym-classes-modal.hidden {
            display: none;
        }

        .gym-classes-modal-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(2, 6, 23, 0.76);
        }

        .gym-classes-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(32rem, 100%);
            max-height: 84vh;
            overflow: hidden;
            border-radius: 1.7rem;
            border: 1px solid rgba(34, 211, 238, 0.22);
            background: linear-gradient(180deg, rgba(2, 6, 23, 0.98), rgba(15, 23, 42, 0.96));
            box-shadow: 0 28px 64px rgba(2, 6, 23, 0.62);
        }

        .gym-classes-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1rem 0.75rem;
        }

        .gym-classes-modal-close {
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.94);
            color: #e2e8f0;
            font-size: 0.78rem;
            font-weight: 800;
            padding: 0.5rem 0.8rem;
        }

        .gym-classes-modal-body {
            padding: 0 1rem 1rem;
        }

        .gym-classes-modal-list {
            max-height: min(58vh, 28rem);
            overflow-y: auto;
            padding-right: 0.2rem;
        }

        .gym-classes-modal-list::-webkit-scrollbar {
            width: 6px;
        }

        .gym-classes-modal-list::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(34, 211, 238, 0.35);
        }

        .classes-feedback-modal {
            z-index: 110;
        }

        .gym-classes-feedback-dialog {
            width: min(24rem, 100%);
            border-color: rgba(248, 113, 113, 0.32);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.99), rgba(30, 41, 59, 0.98));
        }

        .gym-classes-feedback-copy {
            display: grid;
            gap: 0.7rem;
        }

        .gym-classes-feedback-badge {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            border-radius: 999px;
            border: 1px solid rgba(248, 113, 113, 0.25);
            background: rgba(248, 113, 113, 0.12);
            padding: 0.35rem 0.7rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #fecaca;
        }

        .gym-classes-feedback-text {
            font-size: 0.96rem;
            font-weight: 800;
            line-height: 1.5;
            color: #fff;
        }

        .gym-classes-feedback-hint {
            font-size: 0.8rem;
            line-height: 1.45;
            color: rgba(226, 232, 240, 0.8);
        }

        @media (min-width: 768px) {
            .gym-classes-scroll.is-scrollable {
                max-height: 30rem;
            }

            .gym-classes-scroll.is-scrollable.gym-classes-reservations-scroll {
                max-height: min(46vh, 24rem);
            }

            .gym-classes-day {
                min-height: 4.55rem;
                aspect-ratio: auto;
                padding: 0.45rem;
            }

            .gym-classes-day-note,
            .gym-classes-day-empty {
                display: block;
            }

            .gym-classes-modal {
                padding: 1.5rem;
            }

            .gym-classes-modal-dialog {
                width: min(36rem, 100%);
            }
        }
    </style>
<?php endif; ?>

<?php
    $parseClassMoment = static function ($value): ?\Carbon\Carbon {
        try {
            if ($value instanceof \DateTimeInterface) {
                return \Carbon\Carbon::instance($value);
            }

            $rawValue = trim((string) $value);
            if ($rawValue !== '') {
                return \Carbon\Carbon::parse($rawValue);
            }
        } catch (\Throwable) {
        }

        return null;
    };

    $formatClassMoment = static function ($value, string $format = 'D d M - H:i') use ($parseClassMoment): string {
        $moment = $parseClassMoment($value);

        return $moment ? $moment->translatedFormat($format) : '-';
    };

    $formatClassRange = static function ($startsAt, $endsAt) use ($parseClassMoment, $formatClassMoment): string {
        $startMoment = $parseClassMoment($startsAt);
        if (! $startMoment) {
            return '-';
        }

        $endMoment = $parseClassMoment($endsAt) ?? $startMoment->copy();
        if ($endMoment->lt($startMoment)) {
            $endMoment = $startMoment->copy();
        }

        if ($startMoment->isSameDay($endMoment)) {
            return $formatClassMoment($startMoment, 'D d M').' | '
                .$formatClassMoment($startMoment, 'H:i')
                .' a '
                .$formatClassMoment($endMoment, 'H:i');
        }

        return $formatClassMoment($startMoment, 'D d M')
            .' al '
            .$formatClassMoment($endMoment, 'D d M')
            .' | '
            .$formatClassMoment($startMoment, 'H:i')
            .' a '
            .$formatClassMoment($endMoment, 'H:i');
    };

    $formatClassWeekdays = static function ($classItem): string {
        return $classItem instanceof \App\Models\GymClass
            ? $classItem->activeWeekdaysLabel(true)
            : 'Todos los dias';
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

    $describePrice = static function ($classItem): array {
        $amount = (float) ($classItem?->price ?? 0);

        return $amount <= 0
            ? ['label' => 'Gratis', 'classes' => 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100']
            : ['label' => '$'.number_format($amount, 2, '.', ','), 'classes' => 'border-amber-300/45 bg-amber-400/15 text-amber-100'];
    };

    $todayGymClasses = collect($todayGymClasses ?? [])->values();
    $upcomingGymClasses = collect($upcomingGymClasses ?? [])->values();
    $myGymClassReservations = collect($myGymClassReservations ?? [])->values();

    $todayClassIds = $todayGymClasses
        ->pluck('id')
        ->map(static fn ($id): int => (int) $id)
        ->all();

    $upcomingOnlyGymClasses = $upcomingGymClasses
        ->reject(static fn ($classItem): bool => in_array((int) ($classItem->id ?? 0), $todayClassIds, true))
        ->values();

    $agendaPreviewClasses = $upcomingOnlyGymClasses->take(10)->values();
    $nextReservationModel = $nextGymClassReservation ?? null;
    $nextReservationClass = $nextReservationModel?->gymClass;

    $calendarNow = \Carbon\Carbon::now(trim((string) ($gym->timezone ?? config('app.timezone', 'UTC'))));
    $calendarMonthLabel = ucfirst($calendarNow->locale('es')->translatedFormat('F Y'));
    $calendarGridStart = $calendarNow->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::MONDAY);
    $calendarGridEnd = $calendarNow->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
    $calendarDays = [];

    for ($cursor = $calendarGridStart->copy(); $cursor->lte($calendarGridEnd); $cursor->addDay()) {
        $calendarDays[] = $cursor->copy();
    }

    $calendarRangeStart = $calendarGridStart->copy()->startOfDay();
    $calendarRangeEnd = $calendarGridEnd->copy()->endOfDay();
    $calendarMapBuffer = [];

    foreach ($upcomingGymClasses as $classItem) {
        $classStartsAt = $parseClassMoment($classItem->starts_at ?? null);
        if (! $classStartsAt) {
            continue;
        }

        $classEndsAt = $parseClassMoment($classItem->ends_at ?? null) ?? $classStartsAt->copy();
        if ($classEndsAt->lt($classStartsAt)) {
            $classEndsAt = $classStartsAt->copy();
        }

        $rangeCursor = $classStartsAt->copy()->startOfDay();
        $rangeLimit = $classEndsAt->copy()->startOfDay();

        if ($rangeLimit->lt($calendarRangeStart) || $rangeCursor->gt($calendarRangeEnd)) {
            continue;
        }

        if ($rangeCursor->lt($calendarRangeStart)) {
            $rangeCursor = $calendarRangeStart->copy();
        }

        if ($rangeLimit->gt($calendarRangeEnd)) {
            $rangeLimit = $calendarRangeEnd->copy()->startOfDay();
        }

        while ($rangeCursor->lte($rangeLimit)) {
            if (! ($classItem instanceof \App\Models\GymClass) || $classItem->occursOnDate($rangeCursor)) {
                $calendarMapBuffer[$rangeCursor->toDateString()][] = $classItem;
            }
            $rangeCursor->addDay();
        }
    }

    $calendarClassMap = collect($calendarMapBuffer)
        ->map(static fn ($dayClasses) => collect($dayClasses)->sortBy(static fn ($classItem) => $classItem instanceof \App\Models\GymClass ? $classItem->dailyStartSortKey() : PHP_INT_MAX)->values())
        ->sortKeys();

    $daysWithClassesCount = $calendarClassMap->count();
    $classesFeedbackMessage = trim((string) $errors->first('classes'));
    $classesFeedbackModalId = trim((string) session('classes_modal_id', ''));
    $classesFeedbackTitle = str_contains(mb_strtolower($classesFeedbackMessage), 'cancel')
        ? 'No se pudo completar la accion'
        : 'No se puede reservar';
    $classesFeedbackHint = str_contains(mb_strtolower($classesFeedbackMessage), 'cancel')
        ? 'Vuelve a intentarlo en unos segundos.'
        : 'Escoge otro horario o cancela la reserva que ya tienes activa.';
?>

<section id="classes-view" class="space-y-4">
    <article class="glass-card rounded-3xl p-4 space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[.16em] text-cyan-100">Clases</p>
                <h2 class="mt-1 text-lg font-black text-white">Agenda del gimnasio</h2>
                <p class="mt-1 text-xs text-slate-300">Reserva cupos, revisa horarios y toca un dia del calendario para ver todo lo programado.</p>
            </div>
            <a href="<?php echo e(route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home'])); ?>" class="module-action module-action-secondary">Inicio</a>
        </div>

        <div class="gym-classes-kpi-grid">
            <div class="gym-classes-kpi-card">
                <p class="gym-classes-kpi-label">Hoy</p>
                <p class="gym-classes-kpi-value"><?php echo e($todayGymClasses->count()); ?></p>
            </div>
            <div class="gym-classes-kpi-card">
                <p class="gym-classes-kpi-label">Agenda</p>
                <p class="gym-classes-kpi-value"><?php echo e($upcomingGymClasses->count()); ?></p>
            </div>
            <div class="gym-classes-kpi-card">
                <p class="gym-classes-kpi-label">Reservas</p>
                <p class="gym-classes-kpi-value"><?php echo e($myGymClassReservations->count()); ?></p>
            </div>
        </div>

        <div class="gym-classes-calendar-card">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.14em] text-emerald-100">Calendario interactivo</p>
                    <h3 class="mt-1 text-base font-black text-white"><?php echo e($calendarMonthLabel); ?></h3>
                    <p class="mt-1 text-xs text-slate-300">Toca un dia con color para abrir un modal con todas sus clases.</p>
                </div>
                <span class="rounded-full border border-emerald-400/25 bg-emerald-500/10 px-3 py-1.5 text-[11px] font-semibold text-emerald-100">
                    <?php echo e($daysWithClassesCount); ?> dias activos
                </span>
            </div>

            <div class="gym-classes-weekdays">
                <?php $__currentLoopData = ['L', 'M', 'X', 'J', 'V', 'S', 'D']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="gym-classes-weekday"><?php echo e($weekLabel); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="gym-classes-calendar-grid">
                <?php $__currentLoopData = $calendarDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calendarDay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $dayKey = $calendarDay->toDateString();
                        $dayClasses = collect($calendarClassMap->get($dayKey, collect()));
                        $isCurrentMonth = $calendarDay->month === $calendarNow->month;
                        $isToday = $calendarDay->isSameDay($calendarNow);
                        $hasClasses = $dayClasses->isNotEmpty();
                        $modalId = 'classes-day-'.$dayKey;
                    ?>

                    <?php if($hasClasses): ?>
                        <button
                            type="button"
                            class="gym-classes-day has-classes <?php echo e($isToday ? 'is-today' : ''); ?> <?php echo e($isCurrentMonth ? '' : 'is-outside'); ?>"
                            data-classes-modal-open="<?php echo e($modalId); ?>"
                        >
                            <span class="gym-classes-day-number"><?php echo e($calendarDay->day); ?></span>
                            <span class="gym-classes-day-copy">
                                <span class="gym-classes-day-badge"><?php echo e($dayClasses->count()); ?></span>
                                <span class="gym-classes-day-note"><?php echo e($dayClasses->count() === 1 ? '1 clase' : $dayClasses->count().' clases'); ?></span>
                            </span>
                        </button>
                    <?php else: ?>
                        <div class="gym-classes-day <?php echo e($isToday ? 'is-today' : ''); ?> <?php echo e($isCurrentMonth ? '' : 'is-outside'); ?>">
                            <span class="gym-classes-day-number"><?php echo e($calendarDay->day); ?></span>
                            <span class="gym-classes-day-empty">Sin clases</span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </article>

    <?php if($nextReservationModel && $nextReservationClass): ?>
        <?php
            $nextReservationMeta = $describeReservation($nextReservationModel->status ?? null);
            $nextReservationPriceMeta = $describePrice($nextReservationClass);
        ?>
        <article class="glass-card rounded-3xl p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.14em] text-emerald-100">Tu siguiente clase</p>
                    <h3 class="mt-1 text-base font-black text-white"><?php echo e((string) ($nextReservationClass->name ?? 'Clase reservada')); ?></h3>
                    <p class="mt-1 text-xs text-slate-300">
                        <?php echo e($formatClassRange($nextReservationClass->starts_at ?? null, $nextReservationClass->ends_at ?? null)); ?>

                    </p>
                    <p class="text-[11px] text-slate-400">Dias: <?php echo e($formatClassWeekdays($nextReservationClass)); ?></p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] <?php echo e($nextReservationMeta['classes']); ?>">
                        <?php echo e($nextReservationMeta['label']); ?>

                    </span>
                    <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] <?php echo e($nextReservationPriceMeta['classes']); ?>">
                        <?php echo e($nextReservationPriceMeta['label']); ?>

                    </span>
                </div>
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

    <?php if($agendaPreviewClasses->isNotEmpty()): ?>
        <article class="glass-card rounded-3xl p-4 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.14em] text-fuchsia-100">Agenda</p>
                    <h3 class="mt-1 text-base font-black text-white">Proximas clases</h3>
                </div>
                <span class="text-xs font-semibold text-slate-300"><?php echo e($upcomingOnlyGymClasses->count()); ?> disponibles</span>
            </div>

            <div class="gym-classes-scroll <?php echo e($agendaPreviewClasses->count() > 2 ? 'is-scrollable' : ''); ?>">
                <?php $__currentLoopData = $agendaPreviewClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('client-mobile.partials.class-card', ['gym' => $gym, 'classItem' => $classItem, 'showDescription' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </article>
    <?php endif; ?>

    <article class="glass-card rounded-3xl p-4 space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-black uppercase tracking-[.14em] text-amber-100">Mi agenda</p>
                <h3 class="mt-1 text-base font-black text-white">Mis reservas</h3>
            </div>
            <span class="text-xs font-semibold text-slate-300"><?php echo e($myGymClassReservations->count()); ?> activas</span>
        </div>

        <?php if($myGymClassReservations->isNotEmpty()): ?>
            <div class="gym-classes-scroll <?php echo e($myGymClassReservations->count() > 1 ? 'is-scrollable gym-classes-reservations-scroll' : ''); ?>">
                <?php $__currentLoopData = $myGymClassReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $reservationClass = $reservation->gymClass;
                        $reservationMeta = $describeReservation($reservation->status ?? null);
                        $reservationPriceMeta = $describePrice($reservationClass);
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
                                    <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] <?php echo e($reservationPriceMeta['classes']); ?>">
                                        <?php echo e($reservationPriceMeta['label']); ?>

                                    </span>
                                </div>
                                <?php if($reservationClass): ?>
                                    <p class="mt-1 text-xs text-slate-300">
                                        <?php echo e($formatClassRange($reservationClass->starts_at ?? null, $reservationClass->ends_at ?? null)); ?>

                                    </p>
                                    <p class="text-[11px] text-slate-400">Dias: <?php echo e($formatClassWeekdays($reservationClass)); ?></p>
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="rounded-3xl border border-dashed border-white/15 bg-white/5 px-4 py-6 text-center">
                <p class="text-sm font-semibold text-white">Aun no tienes reservas activas.</p>
                <p class="mt-1 text-xs text-slate-300">Reserva una clase y aparecera aqui con su estado.</p>
            </div>
        <?php endif; ?>
    </article>

    <?php $__currentLoopData = $calendarClassMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayKey => $dayClasses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $modalId = 'classes-day-'.$dayKey;
            $modalDate = \Carbon\Carbon::parse($dayKey)->locale('es')->translatedFormat('l d \d\e F');
            $modalClasses = collect($dayClasses)->values();
        ?>

        <div id="<?php echo e($modalId); ?>" class="gym-classes-modal classes-day-modal hidden" aria-hidden="true">
            <button type="button" class="gym-classes-modal-backdrop" data-classes-modal-close aria-label="Cerrar clases del dia"></button>
            <div class="gym-classes-modal-dialog">
                <div class="gym-classes-modal-head">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.14em] text-cyan-100">Clases del dia</p>
                        <h3 class="mt-1 text-lg font-black text-white"><?php echo e(ucfirst($modalDate)); ?></h3>
                        <p class="mt-1 text-xs text-slate-300"><?php echo e($modalClasses->count() === 1 ? '1 clase programada' : $modalClasses->count().' clases programadas'); ?></p>
                    </div>
                    <button type="button" class="gym-classes-modal-close" data-classes-modal-close>Cerrar</button>
                </div>
                <div class="gym-classes-modal-body">
                    <div class="gym-classes-modal-list gym-classes-scroll is-scrollable">
                        <?php $__currentLoopData = $modalClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('client-mobile.partials.class-card', ['gym' => $gym, 'classItem' => $classItem, 'classModalId' => $modalId], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($classesFeedbackMessage !== ''): ?>
        <div id="classes-feedback-modal" class="gym-classes-modal classes-feedback-modal hidden" aria-hidden="true">
            <button type="button" class="gym-classes-modal-backdrop" data-classes-modal-close aria-label="Cerrar aviso de reserva"></button>
            <div class="gym-classes-modal-dialog gym-classes-feedback-dialog">
                <div class="gym-classes-modal-head">
                    <div class="gym-classes-feedback-copy">
                        <span class="gym-classes-feedback-badge">Aviso</span>
                        <div>
                            <h3 class="text-lg font-black text-white"><?php echo e($classesFeedbackTitle); ?></h3>
                            <p class="mt-2 gym-classes-feedback-text"><?php echo e($classesFeedbackMessage); ?></p>
                            <p class="mt-2 gym-classes-feedback-hint"><?php echo e($classesFeedbackHint); ?></p>
                        </div>
                    </div>
                    <button type="button" class="gym-classes-modal-close" data-classes-modal-close>Cerrar</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php if (! $__env->hasRenderedOnce('33f9e1b9-cd6a-4da4-a5ad-cd41a60aff61')): $__env->markAsRenderedOnce('33f9e1b9-cd6a-4da4-a5ad-cd41a60aff61'); ?>
    <script>
        (function () {
            const syncBodyScrollState = () => {
                const hasOpenModal = document.querySelector('.classes-day-modal:not(.hidden), .classes-feedback-modal:not(.hidden)');
                document.body.classList.toggle('gym-classes-modal-open', Boolean(hasOpenModal));
            };

            const closeModal = (modal) => {
                if (!modal) {
                    return;
                }

                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                syncBodyScrollState();
            };

            const openDayModal = (modalId) => {
                const modal = document.getElementById(modalId);
                if (!modal) {
                    return;
                }

                document.querySelectorAll('.classes-day-modal').forEach((item) => {
                    item.classList.add('hidden');
                    item.setAttribute('aria-hidden', 'true');
                });

                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                syncBodyScrollState();
            };

            const openFeedbackModal = () => {
                const modal = document.getElementById('classes-feedback-modal');
                if (!modal) {
                    return;
                }

                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                syncBodyScrollState();
            };

            document.addEventListener('click', (event) => {
                const openTrigger = event.target.closest('[data-classes-modal-open]');
                if (openTrigger) {
                    event.preventDefault();
                    openDayModal(openTrigger.getAttribute('data-classes-modal-open'));
                    return;
                }

                const closeTrigger = event.target.closest('[data-classes-modal-close]');
                if (closeTrigger) {
                    event.preventDefault();
                    closeModal(closeTrigger.closest('.classes-feedback-modal, .classes-day-modal'));
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') {
                    return;
                }

                document.querySelectorAll('.classes-feedback-modal:not(.hidden)').forEach((modal) => {
                    closeModal(modal);
                });

                document.querySelectorAll('.classes-day-modal:not(.hidden)').forEach((modal) => {
                    closeModal(modal);
                });
            });

            const restoreModalId = <?php echo json_encode($classesFeedbackModalId !== '' ? $classesFeedbackModalId : null, 15, 512) ?>;
            const shouldOpenFeedbackModal = <?php echo json_encode($classesFeedbackMessage !== '', 15, 512) ?>;

            if (restoreModalId) {
                openDayModal(restoreModalId);
            }

            if (shouldOpenFeedbackModal) {
                openFeedbackModal();
            }
        })();
    </script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/client-mobile/partials/classes-screen.blade.php ENDPATH**/ ?>