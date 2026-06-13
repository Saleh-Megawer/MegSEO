<?php

declare(strict_types=1);

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;
use MegSEO\Contracts\Check;

test('zero registered checks returns valid empty result', function () {
    $engine = createEngine();

    $result = $engine->analyze(new AnalysisContext('test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
    expect($result->issues())->toBeEmpty();
    expect($result->warnings())->toBeEmpty();
    expect($result->suggestions())->toBeEmpty();
    expect($result->score()->value)->toBeNull();
    expect($result->failures)->toBeEmpty();
});

test('multiple checks produce aggregated results', function () {
    $engine = createEngine();

    $engine->registerCheck(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.issue', 'Issue Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 50.0),
                issues: [new AnalysisIssue('Issue found', 'Detail', 'check.issue')],
            );
        }
    });

    $engine->registerCheck(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.warning', 'Warning Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 40.0),
                warnings: [new AnalysisWarning('Warning found', 'Detail', 'check.warning')],
            );
        }
    });

    $engine->registerCheck(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.suggestion', 'Suggestion Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                suggestions: [new AnalysisSuggestion('Suggestion', 'Detail', 'check.suggestion')],
            );
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->sourceCheckId)->toBe('check.issue');

    expect($result->warnings())->toHaveCount(1);
    expect($result->warnings()[0]->sourceCheckId)->toBe('check.warning');

    expect($result->suggestions())->toHaveCount(1);
    expect($result->suggestions()[0]->sourceCheckId)->toBe('check.suggestion');
});

test('deterministic ordering preserves registry order across results', function () {
    $engine = createEngine();

    $ids = ['check.c', 'check.a', 'check.b'];

    foreach ($ids as $id) {
        $engine->registerCheck(new class($id) implements Check
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
                    issues: [new AnalysisIssue("Issue from {$this->id}", 'Details', $this->id)],
                );
            }
        });
    }

    $result1 = $engine->analyze(new AnalysisContext('test'));
    $result2 = $engine->analyze(new AnalysisContext('test'));

    expect($result1->issues())->toHaveCount(3);
    expect($result2->issues())->toHaveCount(3);

    // Verify deterministic ordering: registered order preserved
    $order1 = array_map(fn ($i) => $i->sourceCheckId, $result1->issues());
    $order2 = array_map(fn ($i) => $i->sourceCheckId, $result2->issues());

    expect($order1)->toBe($order2);
    expect($order1)->toBe($ids);
});

test('check returning empty findings contributes nothing to aggregation', function () {
    $engine = createEngine();

    $engine->registerCheck(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.empty', 'Empty Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    expect($result->issues())->toBeEmpty();
    expect($result->warnings())->toBeEmpty();
    expect($result->suggestions())->toBeEmpty();
    expect($result->score()->value)->toBeNull();
});

test('context data is passed to checks unchanged', function () {
    $engine = createEngine();
    $receivedSubject = null;

    $engine->registerCheck(new class($receivedSubject) implements Check
    {
        private $subject;

        public function __construct(&$subject) { $this->subject = &$subject; }

        public function ref(): CheckReference
        {
            return new CheckReference('check.inspect', 'Inspect Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            $this->subject = $context->subject;
            return new CheckOutcome(check: $this->ref());
        }
    });

    $context = new AnalysisContext('my-subject-data');
    $engine->analyze($context);

    expect($receivedSubject)->toBe('my-subject-data');
});

/**
 * Create a fresh engine instance for integration testing.
 */
function createEngine(): MegSEO\Core\Engine
{
    $registry = new MegSEO\Pipeline\CheckRegistry();
    $runner = new MegSEO\Pipeline\PipelineRunner();
    $pipeline = new MegSEO\Pipeline\CheckPipeline($registry, $runner);
    $plan = new MegSEO\Pipeline\SequentialExecutionPlan($pipeline);
    $resultNormalizer = new MegSEO\Result\ResultNormalizer();
    $scoreAggregator = new MegSEO\Result\ScoreAggregator();
    $aggregator = new MegSEO\Result\ResultAggregator($scoreAggregator, $resultNormalizer);

    return new MegSEO\Core\Engine($plan, $aggregator, $registry);
}
