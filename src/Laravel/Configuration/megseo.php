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
        // MegSEO\Checks\MetaDescription\MetaDescriptionCheck::class,
        // MegSEO\Checks\Canonical\CanonicalCheck::class,
        // MegSEO\Checks\OpenGraph\OpenGraphCheck::class,
        // MegSEO\Checks\TwitterCard\TwitterCardCheck::class,
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

    /*
    |--------------------------------------------------------------------------
    | Meta Description Check Thresholds
    |--------------------------------------------------------------------------
    |
    | Recommended meta description length ranges and short/long boundary
    | thresholds used by the Meta Description Check feature.
    |
    */
    'meta_description' => [
        'min_length' => (int) env('MEGSEO_META_DESCRIPTION_MIN_LENGTH', 120),
        'max_length' => (int) env('MEGSEO_META_DESCRIPTION_MAX_LENGTH', 160),
        'short_threshold' => (int) env('MEGSEO_META_DESCRIPTION_SHORT_THRESHOLD', 80),
        'long_threshold' => (int) env('MEGSEO_META_DESCRIPTION_LONG_THRESHOLD', 170),
    ],

    /*
    |--------------------------------------------------------------------------
    | Canonical Check Configuration
    |--------------------------------------------------------------------------
    */
    'canonical' => [
        'strict_mode' => (bool) env('MEGSEO_CANONICAL_STRICT', true),
        'warn_relative' => (bool) env('MEGSEO_CANONICAL_WARN_RELATIVE', true),
        'warn_cross_domain' => (bool) env('MEGSEO_CANONICAL_WARN_CROSS_DOMAIN', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph Check Configuration
    |--------------------------------------------------------------------------
    */
    'open_graph' => [
        'strict_mode' => (bool) env('MEGSEO_OG_STRICT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Card Check Configuration
    |--------------------------------------------------------------------------
    */
    'twitter_card' => [
        'strict_mode' => (bool) env('MEGSEO_TWITTER_STRICT', true),
    ],
];
