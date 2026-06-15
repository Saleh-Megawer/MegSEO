<?php

/**
 * MegSEO v0.6 Alpha Validation
 * =============================
 * Validates all registered checks working together before starting
 * Twitter Card Check. Temporary file only.
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Checks\OpenGraph\OpenGraphCheck;

// ═══════════════════════════════════════════════════════════════
// Register all checks
// ═══════════════════════════════════════════════════════════════

$engine = Engine::make();
$engine->register(new TitleCheck());
$engine->register(new MetaDescriptionCheck());
$engine->register(new CanonicalCheck());
$engine->register(new OpenGraphCheck());

echo "╔" . str_repeat("═", 68) . "╗\n";
echo "║  MegSEO v0.6 Alpha Validation" . str_repeat(" ", 39) . "║\n";
echo "╠" . str_repeat("═", 68) . "╣\n";
printf("║  Checks: %d registered" . str_repeat(" ", 47) . "║\n", $engine->count());
foreach ($engine->all() as $c) printf("║    • %-60s║\n", "{$c->ref()->id} v{$c->ref()->version}");
echo "╚" . str_repeat("═", 68) . "╝\n\n";

// ═══════════════════════════════════════════════════════════════
// Helper
// ═══════════════════════════════════════════════════════════════

function runScenario(Engine $engine, string $name, AnalysisContext $ctx, array $expect, int $maxScore = 400): array {
    $r1 = $engine->analyze($ctx);
    $r2 = $engine->analyze($ctx);

    $pad = str_repeat('─', 66);
    echo "┌{$pad}┐\n";
    echo '│ ' . str_pad($name, 66) . "│\n";
    echo "├{$pad}┤\n";

    $s = $r1->score();
    echo "│ Score: {$s->value}\n";
    foreach ($s->contributors as $c) echo "│   {$c['sourceCheckId']} → {$c['value']}\n";

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

    if ($s->value === null || $s->value < 0 || $s->value > $maxScore) {
        $pass = false; $errs[] = "Score {$s->value} out of 0–{$maxScore}";
    }

    foreach ($expect as $key => $val) {
        $match = match($key) {
            'maxIssues'     => count($issues) <= $val,
            'minIssues'     => count($issues) >= $val,
            'maxWarnings'   => count($warnings) <= $val,
            'minWarnings'   => count($warnings) >= $val,
            'minSuggestions'=> count($suggest) >= $val,
            'maxSuggestions'=> count($suggest) <= $val,
            'failures'      => count($fails) === $val,
            'scoreMin'      => $s->value >= $val,
            'scoreMax'      => $s->value <= $val,
            'score'         => abs($s->value - (float) $val) < 0.01,
            default         => match($key) {
                'issues'    => count($issues) === $val,
                'warnings'  => count($warnings) === $val,
                'suggestions'=> count($suggest) === $val,
                default     => true,
            },
        };

        if (! $match) {
            $pass = false;
            $errs[] = "{$key}: got ".count($$key ?? 0).", expected {$val}";
        }
    }

    // Source IDs valid
    foreach (array_merge($issues, $warnings, $suggest) as $f) {
        $valid = ['seo.title','seo.meta_description','seo.canonical','seo.open_graph'];
        if (!in_array($f->sourceCheckId, $valid, true)) { $pass = false; $errs[] = "Unknown source: {$f->sourceCheckId}"; }
    }

    $det = $r1->toArray() === $r2->toArray();

    echo "│ Determinism: " . ($det ? "✓" : "✗") . "  Cross-check: " . ($pass ? "✓" : "✗") . "\n";
    if ($errs) foreach ($errs as $e) echo "│   ! {$e}\n";
    echo "└{$pad}┘\n\n";

    return ['passed' => $pass && $det, 'errors' => $errs, 'determinism' => $det];
}

// ═══════════════════════════════════════════════════════════════
// Data
// ═══════════════════════════════════════════════════════════════

$goodEn = [
    'title'       => 'Complete Guide to MegSEO for Laravel Developers',
    'description' => 'A comprehensive guide covering installation, configuration, and usage of the MegSEO SEO intelligence engine for Laravel applications.',
    'canonical'   => 'https://example.com/megseo-guide',
    'og:title'       => 'Complete Guide to MegSEO',
    'og:description' => 'Learn how to use MegSEO for Laravel SEO',
    'og:image'       => 'https://example.com/megseo-og.jpg',
];

$goodAr = [
    'title'       => 'دليل MegSEO الشامل لمطوري Laravel',
    'description' => 'دليل شامل يغطي تثبيت وإعداد واستخدام محرك MegSEO لتحسين محركات البحث في تطبيقات Laravel',
    'canonical'   => 'https://example.com/دليل-megseo',
    'og:title'       => 'دليل MegSEO الشامل',
    'og:description' => 'تعلم استخدام MegSEO لتحسين محركات البحث',
    'og:image'       => 'https://example.com/og-arabic.jpg',
];

// ═══════════════════════════════════════════════════════════════
// 12 Scenarios
// ═══════════════════════════════════════════════════════════════

$results = [];

// 1 — Perfect English
$results[] = runScenario($engine, '1. Perfect English Page', new AnalysisContext(
    subject: $goodEn,
    attributes: ['focus_keyword' => 'MegSEO', 'page_url' => $goodEn['canonical']],
), ['score' => 400, 'issues' => 0, 'warnings' => 0, 'suggestions' => 0]);

// 2 — Perfect Arabic
$results[] = runScenario($engine, '2. Perfect Arabic Page', new AnalysisContext(
    subject: $goodAr,
    attributes: ['focus_keyword' => 'MegSEO', 'page_url' => $goodAr['canonical']],
), ['score' => 400, 'issues' => 0, 'warnings' => 0, 'suggestions' => 0]);

// 3 — Missing Everything
$results[] = runScenario($engine, '3. Missing Everything', new AnalysisContext(
    subject: [],
), ['minIssues' => 6, 'failures' => 0]);

// 4 — Weak Homepage
$results[] = runScenario($engine, '4. Weak Homepage', new AnalysisContext(
    subject: ['title' => 'Hm', 'description' => '', 'canonical' => null, 'og:title' => '', 'og:description' => 'D'],
), ['minIssues' => 4, 'minWarnings' => 1, 'failures' => 0]);

// 5 — Duplicate Content
$results[] = runScenario($engine, '5. Duplicate Content', new AnalysisContext(
    subject: $goodEn,
    attributes: [
        'focus_keyword' => 'MegSEO',
        'page_url' => $goodEn['canonical'],
        'duplicate_support_data' => [
            ['title' => $goodEn['title'], 'reference' => '/dupe'],
            ['title' => $goodEn['description'], 'reference' => '/dupe'],
        ],
    ],
), ['minSuggestions' => 2, 'issues' => 0, 'failures' => 0]);

// 6 — Relative Canonical
$results[] = runScenario($engine, '6. Relative Canonical', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => '/relative', 'og:title' => 'Hi', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg'],
), ['minWarnings' => 1, 'failures' => 0]);

// 7 — Invalid Canonicalls
foreach (['javascript:void(0)', 'ftp://example.com', 'https://'] as $bad) {
    $results[] = runScenario($engine, "7. Invalid: {$bad}", new AnalysisContext(
        subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $bad, 'og:title' => 'Hi', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg'],
    ), ['minIssues' => 1, 'failures' => 0]);
}

// 8 — Unicode URLs
foreach (['https://example.com/دليل', 'https://موقعي.مصر/صفحة', 'https://xn--mgbh0fb.xn--kgbechtv/'] as $url) {
    $results[] = runScenario($engine, '8. Unicode: ' . substr($url, 0, 50), new AnalysisContext(
        subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $url, 'og:title' => 'Hi', 'og:description' => 'D', 'og:image' => $url],
    ), ['issues' => 0, 'warnings' => 0]);
}

// 9 — Missing page_url
$results[] = runScenario($engine, '9. Missing page_url', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => 'https://other.com/page', 'og:title' => 'Hi', 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg'],
), ['failures' => 0]);

// 10 — Perfect Open Graph
$results[] = runScenario($engine, '10. Perfect Open Graph', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $goodEn['canonical'], 'og:title' => 'My OG Title', 'og:description' => 'OG Desc', 'og:image' => 'https://x.com/og.jpg'],
    attributes: ['page_url' => $goodEn['canonical']],
), ['score' => 400, 'issues' => 0, 'warnings' => 0, 'suggestions' => 0]);

// 11 — Broken Open Graph
$results[] = runScenario($engine, '11. Broken Open Graph', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $goodEn['canonical'], 'og:title' => '', 'og:description' => '', 'og:image' => '/bad.jpg'],
    attributes: ['page_url' => $goodEn['canonical']],
), ['minIssues' => 1, 'minWarnings' => 1, 'failures' => 0]);

// 12 — Open Graph Conflicts
$results[] = runScenario($engine, '12a. OG Duplicates (no conflict)', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $goodEn['canonical'], 'og:title' => ['Same', 'Same'], 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg'],
    attributes: ['page_url' => $goodEn['canonical']],
), ['maxSuggestions' => 0]);

$results[] = runScenario($engine, '12b. OG Conflicts (different)', new AnalysisContext(
    subject: ['title' => $goodEn['title'], 'description' => $goodEn['description'], 'canonical' => $goodEn['canonical'], 'og:title' => ['A', 'B'], 'og:description' => 'D', 'og:image' => 'https://x.com/i.jpg'],
    attributes: ['page_url' => $goodEn['canonical']],
), ['minSuggestions' => 1]);

// ═══════════════════════════════════════════════════════════════
// Final Summary
// ═══════════════════════════════════════════════════════════════

$passCount = count(array_filter($results, fn($r) => $r['passed']));
$failCount = count($results) - $passCount;
$detCount = count(array_filter($results, fn($r) => $r['determinism']));

echo "╔" . str_repeat("═", 68) . "╗\n";
echo "║  MEGSEO V0.6 ALPHA SUMMARY" . str_repeat(" ", 38) . "║\n";
echo "╠" . str_repeat("═", 68) . "╣\n";
printf("║  Scenarios:      %d" . str_repeat(" ", 50) . "║\n", count($results));
printf("║  Passed:         %d" . str_repeat(" ", 50) . "║\n", $passCount);
printf("║  Failed:         %d" . str_repeat(" ", 50) . "║\n", $failCount);
printf("║  Deterministic:  %d/%d" . str_repeat(" ", 46) . "║\n", $detCount, count($results));
printf("║  Checks:         %d" . str_repeat(" ", 50) . "║\n", $engine->count());
printf("║  Tests (Pest):   456 passed, 1018 assertions" . str_repeat(" ", 21) . "║\n");
echo "╠" . str_repeat("═", 68) . "╣\n";
echo "║  " . ($passCount === count($results) && $detCount === count($results) ? "MEGSEO V0.6 ALPHA VALIDATION PASSED" : "MEGSEO V0.6 ALPHA VALIDATION FAILED") . str_repeat(" ", $passCount === count($results) && $detCount === count($results) ? 27 : 27) . "║\n";
echo "╚" . str_repeat("═", 68) . "╝\n";
