<?php

declare(strict_types=1);

use MegSEO\Laravel\Facades\MegSEO;
use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;
use MegSEO\Core\Engine;
use MegSEO\Exceptions\DuplicateCheckIdentifierException;

beforeEach(function () {
    // Reset engine singleton for test isolation
    $this->app->forgetInstance('megseo.engine');
    $this->app->forgetInstance(Engine::class);

    $this->engine = $this->app->make(Engine::class);
});

// ┌─────────────────────────────────────────────────────────────┐
// │ FakeCheck — returns score, issue, warning, and suggestion   │
// └─────────────────────────────────────────────────────────────┘

function createPageAnalysisCheck(): Check
{
    return new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference(
                id: 'e2e.page_analysis',
                label: 'Page Analysis Check',
                version: '1.0.0',
            );
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),

                scoreContribution: new ScoreSummary(value: 72.0),

                issues: [
                    new AnalysisIssue(
                        message: 'Meta description is missing.',
                        details: 'The page does not include a meta description tag. Add a concise, compelling description under 160 characters.',
                        sourceCheckId: $this->ref()->id,
                        confidence: 1.0,
                    ),
                ],

                warnings: [
                    new AnalysisWarning(
                        message: 'Heading structure is suboptimal.',
                        details: 'The page uses multiple H1 tags (found 2). Use exactly one H1 per page for optimal SEO.',
                        sourceCheckId: $this->ref()->id,
                    ),
                ],

                suggestions: [
                    new AnalysisSuggestion(
                        message: 'Add internal links to related content.',
                        details: 'Consider linking to your services page and case studies to improve topical relevance and crawl depth.',
                        sourceCheckId: $this->ref()->id,
                        confidence: 0.78,
                    ),
                ],

                metadata: [
                    'runtime_ms' => 2.1,
                    'checks_performed' => 5,
                ],
            );
        }
    };
}

function createFailingCheck(): Check
{
    return new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('e2e.failing', 'Failing Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            throw new \RuntimeException('Simulated failure in e2e test');
        }
    };
}

// ┌─────────────────────────────────────────────────────────────┐
// │ 1. Engine resolution through container                      │
// └─────────────────────────────────────────────────────────────┘

test('engine is resolved as a singleton from the container', function () {
    $a = $this->app->make(Engine::class);
    $b = $this->app->make('megseo.engine');

    expect($a)->toBeInstanceOf(Engine::class);
    expect($a)->toBe($b);
    expect($a)->toBe($this->engine);
});

