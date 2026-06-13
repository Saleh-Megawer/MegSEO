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
        // MegSEO\Checks\Title\TitleCheck::class,
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

    /*
    |--------------------------------------------------------------------------
    | Title Check Thresholds
    |--------------------------------------------------------------------------
    |
    | Recommended title length ranges and short/long boundary thresholds
    | used by the Title Check feature. Values are resolved at the Laravel
    | integration boundary and passed to the framework-agnostic
    | TitleLengthPolicy via its constructor.
    |
    */
    'title' => [
        'min_length' => (int) env('MEGSEO_TITLE_MIN_LENGTH', 30),
        'max_length' => (int) env('MEGSEO_TITLE_MAX_LENGTH', 60),
        'short_threshold' => (int) env('MEGSEO_TITLE_SHORT_THRESHOLD', 20),
        'long_threshold' => (int) env('MEGSEO_TITLE_LONG_THRESHOLD', 70),
    ],
];
