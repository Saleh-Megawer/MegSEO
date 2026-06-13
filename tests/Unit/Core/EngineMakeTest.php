<?php

declare(strict_types=1);

use MegSEO\Core\Engine;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;
use MegSEO\Contracts\AnalyzesContexts;
use MegSEO\Contracts\RegistersChecks;
use MegSEO\Contracts\Check;

beforeEach(function () {
    $this->engine = Engine::make();
});

test('Engine::make() returns a valid engine without arguments', function () {
    expect($this->engine)->toBeInstanceOf(Engine::class);
    expect($this->engine)->toBeInstanceOf(AnalyzesContexts::class);
    expect($this->engine)->toBeInstanceOf(RegistersChecks::class);
});

test('Engine::make() analyzes empty pipeline and returns valid AnalysisResult', function () {
    $result = $this->engine->analyze(new AnalysisContext('test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
    expect($result->issues())->toBeEmpty();
    expect($result->warnings())->toBeEmpty();
    expect($result->suggestions())->toBeEmpty();
    expect($result->score()->value)->toBeNull();
    expect($result->failures)->toBeEmpty();
});

test('Engine::make() can register checks and produce results', function () {
    $this->engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('make.test', 'Make Test Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 75.0),
            );
        }
    });

    $result = $this->engine->analyze(new AnalysisContext('test'));

    expect($result->score()->value)->toBe(75.0);
});

test('Engine::make() count reflects registered checks', function () {
    expect($this->engine->count())->toBe(0);

    $this->engine->register(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('a', 'A');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    });

    expect($this->engine->count())->toBe(1);
});

test('Engine::make() all returns registered checks', function () {
    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('b', 'B');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    };

    $this->engine->register($check);

    expect($this->engine->all())->toBe([$check]);
});

test('Engine constructor with custom pipeline still works for DI scenarios', function () {
    $registry = new MegSEO\Pipeline\CheckRegistry();
    $runner = new MegSEO\Pipeline\PipelineRunner();
    $pipeline = new MegSEO\Pipeline\CheckPipeline($registry, $runner);
    $plan = new MegSEO\Pipeline\SequentialExecutionPlan($pipeline);
    $aggregator = new MegSEO\Result\ResultAggregator(
        new MegSEO\Result\ScoreAggregator(),
        new MegSEO\Result\ResultNormalizer(),
    );

    $engine = new Engine($plan, $aggregator, $registry);

    $result = $engine->analyze(new AnalysisContext('test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
});

test('registerCheck is an alias for register', function () {
    $this->engine->registerCheck(new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('alias', 'Alias');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(check: $this->ref());
        }
    });

    expect($this->engine->count())->toBe(1);
});
