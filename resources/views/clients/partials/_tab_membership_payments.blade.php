@php
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
@endphp

<div class="space-y-6">
    <x-ui.card title="Membresía actual" subtitle="Cobro normal y ajustes sobre el detalle del cliente.">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="space-y-1">
                <p class="text-sm text-slate-600 dark:text-slate-400">Si el pago se recibió antes, registra la fecha real de cobro en el modal.</p>
                <p class="text-xs text-slate-500 dark:text-slate-500">Los ajustes quedan en historial separado para no mezclar la venta original con correcciones posteriores.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-ui.button type="button" variant="success" size="sm" x-on:click="openMembershipModal()">Cobrar / Renovar</x-ui.button>
                @if (! empty($canAdjustMemberships) && $latestMembership)
                    <x-ui.button type="button" variant="secondary" size="sm" x-on:click="openMembershipAdjustmentModal({{ (int) $latestMembership->id }})">
                        Ajustar membresía
                    </x-ui.button>
                @endif
            </div>
        </div>

        @if ($latestMembership && $latestMembershipMeta)
            <div class="mt-5 grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Plan</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-slate-100">{{ $latestMembership->plan?->name ?? 'Sin plan' }}</p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">Membresía #{{ $latestMembership->id }}</p>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Estado efectivo</p>
                    <div class="mt-2 flex items-center gap-2">
                        <x-ui.badge :variant="$latestMembershipMeta['variant']">{{ $latestMembershipMeta['label'] }}</x-ui.badge>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $latestMembershipMeta['countdown'] }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Ventana: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $latestMembershipMeta['window_label'] }}</span></p>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Precio registrado</p>
                    <p class="mt-2 text-lg font-black text-slate-900 dark:text-slate-100">{{ \App\Support\Currency::format((float) ($latestMembership->price ?? 0), $appCurrencyCode) }}</p>
                    @if ($latestMembership->promotion_name)
                        <p class="mt-1 text-sm text-cyan-700 dark:text-cyan-300">Promo: {{ $latestMembership->promotion_name }}</p>
                    @endif
                </div>
            </div>
        @else
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
        @endif
    </x-ui.card>

    <x-ui.card title="Historial de membresías" subtitle="Ventanas de acceso y estado visible para operación.">
        @if ($client->memberships->isNotEmpty())
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
                    @foreach ($client->memberships as $membership)
                        @php
                            $membershipMeta = $resolveMembershipMeta($membership);
                        @endphp
                        <tr>
                            <td>#{{ $membership->id }}</td>
                            <td>{{ $membership->plan?->name ?? '-' }}</td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $membershipMeta['window_label'] }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $membershipMeta['countdown'] }}</p>
                            </td>
                            <td>
                                <x-ui.badge :variant="$membershipMeta['variant']">{{ $membershipMeta['label'] }}</x-ui.badge>
                            </td>
                            <td>{{ \App\Support\Currency::format((float) ($membership->price ?? 0), $appCurrencyCode) }}</td>
                            <td>{{ $membership->promotion_name ?: '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <p class="font-semibold">Sin historial de membresías todavía.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">La primera venta aparecerá aquí junto con su ventana original.</p>
            </div>
        @endif
    </x-ui.card>

    <x-ui.card title="Historial de ajustes" subtitle="Cada corrección deja trazabilidad separada de la venta original.">
        @if ($membershipAdjustments->isNotEmpty())
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
                    @foreach ($membershipAdjustments as $adjustment)
                        @php
                            $adjustmentMeta = $resolveAdjustmentMeta($adjustment);
                        @endphp
                        <tr>
                            <td>{{ $adjustment->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">#{{ $adjustment->membership_id }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $adjustment->membership?->plan?->name ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $adjustmentTypeLabels[$adjustment->type] ?? $adjustment->type }}</p>
                                @if ($adjustment->notes)
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $adjustment->notes }}</p>
                                @endif
                            </td>
                            <td>{{ $adjustmentReasonLabels[$adjustment->reason] ?? $adjustment->reason }}</td>
                            <td>{{ $adjustmentMeta['previous_window'] }}</td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $adjustmentMeta['new_window'] }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $adjustmentMeta['state'] }}</p>
                            </td>
                            <td>
                                <x-ui.badge :variant="$adjustmentMeta['delta_variant']">{{ $adjustmentMeta['delta_label'] }}</x-ui.badge>
                            </td>
                            <td>{{ $adjustment->performedBy?->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <p class="font-semibold">No hay ajustes registrados.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cuando se mueva el inicio, se sumen dias al final o se corrija una ventana, el historial aparecerá aquí.</p>
            </div>
        @endif
    </x-ui.card>

    <x-ui.card title="Pagos relacionados" subtitle="Movimientos de caja vinculados a membresías.">
        @if ($recentMembershipPayments->isNotEmpty())
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
                    @foreach ($recentMembershipPayments as $movement)
                        <tr>
                            <td>{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>#{{ $movement->membership_id }}</td>
                            <td>{{ $movement->membership?->plan?->name ?? '-' }}</td>
                            <td>{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                            <td class="font-semibold text-emerald-700 dark:text-emerald-300">{{ \App\Support\Currency::format((float) $movement->amount, $appCurrencyCode) }}</td>
                            <td>{{ $movement->createdBy?->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
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
                <x-ui.button type="button" variant="ghost" size="sm" class="mt-3" x-on:click="openMembershipModal()">Cobrar ahora</x-ui.button>
            </div>
        @endif
    </x-ui.card>
</div>
