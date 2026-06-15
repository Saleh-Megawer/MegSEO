<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Checks\Canonical\CanonicalCheck;

$engine = Engine::make();
$engine->register(new TitleCheck());
$engine->register(new MetaDescriptionCheck());
$engine->register(new CanonicalCheck());

echo "╔" . str_repeat("═", 64) . "╗\n";
echo "║  MegSEO Full Stack Acceptance Test" . str_repeat(" ", 28) . "║\n";
echo "╚" . str_repeat("═", 64) . "╝\n\n";
echo "✔ {$engine->count()} checks: seo.title, seo.meta_description, seo.canonical\n\n";

// Each scenario uses ONE context with array subject containing all fields
// Each check extracts its relevant field from the array

$goodTitle        = 'Complete Guide to MegSEO for Laravel Developers';
$goodDescription  = 'A comprehensive guide covering installation, configuration, and usage of the MegSEO SEO intelligence engine for Laravel applications.';
$goodCanonical    = 'https://example.com/megseo-guide';
$pageUrl          = 'https://example.com/megseo-guide';

$goodArabicTitle  = 'دليل MegSEO الشامل لمطوري Laravel';
$goodArabicDesc   = 'دليل شامل يغطي تثبيت وإعداد واستخدام محرك MegSEO لتحسين محركات البحث في تطبيقات Laravel';

$scenarios = [
    'A — Healthy Page' => [
        'ctx' => new AnalysisContext(subject: [
            'title'       => $goodTitle,
            'description' => $goodDescription,
            'canonical'   => $goodCanonical,
        ], attributes: [
            'focus_keyword' => 'MegSEO',
            'page_url'      => $pageUrl,
        ]),
        'expect' => ['maxIssues' => 0, 'minScore' => 250, 'maxScore' => 300],
    ],
    'B — Poor Homepage' => [
        'ctx' => new AnalysisContext(subject: [
            'title'       => 'Home',
            'description' => '',
            'canonical'   => null,
        ]),
        'expect' => ['minIssues' => 2, 'maxScore' => 220],
    ],
    'C — Duplicate Content' => [
        'ctx' => new AnalysisContext(subject: [
            'title'       => $goodTitle,
            'description' => $goodDescription,
            'canonical'   => 'https://other.com/copy',
        ], attributes: [
            'focus_keyword'         => 'MegSEO',
            'page_url'              => $pageUrl,
            'duplicate_support_data' => [
                ['title' => $goodTitle, 'reference' => '/dupe'],
                ['title' => $goodDescription, 'reference' => '/dupe'],
            ],
        ]),
        'expect' => ['minSuggestions' => 3, 'maxIssues' => 0, 'failures' => 0],
    ],
    'D — Relative Canonical' => [
        'ctx' => new AnalysisContext(subject: [
            'title'       => $goodTitle,
            'description' => $goodDescription,
            'canonical'   => '/relative-path',
        ]),
        'expect' => ['canonicalWarning' => true, 'noInvalidIssue' => true],
    ],
    'E — Arabic Website' => [
        'ctx' => new AnalysisContext(subject: [
            'title'       => $goodArabicTitle,
            'description' => $goodArabicDesc,
            'canonical'   => 'https://example.com/دليل',
        ], attributes: [
            'focus_keyword' => 'MegSEO',
            'page_url'      => 'https://example.com/دليل',
        ]),
        'expect' => ['maxIssues' => 0, 'minScore' => 270],
    ],
    'F — Missing Page URL' => [
        'ctx' => new AnalysisContext(subject: [
            'title'       => $goodTitle,
            'description' => $goodDescription,
            'canonical'   => 'https://other.com/page',
        ]),
        'expect' => ['failures' => 0, 'canonicalSelfRefSuggestion' => false],
    ],
];

$allPassed = true;
$scenarioResults = [];
$maxScore = 300; // 3 checks × 100 max each

