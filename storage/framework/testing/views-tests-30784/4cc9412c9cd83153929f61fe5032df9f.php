

<?php $__env->startSection('title', 'Planes'); ?>
<?php $__env->startSection('page-title', 'Planes'); ?>

<?php $__env->startPush('styles'); ?>
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
    --pp-preview-border: rgb(16 185 129 / .24);
    --pp-preview-bg: radial-gradient(circle at top right, rgb(16 185 129 / .18), transparent 34%), linear-gradient(150deg, rgb(15 23 42 / .96), rgb(30 41 59 / .84));
    --pp-preview-shadow: 0 20px 42px rgb(15 23 42 / .22);
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

.plans-page .plan-builder-grid {
    align-items: start;
}
.plans-page .plan-preview-sticky {
    position: sticky;
    top: calc(5.4rem + env(safe-area-inset-top));
}
.plans-page .plan-preview-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid rgb(148 163 184 / .25);
    background: rgb(15 23 42 / .4);
    border-radius: .8rem;
    padding: .55rem .75rem;
}
.theme-light .plans-page .plan-preview-row {
    border-color: rgb(148 163 184 / .4);
    background: rgb(255 255 255 / .75);
}
.plans-page .plan-create-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: .55rem;
}
.plans-page .plan-create-note {
    font-size: .72rem;
    line-height: 1.25;
    color: rgb(148 163 184);
}
.plans-page .plans-filter-grid {
    display: grid;
    gap: .75rem;
}
@media (min-width: 1280px) {
    .plans-page .plans-filter-grid {
        grid-template-columns: minmax(0, 1fr) 16rem auto auto;
        align-items: end;
    }
}
.plans-page .plan-actions-modal-summary {
    border: 1px solid rgb(148 163 184 / .28);
    background: rgb(15 23 42 / .45);
    border-radius: .9rem;
    padding: .8rem .9rem;
}
.plans-page .plan-actions-modal-grid {
    display: grid;
    gap: .55rem;
}
.plans-page .plan-actions-modal-grid .ui-action-button {
    justify-content: flex-start;
    width: 100%;
}
.plans-page .plan-promo-empty {
    border: 1px dashed rgb(148 163 184 / .5);
    border-radius: 1rem;
    padding: 1.4rem;
    text-align: center;
    background: rgb(15 23 42 / .22);
}
.plans-page .plans-surface-card {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    border-color: rgb(148 163 184 / .22);
    background: linear-gradient(162deg, rgb(255 255 255 / .98), rgb(248 250 252 / .95));
    box-shadow: 0 24px 44px -34px rgb(15 23 42 / .28), inset 0 1px 0 rgb(255 255 255 / .82);
    backdrop-filter: blur(10px);
}
.theme-dark .plans-page .plans-surface-card,
.dark .plans-page .plans-surface-card {
    border-color: rgb(71 85 105 / .76);
    background: linear-gradient(165deg, rgb(15 23 42 / .92), rgb(2 6 23 / .84));
    box-shadow: 0 28px 46px -34px rgb(2 8 23 / .88), inset 0 1px 0 rgb(255 255 255 / .04);
}
.plans-page .plans-surface-card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 1px;
    background: linear-gradient(90deg, rgb(255 255 255 / .78), transparent 74%);
    opacity: .9;
    pointer-events: none;
}
.theme-dark .plans-page .plans-surface-card::before,
.dark .plans-page .plans-surface-card::before {
    background: linear-gradient(90deg, rgb(255 255 255 / .08), transparent 74%);
}
.plans-page .plans-surface-card > * {
    position: relative;
    z-index: 1;
}
.plans-page .plans-form-shell,
.plans-page .plans-toolbar-shell,
.plans-page .plans-table-shell,
.plans-page .plans-readonly-shell,
.plans-page .plans-modal-section {
    border: 1px solid rgb(148 163 184 / .18);
    border-radius: 1rem;
    background: linear-gradient(160deg, rgb(255 255 255 / .76), rgb(241 245 249 / .84));
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .72);
}
.theme-dark .plans-page .plans-form-shell,
.theme-dark .plans-page .plans-toolbar-shell,
.theme-dark .plans-page .plans-table-shell,
.theme-dark .plans-page .plans-readonly-shell,
.theme-dark .plans-page .plans-modal-section,
.dark .plans-page .plans-form-shell,
.dark .plans-page .plans-toolbar-shell,
.dark .plans-page .plans-table-shell,
.dark .plans-page .plans-readonly-shell,
.dark .plans-page .plans-modal-section {
    border-color: rgb(71 85 105 / .54);
    background: linear-gradient(165deg, rgb(15 23 42 / .62), rgb(2 6 23 / .56));
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .04);
}
.plans-page .plans-form-shell,
.plans-page .plans-readonly-shell {
    padding: 1rem;
}
.plans-page .plans-toolbar-shell {
    padding: .85rem;
}
.plans-page .plans-table-shell {
    overflow: auto;
}
.plans-page .plans-table-shell .plans-table thead th {
    position: sticky;
    top: 0;
    z-index: 3;
    background: rgb(241 245 249 / .94);
    backdrop-filter: blur(4px);
}
.theme-dark .plans-page .plans-table-shell .plans-table thead th,
.dark .plans-page .plans-table-shell .plans-table thead th {
    background: rgb(15 23 42 / .94);
}
.plans-page .plans-empty-state {
    padding: 2rem 1rem;
    text-align: center;
    font-weight: 700;
    color: rgb(100 116 139);
}
.theme-dark .plans-page .plans-empty-state,
.dark .plans-page .plans-empty-state {
    color: rgb(148 163 184);
}
.plans-page .plans-promo-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: .85rem;
}
.plans-page .plans-modal-form {
    display: grid;
    gap: 1rem;
}
.plans-page .plans-modal-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: .65rem;
    padding-top: .8rem;
    border-top: 1px solid rgb(148 163 184 / .16);
}
.theme-dark .plans-page .plans-modal-actions,
.dark .plans-page .plans-modal-actions {
    border-top-color: rgb(71 85 105 / .46);
}
.plans-page .plans-control-shell {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    border: 1px solid rgb(163 230 53 / .22);
    border-radius: 1.22rem;
    background:
        radial-gradient(circle at top right, rgb(163 230 53 / .16), transparent 34%),
        linear-gradient(150deg, rgb(255 255 255 / .99), rgb(248 250 252 / .95));
    box-shadow: 0 28px 56px -40px rgb(15 23 42 / .5);
    backdrop-filter: blur(14px);
    padding: 1.05rem;
}
.theme-dark .plans-page .plans-control-shell {
    border-color: rgb(163 230 53 / .24);
    background:
        radial-gradient(circle at top right, rgb(163 230 53 / .14), transparent 34%),
        linear-gradient(160deg, rgb(2 6 23 / .84), rgb(15 23 42 / .62));
    box-shadow: 0 30px 58px -42px rgb(2 8 23 / .92);
}
.plans-page .plans-control-shell::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgb(255 255 255 / .72), transparent);
    opacity: .8;
    pointer-events: none;
}
.plans-page .plans-control-shell::after {
    content: '';
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(95deg, transparent, rgb(163 230 53 / .05), transparent);
}
.plans-page .plans-control-grid {
    display: grid;
    gap: 1.05rem;
    position: relative;
    z-index: 1;
}
.plans-page .plans-control-copy {
    max-width: 48rem;
}
.plans-page .plans-control-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    font-size: .68rem;
    font-weight: 900;
    letter-spacing: .17em;
    text-transform: uppercase;
    color: rgb(77 124 15 / .94);
}
.theme-dark .plans-page .plans-control-kicker {
    color: rgb(217 249 157 / .94);
}
.plans-page .plans-control-kicker::before {
    content: '';
    width: .52rem;
    height: .52rem;
    border-radius: 999px;
    background: rgb(132 204 22 / .94);
    box-shadow: 0 0 0 6px rgb(132 204 22 / .12);
}
.plans-page .plans-control-heading {
    margin-top: .78rem;
    font-size: clamp(1.14rem, 1.85vw, 1.46rem);
    line-height: 1.08;
    letter-spacing: -.035em;
    font-weight: 900;
    color: rgb(15 23 42 / .97);
}
.theme-dark .plans-page .plans-control-heading {
    color: rgb(241 245 249 / .98);
}
.plans-page .plans-control-summary {
    margin-top: .5rem;
    font-size: .88rem;
    line-height: 1.58;
    color: rgb(71 85 105 / .92);
}
.theme-dark .plans-page .plans-control-summary {
    color: rgb(148 163 184 / .9);
}
.plans-page .plans-control-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .55rem;
    align-items: center;
}
.plans-page .plans-control-actions .ui-button {
    min-height: 2.72rem;
}
.plans-page .plans-control-priority-grid {
    display: grid;
    gap: .75rem;
}
.plans-page .plans-control-priority {
    position: relative;
    overflow: hidden;
    border-radius: 1.05rem;
    border: 1px solid rgb(148 163 184 / .24);
    background: linear-gradient(180deg, rgb(255 255 255 / .9), rgb(248 250 252 / .74));
    box-shadow: 0 18px 30px -28px rgb(15 23 42 / .28);
    min-height: 7rem;
    padding: .9rem .95rem;
}
.theme-dark .plans-page .plans-control-priority {
    border-color: rgb(148 163 184 / .18);
    background: linear-gradient(160deg, rgb(15 23 42 / .74), rgb(15 23 42 / .54));
    box-shadow: 0 20px 34px -28px rgb(2 8 23 / .9);
}
.plans-page .plans-control-priority::before {
    content: '';
    position: absolute;
    left: .9rem;
    right: .9rem;
    top: 0;
    height: 2px;
    border-radius: 999px;
    background: rgb(148 163 184 / .22);
}
.plans-page .plans-control-priority[data-tone='warning']::before {
    background: linear-gradient(90deg, rgb(245 158 11 / .9), rgb(245 158 11 / .24));
}
.plans-page .plans-control-priority[data-tone='success']::before {
    background: linear-gradient(90deg, rgb(16 185 129 / .9), rgb(16 185 129 / .24));
}
.plans-page .plans-control-priority[data-tone='info']::before {
    background: linear-gradient(90deg, rgb(6 182 212 / .9), rgb(6 182 212 / .24));
}
.plans-page .plans-control-priority-label {
    font-size: .67rem;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgb(71 85 105 / .92);
}
.theme-dark .plans-page .plans-control-priority-label {
    color: rgb(148 163 184 / .9);
}
.plans-page .plans-control-priority-value {
    margin-top: .42rem;
    font-size: 1.46rem;
    line-height: 1;
    font-weight: 900;
    letter-spacing: -.03em;
    color: rgb(15 23 42 / .97);
}
.theme-dark .plans-page .plans-control-priority-value {
    color: rgb(248 250 252 / .98);
}
.plans-page .plans-control-priority-note {
    margin-top: .4rem;
    font-size: .75rem;
    line-height: 1.45;
    color: rgb(71 85 105 / .9);
}
.theme-dark .plans-page .plans-control-priority-note {
    color: rgb(148 163 184 / .88);
}
.theme-light .plans-page .plan-promo-empty {
    background: rgb(248 250 252 / .85);
}
@media (min-width: 768px) {
    .plans-page .plans-control-priority-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 1279px) {
    .plans-page .plan-preview-sticky {
        position: static;
    }
}
@media (min-width: 1280px) {
    .plans-page .plans-control-grid {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: start;
    }
    .plans-page .plans-control-priority-grid {
        grid-column: 1 / -1;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}
@media (max-width: 640px) {
    .plans-page .plans-control-actions .ui-button,
    .plans-page .plan-create-actions .ui-button,
    .plans-page .plans-filter-grid #plans-clear-filters,
    .plans-page .plans-modal-actions .ui-button {
        width: 100%;
    }
}

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $planAccessService = app(\App\Services\PlanAccessService::class);
    $contextGym = (string) request()->route('contextGym');
    $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
    $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
    $isBranchContext = (bool) request()->attributes->get('gym_context_is_branch', false);
    $routeExtras = request()->query('pwa_mode') === 'standalone' ? ['pwa_mode' => 'standalone'] : [];
    $plansRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []) + $routeExtras;
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
    $planControlPlansDashboard = is_array($planControlPlansDashboard ?? null) ? $planControlPlansDashboard : null;
    $plansTableSubtitle = $planControlPlansDashboard
        ? 'Catalogo claro para vender y renovar sin salir de esta pantalla.'
        : 'Administra tus planes sin salir de esta pantalla.';
