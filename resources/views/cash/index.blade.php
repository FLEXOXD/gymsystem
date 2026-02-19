@extends('layouts.panel')

@section('title', 'Caja')
@section('page-title', 'Caja por turno')

@section('content')
    @if (! $openSession)
        <x-ui.card title="Abrir turno" subtitle="Debes abrir caja para cobrar membresias o registrar movimientos.">
            <form method="POST" action="{{ route('cash.open') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Monto inicial (obligatorio)</span>
                        <input type="number" name="opening_balance" step="0.01" min="0" value="{{ old('opening_balance') }}" required
                               class="ui-input">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                        <span>Notas</span>
                        <textarea name="notes" rows="3" class="ui-input">{{ old('notes') }}</textarea>
                    </label>
                </div>

                <x-ui.button type="submit" variant="success" size="lg">Abrir turno</x-ui.button>
            </form>
        </x-ui.card>
    @else
        @php
            $currencyFormatter = \App\Support\Currency::class;
            $methodLabels = [
                'cash' => 'Efectivo',
                'card' => 'Tarjeta',
                'transfer' => 'Transferencia',
            ];
            $methodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($methodTotals) {
                $row = $methodTotals->firstWhere('method', $method);
                return (object) [
                    'method' => $method,
                    'movements_count' => (int) ($row->movements_count ?? 0),
                    'income_total' => (float) ($row->income_total ?? 0),
                    'expense_total' => (float) ($row->expense_total ?? 0),
                ];
            });
            $oldClosing = old('closing_balance');
            $previewDifference = is_numeric($oldClosing)
                ? round((float) $oldClosing - (float) $summary['expected_balance'], 2)
                : null;
        @endphp

        <x-ui.card title="Turno activo #{{ $openSession->id }}" subtitle="Apertura {{ $openSession->opened_at?->format('Y-m-d H:i') }} por {{ $openSession->openedBy?->name ?? 'N/D' }}">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Apertura</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ $currencyFormatter::format((float) $openSession->opening_balance, $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Ingresos</p>
                    <p class="mt-1 text-2xl font-black text-emerald-800">{{ $currencyFormatter::format((float) $summary['income_total'], $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border border-rose-200 bg-rose-50 p-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700">Egresos</p>
                    <p class="mt-1 text-2xl font-black text-rose-800">{{ $currencyFormatter::format((float) $summary['expense_total'], $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700">Esperado</p>
                    <p class="mt-1 text-2xl font-black text-cyan-800">{{ $currencyFormatter::format((float) $summary['expected_balance'], $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border border-slate-200 bg-white p-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Movimientos</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">{{ (int) $summary['movements_count'] }}</p>
                </article>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
                @foreach ($methodMap as $methodTotal)
                    <article class="rounded-xl border border-slate-200 bg-white p-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ $methodLabels[$methodTotal->method] ?? $methodTotal->method }}</p>
                        <p class="mt-1 text-sm text-slate-700">Movimientos: <strong>{{ $methodTotal->movements_count }}</strong></p>
                        <p class="text-sm text-emerald-700">+ {{ $currencyFormatter::format((float) $methodTotal->income_total, $appCurrencyCode, true) }}</p>
                        <p class="text-sm text-rose-700">- {{ $currencyFormatter::format((float) $methodTotal->expense_total, $appCurrencyCode, true) }}</p>
                    </article>
                @endforeach
            </div>
        </x-ui.card>

        <section class="grid gap-4 xl:grid-cols-3">
            <x-ui.card title="Registrar movimiento" class="xl:col-span-2">
                <form method="POST" action="{{ route('cash.movements.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Tipo</span>
                            <select name="type" required class="ui-input">
                                <option value="">Seleccione</option>
                                <option value="income" @selected(old('type') === 'income')>Ingreso</option>
                                <option value="expense" @selected(old('type') === 'expense')>Egreso</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Metodo</span>
                            <select name="method" required class="ui-input">
                                <option value="">Seleccione</option>
                                <option value="cash" @selected(old('method') === 'cash')>Efectivo</option>
                                <option value="card" @selected(old('method') === 'card')>Tarjeta</option>
                                <option value="transfer" @selected(old('method') === 'transfer')>Transferencia</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Monto</span>
                            <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                                   class="ui-input">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>ID de membresia (opcional)</span>
                            <input type="number" name="membership_id" min="1" value="{{ old('membership_id') }}"
                                   class="ui-input">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2 xl:col-span-4">
                            <span>Descripcion</span>
                            <textarea name="description" rows="2" class="ui-input">{{ old('description') }}</textarea>
                        </label>
                    </div>

                    <x-ui.button type="submit" variant="success">Registrar movimiento</x-ui.button>
                </form>
            </x-ui.card>

            <x-ui.card title="Cerrar turno" subtitle="Cierre con diferencia contra balance esperado.">
                <p class="text-sm text-slate-700">Esperado actual: <strong id="expected-balance">{{ $currencyFormatter::format((float) $summary['expected_balance'], $appCurrencyCode) }}</strong></p>
                <p class="mt-1 text-sm text-slate-700">
                    Vista previa de diferencia:
                    <strong id="difference-preview">{{ $previewDifference !== null ? $currencyFormatter::format($previewDifference, $appCurrencyCode) : 'Ingrese monto final' }}</strong>
                </p>

                <form method="POST" action="{{ route('cash.close') }}" class="mt-4 space-y-4">
                    @csrf
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Monto final contado</span>
                        <input id="closing-balance" type="number" name="closing_balance" step="0.01" min="0" value="{{ old('closing_balance') }}" required
                               class="ui-input">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Notas de cierre</span>
                        <textarea name="notes" rows="3" class="ui-input">{{ old('notes') }}</textarea>
                    </label>

                    <x-ui.button type="submit" variant="danger" size="lg" class="w-full justify-center">Cerrar turno</x-ui.button>
                </form>
            </x-ui.card>
        </section>

        <x-ui.card title="Ultimos 10 movimientos">
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[980px]">
                    <thead>
                    <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Tipo</th>
                        <th class="px-3 py-3">Metodo</th>
                        <th class="px-3 py-3">Monto</th>
                        <th class="px-3 py-3">Cliente</th>
                        <th class="px-3 py-3">Usuario</th>
                        <th class="px-3 py-3">Descripcion</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($latestMovements as $movement)
                        <tr class="border-b border-slate-100 text-sm">
                            <td class="px-3 py-3">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">{{ $movement->type }}</x-ui.badge>
                            </td>
                            <td class="px-3 py-3">{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                            <td class="px-3 py-3 font-semibold {{ $movement->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $movement->type === 'income' ? '+' : '-' }}{{ $currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true) }}
                            </td>
                            <td class="px-3 py-3">{{ $movement->membership?->client?->full_name ?? '-' }}</td>
                            <td class="px-3 py-3">{{ $movement->createdBy?->name ?? '-' }}</td>
                            <td class="px-3 py-3">{{ $movement->description ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-sm text-slate-500">Aun no hay movimientos en este turno.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <x-ui.button :href="route('clients.index')" variant="primary">Cobrar membresia</x-ui.button>
                <x-ui.button :href="route('cash.sessions.index')" variant="secondary">Ver historial de caja</x-ui.button>
                <x-ui.button :href="route('reports.income')" variant="ghost">Ver reporte de ingresos</x-ui.button>
            </div>
        </x-ui.card>
    @endif
@endsection

@push('scripts')
<script>
    (function () {
        const closingInput = document.getElementById('closing-balance');
        const expectedText = document.getElementById('expected-balance');
        const differenceText = document.getElementById('difference-preview');
        if (!closingInput || !expectedText || !differenceText) {
            return;
        }

        const currencySymbol = @json($appCurrencySymbol);
        const expected = Number(String(expectedText.textContent).replace(/[^0-9.-]/g, '')) || 0;

        function updateDifference() {
            const value = Number(closingInput.value || 0);
            if (closingInput.value === '') {
                differenceText.textContent = 'Ingrese monto final';
                differenceText.className = '';
                return;
            }

            const diff = Math.round((value - expected) * 100) / 100;
            differenceText.textContent = (diff >= 0 ? '+' : '') + currencySymbol + Math.abs(diff).toFixed(2);
            if (diff > 0) {
                differenceText.className = 'text-emerald-700';
            } else if (diff < 0) {
                differenceText.className = 'text-rose-700';
            } else {
                differenceText.className = 'text-slate-700';
            }
        }

        closingInput.addEventListener('input', updateDifference);
        updateDifference();

        const closeForm = closingInput.closest('form');
        closeForm?.addEventListener('submit', function (event) {
            const value = closingInput.value.trim();
            if (!value) return;
            const ok = confirm('Confirma cierre de turno con monto final ' + currencySymbol + Number(value).toFixed(2) + '?');
            if (!ok) {
                event.preventDefault();
            }
        });
    })();
</script>
@endpush
