<?php

/**
 * MegSEO Core MVP — Manual Dogfooding Example
 * =============================================
 *
 * Validates: Engine::make(), check registration, pipeline execution,
 *            result aggregation, score computation, and public accessors.
 *
 * This file is temporary — it validates the Core MVP through real-world
 * usage and does not belong to the package's production API.
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use MegSEO\Core\Engine;
use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

// ┌─────────────────────────────────────────────────────────────┐
// │ 1. FakeCheck — realistic in-memory implementation           │
// └─────────────────────────────────────────────────────────────┘

final readonly class FakeContentQualityCheck implements Check
{
    public function ref(): CheckReference
    {
        return new CheckReference(
            id: 'fake.content_quality',
            label: 'Content Quality Check',
            version: '1.0.0',
        );
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        // Simulate inspecting the subject data (e.g. page body, meta info)
        $data = is_string($context->subject) ? $context->subject : '';

        return new CheckOutcome(
            check: $this->ref(),

            // Score: 85 out of a realistic range (no SEO business rule — just a demo value)
            scoreContribution: new ScoreSummary(value: 85.0),

            // Issue: content too short (demo severity)
            issues: [
                new AnalysisIssue(
                    message: 'Content length is below the recommended threshold.',
                    details: 'The page body contains only 120 words. Aim for at least 300 words of substantive content.',
                    sourceCheckId: $this->ref()->id,
                    confidence: 0.92,
                ),
            ],

            // Warning: readability concern
            warnings: [
                new AnalysisWarning(
                    message: 'Several sentences exceed the recommended readability complexity.',
                    details: '3 out of 8 sentences have a Flesch-Kincaid grade level above 12.',
                    sourceCheckId: $this->ref()->id,
                ),
            ],

            // Suggestion: actionable guidance
            suggestions: [
                new AnalysisSuggestion(
                    message: 'Add supporting examples and break long sentences into shorter ones.',
                    details: 'Introduce 2–3 concrete examples and split any sentence exceeding 25 words.',
                    sourceCheckId: $this->ref()->id,
                    confidence: 0.85,
                ),
            ],

            // Per-check metadata (debug/tracing hooks for future dashboards)
            metadata: [
                'runtime_ms' => 1.2,
                'word_count' => 120,
                'sentence_count' => 8,
            ],
        );
    }
}

// ┌─────────────────────────────────────────────────────────────┐
// │ 2. Instantiate engine — zero configuration                  │
// └─────────────────────────────────────────────────────────────┘

$engine = Engine::make();

echo "✔ Engine instantiated via Engine::make()\n";
echo '  Registered checks: ' . $engine->count() . "\n\n";

// ┌─────────────────────────────────────────────────────────────┐
// │ 3. Register the FakeCheck                                   │
// └─────────────────────────────────────────────────────────────┘

$check = new FakeContentQualityCheck();
$engine->register($check);

echo "✔ Registered check: {$check->ref()->id} (v{$check->ref()->version})\n";
echo '  Registered checks: ' . $engine->count() . "\n\n";

// Verify the check appears in all()
$all = $engine->all();
assert(count($all) === 1, 'Expected 1 registered check.');
assert($all[0]->ref()->id === 'fake.content_quality', 'Expected the FakeContentQualityCheck.');

// ┌─────────────────────────────────────────────────────────────┐
// │ 4. Create a minimal AnalysisContext                         │
// └─────────────────────────────────────────────────────────────┘

$context = new AnalysisContext(
    subject: <<<'HTML'
<html><head><title>About Us</title></head>
<body><p>Welcome to our company. We provide excellent services. Our team is great. 
We have many customers. Contact us today.</p></body></html>
HTML,
    attributes: ['url' => '/about', 'language' => 'en'],
    options: [],
    requestId: 'dogfood-001',
);

echo "✔ AnalysisContext created (subject: HTML page, url: /about)\n";
echo "  requestId: {$context->requestId}\n\n";

// ┌─────────────────────────────────────────────────────────────┐
// │ 5. Execute analysis                                         │
// └─────────────────────────────────────────────────────────────┘

$result = $engine->analyze($context);

echo "✔ Analysis executed — result received\n\n";

// ┌─────────────────────────────────────────────────────────────┐
// │ 6. Dump results — human-readable validation                 │
// └─────────────────────────────────────────────────────────────┘

echo str_repeat('═', 64) . "\n";
echo "  ANALYSIS RESULT\n";
echo str_repeat('═', 64) . "\n\n";

// ── Score ─────────────────────────────────────────────────────
$score = $result->score();

echo "▶ SCORE\n";
echo "  Value:        " . ($score->value !== null ? number_format($score->value, 1) : 'N/A') . "\n";
echo "  Contributors: " . count($score->contributors) . "\n";
foreach ($score->contributors as $contributor) {
    echo "    • {$contributor['sourceCheckId']} → {$contributor['value']}\n";
}
echo "\n";

// ── Issues ────────────────────────────────────────────────────
$issues = $result->issues();
echo "▶ ISSUES (" . count($issues) . ")\n";
foreach ($issues as $i => $issue) {
    echo "  [" . ($i + 1) . "] {$issue->message}\n";
    echo "      Source:  {$issue->sourceCheckId}\n";
    echo "      Details: {$issue->details}\n";
    if ($issue->confidence !== null) {
        echo "      Confidence: " . ($issue->confidence * 100) . "%\n";
    }
}
echo "\n";

// ── Warnings ──────────────────────────────────────────────────
$warnings = $result->warnings();
echo "▶ WARNINGS (" . count($warnings) . ")\n";
foreach ($warnings as $i => $warning) {
    echo "  [" . ($i + 1) . "] {$warning->message}\n";
    echo "      Source:  {$warning->sourceCheckId}\n";
    echo "      Details: {$warning->details}\n";
}
echo "\n";

// ── Suggestions ───────────────────────────────────────────────
$suggestions = $result->suggestions();
echo "▶ SUGGESTIONS (" . count($suggestions) . ")\n";
foreach ($suggestions as $i => $suggestion) {
    echo "  [" . ($i + 1) . "] {$suggestion->message}\n";
    echo "      Source:  {$suggestion->sourceCheckId}\n";
    echo "      Details: {$suggestion->details}\n";
    if ($suggestion->confidence !== null) {
        echo "      Confidence: " . ($suggestion->confidence * 100) . "%\n";
    }
}
echo "\n";

// ── Failures ──────────────────────────────────────────────────
echo "▶ FAILURES (" . count($result->failures) . ")\n";
if (count($result->failures) === 0) {
    echo "  (none)\n";
} else {
    foreach ($result->failures as $i => $failure) {
        echo "  [" . ($i + 1) . "] {$failure['check']->id}: {$failure['error']}\n";
    }
}
echo "\n";

// ── Metadata ──────────────────────────────────────────────────
echo "▶ METADATA\n";
echo "  result-level metadata: " . (count($result->metadata) > 0 ? json_encode($result->metadata) : '(none)') . "\n";
echo "\n";

// ┌─────────────────────────────────────────────────────────────┐
// │ 7. Assertions — automated validation                        │
// └─────────────────────────────────────────────────────────────┘

assert($result->score()->value === 85.0,         'Score should be 85.0');
assert(count($result->issues()) === 1,           'Should have 1 issue');
assert(count($result->warnings()) === 1,         'Should have 1 warning');
assert(count($result->suggestions()) === 1,      'Should have 1 suggestion');
assert(count($result->failures) === 0,           'Should have 0 failures');

assert($result->issues()[0]->message !== '',     'Issue must have a message');
assert($result->warnings()[0]->message !== '',   'Warning must have a message');
assert($result->suggestions()[0]->message !== '', 'Suggestion must have a message');

assert(
    $result->issues()[0]->sourceCheckId === 'fake.content_quality',
    'Issue sourceCheckId must match the registered check',
);

echo str_repeat('═', 64) . "\n";
echo "  ALL ASSERTIONS PASSED\n";
echo str_repeat('═', 64) . "\n";
echo "\n✔ Core MVP dogfooding complete — Engine, pipeline, aggregation, and public API all validated.\n";
