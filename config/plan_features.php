<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plan Features Matrix
    |--------------------------------------------------------------------------
    |
    | This matrix defines enabled capabilities for each fixed subscription
    | plan key. Fase 1 introduces the source of truth; route enforcement
    | is added in later phases.
    |
    */
    'default_plan_key' => 'basico',
    'default_feature_version' => 'v1',
    'access_cache_seconds' => env('PLAN_FEATURE_ACCESS_CACHE_SECONDS', 60),

    'matrix' => [
        'basico' => [
            'cashiers' => false,
            'promotions' => false,
            'reports_base' => true,
            'reports_export' => false,
            'multi_branch' => false,
            'pwa_install' => false,
            'client_accounts' => false,
            'sales_inventory' => false,
            'sales_inventory_reports' => false,
        ],
        'profesional' => [
            'cashiers' => true,
            'promotions' => true,
            'reports_base' => true,
            'reports_export' => true,
            'multi_branch' => false,
            'pwa_install' => true,
            'client_accounts' => false,
            'sales_inventory' => true,
            'sales_inventory_reports' => true,
        ],
        'premium' => [
            'cashiers' => true,
            'promotions' => true,
            'reports_base' => true,
            'reports_export' => true,
            'multi_branch' => false,
            'pwa_install' => true,
            'client_accounts' => true,
            'sales_inventory' => true,
            'sales_inventory_reports' => true,
        ],
        'sucursales' => [
            'cashiers' => true,
            'promotions' => true,
            'reports_base' => true,
            'reports_export' => true,
            'multi_branch' => true,
            'pwa_install' => true,
            'client_accounts' => true,
            'sales_inventory' => true,
            'sales_inventory_reports' => true,
        ],
    ],
];
