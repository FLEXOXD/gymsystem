@extends('layouts.panel')

@section('title', 'Reporte de membresias')
@section('page-title', 'Reporte de membresias')

@push('styles')
<style>
    .report-memberships .report-toolbar {
        align-items: end;
    }

    .report-memberships .members-table-wrap {
        border-radius: 0.85rem;
        border: 1px solid rgb(203 213 225);
        overflow: auto;
    }

    .theme-dark .report-memberships .members-table-wrap {
        border-color: rgb(51 65 85 / 0.85);
    }

    .report-memberships .members-table-wrap .ui-table thead th {
        position: sticky;
        top: 0;
        z-index: 4;
    }
</style>
@endpush

@section('content')
    <div class="report-memberships space-y-4">
        <x-ui.card title="Vista operativa de membresias" subtitle="Busca rapido clientes por nombre o documento y revisa su estado.">
            <div class="report-toolbar grid gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto]">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Buscar cliente</span>
                    <input id="membership-search" type="text" class="ui-input" placeholder="Nombre o documento..." autocomplete="off">
                </label>
                <x-ui.button id="membership-clear" type="button" variant="ghost">Limpiar</x-ui.button>
                <x-ui.button :href="route('reports.index', request()->query())" variant="secondary">Volver al panel</x-ui.button>
            </div>
        </x-ui.card>

        <section class="grid gap-4 md:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Activos</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ (int) $membershipSummary['active'] }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Vencidos</p>
                <p class="mt-2 text-3xl font-black text-rose-700">{{ (int) $membershipSummary['expired'] }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total clientes</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $membershipSummary['total_clients'] }}</p>
            </x-ui.card>
        </section>

        <x-ui.card title="Clientes con membresia activa">
            <p class="mb-3 text-sm ui-muted">
                Mostrando <strong id="active-visible-count">{{ $activeClients->count() }}</strong> de <strong id="active-total-count">{{ $activeClients->count() }}</strong> registros.
            </p>
            <div class="members-table-wrap">
                <table class="ui-table min-w-[820px]">
                    <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3">Cliente</th>
                        <th class="px-3 py-3">Documento</th>
                        <th class="px-3 py-3">Inicio</th>
                        <th class="px-3 py-3">Fin</th>
                        <th class="px-3 py-3">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($activeClients as $client)
                        @php
                            $activeSearch = \Illuminate\Support\Str::lower(trim((string) $client->full_name.' '.(string) $client->document_number));
                        @endphp
                        <tr data-membership-row data-group="active" data-search="{{ $activeSearch }}" class="border-b border-slate-100 text-sm">
                            <td class="px-3 py-3">{{ $client->full_name }}</td>
                            <td class="px-3 py-3">{{ $client->document_number }}</td>
                            <td class="px-3 py-3">{{ $client->starts_at ?? '-' }}</td>
                            <td class="px-3 py-3">{{ $client->ends_at ?? '-' }}</td>
                            <td class="px-3 py-3"><x-ui.badge variant="success">{{ $client->status }}</x-ui.badge></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay clientes activos.</td>
                        </tr>
                    @endforelse
                    <tr id="active-empty-filter" class="hidden">
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay coincidencias para tu busqueda.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card title="Clientes con membresia vencida o no activa">
            <p class="mb-3 text-sm ui-muted">
                Mostrando <strong id="expired-visible-count">{{ $expiredClients->count() }}</strong> de <strong id="expired-total-count">{{ $expiredClients->count() }}</strong> registros.
            </p>
            <div class="members-table-wrap">
                <table class="ui-table min-w-[820px]">
                    <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3">Cliente</th>
                        <th class="px-3 py-3">Documento</th>
                        <th class="px-3 py-3">Inicio</th>
                        <th class="px-3 py-3">Fin</th>
                        <th class="px-3 py-3">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($expiredClients as $client)
                        @php
                            $expiredSearch = \Illuminate\Support\Str::lower(trim((string) $client->full_name.' '.(string) $client->document_number));
                        @endphp
                        <tr data-membership-row data-group="expired" data-search="{{ $expiredSearch }}" class="border-b border-slate-100 text-sm">
                            <td class="px-3 py-3">{{ $client->full_name }}</td>
                            <td class="px-3 py-3">{{ $client->document_number }}</td>
                            <td class="px-3 py-3">{{ $client->starts_at ?? '-' }}</td>
                            <td class="px-3 py-3">{{ $client->ends_at ?? '-' }}</td>
                            <td class="px-3 py-3"><x-ui.badge variant="danger">{{ $client->status }}</x-ui.badge></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay clientes vencidos.</td>
                        </tr>
                    @endforelse
                    <tr id="expired-empty-filter" class="hidden">
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay coincidencias para tu busqueda.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('membership-search');
        const clearButton = document.getElementById('membership-clear');
        const rows = Array.from(document.querySelectorAll('[data-membership-row]'));
        const activeVisible = document.getElementById('active-visible-count');
        const expiredVisible = document.getElementById('expired-visible-count');
        const activeEmpty = document.getElementById('active-empty-filter');
        const expiredEmpty = document.getElementById('expired-empty-filter');

        function normalizeText(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function applyFilter() {
            if (!rows.length) return;

            const term = normalizeText(searchInput?.value || '');
            let activeCount = 0;
            let expiredCount = 0;

            rows.forEach(function (row) {
                const rowSearch = normalizeText(row.getAttribute('data-search') || '');
                const group = row.getAttribute('data-group') || '';
                const show = term === '' || rowSearch.includes(term);
                row.classList.toggle('hidden', !show);

                if (!show) return;
                if (group === 'active') activeCount += 1;
                if (group === 'expired') expiredCount += 1;
            });

            if (activeVisible) activeVisible.textContent = String(activeCount);
            if (expiredVisible) expiredVisible.textContent = String(expiredCount);
            if (activeEmpty) activeEmpty.classList.toggle('hidden', activeCount > 0);
            if (expiredEmpty) expiredEmpty.classList.toggle('hidden', expiredCount > 0);
            if (clearButton) clearButton.classList.toggle('opacity-70', term.length === 0);
        }

        function clearFilter() {
            if (searchInput) searchInput.value = '';
            applyFilter();
        }

        searchInput?.addEventListener('input', applyFilter);
        clearButton?.addEventListener('click', clearFilter);
        applyFilter();
    })();
</script>
@endpush
