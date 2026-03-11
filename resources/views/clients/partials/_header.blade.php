<x-ui.card class="client-hero-card p-4 sm:p-6 lg:p-7">
    <div class="client-hero-layout">
        <div class="min-w-0 space-y-4">
            <div class="flex min-w-0 items-start gap-3 sm:gap-5">
                <div class="h-14 w-14 overflow-hidden rounded-2xl border border-slate-300 bg-slate-100 shadow-sm dark:border-white/10 dark:bg-slate-900/40 sm:h-16 sm:w-16">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $client->full_name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-lg font-black uppercase text-slate-700 dark:text-slate-200">
                            {{ mb_strtoupper(mb_substr($client->first_name, 0, 1).mb_substr($client->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1 space-y-2">
                    <div class="min-w-0 max-w-3xl">
                        <h2 class="ui-heading text-xl leading-tight sm:text-2xl">{{ $client->full_name }}</h2>
                        <p class="ui-muted mt-1 text-sm">Documento: {{ $client->document_number }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label">Membresía</span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $membershipLabel }}</p>
                </div>
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label">{{ $membershipDateLabel }}</span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $membershipDateValue }}</p>
                </div>
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label">{{ $membershipCountdownLabel }}</span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $membershipCountdownValue }}</p>
                </div>
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label">Última asistencia</span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $lastAttendanceLabel }}</p>
                </div>
            </div>
        </div>

        <div class="client-hero-actions">
            <div class="client-hero-status">
                <x-ui.badge :variant="$membershipBadgeVariant" class="px-4 py-2 text-[11px] font-extrabold tracking-[0.12em]">
                    {{ $membershipBadgeText }}
                </x-ui.badge>
            </div>

            <x-ui.button type="button" variant="success" class="w-full justify-center px-4 py-3 text-sm font-bold" x-on:click="openMembershipModal()">
                Cobrar / Renovar
            </x-ui.button>

            @if (! empty($canShowProgress) && ! empty($progressTabUrl))
                <x-ui.button :href="$progressTabUrl" variant="secondary" class="w-full justify-center px-4 py-3 text-sm font-bold">
                    Ver rendimiento
                </x-ui.button>
            @endif

            <div class="relative client-hero-actions-full" x-on:keydown.escape.window="actionsOpen = false" x-on:click.outside="actionsOpen = false">
                <x-ui.button type="button"
                             variant="ghost"
                             class="w-full justify-between px-4 py-3 text-sm font-bold"
                             x-bind:aria-expanded="actionsOpen.toString()"
                             x-on:click="actionsOpen = !actionsOpen">
                    <span>Más acciones</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 transition"
                         x-bind:class="actionsOpen ? 'rotate-180' : ''"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </x-ui.button>

                <div x-cloak
                     x-show="actionsOpen"
                     x-transition.origin.top.right
                     class="client-action-popover absolute right-0 z-30 mt-2 rounded-2xl border border-slate-700 bg-slate-950 p-2 shadow-2xl">
                    <div class="space-y-1.5">
                    <form method="POST" action="{{ route('client-credentials.generate-qr', $client->id) }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center justify-between rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2.5 text-left text-sm font-medium text-slate-100 transition hover:border-slate-700 hover:bg-slate-800"
                                x-on:click="actionsOpen = false">
                            <span>Generar QR</span>
                            <span class="text-xs text-slate-400">POST</span>
                        </button>
                    </form>

                    <button type="button"
                            class="flex w-full items-center justify-between rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2.5 text-left text-sm font-medium text-slate-100 transition hover:border-slate-700 hover:bg-slate-800"
                            x-on:click="actionsOpen = false; openRfidModal()">
                        <span>Asignar RFID</span>
                        <span class="text-xs text-slate-400">Modal</span>
                    </button>

                    @if (! empty($canAdjustMemberships) && $latestMembership)
                        <button type="button"
                                class="flex w-full items-center justify-between rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2.5 text-left text-sm font-medium text-slate-100 transition hover:border-slate-700 hover:bg-slate-800"
                                x-on:click="actionsOpen = false; openMembershipAdjustmentModal({{ (int) $latestMembership->id }})">
                            <span>Ajustar membresía</span>
                            <span class="text-xs text-slate-400">Modal</span>
                        </button>
                    @endif

                    <a href="{{ route('clients.card', $client->id) }}"
                       target="_blank"
                       rel="noopener"
                       class="flex w-full items-center justify-between rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2.5 text-sm font-medium text-slate-100 transition hover:border-slate-700 hover:bg-slate-800"
                       x-on:click="actionsOpen = false">
                        <span>Imprimir tarjeta</span>
                        <span class="text-xs text-slate-400">Nueva pestaña</span>
                    </a>

                    <a href="{{ route('reception.index') }}"
                       class="flex w-full items-center justify-between rounded-xl border border-slate-800 bg-slate-900/80 px-3 py-2.5 text-sm font-medium text-slate-100 transition hover:border-slate-700 hover:bg-slate-800"
                       x-on:click="actionsOpen = false">
                        <span>Ir a recepción</span>
                        <span class="text-xs text-slate-400">Ir</span>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-ui.card>
