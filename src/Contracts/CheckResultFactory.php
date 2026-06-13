<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

/**
 * Factory contract for creating {@see CheckOutcome} instances.
 *
 * Provides a simplified interface for check authors who want to
 * produce structured outcomes without manually constructing every
 * DTO dependency.
 */
interface CheckResultFactory
{
    /**
     * Creates a CheckOutcome with the given parameters.
     *
     * @param array<int, \MegSEO\DTO\AnalysisIssue> $issues
     * @param array<int, \MegSEO\DTO\AnalysisWarning> $warnings
     * @param array<int, \MegSEO\DTO\AnalysisSuggestion> $suggestions
     * @param array<string, mixed> $metadata
     */
    public function createOutcome(
        string $checkId,
        ?float $scoreValue = null,
        array $issues = [],
        array $warnings = [],
        array $suggestions = [],
        array $metadata = [],
    ): CheckOutcome;
}
