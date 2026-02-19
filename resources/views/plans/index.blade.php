@extends('layouts.panel')

@section('title', 'Planes')
@section('page-title', 'Planes')

@section('content')
    <x-ui.card title="Crear plan" subtitle="Planes base para ventas de membresias.">
        <form method="POST" action="{{ route('plans.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Nombre</span>
                    <input type="text" name="name" value="{{ old('name') }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Duracion (dias)</span>
                    <input type="number" name="duration_days" min="1" value="{{ old('duration_days', 30) }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Precio</span>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', '0.00') }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Estado</span>
                    <select name="status" class="ui-input">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </label>
            </div>

            <x-ui.button type="submit" variant="success">Guardar plan</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card title="Planes del gimnasio">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[720px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Nombre</th>
                    <th class="px-3 py-3">Duracion</th>
                    <th class="px-3 py-3">Precio</th>
                    <th class="px-3 py-3">Estado</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($plans as $plan)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3 font-semibold text-slate-700">{{ $plan->id }}</td>
                        <td class="px-3 py-3">{{ $plan->name }}</td>
                        <td class="px-3 py-3">{{ $plan->duration_days }} dias</td>
                        <td class="px-3 py-3">{{ \App\Support\Currency::format((float) $plan->price, $appCurrencyCode) }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$plan->status === 'active' ? 'success' : 'muted'">{{ $plan->status }}</x-ui.badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">No hay planes registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
