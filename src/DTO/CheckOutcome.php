<?php

declare(strict_types=1);

namespace MegSEO\DTO;

final readonly class CheckOutcome
{
    /**
     * @param array<int, AnalysisIssue> $issues
     * @param array<int, AnalysisWarning> $warnings
     * @param array<int, AnalysisSuggestion> $suggestions
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public CheckReference $check,
        public ?ScoreSummary $scoreContribution = null,
        public array $issues = [],
        public array $warnings = [],
        public array $suggestions = [],
        public array $metadata = [],
    ) {}
}
