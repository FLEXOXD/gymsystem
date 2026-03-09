@extends('layouts.panel')

@section('title', 'SuperAdmin Planes')
@section('page-title', 'Planes base y promociones')

@section('content')
    @php
        $planPresentation = is_array($planPresentation ?? null) ? $planPresentation : [];
        $schemaReady = (bool) ($schemaReady ?? true);
        $money = static fn (float $amount): string => number_format($amount, 2, ',', '.');
        $activePlans = $plans->filter(fn ($plan) => (string) ($plan->status ?? '') === 'active')->count();
        $discountPlans = $plans->filter(fn ($plan) => $plan->discount_price !== null && (float) $plan->discount_price < (float) $plan->price)->count();
        $contactPlans = $plans->filter(fn ($plan) => (bool) data_get($planPresentation, (string) ($plan->plan_key ?? '').'.contact_mode', false))->count();
        $featuredPlans = $plans->filter(fn ($plan) => (bool) data_get($planPresentation, (string) ($plan->plan_key ?? '').'.featured', false))->count();
        $activePromotions = $promotions->filter(fn ($promotion) => (string) ($promotion->status ?? '') === 'active')->count();
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Catálogo comercial</span>
                    <h2 class="sa-title">Controla pricing, descuentos y promociones desde una sola interfaz.</h2>
                    <p class="sa-subtitle">
                        Esta página ahora separa claramente el catálogo fijo, los planes sincronizados con la landing
                        y las reglas promocionales. El objetivo es que el equipo comercial entienda impacto antes de editar.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.web-page.edit')">Editar landing</x-ui.button>
                        <x-ui.button :href="route('landing', ['preview_guest' => 1])" variant="secondary">Ver página pública</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Reglas clave</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Catálogo fijo</strong>
                            <span>Los nombres base siguen bloqueados. Aquí se edita precio, descuento y estado.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Landing sincronizada</strong>
                            <span>Cada cambio en pricing impacta la página pública de planes activos.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Promociones separadas</strong>
                            <span>Las reglas comerciales viven aparte para no mezclar pricing base con campañas.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Planes base</p>
                <p class="sa-stat-value">{{ $plans->count() }}</p>
                <p class="sa-stat-meta">Catálogo operativo disponible para ventas y altas nuevas.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value">{{ $activePlans }}</p>
                <p class="sa-stat-meta">Planes visibles para nuevas suscripciones y landing.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Con descuento</p>
                <p class="sa-stat-value">{{ $discountPlans }}</p>
                <p class="sa-stat-meta">Planes que hoy comunican una oferta comercial.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Cotización guiada</p>
                <p class="sa-stat-value">{{ $contactPlans }}</p>
                <p class="sa-stat-meta">Planes que no muestran precio cerrado y requieren contacto.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Promociones activas</p>
                <p class="sa-stat-value">{{ $activePromotions }}</p>
                <p class="sa-stat-meta">Reglas comerciales actualmente disponibles para ventas.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Plan destacado</p>
                <p class="sa-stat-value">{{ $featuredPlans }}</p>
                <p class="sa-stat-meta">Planes con prioridad visual para empuje comercial.</p>
            </article>
        </section>

        <x-ui.card title="Planes base conectados con la landing" subtitle="Cada tarjeta muestra posicionamiento, foco operativo y configuración comercial.">
            @if (! $schemaReady)
                <div class="ui-alert ui-alert-danger mb-4 text-sm font-semibold">
                    Falta la migración del catálogo base. Ejecuta <code>php artisan migrate</code> antes de editar precios.
                </div>
            @else
                <div class="ui-alert ui-alert-success mb-4 text-sm font-semibold">
                    Catálogo estable: básico, profesional, premium y sucursales. Edita precio, descuento y estado.
                </div>
            @endif

            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.45fr)_minmax(280px,0.8fr)]">
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ($plans as $plan)
                        @php
                            $planKey = (string) ($plan->plan_key ?? '');
                            $meta = (array) ($planPresentation[$planKey] ?? []);
                            $features = array_values(array_filter((array) ($meta['features'] ?? []), fn ($value) => is_string($value) && trim($value) !== ''));
                            $summary = (string) ($meta['summary'] ?? 'Plan base disponible para tu operación.');
                            $isFeatured = (bool) ($meta['featured'] ?? false);
                            $isContactMode = (bool) ($meta['contact_mode'] ?? false);
                            $idealFor = (string) ($meta['ideal_for'] ?? 'Operación en crecimiento.');
                            $opsFocus = (string) ($meta['ops_focus'] ?? 'Control operativo.');
                            $setupNote = (string) ($meta['setup_note'] ?? 'Configuración según necesidad.');
                            $price = (float) ($plan->price ?? 0);
                            $discountPrice = $plan->discount_price !== null ? (float) $plan->discount_price : null;
                            $discountPercent = ($discountPrice !== null && $price > 0 && $discountPrice < $price)
                                ? (int) round((($price - $discountPrice) / $price) * 100)
                                : null;
                            $isActive = (string) ($plan->status ?? '') === 'active';
                        @endphp

                        <article class="plan-admin-card relative overflow-hidden rounded-[1.5rem] border p-5">
                            <div class="mb-4 flex flex-wrap items-center gap-2">
                                <span class="sa-pill {{ $isActive ? 'is-success' : 'is-neutral' }}">
                                    {{ $isActive ? 'Activo' : 'Inactivo' }}
                                </span>
                                @if ($isFeatured)
                                    <span class="plan-admin-badge">Plan destacado</span>
                                @endif
                                @if ($isContactMode)
                                    <span class="sa-pill is-info">Cotización guiada</span>
                                @endif
                            </div>

                            <div>
                                <p class="text-xl font-black text-slate-950 dark:text-white">{{ $plan->name }}</p>
                                <p class="mt-1 text-xs font-bold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-300">
                                    {{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }}
                                </p>
                            </div>

                            @if ($isContactMode)
                                <div class="mt-4 text-4xl font-black leading-none text-slate-950 dark:text-white">
                                    Personalizado <span class="text-base font-bold text-slate-600 dark:text-slate-300">/Contacto</span>
                                </div>
                            @else
                                <div class="mt-4 text-5xl font-black leading-none text-slate-950 dark:text-white">
                                    ${{ $money($price) }}<span class="ml-1 text-xl font-bold text-slate-600 dark:text-slate-300">/Mes</span>
                                </div>
                            @endif

                            <div class="mt-3 rounded-2xl border border-emerald-200/70 bg-emerald-50/80 px-3 py-3 text-sm dark:border-emerald-700/40 dark:bg-emerald-900/20">
                                <p class="font-semibold text-emerald-900 dark:text-emerald-100">Primer mes con oferta</p>
                                <p class="mt-1 text-emerald-800 dark:text-emerald-200">
                                    @if ($discountPercent !== null)
                                        {{ $discountPercent }}% menos sobre el precio base.
                                    @elseif ($discountPrice !== null)
                                        Valor promocional: ${{ $money($discountPrice) }}.
                                    @else
                                        Sin oferta configurada.
                                    @endif
                                </p>
                            </div>

                            <p class="mt-4 text-sm leading-6 text-slate-700 dark:text-slate-200">{{ $summary }}</p>

                            <div class="mt-4 grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-3 dark:border-slate-800 dark:bg-slate-950/60">
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Ideal para</p>
                                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">{{ $idealFor }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-3 dark:border-slate-800 dark:bg-slate-950/60">
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Foco operativo</p>
                                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">{{ $opsFocus }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/80 p-3 dark:border-slate-800 dark:bg-slate-950/60">
                                    <p class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Implementación</p>
                                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">{{ $setupNote }}</p>
                                </div>
                            </div>

                            <ul class="mt-4 grid gap-2 text-sm text-slate-700 dark:text-slate-200">
                                @foreach ($features as $feature)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-1.5 inline-block h-2 w-2 shrink-0 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.pricing.update', $plan->id) : '#' }}" class="mt-5 grid gap-3">
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Precio base
                                        <input type="number" step="0.01" min="0" name="price" class="ui-input" value="{{ number_format((float) $plan->price, 2, '.', '') }}" required>
                                    </label>
                                    <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Precio con descuento
                                        <input type="number" step="0.01" min="0" name="discount_price" class="ui-input" value="{{ $plan->discount_price !== null ? number_format((float) $plan->discount_price, 2, '.', '') : '' }}">
                                    </label>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                                    <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                        Estado comercial
                                        <select name="status" class="ui-input">
                                            <option value="active" @selected($isActive)>Activo</option>
                                            <option value="inactive" @selected(! $isActive)>Inactivo</option>
                                        </select>
                                    </label>
                                    <x-ui.button type="submit" size="sm" class="sm:min-w-[160px]" :disabled="! $schemaReady">Guardar cambios</x-ui.button>
                                </div>
                            </form>
                        </article>
                    @endforeach
                </div>

                <aside class="space-y-4">
                    <x-ui.card title="Checklist comercial" subtitle="Que validar antes de publicar cambios al catálogo.">
                        <ul class="sa-check-list">
                            <li>Confirma que el precio y descuento cuentan la misma historia que la landing.</li>
                            <li>Evita activar demasiados descuentos simultaneos para no diluir percepcion de valor.</li>
                            <li>Usa el plan destacado solo para el plan que quieres empujar esta semana.</li>
                            <li>Si un plan depende de cotización, manten claro el CTA comercial y el flujo de contacto.</li>
                        </ul>
                    </x-ui.card>

                    <x-ui.card title="Impacto en UX" subtitle="Motivos del rediseño aplicado en esta página.">
                        <div class="sa-mini-grid md:grid-cols-1">
                            <article class="sa-mini-card">
                                <strong>Más contexto</strong>
                                <span>Cada plan ahora muestra posicionamiento, foco operativo y nota de implementación.</span>
                            </article>
                            <article class="sa-mini-card">
                                <strong>Menos ambiguedad</strong>
                                <span>El estado, descuento y tipo de venta quedan visibles antes de editar formularios.</span>
                            </article>
                            <article class="sa-mini-card">
                                <strong>Mejor lectura</strong>
                                <span>Se redujo el salto visual entre tarjetas, promos y reglas del catálogo.</span>
                            </article>
                        </div>
                    </x-ui.card>
                </aside>
            </div>
        </x-ui.card>
        <x-ui.card title="Promociones base" subtitle="Las promociones ahora se crean con mejor contexto y se filtran sin recorrer toda la tabla.">
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
                <form method="POST" action="{{ route('superadmin.plan-templates.promotions.store') }}" class="grid gap-3">
                    @csrf
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Nombre promoción
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
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Tipo
                            <select name="type" class="ui-input">
                                <option value="percentage">Porcentaje</option>
                                <option value="fixed">Monto fijo</option>
                                <option value="final_price">Precio final</option>
                                <option value="bonus_days">Días extra</option>
                                <option value="two_for_one">2x1</option>
                                <option value="bring_friend">Trae un amigo</option>
                            </select>
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Valor
                            <input type="number" step="0.01" min="0" name="value" class="ui-input" placeholder="25">
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Estado
                            <select name="status" class="ui-input">
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                        </label>
                    </div>

                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Descripción
                        <input type="text" name="description" class="ui-input" placeholder="Ej: 25% por 4 meses para gimnasios referidos">
                    </label>

                    <div class="grid gap-3 md:grid-cols-4">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Inicio
                            <input type="date" name="starts_at" class="ui-input">
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Fin
                            <input type="date" name="ends_at" class="ui-input">
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Máximo usos
                            <input type="number" min="1" name="max_uses" class="ui-input" placeholder="Opcional">
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Duracion (meses)
                            <input type="number" min="1" max="60" name="duration_months" class="ui-input" placeholder="Ej: 4">
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <x-ui.button type="submit">Crear promoción base</x-ui.button>
                    </div>
                </form>

                <div class="space-y-4">
                    <div class="sa-mini-card">
                        <strong>{{ $activePromotions }} promociones activas</strong>
                        <span>Visualiza rápido si el catálogo está muy cargado de campañas o si necesitas activar una nueva.</span>
                    </div>
                    <div class="sa-mini-card">
                        <strong>Buenas practicas aplicadas</strong>
                        <span>Se separó la creación del listado, se agregó filtro visual y se redujo ruido en la tabla.</span>
                    </div>
                    <div class="sa-mini-card">
                        <strong>Tip comercial</strong>
                        <span>Prefiere promociones con objetivo claro: captación, permanencia o upgrade de plan.</span>
                    </div>
                </div>
            </div>

            <div class="sa-toolbar mt-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid flex-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Buscar promoción
                            <span class="sa-search">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <input id="promotion-search" type="text" placeholder="Nombre, plan, tipo o descripción">
                            </span>
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Estado
                            <select id="promotion-status-filter" class="ui-input">
                                <option value="all">Todas</option>
                                <option value="active">Activas</option>
                                <option value="inactive">Inactivas</option>
                            </select>
                        </label>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                        <span class="sa-pill is-info" role="status" aria-live="polite">Visibles: <strong id="promotion-visible-count">{{ $promotions->count() }}</strong></span>
                        <button type="button" id="promotion-clear" class="ui-button ui-button-ghost">Limpiar filtros</button>
                    </div>
                </div>
                <p id="promotion-filter-help" class="sa-filter-note mt-3">
                    Usa estos filtros para revisar saturación comercial y encontrar promociones activas sin recorrer toda la tabla.
                </p>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="ui-table min-w-[1080px]" aria-describedby="promotion-filter-help promotion-table-help">
                    <caption id="promotion-table-help" class="sr-only">
                        Tabla de promociones base con filtros por texto y estado.
                    </caption>
                    <thead>
                        <tr>
                            <th>Promoción</th>
                            <th>Plan</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Vigencia</th>
                            <th>Duracion</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $promotion)
                            @php
                                $isActive = (string) ($promotion->status ?? '') === 'active';
                                $valueLabel = $promotion->value !== null ? (string) $promotion->value : '-';
                                $rangeLabel = ($promotion->starts_at ? $promotion->starts_at->toDateString() : '-') . ' / ' . ($promotion->ends_at ? $promotion->ends_at->toDateString() : '-');
                                $typeLabel = match ((string) $promotion->type) {
                                    'percentage' => 'Porcentaje',
                                    'fixed' => 'Monto fijo',
                                    'final_price' => 'Precio final',
                                    'bonus_days' => 'Días extra',
                                    'two_for_one' => '2x1',
                                    'bring_friend' => 'Trae un amigo',
                                    default => (string) $promotion->type,
                                };
                                $promotionSearch = strtolower(trim(implode(' ', array_filter([
                                    $promotion->name,
                                    $promotion->description,
                                    $promotion->planTemplate?->name,
                                    $promotion->type,
                                ]))));
                            @endphp
                            <tr data-promotion-row data-promotion-status="{{ $isActive ? 'active' : 'inactive' }}" data-promotion-search="{{ $promotionSearch }}">
                                <td>
                                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $promotion->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $promotion->description ?: '-' }}</p>
                                </td>
                                <td class="dark:text-slate-200">{{ $promotion->planTemplate?->name ?? 'Todos' }}</td>
                                <td class="dark:text-slate-200">{{ $typeLabel }}</td>
                                <td class="dark:text-slate-200">{{ $valueLabel }}</td>
                                <td class="dark:text-slate-200">{{ $rangeLabel }}</td>
                                <td class="dark:text-slate-200">{{ $promotion->duration_months ? $promotion->duration_months.' meses' : '-' }}</td>
                                <td>
                                    <span class="sa-pill {{ $isActive ? 'is-success' : 'is-neutral' }}">
                                        {{ $isActive ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('superadmin.plan-templates.promotions.toggle', $promotion->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $isActive ? 'inactive' : 'active' }}">
                                            <x-ui.button type="submit" size="sm" variant="ghost">{{ $isActive ? 'Desactivar' : 'Activar' }}</x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.plan-templates.promotions.destroy', $promotion->id) }}" onsubmit="return confirm('Esta acción eliminará la promoción base. ¿Deseas continuar?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="danger">Eliminar</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="sa-empty-row">No hay promociones base creadas.</td>
                            </tr>
                        @endforelse
                        @if ($promotions->isNotEmpty())
                            <tr id="promotion-empty-state" class="hidden">
                                <td colspan="8" class="sa-empty-row">
                                    No se encontraron promociones con ese criterio.
                                </td>
                            </tr>
                        @endif
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
            linear-gradient(170deg, rgba(255, 255, 255, .96) 0%, rgba(241, 245, 249, .96) 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9), 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .theme-dark .plan-admin-card {
        border-color: rgb(51 65 85 / 0.9);
        background:
            radial-gradient(circle at 90% 4%, rgba(74, 222, 128, 0.2), transparent 34%),
            linear-gradient(165deg, rgba(2, 6, 23, .95) 0%, rgba(15, 23, 42, .96) 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05), 0 16px 34px rgba(2, 6, 23, 0.45);
    }

    .plan-admin-badge {
        border-radius: 9999px;
        border: 1px solid rgba(34, 197, 94, 0.52);
        background: linear-gradient(120deg, #22c55e, #16a34a);
        color: #052e16;
        padding: 0.22rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('promotion-search');
        const statusFilter = document.getElementById('promotion-status-filter');
        const visibleCount = document.getElementById('promotion-visible-count');
        const clearButton = document.getElementById('promotion-clear');
        const emptyState = document.getElementById('promotion-empty-state');
        const rows = Array.from(document.querySelectorAll('[data-promotion-row]'));

        const normalizeText = function (value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        };

        const applyFilter = function () {
            const term = normalizeText(searchInput?.value || '');
            const status = String(statusFilter?.value || 'all').trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                const searchValue = normalizeText(row.getAttribute('data-promotion-search') || '');
                const rowStatus = String(row.getAttribute('data-promotion-status') || '').toLowerCase();
                const matchesSearch = term === '' || searchValue.includes(term);
                const matchesStatus = status === 'all' || rowStatus === status;
                const matches = matchesSearch && matchesStatus;

                row.classList.toggle('hidden', !matches);
                if (matches) {
                    visible += 1;
                }
            });

            if (visibleCount) {
                visibleCount.textContent = String(visible);
            }

            if (emptyState) {
                emptyState.classList.toggle('hidden', visible !== 0);
            }
        };

        searchInput?.addEventListener('input', applyFilter);
        statusFilter?.addEventListener('change', applyFilter);
        clearButton?.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
            }
            if (statusFilter) {
                statusFilter.value = 'all';
            }
            applyFilter();
            searchInput?.focus();
        });
        applyFilter();
    })();
</script>
@endpush
