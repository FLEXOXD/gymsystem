@extends('layouts.panel')

@section('title', 'Planes')
@section('page-title', 'Planes')

@push('styles')
<style>
.plans-page {
    --pp-chip-border: rgb(148 163 184 / .42);
    --pp-chip-bg: rgb(15 23 42 / .5);
    --pp-chip-text: rgb(226 232 240);
    --pp-chip-hover-bg: rgb(30 41 59 / .85);
    --pp-chip-hover-border: rgb(100 116 139 / .72);
    --pp-chip-active-bg: rgb(16 185 129 / .2);
    --pp-chip-active-border: rgb(16 185 129 / .62);
    --pp-chip-active-text: rgb(167 243 208);
    --pp-row-odd: rgb(15 23 42 / .2);
    --pp-row-even: transparent;
    --pp-row-hover: rgb(15 23 42 / .34);
    --pp-mini-bg: rgb(15 23 42 / .55);
    --pp-mini-border: rgb(148 163 184 / .4);
    --pp-mini-text: rgb(226 232 240);
    --pp-mini-hover-bg: rgb(30 41 59 / .9);
    --pp-mini-hover-border: rgb(100 116 139 / .75);
    --pp-preview-border: rgb(148 163 184 / .35);
    --pp-preview-bg: linear-gradient(145deg, rgb(2 6 23 / .88), rgb(15 23 42 / .72));
    --pp-preview-shadow: 0 18px 40px rgb(2 6 23 / .35);
    --pp-inline-bg: rgb(15 23 42 / .5);
    --pp-inline-border: rgb(148 163 184 / .35);
    --pp-inline-text: rgb(241 245 249);
    --pp-inline-symbol: rgb(203 213 225);
    --pp-advanced-bg: rgb(15 23 42 / .35);
    --pp-advanced-border: rgb(148 163 184 / .3);
    --pp-advanced-text: rgb(226 232 240);
    --pp-advanced-muted: rgb(148 163 184);
    --pp-advanced-switch-bg: rgb(15 23 42 / .5);
    --pp-advanced-switch-border: rgb(148 163 184 / .35);
    --pp-advanced-switch-text: rgb(226 232 240);
    --pp-modal-bg: rgb(2 6 23 / .62);
    --pp-modal-border: rgb(148 163 184 / .35);
    --pp-modal-card: rgb(2 6 23 / .96);
    --pp-modal-shadow: 0 24px 50px rgb(2 6 23 / .55);
    --pp-modal-text: rgb(226 232 240);
}

.theme-light .plans-page {
    --pp-chip-border: rgb(148 163 184 / .45);
    --pp-chip-bg: #ffffff;
    --pp-chip-text: #334155;
    --pp-chip-hover-bg: #f1f5f9;
    --pp-chip-hover-border: rgb(100 116 139 / .65);
    --pp-chip-active-bg: #dcfce7;
    --pp-chip-active-border: #34d399;
    --pp-chip-active-text: #047857;
    --pp-row-odd: #ffffff;
    --pp-row-even: #f8fafc;
    --pp-row-hover: #e2e8f0;
    --pp-mini-bg: #e2e8f0;
    --pp-mini-border: #94a3b8;
    --pp-mini-text: #1e293b;
    --pp-mini-hover-bg: #cbd5e1;
    --pp-mini-hover-border: #64748b;
    --pp-inline-bg: #ffffff;
    --pp-inline-border: #cbd5e1;
    --pp-inline-text: #0f172a;
    --pp-inline-symbol: #475569;
    --pp-advanced-bg: #f1f5f9;
    --pp-advanced-border: #cbd5e1;
    --pp-advanced-text: #0f172a;
    --pp-advanced-muted: #64748b;
    --pp-advanced-switch-bg: #ffffff;
    --pp-advanced-switch-border: #cbd5e1;
    --pp-advanced-switch-text: #1e293b;
    --pp-modal-bg: rgb(15 23 42 / .35);
    --pp-modal-border: #cbd5e1;
    --pp-modal-card: #ffffff;
    --pp-modal-shadow: 0 20px 40px rgb(15 23 42 / .2);
    --pp-modal-text: #0f172a;
}

