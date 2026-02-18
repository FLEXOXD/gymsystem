@extends('layouts.panel')

@section('title', 'Historial de notificaciones')
@section('page-title', 'Historial de notificaciones')

@section('content')
    <x-ui.card title="Historial" subtitle="Notificaciones sent/skipped con filtros por fecha y gimnasio.">
        <form method="GET" action="{{ route('superadmin.notifications.history') }}" class="mb-4 grid gap-3 md:grid-cols-4">
            <label class="text-sm font-semibold ui-muted">
                Desde
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="ui-input mt-1 block w-full">
            </label>
            <label class="text-sm font-semibold ui-muted">
                Hasta
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="ui-input mt-1 block w-full">
            </label>
            <label class="text-sm font-semibold ui-muted">
                Gym
                <select name="gym_id" class="ui-input mt-1 block w-full">
                    <option value="">Todos</option>
                    @foreach ($gyms as $gym)
                        <option value="{{ $gym->id }}" @selected($filters['gym_id'] === (int) $gym->id)>{{ $gym->name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end">
                <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Gym</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Vence</th>
                    <th class="px-3 py-3">Gestionado por</th>
                    <th class="px-3 py-3">Enviado en</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($notifications as $notification)
                    <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->scheduled_for?->toDateString() }}</td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $notification->gym?->name ?? 'N/A' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->type }}</td>
                        <td class="px-3 py-3">
                            @php
                                $statusClass = $notification->status === 'sent'
                                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200'
                                    : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-100';
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusClass }}">
                                {{ $notification->status }}
                            </span>
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->subscription?->plan_name ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->subscription?->ends_at?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->createdBy?->name ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay resultados para el filtro actual.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </x-ui.card>
@endsection
