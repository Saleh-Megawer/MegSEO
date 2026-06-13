<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Execution Policy
    |--------------------------------------------------------------------------
    |
    | Controls how the engine handles check failures during analysis.
    | Supported values:
    |   - 'fail_fast': Aborts analysis on first policy-relevant check failure.
    |   - 'isolate_failures': Isolates failures while preserving the result.
    |
    */
    'execution_policy' => env('MEGSEO_EXECUTION_POLICY', 'isolate_failures'),

    /*
    |--------------------------------------------------------------------------
    | Registered Checks
    |--------------------------------------------------------------------------
    |
    | List of check classes to register with the engine. Each class must
    | implement the MegSEO\Contracts\Check contract.
    |
    */
    'checks' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Check Ordering
    |--------------------------------------------------------------------------
    |
    | Optional ordering hints for registered checks. If provided, checks are
    | executed in this order. Unlisted checks run after listed checks in
    | registration order.
    |
    */
    'check_order' => [],
];
