<?php
    $quickActionRouteParams = ['contextGym' => $activeGymSlug]
        + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : []);
    $quickActionReturnUrl = request()->fullUrl();
    $quickActionCurrencyFormatter = \App\Support\Currency::class;
    $quickActionCurrencyCode = (string) ($appCurrencyCode ?? 'USD');
    $quickActionsVisible = ! $isSuperAdmin
        && ! $isGlobalScope
        && $activeGymId > 0
        && $activeGymSlug !== ''
        && ! request()->routeIs('client-portal.*');
    $showQuickClientAction = $quickActionsVisible && \Illuminate\Support\Facades\Route::has('clients.store');
    $showQuickSaleAction = $quickActionsVisible && $canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.store');

    $renderQuickClientModal = $showQuickClientAction && ! request()->routeIs('clients.index');
    $renderQuickSaleModal = $showQuickSaleAction && ! request()->routeIs('sales.index');

    $quickClientAutoOpen = $renderQuickClientModal && old('quick_action_form') === 'quick-client';
    $quickSaleAutoOpen = $renderQuickSaleModal && old('quick_action_form') === 'quick-sale';

    $quickClientCanManagePromotions = false;
    $quickClientCanManageAccounts = false;
    $quickClientPlans = collect();
    $quickClientPromotions = collect();
    $quickClientPlanCatalog = collect();
    $quickClientPromotionCatalog = collect();
    $quickClientErrorMessages = collect();

    if ($renderQuickClientModal) {
        $quickPlanAccessService = app(\App\Services\PlanAccessService::class);
        $quickClientCanManagePromotions = $quickPlanAccessService->canForGym($activeGymId, 'promotions');
        $quickClientCanManageAccounts = $quickPlanAccessService->canForGym($activeGymId, 'client_accounts');
        $quickClientToday = now()->toDateString();

        $quickClientPlans = \App\Models\Plan::query()
            ->forGym($activeGymId)
            ->active()
            ->select(['id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price'])
            ->orderBy('name')
            ->get();

        if ($quickClientCanManagePromotions) {
            $quickClientPromotions = \App\Models\Promotion::query()
                ->forGym($activeGymId)
                ->active()
                ->applicableOn($quickClientToday)
                ->select([
                    'id',
                    'plan_id',
                    'name',
                    'type',
                    'value',
                    'starts_at',
                    'ends_at',
                    'max_uses',
                    'times_used',
                ])
                ->orderByDesc('id')
                ->get();
        }

        $quickClientPlanCatalog = $quickClientPlans
            ->map(fn ($plan) => [
                'id' => (int) $plan->id,
                'name' => (string) $plan->name,
                'duration_days' => (int) $plan->duration_days,
                'duration_unit' => \App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days')),
                'duration_months' => $plan->duration_months !== null ? (int) $plan->duration_months : null,
                'price' => (float) $plan->price,
            ])
            ->values();

        $quickClientPromotionCatalog = $quickClientPromotions
            ->map(fn ($promotion) => [
                'id' => (int) $promotion->id,
                'plan_id' => $promotion->plan_id !== null ? (int) $promotion->plan_id : null,
                'name' => (string) $promotion->name,
                'type' => (string) $promotion->type,
                'value' => (float) ($promotion->value ?? 0),
                'starts_at' => optional($promotion->starts_at)?->toDateString(),
                'ends_at' => optional($promotion->ends_at)?->toDateString(),
                'max_uses' => $promotion->max_uses !== null ? (int) $promotion->max_uses : null,
                'times_used' => (int) ($promotion->times_used ?? 0),
            ])
            ->values();

        $quickClientErrorKeys = [
            'first_name',
            'last_name',
            'document_number',
            'phone',
            'gender',
            'photo',
            'start_membership',
            'plan_id',
            'membership_starts_at',
            'membership_price',
            'promotion_id',
            'payment_method',
            'amount_paid',
            'create_app_account',
            'app_username',
            'app_password',
            'app_password_confirmation',
            'cash',
        ];
        $quickClientErrorMessages = collect($quickClientErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->filter()
            ->unique()
            ->values();
    }

    $quickSaleSchemaReady = false;
    $quickSaleHasOpenCashSession = false;
    $quickSaleProducts = collect();
    $quickSaleClients = collect();
    $quickSaleProductsPayload = collect();
    $quickSaleErrorMessages = collect();
    $quickSaleSelectedProductId = 0;
    $quickSaleSelectedClientId = 0;

    if ($renderQuickSaleModal) {
        $quickSaleSchemaReady = \Illuminate\Support\Facades\Schema::hasTable('products')
            && \Illuminate\Support\Facades\Schema::hasColumn('products', 'barcode')
            && \Illuminate\Support\Facades\Schema::hasTable('product_sales')
            && \Illuminate\Support\Facades\Schema::hasTable('product_stock_movements');

        if ($quickSaleSchemaReady) {
            $quickSaleHasOpenCashSession = app(\App\Services\CashSessionService::class)->getOpenSession($activeGymId) !== null;

            $quickSaleProducts = \App\Models\Product::query()
                ->forGym($activeGymId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'barcode', 'stock', 'sale_price']);

            $quickSaleClients = \App\Models\Client::query()
                ->forGym($activeGymId)
                ->select(['id', 'first_name', 'last_name'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(150)
                ->get();

            $quickSaleProductsPayload = $quickSaleProducts
                ->map(fn ($product) => [
                    'id' => (int) $product->id,
                    'name' => (string) $product->name,
                    'sku' => (string) ($product->sku ?? ''),
                    'barcode' => (string) ($product->barcode ?? ''),
                    'stock' => (int) $product->stock,
                    'sale_price' => round((float) $product->sale_price, 2),
                ])
                ->values();
        }

        $quickSaleSelectedProductId = (int) old('product_id', 0);
        $quickSaleSelectedClientId = (int) old('client_id', 0);
        $quickSaleErrorKeys = ['client_id', 'payment_method', 'notes', 'sale_items_payload', 'product_id', 'quantity', 'sales'];
        $quickSaleErrorMessages = collect($quickSaleErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->filter()
            ->unique()
            ->values();
    }
?>

<?php if($showQuickClientAction || $showQuickSaleAction || $renderQuickClientModal || $renderQuickSaleModal): ?>
    <style>
            .quick-access-fab {
                position: fixed;
                z-index: 88;
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                min-height: 3.1rem;
                min-width: 3.1rem;
                border-radius: 999px;
                border: 1px solid color-mix(in srgb, var(--accent) 30%, var(--border));
                background:
                    radial-gradient(circle at top left, color-mix(in srgb, var(--accent) 18%, transparent), transparent 42%),
                    linear-gradient(145deg, color-mix(in srgb, var(--card) 96%, white), color-mix(in srgb, var(--card-2) 82%, transparent));
                color: color-mix(in srgb, var(--text) 96%, white);
                padding: 0.62rem 0.82rem;
                box-shadow: 0 24px 46px -30px rgb(15 23 42 / 0.42);
                user-select: none;
                -webkit-user-select: none;
                -webkit-touch-callout: none;
                touch-action: manipulation;
                transition: transform 140ms ease, box-shadow 140ms ease, border-color 140ms ease, opacity 160ms ease;
                backdrop-filter: blur(12px);
            }

            .quick-access-fab[data-fab-ready="0"] {
                opacity: 1;
            }

            .quick-access-fab[hidden] {
                display: none;
            }

            #quick-access-client-fab {
                left: 14px;
                bottom: 22px;
            }

            #quick-access-sale-fab {
                left: 14px;
                bottom: 84px;
            }

            .quick-access-fab:hover {
                transform: translateY(-2px);
                box-shadow: 0 28px 52px -30px rgb(15 23 42 / 0.5);
            }

            .quick-access-fab.is-dragging {
                transform: scale(1.03);
                box-shadow: 0 32px 56px -28px rgb(2 6 23 / 0.56);
                border-color: color-mix(in srgb, var(--accent) 56%, var(--border));
                cursor: grabbing;
            }

            .quick-access-fab[data-tone="client"] {
                --quick-accent: #14b8a6;
            }

            .quick-access-fab[data-tone="sale"] {
                --quick-accent: #f59e0b;
            }

            .quick-access-fab__icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2rem;
                height: 2rem;
                border-radius: 999px;
                background: radial-gradient(circle at 30% 30%, color-mix(in srgb, white 92%, var(--quick-accent, var(--accent)) 8%), color-mix(in srgb, var(--quick-accent, var(--accent)) 28%, white));
                color: color-mix(in srgb, var(--sidebar) 78%, #082f49);
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.8);
                flex-shrink: 0;
            }

            .quick-access-fab__copy {
                min-width: 0;
                display: grid;
                gap: 0;
                text-align: left;
            }

            .quick-access-fab__eyebrow {
                display: none;
            }

            .quick-access-fab__label {
                font-size: 0.84rem;
                font-weight: 900;
                letter-spacing: -0.01em;
                line-height: 1;
                white-space: nowrap;
            }

            .floating-menu-switch {
                border: 0;
                background: transparent;
                padding: 0;
                cursor: pointer;
                appearance: none;
                -webkit-appearance: none;
            }

            .floating-menu-switch:disabled,
            .floating-menu-switch.is-disabled {
                cursor: not-allowed;
            }

            .floating-menu-switch__track {
                position: relative;
                display: inline-flex;
                align-items: center;
                justify-content: space-between;
                width: 4.9rem;
                min-width: 4.9rem;
                height: 2.15rem;
                border-radius: 999px;
                border: 1px solid rgb(148 163 184 / 0.4);
                background: linear-gradient(180deg, rgb(15 23 42 / 0.22), rgb(15 23 42 / 0.1));
                padding: 0 0.62rem;
                box-shadow:
                    inset 0 1px 0 rgb(255 255 255 / 0.16),
                    0 10px 24px -18px rgb(15 23 42 / 0.42);
                transition: background 160ms ease, border-color 160ms ease, box-shadow 160ms ease, opacity 160ms ease;
            }

            .floating-menu-switch__text {
                position: relative;
                z-index: 1;
                font-size: 0.8rem;
                font-weight: 900;
                line-height: 1;
                letter-spacing: 0.02em;
                transition: color 160ms ease, opacity 160ms ease;
            }

            .floating-menu-switch__thumb {
                position: absolute;
                top: 0.18rem;
                left: 0.18rem;
                width: 1.75rem;
                height: 1.75rem;
                border-radius: 999px;
                background: linear-gradient(180deg, rgb(255 255 255), rgb(226 232 240));
                box-shadow:
                    0 8px 18px -12px rgb(15 23 42 / 0.62),
                    inset 0 1px 0 rgb(255 255 255 / 0.88);
                transition: transform 180ms ease, background 180ms ease;
            }

            .floating-menu-switch[aria-checked="true"] .floating-menu-switch__track {
                background: linear-gradient(180deg, rgb(99 102 241 / 0.38), rgb(99 102 241 / 0.26));
                border-color: rgb(165 180 252 / 0.7);
                box-shadow:
                    inset 0 1px 0 rgb(255 255 255 / 0.22),
                    0 12px 24px -18px rgb(99 102 241 / 0.58);
            }

            .floating-menu-switch[data-switch-tone="client"][aria-checked="true"] .floating-menu-switch__track {
                background: linear-gradient(180deg, rgb(16 185 129 / 0.34), rgb(16 185 129 / 0.24));
                border-color: rgb(110 231 183 / 0.7);
            }

            .floating-menu-switch[data-switch-tone="sale"][aria-checked="true"] .floating-menu-switch__track {
                background: linear-gradient(180deg, rgb(245 158 11 / 0.34), rgb(245 158 11 / 0.22));
                border-color: rgb(253 224 71 / 0.7);
            }

            .floating-menu-switch[aria-checked="true"] .floating-menu-switch__thumb {
                transform: translateX(2.55rem);
                background: linear-gradient(180deg, rgb(255 255 255), rgb(238 242 255));
            }

            .floating-menu-switch .floating-menu-switch__text--on {
                color: rgb(255 255 255 / 0.96);
            }

            .floating-menu-switch .floating-menu-switch__text--off {
                color: rgb(100 116 139 / 0.82);
            }

            .floating-menu-switch[aria-checked="false"] .floating-menu-switch__text--on {
                color: rgb(148 163 184 / 0.45);
            }

            .floating-menu-switch[aria-checked="false"] .floating-menu-switch__text--off {
                color: rgb(255 255 255 / 0.96);
            }

            .floating-menu-switch.is-disabled .floating-menu-switch__track,
            .floating-menu-switch:disabled .floating-menu-switch__track {
                opacity: 0.58;
                box-shadow: none;
            }

            .quick-access-modal-shell {
                border-color: color-mix(in srgb, var(--accent) 24%, var(--border));
                background:
                    radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 12%, transparent), transparent 32%),
                    linear-gradient(180deg, color-mix(in srgb, var(--card) 96%, #ffffff), color-mix(in srgb, var(--card-2) 86%, transparent));
                color: color-mix(in srgb, var(--text) 96%, #fff);
                box-shadow: 0 34px 64px -38px rgb(15 23 42 / 0.46);
            }

            .quick-access-modal-shell .ui-modal-sticky-footer {
                border-top-color: color-mix(in srgb, var(--border) 84%, transparent);
                background: color-mix(in srgb, var(--card) 94%, transparent);
            }

            .quick-access-hint {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                border-radius: 999px;
                border: 1px solid color-mix(in srgb, var(--accent) 18%, var(--border));
                background: color-mix(in srgb, var(--card) 88%, transparent);
                padding: 0.35rem 0.7rem;
                font-size: 0.68rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--muted) 88%, var(--text));
            }

            .quick-access-panel {
                border: 1px solid color-mix(in srgb, var(--accent) 14%, var(--border));
                border-radius: 1.2rem;
                background:
                    linear-gradient(180deg, color-mix(in srgb, var(--card) 94%, #fff), color-mix(in srgb, var(--card-2) 86%, transparent));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.08);
            }

            .quick-access-doc-state,
            .quick-access-summary-list {
                display: none;
            }

            .quick-access-doc-state.is-visible,
            .quick-access-summary-list.is-visible {
                display: block;
            }

            .quick-access-summary-list ul {
                list-style: disc;
                padding-left: 1.25rem;
                margin-top: 0.45rem;
            }

            .quick-access-membership-grid {
                display: grid;
                gap: 1rem;
            }

            .quick-access-sale-panel {
                border-color: color-mix(in srgb, #38bdf8 28%, var(--border));
            }

            .quick-access-sale-shell {
                border: 1px solid color-mix(in srgb, #38bdf8 18%, var(--border));
                border-radius: 1.2rem;
                background:
                    radial-gradient(circle at top right, rgb(6 182 212 / 0.08), transparent 30%),
                    linear-gradient(180deg, color-mix(in srgb, var(--card) 95%, #fff), color-mix(in srgb, var(--card-2) 84%, transparent));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.08);
            }

            .quick-access-sale-scan {
                border-radius: 1rem;
                border: 1px solid rgb(34 211 238 / 0.24);
                background: rgb(34 211 238 / 0.08);
                padding: 1rem;
            }

            .quick-access-sale-feedback,
            .quick-access-sale-preview,
            .quick-access-sale-list {
                display: none;
            }

            .quick-access-sale-feedback.is-visible,
            .quick-access-sale-preview.is-visible,
            .quick-access-sale-list.is-visible {
                display: block;
            }

            .quick-access-sale-list-items {
                display: grid;
                gap: 0.7rem;
            }

            .quick-access-sale-list-items.is-scrollable {
                max-height: 16rem;
                overflow-y: auto;
                padding-right: 0.2rem;
            }

            .quick-access-sale-line {
                border-radius: 1rem;
                border: 1px solid color-mix(in srgb, var(--border) 88%, transparent);
                background: color-mix(in srgb, var(--card) 90%, transparent);
                padding: 0.8rem;
            }

            .quick-access-sale-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            @media (max-width: 1023px) {
                .quick-access-fab {
                    min-height: 2.95rem;
                    padding: 0.58rem 0.76rem;
                }

                .quick-access-fab__icon {
                    width: 1.85rem;
                    height: 1.85rem;
                }
            }

            @media (max-width: 640px) {
                .quick-access-fab {
                    padding: 0.56rem 0.72rem;
                    min-height: 2.8rem;
                    max-width: min(11.25rem, calc(100vw - 1.25rem));
                }

                .quick-access-fab__label {
                    font-size: 0.78rem;
                }

                .quick-access-sale-actions {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr);
                }

                .quick-access-sale-actions .ui-button {
                    width: 100%;
                }
            }
    </style>

    <?php if($showQuickClientAction): ?>
        <button type="button"
                class="quick-access-fab"
                id="quick-access-client-fab"
                data-quick-action="client"
                data-tone="client"
                data-order="0"
                data-fab-ready="0"
                title="Toca para crear un cliente. Mantén presionado para mover el botón.">
            <span class="quick-access-fab__icon" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 12 12Z" stroke="currentColor" stroke-width="1.9"/>
                    <path d="M5.5 19a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                    <path d="M18.5 6.5v4m2-2h-4" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="quick-access-fab__copy">
                <span class="quick-access-fab__eyebrow">Acceso rápido</span>
                <span class="quick-access-fab__label">Cliente +</span>
            </span>
        </button>
    <?php endif; ?>

    <?php if($showQuickSaleAction): ?>
        <button type="button"
                class="quick-access-fab"
                id="quick-access-sale-fab"
                data-quick-action="sale"
                data-tone="sale"
                data-order="1"
                data-fab-ready="0"
                title="Toca para registrar una venta. Mantén presionado para mover el botón.">
            <span class="quick-access-fab__icon" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <path d="M7.5 8.5V7a2.5 2.5 0 0 1 5 0v1.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                    <path d="M5.5 8.5h9l-.75 8.25A1.75 1.75 0 0 1 12 18.5H8a1.75 1.75 0 0 1-1.74-1.75L5.5 8.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                    <path d="m16 10.5 1.5-1.5 3 3-4.75 4.75H13v-2.75L16 10.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="quick-access-fab__copy">
                <span class="quick-access-fab__eyebrow">Acceso rápido</span>
                <span class="quick-access-fab__label">Venta rápida</span>
            </span>
        </button>
    <?php endif; ?>
<?php endif; ?>

<?php if($showQuickClientAction || $showQuickSaleAction || $renderQuickClientModal || $renderQuickSaleModal): ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            (function () {
                const clientFab = document.getElementById('quick-access-client-fab');
                const saleFab = document.getElementById('quick-access-sale-fab');
                const quickFabs = [clientFab, saleFab].filter(Boolean);
                const clientModal = document.getElementById('quick-client-modal');
                const saleModal = document.getElementById('quick-sale-modal');
                const floatingMenuToggleButton = document.getElementById('user-menu-floating-actions-toggle');
                const floatingMenuPanel = document.getElementById('user-menu-floating-actions-panel');
                const floatingMenuStateBadge = document.getElementById('user-menu-floating-actions-state');
                const floatingMenuChevron = document.getElementById('user-menu-floating-actions-chevron');
                const quickAccessStoragePrefix = ['gym-quick-access-v4', <?php echo json_encode($activeGymSlug, 15, 512) ?>, String(<?php echo json_encode($activeGymId || 0, 15, 512) ?>)].join(':');
                const quickAccessPreferencesKey = [quickAccessStoragePrefix, 'preferences'].join(':');
                const toggleTargets = {
                    all: document.querySelector('[data-floating-toggle="all"]'),
                    client: document.querySelector('[data-floating-toggle="client"]'),
                    sale: document.querySelector('[data-floating-toggle="sale"]'),
                };

                function defaultVisibilityPreferences() {
                    return {
                        enabled: true,
                        client: Boolean(clientFab),
                        sale: Boolean(saleFab),
                    };
                }

                function normalizeVisibilityPreferences(raw) {
                    const defaults = defaultVisibilityPreferences();
                    const source = raw && typeof raw === 'object' ? raw : {};

                    return {
                        enabled: typeof source.enabled === 'boolean' ? source.enabled : defaults.enabled,
                        client: clientFab ? (typeof source.client === 'boolean' ? source.client : defaults.client) : false,
                        sale: saleFab ? (typeof source.sale === 'boolean' ? source.sale : defaults.sale) : false,
                    };
                }

                function loadVisibilityPreferences() {
                    try {
                        return normalizeVisibilityPreferences(JSON.parse(window.localStorage.getItem(quickAccessPreferencesKey) || 'null'));
                    } catch (error) {
                        return normalizeVisibilityPreferences(null);
                    }
                }

                function persistVisibilityPreferences() {
                    try {
                        window.localStorage.setItem(quickAccessPreferencesKey, JSON.stringify(visibilityPreferences));
                    } catch (error) {
                    }
                }

                let visibilityPreferences = loadVisibilityPreferences();

                function setFloatingMenuPanelOpen(isOpen) {
                    if (!floatingMenuPanel || !floatingMenuToggleButton) {
                        return;
                    }

                    floatingMenuPanel.classList.toggle('hidden', !isOpen);
                    floatingMenuToggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    floatingMenuChevron?.classList.toggle('rotate-180', isOpen);
                }

                function setSwitchState(element, isOn) {
                    if (!element) {
                        return;
                    }

                    element.setAttribute('aria-checked', isOn ? 'true' : 'false');
                }

                function syncFabStackOrder() {
                    let visibleIndex = 0;

                    quickFabs.forEach(function (element) {
                        const action = element.dataset.quickAction || '';
                        const isVisible = visibilityPreferences.enabled && visibilityPreferences[action] !== false;
                        element.dataset.stackOrder = String(visibleIndex);
                        if (isVisible) {
                            visibleIndex += 1;
                        }
                    });
                }

                function updateFloatingMenuState() {
                    const activeCount = ['client', 'sale'].filter(function (action) {
                        return (action === 'client' ? clientFab : saleFab) && visibilityPreferences.enabled && visibilityPreferences[action];
                    }).length;

                    if (toggleTargets.all) {
                        setSwitchState(toggleTargets.all, visibilityPreferences.enabled);
                    }

                    ['client', 'sale'].forEach(function (action) {
                        const button = toggleTargets[action];
                        if (!button) {
                            return;
                        }

                        setSwitchState(button, Boolean(visibilityPreferences[action]));
                        button.disabled = !visibilityPreferences.enabled;
                        button.setAttribute('aria-disabled', !visibilityPreferences.enabled ? 'true' : 'false');
                        button.classList.toggle('is-disabled', !visibilityPreferences.enabled);
                        button.closest('label')?.classList.toggle('opacity-60', !visibilityPreferences.enabled);
                    });

                    if (!floatingMenuStateBadge) {
                        return;
                    }

                    if (!visibilityPreferences.enabled || activeCount === 0) {
                        floatingMenuStateBadge.textContent = 'Apagados';
                        return;
                    }

                    floatingMenuStateBadge.textContent = activeCount === 1 ? '1 activo' : 'Activos';
                }

                function openModal(modal, focusSelector) {
                    if (!modal) {
                        return;
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden';

                    if (focusSelector) {
                        window.setTimeout(function () {
                            const focusTarget = modal.querySelector(focusSelector);
                            focusTarget?.focus();
                        }, 40);
                    }
                }

                function closeModal(modal) {
                    if (!modal) {
                        return;
                    }

                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = '';
                }

                function resolveNativeClientTrigger() {
                    return document.getElementById('clients-open-create');
                }

                function resolveNativeSaleTrigger() {
                    return document.getElementById('open-sales-register-modal');
                }

                function openQuickAction(action) {
                    if (action === 'client') {
                        const nativeButton = resolveNativeClientTrigger();
                        if (nativeButton) {
                            nativeButton.click();
                            return;
                        }

                        openModal(clientModal, '[name="first_name"]');
                        return;
                    }

                    if (action === 'sale') {
                        const nativeButton = resolveNativeSaleTrigger();
                        if (nativeButton) {
                            nativeButton.click();
                            return;
                        }

                        openModal(saleModal, 'select[name="payment_method"]');
                    }
                }

                function topClearance() {
                    const header = document.querySelector('header.theme-header');
                    const height = header instanceof HTMLElement ? header.getBoundingClientRect().height : 72;
                    return Math.max(12, Math.round(height + 10));
                }

                function bottomClearance() {
                    return window.innerWidth < 1024 ? 108 : 22;
                }

                function defaultLeftOffset() {
                    if (window.innerWidth < 1024) {
                        return 14;
                    }

                    const sidebar = document.getElementById('panel-sidebar');
                    if (!sidebar) {
                        return 14;
                    }

                    const rect = sidebar.getBoundingClientRect();
                    return Math.max(14, Math.round(rect.right + 16));
                }

                function clampPosition(element, x, y) {
                    const rect = element.getBoundingClientRect();
                    const minX = 12;
                    const minY = topClearance();
                    const maxX = Math.max(minX, window.innerWidth - rect.width - 12);
                    const maxY = Math.max(minY, window.innerHeight - rect.height - bottomClearance());

                    return {
                        x: Math.min(Math.max(x, minX), maxX),
                        y: Math.min(Math.max(y, minY), maxY),
                    };
                }

                function storageKeyFor(element) {
                    return [quickAccessStoragePrefix, element.dataset.quickAction || 'fab'].join(':');
                }

                function setFabPosition(element, x, y, persist) {
                    const next = clampPosition(element, x, y);
                    element.style.left = next.x + 'px';
                    element.style.top = next.y + 'px';
                    element.style.right = 'auto';
                    element.style.bottom = 'auto';
                    element.setAttribute('data-fab-ready', '1');

                    if (!persist) {
                        return;
                    }

                    try {
                        window.localStorage.setItem(storageKeyFor(element), JSON.stringify(next));
                    } catch (error) {
                    }
                }

                function defaultFabPosition(element) {
                    const order = Number(element.dataset.stackOrder || element.dataset.order || '0');
                    const rect = element.getBoundingClientRect();
                    const x = defaultLeftOffset();
                    const y = Math.max(topClearance(), window.innerHeight - bottomClearance() - rect.height - (order * (rect.height + 10)));

                    return clampPosition(element, x, y);
                }

                function restoreFabPosition(element) {
                    let stored = null;

                    try {
                        stored = window.localStorage.getItem(storageKeyFor(element));
                    } catch (error) {
                        stored = null;
                    }

                    if (!stored) {
                        const fallback = defaultFabPosition(element);
                        setFabPosition(element, fallback.x, fallback.y, false);
                        return;
                    }

                    try {
                        const parsed = JSON.parse(stored);
                        const x = Number(parsed && parsed.x);
                        const y = Number(parsed && parsed.y);
                        if (!Number.isFinite(x) || !Number.isFinite(y)) {
                            throw new Error('invalid-position');
                        }

                        setFabPosition(element, x, y, true);
                    } catch (error) {
                        const fallback = defaultFabPosition(element);
                        setFabPosition(element, fallback.x, fallback.y, false);
                    }
                }

                function bindDraggableFab(element) {
                    if (!element) {
                        return;
                    }

                    const state = {
                        active: false,
                        suppressClick: false,
                        pointerId: null,
                        startPointerX: 0,
                        startPointerY: 0,
                        startFabX: 0,
                        startFabY: 0,
                        holdTimer: null,
                    };

                    function clearHoldTimer() {
                        if (state.holdTimer) {
                            window.clearTimeout(state.holdTimer);
                            state.holdTimer = null;
                        }
                    }

                    function releaseDrag() {
                        clearHoldTimer();
                        if (state.pointerId !== null) {
                            try {
                                element.releasePointerCapture(state.pointerId);
                            } catch (error) {
                            }
                        }

                        element.classList.remove('is-dragging');
                        state.active = false;
                        state.pointerId = null;
                        window.setTimeout(function () {
                            state.suppressClick = false;
                        }, 120);
                    }

                    element.addEventListener('pointerdown', function (event) {
                        if (event.button !== 0) {
                            return;
                        }

                        const rect = element.getBoundingClientRect();
                        state.pointerId = event.pointerId;
                        state.startPointerX = event.clientX;
                        state.startPointerY = event.clientY;
                        state.startFabX = rect.left;
                        state.startFabY = rect.top;
                        state.suppressClick = false;

                        clearHoldTimer();
                        state.holdTimer = window.setTimeout(function () {
                            state.active = true;
                            state.suppressClick = true;
                            element.classList.add('is-dragging');
                            try {
                                element.setPointerCapture(event.pointerId);
                            } catch (error) {
                            }
                        }, event.pointerType === 'touch' ? 260 : 180);
                    });

                    element.addEventListener('pointermove', function (event) {
                        if (event.pointerId !== state.pointerId) {
                            return;
                        }

                        const deltaX = event.clientX - state.startPointerX;
                        const deltaY = event.clientY - state.startPointerY;

                        if (!state.active) {
                            if (Math.abs(deltaX) > 12 || Math.abs(deltaY) > 12) {
                                clearHoldTimer();
                            }
                            return;
                        }

                        event.preventDefault();
                        setFabPosition(element, state.startFabX + deltaX, state.startFabY + deltaY, false);
                    });

                    ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(function (eventName) {
                        element.addEventListener(eventName, function (event) {
                            if (state.active) {
                                event.preventDefault();
                                const rect = element.getBoundingClientRect();
                                setFabPosition(element, rect.left, rect.top, true);
                            }

                            releaseDrag();
                        });
                    });

                    element.addEventListener('click', function (event) {
                        if (state.suppressClick) {
                            event.preventDefault();
                            event.stopPropagation();
                            return;
                        }

                        openQuickAction(element.dataset.quickAction || '');
                    });
                }

                function reClampFabs() {
                    quickFabs.forEach(function (element) {
                        if (!element || element.hidden) {
                            return;
                        }

                        const rect = element.getBoundingClientRect();
                        setFabPosition(element, rect.left, rect.top, true);
                    });
                }

                function applyFabVisibility() {
                    syncFabStackOrder();

                    quickFabs.forEach(function (element) {
                        const action = element.dataset.quickAction || '';
                        const shouldShow = visibilityPreferences.enabled && visibilityPreferences[action] !== false;

                        if (shouldShow) {
                            const parsedLeft = Number.parseFloat(element.style.left);
                            const parsedTop = Number.parseFloat(element.style.top);
                            element.hidden = false;
                            element.removeAttribute('aria-hidden');
                            element.removeAttribute('tabindex');

                            if (Number.isFinite(parsedLeft) && Number.isFinite(parsedTop)) {
                                setFabPosition(element, parsedLeft, parsedTop, true);
                            } else {
                                restoreFabPosition(element);
                            }

                            return;
                        }

                        element.hidden = true;
                        element.setAttribute('aria-hidden', 'true');
                        element.setAttribute('tabindex', '-1');
                    });

                    updateFloatingMenuState();
                }

                document.querySelectorAll('[data-quick-client-close]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        closeModal(clientModal);
                    });
                });

                document.querySelectorAll('[data-quick-sale-close]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        closeModal(saleModal);
                    });
                });

                [clientModal, saleModal].forEach(function (modal) {
                    if (!modal) {
                        return;
                    }

                    modal.addEventListener('click', function (event) {
                        if (event.target === modal) {
                            closeModal(modal);
                        }
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') {
                        return;
                    }

                    if (clientModal && clientModal.classList.contains('flex')) {
                        closeModal(clientModal);
                    }

                    if (saleModal && saleModal.classList.contains('flex')) {
                        closeModal(saleModal);
                    }
                });

                syncFabStackOrder();
                quickFabs.forEach(function (element) {
                    restoreFabPosition(element);
                    bindDraggableFab(element);
                });
                applyFabVisibility();

                floatingMenuToggleButton?.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    setFloatingMenuPanelOpen(floatingMenuPanel?.classList.contains('hidden'));
                });

                floatingMenuPanel?.addEventListener('click', function (event) {
                    event.stopPropagation();
                });

                toggleTargets.all?.addEventListener('click', function (event) {
                    event.preventDefault();
                    visibilityPreferences.enabled = !visibilityPreferences.enabled;
                    persistVisibilityPreferences();
                    applyFabVisibility();
                });

                ['client', 'sale'].forEach(function (action) {
                    const button = toggleTargets[action];
                    button?.addEventListener('click', function (event) {
                        event.preventDefault();
                        if (!visibilityPreferences.enabled) {
                            return;
                        }

                        visibilityPreferences[action] = !visibilityPreferences[action];
                        persistVisibilityPreferences();
                        applyFabVisibility();
                    });
                });

                window.addEventListener('resize', reClampFabs);
                window.addEventListener('orientationchange', reClampFabs);

                if (clientModal && clientModal.dataset.autoOpen === '1') {
                    openModal(clientModal, '[name="first_name"]');
                }

                if (saleModal && saleModal.dataset.autoOpen === '1') {
                    openModal(saleModal, 'select[name="payment_method"]');
                }

                const quickClientConfig = {
                    enabled: Boolean(<?php echo json_encode($renderQuickClientModal, 15, 512) ?>),
                    documentCheckUrl: <?php echo json_encode($renderQuickClientModal ? route('clients.check-document', $quickActionRouteParams) : '', 512) ?>,
                    plans: <?php echo json_encode($quickClientPlanCatalog, 15, 512) ?>,
                    promotions: <?php echo json_encode($quickClientPromotionCatalog, 15, 512) ?>,
                    canManageAccounts: Boolean(<?php echo json_encode($quickClientCanManageAccounts, 15, 512) ?>),
                };

                if (quickClientConfig.enabled && clientModal) {
                    const clientForm = document.getElementById('quick-client-form');
                    const clientFields = {
                        firstName: clientForm?.querySelector('[name="first_name"]'),
                        lastName: clientForm?.querySelector('[name="last_name"]'),
                        document: clientForm?.querySelector('[name="document_number"]'),
                        phone: clientForm?.querySelector('[name="phone"]'),
                        photo: clientForm?.querySelector('[data-client-photo-input]'),
                        createAppAccount: clientForm?.querySelector('[data-client-account-toggle]'),
                        appUsername: clientForm?.querySelector('[name="app_username"]'),
                        appPassword: clientForm?.querySelector('[name="app_password"]'),
                        appPasswordConfirmation: clientForm?.querySelector('[name="app_password_confirmation"]'),
                        startMembership: clientForm?.querySelector('[data-client-membership-toggle]'),
                        planId: clientForm?.querySelector('[name="plan_id"]'),
                        membershipStartsAt: clientForm?.querySelector('[name="membership_starts_at"]'),
                        membershipPrice: clientForm?.querySelector('[name="membership_price"]'),
                        promotionId: clientForm?.querySelector('[name="promotion_id"]'),
                        amountPaid: clientForm?.querySelector('[name="amount_paid"]'),
                    };
                    const docStatus = clientForm?.querySelector('[data-client-doc-status]');
                    const photoPreview = clientForm?.querySelector('[data-client-photo-preview]');
                    const avatarFallback = clientForm?.querySelector('[data-client-avatar-fallback]');
                    const accountPanel = clientForm?.querySelector('[data-client-account-panel]');
                    const membershipPanel = clientForm?.querySelector('[data-client-membership-panel]');
                    const membershipBadge = clientForm?.querySelector('[data-client-membership-badge]');
                    const membershipEnd = clientForm?.querySelector('[data-client-membership-end]');
                    const promotionSummary = clientForm?.querySelector('[data-client-promotion-summary]');
                    const paymentBadge = clientForm?.querySelector('[data-client-payment-badge]');
                    const noPlansAlert = clientForm?.querySelector('[data-client-no-plans]');
                    const state = {
                        documentState: 'idle',
                        documentTimer: null,
                    };

                    function setFieldError(field, message) {
                        const error = clientForm?.querySelector('[data-client-error="' + field + '"]');
                        if (!error) {
                            return;
                        }

                        error.textContent = message || '';
                        error.classList.toggle('hidden', String(message || '').trim() === '');
                    }

                    function clearFieldError(field) {
                        setFieldError(field, '');
                    }

                    function setDocStatus(message, tone, url) {
                        if (!docStatus) {
                            return;
                        }

                        docStatus.className = 'quick-access-doc-state rounded-lg border px-3 py-2 text-xs';
                        if (!message) {
                            docStatus.textContent = '';
                            return;
                        }

                        docStatus.classList.add('is-visible');
                        if (tone === 'error') {
                            docStatus.classList.add('border-rose-500/40', 'bg-rose-500/10', 'text-rose-200');
                        } else if (tone === 'success') {
                            docStatus.classList.add('border-emerald-500/40', 'bg-emerald-500/10', 'text-emerald-200');
                        } else {
                            docStatus.classList.add('border-slate-700', 'bg-slate-900/70', 'text-slate-300');
                        }

                        if (url) {
                            docStatus.innerHTML = message + ' <a class="font-semibold text-cyan-300 underline" href="' + url + '">Abrir cliente</a>';
                        } else {
                            docStatus.textContent = message;
                        }
                    }

                    function formatPersonName(value) {
                        const raw = String(value || '').trim().replace(/\s+/g, ' ');
                        if (raw === '') {
                            return '';
                        }

                        return raw
                            .split(/(\s+|-|')/u)
                            .map(function (segment) {
                                if (segment === '' || /^(\s+|-|')$/u.test(segment)) {
                                    return segment;
                                }

                                const chars = Array.from(segment);
                                const first = chars.shift() || '';
                                return first.toLocaleUpperCase('es-ES') + chars.join('').toLocaleLowerCase('es-ES');
                            })
                            .join('');
                    }

                    function initials() {
                        const first = String(clientFields.firstName?.value || '').trim().charAt(0);
                        const last = String(clientFields.lastName?.value || '').trim().charAt(0);
                        const value = (first + last).trim().toUpperCase();
                        return value || '--';
                    }

                    function refreshAvatarFallback() {
                        if (avatarFallback) {
                            avatarFallback.textContent = initials();
                        }
                    }

                    function refreshPhotoPreview(event) {
                        const file = event?.target?.files?.[0] || null;
                        if (!file) {
                            if (photoPreview) {
                                photoPreview.src = '';
                                photoPreview.classList.add('hidden');
                            }
                            avatarFallback?.classList.remove('hidden');
                            refreshAvatarFallback();
                            return;
                        }

                        if (photoPreview) {
                            photoPreview.src = URL.createObjectURL(file);
                            photoPreview.classList.remove('hidden');
                        }
                        avatarFallback?.classList.add('hidden');
                    }

                    function isSequentialDigits(value) {
                        const text = String(value || '');
                        if (text.length < 6 || text.length > 10) {
                            return false;
                        }

                        return '0123456789'.includes(text) || '9876543210'.includes(text);
                    }

                    function validateDocumentValue(value) {
                        const raw = String(value || '').trim();
                        const canonical = raw.toUpperCase().replace(/[-\s]/g, '');

                        if (raw === '') {
                            return 'Ingresa el documento del cliente.';
                        }
                        if (!/^[A-Za-z0-9\- ]+$/.test(raw)) {
                            return 'El documento solo puede usar letras, números, espacios y guion.';
                        }
                        if (canonical.length < 6 || canonical.length > 20) {
                            return 'El documento debe tener entre 6 y 20 caracteres útiles.';
                        }
                        if (!/\d/.test(canonical)) {
                            return 'El documento debe incluir al menos un número.';
                        }
                        if (/^(.)\1+$/.test(canonical) || (/^\d+$/.test(canonical) && isSequentialDigits(canonical))) {
                            return 'El documento ingresado no parece válido.';
                        }
                        return '';
                    }

                    function validatePhoneValue(value) {
                        const raw = String(value || '').trim();
                        const digits = raw.replace(/\D/g, '');

                        if (raw === '') {
                            return 'Ingresa el teléfono del cliente.';
                        }
                        if (!/^[0-9+\-\s()]+$/.test(raw)) {
                            return 'El teléfono solo puede contener números y los símbolos + - ( ).';
                        }
                        if (digits.length < 7 || digits.length > 15) {
                            return 'El teléfono debe tener entre 7 y 15 dígitos.';
                        }
                        if (/^(\d)\1+$/.test(digits) || isSequentialDigits(digits)) {
                            return 'El teléfono ingresado no parece válido.';
                        }
                        return '';
                    }

                    function validateUsernameValue(value) {
                        const username = String(value || '').trim().toLowerCase();
                        if (username === '') {
                            return 'Ingresa el usuario para la app cliente.';
                        }
                        if (username.length < 4 || username.length > 80) {
                            return 'El usuario debe tener entre 4 y 80 caracteres.';
                        }
                        if (!/^[a-z0-9._-]+$/.test(username)) {
                            return 'El usuario solo puede usar letras minúsculas, números, punto, guion y guion bajo.';
                        }
                        return '';
                    }

                    function normalizePlanDurationUnit(unit) {
                        return String(unit || '').toLowerCase() === 'months' ? 'months' : 'days';
                    }

                    function addMonthsNoOverflow(baseDate, monthsToAdd) {
                        const months = Math.max(1, Number(monthsToAdd || 1));
                        const baseDay = baseDate.getDate();
                        const baseMonthIndex = baseDate.getMonth() + months;
                        const targetYear = baseDate.getFullYear() + Math.floor(baseMonthIndex / 12);
                        const targetMonth = ((baseMonthIndex % 12) + 12) % 12;
                        const targetLastDay = new Date(targetYear, targetMonth + 1, 0).getDate();
                        const targetDay = Math.min(baseDay, targetLastDay);

                        return new Date(targetYear, targetMonth, targetDay);
                    }

                    function computeMembershipEndDate(startDate, plan, bonusDays) {
                        const unit = normalizePlanDurationUnit(plan.duration_unit);
                        const safeBonusDays = Math.max(0, Math.round(Number(bonusDays || 0)));
                        let endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());

                        if (unit === 'months') {
                            const months = Math.max(1, Math.round(Number(plan.duration_months || 1)));
                            endDate = addMonthsNoOverflow(startDate, months);
                        } else {
                            const days = Math.max(1, Math.round(Number(plan.duration_days || 1)));
                            endDate.setDate(endDate.getDate() + days - 1);
                        }

                        if (safeBonusDays > 0) {
                            endDate.setDate(endDate.getDate() + safeBonusDays);
                        }

                        return endDate;
                    }

                    function availablePromotions() {
                        const planId = String(clientFields.planId?.value || '');
                        const startDate = String(clientFields.membershipStartsAt?.value || '').trim();

                        return quickClientConfig.promotions.filter(function (promo) {
                            const promoPlanId = promo.plan_id !== null ? String(promo.plan_id) : '';
                            const isPlanMatch = promoPlanId === '' || promoPlanId === planId;
                            const fromOk = !promo.starts_at || promo.starts_at <= startDate;
                            const toOk = !promo.ends_at || promo.ends_at >= startDate;
                            const usesOk = promo.max_uses === null || Number(promo.times_used) < Number(promo.max_uses);
                            return isPlanMatch && fromOk && toOk && usesOk;
                        });
                    }

                    function selectedPromotion() {
                        const promotionId = String(clientFields.promotionId?.value || '');
                        if (promotionId === '') {
                            return null;
                        }

                        return availablePromotions().find(function (promo) {
                            return String(promo.id) === promotionId;
                        }) || null;
                    }

                    function promotionOptionLabel(promo) {
                        const value = Number(promo.value || 0);
                        const labels = {
                            percentage: '-' + value + '%',
                            fixed: '-' + value.toFixed(2),
                            final_price: 'Precio final ' + value.toFixed(2),
                            bonus_days: '+' + Math.max(0, Math.round(value)) + ' días',
                            two_for_one: '2x1',
                            bring_friend: 'Trae a un amigo',
                        };
                        return promo.name + ' (' + (labels[promo.type] || promo.type) + ')';
                    }

                    function computePromotionalPrice(planPrice, promo) {
                        const base = Math.max(0, Number(planPrice || 0));
                        if (!promo) {
                            return { finalPrice: base, bonusDays: 0, summary: '' };
                        }

                        const value = Number(promo.value || 0);
                        let finalPrice = base;
                        let bonusDays = 0;
                        let summary = promo.name;

                        if (promo.type === 'percentage') {
                            const percent = Math.min(Math.max(value, 0), 100);
                            finalPrice = Math.max(0, base - (base * percent / 100));
                            summary += ': -' + percent + '%';
                        } else if (promo.type === 'fixed') {
                            finalPrice = Math.max(0, base - Math.max(0, value));
                            summary += ': -' + value.toFixed(2);
                        } else if (promo.type === 'final_price') {
                            finalPrice = Math.max(0, value);
                            summary += ': precio final ' + finalPrice.toFixed(2);
                        } else if (promo.type === 'bonus_days') {
                            bonusDays = Math.max(0, Math.round(value));
                            summary += ': +' + bonusDays + ' días';
                        } else if (promo.type === 'two_for_one' || promo.type === 'bring_friend') {
                            const percent = value > 0 ? Math.min(Math.max(value, 0), 100) : 50;
                            finalPrice = Math.max(0, base - (base * percent / 100));
                            summary += ': -' + percent + '%';
                        }

                        return {
                            finalPrice: Number(finalPrice.toFixed(2)),
                            bonusDays: bonusDays,
                            summary: summary,
                        };
                    }

                    function refreshPromotionOptions() {
                        if (!clientFields.promotionId) {
                            return;
                        }

                        const currentValue = String(clientFields.promotionId.value || '');
                        const options = availablePromotions();
                        clientFields.promotionId.innerHTML = '<option value="">Sin promoción</option>' + options.map(function (promo) {
                            const selected = currentValue === String(promo.id) ? ' selected' : '';
                            return '<option value="' + String(promo.id) + '"' + selected + '>' + promotionOptionLabel(promo) + '</option>';
                        }).join('');

                        if (currentValue !== '' && !options.some(function (promo) { return String(promo.id) === currentValue; })) {
                            clientFields.promotionId.value = '';
                        }
                    }

                    function refreshMembershipState() {
                        const membershipActive = Boolean(clientFields.startMembership?.checked);
                        membershipPanel?.classList.toggle('hidden', !membershipActive);
                        noPlansAlert?.classList.toggle('hidden', quickClientConfig.plans.length > 0 || !membershipActive);

                        if (!membershipBadge) {
                            return;
                        }

                        if (!membershipActive) {
                            membershipBadge.textContent = 'Sin membresía';
                        } else {
                            membershipBadge.textContent = 'Pendiente';
                        }

                        const plan = quickClientConfig.plans.find(function (item) {
                            return String(item.id) === String(clientFields.planId?.value || '');
                        });
                        const start = String(clientFields.membershipStartsAt?.value || '').trim();
                        const promo = selectedPromotion();
                        const pricing = plan ? computePromotionalPrice(plan.price, promo) : { bonusDays: 0, finalPrice: 0, summary: '' };

                        if (promotionSummary) {
                            promotionSummary.textContent = pricing.summary || '';
                        }

                        if (plan && membershipActive && clientFields.membershipPrice && String(clientFields.membershipPrice.value || '').trim() === '') {
                            clientFields.membershipPrice.value = pricing.finalPrice.toFixed(2);
                        }

                        if (plan && membershipActive && clientFields.amountPaid && String(clientFields.amountPaid.value || '').trim() === '') {
                            clientFields.amountPaid.value = pricing.finalPrice.toFixed(2);
                        }

                        if (!plan || !start || !membershipActive) {
                            if (membershipEnd) {
                                membershipEnd.textContent = 'N/A';
                            }
                            if (paymentBadge) {
                                paymentBadge.className = 'mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide border border-slate-600 bg-slate-800 text-slate-200';
                                paymentBadge.textContent = membershipActive ? 'Pendiente' : 'Sin membresía';
                            }
                            return;
                        }

                        const startDate = new Date(start + 'T00:00:00');
                        if (Number.isNaN(startDate.getTime())) {
                            membershipEnd.textContent = 'N/A';
                            return;
                        }

                        const endDate = computeMembershipEndDate(startDate, plan, pricing.bonusDays);
                        membershipEnd.textContent = endDate.toLocaleDateString('es-EC', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                        });
                        membershipBadge.textContent = 'Activa (automática)';

                        const price = Number(clientFields.membershipPrice?.value || 0);
                        const paid = Number(clientFields.amountPaid?.value || 0);
                        if (paymentBadge) {
                            if (price > 0 && paid >= price) {
                                paymentBadge.className = 'mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide border border-emerald-400/40 bg-emerald-500/20 text-emerald-200';
                                paymentBadge.textContent = 'AL DÍA';
                            } else {
                                paymentBadge.className = 'mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide border border-amber-400/40 bg-amber-500/20 text-amber-200';
                                paymentBadge.textContent = 'PENDIENTE';
                            }
                        }
                    }

                    function refreshAccountState() {
                        if (!accountPanel || !clientFields.createAppAccount) {
                            return;
                        }

                        accountPanel.classList.toggle('hidden', !clientFields.createAppAccount.checked);
                    }

                    async function checkDocument() {
                        if (!quickClientConfig.documentCheckUrl || !clientFields.document) {
                            return;
                        }

                        const value = String(clientFields.document.value || '').trim();
                        if (value === '') {
                            state.documentState = 'idle';
                            setDocStatus('', 'info');
                            return;
                        }

                        const localError = validateDocumentValue(value);
                        if (localError !== '') {
                            state.documentState = 'idle';
                            setDocStatus('', 'info');
                            return;
                        }

                        state.documentState = 'checking';
                        setDocStatus('Validando documento...', 'info');

                        try {
                            const url = new URL(quickClientConfig.documentCheckUrl, window.location.origin);
                            url.searchParams.set('document_number', value);

                            const response = await fetch(url.toString(), {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            });

                            if (!response.ok) {
                                state.documentState = 'idle';
                                setDocStatus('', 'info');
                                return;
                            }

                            const payload = await response.json();
                            if (payload.exists) {
                                state.documentState = 'exists';
                                setDocStatus('Este documento ya existe en este gimnasio.', 'error', payload.show_url || '');
                                return;
                            }

                            state.documentState = 'available';
                            setDocStatus('Documento disponible para crear el cliente.', 'success');
                        } catch (error) {
                            state.documentState = 'idle';
                            setDocStatus('', 'info');
                        }
                    }

                    clientFields.photo?.addEventListener('change', refreshPhotoPreview);
                    clientFields.firstName?.addEventListener('input', refreshAvatarFallback);
                    clientFields.lastName?.addEventListener('input', refreshAvatarFallback);
                    clientFields.createAppAccount?.addEventListener('change', refreshAccountState);
                    clientFields.startMembership?.addEventListener('change', refreshMembershipState);
                    clientFields.planId?.addEventListener('change', function () {
                        refreshPromotionOptions();
                        const plan = quickClientConfig.plans.find(function (item) {
                            return String(item.id) === String(clientFields.planId?.value || '');
                        });
                        if (plan && clientFields.membershipPrice) {
                            const promo = selectedPromotion();
                            const pricing = computePromotionalPrice(plan.price, promo);
                            clientFields.membershipPrice.value = pricing.finalPrice.toFixed(2);
                            if (clientFields.amountPaid && String(clientFields.amountPaid.value || '').trim() === '') {
                                clientFields.amountPaid.value = pricing.finalPrice.toFixed(2);
                            }
                        }
                        refreshMembershipState();
                    });
                    clientFields.membershipStartsAt?.addEventListener('input', function () {
                        refreshPromotionOptions();
                        refreshMembershipState();
                    });
                    clientFields.promotionId?.addEventListener('change', function () {
                        const plan = quickClientConfig.plans.find(function (item) {
                            return String(item.id) === String(clientFields.planId?.value || '');
                        });
                        if (plan && clientFields.membershipPrice) {
                            const pricing = computePromotionalPrice(plan.price, selectedPromotion());
                            clientFields.membershipPrice.value = pricing.finalPrice.toFixed(2);
                        }
                        refreshMembershipState();
                    });
                    clientFields.membershipPrice?.addEventListener('input', refreshMembershipState);
                    clientFields.amountPaid?.addEventListener('input', refreshMembershipState);
                    clientFields.document?.addEventListener('input', function () {
                        clearFieldError('document_number');
                        window.clearTimeout(state.documentTimer);
                        state.documentTimer = window.setTimeout(checkDocument, 260);
                    });

                    ['first_name', 'last_name', 'phone', 'app_username', 'app_password', 'app_password_confirmation'].forEach(function (field) {
                        const input = clientForm?.querySelector('[name="' + field + '"]');
                        input?.addEventListener('input', function () {
                            clearFieldError(field);
                        });
                    });

                    clientForm?.addEventListener('submit', function (event) {
                        if (clientFields.firstName) {
                            clientFields.firstName.value = formatPersonName(clientFields.firstName.value);
                        }
                        if (clientFields.lastName) {
                            clientFields.lastName.value = formatPersonName(clientFields.lastName.value);
                        }
                        if (clientFields.appUsername) {
                            clientFields.appUsername.value = String(clientFields.appUsername.value || '').trim().toLowerCase();
                        }

                        let hasError = false;
                        const firstName = String(clientFields.firstName?.value || '').trim();
                        const lastName = String(clientFields.lastName?.value || '').trim();
                        const documentError = validateDocumentValue(clientFields.document?.value || '');
                        const phoneError = validatePhoneValue(clientFields.phone?.value || '');

                        if (firstName === '') {
                            hasError = true;
                            setFieldError('first_name', 'Ingresa el nombre del cliente.');
                        }
                        if (lastName === '') {
                            hasError = true;
                            setFieldError('last_name', 'Ingresa el apellido del cliente.');
                        }
                        if (documentError !== '') {
                            hasError = true;
                            setFieldError('document_number', documentError);
                        } else if (state.documentState === 'exists') {
                            hasError = true;
                            setFieldError('document_number', 'Este documento ya está registrado en este gimnasio.');
                        }
                        if (phoneError !== '') {
                            hasError = true;
                            setFieldError('phone', phoneError);
                        }

                        if (quickClientConfig.canManageAccounts && clientFields.createAppAccount?.checked) {
                            const usernameError = validateUsernameValue(clientFields.appUsername?.value || '');
                            const password = String(clientFields.appPassword?.value || '');
                            const confirmation = String(clientFields.appPasswordConfirmation?.value || '');
                            if (usernameError !== '') {
                                hasError = true;
                                setFieldError('app_username', usernameError);
                            }
                            if (password.length < 8) {
                                hasError = true;
                                setFieldError('app_password', 'La contraseña debe tener al menos 8 caracteres.');
                            }
                            if (confirmation === '') {
                                hasError = true;
                                setFieldError('app_password_confirmation', 'Confirma la contraseña de la app cliente.');
                            } else if (confirmation !== password) {
                                hasError = true;
                                setFieldError('app_password_confirmation', 'La confirmación de contraseña no coincide.');
                            }
                        }

                        if (clientFields.startMembership?.checked) {
                            if (quickClientConfig.plans.length === 0) {
                                hasError = true;
                            }
                            if (String(clientFields.planId?.value || '').trim() === '') {
                                hasError = true;
                                setFieldError('plan_id', 'Selecciona un plan para iniciar la membresía.');
                            }
                            if (String(clientFields.membershipStartsAt?.value || '').trim() === '') {
                                hasError = true;
                                setFieldError('membership_starts_at', 'La fecha de inicio es obligatoria.');
                            }
                        }

                        if (hasError) {
                            event.preventDefault();
                            const firstError = clientForm.querySelector('[data-client-error]:not(.hidden)');
                            const targetField = firstError ? clientForm.querySelector('[name="' + firstError.getAttribute('data-client-error') + '"]') : null;
                            targetField?.focus();
                        }
                    });

                    refreshPromotionOptions();
                    refreshMembershipState();
                    refreshAccountState();
                    refreshAvatarFallback();
                    if (String(clientFields.document?.value || '').trim() !== '') {
                        checkDocument();
                    }
                }

                const quickSaleConfig = {
                    enabled: Boolean(<?php echo json_encode($renderQuickSaleModal, 15, 512) ?>),
                    products: <?php echo json_encode($quickSaleProductsPayload, 15, 512) ?>,
                    storageKey: ['quick-sale-cart', <?php echo json_encode($activeGymSlug, 15, 512) ?>, String(<?php echo json_encode($activeGymId || 0, 15, 512) ?>)].join(':'),
                };

                if (quickSaleConfig.enabled && saleModal) {
                    const saleForm = document.getElementById('quick-sale-form');
                    const scanInput = document.getElementById('quick-sale-scan-input');
                    const searchButton = document.getElementById('quick-sale-scan-search');
                    const select = document.getElementById('quick-sale-product-select');
                    const addSelectedButton = document.getElementById('quick-sale-add-selected');
                    const quantityInput = document.getElementById('quick-sale-quantity-input');
                    const feedback = document.getElementById('quick-sale-feedback');
                    const preview = document.getElementById('quick-sale-preview');
                    const previewName = document.getElementById('quick-sale-preview-name');
                    const previewCode = document.getElementById('quick-sale-preview-code');
                    const previewPrice = document.getElementById('quick-sale-preview-price');
                    const previewStock = document.getElementById('quick-sale-preview-stock');
                    const scanList = document.getElementById('quick-sale-list');
                    const scanListItems = document.getElementById('quick-sale-list-items');
                    const scanListSummary = document.getElementById('quick-sale-list-summary');
                    const clearListButton = document.getElementById('quick-sale-list-clear');
                    const saleItemsPayloadInput = document.getElementById('quick-sale-items-payload');

                    if (scanInput && select && quantityInput) {
                        const products = quickSaleConfig.products || [];
                        const scanListMap = new Map();
                        const productsById = new Map();
                        let autoSearchTimer = null;

                        products.forEach(function (product) {
                            productsById.set(Number(product.id), product);
                        });

                        function normalize(value) {
                            return String(value || '').trim().toUpperCase().replace(/\s+/g, '');
                        }

                        function toNumber(value) {
                            const parsed = Number(value);
                            return Number.isFinite(parsed) ? parsed : 0;
                        }

                        function escapeHtml(value) {
                            return String(value || '')
                                .replace(/&/g, '&amp;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/"/g, '&quot;')
                                .replace(/'/g, '&#39;');
                        }

                        function setFeedback(text, tone) {
                            if (!feedback) {
                                return;
                            }

                            feedback.className = 'quick-access-sale-feedback is-visible mt-3 rounded-xl border px-3 py-2 text-sm font-semibold';
                            feedback.textContent = text;

                            if (tone === 'success') {
                                feedback.classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-800');
                            } else if (tone === 'error') {
                                feedback.classList.add('border-rose-300', 'bg-rose-50', 'text-rose-800');
                            } else {
                                feedback.classList.add('border-cyan-300', 'bg-cyan-50', 'text-cyan-800');
                            }
                        }

                        function clearFeedback() {
                            if (!feedback) {
                                return;
                            }

                            feedback.textContent = '';
                            feedback.className = 'quick-access-sale-feedback mt-3 rounded-xl border px-3 py-2 text-sm font-semibold';
                        }

                        function serializeScanList() {
                            const payload = [];
                            scanListMap.forEach(function (item) {
                                payload.push({
                                    product_id: Number(item.product_id),
                                    quantity: Number(item.quantity),
                                });
                            });

                            return payload.length > 0 ? JSON.stringify(payload) : '';
                        }

                        function persistScanList() {
                            const serialized = serializeScanList();
                            saleItemsPayloadInput.value = serialized;
                            try {
                                if (serialized === '') {
                                    window.sessionStorage.removeItem(quickSaleConfig.storageKey);
                                } else {
                                    window.sessionStorage.setItem(quickSaleConfig.storageKey, serialized);
                                }
                            } catch (error) {
                            }
                        }

                        function renderPreview(product) {
                            if (!preview || !previewName || !previewCode || !previewPrice || !previewStock) {
                                return;
                            }

                            if (!product) {
                                preview.classList.remove('is-visible');
                                return;
                            }

                            preview.classList.add('is-visible');
                            previewName.textContent = product.name;
                            previewCode.textContent = 'SKU ' + (product.sku || '---') + ' | BAR ' + (product.barcode || '---');
                            previewPrice.textContent = '$' + Number(product.sale_price || 0).toFixed(2);
                            previewStock.textContent = 'Stock disponible: ' + Number(product.stock || 0);
                        }

                        function renderScanList() {
                            if (!scanList || !scanListItems || !scanListSummary) {
                                persistScanList();
                                return;
                            }

                            if (scanListMap.size === 0) {
                                scanList.classList.remove('is-visible');
                                scanListItems.innerHTML = '';
                                scanListSummary.textContent = '';
                                scanListItems.classList.remove('is-scrollable');
                                renderPreview(getSelectedProduct());
                                persistScanList();
                                return;
                            }

                            let totalProducts = 0;
                            let totalUnits = 0;
                            let totalAmount = 0;
                            const chunks = [];

                            scanListMap.forEach(function (item) {
                                totalProducts += 1;
                                totalUnits += Number(item.quantity);
                                totalAmount += Number(item.quantity) * Number(item.sale_price);

                                chunks.push(
                                    '<article class="quick-access-sale-line">' +
                                        '<div class="flex items-start justify-between gap-2">' +
                                            '<div class="min-w-0">' +
                                                '<p class="truncate text-sm font-black text-slate-900 dark:text-slate-100">' + escapeHtml(item.name) + '</p>' +
                                                '<p class="text-[11px] ui-muted">SKU ' + escapeHtml(item.sku || '---') + ' | BAR ' + escapeHtml(item.barcode || '---') + '</p>' +
                                            '</div>' +
                                            '<div class="text-right">' +
                                                '<p class="text-sm font-black text-emerald-700 dark:text-emerald-300">$' + Number(item.sale_price).toFixed(2) + '</p>' +
                                                '<p class="text-[11px] ui-muted">Stock: ' + Number(item.stock) + '</p>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="mt-2 flex items-center gap-2">' +
                                            '<button type="button" class="ui-button ui-button-ghost px-2 py-1 text-xs font-bold" data-sale-action="dec" data-product-id="' + Number(item.product_id) + '">-</button>' +
                                            '<span class="min-w-[36px] text-center text-sm font-black text-slate-900 dark:text-slate-100">' + Number(item.quantity) + '</span>' +
                                            '<button type="button" class="ui-button ui-button-ghost px-2 py-1 text-xs font-bold" data-sale-action="inc" data-product-id="' + Number(item.product_id) + '">+</button>' +
                                            '<button type="button" class="ui-button ui-button-ghost px-2 py-1 text-xs font-semibold text-rose-700 dark:text-rose-300" data-sale-action="remove" data-product-id="' + Number(item.product_id) + '">Quitar</button>' +
                                        '</div>' +
                                    '</article>'
                                );
                            });

                            scanList.classList.add('is-visible');
                            scanListItems.classList.add('is-scrollable');
                            scanListItems.innerHTML = chunks.join('');
                            scanListSummary.textContent = totalProducts + ' producto(s) | ' + totalUnits + ' unidad(es) | Total estimado $' + totalAmount.toFixed(2);
                            if (preview) {
                                preview.classList.remove('is-visible');
                            }
                            persistScanList();
                        }

                        function findProductByCode(rawCode) {
                            const code = normalize(rawCode);
                            if (code === '') {
                                return null;
                            }

                            return products.find(function (product) {
                                return normalize(product.barcode) === code || normalize(product.sku) === code;
                            }) || null;
                        }

                        function getSelectedProduct() {
                            const productId = Number(select.value || 0);
                            return products.find(function (product) {
                                return Number(product.id) === productId;
                            }) || null;
                        }

                        function addProductToScanList(product, quantityToAdd, options) {
                            if (!product) {
                                return false;
                            }

                            const settings = Object.assign({ enforceStock: true }, options || {});
                            const productId = Number(product.id);
                            const key = String(productId);
                            const quantity = Math.max(1, Math.floor(toNumber(quantityToAdd)));
                            const stock = Math.max(0, Math.floor(toNumber(product.stock)));
                            const existing = scanListMap.get(key);
                            const currentQuantity = existing ? Number(existing.quantity) : 0;
                            let nextQuantity = currentQuantity + quantity;

                            if (settings.enforceStock && stock <= 0) {
                                setFeedback('El producto "' + product.name + '" no tiene stock disponible.', 'error');
                                return false;
                            }

                            if (stock > 0 && nextQuantity > stock) {
                                if (settings.enforceStock) {
                                    setFeedback('Stock insuficiente para "' + product.name + '". Disponible: ' + stock + '.', 'error');
                                    return false;
                                }
                                nextQuantity = stock;
                            }

                            scanListMap.set(key, {
                                product_id: productId,
                                quantity: nextQuantity,
                                name: product.name || '',
                                sku: product.sku || '',
                                barcode: product.barcode || '',
                                sale_price: toNumber(product.sale_price),
                                stock: stock,
                            });

                            renderScanList();
                            return true;
                        }

                        function updateScanListItem(productId, mode) {
                            const key = String(Number(productId));
                            const current = scanListMap.get(key);
                            if (!current) {
                                return;
                            }

                            if (mode === 'remove') {
                                scanListMap.delete(key);
                                renderScanList();
                                return;
                            }

                            const stock = Math.max(0, Math.floor(toNumber(current.stock)));
                            const delta = mode === 'inc' ? 1 : -1;
                            const nextQuantity = Number(current.quantity) + delta;

                            if (nextQuantity <= 0) {
                                scanListMap.delete(key);
                                renderScanList();
                                return;
                            }

                            if (mode === 'inc' && stock > 0 && nextQuantity > stock) {
                                setFeedback('No puedes superar el stock de "' + current.name + '".', 'error');
                                return;
                            }

                            current.quantity = nextQuantity;
                            scanListMap.set(key, current);
                            renderScanList();
                        }

                        function clearScanList() {
                            scanListMap.clear();
                            renderScanList();
                        }

                        function restoreScanListFromPayload(serialized) {
                            const raw = String(serialized || '').trim();
                            if (raw === '') {
                                return;
                            }

                            let parsed = null;
                            try {
                                parsed = JSON.parse(raw);
                            } catch (error) {
                                return;
                            }

                            if (!Array.isArray(parsed)) {
                                return;
                            }

                            parsed.forEach(function (row) {
                                const productId = Number(row && row.product_id);
                                const quantity = Math.max(1, Math.floor(toNumber(row && row.quantity)));
                                if (!Number.isFinite(productId) || productId <= 0) {
                                    return;
                                }

                                const product = productsById.get(productId);
                                if (!product) {
                                    return;
                                }

                                addProductToScanList(product, quantity, { enforceStock: false });
                            });
                        }

                        function applyProduct(product) {
                            if (!product) {
                                return;
                            }

                            select.value = String(product.id);
                            quantityInput.value = '1';
                            renderPreview(product);
                        }

                        function resolveScan() {
                            if (autoSearchTimer) {
                                clearTimeout(autoSearchTimer);
                                autoSearchTimer = null;
                            }

                            const product = findProductByCode(scanInput.value);
                            if (!product) {
                                setFeedback('No encontré un producto con ese SKU o código de barras.', 'error');
                                return;
                            }

                            if (!addProductToScanList(product, 1, { enforceStock: true })) {
                                return;
                            }

                            applyProduct(product);
                            quantityInput.value = '1';
                            setFeedback('Producto agregado a la lista: ' + product.name, 'success');
                            scanInput.select();
                            scanInput.focus();
                        }

                        function addSelectedProductToList() {
                            const product = getSelectedProduct();
                            if (!product) {
                                setFeedback('Selecciona un producto para agregar al carrito.', 'error');
                                return;
                            }

                            if (!addProductToScanList(product, 1, { enforceStock: true })) {
                                return;
                            }

                            setFeedback('Producto agregado al carrito: ' + product.name + '.', 'success');
                            scanInput.focus();
                        }

                        searchButton?.addEventListener('click', resolveScan);
                        scanInput.addEventListener('keydown', function (event) {
                            if (event.key === 'Enter') {
                                event.preventDefault();
                                resolveScan();
                            }
                        });
                        scanInput.addEventListener('input', function () {
                            if (autoSearchTimer) {
                                clearTimeout(autoSearchTimer);
                            }

                            if (normalize(scanInput.value).length === 0) {
                                clearFeedback();
                            }

                            autoSearchTimer = window.setTimeout(function () {
                                if (normalize(scanInput.value).length >= 6) {
                                    resolveScan();
                                }
                            }, 240);
                        });
                        select.addEventListener('change', function () {
                            renderPreview(getSelectedProduct());
                            clearFeedback();
                        });
                        addSelectedButton?.addEventListener('click', addSelectedProductToList);
                        clearListButton?.addEventListener('click', function () {
                            clearScanList();
                            clearFeedback();
                            setFeedback('Lista de escaneo limpia.', 'info');
                        });
                        scanListItems?.addEventListener('click', function (event) {
                            const button = event.target.closest('button[data-sale-action]');
                            if (!button) {
                                return;
                            }

                            const action = String(button.getAttribute('data-sale-action') || '').trim();
                            const productId = Number(button.getAttribute('data-product-id') || 0);
                            if (!Number.isFinite(productId) || productId <= 0) {
                                return;
                            }

                            updateScanListItem(productId, action);
                        });

                        saleForm?.addEventListener('submit', function () {
                            const serialized = serializeScanList();
                            saleItemsPayloadInput.value = serialized;

                            if (serialized !== '' && !select.value) {
                                const firstItem = scanListMap.values().next().value;
                                if (firstItem) {
                                    select.value = String(firstItem.product_id);
                                }
                            }
                        });

                        const hiddenPayload = String(saleItemsPayloadInput?.value || '').trim();
                        if (hiddenPayload !== '') {
                            restoreScanListFromPayload(hiddenPayload);
                        } else {
                            try {
                                const stored = window.sessionStorage.getItem(quickSaleConfig.storageKey);
                                if (stored && stored.trim() !== '') {
                                    restoreScanListFromPayload(stored);
                                }
                            } catch (error) {
                            }
                        }

                        renderPreview(getSelectedProduct());
                        renderScanList();
                    }
                }
            })();
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if($renderQuickClientModal): ?>
    <div id="quick-client-modal"
         class="ui-modal-backdrop hidden items-start"
         data-auto-open="<?php echo e($quickClientAutoOpen ? '1' : '0'); ?>">
        <div class="ui-modal-shell quick-access-modal-shell w-full max-w-3xl" role="dialog" aria-modal="true" aria-labelledby="quick-client-title">
            <form method="POST"
                  action="<?php echo e(route('clients.store', $quickActionRouteParams)); ?>"
                  enctype="multipart/form-data"
                  novalidate
                  id="quick-client-form"
                  class="flex h-full min-h-0 flex-1 flex-col space-y-0">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="redirect_to" value="<?php echo e($quickActionReturnUrl); ?>">
                <input type="hidden" name="quick_action_form" value="quick-client">
                <input type="hidden" name="_open_create_modal" value="1">

                <header class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-800/70 px-5 py-4">
                    <div>
                        <span class="quick-access-hint">Mantén presionado el botón para moverlo</span>
                        <h3 id="quick-client-title" class="mt-3 text-xl font-black text-slate-100">Crear cliente</h3>
                        <p class="mt-1 text-sm text-slate-400">Alta rápida desde cualquier pantalla del panel.</p>
                    </div>
                    <button type="button"
                            class="ui-button ui-button-ghost px-2.5 py-2 text-sm"
                            data-quick-client-close
                            aria-label="Cerrar modal de crear cliente">
                        Cerrar
                    </button>
                </header>

                <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                    <?php if($quickClientErrorMessages->isNotEmpty()): ?>
                        <div class="quick-access-summary-list is-visible rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                            <p class="font-semibold">Corrige estos puntos antes de guardar:</p>
                            <ul>
                                <?php $__currentLoopData = $quickClientErrorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($message); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Nombre</span>
                            <input type="text"
                                   name="first_name"
                                   value="<?php echo e(old('first_name', '')); ?>"
                                   class="ui-input"
                                   autocomplete="given-name"
                                   data-client-field="first_name">
                            <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('first_name') ? '' : 'hidden'); ?>"
                               data-client-error="first_name"><?php echo e($errors->first('first_name')); ?></p>
                        </label>

                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Apellido</span>
                            <input type="text"
                                   name="last_name"
                                   value="<?php echo e(old('last_name', '')); ?>"
                                   class="ui-input"
                                   autocomplete="family-name"
                                   data-client-field="last_name">
                            <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('last_name') ? '' : 'hidden'); ?>"
                               data-client-error="last_name"><?php echo e($errors->first('last_name')); ?></p>
                        </label>

                        <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                            <span>Documento</span>
                            <input type="text"
                                   name="document_number"
                                   value="<?php echo e(old('document_number', '')); ?>"
                                   class="ui-input"
                                   autocomplete="off"
                                   data-client-field="document_number">
                            <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('document_number') ? '' : 'hidden'); ?>"
                               data-client-error="document_number"><?php echo e($errors->first('document_number')); ?></p>
                            <div class="quick-access-doc-state rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs text-slate-300"
                                 data-client-doc-status></div>
                        </label>

                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Teléfono</span>
                            <input type="text"
                                   name="phone"
                                   value="<?php echo e(old('phone', '')); ?>"
                                   class="ui-input"
                                   autocomplete="tel"
                                   data-client-field="phone">
                            <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('phone') ? '' : 'hidden'); ?>"
                               data-client-error="phone"><?php echo e($errors->first('phone')); ?></p>
                        </label>

                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Género</span>
                            <select name="gender" class="ui-input" data-client-field="gender">
                                <option value="male" <?php if(old('gender', 'neutral') === 'male'): echo 'selected'; endif; ?>>Hombre</option>
                                <option value="female" <?php if(old('gender', 'neutral') === 'female'): echo 'selected'; endif; ?>>Mujer</option>
                                <option value="neutral" <?php if(old('gender', 'neutral') === 'neutral'): echo 'selected'; endif; ?>>Neutral</option>
                            </select>
                            <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('gender') ? '' : 'hidden'); ?>"
                               data-client-error="gender"><?php echo e($errors->first('gender')); ?></p>
                        </label>

                        <div class="space-y-2 md:col-span-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Foto del cliente</span>
                                <input type="file"
                                       name="photo"
                                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                       class="ui-input"
                                       data-client-photo-input>
                            </label>
                            <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('photo') ? '' : 'hidden'); ?>"
                               data-client-error="photo"><?php echo e($errors->first('photo')); ?></p>
                            <div class="quick-access-panel flex items-center gap-3 p-3">
                                <img src=""
                                     alt="Vista previa cliente"
                                     class="hidden h-16 w-16 rounded-full border border-slate-600 object-cover"
                                     data-client-photo-preview>
                                <div class="flex h-16 w-16 items-center justify-center rounded-full border border-slate-600 bg-slate-800 text-lg font-black uppercase text-slate-200"
                                     data-client-avatar-fallback>
                                    --
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-200">Vista previa</p>
                                    <p class="text-xs text-slate-400">Si no subes imagen se mostrarán iniciales.</p>
                                </div>
                            </div>
                        </div>

                        <?php if($quickClientCanManageAccounts): ?>
                            <div class="space-y-3 rounded-xl border border-cyan-500/30 bg-cyan-500/5 p-3 md:col-span-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-100">
                                        <input type="checkbox"
                                               name="create_app_account"
                                               value="1"
                                               <?php if(old('create_app_account')): echo 'checked'; endif; ?>
                                               class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-cyan-400/40"
                                               data-client-account-toggle>
                                        Crear usuario y contraseña para app cliente
                                    </label>
                                    <span class="inline-flex rounded-full border border-cyan-400/30 bg-cyan-500/15 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-cyan-100">
                                        Premium / Sucursales
                                    </span>
                                </div>

                                <div class="grid gap-3 md:grid-cols-2 <?php echo e(old('create_app_account') ? '' : 'hidden'); ?>"
                                     data-client-account-panel>
                                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                                        <span>Usuario app</span>
                                        <input type="text"
                                               name="app_username"
                                               value="<?php echo e(old('app_username', '')); ?>"
                                               autocomplete="off"
                                               class="ui-input"
                                               data-client-field="app_username">
                                        <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('app_username') ? '' : 'hidden'); ?>"
                                           data-client-error="app_username"><?php echo e($errors->first('app_username')); ?></p>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                                        <span>Contraseña app</span>
                                        <input type="password"
                                               name="app_password"
                                               autocomplete="new-password"
                                               class="ui-input"
                                               data-client-field="app_password">
                                        <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('app_password') ? '' : 'hidden'); ?>"
                                           data-client-error="app_password"><?php echo e($errors->first('app_password')); ?></p>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                        <span>Confirmar contraseña app</span>
                                        <input type="password"
                                               name="app_password_confirmation"
                                               autocomplete="new-password"
                                               class="ui-input"
                                               data-client-field="app_password_confirmation">
                                        <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('app_password_confirmation') ? '' : 'hidden'); ?>"
                                           data-client-error="app_password_confirmation"><?php echo e($errors->first('app_password_confirmation')); ?></p>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="quick-access-panel p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-200">
                                <input type="checkbox"
                                       name="start_membership"
                                       value="1"
                                       <?php if(old('start_membership')): echo 'checked'; endif; ?>
                                       class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-cyan-400/40"
                                       data-client-membership-toggle>
                                Iniciar membresía ahora
                            </label>

                            <span class="inline-flex rounded-full border border-slate-600 bg-slate-800 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-200"
                                  data-client-membership-badge>
                                Sin membresía
                            </span>
                        </div>

                        <div class="quick-access-membership-grid mt-4 <?php echo e(old('start_membership') ? '' : 'hidden'); ?>"
                             data-client-membership-panel>
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Plan</span>
                                    <select name="plan_id" class="ui-input" data-client-field="plan_id">
                                        <option value="">Selecciona un plan</option>
                                        <?php $__currentLoopData = $quickClientPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($plan->id); ?>" <?php if((string) old('plan_id', '') === (string) $plan->id): echo 'selected'; endif; ?>>
                                                <?php echo e($plan->name); ?> (<?php echo e(\App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months)); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('plan_id') ? '' : 'hidden'); ?>"
                                       data-client-error="plan_id"><?php echo e($errors->first('plan_id')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Fecha inicio</span>
                                    <input type="date"
                                           name="membership_starts_at"
                                           value="<?php echo e(old('membership_starts_at', now()->toDateString())); ?>"
                                           class="ui-input"
                                           data-client-field="membership_starts_at">
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('membership_starts_at') ? '' : 'hidden'); ?>"
                                       data-client-error="membership_starts_at"><?php echo e($errors->first('membership_starts_at')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Precio</span>
                                    <input type="number"
                                           name="membership_price"
                                           min="0"
                                           step="0.01"
                                           value="<?php echo e(old('membership_price', '')); ?>"
                                           class="ui-input"
                                           data-client-field="membership_price">
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('membership_price') ? '' : 'hidden'); ?>"
                                       data-client-error="membership_price"><?php echo e($errors->first('membership_price')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Promoción</span>
                                    <select name="promotion_id"
                                            class="ui-input"
                                            data-client-field="promotion_id"
                                            <?php if(! $quickClientCanManagePromotions): echo 'disabled'; endif; ?>>
                                        <option value="">Sin promoción</option>
                                    </select>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('promotion_id') ? '' : 'hidden'); ?>"
                                       data-client-error="promotion_id"><?php echo e($errors->first('promotion_id')); ?></p>
                                    <?php if (! ($quickClientCanManagePromotions)): ?>
                                        <p class="text-xs text-amber-200">Tu plan actual no incluye promociones.</p>
                                    <?php endif; ?>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Método de pago</span>
                                    <select name="payment_method" class="ui-input" data-client-field="payment_method">
                                        <option value="cash" <?php if(old('payment_method', 'cash') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                                        <option value="transfer" <?php if(old('payment_method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                                        <option value="card" <?php if(old('payment_method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                                    </select>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('payment_method') ? '' : 'hidden'); ?>"
                                       data-client-error="payment_method"><?php echo e($errors->first('payment_method')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Monto pagado</span>
                                    <input type="number"
                                           name="amount_paid"
                                           min="0"
                                           step="0.01"
                                           value="<?php echo e(old('amount_paid', '')); ?>"
                                           class="ui-input"
                                           data-client-field="amount_paid">
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('amount_paid') ? '' : 'hidden'); ?>"
                                       data-client-error="amount_paid"><?php echo e($errors->first('amount_paid')); ?></p>
                                </label>
                            </div>

                            <div class="rounded-xl border border-slate-700 bg-slate-900/70 p-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha fin estimada</p>
                                <p class="mt-2 text-sm font-bold text-slate-100" data-client-membership-end>N/A</p>
                                <p class="mt-2 text-xs text-slate-400" data-client-promotion-summary></p>
                                <span class="mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                                      data-client-payment-badge>
                                    Pendiente
                                </span>
                            </div>

                            <p class="rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200 <?php echo e($quickClientPlans->isEmpty() ? '' : 'hidden'); ?>"
                               data-client-no-plans>
                                No hay planes activos. Crea un plan antes de iniciar membresías desde este modal.
                            </p>

                            <div class="rounded-xl border-2 border-rose-400/80 bg-rose-500/20 p-3 text-rose-100 shadow-lg <?php echo e($errors->has('cash') ? '' : 'hidden'); ?>"
                                 data-client-cash-alert>
                                <p class="text-xs font-black uppercase tracking-wide">Debe abrir caja para cobrar</p>
                                <p class="mt-1 text-sm font-semibold"><?php echo e($errors->first('cash')); ?></p>
                                <div class="mt-2">
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $quickActionRouteParams),'variant' => 'secondary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $quickActionRouteParams)),'variant' => 'secondary','size' => 'sm']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
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

                <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','dataQuickClientClose' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','data-quick-client-close' => true]); ?>Cancelar <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'success']); ?>Guardar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </footer>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if($renderQuickSaleModal): ?>
    <div id="quick-sale-modal"
         class="ui-modal-backdrop hidden items-start"
         data-auto-open="<?php echo e($quickSaleAutoOpen ? '1' : '0'); ?>">
        <div class="ui-modal-shell quick-access-modal-shell quick-access-sale-panel w-full max-w-5xl" role="dialog" aria-modal="true" aria-labelledby="quick-sale-title">
            <div class="flex h-full min-h-0 flex-1 flex-col">
                <header class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-800/70 px-5 py-4">
                    <div>
                        <span class="quick-access-hint">Venta rápida desde cualquier módulo</span>
                        <h3 id="quick-sale-title" class="mt-3 text-2xl font-black text-slate-100">Registrar venta de producto</h3>
                        <p class="mt-1 text-sm text-slate-400">Abre el modal, agrega productos y registra el cobro sin salir de la pantalla actual.</p>
                    </div>
                    <button type="button"
                            class="ui-button ui-button-ghost px-2.5 py-2 text-sm"
                            data-quick-sale-close
                            aria-label="Cerrar modal de venta rápida">
                        Cerrar
                    </button>
                </header>

                <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                    <?php if($quickSaleErrorMessages->isNotEmpty()): ?>
                        <div class="quick-access-summary-list is-visible rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                            <p class="font-semibold">Revisa estos puntos antes de registrar la venta:</p>
                            <ul>
                                <?php $__currentLoopData = $quickSaleErrorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($message); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="quick-access-sale-shell p-4">
                        <?php if(! $quickSaleSchemaReady): ?>
                            <p class="ui-alert ui-alert-warning">Falta ejecutar <code>php artisan migrate</code> para activar el módulo de ventas e inventario.</p>
                        <?php elseif(! $quickSaleHasOpenCashSession): ?>
                            <p class="ui-alert ui-alert-warning">Caja cerrada. Debes abrir caja antes de registrar ventas de productos.</p>
                            <div class="mt-3">
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $quickActionRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $quickActionRouteParams)),'variant' => 'secondary']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
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
                        <?php elseif($quickSaleProducts->isEmpty()): ?>
                            <p class="ui-alert ui-alert-warning">No hay productos activos para vender. Crea al menos un producto y carga stock primero.</p>
                            <div class="mt-3">
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('products.index', $quickActionRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index', $quickActionRouteParams)),'variant' => 'secondary']); ?>Ir a productos <?php echo $__env->renderComponent(); ?>
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
                        <?php else: ?>
                            <form method="POST"
                                  action="<?php echo e(route('sales.store', $quickActionRouteParams)); ?>"
                                  class="grid gap-3 md:grid-cols-2 md:gap-4"
                                  id="quick-sale-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="redirect_to" value="<?php echo e($quickActionReturnUrl); ?>">
                                <input type="hidden" name="quick_action_form" value="quick-sale">
                                <input type="hidden" name="open_sales_register_modal" value="1">
                                <input type="hidden" name="sale_items_payload" id="quick-sale-items-payload" value="<?php echo e(old('sale_items_payload', '')); ?>">
                                <input type="hidden" name="quantity" id="quick-sale-quantity-input" value="<?php echo e(old('quantity', 1)); ?>">

                                <div class="quick-access-sale-scan md:col-span-2">
                                    <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                                        <label class="space-y-1 text-sm font-semibold ui-muted">
                                            <span>Escanear código</span>
                                            <input id="quick-sale-scan-input"
                                                   type="text"
                                                   class="ui-input"
                                                   placeholder="Escribe SKU o código de barras"
                                                   autocomplete="off">
                                        </label>
                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','id' => 'quick-sale-scan-search','variant' => 'secondary','class' => 'w-full lg:w-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','id' => 'quick-sale-scan-search','variant' => 'secondary','class' => 'w-full lg:w-auto']); ?>Buscar código <?php echo $__env->renderComponent(); ?>
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
                                    <p class="mt-2 text-xs ui-muted">Puedes escribir el código, buscarlo y seguir sumando productos al carrito rápido.</p>
                                    <div id="quick-sale-feedback" class="quick-access-sale-feedback mt-3 rounded-xl border px-3 py-2 text-sm font-semibold"></div>
                                    <div id="quick-sale-preview" class="quick-access-sale-preview mt-3 rounded-2xl border border-slate-200 bg-white/80 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <div>
                                                <p id="quick-sale-preview-name" class="text-base font-black text-slate-900 dark:text-slate-100"></p>
                                                <p id="quick-sale-preview-code" class="text-xs ui-muted"></p>
                                            </div>
                                            <div class="text-right">
                                                <p id="quick-sale-preview-price" class="text-sm font-bold text-emerald-700 dark:text-emerald-300"></p>
                                                <p id="quick-sale-preview-stock" class="text-xs ui-muted"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="quick-sale-list" class="quick-access-sale-list mt-3 rounded-2xl border border-cyan-200 bg-white/80 p-3 dark:border-cyan-400/40 dark:bg-slate-900/70">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="text-xs font-black uppercase tracking-wider ui-muted">Carrito rápido</p>
                                            <button type="button" id="quick-sale-list-clear" class="ui-button ui-button-ghost px-3 py-1 text-xs font-semibold">
                                                Limpiar lista
                                            </button>
                                        </div>
                                        <div id="quick-sale-list-items" class="quick-access-sale-list-items mt-2"></div>
                                        <p id="quick-sale-list-summary" class="mt-2 text-xs ui-muted"></p>
                                    </div>
                                </div>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Producto</span>
                                    <select name="product_id" id="quick-sale-product-select" class="ui-input">
                                        <option value="">Selecciona un producto</option>
                                        <?php $__currentLoopData = $quickSaleProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($product->id); ?>" <?php if($quickSaleSelectedProductId === (int) $product->id): echo 'selected'; endif; ?>>
                                                <?php echo e($product->name); ?> | SKU <?php echo e($product->sku ?: '---'); ?> | BAR <?php echo e($product->barcode ?: '---'); ?> | stock <?php echo e((int) $product->stock); ?> | <?php echo e($quickActionCurrencyFormatter::format((float) $product->sale_price, $quickActionCurrencyCode)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button type="button" id="quick-sale-add-selected" class="ui-button ui-button-ghost w-full px-3 py-2 text-sm font-semibold sm:w-auto">
                                            Agregar seleccionado al carrito
                                        </button>
                                    </div>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('product_id') ? '' : 'hidden'); ?>"><?php echo e($errors->first('product_id')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Método</span>
                                    <select name="payment_method" class="ui-input" required>
                                        <option value="cash" <?php if(old('payment_method', 'cash') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                                        <option value="card" <?php if(old('payment_method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                                        <option value="transfer" <?php if(old('payment_method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                                    </select>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('payment_method') ? '' : 'hidden'); ?>"><?php echo e($errors->first('payment_method')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Cliente opcional</span>
                                    <select name="client_id" class="ui-input">
                                        <option value="">Venta sin cliente vinculado</option>
                                        <?php $__currentLoopData = $quickSaleClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($client->id); ?>" <?php if($quickSaleSelectedClientId === (int) $client->id): echo 'selected'; endif; ?>>
                                                <?php echo e($client->full_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('client_id') ? '' : 'hidden'); ?>"><?php echo e($errors->first('client_id')); ?></p>
                                </label>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Notas</span>
                                    <textarea name="notes" rows="3" class="ui-input" placeholder="Ej: bebida, guantes, proteína, promo del día"><?php echo e(old('notes')); ?></textarea>
                                    <p class="text-xs font-semibold text-rose-300 <?php echo e($errors->has('notes') ? '' : 'hidden'); ?>"><?php echo e($errors->first('notes')); ?></p>
                                </label>

                                <div class="quick-access-sale-actions md:col-span-2">
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>Registrar venta <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('products.index', $quickActionRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index', $quickActionRouteParams)),'variant' => 'ghost']); ?>Abrir productos <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','dataQuickSaleClose' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','data-quick-sale-close' => true]); ?>Cancelar <?php echo $__env->renderComponent(); ?>
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
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/quick-actions.blade.php ENDPATH**/ ?>