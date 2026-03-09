<x-ui.card class="p-6 lg:p-7">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 items-start gap-4 lg:gap-5">
            <div class="h-16 w-16 overflow-hidden rounded-2xl border border-slate-300 bg-slate-100 dark:border-white/10 dark:bg-slate-900/40">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $client->full_name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-lg font-black uppercase text-slate-700 dark:text-slate-200">
                        {{ mb_strtoupper(mb_substr($client->first_name, 0, 1).mb_substr($client->last_name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="min-w-0 space-y-2.5">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="ui-heading truncate text-2xl">{{ $client->full_name }}</h2>
                    <x-ui.badge :variant="$membershipBadgeVariant">{{ $membershipBadgeText }}</x-ui.badge>
                </div>

                <p class="ui-muted text-sm">Documento: {{ $client->document_number }}</p>

                <div class="flex max-w-4xl flex-wrap items-center gap-x-2 gap-y-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <span>Membresía: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membershipLabel }}</span></span>
                    <span class="text-slate-500">|</span>
                    <span>{{ $membershipDateLabel }}: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membershipDateValue }}</span></span>
                    <span class="text-slate-500">|</span>
                    <span>{{ $membershipCountdownLabel }}: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membershipCountdownValue }}</span></span>
                    <span class="text-slate-500">|</span>
                    <span>Última asistencia: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lastAttendanceLabel }}</span></span>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 flex-wrap items-center gap-2.5">
            <x-ui.button type="button" variant="success" class="px-5 py-2.5" x-on:click="openMembershipModal()">
                Cobrar / Renovar
            </x-ui.button>

            <div class="relative" x-on:keydown.escape.window="actionsOpen = false" x-on:click.outside="actionsOpen = false">
                <x-ui.button type="button" variant="ghost" class="px-4 py-2 text-xs font-bold" x-on:click="actionsOpen = !actionsOpen">
                    Acciones v
                </x-ui.button>

                <div x-cloak
                     x-show="actionsOpen"
                     x-transition.origin.top.right
                     class="absolute right-0 z-30 mt-2 w-64 rounded-xl border border-slate-700 bg-slate-950 p-2 shadow-xl">
                    <form method="POST" action="{{ route('client-credentials.generate-qr', $client->id) }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-slate-200 transition hover:bg-slate-800"
                                x-on:click="actionsOpen = false">
                            <span>Generar QR</span>
                            <span class="text-xs text-slate-400">POST</span>
                        </button>
                    </form>

                    <button type="button"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-slate-200 transition hover:bg-slate-800"
                            x-on:click="openRfidModal()">
                        <span>Asignar RFID</span>
                        <span class="text-xs text-slate-400">Modal</span>
                    </button>

                    @if (! empty($canAdjustMemberships) && $latestMembership)
                        <button type="button"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-slate-200 transition hover:bg-slate-800"
                                x-on:click="openMembershipAdjustmentModal({{ (int) $latestMembership->id }})">
                            <span>Ajustar membresía</span>
                            <span class="text-xs text-slate-400">Modal</span>
                        </button>
                    @endif

                    <a href="{{ route('clients.card', $client->id) }}"
                       target="_blank"
                       rel="noopener"
                       class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-slate-800"
                       x-on:click="actionsOpen = false">
                        <span>Imprimir tarjeta</span>
                        <span class="text-xs text-slate-400">Nueva pestaña</span>
                    </a>

                    <a href="{{ route('reception.index') }}"
                       class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-slate-800"
                       x-on:click="actionsOpen = false">
                        <span>Ir a recepción</span>
                        <span class="text-xs text-slate-400">Ir</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-ui.card>
