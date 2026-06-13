<?php

declare(strict_types=1);

use MegSEO\Laravel\Facades\MegSEO as MegSEOFacade;
use MegSEO\Core\Engine;
use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

test('service provider binds Engine as a singleton in the container', function () {
    $engine1 = $this->app->make('megseo.engine');
    $engine2 = $this->app->make('megseo.engine');

    expect($engine1)->toBeInstanceOf(Engine::class);
    expect($engine1)->toBe($engine2);
});

test('Engine is bound to its class name alias', function () {
    $engine = $this->app->make(Engine::class);

    expect($engine)->toBeInstanceOf(Engine::class);
});

test('MegSEO facade resolves to engine and can analyze', function () {
    $engine = MegSEOFacade::getFacadeRoot();

    $result = $engine->analyze(new AnalysisContext('facade-test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
});

test('MegSEO facade analyze returns valid AnalysisResult', function () {
    $result = MegSEOFacade::analyze(new AnalysisContext('test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
    expect($result->issues())->toBeEmpty();
    expect($result->score()->value)->toBeNull();
});

test('facade registers checks and participates in analysis', function () {
    $check = new class implements Check
    {
        public function ref(): CheckReference
        {
            return new CheckReference('facade.check', 'Facade Check');
        }

        public function analyze(AnalysisContext $context): CheckOutcome
        {
            return new CheckOutcome(
                check: $this->ref(),
                scoreContribution: new ScoreSummary(value: 100.0),
            );
        }
    };

    MegSEOFacade::register($check);

    $result = MegSEOFacade::analyze(new AnalysisContext('test'));

    expect($result->score()->value)->toBe(100.0);
});

test('config merges megseo configuration', function () {
    $policy = config('megseo.execution_policy');

    expect($policy)->toBe('isolate_failures');
});
