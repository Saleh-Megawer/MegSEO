<?php

declare(strict_types=1);

namespace MegSEO\DTO;

use MegSEO\Contracts\ArrayableResult;

final readonly class AnalysisResult implements ArrayableResult
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

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'score' => [
                'value' => $this->score->value,
                'contributors' => $this->score->contributors,
            ],
            'issues' => array_map(
                fn (AnalysisIssue $i): array => [
                    'message' => $i->message,
                    'details' => $i->details,
                    'sourceCheckId' => $i->sourceCheckId,
                    'confidence' => $i->confidence,
                ],
                $this->issues,
            ),
            'warnings' => array_map(
                fn (AnalysisWarning $w): array => [
                    'message' => $w->message,
                    'details' => $w->details,
                    'sourceCheckId' => $w->sourceCheckId,
                ],
                $this->warnings,
            ),
            'suggestions' => array_map(
                fn (AnalysisSuggestion $s): array => [
                    'message' => $s->message,
                    'details' => $s->details,
                    'sourceCheckId' => $s->sourceCheckId,
                    'confidence' => $s->confidence,
                ],
                $this->suggestions,
            ),
            'failures' => array_map(
                fn (array $f): array => [
                    'check' => $f['check']->id,
                    'error' => $f['error'],
                ],
                $this->failures,
            ),
            'metadata' => $this->metadata,
        ];
    }
}
