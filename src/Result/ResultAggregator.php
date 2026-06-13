<?php

declare(strict_types=1);

namespace MegSEO\Result;

use MegSEO\Contracts\AggregatesCheckResults;
use MegSEO\DTO\AnalysisResult;
use MegSEO\DTO\ScoreSummary;

final class ResultAggregator implements AggregatesCheckResults
{
    public function __construct(
        private readonly ScoreAggregator $scoreAggregator,
        private readonly ResultNormalizer $normalizer,
    ) {}

    /**
     * @param array<int, \MegSEO\DTO\CheckOutcome> $outcomes
     * @param array<int, array{check: \MegSEO\DTO\CheckReference, error: string}> $failures
     */
    public function aggregate(array $outcomes, array $failures): AnalysisResult
    {
        $score = $this->scoreAggregator->aggregate($outcomes);
        $issues = $this->normalizer->normalizeIssues($outcomes);
        $warnings = $this->normalizer->normalizeWarnings($outcomes);
        $suggestions = $this->normalizer->normalizeSuggestions($outcomes);

        return new AnalysisResult(
            score: $score,
            issues: $issues,
            warnings: $warnings,
            suggestions: $suggestions,
            failures: $failures,
        );
    }
}
