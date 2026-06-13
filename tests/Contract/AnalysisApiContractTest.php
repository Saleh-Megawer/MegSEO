<?php

declare(strict_types=1);

use MegSEO\Contracts\AnalyzesContexts;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\ScoreSummary;

test('Analysis API contract returns AnalysisResult with required accessors', function () {
    $analyzer = new class implements AnalyzesContexts
    {
        public function analyze(AnalysisContext $context): AnalysisResult
        {
            return new AnalysisResult(
                score: new ScoreSummary(value: 100.0),
                issues: [],
                warnings: [],
                suggestions: [],
            );
        }
    };

    $result = $analyzer->analyze(new AnalysisContext('test'));

    expect($result)->toBeInstanceOf(AnalysisResult::class);
    expect($result->score())->toBeInstanceOf(ScoreSummary::class);
    expect($result->issues())->toBeArray();
    expect($result->warnings())->toBeArray();
    expect($result->suggestions())->toBeArray();
});

test('Analysis API contract handles empty pipeline gracefully', function () {
    $analyzer = new class implements AnalyzesContexts
    {
        public function analyze(AnalysisContext $context): AnalysisResult
        {
            return new AnalysisResult(score: new ScoreSummary());
        }
    };

    $result = $analyzer->analyze(new AnalysisContext('empty-context'));

    expect($result->score()->value)->toBeNull();
    expect($result->issues())->toBeEmpty();
    expect($result->warnings())->toBeEmpty();
    expect($result->suggestions())->toBeEmpty();
    expect($result->failures)->toBeEmpty();
});

test('Analysis API accepts immutable context and returns immutable result', function () {
    $context = new AnalysisContext(
        subject: 'page-data',
        attributes: ['url' => '/test'],
        options: ['detailed' => true],
        requestId: 'abc-123',
    );

    $analyzer = new class implements AnalyzesContexts
    {
        public function analyze(AnalysisContext $context): AnalysisResult
        {
            expect($context->subject)->toBe('page-data');
            expect($context->attributes->get('url'))->toBe('/test');

            return new AnalysisResult(score: new ScoreSummary());
        }
    };

    $result = $analyzer->analyze($context);

    expect($result)->toBeInstanceOf(AnalysisResult::class);
});
