<x-ui.card title="Credenciales" subtitle="Gestion de QR y RFID con acciones directas.">
    <div class="mb-4 flex flex-wrap gap-2">
        <x-ui.button type="button" size="sm" x-on:click="openRfidModal()">Asignar RFID</x-ui.button>
        <form method="POST" action="{{ route('client-credentials.generate-qr', $client->id) }}">
            @csrf
            <x-ui.button type="submit" size="sm" variant="secondary">Generar QR</x-ui.button>
        </form>
    </div>

    @if (session('generated_qr_value'))
        <x-ui.alert type="success" class="mb-4">
            QR generado: <span class="font-mono text-xs">{{ session('generated_qr_value') }}</span>
        </x-ui.alert>
    @endif

    @if ($activeQrCredential)
        <div class="mb-5 grid gap-4 rounded-xl border border-cyan-500/30 bg-cyan-500/10 p-4 md:grid-cols-[210px_1fr] md:items-center">
            <div class="rounded-lg border border-slate-300 bg-slate-100 p-3 text-center dark:border-white/10 dark:bg-slate-900/60">
                {!! $activeQrSvg !!}
            </div>
            <div class="space-y-3">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">QR activo para acceso</p>
                <p class="break-all rounded-lg border border-slate-300 bg-slate-100 p-2 font-mono text-xs text-slate-800 dark:border-white/10 dark:bg-slate-900/60 dark:text-slate-200">{{ $activeQrCredential->value }}</p>
                <div class="flex flex-wrap gap-2">
                    <x-ui.button type="button" variant="muted" size="sm" x-on:click="copyQr(@js($activeQrCredential->value))">Copiar valor</x-ui.button>
                    <x-ui.button :href="route('clients.card', $client->id)" target="_blank" rel="noopener" variant="ghost" size="sm">Imprimir tarjeta</x-ui.button>
                    <x-ui.button :href="route('clients.card.pdf', $client->id)" target="_blank" rel="noopener" variant="secondary" size="sm">Exportar PDF</x-ui.button>
                </div>
                <p class="text-xs text-cyan-700 dark:text-cyan-300" x-text="qrCopyFeedback"></p>
            </div>
        </div>
    @endif

    @if ($client->credentials->isNotEmpty())
        <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
            <table class="ui-table min-w-[880px]">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th>Accion</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($client->credentials as $credential)
                    @php
                        $displayValue = $credential->value;
                        if ($credential->type === 'rfid' && strlen((string) $credential->value) > 6) {
                            $displayValue = str_repeat('*', 6).' '.substr((string) $credential->value, -6);
                        }
                        $formId = 'deactivate-credential-'.$credential->id;
                        $credentialLabel = strtoupper((string) $credential->type).' #'.$credential->id;
                    @endphp
                    <tr>
                        <td>#{{ $credential->id }}</td>
                        <td class="font-semibold">{{ strtoupper((string) $credential->type) }}</td>
                        <td class="font-mono text-xs">{{ $displayValue }}</td>
                        <td>
                            <x-ui.badge :variant="$credential->status === 'active' ? 'success' : 'muted'">{{ $credential->status }}</x-ui.badge>
                        </td>
                        <td>{{ $credential->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>
                            @if ($credential->status === 'active')
                                <form id="{{ $formId }}"
                                      method="POST"
                                      action="{{ route('client-credentials.deactivate', $credential->id) }}"
                                      x-on:submit.prevent="requestDeactivate(@js($formId), @js($credentialLabel))">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                                </form>
                            @else
                                <span class="text-slate-500 dark:text-slate-400">-</span>
                            @endif
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
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 9h10"/>
                    <path d="M7 13h6"/>
                </svg>
            </div>
            <p class="font-semibold">Sin credenciales registradas.</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Puedes asignar RFID o generar QR para activar el acceso.</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <x-ui.button type="button" size="sm" variant="ghost" x-on:click="openRfidModal()">Asignar RFID</x-ui.button>
                <form method="POST" action="{{ route('client-credentials.generate-qr', $client->id) }}">
                    @csrf
                    <x-ui.button type="submit" size="sm" variant="secondary">Generar QR</x-ui.button>
                </form>
            </div>
        </div>
    @endif
</x-ui.card>