.plans-page .chip-btn {
    border: 1px solid var(--pp-chip-border);
    background: var(--pp-chip-bg);
    color: var(--pp-chip-text);
    border-radius: 9999px;
    padding: .35rem .75rem;
    font-size: .75rem;
    font-weight: 700;
    line-height: 1;
    transition: .15s ease;
}
.plans-page .chip-btn:hover {
    border-color: var(--pp-chip-hover-border);
    background: var(--pp-chip-hover-bg);
}
.plans-page .chip-btn.active {
    border-color: var(--pp-chip-active-border);
    background: var(--pp-chip-active-bg);
    color: var(--pp-chip-active-text);
}
.plans-page .plan-preview {
    border: 1px solid var(--pp-preview-border);
    background: var(--pp-preview-bg);
    border-radius: 1rem;
    box-shadow: var(--pp-preview-shadow);
}
.plans-page .plans-table tbody tr:nth-child(odd) { background: var(--pp-row-odd); }
.plans-page .plans-table tbody tr:nth-child(even) { background: var(--pp-row-even); }
.plans-page .plans-table tbody tr:hover { background: var(--pp-row-hover); }
.plans-page .mini-action {
    font-size: .72rem;
    line-height: 1;
    padding: .4rem .55rem;
    border-radius: .55rem;
    border: 1px solid var(--pp-mini-border);
    background: var(--pp-mini-bg);
    color: var(--pp-mini-text);
    font-weight: 700;
    transition: .15s ease;
}
.plans-page .mini-action:hover {
    background: var(--pp-mini-hover-bg);
    border-color: var(--pp-mini-hover-border);
}
.plans-page .mini-action.danger:hover {
    border-color: rgb(244 63 94 / .7);
    color: rgb(159 18 57);
    background: rgb(254 226 226);
}
.theme-dark .plans-page .mini-action.danger:hover {
    color: rgb(254 205 211);
    background: rgb(159 18 57 / .25);
}
.plans-page .price-shell {
    border: 1px solid var(--pp-inline-border);
    background: var(--pp-inline-bg);
}
.plans-page .price-inline-input {
    color: var(--pp-inline-text);
}
.plans-page .price-inline-symbol {
    color: var(--pp-inline-symbol);
}
.plans-page .plans-advanced-shell {
    border-color: var(--pp-advanced-border);
    background: var(--pp-advanced-bg);
}
.plans-page .plans-advanced-toggle {
    color: var(--pp-advanced-text);
}
.plans-page .plans-advanced-icon {
    color: var(--pp-advanced-muted);
}
.plans-page .plans-advanced-switch {
    border-color: var(--pp-advanced-switch-border);
    background: var(--pp-advanced-switch-bg);
}
.plans-page .plans-advanced-switch-text {
    color: var(--pp-advanced-switch-text);
}
.plans-page .modal-shell {
    position: fixed;
    inset: 0;
    z-index: 60;
    background: var(--pp-modal-bg);
    backdrop-filter: blur(3px);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.plans-page .modal-shell.is-open { display: flex; }
.plans-page .modal-card {
    width: min(100%, 42rem);
    max-height: calc(100vh - 2rem);
    max-height: calc(100dvh - 2rem);
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 1rem;
    border: 1px solid var(--pp-modal-border);
    background: var(--pp-modal-card);
    box-shadow: var(--pp-modal-shadow);
    color: var(--pp-modal-text);
}
</style>
@endpush

@section('content')
@php
    $planAccessService = app(\App\Services\PlanAccessService::class);
    $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
    $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
    $isBranchContext = (bool) request()->attributes->get('gym_context_is_branch', false);
    $isReadOnlyScope = $isGlobalScope || $isBranchContext;
    $canManagePromotions = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'promotions');
    $canViewPromotions = $canManagePromotions || $isReadOnlyScope;
    $routeHasUpdate = \Illuminate\Support\Facades\Route::has('plans.update');
    $routeHasDestroy = \Illuminate\Support\Facades\Route::has('plans.destroy');
    $routeHasToggle = \Illuminate\Support\Facades\Route::has('plans.toggle');
    $updateRouteTemplate = $routeHasUpdate ? route('plans.update', ['plan' => '__PLAN__']) : '';
    $destroyRouteTemplate = $routeHasDestroy ? route('plans.destroy', ['plan' => '__PLAN__']) : '';
    $toggleRouteTemplate = $routeHasToggle ? route('plans.toggle', ['plan' => '__PLAN__']) : '';
    $promotionTypeLabels = [
        'percentage' => 'Descuento %',
        'fixed' => 'Descuento monto',
        'final_price' => 'Precio final',
        'bonus_days' => 'Días extra',
        'two_for_one' => '2x1',
        'bring_friend' => 'Trae a un amigo',
    ];
    $openPromotionModal = ! $isReadOnlyScope && $canManagePromotions && (string) old('promotion_form', '0') === '1';
    $readOnlyMessage = $isGlobalScope
        ? 'Modo global activo: planes y promociones en solo lectura. Selecciona una sede específica para crear, editar o eliminar.'
        : 'Modo sucursal secundaria: planes y promociones en solo lectura. La sede principal administra estos cambios.';
    $readOnlyActionLabel = $isGlobalScope ? 'Solo lectura global' : 'Solo lectura sucursal';
    $defaultStatus = old('status', 'active');
    $defaultDurationUnit = \App\Support\PlanDuration::normalizeUnit((string) old('duration_unit', 'days'));
    $defaultDurationDays = max(1, (int) old('duration_days', 30));
    $defaultDurationMonths = max(1, (int) old('duration_months', 1));
    $defaultDurationLabel = \App\Support\PlanDuration::label($defaultDurationUnit, $defaultDurationDays, $defaultDurationMonths);
@endphp

