@extends('layouts.panel')

@section('title', 'SuperAdmin Planes')
@section('page-title', 'Planes base y promociones')

@section('content')
    @php
        $planPresentation = is_array($planPresentation ?? null) ? $planPresentation : [];
        $schemaReady = (bool) ($schemaReady ?? true);
        $money = static fn (float $amount): string => number_format($amount, 2, ',', '.');
    @endphp

    <div class="space-y-5">
        <x-ui.card title="Planes base conectados con la landing" subtitle="Lo que edites aqui se refleja en la seccion publica de precios.">
            @if (! $schemaReady)
                <div class="ui-alert ui-alert-danger mb-4 text-xs font-semibold">
                    Falta migracion de planes base. Ejecuta: <code>php artisan migrate</code>.
                </div>
            @else
                <div class="ui-alert ui-alert-success mb-4 text-xs font-semibold">
                    Catalogo fijo: basico, profesional, premium y sucursales. Solo editas precio, descuento y estado.
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($plans as $plan)
                    @php
                        $planKey = (string) ($plan->plan_key ?? '');
                        $meta = (array) ($planPresentation[$planKey] ?? []);
                        $features = array_values(array_filter((array) ($meta['features'] ?? []), fn ($value) => is_string($value) && trim($value) !== ''));
                        $summary = (string) ($meta['summary'] ?? 'Plan base disponible para tu operacion.');
                        $isFeatured = (bool) ($meta['featured'] ?? false);
                        $isContactMode = (bool) ($meta['contact_mode'] ?? false);
                        $price = (float) ($plan->price ?? 0);
                        $discountPrice = $plan->discount_price !== null ? (float) $plan->discount_price : null;
                        $discountPercent = ($discountPrice !== null && $price > 0 && $discountPrice < $price)
                            ? (int) round((($price - $discountPrice) / $price) * 100)
                            : null;
                        $isActive = (string) ($plan->status ?? '') === 'active';
                    @endphp

                    <article class="plan-admin-card relative overflow-hidden rounded-2xl border p-4">
                        @if ($isFeatured)
                            <span class="plan-admin-badge">Plan destacado</span>
                        @endif

                        <div class="mb-3">
                            <p class="text-lg font-black text-slate-900 dark:text-slate-100">{{ $plan->name }}</p>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300">{{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }}</p>
                        </div>

                        @if ($isContactMode)
                            <div class="mb-2 text-3xl font-black leading-none text-slate-900 dark:text-slate-100">Personalizado <span class="text-base font-bold text-slate-700 dark:text-slate-300">/Contacto</span></div>
                            <p class="text-sm text-slate-700 dark:text-slate-200">
                                Primer mes con oferta:
                                @if ($discountPercent !== null)
                                    <strong>{{ $discountPercent }}% menos</strong> sobre el valor cotizado.
                                @elseif ($discountPrice !== null)
                                    <strong>${{ $money($discountPrice) }}</strong> de referencia.
                                @else
                                    <strong>según cotización</strong>.
                                @endif
                            </p>
                        @else
                            <div class="mb-2 text-5xl font-black leading-none text-slate-900 dark:text-slate-100">${{ $money($price) }}<span class="ml-1 text-2xl font-bold text-slate-700 dark:text-slate-300">/Mes</span></div>
                            <p class="text-sm text-slate-700 dark:text-slate-200">
                                Primer mes con oferta:
                                @if ($discountPrice !== null && $discountPrice < $price)
                                    <span class="line-through opacity-70">${{ $money($price) }}</span>
                                    <strong>${{ $money($discountPrice) }}</strong>
                                @else
                                    <strong>sin oferta configurada</strong>
                                @endif
                            </p>
                        @endif

                        <p class="mt-3 text-sm text-slate-700 dark:text-slate-200">{{ $summary }}</p>
                        <ul class="mt-3 grid gap-1.5 text-sm text-slate-700 dark:text-slate-200">
                            @foreach ($features as $feature)
                                <li class="flex items-start gap-2">
                                    <span class="mt-1.5 inline-block h-2 w-2 shrink-0 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.pricing.update', $plan->id) : '#' }}" class="mt-4 grid gap-2">
                            @csrf
                            @method('PATCH')

                            <div class="grid gap-2 sm:grid-cols-2">
                                <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300">
                                    Precio
                                    <input type="number" step="0.01" min="0" name="price" class="ui-input" value="{{ number_format((float) $plan->price, 2, '.', '') }}" required>
                                </label>
                                <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300">
                                    Precio con descuento
                                    <input type="number" step="0.01" min="0" name="discount_price" class="ui-input" value="{{ $plan->discount_price !== null ? number_format((float) $plan->discount_price, 2, '.', '') : '' }}">
                                </label>
                            </div>

                            <div class="grid gap-2 sm:grid-cols-[1fr_auto] sm:items-end">
                                <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300">
                                    Estado
                                    <select name="status" class="ui-input">
                                        <option value="active" @selected($isActive)>Activo</option>
                                        <option value="inactive" @selected(! $isActive)>Inactivo</option>
                                    </select>
                                </label>
                                <x-ui.button type="submit" size="sm" class="sm:min-w-[140px]" :disabled="!$schemaReady">Guardar</x-ui.button>
                            </div>
                        </form>
                    </article>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Promociones base" subtitle="Promociones globales para aplicar reglas comerciales por plan.">
            <form method="POST" action="{{ route('superadmin.plan-templates.promotions.store') }}" class="grid gap-3 lg:grid-cols-4">
                @csrf
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Nombre promocion
                    <input type="text" name="name" class="ui-input" placeholder="Ej: Trae un gym amigo" required>
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
                    <input type="number" step="0.01" min="0" name="value" class="ui-input" placeholder="25">
                </label>
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                    Descripcion
                    <input type="text" name="description" class="ui-input" placeholder="Ej: 25% por 4 meses para gimnasios referidos">
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
                <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Duracion (meses)
                    <input type="number" min="1" max="60" name="duration_months" class="ui-input" placeholder="Ej: 4">
                </label>
                <div class="flex items-end lg:col-span-4">
                    <x-ui.button type="submit">Crear promocion base</x-ui.button>
                </div>
            </form>

            <div class="mt-4 overflow-x-auto">
                <table class="ui-table min-w-[1080px]">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Promocion</th>
                            <th class="px-3 py-3">Plan</th>
                            <th class="px-3 py-3">Tipo</th>
                            <th class="px-3 py-3">Valor</th>
                            <th class="px-3 py-3">Vigencia</th>
                            <th class="px-3 py-3">Duracion</th>
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
                                <td class="px-3 py-3 dark:text-slate-200">{{ $promotion->duration_months ? $promotion->duration_months.' meses' : '-' }}</td>
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
                                <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">No hay promociones base creadas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('styles')
<style>
    .plan-admin-card {
        border-color: rgb(148 163 184 / 0.45);
        background:
            radial-gradient(circle at 90% 4%, rgba(34, 197, 94, 0.16), transparent 32%),
            linear-gradient(170deg, rgba(255,255,255,.96) 0%, rgba(241,245,249,.96) 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    .theme-dark .plan-admin-card {
        border-color: rgb(51 65 85 / 0.9);
        background:
            radial-gradient(circle at 90% 4%, rgba(74, 222, 128, 0.2), transparent 34%),
            linear-gradient(165deg, rgba(2,6,23,.95) 0%, rgba(15,23,42,.96) 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.05), 0 16px 34px rgba(2, 6, 23, 0.45);
    }
    .plan-admin-badge {
        position: absolute;
        right: 0.9rem;
        top: 0.9rem;
        border-radius: 9999px;
        border: 1px solid rgba(34, 197, 94, 0.52);
        background: linear-gradient(120deg, #22c55e, #16a34a);
        color: #ecfdf3;
        padding: 0.22rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .theme-light .plan-admin-badge {
        color: #052e16;
    }
</style>
@endpush
