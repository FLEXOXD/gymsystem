<div class="space-y-6">
    <x-ui.card title="Membresías" subtitle="Historial y renovaciones del cliente.">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-600 dark:text-slate-400">Registra un nuevo cobro de membresía desde el modal.</p>
            <x-ui.button type="button" variant="success" size="sm" x-on:click="openMembershipModal()">Cobrar / Renovar</x-ui.button>
        </div>

        @if ($client->memberships->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plan</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($client->memberships as $membership)
                        @php
                            $membershipBadge = match ($membership->status) {
                                'active' => 'success',
                                'expired' => 'danger',
                                default => 'muted',
                            };
                        @endphp
                        <tr>
                            <td>#{{ $membership->id }}</td>
                            <td>{{ $membership->plan?->name ?? '-' }}</td>
                            <td>{{ $membership->starts_at?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td>{{ $membership->ends_at?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td>
                                <x-ui.badge :variant="$membershipBadge">{{ $statusLabels[$membership->status] ?? $membership->status }}</x-ui.badge>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                </div>
                <p class="font-semibold">Este cliente aun no tiene membresías.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Haz clic en "Cobrar / Renovar" para crear su primera membresía.</p>
                <x-ui.button type="button" variant="ghost" size="sm" class="mt-3" x-on:click="openMembershipModal()">Crear membresía</x-ui.button>
            </div>
        @endif
    </x-ui.card>

    <x-ui.card title="Pagos relacionados" subtitle="Movimientos de caja vinculados a membresías.">
        @if ($recentMembershipPayments->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
                <table class="ui-table min-w-[860px]">
                    <thead>
                    <tr>
                        <th>Fecha</th>
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
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Los pagos apareceran aqui luego de cobrar una membresía.</p>
                <x-ui.button type="button" variant="ghost" size="sm" class="mt-3" x-on:click="openMembershipModal()">Cobrar ahora</x-ui.button>
            </div>
        @endif
    </x-ui.card>
</div>
