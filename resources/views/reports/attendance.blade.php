@extends('layouts.panel')

@section('title', 'Reporte de asistencias')
@section('page-title', 'Reporte de asistencias')

@section('content')
    <x-ui.card title="Filtro de asistencias">
        <form method="GET" action="{{ route('reports.attendance') }}" class="grid gap-3 md:grid-cols-3 md:items-end">
            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Desde</span>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="ui-input">
            </label>

            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Hasta</span>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="ui-input">
            </label>

            <div class="flex flex-wrap gap-2">
                <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
                <x-ui.button :href="route('reports.index', request()->query())" variant="ghost">Panel</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total asistencias</p>
        <p class="mt-2 text-3xl font-black text-slate-900">{{ (int) $attendanceSummary['total_attendances'] }}</p>
    </x-ui.card>

    <x-ui.card title="Asistencias por dia">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[560px]">
                <thead>
                <tr class="sticky top-0 border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Cantidad</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($attendanceByDay as $row)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}</td>
                        <td class="px-3 py-3">{{ (int) $row->attendances_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-3 py-6 text-center text-sm text-slate-500">No hay asistencias en este rango.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
