<x-ui.card title="Asistencias recientes" subtitle="Ultimos ingresos del cliente.">
    @if ($client->attendances->isNotEmpty())
        <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
            <table class="ui-table min-w-[760px]">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Metodo</th>
                    <th>Credencial</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($client->attendances as $attendance)
                    @php
                        $attendanceMethod = $attendance->credential?->type ?? 'document';
                    @endphp
                    <tr>
                        <td>{{ $attendance->date?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td>{{ $attendance->time ? mb_substr((string) $attendance->time, 0, 5) : '-' }}</td>
                        <td>{{ $attendanceMethodLabels[$attendanceMethod] ?? strtoupper($attendanceMethod) }}</td>
                        <td class="font-mono text-xs">{{ $attendance->credential?->value ? substr($attendance->credential->value, -12) : '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
            <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 3"/>
                </svg>
            </div>
            <p class="font-semibold">Sin asistencias recientes.</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Registra el proximo ingreso desde Recepcion.</p>
            <x-ui.button :href="route('reception.index')" variant="ghost" size="sm" class="mt-3">Ir a recepcion</x-ui.button>
        </div>
    @endif
</x-ui.card>