<div class="plans-page space-y-5">
    <div id="plans-alert-container" class="space-y-2" aria-live="polite" aria-atomic="true">
        @if (session('status'))
            <div class="ui-alert ui-alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="ui-alert ui-alert-danger">{{ $errors->first() }}</div>
        @endif
        @if ($isReadOnlyScope)
            <div class="ui-alert ui-alert-warning">
                {{ $readOnlyMessage }}
            </div>
        @endif
    </div>

    @if (! $isReadOnlyScope)
    <x-ui.card>
        <div class="space-y-4">
            <div>
                <h2 class="ui-heading text-xl font-black">Crear plan</h2>
                <p class="ui-muted mt-1 text-sm">Define planes claros para ventas rápidas y control de membresías.</p>
            </div>

            <form id="create-plan-form" method="POST" action="{{ route('plans.store') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 xl:grid-cols-3">
                    <div class="space-y-4 xl:col-span-2">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span class="text-xs uppercase tracking-wide">Nombre</span>
                                <input id="plan-name" type="text" name="name" value="{{ old('name') }}" required minlength="3" class="ui-input" placeholder="Ej: Mensual Pro" aria-label="Nombre del plan">
                                @error('name') <span class="text-xs font-bold text-rose-300">{{ $message }}</span> @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span class="text-xs uppercase tracking-wide">Estado</span>
                                <select id="plan-status" name="status" class="ui-input" aria-label="Estado del plan">
                                    <option value="active" @selected($defaultStatus === 'active')>Activo</option>
                                    <option value="inactive" @selected($defaultStatus === 'inactive')>Oculto</option>
                                </select>
                                @error('status') <span class="text-xs font-bold text-rose-300">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div id="duration-presets-row" class="space-y-2 {{ $defaultDurationUnit === 'months' ? 'opacity-50' : '' }}">
                            <p class="ui-muted text-xs font-bold uppercase tracking-wide">Duración rápida</p>
                            <div class="flex flex-wrap gap-2" role="group" aria-label="Presets de duración">
                                @foreach ([1, 7, 15, 30, 60, 90] as $preset)
                                    <button type="button" class="chip-btn js-duration-chip {{ $defaultDurationUnit === 'months' ? 'cursor-not-allowed' : '' }}" data-days="{{ $preset }}" @disabled($defaultDurationUnit === 'months')>{{ $preset }} días</button>
                                @endforeach
                                <button type="button" class="chip-btn js-duration-chip {{ $defaultDurationUnit === 'months' ? 'cursor-not-allowed' : '' }}" data-days="custom" @disabled($defaultDurationUnit === 'months')>Personalizado</button>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span class="text-xs uppercase tracking-wide">Tipo duración</span>
                                <select id="plan-duration-unit" name="duration_unit" class="ui-input" aria-label="Tipo de duración">
                                    <option value="days" @selected($defaultDurationUnit === 'days')>Por días exactos</option>
                                    <option value="months" @selected($defaultDurationUnit === 'months')>Por meses calendario</option>
                                </select>
                            </label>

                            <label id="plan-duration-days-row" class="space-y-1 text-sm font-semibold ui-muted {{ $defaultDurationUnit === 'months' ? 'hidden' : '' }}">
                                <span class="text-xs uppercase tracking-wide">Duración (días)</span>
                                <input id="plan-duration" type="number" name="duration_days" min="1" step="1" value="{{ $defaultDurationDays }}" @required($defaultDurationUnit === 'days') class="ui-input" aria-label="Duración en días">
                                @error('duration_days') <span class="text-xs font-bold text-rose-300">{{ $message }}</span> @enderror
                            </label>

                            <label id="plan-duration-months-row" class="space-y-1 text-sm font-semibold ui-muted {{ $defaultDurationUnit === 'months' ? '' : 'hidden' }}">
                                <span class="text-xs uppercase tracking-wide">Duración (meses)</span>
                                <input id="plan-duration-months" type="number" name="duration_months" min="1" step="1" value="{{ $defaultDurationMonths }}" @required($defaultDurationUnit === 'months') class="ui-input" aria-label="Duración en meses">
                                @error('duration_months') <span class="text-xs font-bold text-rose-300">{{ $message }}</span> @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-3">
                                <span class="text-xs uppercase tracking-wide">Precio</span>
                                <div class="price-shell flex items-center gap-2 rounded-xl px-3 py-2">
                                    <span class="price-inline-symbol text-sm font-black">{{ trim((string) $appCurrencySymbol) }}</span>
                                    <input id="plan-price" type="number" name="price" min="0" step="0.01" value="{{ old('price', '0.00') }}" required class="price-inline-input w-full bg-transparent text-sm font-semibold outline-none" aria-label="Precio del plan">
                                </div>
                                <p class="text-xs text-slate-400">Vista: <strong id="price-visual">{{ \App\Support\Currency::format((float) old('price', 0), $appCurrencyCode) }}</strong></p>
                                @error('price') <span class="text-xs font-bold text-rose-300">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div class="plans-advanced-shell rounded-2xl border">
                            <button type="button" class="plans-advanced-toggle flex w-full items-center justify-between px-4 py-3 text-left text-sm font-bold" data-accordion-toggle="plan-advanced-options" aria-expanded="false" aria-controls="plan-advanced-options">
                                <span>Opciones avanzadas</span>
                                <span class="plans-advanced-icon text-xs" data-accordion-icon>Mostrar</span>
                            </button>
                            <div id="plan-advanced-options" class="hidden border-t border-slate-300/20 px-4 py-4">
                                <div class="grid gap-4 md:grid-cols-3">
                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span class="text-xs uppercase tracking-wide">Días de tolerancia</span>
                                        <input type="number" name="grace_days" min="0" step="1" value="{{ old('grace_days', 0) }}" class="ui-input" aria-label="Días de tolerancia">
                                    </label>
                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span class="text-xs uppercase tracking-wide">Límite ingresos/día</span>
                                        <input type="number" name="daily_checkin_limit" min="1" step="1" value="{{ old('daily_checkin_limit') }}" class="ui-input" placeholder="Ilimitado" aria-label="Límite de ingresos por día">
                                    </label>
                                    <label class="plans-advanced-switch flex items-center gap-2 rounded-xl border px-3 py-3">
                                        <input type="checkbox" name="quick_sale_enabled" value="1" class="h-4 w-4" @checked(old('quick_sale_enabled', '1') === '1') aria-label="Mostrar en venta rápida">
                                        <span class="plans-advanced-switch-text text-sm font-semibold">Mostrar en venta rápida</span>
                                    </label>
                                </div>
                                {{-- Backend mapping pendiente:
                                     Migration: plans.grace_days, plans.daily_checkin_limit, plans.quick_sale_enabled
                                     Requests/Controller: store + update --}}
                            </div>
                        </div>

                        <div class="pt-1">
                            <button id="create-plan-submit" type="submit" class="ui-button ui-button-primary px-5 py-2.5 text-sm font-black">
                                <span class="js-submit-label">Guardar plan</span>
                                <span class="js-submit-loading hidden">Guardando...</span>
                            </button>
                        </div>
                    </div>

                    <aside class="plan-preview p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Vista previa</p>
                        <div class="mt-3 space-y-3">
                            <h3 id="preview-name" class="text-lg font-black text-slate-100">Nombre del plan</h3>
                            <div class="flex items-center justify-between rounded-xl border border-slate-300/20 bg-slate-900/45 px-3 py-2">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Precio</span>
                                <span id="preview-price" class="text-base font-black text-emerald-300">{{ \App\Support\Currency::format((float) old('price', 0), $appCurrencyCode) }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-300/20 bg-slate-900/45 px-3 py-2">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Duración</span>
                                <span id="preview-duration" class="text-sm font-bold text-slate-100">{{ $defaultDurationLabel }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-slate-300/20 bg-slate-900/45 px-3 py-2">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Estado</span>
                                <span id="preview-status" class="inline-flex rounded-full border border-emerald-400/40 bg-emerald-500/15 px-2.5 py-1 text-xs font-bold text-emerald-200">{{ $defaultStatus === 'active' ? 'Activo' : 'Oculto' }}</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </x-ui.card>
    @else
    <x-ui.card title="Crear plan" subtitle="Modo de solo lectura.">
        <p class="ui-muted text-sm">
            {{ $isGlobalScope
                ? 'Para crear o editar planes, cambia el selector de sucursal arriba y entra en una sede específica.'
                : 'La sede secundaria no puede crear ni editar planes. Esta gestión se realiza desde la sede principal.' }}
        </p>
    </x-ui.card>
    @endif

    <x-ui.card>
            <div class="space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="ui-heading text-lg font-black">Planes del gimnasio</h2>
                        <p class="ui-muted text-sm">Administra tus planes sin salir de esta pantalla.</p>
                    </div>
                <span id="plans-count" class="ui-text inline-flex rounded-full border border-slate-300/35 px-3 py-1 text-xs font-bold">{{ $plans->count() }} planes</span>
            </div>

            <div class="grid gap-3 md:grid-cols-3">
                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                    <span class="text-xs uppercase tracking-wide">Buscar plan</span>
                    <input id="plans-search" type="search" class="ui-input" placeholder="Buscar por nombre o ID" aria-label="Buscar planes">
                </label>
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span class="text-xs uppercase tracking-wide">Estado</span>
                    <select id="plans-status-filter" class="ui-input" aria-label="Filtrar por estado">
                        <option value="all">Todos</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Ocultos</option>
                    </select>
                </label>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-300/30">
                <table class="plans-table ui-table min-w-[980px] text-sm">
                    <thead>
                        <tr class="border-b border-slate-200/40 text-left text-xs uppercase tracking-wider">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre</th>
                            @if ($isGlobalScope)
                                <th class="px-4 py-3">Sede</th>
                            @endif
                            <th class="px-4 py-3">Duración</th>
                            <th class="px-4 py-3">Precio</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="plans-table-body">
                        @forelse ($plans as $plan)
                            <tr
                                data-plan-id="{{ $plan->id }}"
                                data-plan-name="{{ mb_strtolower($plan->name) }}"
                                data-plan-status="{{ $plan->status }}"
                                data-plan-duration="{{ $plan->duration_days }}"
                                data-plan-duration-unit="{{ \App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days')) }}"
                                data-plan-duration-months="{{ $plan->duration_months !== null ? (int) $plan->duration_months : '' }}"
                                data-plan-price="{{ number_format((float) $plan->price, 2, '.', '') }}"
                                class="border-b border-slate-200/30 align-middle">
                                <td class="ui-text px-4 py-3 font-black">{{ $plan->id }}</td>
                                <td class="ui-text px-4 py-3 font-semibold">{{ $plan->name }}</td>
                                @if ($isGlobalScope)
                                    <td class="px-4 py-3">
                                        <x-ui.badge variant="info">{{ $plan->gym?->name ?? '-' }}</x-ui.badge>
                                    </td>
                                @endif
                                <td class="ui-text px-4 py-3">{{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }}</td>
                                <td class="ui-text px-4 py-3">{{ \App\Support\Currency::format((float) $plan->price, $appCurrencyCode) }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$plan->status === 'active' ? 'success' : 'muted'">{{ $plan->status === 'active' ? 'Activo' : 'Oculto' }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($isReadOnlyScope)
                                        <span class="text-xs font-semibold ui-muted">{{ $readOnlyActionLabel }}</span>
                                    @else
                                        <div class="flex justify-end gap-1.5">
                                            <button type="button" class="mini-action js-edit-plan" data-plan-id="{{ $plan->id }}" data-plan-name-value="{{ $plan->name }}" data-plan-duration-value="{{ $plan->duration_days }}" data-plan-duration-unit-value="{{ \App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days')) }}" data-plan-duration-months-value="{{ $plan->duration_months !== null ? (int) $plan->duration_months : '' }}" data-plan-price-value="{{ number_format((float) $plan->price, 2, '.', '') }}" data-plan-status-value="{{ $plan->status }}" title="Editar" aria-label="Editar plan {{ $plan->name }}">Editar</button>
                                            <button type="button" class="mini-action js-duplicate-plan" data-plan-name-value="{{ $plan->name }}" data-plan-duration-value="{{ $plan->duration_days }}" data-plan-duration-unit-value="{{ \App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days')) }}" data-plan-duration-months-value="{{ $plan->duration_months !== null ? (int) $plan->duration_months : '' }}" data-plan-price-value="{{ number_format((float) $plan->price, 2, '.', '') }}" data-plan-status-value="{{ $plan->status }}" title="Duplicar" aria-label="Duplicar plan {{ $plan->name }}">Duplicar</button>
                                            <button type="button" class="mini-action js-toggle-plan" data-plan-id="{{ $plan->id }}" data-plan-name-value="{{ $plan->name }}" data-current-status="{{ $plan->status }}" title="{{ $plan->status === 'active' ? 'Desactivar' : 'Activar' }}" aria-label="{{ $plan->status === 'active' ? 'Desactivar' : 'Activar' }} plan {{ $plan->name }}">{{ $plan->status === 'active' ? 'Desactivar' : 'Activar' }}</button>
                                            <button type="button" class="mini-action danger js-delete-plan" data-plan-id="{{ $plan->id }}" data-plan-name-value="{{ $plan->name }}" title="Eliminar" aria-label="Eliminar plan {{ $plan->name }}">Eliminar</button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isGlobalScope ? 7 : 6 }}" class="px-4 py-8 text-center text-sm font-semibold text-slate-400">No hay planes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-ui.card>

    @if ($canViewPromotions)
    <x-ui.card>
        <div class="space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="ui-heading text-lg font-black">Promociones comerciales</h2>
                    <p class="ui-muted text-sm">Crea promociones fáciles para fechas especiales: San Valentín, 2x1, trae a un amigo y más.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if (! $isReadOnlyScope && $canManagePromotions)
                        <button type="button" id="open-promotion-modal-btn" class="ui-button ui-button-primary px-3 py-1.5 text-xs font-black">
                            + Nueva promoción
                        </button>
                    @else
                        <span class="text-xs font-semibold ui-muted">{{ $readOnlyActionLabel }}</span>
                    @endif
                    <span class="ui-text inline-flex rounded-full border border-slate-300/35 px-3 py-1 text-xs font-bold">{{ ($promotions ?? collect())->count() }} promociones</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-300/30">
                <table class="plans-table ui-table min-w-[1020px] text-sm">
                    <thead>
                    <tr class="border-b border-slate-200/40 text-left text-xs uppercase tracking-wider">
                        <th class="px-4 py-3">Promo</th>
                        @if ($isGlobalScope)
                            <th class="px-4 py-3">Sede</th>
                        @endif
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Valor</th>
                        <th class="px-4 py-3">Vigencia</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Usos</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse (($promotions ?? collect()) as $promotion)
                        <tr class="border-b border-slate-200/30 align-middle">
                            <td class="ui-text px-4 py-3 font-semibold">{{ $promotion->name }}</td>
                            @if ($isGlobalScope)
                                <td class="px-4 py-3">
                                    <x-ui.badge variant="info">{{ $promotion->gym?->name ?? '-' }}</x-ui.badge>
                                </td>
                            @endif
                            <td class="ui-text px-4 py-3">{{ $promotionTypeLabels[$promotion->type] ?? $promotion->type }}</td>
                            <td class="ui-text px-4 py-3">
                                @if ($promotion->type === 'percentage')
                                    -{{ (float) $promotion->value }}%
                                @elseif ($promotion->type === 'fixed')
                                    -{{ \App\Support\Currency::format((float) $promotion->value, $appCurrencyCode) }}
                                @elseif ($promotion->type === 'final_price')
                                    {{ \App\Support\Currency::format((float) $promotion->value, $appCurrencyCode) }}
                                @elseif ($promotion->type === 'bonus_days')
                                    +{{ (int) $promotion->value }} días
                                @elseif (in_array($promotion->type, ['two_for_one', 'bring_friend'], true))
                                    {{ (float) ($promotion->value ?? 50) }}% desc.
                                @else
                                    {{ (float) $promotion->value }}
                                @endif
                            </td>
                            <td class="ui-text px-4 py-3">
                                {{ $promotion->starts_at?->toDateString() ?? 'Sin inicio' }} - {{ $promotion->ends_at?->toDateString() ?? 'Sin fin' }}
                            </td>
                            <td class="ui-text px-4 py-3">{{ $promotion->plan?->name ?? 'Todos' }}</td>
                            <td class="ui-text px-4 py-3">{{ $promotion->times_used }}{{ $promotion->max_uses ? ' / '.$promotion->max_uses : '' }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge :variant="$promotion->status === 'active' ? 'success' : 'muted'">{{ $promotion->status === 'active' ? 'Activo' : 'Inactivo' }}</x-ui.badge>
                            </td>
                            <td class="px-4 py-3">
                                @if ($isReadOnlyScope)
                                    <span class="text-xs font-semibold ui-muted">{{ $readOnlyActionLabel }}</span>
                                @else
                                    <div class="flex justify-end gap-1.5">
                                        <form method="POST" action="{{ route('plans.promotions.toggle', $promotion->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $promotion->status === 'active' ? 'inactive' : 'active' }}">
                                            <button type="submit" class="mini-action">
                                                {{ $promotion->status === 'active' ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('plans.promotions.destroy', $promotion->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="mini-action danger">Eliminar</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobalScope ? 9 : 8 }}" class="px-4 py-8 text-center text-sm font-semibold text-slate-400">No hay promociones creadas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-ui.card>

    @if (! $isReadOnlyScope && $canManagePromotions)
    <div id="promotion-create-modal" class="modal-shell" aria-hidden="true" aria-labelledby="promotion-create-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="promotion-create-title" class="ui-heading text-base font-black">Nueva promoción</h3>
                <button type="button" class="mini-action" data-close-modal="promotion-create-modal" aria-label="Cerrar modal promoción">Cerrar</button>
            </div>

            <form id="promotion-form" method="POST" action="{{ route('plans.promotions.store') }}" class="space-y-4 px-4 py-4">
                @csrf
                <input type="hidden" name="promotion_form" value="1">

                <div class="space-y-2">
                    <p class="ui-muted text-xs font-bold uppercase tracking-wide">Plantillas rápidas</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="chip-btn js-promo-template"
                                data-name="San Valentin 2x1"
                                data-type="two_for_one"
                                data-value="50"
                                data-description="Promo por temporada. Aplica 50% de descuento.">
                            San Valentin 2x1
                        </button>
                        <button type="button" class="chip-btn js-promo-template"
                                data-name="Trae a un amigo"
                                data-type="bring_friend"
                                data-value="50"
                                data-description="Promoción por referido. Aplica 50% de descuento al registro.">
                            Trae a un amigo
                        </button>
                        <button type="button" class="chip-btn js-promo-template"
                                data-name="Descuento fin de mes"
                                data-type="percentage"
                                data-value="20"
                                data-description="Promo de cierre de mes.">
                            Fin de mes -20%
                        </button>
                        <button type="button" class="chip-btn js-promo-template"
                                data-name="Semana premium"
                                data-type="bonus_days"
                                data-value="7"
                                data-description="Otorga 7 días extra sobre el plan.">
                            +7 días extra
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <label class="space-y-1 text-sm font-semibold ui-muted lg:col-span-2">
                        <span class="text-xs uppercase tracking-wide">Nombre promoción</span>
                        <input type="text" id="promo-name" name="name" class="ui-input" required maxlength="120" placeholder="Ej: San Valentin 2x1"
                               value="{{ old('name') }}">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Estado</span>
                        <select id="promo-status" name="status" class="ui-input">
                            <option value="active" @selected(old('status', 'active') === 'active')>Activo</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactivo</option>
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Tipo</span>
                        <select id="promo-type" name="type" class="ui-input" required>
                            <option value="percentage" @selected(old('type', 'percentage') === 'percentage')>Descuento porcentual</option>
                            <option value="fixed" @selected(old('type') === 'fixed')>Descuento monto fijo</option>
                            <option value="final_price" @selected(old('type') === 'final_price')>Precio final fijo</option>
                            <option value="bonus_days" @selected(old('type') === 'bonus_days')>Días extra</option>
                            <option value="two_for_one" @selected(old('type') === 'two_for_one')>2x1</option>
                            <option value="bring_friend" @selected(old('type') === 'bring_friend')>Trae a un amigo</option>
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide" id="promo-value-label">Valor</span>
                        <input type="number" id="promo-value" name="value" class="ui-input" min="0" step="0.01" value="{{ old('value', '0') }}">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Aplicar a plan</span>
                        <select id="promo-plan-id" name="plan_id" class="ui-input">
                            <option value="">Todos los planes</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected((string) old('plan_id') === (string) $plan->id)>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Fecha inicio</span>
                        <input type="date" id="promo-starts-at" name="starts_at" class="ui-input" value="{{ old('starts_at') }}">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Fecha fin</span>
                        <input type="date" id="promo-ends-at" name="ends_at" class="ui-input" value="{{ old('ends_at') }}">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Límite de usos</span>
                        <input type="number" id="promo-max-uses" name="max_uses" class="ui-input" min="1" step="1" placeholder="Ilimitado" value="{{ old('max_uses') }}">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted lg:col-span-3">
                        <span class="text-xs uppercase tracking-wide">Descripción</span>
                        <textarea id="promo-description" name="description" rows="2" class="ui-input" placeholder="Nota interna para recepción o caja.">{{ old('description') }}</textarea>
                    </label>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="ui-muted text-xs" id="promo-help-text">Tip: usa nombre claro y vigencia para evitar cobros equivocados.</p>
                    <div class="flex items-center gap-2">
                        <button type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold" data-close-modal="promotion-create-modal">Cancelar</button>
                        <button id="promo-submit" type="submit" class="ui-button ui-button-primary px-4 py-2 text-xs font-black">
                            <span class="js-submit-label">Guardar promoción</span>
                            <span class="js-submit-loading hidden">Guardando...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    @else
    <x-ui.card>
        <div class="space-y-3">
            <h2 class="ui-heading text-lg font-black">Promociones comerciales</h2>
            <p class="ui-muted text-sm">
                Este módulo no está disponible en tu plan actual. Para habilitar promociones cambia al Plan profesional o superior.
            </p>
        </div>
    </x-ui.card>
    @endif

    <div id="plan-edit-modal" class="modal-shell" aria-hidden="true" aria-labelledby="plan-edit-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="plan-edit-title" class="ui-heading text-base font-black">Editar plan</h3>
                <button type="button" class="mini-action" data-close-modal="plan-edit-modal" aria-label="Cerrar modal editar">Cerrar</button>
            </div>
            <form id="edit-plan-form" method="POST" action="#" class="space-y-3 px-4 py-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span class="text-xs uppercase tracking-wide">Nombre</span>
                    <input id="edit-plan-name" type="text" name="name" required minlength="3" class="ui-input" aria-label="Editar nombre">
                </label>
                <div class="grid gap-3 md:grid-cols-3">
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Tipo duración</span>
                        <select id="edit-plan-duration-unit" name="duration_unit" class="ui-input" aria-label="Editar tipo de duración">
                            <option value="days">Por días exactos</option>
                            <option value="months">Por meses calendario</option>
                        </select>
                    </label>
                    <label id="edit-plan-duration-days-row" class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Duración (días)</span>
                        <input id="edit-plan-duration" type="number" name="duration_days" min="1" step="1" required class="ui-input" aria-label="Editar duración">
                    </label>
                    <label id="edit-plan-duration-months-row" class="hidden space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Duración (meses)</span>
                        <input id="edit-plan-duration-months" type="number" name="duration_months" min="1" step="1" class="ui-input" aria-label="Editar duración meses">
                    </label>
                    <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-3">
                        <span class="text-xs uppercase tracking-wide">Precio</span>
                        <input id="edit-plan-price" type="number" name="price" min="0" step="0.01" required class="ui-input" aria-label="Editar precio">
                    </label>
                </div>
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span class="text-xs uppercase tracking-wide">Estado</span>
                    <select id="edit-plan-status" name="status" class="ui-input" aria-label="Editar estado">
                        <option value="active">Activo</option>
                        <option value="inactive">Oculto</option>
                    </select>
                </label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold" data-close-modal="plan-edit-modal">Cancelar</button>
                    <button id="edit-plan-submit" type="submit" class="ui-button ui-button-primary px-4 py-2 text-xs font-black"><span class="js-submit-label">Guardar cambios</span><span class="js-submit-loading hidden">Guardando...</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="plan-delete-modal" class="modal-shell" aria-hidden="true" aria-labelledby="plan-delete-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="plan-delete-title" class="ui-heading text-base font-black">Eliminar plan</h3>
                <button type="button" class="mini-action" data-close-modal="plan-delete-modal" aria-label="Cerrar modal eliminar">Cerrar</button>
            </div>
            <form id="delete-plan-form" method="POST" action="#" class="space-y-4 px-4 py-4">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <p class="ui-text text-sm">Vas a eliminar <strong id="delete-plan-name" class="text-rose-500">este plan</strong>. Esta acción no se puede deshacer.</p>
                <div class="flex justify-end gap-2">
                    <button type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold" data-close-modal="plan-delete-modal">Cancelar</button>
                    <button id="delete-plan-submit" type="submit" class="ui-button ui-button-danger px-4 py-2 text-xs font-black"><span class="js-submit-label">Eliminar</span><span class="js-submit-loading hidden">Eliminando...</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="plan-toggle-modal" class="modal-shell" aria-hidden="true" aria-labelledby="plan-toggle-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="plan-toggle-title" class="ui-heading text-base font-black">Cambiar estado</h3>
                <button type="button" class="mini-action" data-close-modal="plan-toggle-modal" aria-label="Cerrar modal cambiar estado">Cerrar</button>
            </div>
            <form id="toggle-plan-form" method="POST" action="#" class="space-y-4 px-4 py-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="status" id="toggle-plan-status" value="inactive">
                <p class="ui-text text-sm" id="toggle-plan-message">Confirma actualizar estado del plan.</p>
                <div class="flex justify-end gap-2">
                    <button type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold" data-close-modal="plan-toggle-modal">Cancelar</button>
                    <button id="toggle-plan-submit" type="submit" class="ui-button ui-button-primary px-4 py-2 text-xs font-black"><span class="js-submit-label">Confirmar</span><span class="js-submit-loading hidden">Actualizando...</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const currencySymbol = @json((string) $appCurrencySymbol);
    const routeUpdateTemplate = @json($updateRouteTemplate);
    const routeDestroyTemplate = @json($destroyRouteTemplate);
    const routeToggleTemplate = @json($toggleRouteTemplate);
    const openPromotionModalOnLoad = @json($openPromotionModal);

    const createForm = document.getElementById('create-plan-form');
    const promotionForm = document.getElementById('promotion-form');
    const nameInput = document.getElementById('plan-name');
    const durationUnitInput = document.getElementById('plan-duration-unit');
    const durationInput = document.getElementById('plan-duration');
    const durationMonthsInput = document.getElementById('plan-duration-months');
    const durationPresetsRow = document.getElementById('duration-presets-row');
    const durationDaysRow = document.getElementById('plan-duration-days-row');
    const durationMonthsRow = document.getElementById('plan-duration-months-row');
    const priceInput = document.getElementById('plan-price');
    const statusInput = document.getElementById('plan-status');
    const priceVisual = document.getElementById('price-visual');
    const previewName = document.getElementById('preview-name');
    const previewDuration = document.getElementById('preview-duration');
    const previewPrice = document.getElementById('preview-price');
    const previewStatus = document.getElementById('preview-status');
    const durationChips = Array.from(document.querySelectorAll('.js-duration-chip'));
    const plansSearchInput = document.getElementById('plans-search');
    const plansStatusFilter = document.getElementById('plans-status-filter');
    const plansTableBody = document.getElementById('plans-table-body');
    const plansCount = document.getElementById('plans-count');
    const alertContainer = document.getElementById('plans-alert-container');
    const promoType = document.getElementById('promo-type');
    const promoValue = document.getElementById('promo-value');
    const promoValueLabel = document.getElementById('promo-value-label');
    const promoHelpText = document.getElementById('promo-help-text');
    const promoTemplateButtons = Array.from(document.querySelectorAll('.js-promo-template'));
    const promoNameInput = document.getElementById('promo-name');
    const promoDescription = document.getElementById('promo-description');
    const openPromotionModalBtn = document.getElementById('open-promotion-modal-btn');

    const setButtonLoading = (button, loading) => {
        if (!button) return;
        const normalLabel = button.querySelector('.js-submit-label');
        const loadingLabel = button.querySelector('.js-submit-loading');
        button.disabled = loading;
        normalLabel?.classList.toggle('hidden', loading);
        loadingLabel?.classList.toggle('hidden', !loading);
    };

    const addAlert = (message, type = 'success') => {
        if (!alertContainer) return;
        const node = document.createElement('div');
        node.className = type === 'success' ? 'ui-alert ui-alert-success' : 'ui-alert ui-alert-danger';
        node.textContent = message;
        alertContainer.prepend(node);
        setTimeout(() => {
            node.classList.add('opacity-0', 'transition');
            setTimeout(() => node.remove(), 250);
        }, 3200);
    };

    const normalizePrice = (value) => {
        const number = Number(value);
        return Number.isFinite(number) && number >= 0 ? number : 0;
    };

    const formatMoney = (value) => `${currencySymbol}${normalizePrice(value).toFixed(2)}`;
    const normalizeDurationUnit = (value) => String(value || '').toLowerCase() === 'months' ? 'months' : 'days';
    const durationLabel = (unit, days, months) => {
        if (normalizeDurationUnit(unit) === 'months') {
            const value = Math.max(1, Number(months || 1));
            return `${value} ${value === 1 ? 'mes' : 'meses'}`;
        }
        const value = Math.max(1, Number(days || 1));
        return `${value} ${value === 1 ? 'día' : 'días'}`;
    };
    const syncDerivedDaysFromMonths = () => {
        if (!durationUnitInput || !durationInput || !durationMonthsInput) return;
        if (normalizeDurationUnit(durationUnitInput.value) !== 'months') return;
        const months = Math.max(1, Number(durationMonthsInput.value || 1));
        durationInput.value = String(months * 30);
    };
    const syncCreateDurationVisibility = () => {
        const isDays = normalizeDurationUnit(durationUnitInput?.value) === 'days';
        durationDaysRow?.classList.toggle('hidden', !isDays);
        durationMonthsRow?.classList.toggle('hidden', isDays);
        if (durationInput) durationInput.required = isDays;
        if (durationMonthsInput) durationMonthsInput.required = !isDays;
        if (durationPresetsRow) {
            durationPresetsRow.classList.toggle('opacity-50', !isDays);
        }
        durationChips.forEach((chip) => {
            chip.disabled = !isDays;
            chip.classList.toggle('cursor-not-allowed', !isDays);
        });
        if (isDays) {
            if (durationInput && Number(durationInput.value) < 1) durationInput.value = '1';
            return;
        }
        if (durationMonthsInput && Number(durationMonthsInput.value) < 1) durationMonthsInput.value = '1';
        syncDerivedDaysFromMonths();
    };

    const updatePreview = () => {
        const name = (nameInput?.value || '').trim();
        const durationUnit = normalizeDurationUnit(durationUnitInput?.value);
        const durationDays = Math.max(1, Number(durationInput?.value || 1));
        const durationMonths = Math.max(1, Number(durationMonthsInput?.value || 1));
        const price = normalizePrice(priceInput?.value || 0);
        const statusText = (statusInput?.value || 'active') === 'active' ? 'Activo' : 'Oculto';

        if (previewName) previewName.textContent = name || 'Nombre del plan';
        if (previewDuration) previewDuration.textContent = durationLabel(durationUnit, durationDays, durationMonths);
        if (previewPrice) previewPrice.textContent = formatMoney(price);
        if (priceVisual) priceVisual.textContent = formatMoney(price);
        if (previewStatus) {
            previewStatus.textContent = statusText;
            previewStatus.className = statusText === 'Activo'
                ? 'inline-flex rounded-full border border-emerald-400/40 bg-emerald-500/15 px-2.5 py-1 text-xs font-bold text-emerald-200'
                : 'inline-flex rounded-full border border-slate-300/35 bg-slate-700/45 px-2.5 py-1 text-xs font-bold text-slate-200';
        }
    };

    const syncDurationChips = () => {
        if (normalizeDurationUnit(durationUnitInput?.value) !== 'days') {
            durationChips.forEach((chip) => chip.classList.remove('active'));
            return;
        }
        const val = String(Math.max(1, Number(durationInput?.value || 1)));
        let matched = false;
        durationChips.forEach((chip) => {
            const days = chip.getAttribute('data-days');
            const active = days === val;
            if (active) matched = true;
            chip.classList.toggle('active', active);
        });
        const customChip = durationChips.find((chip) => chip.getAttribute('data-days') === 'custom');
        customChip?.classList.toggle('active', !matched);
    };

    durationChips.forEach((chip) => {
        chip.addEventListener('click', () => {
            const days = chip.getAttribute('data-days');
            if (!durationInput || !days) return;
            if (durationUnitInput) durationUnitInput.value = 'days';
            syncCreateDurationVisibility();
            if (days !== 'custom') durationInput.value = days;
            durationInput.focus();
            updatePreview();
            syncDurationChips();
        });
    });

    [nameInput, durationUnitInput, durationInput, durationMonthsInput, priceInput, statusInput].forEach((input) => {
        input?.addEventListener('input', () => {
            if (normalizeDurationUnit(durationUnitInput?.value) === 'days') {
                if (durationInput && Number(durationInput.value) < 1) durationInput.value = '1';
            } else if (durationMonthsInput && Number(durationMonthsInput.value) < 1) {
                durationMonthsInput.value = '1';
                syncDerivedDaysFromMonths();
            }
            if (priceInput && Number(priceInput.value) < 0) priceInput.value = '0.00';
            if (input === durationUnitInput) syncCreateDurationVisibility();
            updatePreview();
            syncDurationChips();
        });
        input?.addEventListener('change', () => {
            if (input === durationUnitInput) syncCreateDurationVisibility();
            updatePreview();
            syncDurationChips();
        });
    });

    createForm?.addEventListener('submit', () => {
        syncDerivedDaysFromMonths();
        setButtonLoading(document.getElementById('create-plan-submit'), true);
    });

    const syncPromotionTypeUi = () => {
        if (!promoType || !promoValueLabel || !promoHelpText || !promoValue) return;
        const type = String(promoType.value || 'percentage');

        if (type === 'percentage') {
            promoValueLabel.textContent = 'Porcentaje (%)';
            promoHelpText.textContent = 'Ejemplo: 20 = descuento del 20%.';
            promoValue.step = '0.01';
        } else if (type === 'fixed') {
            promoValueLabel.textContent = 'Monto descuento';
            promoHelpText.textContent = 'Monto fijo que se resta al precio del plan.';
            promoValue.step = '0.01';
        } else if (type === 'final_price') {
            promoValueLabel.textContent = 'Precio final';
            promoHelpText.textContent = 'Precio final que pagará el cliente con promoción.';
            promoValue.step = '0.01';
        } else if (type === 'bonus_days') {
            promoValueLabel.textContent = 'Días extra';
            promoHelpText.textContent = 'Suma días a la duración del plan sin cambiar precio.';
            promoValue.step = '1';
        } else if (type === 'two_for_one') {
            promoValueLabel.textContent = 'Descuento (%)';
            promoHelpText.textContent = '2x1 simplificado: por defecto 50% de descuento.';
            promoValue.step = '0.01';
            if (Number(promoValue.value || 0) <= 0) {
                promoValue.value = '50';
            }
        } else if (type === 'bring_friend') {
            promoValueLabel.textContent = 'Descuento referido (%)';
            promoHelpText.textContent = 'Trae a un amigo: por defecto 50% de descuento.';
            promoValue.step = '0.01';
            if (Number(promoValue.value || 0) <= 0) {
                promoValue.value = '50';
            }
        }
    };

    promoType?.addEventListener('change', syncPromotionTypeUi);
    promoTemplateButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const type = String(button.getAttribute('data-type') || 'percentage');
            const value = String(button.getAttribute('data-value') || '0');
            const name = String(button.getAttribute('data-name') || '');
            const description = String(button.getAttribute('data-description') || '');
            if (promoType) promoType.value = type;
            if (promoValue) promoValue.value = value;
            if (promoNameInput) promoNameInput.value = name;
            if (promoDescription) promoDescription.value = description;
            syncPromotionTypeUi();
            promoNameInput?.focus();
        });
    });

    promotionForm?.addEventListener('submit', () => setButtonLoading(document.getElementById('promo-submit'), true));

    const rows = () => Array.from(plansTableBody?.querySelectorAll('tr[data-plan-id]') || []);
    const applyTableFilters = () => {
        const q = String(plansSearchInput?.value || '').trim().toLowerCase();
        const status = String(plansStatusFilter?.value || 'all');
        let visible = 0;
        rows().forEach((row) => {
            const rowName = String(row.getAttribute('data-plan-name') || '');
            const rowId = String(row.getAttribute('data-plan-id') || '');
            const rowStatus = String(row.getAttribute('data-plan-status') || '');
            const okQ = q === '' || rowName.includes(q) || rowId.includes(q);
            const okS = status === 'all' || rowStatus === status;
            const show = okQ && okS;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        if (plansCount) plansCount.textContent = `${visible} plan${visible === 1 ? '' : 'es'}`;
    };

    plansSearchInput?.addEventListener('input', applyTableFilters);
    plansStatusFilter?.addEventListener('change', applyTableFilters);

    const routeFromTemplate = (template, id) => template ? template.replace('__PLAN__', String(id)) : '';
    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };
    const closeModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        const hasOpenModal = document.querySelector('.modal-shell.is-open');
        if (!hasOpenModal) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => closeModal(String(button.getAttribute('data-close-modal'))));
    });

    document.querySelectorAll('.modal-shell').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        ['promotion-create-modal', 'plan-edit-modal', 'plan-delete-modal', 'plan-toggle-modal'].forEach(closeModal);
    });

    openPromotionModalBtn?.addEventListener('click', () => openModal('promotion-create-modal'));

    const editForm = document.getElementById('edit-plan-form');
    const editName = document.getElementById('edit-plan-name');
    const editDurationUnit = document.getElementById('edit-plan-duration-unit');
    const editDuration = document.getElementById('edit-plan-duration');
    const editDurationMonths = document.getElementById('edit-plan-duration-months');
    const editDurationDaysRow = document.getElementById('edit-plan-duration-days-row');
    const editDurationMonthsRow = document.getElementById('edit-plan-duration-months-row');
    const editPrice = document.getElementById('edit-plan-price');
    const editStatus = document.getElementById('edit-plan-status');
    const syncEditDurationVisibility = () => {
        const isDays = normalizeDurationUnit(editDurationUnit?.value) === 'days';
        editDurationDaysRow?.classList.toggle('hidden', !isDays);
        editDurationMonthsRow?.classList.toggle('hidden', isDays);
        if (editDuration) editDuration.required = isDays;
        if (editDurationMonths) editDurationMonths.required = !isDays;
        if (!isDays && editDurationMonths && Number(editDurationMonths.value) < 1) {
            editDurationMonths.value = '1';
        }
    };

    editDurationUnit?.addEventListener('change', syncEditDurationVisibility);

    document.querySelectorAll('.js-edit-plan').forEach((button) => {
        button.addEventListener('click', () => {
            const planId = button.getAttribute('data-plan-id');
            if (!planId || !editForm) return;
            if (!routeUpdateTemplate) {
                addAlert('Falta route plans.update en backend para editar.', 'error');
                return;
            }
            editForm.action = routeFromTemplate(routeUpdateTemplate, planId);
            if (editName) editName.value = String(button.getAttribute('data-plan-name-value') || '');
            if (editDurationUnit) editDurationUnit.value = normalizeDurationUnit(button.getAttribute('data-plan-duration-unit-value'));
            if (editDuration) editDuration.value = String(button.getAttribute('data-plan-duration-value') || '30');
            if (editDurationMonths) editDurationMonths.value = String(button.getAttribute('data-plan-duration-months-value') || '1');
            if (editPrice) editPrice.value = String(button.getAttribute('data-plan-price-value') || '0.00');
            if (editStatus) editStatus.value = String(button.getAttribute('data-plan-status-value') || 'active');
            syncEditDurationVisibility();
            openModal('plan-edit-modal');
        });
    });
    editForm?.addEventListener('submit', () => {
        if (normalizeDurationUnit(editDurationUnit?.value) === 'months' && editDuration && editDurationMonths) {
            const months = Math.max(1, Number(editDurationMonths.value || 1));
            editDuration.value = String(months * 30);
        }
        setButtonLoading(document.getElementById('edit-plan-submit'), true);
    });

    document.querySelectorAll('.js-duplicate-plan').forEach((button) => {
        button.addEventListener('click', () => {
            if (nameInput) nameInput.value = `${String(button.getAttribute('data-plan-name-value') || '').trim()} copia`.trim();
            if (durationUnitInput) durationUnitInput.value = normalizeDurationUnit(button.getAttribute('data-plan-duration-unit-value'));
            if (durationInput) durationInput.value = String(button.getAttribute('data-plan-duration-value') || '30');
            if (durationMonthsInput) durationMonthsInput.value = String(button.getAttribute('data-plan-duration-months-value') || '1');
            if (priceInput) priceInput.value = String(button.getAttribute('data-plan-price-value') || '0.00');
            if (statusInput) statusInput.value = String(button.getAttribute('data-plan-status-value') || 'active');
            syncCreateDurationVisibility();
            updatePreview();
            syncDurationChips();
            createForm?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            nameInput?.focus();
            addAlert('Plan cargado para duplicar. Revisa y guarda.', 'success');
        });
    });

    const deleteForm = document.getElementById('delete-plan-form');
    const deleteName = document.getElementById('delete-plan-name');
    document.querySelectorAll('.js-delete-plan').forEach((button) => {
        button.addEventListener('click', () => {
            const planId = button.getAttribute('data-plan-id');
            if (!planId || !deleteForm) return;
            if (!routeDestroyTemplate) {
                addAlert('Falta route plans.destroy en backend para eliminar.', 'error');
                return;
            }
            deleteForm.action = routeFromTemplate(routeDestroyTemplate, planId);
            if (deleteName) deleteName.textContent = String(button.getAttribute('data-plan-name-value') || 'este plan');
            openModal('plan-delete-modal');
        });
    });
    deleteForm?.addEventListener('submit', () => setButtonLoading(document.getElementById('delete-plan-submit'), true));

    const toggleForm = document.getElementById('toggle-plan-form');
    const toggleStatus = document.getElementById('toggle-plan-status');
    const toggleMsg = document.getElementById('toggle-plan-message');
    document.querySelectorAll('.js-toggle-plan').forEach((button) => {
        button.addEventListener('click', () => {
            const planId = button.getAttribute('data-plan-id');
            const currentStatus = String(button.getAttribute('data-current-status') || 'inactive');
            const nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const planName = String(button.getAttribute('data-plan-name-value') || 'el plan');
            if (!planId || !toggleForm || !toggleStatus) return;
            if (!routeToggleTemplate) {
                addAlert('Falta route plans.toggle en backend para activar/desactivar.', 'error');
                return;
            }
            toggleForm.action = routeFromTemplate(routeToggleTemplate, planId);
            toggleStatus.value = nextStatus;
            if (toggleMsg) toggleMsg.textContent = `Confirma ${nextStatus === 'active' ? 'activar' : 'desactivar'} "${planName}".`;
            openModal('plan-toggle-modal');
        });
    });
    toggleForm?.addEventListener('submit', () => setButtonLoading(document.getElementById('toggle-plan-submit'), true));

    document.querySelectorAll('[data-accordion-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = String(button.getAttribute('data-accordion-toggle') || '');
            const target = document.getElementById(targetId);
            if (!target) return;
            const opened = !target.classList.contains('hidden');
            target.classList.toggle('hidden', opened);
            button.setAttribute('aria-expanded', opened ? 'false' : 'true');
            const icon = button.querySelector('[data-accordion-icon]');
            if (icon) icon.textContent = opened ? 'Mostrar' : 'Ocultar';
        });
    });

    if (window.bootstrap && typeof window.bootstrap.Tooltip === 'function') {
        document.querySelectorAll('[title]').forEach((node) => new window.bootstrap.Tooltip(node));
    }

    syncCreateDurationVisibility();
    syncEditDurationVisibility();
    updatePreview();
    syncDurationChips();
    applyTableFilters();
    syncPromotionTypeUi();
    if (openPromotionModalOnLoad) {
        openModal('promotion-create-modal');
    }
})();
</script>
@endpush

