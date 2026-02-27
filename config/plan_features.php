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

    'matrix' => [
        'basico' => [
            'promotions' => false,
            'reports_base' => false,
            'reports_export' => false,
            'multi_branch' => false,
            'pwa_install' => false,
        ],
        'profesional' => [
            'promotions' => true,
            'reports_base' => true,
            'reports_export' => false,
            'multi_branch' => false,
            'pwa_install' => true,
        ],
        'premium' => [
            'promotions' => true,
            'reports_base' => true,
            'reports_export' => true,
            'multi_branch' => false,
            'pwa_install' => true,
        ],
        'sucursales' => [
            'promotions' => true,
            'reports_base' => true,
            'reports_export' => true,
            'multi_branch' => true,
            'pwa_install' => true,
        ],
    ],
];