?>

<div class="plans-page space-y-5">
    <div id="plans-alert-container" class="space-y-2" aria-live="polite" aria-atomic="true">
        <?php if(session('status')): ?>
            <div class="ui-alert ui-alert-success"><?php echo e(session('status')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="ui-alert ui-alert-danger"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>
        <?php if($isReadOnlyScope): ?>
            <div class="ui-alert ui-alert-warning">
                <?php echo e($readOnlyMessage); ?>

            </div>
        <?php endif; ?>
    </div>

    <?php if($planControlPlansDashboard): ?>
        <?php
            $topPlanMemberships = (int) ($planControlPlansDashboard['top_plan_memberships'] ?? 0);
            $plansControlPriorities = [
                [
                    'label' => 'Planes activos',
                    'value' => (string) ($planControlPlansDashboard['active_plans'] ?? 0),
                    'note' => ((int) ($planControlPlansDashboard['active_plans'] ?? 0)) > 0
                        ? 'Listos para vender desde recepcion y clientes.'
                        : 'Activa al menos un plan para empezar a cobrar.',
                    'tone' => ((int) ($planControlPlansDashboard['active_plans'] ?? 0)) > 0 ? 'success' : 'warning',
                ],
                [
                    'label' => 'Catalogo total',
                    'value' => (string) ($planControlPlansDashboard['total_plans'] ?? 0),
                    'note' => ((int) ($planControlPlansDashboard['hidden_plans'] ?? 0)) > 0
                        ? (int) ($planControlPlansDashboard['hidden_plans'] ?? 0).' oculto(s) listos para futuras ventas.'
                        : 'No hay planes ocultos ahora mismo.',
                    'tone' => ((int) ($planControlPlansDashboard['hidden_plans'] ?? 0)) > 0 ? 'info' : 'success',
                ],
                [
                    'label' => 'Precio base',
                    'value' => $planControlPlansDashboard['base_price'] !== null
                        ? \App\Support\Currency::format((float) $planControlPlansDashboard['base_price'], $appCurrencyCode)
                        : 'Sin precio',
                    'note' => $planControlPlansDashboard['base_price'] !== null
                        ? 'Es la entrada mas baja visible de tu catalogo.'
                        : 'Crea tu primer plan para definir el precio de entrada.',
                    'tone' => $planControlPlansDashboard['base_price'] !== null ? 'info' : 'warning',
                ],
                [
                    'label' => 'Plan con movimiento',
                    'value' => (string) ($planControlPlansDashboard['top_plan_name'] ?? 'Sin ventas aun'),
                    'note' => $topPlanMemberships > 0
                        ? $topPlanMemberships.' membresia(s) asociadas a este plan.'
                        : 'Todavia no hay membresias registradas en un plan.',
                    'tone' => $topPlanMemberships > 0 ? 'success' : 'warning',
                ],
            ];
        ?>
        <section class="plans-control-shell">
            <div class="plans-control-grid">
                <div class="plans-control-copy">
                    <span class="plans-control-kicker">Plan Control / Planes</span>
                    <h2 class="plans-control-heading"><?php echo e($planControlPlansDashboard['headline'] ?? 'Catalogo claro y listo para vender desde una sola sede'); ?></h2>
                    <p class="plans-control-summary"><?php echo e($planControlPlansDashboard['summary'] ?? 'Ordena el catalogo para vender y renovar sin ruido.'); ?></p>
                </div>

                <div class="plans-control-actions">
                    <?php if(! $isReadOnlyScope): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => '#create-plan-form','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '#create-plan-form','variant' => 'primary']); ?>Nuevo plan <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $plansRouteParams),'variant' => ''.e($isReadOnlyScope ? 'primary' : 'secondary').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $plansRouteParams)),'variant' => ''.e($isReadOnlyScope ? 'primary' : 'secondary').'']); ?>Ir a clientes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $plansRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $plansRouteParams)),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </div>

                <div class="plans-control-priority-grid">
                    <?php $__currentLoopData = $plansControlPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="plans-control-priority" data-tone="<?php echo e($priority['tone']); ?>">
                            <p class="plans-control-priority-label"><?php echo e($priority['label']); ?></p>
                            <p class="plans-control-priority-value"><?php echo e($priority['value']); ?></p>
                            <p class="plans-control-priority-note"><?php echo e($priority['note']); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if(! $isReadOnlyScope): ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'plans-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'plans-surface-card']); ?>
        <div class="space-y-4">
            <div>
                <h2 class="ui-heading text-xl font-black">Crear plan</h2>
                <p class="ui-muted mt-1 text-sm">Define planes claros para ventas rápidas y control de membresías.</p>
            </div>

            <form id="create-plan-form" method="POST" action="<?php echo e(route('plans.store')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div class="plan-builder-grid grid gap-4 xl:grid-cols-[minmax(0,1.55fr)_minmax(280px,0.75fr)]">
                    <div class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span class="text-xs uppercase tracking-wide">Nombre</span>
                                <input id="plan-name" type="text" name="name" value="<?php echo e(old('name')); ?>" required minlength="3" class="ui-input" placeholder="Ej: Mensual Pro" aria-label="Nombre del plan">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs font-bold text-rose-300"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span class="text-xs uppercase tracking-wide">Estado</span>
                                <select id="plan-status" name="status" class="ui-input" aria-label="Estado del plan">
                                    <option value="active" <?php if($defaultStatus === 'active'): echo 'selected'; endif; ?>>Activo</option>
                                    <option value="inactive" <?php if($defaultStatus === 'inactive'): echo 'selected'; endif; ?>>Oculto</option>
                                </select>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs font-bold text-rose-300"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>
                        </div>

                        <div id="duration-presets-row" class="space-y-2 <?php echo e($defaultDurationUnit === 'months' ? 'opacity-50' : ''); ?>">
                            <p class="ui-muted text-xs font-bold uppercase tracking-wide">Duración rápida</p>
                            <div class="flex flex-wrap gap-2" role="group" aria-label="Presets de duración">
                                <?php $__currentLoopData = [1, 7, 15, 30, 60, 90]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button type="button" class="chip-btn js-duration-chip <?php echo e($defaultDurationUnit === 'months' ? 'cursor-not-allowed' : ''); ?>" data-days="<?php echo e($preset); ?>" <?php if($defaultDurationUnit === 'months'): echo 'disabled'; endif; ?>><?php echo e($preset); ?> días</button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" class="chip-btn js-duration-chip <?php echo e($defaultDurationUnit === 'months' ? 'cursor-not-allowed' : ''); ?>" data-days="custom" <?php if($defaultDurationUnit === 'months'): echo 'disabled'; endif; ?>>Personalizado</button>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span class="text-xs uppercase tracking-wide">Tipo duración</span>
                                <select id="plan-duration-unit" name="duration_unit" class="ui-input" aria-label="Tipo de duración">
                                    <option value="days" <?php if($defaultDurationUnit === 'days'): echo 'selected'; endif; ?>>Por días exactos</option>
                                    <option value="months" <?php if($defaultDurationUnit === 'months'): echo 'selected'; endif; ?>>Por meses calendario</option>
                                </select>
                            </label>

                            <label id="plan-duration-days-row" class="space-y-1 text-sm font-semibold ui-muted <?php echo e($defaultDurationUnit === 'months' ? 'hidden' : ''); ?>">
                                <span class="text-xs uppercase tracking-wide">Duración (días)</span>
                                <input id="plan-duration" type="number" name="duration_days" min="1" step="1" value="<?php echo e($defaultDurationDays); ?>" <?php if($defaultDurationUnit === 'days'): echo 'required'; endif; ?> class="ui-input" aria-label="Duración en días">
                                <?php $__errorArgs = ['duration_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs font-bold text-rose-300"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label id="plan-duration-months-row" class="space-y-1 text-sm font-semibold ui-muted <?php echo e($defaultDurationUnit === 'months' ? '' : 'hidden'); ?>">
                                <span class="text-xs uppercase tracking-wide">Duración (meses)</span>
                                <input id="plan-duration-months" type="number" name="duration_months" min="1" step="1" value="<?php echo e($defaultDurationMonths); ?>" <?php if($defaultDurationUnit === 'months'): echo 'required'; endif; ?> class="ui-input" aria-label="Duración en meses">
                                <?php $__errorArgs = ['duration_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs font-bold text-rose-300"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-3">
                                <span class="text-xs uppercase tracking-wide">Precio</span>
                                <div class="price-shell flex items-center gap-2 rounded-xl px-3 py-2">
                                    <span class="price-inline-symbol text-sm font-black"><?php echo e(trim((string) $appCurrencySymbol)); ?></span>
                                    <input id="plan-price" type="number" name="price" min="0" step="0.01" value="<?php echo e(old('price', '0.00')); ?>" required class="price-inline-input w-full bg-transparent text-sm font-semibold outline-none" aria-label="Precio del plan">
                                </div>
                                <p class="text-xs text-slate-400">Vista: <strong id="price-visual"><?php echo e(\App\Support\Currency::format((float) old('price', 0), $appCurrencyCode)); ?></strong></p>
                                <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs font-bold text-rose-300"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                        <input type="number" name="grace_days" min="0" step="1" value="<?php echo e(old('grace_days', 0)); ?>" class="ui-input" aria-label="Días de tolerancia">
                                    </label>
                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span class="text-xs uppercase tracking-wide">Límite ingresos/día</span>
                                        <input type="number" name="daily_checkin_limit" min="1" step="1" value="<?php echo e(old('daily_checkin_limit')); ?>" class="ui-input" placeholder="Ilimitado" aria-label="Límite de ingresos por día">
                                    </label>
                                    <label class="plans-advanced-switch flex items-center gap-2 rounded-xl border px-3 py-3">
                                        <input type="checkbox" name="quick_sale_enabled" value="1" class="h-4 w-4" <?php if(old('quick_sale_enabled', '1') === '1'): echo 'checked'; endif; ?> aria-label="Mostrar en venta rápida">
                                        <span class="plans-advanced-switch-text text-sm font-semibold">Mostrar en venta rápida</span>
                                    </label>
                                </div>
                                
                            </div>
                        </div>

                        <div class="plan-create-actions pt-1">
                            <button id="create-plan-submit" type="submit" class="ui-button ui-button-primary px-5 py-2.5 text-sm font-black">
                                <span class="js-submit-label">Guardar plan</span>
                                <span class="js-submit-loading hidden">Guardando...</span>
                            </button>
                            <button id="create-plan-reset" type="button" class="ui-button ui-button-muted px-4 py-2.5 text-sm font-bold">Limpiar</button>
                            <p class="plan-create-note">Se guarda al instante y aparecerá en la tabla de planes.</p>
                        </div>
                    </div>

                    <aside class="plan-preview plan-preview-sticky self-start p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Vista previa</p>
                            <span id="preview-status" class="inline-flex rounded-full border border-emerald-400/40 bg-emerald-500/15 px-2.5 py-1 text-xs font-bold text-emerald-200"><?php echo e($defaultStatus === 'active' ? 'Activo' : 'Oculto'); ?></span>
                        </div>
                        <div class="mt-3 space-y-2.5">
                            <h3 id="preview-name" class="text-lg font-black text-slate-100">Nombre del plan</h3>
                            <div class="plan-preview-row">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Precio</span>
                                <span id="preview-price" class="text-base font-black text-emerald-300"><?php echo e(\App\Support\Currency::format((float) old('price', 0), $appCurrencyCode)); ?></span>
                            </div>
                            <div class="plan-preview-row">
                                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">Duración</span>
                                <span id="preview-duration" class="text-sm font-bold text-slate-100"><?php echo e($defaultDurationLabel); ?></span>
                            </div>
                            <p id="preview-validity" class="text-xs text-slate-400">Vigencia: <?php echo e($defaultDurationLabel); ?>. <?php echo e($defaultStatus === 'active' ? 'Listo para vender.' : 'Se guardará oculto.'); ?></p>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    <?php else: ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'plans-surface-card','title' => 'Crear plan','subtitle' => 'Modo de solo lectura.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'plans-surface-card','title' => 'Crear plan','subtitle' => 'Modo de solo lectura.']); ?>
        <div class="plans-readonly-shell">
            <p class="ui-muted text-sm">
            <?php echo e($isGlobalScope
                ? 'Para crear o editar planes, cambia el selector de sucursal arriba y entra en una sede específica.'
                : 'La sede secundaria no puede crear ni editar planes. Esta gestión se realiza desde la sede principal.'); ?>

            </p>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    <?php endif; ?>

    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'plans-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'plans-surface-card']); ?>
            <div class="space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="ui-heading text-lg font-black">Planes del gimnasio</h2>
                        <p class="ui-muted text-sm"><?php echo e($plansTableSubtitle); ?></p>
                    </div>
                </div>

                <div class="plans-filter-grid plans-toolbar-shell">
                    <label class="space-y-1 text-sm font-semibold ui-muted">
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
                    <div class="pt-1 xl:pt-0">
                        <span id="plans-count" class="ui-text inline-flex rounded-full border border-slate-300/35 px-3 py-1 text-xs font-bold"><?php echo e($plans->count()); ?> planes</span>
                    </div>
                    <button id="plans-clear-filters" type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold">Limpiar filtros</button>
                </div>

                <div class="plans-table-shell overflow-x-auto rounded-2xl border border-slate-300/30">
                    <table class="plans-table ui-table min-w-[980px] text-sm">
                        <thead>
                            <tr class="border-b border-slate-200/40 text-left text-xs uppercase tracking-wider">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Nombre</th>
                                <?php if($isGlobalScope): ?>
                                    <th class="px-4 py-3">Sede</th>
                                <?php endif; ?>
                                <th class="px-4 py-3">Duración</th>
                                <th class="px-4 py-3">Precio</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="plans-table-body">
                            <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr
                                    data-plan-id="<?php echo e($plan->id); ?>"
                                    data-plan-name="<?php echo e(mb_strtolower($plan->name)); ?>"
                                    data-plan-status="<?php echo e($plan->status); ?>"
                                    data-plan-duration="<?php echo e($plan->duration_days); ?>"
                                    data-plan-duration-unit="<?php echo e(\App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days'))); ?>"
                                    data-plan-duration-months="<?php echo e($plan->duration_months !== null ? (int) $plan->duration_months : ''); ?>"
                                    data-plan-price="<?php echo e(number_format((float) $plan->price, 2, '.', '')); ?>"
                                    class="border-b border-slate-200/30 align-middle">
                                    <td class="ui-text px-4 py-3 font-black"><?php echo e($plan->id); ?></td>
                                    <td class="ui-text px-4 py-3 font-semibold"><?php echo e($plan->name); ?></td>
                                    <?php if($isGlobalScope): ?>
                                        <td class="px-4 py-3">
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'info']); ?><?php echo e($plan->gym?->name ?? '-'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="ui-text px-4 py-3"><?php echo e(\App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months)); ?></td>
                                    <td class="ui-text px-4 py-3"><?php echo e(\App\Support\Currency::format((float) $plan->price, $appCurrencyCode)); ?></td>
                                    <td class="px-4 py-3">
                                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $plan->status === 'active' ? 'success' : 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plan->status === 'active' ? 'success' : 'muted')]); ?><?php echo e($plan->status === 'active' ? 'Activo' : 'Oculto'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 min-w-[16rem]">
                                        <?php if($isReadOnlyScope): ?>
                                            <span class="text-xs font-semibold ui-muted"><?php echo e($readOnlyActionLabel); ?></span>
                                        <?php else: ?>
                                            <div class="flex items-center justify-end gap-2">
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'secondary','class' => 'ui-action-button js-edit-plan','dataPlanId' => ''.e($plan->id).'','dataPlanNameValue' => ''.e($plan->name).'','dataPlanDurationValue' => ''.e($plan->duration_days).'','dataPlanDurationUnitValue' => ''.e(\App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days'))).'','dataPlanDurationMonthsValue' => ''.e($plan->duration_months !== null ? (int) $plan->duration_months : '').'','dataPlanPriceValue' => ''.e(number_format((float) $plan->price, 2, '.', '')).'','dataPlanStatusValue' => ''.e($plan->status).'','title' => 'Editar','ariaLabel' => 'Editar plan '.e($plan->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'secondary','class' => 'ui-action-button js-edit-plan','data-plan-id' => ''.e($plan->id).'','data-plan-name-value' => ''.e($plan->name).'','data-plan-duration-value' => ''.e($plan->duration_days).'','data-plan-duration-unit-value' => ''.e(\App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days'))).'','data-plan-duration-months-value' => ''.e($plan->duration_months !== null ? (int) $plan->duration_months : '').'','data-plan-price-value' => ''.e(number_format((float) $plan->price, 2, '.', '')).'','data-plan-status-value' => ''.e($plan->status).'','title' => 'Editar','aria-label' => 'Editar plan '.e($plan->name).'']); ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 20h9"/>
                                                        <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z"/>
                                                    </svg>
                                                    <span class="ui-action-button-label">Editar</span>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'ui-action-button js-open-plan-actions','dataPlanId' => ''.e($plan->id).'','dataPlanNameValue' => ''.e($plan->name).'','dataPlanDurationValue' => ''.e($plan->duration_days).'','dataPlanDurationUnitValue' => ''.e(\App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days'))).'','dataPlanDurationMonthsValue' => ''.e($plan->duration_months !== null ? (int) $plan->duration_months : '').'','dataPlanPriceValue' => ''.e(number_format((float) $plan->price, 2, '.', '')).'','dataPlanStatusValue' => ''.e($plan->status).'','title' => 'Más acciones','ariaLabel' => 'Más acciones para el plan '.e($plan->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'ui-action-button js-open-plan-actions','data-plan-id' => ''.e($plan->id).'','data-plan-name-value' => ''.e($plan->name).'','data-plan-duration-value' => ''.e($plan->duration_days).'','data-plan-duration-unit-value' => ''.e(\App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days'))).'','data-plan-duration-months-value' => ''.e($plan->duration_months !== null ? (int) $plan->duration_months : '').'','data-plan-price-value' => ''.e(number_format((float) $plan->price, 2, '.', '')).'','data-plan-status-value' => ''.e($plan->status).'','title' => 'Más acciones','aria-label' => 'Más acciones para el plan '.e($plan->name).'']); ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="1"/>
                                                        <circle cx="19" cy="12" r="1"/>
                                                        <circle cx="5" cy="12" r="1"/>
                                                    </svg>
                                                    <span class="ui-action-button-label">Más acciones</span>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="<?php echo e($isGlobalScope ? 7 : 6); ?>" class="plans-empty-state px-4 py-8 text-center text-sm font-semibold text-slate-400">No hay planes registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

    <?php if($canViewPromotions): ?>
    <?php $promotionRows = ($promotions ?? collect()); ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'plans-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'plans-surface-card']); ?>
        <div class="plans-form-shell space-y-4">
            <div class="plans-promo-toolbar flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="ui-heading text-lg font-black">Promociones comerciales</h2>
                    <p class="ui-muted text-sm">Crea promociones fáciles para fechas especiales: San Valentín, 2x1, trae a un amigo y más.</p>
                </div>
                <div class="flex items-center gap-2">
                    <?php if(! $isReadOnlyScope && $canManagePromotions): ?>
                        <button type="button" id="open-promotion-modal-btn" data-open-promotion-modal class="ui-button ui-button-primary px-3 py-1.5 text-xs font-black">
                            + Nueva promoción
                        </button>
                    <?php else: ?>
                        <span class="text-xs font-semibold ui-muted"><?php echo e($readOnlyActionLabel); ?></span>
                    <?php endif; ?>
                    <span class="ui-text inline-flex rounded-full border border-slate-300/35 px-3 py-1 text-xs font-bold"><?php echo e($promotionRows->count()); ?> promociones</span>
                </div>
            </div>

            <?php if($promotionRows->isEmpty()): ?>
                <div class="plan-promo-empty">
                    <p class="text-sm font-bold ui-text">No hay promociones creadas todavía.</p>
                    <p class="ui-muted mt-1 text-xs">Crea una promoción para activar campañas como 2x1, descuento por temporada o días extra.</p>
                    <?php if(! $isReadOnlyScope && $canManagePromotions): ?>
                        <button type="button" data-open-promotion-modal class="ui-button ui-button-primary mt-3 px-4 py-2 text-sm font-black">+ Nueva promoción</button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="plans-table-shell overflow-x-auto rounded-2xl border border-slate-300/30">
                    <table class="plans-table ui-table min-w-[1020px] text-sm">
                        <thead>
                        <tr class="border-b border-slate-200/40 text-left text-xs uppercase tracking-wider">
                            <th class="px-4 py-3">Promo</th>
                            <?php if($isGlobalScope): ?>
                                <th class="px-4 py-3">Sede</th>
                            <?php endif; ?>
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
                        <?php $__currentLoopData = $promotionRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-slate-200/30 align-middle">
                                <td class="ui-text px-4 py-3 font-semibold"><?php echo e($promotion->name); ?></td>
                                <?php if($isGlobalScope): ?>
                                    <td class="px-4 py-3">
                                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'info']); ?><?php echo e($promotion->gym?->name ?? '-'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="ui-text px-4 py-3"><?php echo e($promotionTypeLabels[$promotion->type] ?? $promotion->type); ?></td>
                                <td class="ui-text px-4 py-3">
                                    <?php if($promotion->type === 'percentage'): ?>
                                        -<?php echo e((float) $promotion->value); ?>%
                                    <?php elseif($promotion->type === 'fixed'): ?>
                                        -<?php echo e(\App\Support\Currency::format((float) $promotion->value, $appCurrencyCode)); ?>

                                    <?php elseif($promotion->type === 'final_price'): ?>
                                        <?php echo e(\App\Support\Currency::format((float) $promotion->value, $appCurrencyCode)); ?>

                                    <?php elseif($promotion->type === 'bonus_days'): ?>
                                        +<?php echo e((int) $promotion->value); ?> días
                                    <?php elseif(in_array($promotion->type, ['two_for_one', 'bring_friend'], true)): ?>
                                        <?php echo e((float) ($promotion->value ?? 50)); ?>% desc.
                                    <?php else: ?>
                                        <?php echo e((float) $promotion->value); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="ui-text px-4 py-3">
                                    <?php echo e($promotion->starts_at?->toDateString() ?? 'Sin inicio'); ?> - <?php echo e($promotion->ends_at?->toDateString() ?? 'Sin fin'); ?>

                                </td>
                                <td class="ui-text px-4 py-3"><?php echo e($promotion->plan?->name ?? 'Todos'); ?></td>
                                <td class="ui-text px-4 py-3"><?php echo e($promotion->times_used); ?><?php echo e($promotion->max_uses ? ' / '.$promotion->max_uses : ''); ?></td>
                                <td class="px-4 py-3">
                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $promotion->status === 'active' ? 'success' : 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($promotion->status === 'active' ? 'success' : 'muted')]); ?><?php echo e($promotion->status === 'active' ? 'Activo' : 'Inactivo'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                </td>
                                <td class="px-4 py-3 min-w-[16rem]">
                                    <?php if($isReadOnlyScope): ?>
                                        <span class="text-xs font-semibold ui-muted"><?php echo e($readOnlyActionLabel); ?></span>
                                    <?php else: ?>
                                        <div class="ui-action-grid ui-action-grid-end">
                                            <form method="POST" action="<?php echo e(route('plans.promotions.toggle', $promotion->id)); ?>" class="w-full">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="status" value="<?php echo e($promotion->status === 'active' ? 'inactive' : 'active'); ?>">
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => $promotion->status === 'active' ? 'muted' : 'success','class' => 'ui-action-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($promotion->status === 'active' ? 'muted' : 'success'),'class' => 'ui-action-button']); ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 2v10"/>
                                                        <path d="M18.36 6.64a9 9 0 1 1-12.72 0"/>
                                                    </svg>
                                                    <span class="ui-action-button-label"><?php echo e($promotion->status === 'active' ? 'Desactivar' : 'Activar'); ?></span>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                            </form>
                                            <form method="POST" action="<?php echo e(route('plans.promotions.destroy', $promotion->id)); ?>" class="w-full">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'danger','class' => 'ui-action-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'danger','class' => 'ui-action-button']); ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18"/>
                                                        <path d="M8 6V4h8v2"/>
                                                        <path d="M19 6l-1 14H6L5 6"/>
                                                        <path d="M10 11v6"/>
                                                        <path d="M14 11v6"/>
                                                    </svg>
                                                    <span class="ui-action-button-label">Eliminar</span>
                                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

    <?php if(! $isReadOnlyScope && $canManagePromotions): ?>
    <div id="promotion-create-modal" class="modal-shell" aria-hidden="true" aria-labelledby="promotion-create-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="promotion-create-title" class="ui-heading text-base font-black">Nueva promoción</h3>
                <button type="button" class="mini-action" data-close-modal="promotion-create-modal" aria-label="Cerrar modal promoción">Cerrar</button>
            </div>

            <form id="promotion-form" method="POST" action="<?php echo e(route('plans.promotions.store')); ?>" class="plans-modal-form space-y-4 px-4 py-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="promotion_form" value="1">

                <div class="plans-modal-section space-y-2">
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
                               value="<?php echo e(old('name')); ?>">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Estado</span>
                        <select id="promo-status" name="status" class="ui-input">
                            <option value="active" <?php if(old('status', 'active') === 'active'): echo 'selected'; endif; ?>>Activo</option>
                            <option value="inactive" <?php if(old('status') === 'inactive'): echo 'selected'; endif; ?>>Inactivo</option>
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Tipo</span>
                        <select id="promo-type" name="type" class="ui-input" required>
                            <option value="percentage" <?php if(old('type', 'percentage') === 'percentage'): echo 'selected'; endif; ?>>Descuento porcentual</option>
                            <option value="fixed" <?php if(old('type') === 'fixed'): echo 'selected'; endif; ?>>Descuento monto fijo</option>
                            <option value="final_price" <?php if(old('type') === 'final_price'): echo 'selected'; endif; ?>>Precio final fijo</option>
                            <option value="bonus_days" <?php if(old('type') === 'bonus_days'): echo 'selected'; endif; ?>>Días extra</option>
                            <option value="two_for_one" <?php if(old('type') === 'two_for_one'): echo 'selected'; endif; ?>>2x1</option>
                            <option value="bring_friend" <?php if(old('type') === 'bring_friend'): echo 'selected'; endif; ?>>Trae a un amigo</option>
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide" id="promo-value-label">Valor</span>
                        <input type="number" id="promo-value" name="value" class="ui-input" min="0" step="0.01" value="<?php echo e(old('value', '0')); ?>">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Aplicar a plan</span>
                        <select id="promo-plan-id" name="plan_id" class="ui-input">
                            <option value="">Todos los planes</option>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" <?php if((string) old('plan_id') === (string) $plan->id): echo 'selected'; endif; ?>><?php echo e($plan->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Fecha inicio</span>
                        <input type="date" id="promo-starts-at" name="starts_at" class="ui-input" value="<?php echo e(old('starts_at')); ?>">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Fecha fin</span>
                        <input type="date" id="promo-ends-at" name="ends_at" class="ui-input" value="<?php echo e(old('ends_at')); ?>">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span class="text-xs uppercase tracking-wide">Límite de usos</span>
                        <input type="number" id="promo-max-uses" name="max_uses" class="ui-input" min="1" step="1" placeholder="Ilimitado" value="<?php echo e(old('max_uses')); ?>">
                    </label>

                    <label class="space-y-1 text-sm font-semibold ui-muted lg:col-span-3">
                        <span class="text-xs uppercase tracking-wide">Descripción</span>
                        <textarea id="promo-description" name="description" rows="2" class="ui-input" placeholder="Nota interna para recepción o caja."><?php echo e(old('description')); ?></textarea>
                    </label>
                </div>

                <div class="plans-modal-actions flex flex-wrap items-center justify-between gap-3">
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
    <?php endif; ?>
    <?php elseif(! $planControlPlansDashboard): ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'plans-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'plans-surface-card']); ?>
        <div class="plans-readonly-shell space-y-3">
            <h2 class="ui-heading text-lg font-black">Promociones comerciales</h2>
            <p class="ui-muted text-sm">
                Este módulo no está disponible en tu plan actual. Para habilitar promociones cambia al Plan profesional o superior.
            </p>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    <?php endif; ?>

    <div id="plan-edit-modal" class="modal-shell" aria-hidden="true" aria-labelledby="plan-edit-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="plan-edit-title" class="ui-heading text-base font-black">Editar plan</h3>
                <button type="button" class="mini-action" data-close-modal="plan-edit-modal" aria-label="Cerrar modal editar">Cerrar</button>
            </div>
            <form id="edit-plan-form" method="POST" action="#" class="plans-modal-form space-y-3 px-4 py-4">
                <?php echo csrf_field(); ?>
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
                <div class="plans-modal-actions flex justify-end gap-2 pt-2">
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
            <form id="delete-plan-form" method="POST" action="#" class="plans-modal-form space-y-4 px-4 py-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="DELETE">
                <p class="ui-text text-sm">Vas a eliminar <strong id="delete-plan-name" class="text-rose-500">este plan</strong>. Esta acción no se puede deshacer.</p>
                <div class="plans-modal-actions flex justify-end gap-2">
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
            <form id="toggle-plan-form" method="POST" action="#" class="plans-modal-form space-y-4 px-4 py-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="status" id="toggle-plan-status" value="inactive">
                <p class="ui-text text-sm" id="toggle-plan-message">Confirma actualizar estado del plan.</p>
                <div class="plans-modal-actions flex justify-end gap-2">
                    <button type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold" data-close-modal="plan-toggle-modal">Cancelar</button>
                    <button id="toggle-plan-submit" type="submit" class="ui-button ui-button-primary px-4 py-2 text-xs font-black"><span class="js-submit-label">Confirmar</span><span class="js-submit-loading hidden">Actualizando...</span></button>
                </div>
            </form>
        </div>
    </div>

    <div id="plan-actions-modal" class="modal-shell" aria-hidden="true" aria-labelledby="plan-actions-title">
        <div class="modal-card">
            <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                <h3 id="plan-actions-title" class="ui-heading text-base font-black">Más acciones del plan</h3>
                <button type="button" class="mini-action" data-close-modal="plan-actions-modal" aria-label="Cerrar modal de acciones">Cerrar</button>
            </div>
            <div class="plans-modal-form space-y-4 px-4 py-4">
                <div class="plan-actions-modal-summary plans-modal-section">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Plan seleccionado</p>
                    <p id="plan-actions-plan-name" class="mt-1 text-base font-black text-slate-100">Plan</p>
                    <p id="plan-actions-plan-meta" class="mt-1 text-xs text-slate-300">Duración y precio</p>
                </div>

                <div class="plan-actions-modal-grid">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','id' => 'plan-actions-duplicate','class' => 'ui-action-button js-duplicate-plan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','id' => 'plan-actions-duplicate','class' => 'ui-action-button js-duplicate-plan']); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                        <span class="ui-action-button-label">Duplicar plan</span>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'secondary','id' => 'plan-actions-toggle','class' => 'ui-action-button js-toggle-plan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'secondary','id' => 'plan-actions-toggle','class' => 'ui-action-button js-toggle-plan']); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v10"/>
                            <path d="M18.36 6.64a9 9 0 1 1-12.72 0"/>
                        </svg>
                        <span id="plan-actions-toggle-label" class="ui-action-button-label">Cambiar estado</span>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'danger','id' => 'plan-actions-delete','class' => 'ui-action-button js-delete-plan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'danger','id' => 'plan-actions-delete','class' => 'ui-action-button js-delete-plan']); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18"/>
                            <path d="M8 6V4h8v2"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6"/>
                            <path d="M14 11v6"/>
                        </svg>
                        <span class="ui-action-button-label">Eliminar plan</span>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const currencySymbol = <?php echo json_encode((string) $appCurrencySymbol, 15, 512) ?>;
    const routeUpdateTemplate = <?php echo json_encode($updateRouteTemplate, 15, 512) ?>;
    const routeDestroyTemplate = <?php echo json_encode($destroyRouteTemplate, 15, 512) ?>;
    const routeToggleTemplate = <?php echo json_encode($toggleRouteTemplate, 15, 512) ?>;
    const openPromotionModalOnLoad = <?php echo json_encode($openPromotionModal, 15, 512) ?>;

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
    const previewValidity = document.getElementById('preview-validity');
    const durationChips = Array.from(document.querySelectorAll('.js-duration-chip'));
    const plansSearchInput = document.getElementById('plans-search');
    const plansStatusFilter = document.getElementById('plans-status-filter');
    const plansTableBody = document.getElementById('plans-table-body');
    const plansCount = document.getElementById('plans-count');
    const plansClearFilters = document.getElementById('plans-clear-filters');
    const alertContainer = document.getElementById('plans-alert-container');
    const promoType = document.getElementById('promo-type');
    const promoValue = document.getElementById('promo-value');
    const promoValueLabel = document.getElementById('promo-value-label');
    const promoHelpText = document.getElementById('promo-help-text');
    const promoTemplateButtons = Array.from(document.querySelectorAll('.js-promo-template'));
    const promoNameInput = document.getElementById('promo-name');
    const promoDescription = document.getElementById('promo-description');
    const openPromotionModalButtons = Array.from(document.querySelectorAll('[data-open-promotion-modal]'));
    const createPlanReset = document.getElementById('create-plan-reset');
    const openPlanActionsButtons = Array.from(document.querySelectorAll('.js-open-plan-actions'));
    const planActionsName = document.getElementById('plan-actions-plan-name');
    const planActionsMeta = document.getElementById('plan-actions-plan-meta');
    const planActionsDuplicateButton = document.getElementById('plan-actions-duplicate');
    const planActionsToggleButton = document.getElementById('plan-actions-toggle');
    const planActionsToggleLabel = document.getElementById('plan-actions-toggle-label');
    const planActionsDeleteButton = document.getElementById('plan-actions-delete');

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
        if (previewValidity) {
            const humanDuration = durationLabel(durationUnit, durationDays, durationMonths);
            previewValidity.textContent = statusText === 'Activo'
                ? `Vigencia: ${humanDuration}. Listo para vender en recepción.`
                : `Vigencia: ${humanDuration}. Se guardará oculto hasta activarlo.`;
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
    createPlanReset?.addEventListener('click', () => {
        createForm?.reset();
        syncCreateDurationVisibility();
        updatePreview();
        syncDurationChips();
        nameInput?.focus();
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
    plansClearFilters?.addEventListener('click', () => {
        if (plansSearchInput) plansSearchInput.value = '';
        if (plansStatusFilter) plansStatusFilter.value = 'all';
        applyTableFilters();
        plansSearchInput?.focus();
    });

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

    openPlanActionsButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const planId = String(button.getAttribute('data-plan-id') || '');
            const planName = String(button.getAttribute('data-plan-name-value') || 'Plan');
            const planDuration = Number(button.getAttribute('data-plan-duration-value') || 30);
            const planDurationUnit = normalizeDurationUnit(button.getAttribute('data-plan-duration-unit-value'));
            const planDurationMonths = Number(button.getAttribute('data-plan-duration-months-value') || 1);
            const planPrice = Number(button.getAttribute('data-plan-price-value') || 0);
            const planStatus = String(button.getAttribute('data-plan-status-value') || 'inactive');
            const humanDuration = durationLabel(planDurationUnit, planDuration, planDurationMonths);
            const humanStatus = planStatus === 'active' ? 'Activo' : 'Oculto';

            if (planActionsName) planActionsName.textContent = planName;
            if (planActionsMeta) planActionsMeta.textContent = `${humanDuration} | ${formatMoney(planPrice)} | ${humanStatus}`;

            if (planActionsDuplicateButton) {
                planActionsDuplicateButton.setAttribute('data-plan-name-value', planName);
                planActionsDuplicateButton.setAttribute('data-plan-duration-value', String(planDuration));
                planActionsDuplicateButton.setAttribute('data-plan-duration-unit-value', planDurationUnit);
                planActionsDuplicateButton.setAttribute('data-plan-duration-months-value', String(planDurationMonths));
                planActionsDuplicateButton.setAttribute('data-plan-price-value', String(planPrice));
                planActionsDuplicateButton.setAttribute('data-plan-status-value', planStatus);
            }
            if (planActionsToggleButton) {
                planActionsToggleButton.setAttribute('data-plan-id', planId);
                planActionsToggleButton.setAttribute('data-plan-name-value', planName);
                planActionsToggleButton.setAttribute('data-current-status', planStatus);
            }
            if (planActionsDeleteButton) {
                planActionsDeleteButton.setAttribute('data-plan-id', planId);
                planActionsDeleteButton.setAttribute('data-plan-name-value', planName);
            }
            if (planActionsToggleLabel) {
                planActionsToggleLabel.textContent = planStatus === 'active' ? 'Desactivar plan' : 'Activar plan';
            }

            openModal('plan-actions-modal');
        });
    });

    [planActionsDuplicateButton, planActionsToggleButton, planActionsDeleteButton].forEach((button) => {
        button?.addEventListener('click', () => closeModal('plan-actions-modal'));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        ['promotion-create-modal', 'plan-edit-modal', 'plan-delete-modal', 'plan-toggle-modal', 'plan-actions-modal'].forEach(closeModal);
    });

    openPromotionModalButtons.forEach((button) => {
        button.addEventListener('click', () => openModal('promotion-create-modal'));
    });

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
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/plans/index.blade.php ENDPATH**/ ?>