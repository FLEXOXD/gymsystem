

<?php $__env->startSection('title', 'Caja profesional'); ?>
<?php $__env->startSection('page-title', 'Caja por turno'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .cash-page [data-tone='ok'] { color: rgb(5 150 105); }
    .cash-page [data-tone='warn'] { color: rgb(217 119 6); }
    .cash-page [data-tone='bad'] { color: rgb(225 29 72); }
    .theme-dark .cash-page [data-tone='ok'] { color: rgb(110 231 183); }
    .theme-dark .cash-page [data-tone='warn'] { color: rgb(252 211 77); }
    .theme-dark .cash-page [data-tone='bad'] { color: rgb(251 113 133); }
    .cash-control-shell {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(163 230 53 / 0.22);
        border-radius: 1.22rem;
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.16), transparent 34%),
            linear-gradient(150deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
        box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
        backdrop-filter: blur(14px);
        padding: 1.05rem;
    }
    .theme-dark .cash-control-shell,
    .dark .cash-control-shell {
        border-color: rgb(163 230 53 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.14), transparent 34%),
            linear-gradient(160deg, rgb(2 6 23 / 0.84), rgb(15 23 42 / 0.62));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.92);
    }

    .cash-control-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .cash-control-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(95deg, transparent, rgb(163 230 53 / 0.05), transparent);
    }
    .cash-control-grid {
        display: grid;
        gap: 1.05rem;
        position: relative;
        z-index: 1;
    }
    .cash-control-copy {
        max-width: 48rem;
    }
    .cash-control-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.17em;
        text-transform: uppercase;
        color: rgb(77 124 15 / 0.94);
    }
    .theme-dark .cash-control-kicker,
    .dark .cash-control-kicker {
        color: rgb(217 249 157 / 0.94);
    }
    .cash-control-kicker::before {
        content: '';
        width: 0.52rem;
        height: 0.52rem;
        border-radius: 999px;
        background: rgb(132 204 22 / 0.94);
        box-shadow: 0 0 0 6px rgb(132 204 22 / 0.12);
    }
    .cash-control-heading {
        margin-top: 0.78rem;
        font-size: clamp(1.14rem, 1.85vw, 1.46rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.97);
    }
    .theme-dark .cash-control-heading,
    .dark .cash-control-heading {
        color: rgb(241 245 249 / 0.98);
    }
    .cash-control-summary {
        margin-top: 0.5rem;
        font-size: 0.88rem;
        line-height: 1.58;
        color: rgb(71 85 105 / 0.92);
    }
    .theme-dark .cash-control-summary,
    .dark .cash-control-summary {
        color: rgb(148 163 184 / 0.9);
    }
    .cash-control-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }
    .cash-control-actions .ui-button {
        min-height: 2.72rem;
    }
    .cash-control-priority-grid {
        display: grid;
        gap: 0.75rem;
    }
    .cash-control-priority {
        position: relative;
        overflow: hidden;
        border-radius: 1.05rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        min-height: 7rem;
        padding: 0.9rem 0.95rem;
    }
    .theme-dark .cash-control-priority,
    .dark .cash-control-priority {
        border-color: rgb(148 163 184 / 0.18);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }
    .cash-control-priority::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }
    .cash-control-priority[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }
    .cash-control-priority[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
    }
    .cash-control-priority[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
    }
    .cash-control-priority-label {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }
    .theme-dark .cash-control-priority-label,
    .dark .cash-control-priority-label {
        color: rgb(148 163 184 / 0.9);
    }
    .cash-control-priority-value {
        margin-top: 0.42rem;
        font-size: 1.46rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.97);
    }
    .theme-dark .cash-control-priority-value,
    .dark .cash-control-priority-value {
        color: rgb(248 250 252 / 0.98);
    }
    .cash-control-priority-note {
        margin-top: 0.4rem;
        font-size: 0.75rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }
    .theme-dark .cash-control-priority-note,
    .dark .cash-control-priority-note {
        color: rgb(148 163 184 / 0.88);
    }
    .cash-page .cash-kpi-grid {
        display: grid;
        gap: .75rem;
    }
    .cash-page .cash-kpi-grid > article:nth-child(n+5) {
        display: none;
    }
    @media (min-width: 1280px) {
        .cash-control-grid {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
        }
        .cash-control-priority-grid {
            grid-column: 1 / -1;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .cash-page .cash-kpi-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    @media (min-width: 768px) {
        .cash-control-priority-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .cash-control-actions .ui-button {
            width: 100%;
        }
    }
    .cash-page .cash-method-accordion {
        border: 1px solid rgb(148 163 184 / .28);
        border-radius: .95rem;
        background: rgb(15 23 42 / .4);
        padding: .35rem .75rem .75rem;
    }
    .theme-light .cash-page .cash-method-accordion {
        background: rgb(255 255 255 / .82);
    }
    .cash-page .cash-method-accordion > summary {
        list-style: none;
        cursor: pointer;
        padding: .55rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: rgb(148 163 184);
    }
    .cash-page .cash-method-accordion > summary::-webkit-details-marker {
        display: none;
    }
    .cash-page .cash-method-grid {
        display: grid;
        gap: .75rem;
    }
    @media (min-width: 768px) {
        .cash-page .cash-method-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    .cash-page .cash-operational-grid {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 1280px) {
        .cash-page .cash-operational-grid {
            grid-template-columns: minmax(0, 1.65fr) minmax(320px, 0.85fr);
            align-items: start;
        }
        .cash-page .cash-close-card {
            position: sticky;
            top: calc(5.35rem + env(safe-area-inset-top));
        }
    }
    .cash-page .cash-difference-total {
        font-size: 1.65rem;
        line-height: 1;
        letter-spacing: -.015em;
    }
    .cash-page #difference-total-card[data-tone='ok'] {
        border-color: rgb(16 185 129 / .45);
        background: rgb(16 185 129 / .08);
    }
    .cash-page #difference-total-card[data-tone='warn'] {
        border-color: rgb(245 158 11 / .55);
        background: rgb(245 158 11 / .08);
    }
    .cash-page #difference-total-card[data-tone='bad'] {
        border-color: rgb(244 63 94 / .55);
        background: rgb(244 63 94 / .08);
    }
    .cash-page .close-status-badge {
        display: inline-flex;
        align-items: center;
        border: 1px solid rgb(148 163 184 / .35);
        border-radius: 999px;
        padding: .2rem .65rem;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        background: rgb(15 23 42 / .45);
    }
    .cash-page .closure-table-wrap table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .cash-page .quick-actions {
        border: 1px solid rgb(148 163 184 / .25);
        border-radius: 1rem;
        background: rgb(15 23 42 / .25);
        padding: .9rem;
    }
    .theme-light .cash-page .quick-actions {
        background: rgb(248 250 252 / .8);
    }
    .cash-page .quick-actions-grid {
        display: grid;
        gap: .65rem;
    }
    @media (min-width: 768px) {
        .cash-page .quick-actions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (min-width: 1280px) {
        .cash-page .quick-actions-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    .cash-page .quick-actions .ui-button {
        justify-content: flex-start;
        gap: .45rem;
        width: 100%;
    }
    .cash-page .cash-surface-card {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(148 163 184 / .26);
        background:
            radial-gradient(circle at top right, rgb(6 182 212 / .08), transparent 26%),
            linear-gradient(165deg, rgb(255 255 255 / .98), rgb(248 250 252 / .95));
        box-shadow: 0 26px 44px -38px rgb(15 23 42 / .24);
    }
    .cash-page .cash-surface-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / .72), transparent);
        opacity: .82;
        pointer-events: none;
    }
    .cash-page .cash-surface-card > * {
        position: relative;
        z-index: 1;
    }
    .cash-page .cash-surface-card > header .ui-heading {
        font-size: clamp(1.12rem, 1.35vw, 1.36rem);
        letter-spacing: -.03em;
        font-weight: 900;
    }
    .cash-page .cash-surface-card > header .ui-muted {
        margin-top: .3rem;
        font-size: .88rem;
    }
    .cash-page .cash-kpi-card {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / .24);
        padding: .95rem 1rem;
        box-shadow: 0 18px 28px -28px rgb(15 23 42 / .26);
    }
    .cash-page .cash-kpi-card::before {
        content: '';
        position: absolute;
        left: 1rem;
        right: 1rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / .22);
    }
    .cash-page .cash-kpi-card[data-tone='neutral']::before {
        background: linear-gradient(90deg, rgb(100 116 139 / .78), rgb(100 116 139 / .14));
    }
    .cash-page .cash-kpi-card[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / .9), rgb(16 185 129 / .18));
    }
    .cash-page .cash-kpi-card[data-tone='danger']::before {
        background: linear-gradient(90deg, rgb(244 63 94 / .9), rgb(244 63 94 / .18));
    }
    .cash-page .cash-kpi-card[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / .9), rgb(6 182 212 / .18));
    }
    .cash-page .cash-kpi-card[data-tone='accent']::before {
        background: linear-gradient(90deg, rgb(139 92 246 / .9), rgb(139 92 246 / .18));
    }
    .cash-page .cash-method-card {
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / .24);
        background: rgb(255 255 255 / .88);
        box-shadow: 0 18px 30px -30px rgb(15 23 42 / .22);
    }
    .cash-page .cash-table-shell {
        border: 1px solid rgb(148 163 184 / .24);
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgb(255 255 255 / .92), rgb(248 250 252 / .84));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / .8),
            0 18px 32px -34px rgb(15 23 42 / .18);
    }
    .cash-page .cash-form-shell {
        border: 1px solid rgb(148 163 184 / .22);
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgb(255 255 255 / .76), rgb(248 250 252 / .68));
        padding: .95rem;
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / .78),
            0 18px 30px -32px rgb(15 23 42 / .18);
    }
    .cash-page .cash-difference-card {
        border-radius: 1rem;
        box-shadow: 0 18px 30px -32px rgb(15 23 42 / .18);
    }

    .theme-light .cash-control-shell {
        border-color: rgb(163 230 53 / 0.18);
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.1), transparent 34%),
            linear-gradient(150deg, rgb(255 255 255 / 0.98), rgb(241 245 249 / 0.96));
        box-shadow: 0 28px 48px -40px rgb(15 23 42 / 0.12);
    }

    .theme-light .cash-control-priority,
    .theme-light .cash-page .cash-method-accordion,
    .theme-light .cash-page .quick-actions,
    .theme-light .cash-page .cash-kpi-grid > article,
    .theme-light .cash-page .cash-surface-card,
    .theme-light .cash-page .cash-kpi-card,
    .theme-light .cash-page .cash-method-card,
    .theme-light .cash-page .cash-table-shell,
    .theme-light .cash-page .cash-form-shell,
    .theme-light .cash-page #difference-total-card,
    .theme-light .cash-page .closure-table-wrap {
        border-color: rgb(203 213 225 / 0.82);
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.97), rgb(248 250 252 / 0.94));
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.84),
            0 18px 30px -30px rgb(15 23 42 / 0.1);
    }

    .theme-light .cash-page .cash-method-accordion > summary,
    .theme-light .cash-control-priority-label {
        color: rgb(71 85 105 / 0.9);
    }

    .theme-light .cash-control-summary,
    .theme-light .cash-control-priority-note {
        color: rgb(71 85 105 / 0.92);
    }

    .theme-light .cash-page .close-status-badge {
        border-color: rgb(203 213 225 / 0.82);
        background: rgb(248 250 252 / 0.92);
        color: rgb(51 65 85 / 0.92);
    }

    .theme-light .cash-page .closure-table-wrap {
        border-color: rgb(203 213 225 / 0.78) !important;
    }

    .theme-light .cash-page .cash-surface-card,
    .theme-light .cash-page .cash-kpi-card,
    .theme-light .cash-page .cash-method-card,
    .theme-light .cash-page .cash-table-shell,
    .theme-light .cash-page .cash-form-shell,
    .theme-light .cash-page .cash-difference-card {
        border-color: rgb(203 213 225 / 0.82);
    }

    .theme-light .cash-page .closure-table-wrap table thead th {
        background:
            linear-gradient(180deg, rgb(248 250 252 / 0.98), rgb(241 245 249 / 0.95));
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .cash-control-shell,
    .dark .cash-control-shell {
        border-color: rgb(163 230 53 / 0.18);
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.08), transparent 34%),
            linear-gradient(160deg, rgb(9 16 32 / 0.96), rgb(15 23 42 / 0.88));
    }

    .theme-dark .cash-page .cash-method-accordion,
    .dark .cash-page .cash-method-accordion,
    .theme-dark .cash-page .quick-actions,
    .dark .cash-page .quick-actions,
    .theme-dark .cash-page .cash-kpi-grid > article,
    .dark .cash-page .cash-kpi-grid > article,
    .theme-dark .cash-page .cash-surface-card,
    .dark .cash-page .cash-surface-card,
    .theme-dark .cash-page .cash-kpi-card,
    .dark .cash-page .cash-kpi-card,
    .theme-dark .cash-page .cash-method-card,
    .dark .cash-page .cash-method-card,
    .theme-dark .cash-page .cash-table-shell,
    .dark .cash-page .cash-table-shell,
    .theme-dark .cash-page .cash-form-shell,
    .dark .cash-page .cash-form-shell,
    .theme-dark .cash-page #difference-total-card,
    .dark .cash-page #difference-total-card,
    .theme-dark .cash-page .closure-table-wrap,
    .dark .cash-page .closure-table-wrap {
        border-color: rgb(51 65 85 / 0.74);
    }

    .theme-dark .cash-page .cash-method-accordion,
    .dark .cash-page .cash-method-accordion,
    .theme-dark .cash-page .quick-actions,
    .dark .cash-page .quick-actions,
    .theme-dark .cash-page .cash-surface-card,
    .dark .cash-page .cash-surface-card,
    .theme-dark .cash-page .cash-method-card,
    .dark .cash-page .cash-method-card,
    .theme-dark .cash-page .cash-table-shell,
    .dark .cash-page .cash-table-shell,
    .theme-dark .cash-page .cash-form-shell,
    .dark .cash-page .cash-form-shell,
    .theme-dark .cash-page .closure-table-wrap,
    .dark .cash-page .closure-table-wrap {
        background:
            linear-gradient(180deg, rgb(15 23 42 / 0.94), rgb(17 24 39 / 0.9));
    }

    .theme-dark .cash-page .cash-kpi-card,
    .dark .cash-page .cash-kpi-card,
    .theme-dark .cash-page .cash-difference-card,
    .dark .cash-page .cash-difference-card {
        box-shadow:
            inset 0 1px 0 rgb(255 255 255 / 0.04),
            0 20px 32px -32px rgb(2 8 23 / 0.78);
    }

    .theme-dark .cash-page .close-status-badge,
    .dark .cash-page .close-status-badge {
        border-color: rgb(71 85 105 / 0.78);
        background: rgb(15 23 42 / 0.88);
        color: rgb(226 232 240 / 0.92);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $currencyCode = $appCurrencyCode ?? null;
        $currencySymbol = trim((string) ($appCurrencySymbol ?? '$'));

        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $user = auth()->user();
        $isOwnerUser = (bool) ($user?->isOwner());
        $isCashierUser = (bool) ($user?->isCashier());
        $isCashAdmin = (bool) ($isCashAdmin ?? ($user && ($user->gym_id === null || $isOwnerUser)));
        $canApproveDifference = (bool) ($canApproveCashDifference ?? $isCashAdmin);

        $routeHasVoidMovement = \Illuminate\Support\Facades\Route::has('cash.movements.void');
        $voidRouteTemplate = $routeHasVoidMovement ? route('cash.movements.void', ['movement' => '__MOVEMENT__']) : '';

        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $contextGym = (string) request()->route('contextGym');
        $routeExtras = request()->query('pwa_mode') === 'standalone' ? ['pwa_mode' => 'standalone'] : [];
        $cashRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []) + $routeExtras;
        $isCurrentCashView = ! $isGlobalScope && array_key_exists('openSession', get_defined_vars());
        $openSession = $openSession ?? null;
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $cashWriteBlocked = (bool) ($cashWriteBlocked ?? false);
        $cashWriteBlockedReason = trim((string) ($cashWriteBlockedReason ?? ''));
        $canOpenCash = (bool) ($canOpenCash ?? $isOwnerUser);
        $canCloseCash = (bool) ($canCloseCash ?? $isOwnerUser);
        $canManageMovements = (bool) ($canManageMovements ?? true);
        $recentClosedSessions = $recentClosedSessions ?? collect();
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? $user?->gym_id ?? 0);
        $activePlanKey = $activeGymId > 0 ? $planAccessService->currentPlanKeyForGym($activeGymId) : null;
        $isPlanControl = ! $isGlobalScope && $activePlanKey === 'basico';
        $cashControlDashboard = null;

        if ($isPlanControl && $isCurrentCashView) {
            $lastClosedSession = $recentClosedSessions->first();
            $lastClosingBalance = (float) ($lastClosedSession->closing_balance ?? 0);
            $lastDifference = (float) ($lastClosedSession->difference ?? 0);

            if ($openSession) {
                $controlSummary = $summary ?? ['income_total' => 0.0, 'expense_total' => 0.0, 'expected_balance' => 0.0, 'movements_count' => 0];
                $expectedVisible = (float) ($controlSummary['expected_balance'] ?? 0);
                if (abs($expectedVisible) < 0.00001) {
                    $expectedVisible = round(
                        (float) ($openSession->opening_balance ?? 0)
                        + (float) ($controlSummary['income_total'] ?? 0)
                        - (float) ($controlSummary['expense_total'] ?? 0),
                        2
                    );
                }

                $cashControlDashboard = [
                    'headline' => $isCashierScoped
                        ? 'Tu caja del turno ya esta lista para cobrar sin salir del modulo'
                        : 'Caja abierta y lista para cobrar desde una sola sede',
                    'summary' => $isCashierScoped
                        ? 'Ve tu produccion, registra movimientos y mantente dentro de una lectura compacta del turno.'
                        : 'Ingresos, egresos y cierre quedan ordenados en una sola cabina para operar sin ruido.',
                    'actions' => array_values(array_filter([
                        ! $cashWriteBlocked && $canManageMovements ? [
                            'label' => 'Registrar movimiento',
                            'href' => '#cash-movement-form',
                            'variant' => 'primary',
                        ] : null,
                        $canCloseCash ? [
                            'label' => 'Ir a cierre',
                            'href' => '#cash-close-form',
                            'variant' => 'secondary',
                        ] : null,
                        [
                            'label' => 'Ir a clientes',
                            'href' => route('clients.index', $cashRouteParams),
                            'variant' => 'ghost',
                        ],
                    ])),
                    'priorities' => [
                        [
                            'label' => 'Turno',
                            'value' => '#'.$openSession->id,
                            'note' => 'Abierto '.$openSession->opened_at?->format('d/m H:i').' por '.($openSession->openedBy?->name ?? 'N/D'),
                            'tone' => 'success',
                        ],
                        [
                            'label' => $isCashierScoped ? 'Tus ingresos' : 'Ingresos',
                            'value' => $currencyFormatter::format((float) ($controlSummary['income_total'] ?? 0), $currencyCode),
                            'note' => 'Cobros registrados en el turno actual.',
                            'tone' => 'success',
                        ],
                        [
                            'label' => $isCashierScoped ? 'Tus egresos' : 'Egresos',
                            'value' => $currencyFormatter::format((float) ($controlSummary['expense_total'] ?? 0), $currencyCode),
                            'note' => 'Salidas registradas dentro del mismo turno.',
                            'tone' => 'warning',
                        ],
                        [
                            'label' => $isCashierScoped ? 'Saldo visible' : 'Saldo esperado',
                            'value' => $currencyFormatter::format($expectedVisible, $currencyCode),
                            'note' => ((int) ($controlSummary['movements_count'] ?? 0)).' movimiento(s) registrados.',
                            'tone' => 'info',
                        ],
                    ],
                ];
            } else {
                $statusValue = $cashWriteBlocked
                    ? 'Solo lectura'
                    : ($canOpenCash ? 'Lista para abrir' : 'Apertura restringida');
                $statusNote = $cashWriteBlocked
                    ? ($cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'Esta sede opera la caja desde la sede principal.')
                    : ($canOpenCash
                        ? 'Abre el turno para empezar a cobrar membresias y registrar caja.'
                        : 'Necesitas un usuario autorizado para abrir el turno.');

                $cashControlDashboard = [
                    'headline' => $cashWriteBlocked
                        ? 'Caja visible, pero administrada desde otra sede'
                        : ($canOpenCash
                            ? 'Caja lista para abrir y ordenar cobros del dia'
                            : 'Caja pendiente de apertura por usuario autorizado'),
                    'summary' => $cashWriteBlocked
                        ? 'La sede puede revisar informacion, pero la operacion de apertura y cierre vive en la sede principal.'
                        : ($canOpenCash
                            ? 'Abre el turno desde aqui y deja cobros, ingresos y cierres dentro del sistema desde el primer movimiento.'
                            : 'La vista ya esta preparada; falta una apertura autorizada para empezar a cobrar en orden.'),
                    'actions' => array_values(array_filter([
                        ! $cashWriteBlocked && $canOpenCash ? [
                            'label' => 'Abrir turno',
                            'href' => '#cash-open-form',
                            'variant' => 'primary',
                        ] : null,
                        [
                            'label' => 'Ir a clientes',
                            'href' => route('clients.index', $cashRouteParams),
                            'variant' => ! $cashWriteBlocked && $canOpenCash ? 'secondary' : 'primary',
                        ],
                        $recentClosedSessions->isNotEmpty() ? [
                            'label' => 'Ver historial',
                            'href' => route('cash.sessions.index', $cashRouteParams),
                            'variant' => 'ghost',
                        ] : null,
                    ])),
                    'priorities' => [
                        [
                            'label' => 'Estado',
                            'value' => $statusValue,
                            'note' => $statusNote,
                            'tone' => $cashWriteBlocked ? 'warning' : ($canOpenCash ? 'success' : 'info'),
                        ],
                        [
                            'label' => 'Cierres recientes',
                            'value' => (string) $recentClosedSessions->count(),
                            'note' => $recentClosedSessions->isNotEmpty() ? 'Ultimos turnos cerrados listos para revisar.' : 'Aun no hay cierres registrados.',
                            'tone' => $recentClosedSessions->isNotEmpty() ? 'info' : 'neutral',
                        ],
                        [
                            'label' => 'Ultimo cierre',
                            'value' => $lastClosedSession?->closed_at?->format('d/m H:i') ?? 'Sin cierre',
                            'note' => $lastClosedSession ? 'Balance final '.$currencyFormatter::format($lastClosingBalance, $currencyCode) : 'Todavia no hay un turno cerrado.',
                            'tone' => $lastClosedSession ? 'success' : 'neutral',
                        ],
                        [
                            'label' => 'Ultima diferencia',
                            'value' => $lastClosedSession ? $currencyFormatter::format($lastDifference, $currencyCode) : $currencyFormatter::format(0, $currencyCode),
                            'note' => ! $lastClosedSession
                                ? 'Sin novedades de cierre todavia.'
                                : ($lastDifference == 0.0
                                    ? 'El ultimo cierre cuadro sin diferencias.'
                                    : 'Revisa el ultimo cierre si quieres validar el ajuste.'),
                            'tone' => ! $lastClosedSession ? 'neutral' : ($lastDifference == 0.0 ? 'success' : 'warning'),
                        ],
                    ],
                ];
            }
        }
    ?>

    <div class="cash-page space-y-4"
         data-module="cash-index"
         data-currency-symbol="<?php echo e($currencySymbol); ?>"
         data-void-route-template="<?php echo e($voidRouteTemplate); ?>">
        <?php if($isCurrentCashView && $isCashierScoped): ?>
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Vista privada','subtitle' => 'Solo ves tus cobros, egresos y movimientos personales dentro del turno.','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Vista privada','subtitle' => 'Solo ves tus cobros, egresos y movimientos personales dentro del turno.','class' => 'cash-surface-card']); ?>
                <p class="ui-alert ui-alert-info">Los acumulados del gimnasio quedan ocultos para tu perfil.</p>
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
        <?php if($cashControlDashboard): ?>
            <section class="cash-control-shell">
                <div class="cash-control-grid">
                    <div class="cash-control-copy">
                        <span class="cash-control-kicker">Plan Control / Caja</span>
                        <h2 class="cash-control-heading"><?php echo e($cashControlDashboard['headline'] ?? 'Caja lista para operar sin ruido'); ?></h2>
                        <p class="cash-control-summary"><?php echo e($cashControlDashboard['summary'] ?? 'Apertura, movimientos y cierre quedan dentro de una sola lectura compacta.'); ?></p>
                    </div>

                    <div class="cash-control-actions">
                        <?php $__currentLoopData = ($cashControlDashboard['actions'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $action['href'] ?? '#','variant' => $action['variant'] ?? 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['href'] ?? '#'),'variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($action['variant'] ?? 'ghost')]); ?><?php echo e($action['label'] ?? 'Accion'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="cash-control-priority-grid">
                        <?php $__currentLoopData = ($cashControlDashboard['priorities'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="cash-control-priority" data-tone="<?php echo e($priority['tone'] ?? 'neutral'); ?>">
                                <p class="cash-control-priority-label"><?php echo e($priority['label'] ?? 'Dato'); ?></p>
                                <p class="cash-control-priority-value"><?php echo e($priority['value'] ?? '-'); ?></p>
                                <p class="cash-control-priority-note"><?php echo e($priority['note'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php if($isCurrentCashView): ?>
            <?php if(! $openSession): ?>
                <?php if($cashWriteBlocked): ?>
                    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Caja en solo lectura','subtitle' => 'Operación administrada desde sede principal.','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Caja en solo lectura','subtitle' => 'Operación administrada desde sede principal.','class' => 'cash-surface-card']); ?>
                        <p class="ui-alert ui-alert-warning">
                            <?php echo e($cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'No tienes permisos para abrir o cerrar caja en esta sucursal.'); ?>

                        </p>
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
                <?php elseif(! $canOpenCash): ?>
                    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Apertura restringida','subtitle' => 'Solo usuarios autorizados pueden abrir caja.','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Apertura restringida','subtitle' => 'Solo usuarios autorizados pueden abrir caja.','class' => 'cash-surface-card']); ?>
                        <p class="ui-alert ui-alert-warning">
                            Tu perfil no tiene permiso para abrir caja. Solicita al dueño del gimnasio que abra el turno o te habilite este permiso.
                        </p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Abrir turno','subtitle' => 'Debes abrir caja para cobrar membresías o registrar movimientos.','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Abrir turno','subtitle' => 'Debes abrir caja para cobrar membresías o registrar movimientos.','class' => 'cash-surface-card']); ?>
                        <form id="cash-open-form" method="POST" action="<?php echo e(route('cash.open')); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div class="cash-form-shell grid gap-4 md:grid-cols-2">
                                <label class="space-y-1 text-sm font-semibold ui-muted">
                                    <span>Monto inicial (obligatorio)</span>
                                    <input type="number" name="opening_balance" step="0.01" min="0" value="<?php echo e(old('opening_balance')); ?>" required class="ui-input">
                                </label>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Notas</span>
                                    <textarea name="notes" rows="3" class="ui-input"><?php echo e(old('notes')); ?></textarea>
                                </label>
                            </div>

                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'success','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'success','size' => 'lg']); ?>Abrir turno <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial reciente de cierres','subtitle' => 'Fecha, hora, motivo y notas del cierre.','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial reciente de cierres','subtitle' => 'Fecha, hora, motivo y notas del cierre.','class' => 'cash-surface-card']); ?>
                    <?php echo $__env->make('cash.partials.closure-history', [
                        'sessions' => $recentClosedSessions,
                        'currencyFormatter' => $currencyFormatter,
                        'currencyCode' => $currencyCode,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                <?php
                    $activeSummary = $summary ?? ['income_total' => 0, 'expense_total' => 0, 'expected_balance' => 0, 'movements_count' => 0];
                    $activeMethodTotals = $methodTotals ?? collect();
                    $closingSummary = $closeSummary ?? $activeSummary;
                    $closingMethodTotals = $closeMethodTotals ?? $activeMethodTotals;
                    $activeMovements = $latestMovements ?? collect();
                    $scopedNetTotal = round((float) ($activeSummary['income_total'] ?? 0) - (float) ($activeSummary['expense_total'] ?? 0), 2);
                    $scopedVisibleTotal = round((float) $openSession->opening_balance + $scopedNetTotal, 2);

                    $methodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($activeMethodTotals) {
                        $row = $activeMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $closeMethodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($closingMethodTotals) {
                        $row = $closingMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $expectedCash = (float) $openSession->opening_balance + (float) ($closeMethodMap->get('cash')->income_total ?? 0) - (float) ($closeMethodMap->get('cash')->expense_total ?? 0);
                    $expectedCard = (float) ($closeMethodMap->get('card')->income_total ?? 0) - (float) ($closeMethodMap->get('card')->expense_total ?? 0);
                    $expectedTransfer = (float) ($closeMethodMap->get('transfer')->income_total ?? 0) - (float) ($closeMethodMap->get('transfer')->expense_total ?? 0);
                    $expectedTotal = round($expectedCash + $expectedCard + $expectedTransfer, 2);
                ?>

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tu producción en el turno #'.$openSession->id : 'Turno activo #'.$openSession->id).'','subtitle' => 'Apertura '.e($openSession->opened_at?->format('Y-m-d H:i')).' por '.e($openSession->openedBy?->name ?? 'N/D').'','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tu producción en el turno #'.$openSession->id : 'Turno activo #'.$openSession->id).'','subtitle' => 'Apertura '.e($openSession->opened_at?->format('Y-m-d H:i')).' por '.e($openSession->openedBy?->name ?? 'N/D').'','class' => 'cash-surface-card']); ?>
                    <?php if($isCashierScoped): ?>
                        <div class="cash-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="cash-kpi-card rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75" data-tone="neutral">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $openSession->opening_balance, $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15" data-tone="success">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Tus ingresos</p>
                                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $activeSummary['income_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15" data-tone="danger">
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Tus egresos</p>
                                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $activeSummary['expense_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15" data-tone="info">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Saldo visible</p>
                                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format($scopedVisibleTotal, $currencyCode)); ?></p>
                                <p class="text-xs text-cyan-700 dark:text-cyan-200">Apertura + tus movimientos</p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75" data-tone="neutral">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Tus movimientos</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) $activeSummary['movements_count']); ?></p>
                            </article>
                        </div>
                    <?php else: ?>
                        <div class="cash-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="cash-kpi-card rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75" data-tone="neutral">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $openSession->opening_balance, $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15" data-tone="success">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Ingresos</p>
                                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $activeSummary['income_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15" data-tone="danger">
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Egresos</p>
                                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $activeSummary['expense_total'], $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15" data-tone="info">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperados</p>
                                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format($expectedTotal, $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15" data-tone="accent">
                                <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Saldo actual</p>
                                <p class="mt-1 text-2xl font-black text-violet-800 dark:text-violet-100"><?php echo e($currencyFormatter::format($expectedTotal, $currencyCode)); ?></p>
                            </article>
                            <article class="cash-kpi-card rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75" data-tone="neutral">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) $activeSummary['movements_count']); ?></p>
                            </article>
                        </div>
                    <?php endif; ?>

                    <details class="cash-method-accordion mt-4">
                        <summary>
                            <span>Detalle por metodo</span>
                            <span class="text-[0.72rem] font-bold text-slate-400">Expandir</span>
                        </summary>
                        <div class="cash-method-grid">
                        <?php $__currentLoopData = $methodMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodTotal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="cash-method-card rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"><?php echo e($methodLabels[$methodTotal->method] ?? $methodTotal->method); ?></p>
                                <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">Movimientos: <strong><?php echo e($methodTotal->movements_count); ?></strong></p>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">+ <?php echo e($currencyFormatter::format((float) $methodTotal->income_total, $currencyCode, true)); ?></p>
                                <p class="text-sm text-rose-700 dark:text-rose-300">- <?php echo e($currencyFormatter::format((float) $methodTotal->expense_total, $currencyCode, true)); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </details>
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

                <section class="cash-operational-grid">
                    <?php if($cashWriteBlocked): ?>
                        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Operaciones de caja bloqueadas','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Operaciones de caja bloqueadas','class' => 'cash-surface-card']); ?>
                            <p class="ui-alert ui-alert-warning">
                                <?php echo e($cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'Esta sucursal opera con caja controlada por sede principal.'); ?>

                            </p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Registrar movimiento','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Registrar movimiento','class' => 'cash-surface-card']); ?>
                            <?php if(! $canManageMovements): ?>
                                <p class="ui-alert ui-alert-warning">
                                    Tu perfil no tiene permiso para registrar cobros o movimientos de caja.
                                </p>
                            <?php else: ?>
                                <form id="cash-movement-form" method="POST" action="<?php echo e(route('cash.movements.store')); ?>" class="space-y-4" data-high-threshold="100">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" id="movement-high-confirmed" name="high_amount_confirmed" value="0">

                                <div id="movement-guard-alert" class="hidden ui-alert ui-alert-warning"></div>

                                <div class="cash-form-shell grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Tipo</span>
                                        <select id="movement-type" name="type" required class="ui-input" aria-label="Tipo de movimiento">
                                            <option value="">Seleccione</option>
                                            <option value="income" <?php if(old('type') === 'income'): echo 'selected'; endif; ?>>Ingreso</option>
                                            <option value="expense" <?php if(old('type') === 'expense'): echo 'selected'; endif; ?>>Egreso</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Método</span>
                                        <select id="movement-method" name="method" required class="ui-input" aria-label="Metodo de pago">
                                            <option value="">Seleccione</option>
                                            <option value="cash" <?php if(old('method') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                                            <option value="card" <?php if(old('method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                                            <option value="transfer" <?php if(old('method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Monto</span>
                                        <input id="movement-amount" type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required class="ui-input" aria-label="Monto">
                                    </label>

                                    <label id="movement-expense-category-wrap" class="hidden space-y-1 text-sm font-semibold ui-muted">
                                        <span>Categoria egreso (opcional)</span>
                                        <select id="movement-expense-category" name="expense_category" class="ui-input" aria-label="Categoria de egreso">
                                            <option value="">Sin categoria</option>
                                            <option value="insumos" <?php if(old('expense_category') === 'insumos'): echo 'selected'; endif; ?>>Insumos</option>
                                            <option value="servicios" <?php if(old('expense_category') === 'servicios'): echo 'selected'; endif; ?>>Servicios</option>
                                            <option value="mantenimiento" <?php if(old('expense_category') === 'mantenimiento'): echo 'selected'; endif; ?>>Mantenimiento</option>
                                            <option value="nomina" <?php if(old('expense_category') === 'nomina'): echo 'selected'; endif; ?>>Nomina</option>
                                            <option value="otros" <?php if(old('expense_category') === 'otros'): echo 'selected'; endif; ?>>Otros</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2 xl:col-span-4">
                                        <span id="movement-description-label">Descripción (obligatoria)</span>
                                        <textarea id="movement-description" name="description" rows="2" required class="ui-input" aria-label="Descripción" placeholder="Ingresa descripción obligatoria."><?php echo e(old('description')); ?></textarea>
                                    </label>
                                </div>

                                    <p id="movement-form-hint" class="text-xs font-semibold text-slate-400">Completa tipo, metodo, monto y descripcion para habilitar el registro.</p>
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'movement-submit','type' => 'submit','variant' => 'success','class' => 'w-full justify-center md:w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'movement-submit','type' => 'submit','variant' => 'success','class' => 'w-full justify-center md:w-auto']); ?>Registrar ingreso <?php echo $__env->renderComponent(); ?>
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
                            <?php endif; ?>

                            <div class="mt-6 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <div class="mb-3">
                                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Historial reciente de cierres</h3>
                                    <p class="text-sm ui-muted">Vista compacta del cierre mas reciente para operar sin scroll horizontal.</p>
                                </div>

                                <div class="cash-table-shell closure-table-wrap max-h-72 overflow-auto rounded-xl border border-slate-300/30">
                                    <table class="ui-table w-full text-sm">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Diferencia</th>
                                            <th>Motivo</th>
                                            <th>Mensaje</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $recentClosedSessions->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentSession): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php
                                                $rowDifference = (float) ($recentSession->difference ?? 0);
                                            ?>
                                            <tr>
                                                <td><?php echo e($recentSession->closed_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                                <td>
                                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $recentSession->wasAutoClosedAtMidnight() ? 'warning' : 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($recentSession->wasAutoClosedAtMidnight() ? 'warning' : 'info')]); ?>
                                                        <?php echo e($recentSession->closeSourceLabel()); ?>

                                                     <?php echo $__env->renderComponent(); ?>
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
                                                <td class="font-semibold <?php echo e($rowDifference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($rowDifference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200')); ?>">
                                                    <?php echo e($currencyFormatter::format($rowDifference, $currencyCode)); ?>

                                                </td>
                                                <td><?php echo e($recentSession->difference_reason ?: 'Sin novedad'); ?></td>
                                                <td>
                                                    <div class="space-y-1">
                                                        <p><?php echo e(\Illuminate\Support\Str::limit($recentSession->closeMessage(), 52)); ?></p>
                                                        <?php if(filled($recentSession->closing_notes)): ?>
                                                            <p class="text-xs text-slate-500 dark:text-slate-300"><?php echo e(\Illuminate\Support\Str::limit((string) $recentSession->closing_notes, 72)); ?></p>
                                                        <?php endif; ?>
                                                        <p class="text-xs text-slate-400 dark:text-slate-500">
                                                            <?php echo e($recentSession->wasAutoClosedAtMidnight() ? 'Sistema' : ($recentSession->closedBy?->name ?? '-')); ?>

                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-sm text-slate-500 dark:text-slate-300">
                                                    Aun no hay cierres registrados.
                                                </td>
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

                        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($canCloseCash ? 'Cerrar turno' : 'Cierre restringido').'','subtitle' => ''.e($canCloseCash ? 'Conteo por metodo y control de diferencias.' : 'Solo usuarios autorizados pueden ver y ejecutar el cierre completo.').'','class' => 'cash-surface-card cash-close-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($canCloseCash ? 'Cerrar turno' : 'Cierre restringido').'','subtitle' => ''.e($canCloseCash ? 'Conteo por metodo y control de diferencias.' : 'Solo usuarios autorizados pueden ver y ejecutar el cierre completo.').'','class' => 'cash-surface-card cash-close-card']); ?>
                            <?php if(! $canCloseCash): ?>
                                <p class="ui-alert ui-alert-warning mb-3">
                                    Tu perfil no tiene permiso para cerrar caja. Esta acción la realiza el dueño o un usuario autorizado.
                                </p>
                            <?php else: ?>

                            <div id="close-form-alert" class="hidden ui-alert ui-alert-warning"></div>

                            <div class="space-y-2 text-sm">
                                <p class="ui-muted">Esperado total: <strong><?php echo e($currencyFormatter::format($expectedTotal, $currencyCode)); ?></strong></p>
                                <p class="ui-muted">Estado de cierre:
                                    <strong id="close-status-text" class="close-status-badge" data-tone="ok">CUADRA</strong>
                                </p>
                            </div>

                            <form id="cash-close-form" method="POST" action="<?php echo e(route('cash.close')); ?>" class="mt-4 space-y-4"
                                  data-expected-cash="<?php echo e(number_format($expectedCash, 2, '.', '')); ?>"
                                  data-expected-card="<?php echo e(number_format($expectedCard, 2, '.', '')); ?>"
                                  data-expected-transfer="<?php echo e(number_format($expectedTransfer, 2, '.', '')); ?>"
                                  data-can-approve-difference="<?php echo e($canApproveDifference ? '1' : '0'); ?>">
                                <?php echo csrf_field(); ?>
                                <input id="close-closing-balance" type="hidden" name="closing_balance" value="<?php echo e(old('closing_balance')); ?>">
                                <input id="close-difference-approved" type="hidden" name="difference_approved" value="0">

                            <div class="cash-form-shell grid gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <div class="grid grid-cols-4 gap-2 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">
                                    <span>Método</span>
                                    <span class="text-right">Esperado</span>
                                    <span class="text-right">Contado</span>
                                    <span class="text-right">Diferencia</span>
                                </div>

                                <?php $__currentLoopData = ['cash' => 'efectivo_contado', 'card' => 'tarjeta_contado', 'transfer' => 'transferencia_contado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $fieldName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $expectedByMethod = $methodKey === 'cash' ? $expectedCash : ($methodKey === 'card' ? $expectedCard : $expectedTransfer);
                                    ?>
                                    <div class="grid grid-cols-4 items-center gap-2 text-sm">
                                        <span class="font-semibold text-slate-700 dark:text-slate-200"><?php echo e($methodLabels[$methodKey]); ?></span>
                                        <span class="text-right text-slate-700 dark:text-slate-200"><?php echo e($currencyFormatter::format($expectedByMethod, $currencyCode)); ?></span>
                                        <input id="counted-<?php echo e($methodKey); ?>" class="ui-input text-right" type="number" min="0" step="0.01" name="<?php echo e($fieldName); ?>" value="<?php echo e(old($fieldName, number_format($expectedByMethod, 2, '.', ''))); ?>" aria-label="Contado <?php echo e($methodLabels[$methodKey]); ?>">
                                        <span id="difference-<?php echo e($methodKey); ?>" class="text-right font-bold" data-tone="ok"><?php echo e($currencyFormatter::format(0, $currencyCode)); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div id="difference-total-card" class="cash-difference-card rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Diferencia total</span>
                                    <span id="difference-total" class="cash-difference-total font-black" data-tone="ok"><?php echo e($currencyFormatter::format(0, $currencyCode)); ?></span>
                                </div>
                            </div>

                            <label id="difference-reason-wrap" class="hidden space-y-1 text-sm font-semibold ui-muted">
                                <span>Motivo de diferencia (obligatorio si no cuadra)</span>
                                <textarea id="difference-reason" name="difference_reason" rows="2" class="ui-input" placeholder="Explica por qué hay diferencia."><?php echo e(old('difference_reason')); ?></textarea>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Notas de cierre</span>
                                <textarea name="notes" rows="3" class="ui-input"><?php echo e(old('notes')); ?></textarea>
                            </label>

                            <?php if(! $canApproveDifference): ?>
                                <p class="ui-alert ui-alert-warning text-xs">Solo Admin puede confirmar cierre con diferencia.</p>
                            <?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'close-submit','type' => 'submit','variant' => 'danger','size' => 'lg','class' => 'w-full justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'close-submit','type' => 'submit','variant' => 'danger','size' => 'lg','class' => 'w-full justify-center']); ?>Cerrar turno <?php echo $__env->renderComponent(); ?>
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
                            <?php endif; ?>
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
                </section>

                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tus últimos 10 movimientos' : 'últimos 10 movimientos').'','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tus últimos 10 movimientos' : 'últimos 10 movimientos').'','class' => 'cash-surface-card']); ?>
                    <div class="cash-table-shell overflow-x-auto">
                        <table class="ui-table min-w-[1180px]">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Cliente</th>
                                <th>Alta cliente</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $activeMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $movementIsVoided = (string) ($movement->status ?? '') === 'voided' || !empty($movement->voided_at) || !empty($movement->void_reason);
                                ?>
                                <tr class="<?php echo e($movementIsVoided ? 'opacity-70' : ''); ?>">
                                    <td><?php echo e($movement->occurred_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                    <td><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $movement->type === 'income' ? 'success' : 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($movement->type === 'income' ? 'success' : 'danger')]); ?><?php echo e($movement->type); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                                    <td><?php echo e($methodLabels[$movement->method] ?? $movement->method); ?></td>
                                    <td class="font-semibold <?php echo e($movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'); ?>">
                                        <?php echo e($movement->type === 'income' ? '+' : '-'); ?><?php echo e($currencyFormatter::format((float) $movement->amount, $currencyCode, true)); ?>

                                    </td>
                                    <td><?php echo e($movement->membership?->client?->full_name ?? '-'); ?></td>
                                    <td><?php echo e(\App\Support\ClientAudit::actorDisplay((string) ($movement->membership?->client?->created_by_name_snapshot ?? ''), (string) ($movement->membership?->client?->created_by_role_snapshot ?? ''))); ?></td>
                                    <td><?php echo e($movement->createdBy?->name ?? '-'); ?></td>
                                    <td><?php echo e($movement->description ?: '-'); ?></td>
                                    <td>
                                        <?php if($movementIsVoided): ?>
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning']); ?>Anulado <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                        <?php else: ?>
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success']); ?>Activo <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if($movementIsVoided): ?>
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Sin acción</span>
                                        <?php elseif(! $isCashAdmin): ?>
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Solo Admin</span>
                                        <?php elseif(! $routeHasVoidMovement): ?>
                                            <button type="button" class="ui-button ui-button-muted px-2 py-1 text-xs" disabled title="Falta route cash.movements.void">Anular</button>
                                        <?php else: ?>
                                            <button type="button" class="ui-button ui-button-danger px-2 py-1 text-xs js-open-void-modal" data-movement-id="<?php echo e($movement->id); ?>" data-movement-label="#<?php echo e($movement->id); ?> <?php echo e($movement->type); ?> <?php echo e($currencyFormatter::format((float) $movement->amount, $currencyCode)); ?>" aria-label="Anular movimiento <?php echo e($movement->id); ?>" title="Anular movimiento">Anular</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-sm text-slate-500 dark:text-slate-300"><?php echo e($isCashierScoped ? 'Aún no tienes movimientos en este turno.' : 'Aún no hay movimientos en este turno.'); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="quick-actions mt-4">
                        <p class="mb-3 text-xs font-black uppercase tracking-[0.14em] text-slate-400">Acciones rapidas de caja</p>
                        <div class="quick-actions-grid">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $cashRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $cashRouteParams)),'variant' => 'primary']); ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 19h6"/>
                                    <path d="M19 16v6"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M3 21a6 6 0 0 1 12 0"/>
                                </svg>
                                <span>Cobrar membresia</span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','id' => 'open-monthly-movements-modal','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','id' => 'open-monthly-movements-modal','variant' => 'secondary']); ?>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M8 2v4"/>
                                    <path d="M16 2v4"/>
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                <span><?php echo e($isCashierScoped ? 'Ver mis movimientos del mes' : 'Ver movimientos del mes'); ?></span>
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
                            <?php if(! $isCashierUser || $canCloseCash): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'cash-go-history','href' => route('cash.sessions.index', $cashRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'cash-go-history','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.sessions.index', $cashRouteParams)),'variant' => 'secondary']); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3h18v18H3z"/>
                                        <path d="M7 8h10"/>
                                        <path d="M7 12h10"/>
                                        <path d="M7 16h6"/>
                                    </svg>
                                    <span>Ver historial de caja</span>
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
                            <?php endif; ?>
                            <?php if(! $isCashierUser): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.income', $cashRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.income', $cashRouteParams)),'variant' => 'ghost']); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v18h18"/>
                                        <path d="m19 9-5 5-4-4-3 3"/>
                                    </svg>
                                    <span>Ver reporte de ingresos</span>
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
                            <?php endif; ?>
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

                <?php echo $__env->make('cash.partials.session-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php
                $historyRows = $sessions ?? collect();
            ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial de caja','subtitle' => 'Revisión de cierres, diferencias y responsables.','class' => 'cash-surface-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de caja','subtitle' => 'Revisión de cierres, diferencias y responsables.','class' => 'cash-surface-card']); ?>
                <?php if($isGlobalScope): ?>
                    <p class="mb-4 ui-alert ui-alert-info">
                        Modo global activo: historial consolidado de todas tus sedes en solo lectura.
                    </p>
                <?php endif; ?>
                <div class="cash-table-shell overflow-x-auto">
                    <table class="ui-table min-w-[1480px]">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <?php if($isGlobalScope): ?>
                                <th>Sede</th>
                            <?php endif; ?>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Apertura por</th>
                            <th>Cierre por</th>
                            <th>Tipo</th>
                            <th>Mensaje</th>
                            <th>Esperado</th>
                            <th>Cierre</th>
                            <th>Diferencia</th>
                            <th>Motivo</th>
                            <th>Notas</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $historyRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $difference = (float) ($session->difference ?? 0);
                                $closedWithDifference = (string) $session->status === 'closed' && abs($difference) > 0.00001;
                            ?>
                            <tr>
                                <td><?php echo e($session->id); ?></td>
                                <?php if($isGlobalScope): ?>
                                    <td><?php echo e($session->gym?->name ?? '-'); ?></td>
                                <?php endif; ?>
                                <td><?php echo e($session->opened_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td><?php echo e($session->closed_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td><?php echo e($session->openedBy?->name ?? '-'); ?></td>
                                <td><?php echo e($session->wasAutoClosedAtMidnight() ? 'Sistema' : ($session->closedBy?->name ?? '-')); ?></td>
                                <td><?php echo e($session->closeSourceLabel()); ?></td>
                                <td><?php echo e($session->closeMessage()); ?></td>
                                <td><?php echo e($currencyFormatter::format((float) ($session->expected_balance ?? 0), $currencyCode)); ?></td>
                                <td><?php echo e($session->closing_balance !== null ? $currencyFormatter::format((float) $session->closing_balance, $currencyCode) : '-'); ?></td>
                                <td class="font-bold <?php echo e($difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200')); ?>"><?php echo e($currencyFormatter::format($difference, $currencyCode)); ?></td>
                                <td><?php echo e($session->difference_reason ?: '-'); ?></td>
                                <td><?php echo e($session->closing_notes ?: '-'); ?></td>
                                <td>
                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => (string) $session->status === 'open' ? 'info' : 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $session->status === 'open' ? 'info' : 'success')]); ?><?php echo e($session->status); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    <?php if($closedWithDifference): ?>
                                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning']); ?>Cerro con diferencia <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.sessions.show', $session->id),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.sessions.show', $session->id)),'variant' => 'ghost','size' => 'sm']); ?>Detalle <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($isGlobalScope ? 15 : 14); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay sesiones registradas.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(method_exists($historyRows, 'links')): ?>
                    <div class="mt-4"><?php echo e($historyRows->links()); ?></div>
                <?php endif; ?>
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
    </div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/cash/index.blade.php ENDPATH**/ ?>