<?php

declare(strict_types=1);

use MegSEO\Contracts\Check;
use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;
use MegSEO\Policy\FailFastExecutionPolicy;
use MegSEO\Policy\IsolateFailuresExecutionPolicy;

test('isolate-failures policy continues after check failure and records it', function () {
    $engine = Engine::make(new IsolateFailuresExecutionPolicy());

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.before', 'Before');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 10.0),
            );
        }
    });

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.failing', 'Failing');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            throw new \RuntimeException('Simulated check failure');
        }
    });

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.after', 'After');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                issues: [new AnalysisIssue('Issue', 'details', 'check.after')],
            );
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    // First check contributed score
    expect($result->score()->value)->toBe(10.0);

    // Third check still executed — its issues are present
    expect($result->issues())->toHaveCount(1);
    expect($result->issues()[0]->sourceCheckId)->toBe('check.after');

    // Failure recorded
    expect($result->failures)->toHaveCount(1);
    expect($result->failures[0]['check']->id)->toBe('check.failing');
    expect($result->failures[0]['error'])->toBe('Simulated check failure');
});

test('fail-fast policy stops execution on first failure', function () {
    $engine = Engine::make(new FailFastExecutionPolicy());

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.before', 'Before');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 5.0),
            );
        }
    });

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.failing', 'Failing');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            throw new \RuntimeException('Simulated fatal failure');
        }
    });

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.after', 'After');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                issues: [new AnalysisIssue('Should not reach', 'details', 'check.after')],
            );
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    // First check contributed score
    expect($result->score()->value)->toBe(5.0);

    // Failure recorded
    expect($result->failures)->toHaveCount(1);
    expect($result->failures[0]['check']->id)->toBe('check.failing');

    // Third check should NOT have executed — no issues from it
    $afterIssues = array_filter(
        $result->issues(),
        fn ($i) => $i->sourceCheckId === 'check.after',
    );
    expect($afterIssues)->toBeEmpty();
});

test('default policy via Engine::make() isolates failures', function () {
    $engine = Engine::make();

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.ok', 'OK');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 50.0),
            );
        }
    });

    $engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('check.broken', 'Broken');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            throw new \LogicException('Unexpected state');
        }
    });

    $result = $engine->analyze(new AnalysisContext('test'));

    expect($result->score()->value)->toBe(50.0);
    expect($result->failures)->toHaveCount(1);
});
