@extends('layouts.panel')

@section('title', 'SuperAdmin Planes')
@section('page-title', 'Planes base y promociones')

@section('content')
    @php
        $planPresentation = is_array($planPresentation ?? null) ? $planPresentation : [];
        $plans = $plans ?? collect();
        $basePlans = $basePlans ?? $plans->filter(fn ($plan) => in_array((string) ($plan->plan_key ?? ''), \App\Support\SuperAdminPlanCatalog::keys(), true))->values();
        $internalPlans = $internalPlans ?? collect();
        $promotionPlans = ($promotionPlans ?? collect())->values();
        if ($promotionPlans->isEmpty()) {
            $promotionPlans = $basePlans->where('status', 'active')->values();
        }
        $basePlansById = $basePlans->keyBy(fn ($plan) => (int) $plan->id);
        $promotionPlanLookup = $basePlans->concat($internalPlans)->keyBy(fn ($plan) => (int) $plan->id);
        $schemaReady = (bool) ($schemaReady ?? true);
        $activeBasePlans = $basePlans->where('status', 'active')->count();
        $activePromotions = $promotions->where('status', 'active')->count();
        $plansWithPromotions = $promotions->where('status', 'active')->pluck('plan_template_id')->filter()->unique()->count();
        $inactivePromotions = $promotions->where('status', 'inactive')->count();
        $durationPromotions = $promotions->filter(fn ($promotion) => $promotion->duration_months !== null)->count();
        $activePromotionsByPlanId = $promotions
            ->where('status', 'active')
            ->sortByDesc('id')
            ->groupBy(fn ($promotion) => (int) ($promotion->plan_template_id ?? 0))
            ->map(fn ($group) => $group->first());
        $promotionPreviewPlans = $promotionPlans
            ->mapWithKeys(function ($plan): array {
                return [
                    (int) $plan->id => [
                        'id' => (int) $plan->id,
                        'name' => (string) $plan->name,
                        'price' => (float) $plan->price,
                        'discount_price' => $plan->discount_price !== null ? (float) $plan->discount_price : null,
                        'offer_text' => trim((string) ($plan->offer_text ?? '')) !== '' ? (string) $plan->offer_text : null,
                        'duration_label' => \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months),
                    ],
                ];
            })
            ->all();
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Catalogo comercial</span>
                    <h2 class="sa-title">Mantén tus planes base y asigna promociones solo cuando quieras.</h2>
                    <p class="sa-subtitle">
                        Los 4 planes base se mantienen fijos. Puedes poner texto de oferta directo en cada plan base o crear una promocion abajo para que la landing y el cobro se recalculen automaticamente.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.web-page.edit')">Editar landing</x-ui.button>
                        <x-ui.button :href="route('landing', ['preview_guest' => 1])" variant="secondary">Ver pagina publica</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Resumen</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Base</strong>
                            <span>{{ $basePlans->count() }} slots funcionales conectados con la landing.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Planes base</strong>
                            <span>{{ $activeBasePlans }} activos con precio base editable.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Oferta</strong>
                            <span>La landing prioriza una promocion activa; si no hay, usa el texto de oferta que pongas en el plan base.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Planes base</p>
                <p class="sa-stat-value">{{ $basePlans->count() }}</p>
                <p class="sa-stat-meta">Slots fijos de funciones para la landing.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Con promo</p>
                <p class="sa-stat-value">{{ $plansWithPromotions }}</p>
                <p class="sa-stat-meta">Planes base con promoción activa.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Promos activas</p>
                <p class="sa-stat-value">{{ $activePromotions }}</p>
                <p class="sa-stat-meta">Promociones aplicándose hoy.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Base activos</p>
                <p class="sa-stat-value">{{ $activeBasePlans }}</p>
                <p class="sa-stat-meta">Slots base disponibles para vender.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Por plazo</p>
                <p class="sa-stat-value">{{ $durationPromotions }}</p>
                <p class="sa-stat-meta">Promociones con meses definidos.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Sin promo</p>
                <p class="sa-stat-value">{{ max(0, $basePlans->count() - $plansWithPromotions) }}</p>
                <p class="sa-stat-meta">Planes base sin oferta asignada.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Inactivas</p>
                <p class="sa-stat-value">{{ $inactivePromotions }}</p>
                <p class="sa-stat-meta">Promociones guardadas pero apagadas.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Total promos</p>
                <p class="sa-stat-value">{{ $promotions->count() }}</p>
                <p class="sa-stat-meta">Historial de promociones base.</p>
            </article>
        </section>

        <x-ui.card title="Planes base conectados con la landing" subtitle="Aqui mantienes el precio base de cada plan y ves si tiene una promoción activa.">
            @if (! $schemaReady)
                <div class="ui-alert ui-alert-danger mb-4 text-sm font-semibold">
                    Falta la migracion del catalogo comercial. Ejecuta <code>php artisan migrate</code> antes de editar esta seccion.
                </div>
            @endif

            <div class="grid gap-4 lg:grid-cols-2">
                @foreach ($basePlans as $plan)
                    @php
                        $planKey = (string) ($plan->plan_key ?? '');
                        $meta = (array) ($planPresentation[$planKey] ?? []);
                        $isActive = (string) ($plan->status ?? '') === 'active';
                        $activePromotion = $activePromotionsByPlanId->get((int) $plan->id);
                    @endphp
                    <article class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="sa-pill {{ $isActive ? 'is-success' : 'is-neutral' }}">{{ $isActive ? 'Activo' : 'Inactivo' }}</span>
                            <span class="sa-pill is-info">Base funcional</span>
                        </div>

                        <h3 class="mt-4 text-xl font-black text-slate-900 dark:text-slate-100">{{ $plan->name }}</h3>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ (string) ($meta['summary'] ?? 'Plan base disponible para tu operacion.') }}</p>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Duracion</p>
                                <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Precio base</p>
                                <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ \App\Support\Currency::format((float) $plan->price, $appCurrencyCode ?? 'USD') }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.pricing.update', $plan->id) : '#' }}" class="mt-5 grid gap-3">
                            @csrf
                            @method('PATCH')

                            <div class="rounded-[1.2rem] border border-dashed border-slate-300/80 bg-slate-50/70 p-4 text-xs leading-6 text-slate-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                                @if ($activePromotion)
                                    Promocion activa: <strong>{{ $activePromotion->name }}</strong>.
                                    {{ trim((string) ($activePromotion->description ?? '')) !== '' ? $activePromotion->description : 'La landing y el cobro mostraran esta oferta automaticamente cuando aplique.' }}
                                @elseif (trim((string) ($plan->offer_text ?? '')) !== '')
                                    Oferta manual activa: <strong>{{ $plan->offer_text }}</strong>.
                                    Este texto se mostrara en la landing hasta que actives una promocion para este plan.
                                @else
                                    Este plan base no tiene promocion asignada. La landing mostrara solo el precio base normal.
                                @endif
                            </div>

                            <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Precio base mensual
                                <input type="number" step="0.01" min="0" name="price" class="ui-input" value="{{ number_format((float) $plan->price, 2, '.', '') }}" required>
                            </label>

                            <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Texto oferta (landing)
                                <input type="text"
                                       name="offer_text"
                                       maxlength="255"
                                       class="ui-input"
                                       value="{{ trim((string) ($plan->offer_text ?? '')) }}"
                                       placeholder="Ej: 30% de descuento por lanzamiento">
                            </label>

                            <div class="grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
                                <label class="space-y-1 text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                    Estado
                                    <select name="status" class="ui-input">
                                        <option value="active" @selected($isActive)>Activo</option>
                                        <option value="inactive" @selected(! $isActive)>Inactivo</option>
                                    </select>
                                </label>
                                <x-ui.button type="submit" size="sm" :disabled="! $schemaReady">Guardar cambios</x-ui.button>
                            </div>
                        </form>
                    </article>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="Promociones comerciales" subtitle="Aqui solo creas promociones. Luego las aplicas donde quieras al renovar.">
            <div class="grid gap-5 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                <section class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                    <div class="flex flex-col gap-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">Crear promocion</p>
                        <h3 class="text-lg font-black text-slate-900 dark:text-slate-50">Nueva promocion comercial</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Crea la promocion una sola vez. En renovaciones la combinas con cualquier plan base.</p>
                    </div>

                    <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.promotions.store') : '#' }}" class="mt-4 grid gap-3">
                        @csrf
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Nombre promocion
                            <input type="text" name="name" class="ui-input" placeholder="Ej: Promo Control 30%" required>
                        </label>

                        <div class="grid gap-3 md:grid-cols-[180px_minmax(0,1fr)_minmax(0,1fr)] md:items-end">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Unidad duracion
                                <select name="duration_unit" id="promotion-duration-unit" class="ui-input">
                                    <option value="months" @selected((string) old('duration_unit', 'months') === 'months')>Meses</option>
                                    <option value="days" @selected((string) old('duration_unit') === 'days')>Dias</option>
                                </select>
                            </label>
                            <label id="promotion-duration-months-wrap" class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Duracion (meses)
                                <input type="number"
                                       min="1"
                                       max="60"
                                       name="duration_months"
                                       id="promotion-duration-months"
                                       class="ui-input"
                                       value="{{ old('duration_months', 1) }}">
                            </label>
                            <label id="promotion-duration-days-wrap" class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Duracion (dias)
                                <input type="number"
                                       min="1"
                                       max="365"
                                       name="duration_days"
                                       id="promotion-duration-days"
                                       class="ui-input"
                                       value="{{ old('duration_days', 5) }}">
                            </label>
                        </div>

                        <div class="rounded-[1rem] border border-dashed border-slate-300/70 bg-slate-50/70 p-3 text-xs leading-5 text-slate-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                            Si quieres una prueba de 5 dias, elige <strong>Dias</strong> + <strong>5</strong>.
                            Esta promocion queda disponible para combinarla con cualquier plan base desde "Gimnasios y Suscripciones".
                        </div>

                        <div class="grid gap-3 md:grid-cols-[220px_minmax(0,1fr)]">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Tipo
                                <select name="type" class="ui-input">
                                    <option value="percentage">Porcentaje</option>
                                    <option value="fixed">Monto fijo</option>
                                    <option value="final_price">Precio final</option>
                                </select>
                            </label>
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Valor
                                <input type="number" step="0.01" min="0" name="value" class="ui-input" placeholder="Ej: 30 o 19" required>
                            </label>
                        </div>

                        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Texto visible de oferta
                                <input type="text" name="description" class="ui-input" placeholder="Ej: Primer mes gratis o 30% de descuento">
                            </label>
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Estado
                                <select name="status" class="ui-input">
                                    <option value="active">Activa</option>
                                    <option value="inactive">Inactiva</option>
                                </select>
                            </label>
                        </div>

                        <div class="rounded-[1.2rem] border border-dashed border-slate-300/80 bg-slate-50/70 p-4 text-xs leading-6 text-slate-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                            Ejemplos:
                            "30% de descuento" = tipo <strong>Porcentaje</strong> + valor <strong>30</strong>.
                            "Prueba 5 dias sin costo" = unidad <strong>Dias</strong> + duracion <strong>5</strong> + tipo <strong>Precio final</strong> + valor <strong>0</strong>.
                        </div>

                        <div class="flex justify-end">
                            <x-ui.button type="submit" :disabled="! $schemaReady">Crear promoción</x-ui.button>
                        </div>
                    </form>
                </section>

                <section class="space-y-4">
                    @forelse ($promotions as $promotion)
                        @php
                            $promotionStatus = (string) ($promotion->status ?? '') === 'active';
                            $linkedBasePlan = $basePlansById->get((int) ($promotion->plan_template_id ?? 0));
                            $typeLabel = match ((string) $promotion->type) {
                                'percentage' => 'Porcentaje',
                                'fixed' => 'Monto fijo',
                                'final_price' => 'Precio final',
                                'bonus_days' => 'Dias extra',
                                default => (string) $promotion->type,
                            };
                            $valueLabel = match ((string) $promotion->type) {
                                'percentage' => ($promotion->value !== null ? rtrim(rtrim(number_format((float) $promotion->value, 2, '.', ''), '0'), '.') : '0').'%',
                                'fixed', 'final_price' => $promotion->value !== null ? \App\Support\Currency::format((float) $promotion->value, $appCurrencyCode ?? 'USD') : '-',
                                default => $promotion->value !== null ? (string) $promotion->value : '-',
                            };
                            $promotionDurationLabel = method_exists($promotion, 'durationLabel')
                                ? $promotion->durationLabel()
                                : (((int) ($promotion->duration_months ?? 1)).' '.(((int) ($promotion->duration_months ?? 1)) === 1 ? 'mes' : 'meses'));
                        @endphp
                        <article class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="sa-pill {{ $promotionStatus ? 'is-success' : 'is-neutral' }}">{{ $promotionStatus ? 'Activa' : 'Inactiva' }}</span>
                                <span class="sa-pill is-info">{{ $linkedBasePlan?->name ?? 'Global' }}</span>
                            </div>

                            <h3 class="mt-4 text-lg font-black text-slate-900 dark:text-slate-50">{{ $promotion->name }}</h3>
                            <div class="mt-3 grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Tipo</p>
                                    <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $typeLabel }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Valor</p>
                                    <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $valueLabel }}</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/50">
                                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Duracion</p>
                                    <p class="mt-1 font-semibold text-slate-900 dark:text-slate-100">{{ $promotionDurationLabel }}</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                {{ trim((string) ($promotion->description ?? '')) !== '' ? $promotion->description : 'Sin texto visible configurado.' }}
                            </p>

                            <div class="mt-5 ui-action-grid">
                                <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.promotions.toggle', $promotion->id) : '#' }}" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $promotionStatus ? 'inactive' : 'active' }}">
                                    <x-ui.button type="submit" size="sm" :variant="$promotionStatus ? 'muted' : 'success'" class="ui-action-button" :disabled="! $schemaReady">
                                        <span class="ui-action-button-label">{{ $promotionStatus ? 'Desactivar' : 'Activar' }}</span>
                                    </x-ui.button>
                                </form>
                                <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.promotions.destroy', $promotion->id) : '#' }}" onsubmit="return confirm('Esta accion eliminara la promocion. Deseas continuar?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="danger" class="ui-action-button" :disabled="! $schemaReady">
                                        <span class="ui-action-button-label">Eliminar</span>
                                    </x-ui.button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="sa-empty-row rounded-[1.5rem] border border-dashed border-slate-300/80 bg-white/70 p-6 text-sm dark:border-slate-700 dark:bg-slate-950/40">
                            Aun no has creado promociones. Crea la primera aqui y luego aplicala al plan base que quieras en renovaciones.
                        </div>
                    @endforelse
                </section>
            </div>
        </x-ui.card>

        @if (false)
        <x-ui.card title="Motor comercial de promociones" subtitle="Organiza promociones directas y tambien ofertas mas compuestas, como mes gratis o cierres de 10 dias.">
            <div class="mb-5 rounded-[1.75rem] border border-amber-200/70 bg-amber-50/60 p-5 shadow-sm dark:border-amber-500/20 dark:bg-amber-500/5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-amber-700 dark:text-amber-200">Atajos comerciales</p>
                        <h3 class="mt-2 text-xl font-black text-slate-900 dark:text-slate-50">Piensa la promo como una oferta comercial, no solo como una formula.</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Si la oferta mezcla meses gratis, ventanas de cierre o precio especial en el segundo mes,
                            aqui la puedes representar como un total final del plazo completo y seguir viendola de forma profesional.
                        </p>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2 xl:w-[34rem]">
                        <button type="button" class="js-promotion-blueprint rounded-[1.35rem] border border-slate-200 bg-white/90 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950/60" data-promotion-blueprint="manual" aria-pressed="true">
                            <span class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Manual</span>
                            <strong class="mt-2 block text-sm text-slate-900 dark:text-slate-100">Promocion personalizada</strong>
                            <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Tu eliges el plazo, el tipo y el valor exacto.</span>
                        </button>
                        <button type="button" class="js-promotion-blueprint rounded-[1.35rem] border border-slate-200 bg-white/90 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950/60" data-promotion-blueprint="trial" aria-pressed="false">
                            <span class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Entrada</span>
                            <strong class="mt-2 block text-sm text-slate-900 dark:text-slate-100">Mes de prueba gratis</strong>
                            <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Ideal para quitar friccion y activar clientes nuevos.</span>
                        </button>
                        <button type="button" class="js-promotion-blueprint rounded-[1.35rem] border border-slate-200 bg-white/90 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950/60" data-promotion-blueprint="close_10_days" aria-pressed="false">
                            <span class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Cierre rapido</span>
                            <strong class="mt-2 block text-sm text-slate-900 dark:text-slate-100">10 dias: mes 1 gratis + mes 2 promo</strong>
                            <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Se guarda como un total cerrado de 2 meses y se apoya en tu precio con descuento.</span>
                        </button>
                        <button type="button" class="js-promotion-blueprint rounded-[1.35rem] border border-slate-200 bg-white/90 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950/60" data-promotion-blueprint="prepay_3" aria-pressed="false">
                            <span class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Ticket medio</span>
                            <strong class="mt-2 block text-sm text-slate-900 dark:text-slate-100">Pago adelantado 3 meses</strong>
                            <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">Empuja permanencia con un descuento claro por volumen.</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px]">
                <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.promotions.store') : '#' }}" class="grid gap-4">
                    @csrf

                    <section class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                        <div class="flex flex-col gap-1">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">1. Identidad y alcance</p>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-50">Que oferta vera el cliente</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">Ponle nombre comercial a la promo y ligala al plan comercial que la va a vender.</p>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Nombre promocion
                                <input type="text" name="name" class="ui-input" placeholder="Ej: Cierre 10 dias Plan Elite" required>
                            </label>
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Plan asociado
                                <select name="plan_template_id" class="ui-input js-promotion-plan-select" required>
                                    <option value="">Selecciona un plan comercial</option>
                                    @foreach ($promotionPlans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="mt-3 grid gap-3 md:grid-cols-[1fr_220px]">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Descripcion comercial
                                <input type="text" name="description" class="ui-input" placeholder="Ej: Si contrata antes de 10 dias, recibe el primer mes gratis y el segundo al precio promo">
                            </label>
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Estado
                                <select name="status" class="ui-input">
                                    <option value="active">Activa</option>
                                    <option value="inactive">Inactiva</option>
                                </select>
                            </label>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                        <div class="flex flex-col gap-1">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">2. Condicion de activacion</p>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-50">Cuando aplica la oferta</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">Define el plazo de compra, la ventana comercial y si quieres limitar usos.</p>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-4">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Duracion (meses)
                                <input type="number" min="1" max="60" name="duration_months" class="ui-input js-promotion-duration-input" placeholder="Ej: 2" required>
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
                                Maximo usos
                                <input type="number" min="1" name="max_uses" class="ui-input" placeholder="Opcional">
                            </label>
                        </div>
                    </section>

                    <section class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                        <div class="flex flex-col gap-1">
                            <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">3. Resultado economico</p>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-50">Como se traduce a cobro real</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300">Las promos compuestas se pueden guardar como "precio final" del plazo completo.</p>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-[220px_minmax(0,1fr)]">
                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                Tipo
                                <select name="type" class="ui-input js-promotion-type-select">
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
                                <input type="number" step="0.01" min="0" name="value" class="ui-input js-promotion-value-input" placeholder="25">
                                <span id="promotion-value-help" class="block text-[11px] normal-case tracking-normal text-slate-500 dark:text-slate-400">
                                    Si eliges porcentaje, escribe el % de descuento. Si eliges precio final, escribe el total que quieres cobrar.
                                </span>
                            </label>
                        </div>

                        <div class="mt-4 rounded-[1.2rem] border border-dashed border-slate-300/80 bg-slate-50/70 p-4 text-xs leading-6 text-slate-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                            Para la promocion que describiste:
                            usa <strong>duracion 2 meses</strong> + <strong>tipo Precio final</strong> +
                            <strong>valor = precio con descuento del plan</strong>.
                            Asi comercialmente queda como: <strong>mes 1 gratis + mes 2 al precio promo</strong>.
                        </div>
                    </section>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-slate-600 dark:text-slate-300">
                            Vista rapida: selecciona un atajo, luego ajusta el plan y el plazo. El panel derecho te muestra el cobro real.
                        </p>
                        <x-ui.button type="submit" :disabled="! $schemaReady">Crear promocion base</x-ui.button>
                    </div>
                </form>

                <div class="space-y-4">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Playbook activo</p>
                        <strong id="promotion-playbook-title" class="mt-3 block text-lg font-black text-slate-900 dark:text-slate-50">Promocion personalizada</strong>
                        <p id="promotion-playbook-copy" class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Ajusta manualmente el plazo, el tipo y el valor para construir cualquier oferta comercial.
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span id="promotion-playbook-meta" class="sa-pill is-info">Sin playbook automatico</span>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/60">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Vista previa comercial</p>
                        <div class="mt-4 space-y-3 text-sm text-slate-700 dark:text-slate-200">
                            <div class="flex items-center justify-between gap-4">
                                <span>Plan</span>
                                <strong id="promotion-preview-plan">Selecciona un plan</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Estrategia</span>
                                <strong id="promotion-preview-strategy">Promocion personalizada</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Precio mensual base</span>
                                <strong id="promotion-preview-base-monthly">$0.00</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Precio promo de referencia</span>
                                <strong id="promotion-preview-discount-monthly">Sin precio promo</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Subtotal normal</span>
                                <strong id="promotion-preview-base-total">$0.00</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Ajuste promo</span>
                                <strong id="promotion-preview-adjustment">Sin ajuste</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-800 dark:bg-slate-900/60">
                                <span>Total final</span>
                                <strong id="promotion-preview-final-total" class="text-base text-slate-900 dark:text-slate-50">$0.00</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Promedio por mes</span>
                                <strong id="promotion-preview-effective-monthly">$0.00</strong>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span>Ventana comercial</span>
                                <strong id="promotion-preview-window">Sin limite</strong>
                            </div>
                        </div>
                        <p id="promotion-preview-note" class="mt-4 text-xs leading-5 text-slate-500 dark:text-slate-400">
                            El sistema usa este mismo calculo cuando elijas un plan y un plazo en altas o renovaciones.
                        </p>
                    </div>

                    <div class="sa-mini-card">
                        <strong>{{ $activePromotions }} promociones activas</strong>
                        <span>Cada una se aplica automaticamente cuando coinciden el plan, la fecha y los meses elegidos.</span>
                    </div>
                    <div class="sa-mini-card">
                        <strong>Como cargar una promo compuesta</strong>
                        <span>Si quieres "mes 1 gratis + mes 2 con descuento", usa 2 meses y guarda el total final del combo.</span>
                    </div>
                </div>
            </div>

            <div class="sa-toolbar mt-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div class="grid flex-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px]">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Buscar promocion
                            <span class="sa-search">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <input id="promotion-search" type="text" placeholder="Nombre, plan, tipo o descripcion">
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
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="ui-table min-w-[1080px]">
                    <thead>
                        <tr>
                            <th>Promocion</th>
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
                                $linkedPlan = $promotionPlanLookup->get((int) ($promotion->plan_template_id ?? 0));
                                $valueLabel = match ((string) $promotion->type) {
                                    'percentage' => $promotion->value !== null ? rtrim(rtrim(number_format((float) $promotion->value, 2, '.', ''), '0'), '.').'%' : '-',
                                    'fixed', 'final_price' => $promotion->value !== null ? \App\Support\Currency::format((float) $promotion->value, $appCurrencyCode ?? 'USD') : '-',
                                    'bonus_days' => $promotion->value !== null ? (int) round((float) $promotion->value).' dias' : '-',
                                    default => $promotion->value !== null ? (string) $promotion->value : '-',
                                };
                                $rangeLabel = ($promotion->starts_at ? $promotion->starts_at->toDateString() : '-') . ' / ' . ($promotion->ends_at ? $promotion->ends_at->toDateString() : '-');
                                $typeLabel = match ((string) $promotion->type) {
                                    'percentage' => 'Porcentaje',
                                    'fixed' => 'Monto fijo',
                                    'final_price' => 'Precio final',
                                    'bonus_days' => 'Dias extra',
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
                                $durationLabel = $promotion->duration_months ? $promotion->duration_months.' '.($promotion->duration_months === 1 ? 'mes' : 'meses') : 'plazo flexible';
                                $commercialNarrative = match ((string) $promotion->type) {
                                    'percentage' => ($promotion->value !== null ? rtrim(rtrim(number_format((float) $promotion->value, 2, '.', ''), '0'), '.') : '0').'% de descuento al pagar '.$durationLabel.'.',
                                    'fixed' => 'Se resta '.\App\Support\Currency::format((float) ($promotion->value ?? 0), $appCurrencyCode ?? 'USD').' al total del plazo.',
                                    'final_price' => (
                                        $promotion->duration_months === 1 && (float) ($promotion->value ?? 0) === 0.0
                                            ? 'Mes de prueba gratis para activar el plan.'
                                            : (
                                                $promotion->duration_months === 2
                                                && $linkedPlan?->discount_price !== null
                                                && abs((float) $linkedPlan->discount_price - (float) ($promotion->value ?? 0)) < 0.01
                                                    ? 'Mes 1 gratis + mes 2 al precio promocional del plan.'
                                                    : 'Se cobra un total cerrado de '.\App\Support\Currency::format((float) ($promotion->value ?? 0), $appCurrencyCode ?? 'USD').' por '.$durationLabel.'.'
                                            )
                                    ),
                                    'bonus_days' => 'Mantiene el cobro y suma '.(int) round((float) ($promotion->value ?? 0)).' dias extra.',
                                    'two_for_one' => 'Oferta 2x1 con descuento de referencia sobre el plazo elegido.',
                                    'bring_friend' => 'Oferta comercial para traer un amigo con ajuste automatico.',
                                    default => 'Promocion comercial aplicada segun plan y vigencia.',
                                };
                            @endphp
                            <tr data-promotion-row data-promotion-status="{{ $isActive ? 'active' : 'inactive' }}" data-promotion-search="{{ $promotionSearch }}">
                                <td>
                                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $promotion->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $promotion->description ?: '-' }}</p>
                                    <p class="mt-2 text-xs font-semibold leading-5 text-slate-600 dark:text-slate-300">{{ $commercialNarrative }}</p>
                                </td>
                                <td class="dark:text-slate-200">{{ $promotion->planTemplate?->name ?? '-' }}</td>
                                <td class="dark:text-slate-200">{{ $typeLabel }}</td>
                                <td class="dark:text-slate-200">{{ $valueLabel }}</td>
                                <td class="dark:text-slate-200">{{ $rangeLabel }}</td>
                                <td class="dark:text-slate-200">{{ $promotion->duration_months ? $promotion->duration_months.' meses' : '-' }}</td>
                                <td>
                                    <span class="sa-pill {{ $isActive ? 'is-success' : 'is-neutral' }}">{{ $isActive ? 'Activa' : 'Inactiva' }}</span>
                                </td>
                                <td class="min-w-[16rem]">
                                    <div class="ui-action-grid">
                                        <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.promotions.toggle', $promotion->id) : '#' }}" class="w-full">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $isActive ? 'inactive' : 'active' }}">
                                            <x-ui.button type="submit" size="sm" :variant="$isActive ? 'muted' : 'success'" class="ui-action-button" :disabled="! $schemaReady">
                                                <span class="ui-action-button-label">{{ $isActive ? 'Desactivar' : 'Activar' }}</span>
                                            </x-ui.button>
                                        </form>
                                        <form method="POST" action="{{ $schemaReady ? route('superadmin.plan-templates.promotions.destroy', $promotion->id) : '#' }}" onsubmit="return confirm('Esta accion eliminara la promocion base. Deseas continuar?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="danger" class="ui-action-button" :disabled="! $schemaReady">
                                                <span class="ui-action-button-label">Eliminar</span>
                                            </x-ui.button>
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
                                <td colspan="8" class="sa-empty-row">No se encontraron promociones con ese criterio.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const promotionPlanCatalog = @json($promotionPreviewPlans);
        const searchInput = document.getElementById('promotion-search');
        const statusFilter = document.getElementById('promotion-status-filter');
        const visibleCount = document.getElementById('promotion-visible-count');
        const clearButton = document.getElementById('promotion-clear');
        const emptyState = document.getElementById('promotion-empty-state');
        const rows = Array.from(document.querySelectorAll('[data-promotion-row]'));
        const blueprintButtons = Array.from(document.querySelectorAll('.js-promotion-blueprint'));
        const promotionPlanSelect = document.querySelector('.js-promotion-plan-select');
        const promotionTypeSelect = document.querySelector('.js-promotion-type-select');
        const promotionValueInput = document.querySelector('.js-promotion-value-input');
        const promotionDurationInput = document.querySelector('.js-promotion-duration-input');
        const promotionNameInput = document.querySelector('input[name="name"]');
        const promotionDescriptionInput = document.querySelector('input[name="description"]');
        const promotionStartsAtInput = document.querySelector('input[name="starts_at"]');
        const promotionEndsAtInput = document.querySelector('input[name="ends_at"]');
        const promotionValueHelp = document.getElementById('promotion-value-help');
        const playbookTitle = document.getElementById('promotion-playbook-title');
        const playbookCopy = document.getElementById('promotion-playbook-copy');
        const playbookMeta = document.getElementById('promotion-playbook-meta');
        const previewPlan = document.getElementById('promotion-preview-plan');
        const previewStrategy = document.getElementById('promotion-preview-strategy');
        const previewBaseMonthly = document.getElementById('promotion-preview-base-monthly');
        const previewDiscountMonthly = document.getElementById('promotion-preview-discount-monthly');
        const previewBaseTotal = document.getElementById('promotion-preview-base-total');
        const previewAdjustment = document.getElementById('promotion-preview-adjustment');
        const previewFinalTotal = document.getElementById('promotion-preview-final-total');
        const previewEffectiveMonthly = document.getElementById('promotion-preview-effective-monthly');
        const previewWindow = document.getElementById('promotion-preview-window');
        const previewNote = document.getElementById('promotion-preview-note');
        let selectedBlueprint = 'manual';

        const normalizeText = function (value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        };

        const formatMoney = function (value) {
            const numeric = Number(value);
            if (!Number.isFinite(numeric)) {
                return '$0.00';
            }

            return '$' + numeric.toFixed(2);
        };

        const roundMoney = function (value) {
            return Number(Number(value || 0).toFixed(2));
        };

        const formatDateInput = function (date) {
            if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
                return '';
            }

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        };

        const addDays = function (date, days) {
            const nextDate = new Date(date.getTime());
            nextDate.setDate(nextDate.getDate() + days);

            return nextDate;
        };

        const getSelectedPlan = function () {
            return promotionPlanCatalog[String(promotionPlanSelect?.value || '')] || null;
        };

        const resolveConfiguredDiscountPrice = function (plan) {
            if (!plan) {
                return null;
            }

            const basePrice = Number(plan.price);
            const discountPrice = plan.discount_price === null ? Number.NaN : Number(plan.discount_price);

            if (!Number.isFinite(discountPrice) || discountPrice < 0) {
                return null;
            }

            if (Number.isFinite(basePrice) && discountPrice >= basePrice) {
                return null;
            }

            return roundMoney(discountPrice);
        };

        const resolveDiscountReference = function (plan) {
            const configuredDiscount = resolveConfiguredDiscountPrice(plan);
            if (configuredDiscount !== null) {
                return configuredDiscount;
            }

            const basePrice = Number(plan?.price || 0);
            if (!Number.isFinite(basePrice) || basePrice < 0) {
                return null;
            }

            return roundMoney(basePrice);
        };

        const resolveWindowLabel = function () {
            const startsAt = String(promotionStartsAtInput?.value || '').trim();
            const endsAt = String(promotionEndsAtInput?.value || '').trim();

            if (startsAt !== '' && endsAt !== '') {
                return `${startsAt} a ${endsAt}`;
            }

            if (startsAt !== '') {
                return `Desde ${startsAt}`;
            }

            if (endsAt !== '') {
                return `Hasta ${endsAt}`;
            }

            return 'Sin limite';
        };

        const fillIfBlank = function (input, value) {
            if (!input) {
                return;
            }

            if (normalizeText(input.value) === '') {
                input.value = value;
            }
        };

        const syncBlueprintButtons = function () {
            blueprintButtons.forEach(function (button) {
                const blueprint = String(button.getAttribute('data-promotion-blueprint') || 'manual');
                const isActive = blueprint === selectedBlueprint;

                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                button.classList.toggle('border-amber-400', isActive);
                button.classList.toggle('bg-amber-500/10', isActive);
                button.classList.toggle('shadow-md', isActive);
                button.classList.toggle('border-slate-200', !isActive);
                button.classList.toggle('bg-white/90', !isActive);
            });
        };

        const syncPromotionValueHelp = function () {
            if (!promotionTypeSelect || !promotionValueInput || !promotionValueHelp) {
                return;
            }

            const selectedType = String(promotionTypeSelect.value || 'percentage').trim().toLowerCase();

            if (selectedBlueprint === 'trial' && selectedType === 'final_price') {
                promotionValueInput.placeholder = '0';
                promotionValueHelp.textContent = 'Para un mes gratis, deja el total final en 0.';
                return;
            }

            if (selectedBlueprint === 'close_10_days' && selectedType === 'final_price') {
                promotionValueInput.placeholder = '35';
                promotionValueHelp.textContent = 'Escribe el total que quieres cobrar por los 2 meses juntos. Si el plan tiene precio promo, el atajo lo usa como referencia.';
                return;
            }

            if (selectedType === 'percentage') {
                promotionValueInput.placeholder = '25';
                promotionValueHelp.textContent = 'Escribe el porcentaje que quieres descontar del total del plazo elegido.';
                return;
            }

            if (selectedType === 'fixed') {
                promotionValueInput.placeholder = '30';
                promotionValueHelp.textContent = 'Escribe el monto fijo que quieres restar al total de esos meses.';
                return;
            }

            if (selectedType === 'final_price') {
                promotionValueInput.placeholder = '100';
                promotionValueHelp.textContent = 'Escribe el total final exacto que quieres cobrar por todo el plazo.';
                return;
            }

            if (selectedType === 'bonus_days') {
                promotionValueInput.placeholder = '5';
                promotionValueHelp.textContent = 'Escribe cuantos dias extra dara la promo. El total no cambia, solo la cobertura.';
                return;
            }

            promotionValueInput.placeholder = '50';
            promotionValueHelp.textContent = 'Si dejas este valor vacio, el sistema tomara 50% como referencia para la vista previa.';
        };

        const syncPlaybookPanel = function (plan, months, windowLabel, selectedType) {
            const typeLabels = {
                percentage: 'Descuento porcentual',
                fixed: 'Ajuste de monto fijo',
                final_price: 'Precio final cerrado',
                bonus_days: 'Dias extra',
                two_for_one: 'Oferta 2x1',
                bring_friend: 'Trae un amigo',
            };
            let title = typeLabels[selectedType] || 'Promocion personalizada';
            let copy = 'Ajusta manualmente el plazo, el tipo y el valor para construir cualquier oferta comercial.';
            let meta = `${months} ${months === 1 ? 'mes' : 'meses'} / ${windowLabel}`;

            if (selectedBlueprint === 'trial') {
                title = 'Mes de prueba gratis';
                copy = 'Entrada suave para clientes nuevos: 1 mes sin cobro para bajar la barrera de contratacion.';
                meta = '1 mes / activacion inicial';
            } else if (selectedBlueprint === 'close_10_days') {
                title = 'Cierre en 10 dias';
                copy = 'Usa el plazo de 2 meses y guarda el combo como total cerrado. Comercialmente se comunica como mes 1 gratis + mes 2 promocional.';
                if (plan && resolveConfiguredDiscountPrice(plan) === null) {
                    copy += ' Define un precio con descuento en el plan para que el segundo mes herede ese valor.';
                }
                meta = `${months} ${months === 1 ? 'mes' : 'meses'} / ventana corta`;
            } else if (selectedBlueprint === 'prepay_3') {
                title = 'Pago adelantado 3 meses';
                copy = 'Promocion clasica para aumentar permanencia y ticket medio con una oferta simple de explicar.';
                meta = '3 meses / descuento por volumen';
            }

            if (playbookTitle) playbookTitle.textContent = title;
            if (playbookCopy) playbookCopy.textContent = copy;
            if (playbookMeta) playbookMeta.textContent = meta;
            if (previewStrategy) previewStrategy.textContent = title;
        };

        const syncBlueprintValueFromPlan = function () {
            if (!promotionValueInput) {
                return;
            }

            const selectedPlan = getSelectedPlan();

            if (selectedBlueprint === 'trial') {
                promotionValueInput.value = '0';
                return;
            }

            if (selectedBlueprint === 'close_10_days') {
                const reference = resolveDiscountReference(selectedPlan);
                promotionValueInput.value = reference !== null ? String(reference) : '';
            }
        };

        const applyBlueprint = function (blueprint) {
            selectedBlueprint = blueprint;

            if (blueprint === 'trial') {
                if (promotionTypeSelect) promotionTypeSelect.value = 'final_price';
                if (promotionDurationInput) promotionDurationInput.value = '1';
                if (promotionValueInput) promotionValueInput.value = '0';
                fillIfBlank(promotionNameInput, 'Mes de prueba gratis');
                fillIfBlank(promotionDescriptionInput, 'Primer mes gratis para activar el plan y reducir friccion comercial.');
            } else if (blueprint === 'close_10_days') {
                const today = new Date();
                if (promotionTypeSelect) promotionTypeSelect.value = 'final_price';
                if (promotionDurationInput) promotionDurationInput.value = '2';
                if (promotionStartsAtInput) promotionStartsAtInput.value = formatDateInput(today);
                if (promotionEndsAtInput) promotionEndsAtInput.value = formatDateInput(addDays(today, 10));
                syncBlueprintValueFromPlan();
                fillIfBlank(promotionNameInput, 'Cierre 10 dias');
                fillIfBlank(promotionDescriptionInput, 'Si contrata dentro de 10 dias, recibe el primer mes gratis y el segundo al precio promocional del plan.');
            } else if (blueprint === 'prepay_3') {
                if (promotionTypeSelect) promotionTypeSelect.value = 'percentage';
                if (promotionDurationInput) promotionDurationInput.value = '3';
                if (promotionValueInput) promotionValueInput.value = '25';
                fillIfBlank(promotionNameInput, 'Pago adelantado 3 meses');
                fillIfBlank(promotionDescriptionInput, 'Descuento por pago adelantado del plan durante 3 meses.');
            }

            syncBlueprintButtons();
            syncPromotionValueHelp();
            syncPromotionPreview();
        };

        const syncPromotionPreview = function () {
            const selectedPlan = getSelectedPlan();
            const selectedType = String(promotionTypeSelect?.value || 'percentage').trim().toLowerCase();
            const months = Math.max(1, Math.round(Number(promotionDurationInput?.value || 1)));
            const rawValue = Number(promotionValueInput?.value || 0);
            const windowLabel = resolveWindowLabel();
            const configuredDiscount = resolveConfiguredDiscountPrice(selectedPlan);

            syncBlueprintButtons();
            syncPlaybookPanel(selectedPlan, months, windowLabel, selectedType);

            if (!selectedPlan) {
                if (previewPlan) previewPlan.textContent = 'Selecciona un plan';
                if (previewDiscountMonthly) previewDiscountMonthly.textContent = 'Sin precio promo';
                if (previewBaseMonthly) previewBaseMonthly.textContent = '$0.00';
                if (previewBaseTotal) previewBaseTotal.textContent = '$0.00';
                if (previewAdjustment) previewAdjustment.textContent = 'Sin ajuste';
                if (previewFinalTotal) previewFinalTotal.textContent = '$0.00';
                if (previewEffectiveMonthly) previewEffectiveMonthly.textContent = '$0.00';
                if (previewWindow) previewWindow.textContent = windowLabel;
                if (previewNote) previewNote.textContent = 'Elige primero un plan comercial para calcular el cobro final.';
                return;
            }

            const baseMonthlyPrice = roundMoney(selectedPlan.price || 0);
            const baseTotal = roundMoney(baseMonthlyPrice * months);
            let discountAmount = 0;
            let finalTotal = baseTotal;
            let adjustmentLabel = 'Sin ajuste';
            let bonusDays = 0;

            if (selectedType === 'percentage') {
                const percent = Math.max(0, Math.min(100, rawValue));
                discountAmount = roundMoney(baseTotal * (percent / 100));
                finalTotal = roundMoney(Math.max(0, baseTotal - discountAmount));
                adjustmentLabel = percent > 0 ? '-' + formatMoney(discountAmount) + ' (' + percent.toFixed(2).replace(/\.00$/, '') + '%)' : '0% sin descuento';
            } else if (selectedType === 'fixed') {
                discountAmount = roundMoney(Math.max(0, Math.min(baseTotal, rawValue)));
                finalTotal = roundMoney(Math.max(0, baseTotal - discountAmount));
                adjustmentLabel = '-' + formatMoney(discountAmount) + ' fijos';
            } else if (selectedType === 'final_price') {
                finalTotal = roundMoney(Math.max(0, rawValue));
                discountAmount = roundMoney(Math.max(0, baseTotal - finalTotal));
                adjustmentLabel = 'Total cerrado en ' + formatMoney(finalTotal);
            } else if (selectedType === 'bonus_days') {
                bonusDays = Math.max(0, Math.round(rawValue));
                adjustmentLabel = '+' + bonusDays + ' dias extra';
            } else if (selectedType === 'two_for_one' || selectedType === 'bring_friend') {
                const percent = rawValue > 0 ? Math.max(0, Math.min(100, rawValue)) : 50;
                discountAmount = roundMoney(baseTotal * (percent / 100));
                finalTotal = roundMoney(Math.max(0, baseTotal - discountAmount));
                adjustmentLabel = '-' + formatMoney(discountAmount) + ' (' + percent.toFixed(2).replace(/\.00$/, '') + '% ref.)';
            }

            const effectiveMonthly = roundMoney(finalTotal / months);

            if (selectedBlueprint === 'trial' && selectedType === 'final_price' && months === 1 && finalTotal === 0) {
                adjustmentLabel = 'Mes 1 gratis';
            }

            if (selectedBlueprint === 'close_10_days' && selectedType === 'final_price' && months === 2) {
                adjustmentLabel = 'Mes 1 gratis + mes 2 en ' + formatMoney(finalTotal);
            }

            if (previewPlan) previewPlan.textContent = selectedPlan.name + ' - ' + months + ' ' + (months === 1 ? 'mes' : 'meses');
            if (previewDiscountMonthly) previewDiscountMonthly.textContent = configuredDiscount !== null ? formatMoney(configuredDiscount) : 'Sin precio promo';
            if (previewBaseMonthly) previewBaseMonthly.textContent = formatMoney(baseMonthlyPrice);
            if (previewBaseTotal) previewBaseTotal.textContent = formatMoney(baseTotal);
            if (previewAdjustment) previewAdjustment.textContent = adjustmentLabel;
            if (previewFinalTotal) previewFinalTotal.textContent = formatMoney(finalTotal);
            if (previewEffectiveMonthly) previewEffectiveMonthly.textContent = formatMoney(effectiveMonthly);
            if (previewWindow) previewWindow.textContent = windowLabel;
            if (previewNote) {
                if (selectedBlueprint === 'trial' && selectedType === 'final_price' && months === 1 && finalTotal === 0) {
                    previewNote.textContent = 'Entrada suave: ' + selectedPlan.name + ' se activa gratis durante el primer mes y luego podras vender la renovacion normal.';
                } else if (selectedBlueprint === 'close_10_days' && selectedType === 'final_price' && months === 2) {
                    previewNote.textContent = configuredDiscount !== null
                        ? 'Cierre rapido: si el cliente compra antes del ' + String(promotionEndsAtInput?.value || 'fin de campana') + ', el sistema cobra ' + formatMoney(finalTotal) + ' por 2 meses. Comercialmente queda como mes 1 gratis + mes 2 a ' + formatMoney(finalTotal) + '.'
                        : 'Cierre rapido: la promo ya puede guardarse como total final de 2 meses, pero conviene definir un precio con descuento en el plan para comunicar mejor el segundo mes promocional.';
                } else if (selectedBlueprint === 'prepay_3' && months === 3) {
                    previewNote.textContent = selectedPlan.name + ' cobra normalmente ' + selectedPlan.duration_label + '. Con este pago adelantado quedaria en ' + formatMoney(finalTotal) + ' por 3 meses.';
                } else {
                    previewNote.textContent = selectedPlan.name + ' cobra normalmente ' + selectedPlan.duration_label + '. Con esta promo quedaria en ' + formatMoney(finalTotal) + ' por ' + months + ' ' + (months === 1 ? 'mes' : 'meses') + (bonusDays > 0 ? ' y ademas suma ' + bonusDays + ' dias extra.' : '.');
                }
            }
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
        blueprintButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                applyBlueprint(String(button.getAttribute('data-promotion-blueprint') || 'manual'));
            });
        });
        promotionPlanSelect?.addEventListener('change', function () {
            syncBlueprintValueFromPlan();
            syncPromotionPreview();
        });
        promotionTypeSelect?.addEventListener('change', function () {
            syncPromotionValueHelp();
            syncPromotionPreview();
        });
        promotionValueInput?.addEventListener('input', syncPromotionPreview);
        promotionDurationInput?.addEventListener('input', syncPromotionPreview);
        promotionStartsAtInput?.addEventListener('change', syncPromotionPreview);
        promotionEndsAtInput?.addEventListener('change', syncPromotionPreview);
        syncPromotionValueHelp();
        syncBlueprintButtons();
        syncPromotionPreview();
        applyFilter();
    })();
</script>
@endpush

@push('scripts')
<script>
    (() => {
        const unitSelect = document.getElementById('promotion-duration-unit');
        const monthsWrap = document.getElementById('promotion-duration-months-wrap');
        const daysWrap = document.getElementById('promotion-duration-days-wrap');
        const monthsInput = document.getElementById('promotion-duration-months');
        const daysInput = document.getElementById('promotion-duration-days');

        if (!unitSelect || !monthsWrap || !daysWrap || !monthsInput || !daysInput) {
            return;
        }

        const syncDurationInputs = () => {
            const unit = String(unitSelect.value || 'months').toLowerCase();
            const useDays = unit === 'days';

            monthsWrap.classList.toggle('hidden', useDays);
            daysWrap.classList.toggle('hidden', !useDays);
            monthsInput.required = !useDays;
            daysInput.required = useDays;
        };

        unitSelect.addEventListener('change', syncDurationInputs);
        syncDurationInputs();
    })();
</script>
@endpush

