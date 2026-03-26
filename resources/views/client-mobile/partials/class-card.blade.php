@php
    $showDescription = isset($showDescription) ? (bool) $showDescription : true;
    $classModalId = isset($classModalId) ? trim((string) $classModalId) : '';
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

    $priceAmount = (float) ($classItem->price ?? 0);
    $priceLabel = $classItem instanceof \App\Models\GymClass
        ? $classItem->formattedPrice()
        : ($priceAmount <= 0 ? 'Gratis' : '$'.number_format($priceAmount, 2, '.', ','));
    $priceClasses = $priceAmount <= 0
        ? 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100'
        : 'border-amber-300/45 bg-amber-400/15 text-amber-100';

    $classMeta = collect([
        trim((string) ($classItem->category ?? '')),
        trim((string) ($classItem->level ?? '')),
        trim((string) ($classItem->room_name ?? '')),
    ])->filter()->implode(' - ');
@endphp

<article class="rounded-3xl border border-white/10 bg-slate-950/40 p-4 space-y-3">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-base font-black text-white">{{ (string) ($classItem->name ?? 'Clase') }}</h3>
                <span class="rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-[.14em] {{ $statusClasses }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <p class="mt-1 text-xs text-slate-300">
                {{ $formatClassRange($classItem->starts_at ?? null, $classItem->ends_at ?? null) }}
            </p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-[10px] font-black uppercase tracking-[.14em] {{ $priceClasses }}">
                {{ $priceLabel }}
            </span>
            <div class="rounded-2xl border border-cyan-400/30 bg-cyan-500/10 px-3 py-2 text-right">
                <p class="text-[10px] font-black uppercase tracking-[.14em] text-cyan-100">Cupos</p>
                <p class="text-base font-black text-white">{{ $reservedCount }}/{{ $capacity }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300">
        <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Instructor</p>
            <p class="mt-1 font-semibold text-white">{{ trim((string) ($classItem->instructor_name ?? 'Por confirmar')) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[10px] font-black uppercase tracking-[.14em] text-slate-400">Detalle</p>
            <p class="mt-1 font-semibold text-white">{{ $classMeta !== '' ? $classMeta : 'Sin detalle extra' }}</p>
        </div>
    </div>

    @if ($showDescription && trim((string) ($classItem->description ?? '')) !== '')
        <p class="text-xs leading-5 text-slate-300">{{ trim((string) $classItem->description) }}</p>
    @endif

    @if ($spotsLeft > 0)
        <p class="text-[11px] text-emerald-100">Quedan {{ $spotsLeft }} cupos disponibles.</p>
    @elseif ($waitlistCount > 0)
        <p class="text-[11px] text-amber-100">Hay {{ $waitlistCount }} personas esperando un cupo.</p>
    @else
        <p class="text-[11px] text-slate-300">No quedan cupos disponibles por ahora.</p>
    @endif

    <div class="flex flex-wrap gap-2">
        @if ($reservationStatus === \App\Models\GymClassReservation::STATUS_RESERVED || $reservationStatus === \App\Models\GymClassReservation::STATUS_WAITLIST)
            <form method="POST" action="{{ route('client-mobile.classes.cancel', ['gymSlug' => $gym->slug, 'gymClass' => $classItem->id]) }}">
                @csrf
                @if ($classModalId !== '')
                    <input type="hidden" name="classes_modal_id" value="{{ $classModalId }}">
                @endif
                <button type="submit" class="module-action module-action-secondary">
                    {{ $reservationStatus === \App\Models\GymClassReservation::STATUS_RESERVED ? 'Cancelar reserva' : 'Salir de espera' }}
                </button>
            </form>
        @elseif ($spotsLeft > 0)
            <form method="POST" action="{{ route('client-mobile.classes.reserve', ['gymSlug' => $gym->slug, 'gymClass' => $classItem->id]) }}">
                @csrf
                @if ($classModalId !== '')
                    <input type="hidden" name="classes_modal_id" value="{{ $classModalId }}">
                @endif
                <button type="submit" class="module-action module-action-primary">Reservar cupo</button>
            </form>
        @elseif ((bool) ($classItem->allow_waitlist ?? false))
            <form method="POST" action="{{ route('client-mobile.classes.reserve', ['gymSlug' => $gym->slug, 'gymClass' => $classItem->id]) }}">
                @csrf
                @if ($classModalId !== '')
                    <input type="hidden" name="classes_modal_id" value="{{ $classModalId }}">
                @endif
                <button type="submit" class="module-action module-action-primary">Entrar en espera</button>
            </form>
        @else
            <span class="inline-flex items-center rounded-full border border-rose-400/30 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-100">
                Clase llena
            </span>
        @endif
    </div>
</article>
