@extends('layouts.panel')

@section('title', 'Recepción global')
@section('page-title', 'Recepción consolidada')

@section('content')
    <div class="space-y-4">
        <x-ui.card title="Modo global activo" subtitle="Vista consolidada de asistencias entre sucursales vinculadas.">
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/70">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Sucursales incluidas</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($scopeBranchCount ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 dark:border-cyan-600/50 dark:bg-cyan-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-200">Asistencias cargadas</p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ number_format((int) ($attendanceHistoryTotal ?? 0)) }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-600/50 dark:bg-amber-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-amber-700 dark:text-amber-200">Check-in operativo</p>
                    <p class="mt-1 text-sm font-bold text-amber-800 dark:text-amber-100">Selecciona una sede para registrar ingresos</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Ultimos check-ins" subtitle="Actividad más reciente combinada de todas las sedes.">
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[980px]">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Sede</th>
                            <th>Cliente</th>
                            <th>Método</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($recentAttendances ?? collect()) as $attendance)
                            <tr>
                                <td>{{ $attendance->date?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ (string) ($attendance->time ?? '-') }}</td>
                                <td>{{ (string) ($attendance->gym?->name ?? '-') }}</td>
                                <td>{{ (string) ($attendance->client?->full_name ?? '-') }}</td>
                                <td>{{ (string) ($attendance->credential?->type ?? 'document') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay asistencias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection
