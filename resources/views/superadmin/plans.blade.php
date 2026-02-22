@extends('layouts.panel')

@section('title', 'SuperAdmin Planes')
@section('page-title', 'Planes base y promociones')

@section('content')
    <div class="space-y-5">
        <x-ui.card title="Planes base para control de suscripciones" subtitle="Estos planes se usan en SuperAdmin para renovaciones. No se copian automaticamente al catalogo interno de cada gimnasio.">
            <form method="POST" action="{{ route('superadmin.plan-templates.store') }}" class="grid gap-3 lg:grid-cols-5">
                @csrf
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                    Nombre del plan
                    <input type="text" name="name" class="ui-input" placeholder="Ej: Premium 90 dias" required>
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Unidad
                    <select id="template-duration-unit" name="duration_unit" class="ui-input">
                        <option value="days">Dias</option>
                        <option value="months">Meses</option>
                    </select>
                </label>
                <label id="template-duration-days-wrap" class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Duracion (dias)
                    <input id="template-duration-days" type="number" name="duration_days" min="1" max="3650" class="ui-input" value="30">
                </label>
                <label id="template-duration-months-wrap" class="hidden space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Duracion (meses)
                    <input id="template-duration-months" type="number" name="duration_months" min="1" max="120" class="ui-input" value="1">
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Precio
                    <input type="number" step="0.01" min="0" name="price" class="ui-input" placeholder="90.00" required>
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Estado
                    <select name="status" class="ui-input">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </label>
                <div class="flex items-end lg:col-span-5">
                    <x-ui.button type="submit">Crear plan base</x-ui.button>
                </div>
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="ui-table min-w-[860px]">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Plan</th>
                            <th class="px-3 py-3">Duracion</th>
                            <th class="px-3 py-3">Precio</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($plans as $plan)
                            @php
                                $isActive = (string) ($plan->status ?? '') === 'active';
                            @endphp
                            <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $plan->name }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ \App\Support\Currency::format((float) $plan->price, $appCurrencyCode ?? 'USD') }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $isActive ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                        {{ $isActive ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('superadmin.plan-templates.toggle', $plan->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $isActive ? 'inactive' : 'active' }}">
                                            <x-ui.button type="submit" size="sm" variant="ghost">{{ $isActive ? 'Desactivar' : 'Activar' }}</x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.plan-templates.destroy', $plan->id) }}" onsubmit="return confirm('Eliminar este plan base?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="danger">Eliminar</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">No hay planes base creados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <x-ui.card title="Promociones base" subtitle="Estas promociones son referencias administrativas de SuperAdmin y no se copian automaticamente al panel del gimnasio.">
            <form method="POST" action="{{ route('superadmin.plan-templates.promotions.store') }}" class="grid gap-3 lg:grid-cols-4">
                @csrf
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Nombre promocion
                    <input type="text" name="name" class="ui-input" placeholder="Ej: Promo lanzamiento" required>
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Plan asociado
                    <select name="plan_template_id" class="ui-input">
                        <option value="">Todos los planes</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Tipo
                    <select name="type" class="ui-input">
                        <option value="percentage">Porcentaje</option>
                        <option value="fixed">Monto fijo</option>
                        <option value="final_price">Precio final</option>
                        <option value="bonus_days">Dias extra</option>
                        <option value="two_for_one">2x1</option>
                        <option value="bring_friend">Trae un amigo</option>
                    </select>
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Valor
                    <input type="number" step="0.01" min="0" name="value" class="ui-input" placeholder="10">
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                    Descripcion
                    <input type="text" name="description" class="ui-input" placeholder="Texto breve de la promocion">
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Inicio
                    <input type="date" name="starts_at" class="ui-input">
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Fin
                    <input type="date" name="ends_at" class="ui-input">
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Estado
                    <select name="status" class="ui-input">
                        <option value="active">Activa</option>
                        <option value="inactive">Inactiva</option>
                    </select>
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Maximo usos
                    <input type="number" min="1" name="max_uses" class="ui-input" placeholder="Opcional">
                </label>
                <div class="flex items-end lg:col-span-4">
                    <x-ui.button type="submit">Crear promocion base</x-ui.button>
                </div>
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="ui-table min-w-[980px]">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Promocion</th>
                            <th class="px-3 py-3">Plan</th>
                            <th class="px-3 py-3">Tipo</th>
                            <th class="px-3 py-3">Valor</th>
                            <th class="px-3 py-3">Vigencia</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $promotion)
                            @php
                                $isActive = (string) ($promotion->status ?? '') === 'active';
                                $valueLabel = $promotion->value !== null ? (string) $promotion->value : '-';
                                $rangeLabel = ($promotion->starts_at ? $promotion->starts_at->toDateString() : '-') . ' / ' . ($promotion->ends_at ? $promotion->ends_at->toDateString() : '-');
                            @endphp
                            <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $promotion->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $promotion->description ?: '-' }}</p>
                                </td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $promotion->planTemplate?->name ?? 'Todos' }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $promotion->type }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $valueLabel }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $rangeLabel }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $isActive ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                        {{ $isActive ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('superadmin.plan-templates.promotions.toggle', $promotion->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $isActive ? 'inactive' : 'active' }}">
                                            <x-ui.button type="submit" size="sm" variant="ghost">{{ $isActive ? 'Desactivar' : 'Activar' }}</x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.plan-templates.promotions.destroy', $promotion->id) }}" onsubmit="return confirm('Eliminar esta promocion base?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="danger">Eliminar</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">No hay promociones base creadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const durationUnit = document.getElementById('template-duration-unit');
        const daysWrap = document.getElementById('template-duration-days-wrap');
        const monthsWrap = document.getElementById('template-duration-months-wrap');
        const daysInput = document.getElementById('template-duration-days');
        const monthsInput = document.getElementById('template-duration-months');

        if (!durationUnit || !daysWrap || !monthsWrap || !daysInput || !monthsInput) return;

        function syncDurationMode() {
            const isMonths = String(durationUnit.value || '') === 'months';

            daysWrap.classList.toggle('hidden', isMonths);
            monthsWrap.classList.toggle('hidden', !isMonths);

            daysInput.required = !isMonths;
            monthsInput.required = isMonths;
        }

        durationUnit.addEventListener('change', syncDurationMode);
        syncDurationMode();
    })();
</script>
@endpush