foreach ($scenarios as $name => $sc) {
    $ctx = $sc['ctx'];
    $exp = $sc['expect'];

    $padding = str_repeat('─', 62);
    echo "┌{$padding}┐\n";
    echo '│ ' . str_pad($name, 62) . "│\n";
    echo "└{$padding}┘\n\n";

    $result = $engine->analyze($ctx);

    $score = $result->score();
    echo "▶ SCORE: {$score->value}";
    if (isset($score->metadata['total_deductions'])) {
        $sm = $score->metadata;
        // Score aggregator metadata is flat; per-check rationale is in CheckOutcome
    }
    foreach ($score->contributors as $c) {
        echo "\n    • {$c['sourceCheckId']} → {$c['value']}";
    }
    echo "\n\n";

    $issues = $result->issues();
    echo "▶ ISSUES (" . count($issues) . ")\n";
    if (count($issues) === 0) { echo "  ✓ (none)\n"; }
    foreach ($issues as $i => $iss) {
        echo "  [" . ($i + 1) . "] [{$iss->sourceCheckId}] {$iss->message}\n";
    }
    echo "\n";

    $warnings = $result->warnings();
    echo "▶ WARNINGS (" . count($warnings) . ")\n";
    if (count($warnings) === 0) { echo "  ✓ (none)\n"; }
    foreach ($warnings as $i => $w) {
        echo "  [" . ($i + 1) . "] [{$w->sourceCheckId}] {$w->message}\n";
    }
    echo "\n";

    $suggestions = $result->suggestions();
    echo "▶ SUGGESTIONS (" . count($suggestions) . ")\n";
    if (count($suggestions) === 0) { echo "  ✓ (none)\n"; }
    foreach ($suggestions as $i => $s) {
        echo "  [" . ($i + 1) . "] [{$s->sourceCheckId}] {$s->message}\n";
    }
    echo "\n";

    echo "▶ FAILURES (" . count($result->failures) . ")\n";
    if (count($result->failures) === 0) { echo "  ✓ (none)\n"; }
    echo "\n";

    // Determinism
    $r2 = $engine->analyze($ctx);
    $det = $result->toArray() === $r2->toArray();
    echo "▶ DETERMINISM: " . ($det ? "✓ PASS" : "✗ FAIL") . "\n\n";

    // Cross-checks
    $cross = true;
    $cd = [];

    if ($score->value < 0 || $score->value > $maxScore) { $cross = false; $cd[] = "Score {$score->value} out of range (0–{$maxScore})"; }
    if (isset($exp['failures']) && count($result->failures) !== $exp['failures']) { $cross = false; $cd[] = "Failures mismatch"; }
    if (isset($exp['maxIssues']) && count($issues) > $exp['maxIssues']) { $cross = false; $cd[] = "Too many issues: ".count($issues); }
    if (isset($exp['minIssues']) && count($issues) < $exp['minIssues']) { $cross = false; $cd[] = "Too few issues: ".count($issues); }
    if (isset($exp['minScore']) && $score->value < $exp['minScore']) { $cross = false; $cd[] = "Score below min {$exp['minScore']}"; }
    if (isset($exp['maxScore']) && $score->value > $exp['maxScore']) { $cross = false; $cd[] = "Score above max {$exp['maxScore']}"; }
    if (isset($exp['minSuggestions']) && count($suggestions) < $exp['minSuggestions']) { $cross = false; $cd[] = "Too few suggestions"; }
    if (isset($exp['canonicalWarning'])) {
        $found = false;
        foreach ($warnings as $w) { if ($w->sourceCheckId === 'seo.canonical') { $found = true; break; } }
        if (! $found) { $cross = false; $cd[] = "Canonical warning missing"; }
    }
    if (isset($exp['noInvalidIssue'])) {
        foreach ($issues as $iss) {
            if ($iss->sourceCheckId === 'seo.canonical' && stripos($iss->message, 'invalid') !== false) { $cross = false; $cd[] = "Unexpected invalid canonical issue"; break; }
        }
    }
    if (isset($exp['canonicalSelfRefSuggestion']) && $exp['canonicalSelfRefSuggestion'] === false) {
        foreach ($suggestions as $s) {
            if ($s->sourceCheckId === 'seo.canonical' && stripos($s->message, 'self-reference') !== false) { $cross = false; $cd[] = "Unexpected self-reference suggestion"; break; }
        }
    }

    echo "▶ CROSS-CHECK: " . ($cross ? "✓ PASS" : "✗ FAIL");
    if ($cd) echo "\n  " . implode("\n  ", $cd);
    echo "\n\n";

    $passed = $det && $cross;
    $scenarioResults[$name] = $passed;
    if (! $passed) $allPassed = false;
}

// Final summary
$passCount = count(array_filter($scenarioResults));
$failCount = count($scenarioResults) - $passCount;

echo "╔" . str_repeat("═", 64) . "╗\n";
echo "║  FINAL SUMMARY" . str_repeat(" ", 48) . "║\n";
echo "╠" . str_repeat("═", 64) . "╣\n";
printf("║  Scenarios:  %d" . str_repeat(" ", 49) . "║\n", count($scenarioResults));
printf("║  Passed:     %d" . str_repeat(" ", 49) . "║\n", $passCount);
printf("║  Failed:     %d" . str_repeat(" ", 49) . "║\n", $failCount);
printf("║  Checks:     %d" . str_repeat(" ", 49) . "║\n", $engine->count());
echo "╠" . str_repeat("═", 64) . "╣\n";
echo "║  " . ($allPassed ? "MEGSEO ACCEPTANCE TEST PASSED" : "MEGSEO ACCEPTANCE TEST FAILED") . str_repeat(" ", $allPassed ? 25 : 25) . "║\n";
echo "╚" . str_repeat("═", 64) . "╝\n";
