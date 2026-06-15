<?php

/**
 * MegSEO Alpha Validation Script
 * ===============================
 *
 * Final Alpha validation of all completed MegSEO checks before
 * starting new features. Does NOT modify production code, tests,
 * or public APIs. Temporary file only.
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Checks\Canonical\CanonicalCheck;

// ═══════════════════════════════════════════════════════════
// Register all checks
// ═══════════════════════════════════════════════════════════

$engine = Engine::make();
$engine->register(new TitleCheck());
$engine->register(new MetaDescriptionCheck());
$engine->register(new CanonicalCheck());

echo "╔" . str_repeat("═", 66) . "╗\n";
echo "║  MegSEO Alpha Validation" . str_repeat(" ", 41) . "║\n";
echo "╠" . str_repeat("═", 66) . "╣\n";
echo "║  Checks: {$engine->count()} registered" . str_repeat(" ", 45) . "║\n";
foreach ($engine->all() as $c) {
    printf("║    • %-58s║\n", "{$c->ref()->id} v{$c->ref()->version}");
}
echo "╚" . str_repeat("═", 66) . "╝\n\n";

// ═══════════════════════════════════════════════════════════
// Helper
// ═══════════════════════════════════════════════════════════

function runScenario(Engine $engine, string $name, AnalysisContext $ctx, array $expect, int $maxScore = 300): array {
    $r1 = $engine->analyze($ctx);
    $r2 = $engine->analyze($ctx);

    $pad = str_repeat('─', 64);
    echo "┌{$pad}┐\n";
    echo '│ ' . str_pad($name, 64) . "│\n";
    echo "├{$pad}┤\n";

    $s = $r1->score();
    echo "│ Score: {$s->value}\n";
    foreach ($s->contributors as $c) {
        echo "│   {$c['sourceCheckId']} → {$c['value']}\n";
    }

    $issues    = $r1->issues();
    $warnings  = $r1->warnings();
    $suggest   = $r1->suggestions();
    $fails     = $r1->failures;

    echo "│ Issues: " . count($issues) . "  Warnings: " . count($warnings) . "  Suggestions: " . count($suggest) . "  Failures: " . count($fails) . "\n";

    foreach ($issues as $i) echo "│   ✗ [{$i->sourceCheckId}] {$i->message}\n";
    foreach ($warnings as $w) echo "│   ⚠ [{$w->sourceCheckId}] {$w->message}\n";
    foreach ($suggest as $sg) echo "│   → [{$sg->sourceCheckId}] {$sg->message}\n";

    // Cross-validations
    $pass = true;
    $errs = [];

    // Scoring bounds
    if ($s->value === null || $s->value < 0 || $s->value > $maxScore) {
        $pass = false; $errs[] = "Score {$s->value} out of 0–{$maxScore}";
    }

    // Expectations
    foreach ($expect as $key => $val) {
        $actual = match($key) {
            'issues', 'warnings', 'suggestions', 'failures' => count(match($key) {
                'issues' => $issues, 'warnings' => $warnings, 'suggestions' => $suggest, 'failures' => $fails,
            }),
            'score' => $s->value,
            default => null,
        };

        $match = match($key) {
            'maxIssues'     => count($issues) <= $val,
            'minIssues'     => count($issues) >= $val,
            'maxWarnings'   => count($warnings) <= $val,
            'minSuggestions'=> count($suggest) >= $val,
            'score'         => abs($s->value - (float) $val) < 0.01,
            default         => $actual === $val,
        };

        if (! $match) {
            $pass = false;
            $label = match($key) {
                'maxIssues' => "Too many issues: ".count($issues)." (max {$val})",
                'minIssues' => "Too few issues: ".count($issues)." (min {$val})",
                default     => "{$key}: expected {$val}, got {$actual}",
            };
            $errs[] = $label;
        }
    }

    // Source IDs valid
    foreach (array_merge($issues, $warnings, $suggest) as $f) {
        if (! in_array($f->sourceCheckId, ['seo.title', 'seo.meta_description', 'seo.canonical'], true)) {
            $pass = false; $errs[] = "Unknown sourceCheckId: {$f->sourceCheckId}";
        }
    }

    // Determinism
    $det = $r1->toArray() === $r2->toArray();

    echo "│ Determinism: " . ($det ? "✓" : "✗") . "  Cross-check: " . ($pass ? "✓" : "✗") . "\n";
    if ($errs) foreach ($errs as $e) echo "│   ! {$e}\n";
    echo "└{$pad}┘\n\n";

    return ['passed' => $pass && $det, 'errors' => $errs, 'determinism' => $det];
}

// ═══════════════════════════════════════════════════════════
// Scenario data
// ═══════════════════════════════════════════════════════════

$goodEn = [
    'title'       => 'Complete Guide to MegSEO for Laravel Developers',
    'description' => 'A comprehensive guide covering installation, configuration, and usage of the MegSEO SEO intelligence engine for Laravel applications.',
    'canonical'   => 'https://example.com/megseo-guide',
];

$goodAr = [
    'title'       => 'دليل MegSEO الشامل لمطوري Laravel',
    'description' => 'دليل شامل يغطي تثبيت وإعداد واستخدام محرك MegSEO لتحسين محركات البحث في تطبيقات Laravel',
    'canonical'   => 'https://example.com/دليل-megseo',
];

// ═══════════════════════════════════════════════════════════
// 10 Scenarios
// ═══════════════════════════════════════════════════════════

$results = [];

// 1 — Perfect English Page
$results[] = runScenario($engine, '1. Perfect English Page', new AnalysisContext(
    subject: $goodEn,
    attributes: ['focus_keyword' => 'MegSEO', 'page_url' => $goodEn['canonical']],
), ['issues' => 0, 'warnings' => 0, 'suggestions' => 0, 'score' => 300]);

// 2 — Perfect Arabic Page
$results[] = runScenario($engine, '2. Perfect Arabic Page', new AnalysisContext(
    subject: $goodAr,
    attributes: ['focus_keyword' => 'MegSEO', 'page_url' => $goodAr['canonical']],
), ['issues' => 0, 'warnings' => 0, 'suggestions' => 0, 'score' => 300]);

// 3 — Missing Everything
$results[] = runScenario($engine, '3. Missing Everything', new AnalysisContext(
    subject: ['title' => null, 'description' => null, 'canonical' => null],
), ['minIssues' => 3, 'minSuggestions' => 0]);

// 4 — Weak Homepage
$results[] = runScenario($engine, '4. Weak Homepage', new AnalysisContext(
    subject: ['title' => 'Hm', 'description' => '', 'canonical' => null],
), ['minIssues' => 2, 'minSuggestions' => 0]);

// 5 — Duplicate Content
$results[] = runScenario($engine, '5. Duplicate Content', new AnalysisContext(
    subject: $goodEn,
    attributes: [
        'focus_keyword'         => 'MegSEO',
        'page_url'              => $goodEn['canonical'],
        'duplicate_support_data' => [
            ['title' => $goodEn['title'], 'reference' => '/dupe'],
            ['title' => $goodEn['description'], 'reference' => '/dupe'],
        ],
    ],
), ['issues' => 0, 'warnings' => 0, 'minSuggestions' => 2]);

// 6 — Relative Canonical
$results[] = runScenario($engine, '6. Relative Canonical', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => '/relative-path'],
), ['issues' => 0, 'warnings' => 1, 'minSuggestions' => 0]);

// 7 — Invalid Canonicalls
foreach (['javascript:void(0)', 'ftp://example.com', 'https://'] as $bad) {
    $results[] = runScenario($engine, "7. Invalid: {$bad}", new AnalysisContext(
        subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $bad],
    ), ['minIssues' => 1, 'failures' => 0]);
}

// 8 — Unicode URLs
foreach (['https://example.com/دليل', 'https://موقعي.مصر/صفحة', 'https://xn--mgbh0fb.xn--kgbechtv/', 'https://example.com/hello-مرحبا-world'] as $url) {
    $results[] = runScenario($engine, "8. Unicode: " . substr($url, 0, 50), new AnalysisContext(
        subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $url],
    ), ['issues' => 0, 'warnings' => 0, 'minSuggestions' => 0]);
}

// 9 — Missing page_url
$results[] = runScenario($engine, '9. Missing page_url', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => 'https://other.com/page'],
), ['failures' => 0]);

// 10 — Multi-check determinism (verified in runScenario for each)
// (Already verified per-scenario via double execution)

// ═══════════════════════════════════════════════════════════
// Final Summary
// ═══════════════════════════════════════════════════════════

$passCount = count(array_filter($results, fn($r) => $r['passed']));
$failCount = count($results) - $passCount;
$totalDet = count(array_filter($results, fn($r) => $r['determinism']));

echo "╔" . str_repeat("═", 66) . "╗\n";
echo "║  FINAL ALPHA SUMMARY" . str_repeat(" ", 46) . "║\n";
echo "╠" . str_repeat("═", 66) . "╣\n";
printf("║  Scenarios:     %d" . str_repeat(" ", 50) . "║\n", count($results));
printf("║  Passed:        %d" . str_repeat(" ", 50) . "║\n", $passCount);
printf("║  Failed:        %d" . str_repeat(" ", 50) . "║\n", $failCount);
printf("║  Deterministic: %d/%d" . str_repeat(" ", 46) . "║\n", $totalDet, count($results));
printf("║  Checks:        %d" . str_repeat(" ", 50) . "║\n", $engine->count());
echo "╠" . str_repeat("═", 66) . "╣\n";

if ($passCount === count($results) && $totalDet === count($results)) {
    echo "║  MEGSEO ALPHA VALIDATION PASSED" . str_repeat(" ", 32) . "║\n";
} else {
    echo "║  MEGSEO ALPHA VALIDATION FAILED" . str_repeat(" ", 32) . "║\n";
}
echo "╚" . str_repeat("═", 66) . "╝\n";
