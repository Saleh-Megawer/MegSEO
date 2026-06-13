<?php

declare(strict_types=1);

use MegSEO\Contracts\AnalyzesContexts;
use MegSEO\Contracts\Check;
use MegSEO\Contracts\ExecutionPolicy;
use MegSEO\Contracts\Pipeline;
use MegSEO\Contracts\RegistersChecks;
use MegSEO\Contracts\AggregatesCheckResults;
use MegSEO\Contracts\CheckResultFactory;
use MegSEO\Contracts\ContextFactory;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ExecutionDecision;
use MegSEO\DTO\ScoreSummary;

test('AnalyzesContexts contract requires analyze method returning AnalysisResult', function () {
    $analyzer = new class implements AnalyzesContexts
    {
        public function analyze(AnalysisContext $context): AnalysisResult
        {
            return new AnalysisResult(score: new ScoreSummary());
        }
    };

    $result = $analyzer->analyze(new AnalysisContext('test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
});

test('Check contract requires ref and analyze methods', function () {
    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('stub.id', 'Stub Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    expect($check->ref()->id)->toBe('stub.id');

    $outcome = $check->analyze(new AnalysisContext('test'));

    expect($outcome)->toBeInstanceOf(CheckOutcome::class);
    expect($outcome->check->id)->toBe('stub.id');
});

test('Check contract exposes stable identifier', function () {
    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('stable.id', 'Stable Check', '2.0.0');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    expect($check->ref()->id)->toBe('stable.id');
    expect($check->ref()->label)->toBe('Stable Check');
    expect($check->ref()->version)->toBe('2.0.0');
});

test('ExecutionPolicy contract returns ExecutionDecision', function () {
    $policy = new class implements ExecutionPolicy
    {
        public function evaluate(\Throwable $error, Check $check, AnalysisContext $context): ExecutionDecision
        {
            return new ExecutionDecision('abort', 'Test policy always aborts', false);
        }
    };

    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('stub', 'Stub');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $decision = $policy->evaluate(
        new \RuntimeException('test'),
        $check,
        new AnalysisContext('test'),
    );

    expect($decision)->toBeInstanceOf(ExecutionDecision::class);
    expect($decision->action)->toBe('abort');
});

test('Pipeline contract returns outcomes and failures', function () {
    $pipeline = new class implements Pipeline
    {
        public function execute(AnalysisContext $context): array
        {
            return [];
        }

        public function failures(): array
        {
            return [];
        }
    };

    $outcomes = $pipeline->execute(new AnalysisContext('test'));

    expect($outcomes)->toBe([]);
    expect($pipeline->failures())->toBe([]);
});

test('RegistersChecks contract supports registration and enumeration', function () {
    $registry = new class implements RegistersChecks
    {
        /** @var array<int, Check> */
        private array $checks = [];

        public function register(Check $check): void
        {
            $this->checks[] = $check;
        }

        public function all(): array
        {
            return $this->checks;
        }

        public function count(): int
        {
            return count($this->checks);
        }
    };

    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('reg.test', 'Test');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $registry->register($check);

    expect($registry->count())->toBe(1);
    expect($registry->all())->toHaveCount(1);
});

test('AggregatesCheckResults contract returns AnalysisResult', function () {
    $aggregator = new class implements AggregatesCheckResults
    {
        public function aggregate(array $outcomes, array $failures): AnalysisResult
        {
            return new AnalysisResult(score: new ScoreSummary());
        }
    };

    $result = $aggregator->aggregate([], []);

    expect($result)->toBeInstanceOf(AnalysisResult::class);
});

test('CheckResultFactory contract creates CheckOutcome', function () {
    $factory = new class implements CheckResultFactory
    {
        public function createOutcome(
            string $checkId,
            ?float $scoreValue = null,
            array $issues = [],
            array $warnings = [],
            array $suggestions = [],
            array $metadata = [],
        ): CheckOutcome {
            return new CheckOutcome(
                check: new CheckReference($checkId, $checkId),
                scoreContribution: $scoreValue !== null ? new ScoreSummary(value: $scoreValue) : null,
                issues: $issues,
                warnings: $warnings,
                suggestions: $suggestions,
                metadata: $metadata,
            );
        }
    };

    $outcome = $factory->createOutcome('check.test', 95.0);

    expect($outcome)->toBeInstanceOf(CheckOutcome::class);
    expect($outcome->check->id)->toBe('check.test');
    expect($outcome->scoreContribution?->value)->toBe(95.0);
});

test('ContextFactory contract creates AnalysisContext', function () {
    $factory = new class implements ContextFactory
    {
        public function create(mixed $subject, array $attributes = [], array $options = [], ?string $requestId = null): AnalysisContext
        {
            return new AnalysisContext(
                subject: $subject,
                attributes: $attributes,
                options: $options,
                requestId: $requestId,
            );
        }
    };

    $context = $factory->create('my-subject', ['lang' => 'en'], ['verbose' => true], 'req-42');

    expect($context->subject)->toBe('my-subject');
    expect($context->attributes->get('lang'))->toBe('en');
    expect($context->options)->toBe(['verbose' => true]);
    expect($context->requestId)->toBe('req-42');
});
