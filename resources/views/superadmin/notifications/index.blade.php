@extends('layouts.panel')

@section('title', 'Bandeja de notificaciones')
@section('page-title', 'Notificaciones pendientes')

@section('content')
    <x-ui.card title="Campanas push" subtitle="Envio segmentado de notificaciones push a gimnasios y roles operativos.">
        <form method="POST"
              action="{{ route('superadmin.notifications.push-campaigns.send') }}"
              class="grid gap-3 md:grid-cols-2 xl:grid-cols-4"
              data-ui-loading-overlay="1"
              data-ui-loading-message="Enviando campana push...">
            @csrf
            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block w-full">
                    <option value="">Todos los gimnasios</option>
                    @foreach ($gyms as $gym)
                        <option value="{{ $gym->id }}" @selected((int) old('gym_id') === (int) $gym->id)>{{ $gym->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Audiencia
                <select name="audience" class="ui-input mt-1 block w-full" required>
                    <option value="owners" @selected(old('audience', 'owners') === 'owners')>Solo duenos</option>
                    <option value="staff" @selected(old('audience') === 'staff')>Duenos y cajeros</option>
                    <option value="all_users" @selected(old('audience') === 'all_users')>Todos los usuarios del gym</option>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-2">
                Titulo
                <input type="text" name="title" value="{{ old('title') }}" maxlength="120" class="ui-input mt-1 block w-full" placeholder="Ej: Recordatorio de renovacion" required>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-2">
                Mensaje
                <input type="text" name="body" value="{{ old('body') }}" maxlength="255" class="ui-input mt-1 block w-full" placeholder="Mensaje corto para push en celular" required>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-3">
                Detalle (opcional)
                <textarea name="detail_text" rows="3" maxlength="1500" class="ui-input mt-1 block w-full" placeholder="Texto adicional para abrir en detalle">{{ old('detail_text') }}</textarea>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Tag (opcional)
                <input type="text" name="tag" value="{{ old('tag') }}" maxlength="120" class="ui-input mt-1 block w-full" placeholder="promo-marzo-2026">
            </label>

            <div class="flex items-end xl:col-span-1">
                <x-ui.button type="submit" variant="primary">Enviar campana push</x-ui.button>
            </div>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Audiencia</th>
                    <th class="px-3 py-3">Titulo</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Enviadas</th>
                    <th class="px-3 py-3">Fallidas</th>
                    <th class="px-3 py-3">Saltadas</th>
                </tr>
                </thead>
                <tbody>
                @forelse (($pushCampaigns ?? collect()) as $campaign)
                    @php
                        $statusVariant = match ((string) ($campaign->status ?? 'queued')) {
                            'sent' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                            'partial' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            'failed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                            'skipped' => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-100',
                            'sending' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200',
                            default => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
                        };
                        $audienceLabel = match ((string) ($campaign->audience ?? 'owners')) {
                            'staff' => 'Duenos y cajeros',
                            'all_users' => 'Todos usuarios',
                            default => 'Solo duenos',
                        };
                    @endphp
                    <tr data-push-campaign-status="{{ (string) ($campaign->status ?? 'queued') }}" class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 dark:text-slate-200">{{ $campaign->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $campaign->gym?->name ?? 'Todos' }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $audienceLabel }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $campaign->title }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusVariant }}">
                                {{ $campaign->status }}
                            </span>
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ (int) ($campaign->sent_count ?? 0) }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ (int) ($campaign->failed_count ?? 0) }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ (int) ($campaign->skipped_count ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            Aun no se han enviado campanas push.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card title="Bandeja de notificaciones" subtitle="Avisos automaticos por vencimiento y dias de gracia.">
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
                    <th class="px-3 py-3">Gimnasio</th>
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
                        $typeLabel = match ($notification->type) {
                            'expires_7' => 'Vence en 7 dias',
                            'expires_3' => 'Vence en 3 dias',
                            'expires_1' => 'Vence en 1 dia',
                            'grace_1' => 'Gracia dia 1',
                            'grace_2' => 'Gracia dia 2',
                            'grace_3' => 'Gracia dia 3',
                            default => str_replace('_', ' ', $notification->type),
                        };
                    @endphp
                    <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $typeClass }}">
                                {{ $typeLabel }}
                            </span>
                        </td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $notification->gym?->name ?? 'N/D' }}</td>
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
