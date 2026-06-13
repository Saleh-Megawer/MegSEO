<?php

declare(strict_types=1);

namespace MegSEO\DTO;

final readonly class AnalysisResult
{
    /**
     * @param array<int, AnalysisIssue> $issues
     * @param array<int, AnalysisWarning> $warnings
     * @param array<int, AnalysisSuggestion> $suggestions
     * @param array<string, mixed> $metadata
     * @param array<int, array{check: CheckReference, error: string}> $failures
     */
    public function __construct(
        public ScoreSummary $score,
        public array $issues = [],
        public array $warnings = [],
        public array $suggestions = [],
        public array $metadata = [],
        public array $failures = [],
    ) {}

    public function score(): ScoreSummary
    {
        return $this->score;
    }

    /** @return array<int, AnalysisIssue> */
    public function issues(): array
    {
        return $this->issues;
    }

    /** @return array<int, AnalysisWarning> */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /** @return array<int, AnalysisSuggestion> */
    public function suggestions(): array
    {
        return $this->suggestions;
    }
}
