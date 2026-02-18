@extends('layouts.panel')

@section('title', 'Inbox de notificaciones')
@section('page-title', 'Notificaciones pendientes')

@section('content')
    <x-ui.card title="Inbox de notificaciones" subtitle="Avisos automaticos por vencimiento y dias de gracia.">
        <form method="GET" action="{{ route('superadmin.notifications.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Fecha
                <input type="date" name="date" value="{{ $selectedDate }}" class="ui-input mt-1 block">
            </label>
            <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
        </form>

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">Pendientes para {{ $selectedDate }}: <strong>{{ $notifications->count() }}</strong></div>
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Gym</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Vence</th>
                    <th class="px-3 py-3">Canal</th>
                    <th class="px-3 py-3">Mensaje</th>
                    <th class="px-3 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($notifications as $notification)
                    @php
                        $typeClass = str_starts_with($notification->type, 'grace_')
                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'
                            : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200';
                    @endphp
                    <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $typeClass }}">
                                {{ $notification->type }}
                            </span>
                        </td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $notification->gym?->name ?? 'N/A' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->subscription?->plan_name ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->subscription?->ends_at?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $notification->channel }}</td>
                        <td class="px-3 py-3">
                            <p id="msg-{{ $notification->id }}" class="max-w-md whitespace-pre-wrap break-words text-xs text-slate-700 dark:text-slate-200">{{ $notification->message_snapshot }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-600"
                                        data-copy-target="msg-{{ $notification->id }}">
                                    Copiar mensaje
                                </button>
                                <form method="POST" action="{{ route('superadmin.notifications.sent', $notification->id) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="sm" variant="success">Marcar enviado</x-ui.button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.notifications.skipped', $notification->id) }}">
                                    @csrf
                                    <x-ui.button type="submit" size="sm" variant="danger">Omitir</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay notificaciones pendientes para esta fecha.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-copy-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.getAttribute('data-copy-target');
            const element = document.getElementById(id);
            if (!element) {
                return;
            }

            navigator.clipboard.writeText(element.innerText).then(function () {
                const original = button.innerText;
                button.innerText = 'Copiado';
                setTimeout(function () {
                    button.innerText = original;
                }, 1000);
            });
        });
    });
</script>
@endpush