test('engine starts with zero registered checks', function () {
    expect(MegSEO::count())->toBe(0);
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 2. Check registration via facade                            │
// └─────────────────────────────────────────────────────────────┘

test('facade registers a check and count reflects it', function () {
    MegSEO::register(createPageAnalysisCheck());

    expect(MegSEO::count())->toBe(1);
    expect(MegSEO::has('e2e.page_analysis'))->toBeTrue();
});

test('facade registerCheck alias works identically to register', function () {
    MegSEO::registerCheck(createPageAnalysisCheck());

    expect(MegSEO::count())->toBe(1);
});

test('duplicate identifier throws exception through facade', function () {
    MegSEO::register(createPageAnalysisCheck());

    MegSEO::register(createPageAnalysisCheck());
})->throws(DuplicateCheckIdentifierException::class, 'e2e.page_analysis');

// ┌─────────────────────────────────────────────────────────────┐
// │ 3. Analysis context creation                                │
// └─────────────────────────────────────────────────────────────┘

test('AnalysisContext carries all metadata fields', function () {
    $context = new AnalysisContext(
        subject: '<html><head><title>Products</title></head><body><h1>Our Products</h1><p>Content.</p></body></html>',
        attributes: ['url' => '/products', 'language' => 'en', 'content_type' => 'page'],
        options: ['detailed' => true],
        requestId: 'e2e-ctx-001',
    );

    expect($context->subject)->toContain('<title>Products</title>');
    expect($context->attributes->get('url'))->toBe('/products');
    expect($context->attributes->get('language'))->toBe('en');
    expect($context->options)->toBe(['detailed' => true]);
    expect($context->requestId)->toBe('e2e-ctx-001');
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 4. Execute analysis through facade                          │
// └─────────────────────────────────────────────────────────────┘

test('full analysis workflow completes without exceptions', function () {
    MegSEO::register(createPageAnalysisCheck());

    $context = new AnalysisContext(
        subject: '<html><head><title>Test Page</title></head><body><h1>Test</h1></body></html>',
        attributes: ['url' => '/test'],
        requestId: 'e2e-run-001',
    );

    $result = MegSEO::analyze($context);

    expect($result)->toBeInstanceOf(AnalysisResult::class);
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 5. Score, issues, warnings, suggestions                     │
// └─────────────────────────────────────────────────────────────┘

test('result score aggregation returns correct value', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    $score = $result->score();

    expect($score)->toBeInstanceOf(ScoreSummary::class);
    expect($score->value)->toBe(72.0);
    expect($score->contributors)->toHaveCount(1);
    expect($score->contributors[0]['sourceCheckId'])->toBe('e2e.page_analysis');
    expect($score->contributors[0]['value'])->toBe(72.0);
});

test('result issues collection is correct', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    $issues = $result->issues();

    expect($issues)->toHaveCount(1);
    expect($issues[0])->toBeInstanceOf(AnalysisIssue::class);
    expect($issues[0]->message)->toBe('Meta description is missing.');
    expect($issues[0]->details)->toContain('meta description tag');
    expect($issues[0]->sourceCheckId)->toBe('e2e.page_analysis');
    expect($issues[0]->confidence)->toBe(1.0);
});

test('result warnings collection is correct', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    $warnings = $result->warnings();

    expect($warnings)->toHaveCount(1);
    expect($warnings[0])->toBeInstanceOf(AnalysisWarning::class);
    expect($warnings[0]->message)->toBe('Heading structure is suboptimal.');
    expect($warnings[0]->details)->toContain('multiple H1 tags');
    expect($warnings[0]->sourceCheckId)->toBe('e2e.page_analysis');
});

test('result suggestions collection is correct', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    $suggestions = $result->suggestions();

    expect($suggestions)->toHaveCount(1);
    expect($suggestions[0])->toBeInstanceOf(AnalysisSuggestion::class);
    expect($suggestions[0]->message)->toBe('Add internal links to related content.');
    expect($suggestions[0]->details)->toContain('services page');
    expect($suggestions[0]->sourceCheckId)->toBe('e2e.page_analysis');
    expect($suggestions[0]->confidence)->toBe(0.78);
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 6. Failure collection                                       │
// └─────────────────────────────────────────────────────────────┘

test('failure collection is empty when no checks fail', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    expect($result->failures)->toBeArray();
    expect($result->failures)->toBeEmpty();
});

test('failure collection records isolated check failures', function () {
    MegSEO::register(createFailingCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    expect($result->failures)->toHaveCount(1);
    expect($result->failures[0]['check']->id)->toBe('e2e.failing');
    expect($result->failures[0]['error'])->toBe('Simulated failure in e2e test');
    expect($result->score()->value)->toBeNull();
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 7. Stable identifiers preserved                             │
// └─────────────────────────────────────────────────────────────┘

test('stable identifier is preserved throughout the result', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));

    expect($result->issues()[0]->sourceCheckId)->toBe('e2e.page_analysis');
    expect($result->warnings()[0]->sourceCheckId)->toBe('e2e.page_analysis');
    expect($result->suggestions()[0]->sourceCheckId)->toBe('e2e.page_analysis');
    expect($result->score()->contributors[0]['sourceCheckId'])->toBe('e2e.page_analysis');
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 8. toArray() serialization stability                        │
// └─────────────────────────────────────────────────────────────┘

test('toArray serialization preserves all categories', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('test'));
    $array = $result->toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveKeys(['score', 'issues', 'warnings', 'suggestions', 'failures', 'metadata']);

    expect($array['score'])->toMatchArray(['value' => 72.0]);

    expect($array['issues'][0])->toMatchArray([
        'message' => 'Meta description is missing.',
        'sourceCheckId' => 'e2e.page_analysis',
        'confidence' => 1.0,
    ]);

    expect($array['warnings'][0])->toMatchArray([
        'message' => 'Heading structure is suboptimal.',
        'sourceCheckId' => 'e2e.page_analysis',
    ]);

    expect($array['suggestions'][0])->toMatchArray([
        'message' => 'Add internal links to related content.',
        'sourceCheckId' => 'e2e.page_analysis',
        'confidence' => 0.78,
    ]);

    expect($array['failures'])->toBe([]);
    expect($array['metadata'])->toBe([]);
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 9. Deterministic repeated runs                              │
// └─────────────────────────────────────────────────────────────┘

test('deterministic repeated runs produce identical results', function () {
    MegSEO::register(createPageAnalysisCheck());

    $context = new AnalysisContext(
        subject: '<html><head><title>Determinism Test</title></head><body>Content</body></html>',
        attributes: ['url' => '/determinism'],
        requestId: 'det-001',
    );

    $r1 = MegSEO::analyze($context);
    $r2 = MegSEO::analyze($context);

    expect($r1->score()->value)->toBe($r2->score()->value);

    foreach (['issues', 'warnings', 'suggestions'] as $method) {
        expect($r1->{$method}())->toHaveCount(count($r2->{$method}()));
        for ($i = 0; $i < count($r1->{$method}()); $i++) {
            expect($r1->{$method}()[$i]->message)->toBe($r2->{$method}()[$i]->message);
            expect($r1->{$method}()[$i]->sourceCheckId)->toBe($r2->{$method}()[$i]->sourceCheckId);
        }
    }

    expect($r1->failures)->toBe($r2->failures);
    expect($r1->toArray())->toBe($r2->toArray());
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 10. Multi-check aggregation with deterministic ordering      │
// └─────────────────────────────────────────────────────────────┘

test('multiple checks produce combined correctly ordered results', function () {
    $ids = ['e2e.zulu', 'e2e.alpha', 'e2e.mike'];

    foreach ($ids as $id) {
        MegSEO::register(new class($id) implements Check
        {
            private readonly string $id;

            public function __construct(string $id) { $this->id = $id; }

            public function ref(): CheckReference
            {
                return new CheckReference($this->id, "Check {$this->id}");
            }

            public function analyze(AnalysisContext $context): CheckOutcome
            {
                return new CheckOutcome(
                    check: $this->ref(),
                    scoreContribution: new ScoreSummary(value: 25.0),
                    issues: [new AnalysisIssue("Issue from {$this->id}", 'd', $this->id)],
                    warnings: [new AnalysisWarning("Warning from {$this->id}", 'd', $this->id)],
                    suggestions: [new AnalysisSuggestion("Suggestion from {$this->id}", 'd', $this->id)],
                );
            }
        });
    }

    $result = MegSEO::analyze(new AnalysisContext('multi'));

    expect($result->score()->value)->toBe(75.0);

    $issueIds = array_map(fn ($i) => $i->sourceCheckId, $result->issues());
    expect($issueIds)->toBe($ids);
});

// ┌─────────────────────────────────────────────────────────────┐
// │ 11. Result accessor type verification                       │
// └─────────────────────────────────────────────────────────────┘

test('result accessors return expected types', function () {
    MegSEO::register(createPageAnalysisCheck());

    $result = MegSEO::analyze(new AnalysisContext('type-check'));

    expect($result->score())->toBeInstanceOf(ScoreSummary::class);
    expect($result->issues())->toBeArray();
    expect($result->warnings())->toBeArray();
    expect($result->suggestions())->toBeArray();
    expect($result->toArray())->toBeArray();
    expect($result->failures)->toBeArray();
});
