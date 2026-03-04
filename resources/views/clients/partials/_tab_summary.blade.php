<div class="grid gap-6 xl:grid-cols-12">
    <div class="space-y-6 xl:col-span-8">
        <x-ui.card title="Estado de membresía" subtitle="Vista rapida para recepción y renovación.">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Estado actual</p>
                    <div class="mt-2 flex items-center gap-2">
                        <x-ui.badge :variant="$membershipBadgeVariant">{{ $membershipBadgeText }}</x-ui.badge>
                        @if ($latestMembership)
                            <span class="text-sm text-slate-700 dark:text-slate-300">Plan: {{ $latestMembership->plan?->name ?? 'Sin plan' }}</span>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-slate-700 dark:text-slate-300">Vence: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $membershipEndsLabel }}</span></p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Días restantes: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $remainingLabel }}</span></p>
                    <x-ui.button type="button" variant="success" class="mt-4 w-full" x-on:click="openMembershipModal()">Cobrar / Renovar</x-ui.button>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Cliente</p>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Estado general: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $statusLabels[$client->status] ?? $client->status }}</span></p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Teléfono: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $client->phone ?: '-' }}</span></p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Ultima asistencia: <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lastAttendanceLabel }}</span></p>
                </div>
            </div>
        </x-ui.card>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-ui.card title="Últimas asistencias" subtitle="Últimos ingresos registrados.">
                @if ($attendancePreview->isNotEmpty())
                    <div class="space-y-2">
                        @foreach ($attendancePreview as $attendance)
                            <div class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm dark:border-white/10 dark:bg-slate-900/40">
                                <span class="text-slate-700 dark:text-slate-200">{{ $attendance->date?->translatedFormat('d M Y') ?? '-' }}</span>
                                <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $attendance->time ? mb_substr((string) $attendance->time, 0, 5) : '--:--' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                        <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12h18"/>
                                <path d="M12 3v18"/>
                            </svg>
                        </div>
                        <p class="font-semibold">Aún no hay asistencias registradas.</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cuando el cliente haga check-in aparecera aqui.</p>
                        <x-ui.button :href="route('reception.index')" variant="ghost" size="sm" class="mt-3">Ir a recepción</x-ui.button>
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card title="Últimos pagos" subtitle="Movimientos de caja vinculados al cliente.">
                @if ($paymentsPreview->isNotEmpty())
                    <div class="space-y-2">
                        @foreach ($paymentsPreview as $movement)
                            <div class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm dark:border-white/10 dark:bg-slate-900/40">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ $movement->membership?->plan?->name ?? 'Pago membresía' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</p>
                                </div>
                                <span class="font-bold text-emerald-700 dark:text-emerald-300">{{ \App\Support\Currency::format((float) $movement->amount, $appCurrencyCode) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                        <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16"/>
                                <path d="M4 12h16"/>
                                <path d="M4 17h16"/>
                            </svg>
                        </div>
                        <p class="font-semibold">Sin pagos vinculados todavia.</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cobra una membresía para registrar el primer pago.</p>
                        <x-ui.button type="button" variant="ghost" size="sm" class="mt-3" x-on:click="openMembershipModal()">Cobrar / Renovar</x-ui.button>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    <div class="space-y-6 xl:col-span-4">
        <x-ui.card title="Perfil del cliente" subtitle="Datos base de contacto.">
            <div class="space-y-4">
                <div class="h-40 overflow-hidden rounded-xl border border-slate-300 bg-slate-100 dark:border-white/10 dark:bg-slate-900/50">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="Foto cliente" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-slate-600 dark:text-slate-400">Sin foto</div>
                    @endif
                </div>

                <form method="POST"
                      action="{{ route('clients.photo.update', $client->id) }}"
                      enctype="multipart/form-data"
                      class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <input type="file"
                           name="photo"
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           class="ui-input">
                    @error('photo')
                        <p class="text-xs font-semibold text-rose-500 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                    <x-ui.button type="submit" variant="ghost" size="sm" class="w-full">Guardar foto</x-ui.button>
                </form>

                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Documento</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $client->document_number }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Teléfono</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $client->phone ?: '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Estado</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $statusLabels[$client->status] ?? $client->status }}</dd>
                    </div>
                </dl>
            </div>
        </x-ui.card>

        <x-ui.card title="Accesos rápidos" subtitle="Atajos operativos compactos.">
            <div class="space-y-2">
                <a href="{{ route('reception.index') }}" class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800">
                    <span>Ir a recepción</span>
                    <span class="text-slate-500 dark:text-slate-400">-></span>
                </a>

                <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('attendance')">
                    <span>Ver asistencias</span>
                    <span class="text-slate-500 dark:text-slate-400">Tab</span>
                </button>

                <button type="button" class="text-xs font-semibold text-cyan-700 underline dark:text-cyan-300" x-on:click="quickMoreOpen = !quickMoreOpen">
                    Ver más
                </button>

                <div x-cloak x-show="quickMoreOpen" class="space-y-2">
                    <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('membership')">
                        <span>Ver membresías y pagos</span>
                        <span class="text-slate-500 dark:text-slate-400">Tab</span>
                    </button>

                    <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('credentials')">
                        <span>Ver credenciales</span>
                        <span class="text-slate-500 dark:text-slate-400">Tab</span>
                    </button>

                    @if (! empty($canManageClientAccounts))
                        <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('app_access')">
                            <span>Usuario app cliente</span>
                            <span class="text-slate-500 dark:text-slate-400">Tab</span>
                        </button>
                    @endif

                    <a href="{{ route('cash.index') }}" class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span>Ir a caja</span>
                        <span class="text-slate-500 dark:text-slate-400">-></span>
                    </a>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
