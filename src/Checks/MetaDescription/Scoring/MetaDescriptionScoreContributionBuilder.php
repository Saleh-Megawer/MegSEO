<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Scoring;

use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\ScoreSummary;

final readonly class MetaDescriptionScoreContributionBuilder
{
    private const MAX_SCORE = 100.0;

    private const PENALTY_MISSING_DESCRIPTION = 40.0;
    private const PENALTY_EMPTY_DESCRIPTION = 35.0;
    private const PENALTY_SEPARATOR_ONLY = 30.0;
    private const PENALTY_SHORT_DESCRIPTION = 15.0;
    private const PENALTY_LONG_DESCRIPTION = 10.0;
    private const PENALTY_MISSING_KEYWORD = 5.0;
    private const PENALTY_DUPLICATE_DESCRIPTION = 8.0;

    /**
     * @param array<int, AnalysisIssue> $issues
     * @param array<int, AnalysisWarning> $warnings
     * @param array<int, AnalysisSuggestion> $suggestions
     * @param array<string, mixed> $metadata
     */
    public function build(
        array $issues,
        array $warnings,
        array $suggestions,
        array $metadata = [],
    ): ScoreSummary {
        $deductions = 0.0;
        $contributors = [];
        $rationale = [];

        foreach ($issues as $issue) {
            $penalty = $this->penaltyForIssue($issue);
            $deductions += $penalty;
            $contributors[] = ['value' => -$penalty, 'sourceCheckId' => $issue->sourceCheckId];
            $rationale[] = ['finding' => $issue->message, 'severity' => 'issue', 'deduction' => $penalty];
        }

        foreach ($warnings as $warning) {
            $penalty = $this->penaltyForWarning($warning);
            $deductions += $penalty;
            $contributors[] = ['value' => -$penalty, 'sourceCheckId' => $warning->sourceCheckId];
            $rationale[] = ['finding' => $warning->message, 'severity' => 'warning', 'deduction' => $penalty];
        }

        foreach ($suggestions as $suggestion) {
            $penalty = $this->penaltyForSuggestion($suggestion);
            $deductions += $penalty;
            $contributors[] = ['value' => -$penalty, 'sourceCheckId' => $suggestion->sourceCheckId];
            $rationale[] = ['finding' => $suggestion->message, 'severity' => 'suggestion', 'deduction' => $penalty];
        }

        $score = max(0.0, self::MAX_SCORE - $deductions);

        return new ScoreSummary(
            value: round($score, 1),
            contributors: $contributors,
            metadata: [
                'max_score' => self::MAX_SCORE,
                'total_deductions' => round($deductions, 1),
                'rationale' => $rationale,
            ],
        );
    }

    private function penaltyForIssue(AnalysisIssue $issue): float
    {
        return match (true) {
            str_contains($issue->message, 'missing') => self::PENALTY_MISSING_DESCRIPTION,
            str_contains($issue->message, 'empty') => self::PENALTY_EMPTY_DESCRIPTION,
            str_contains($issue->message, 'punctuation') => self::PENALTY_SEPARATOR_ONLY,
            default => 10.0,
        };
    }

    private function penaltyForWarning(AnalysisWarning $warning): float
    {
        return match (true) {
            str_contains($warning->message, 'too short') => self::PENALTY_SHORT_DESCRIPTION,
            str_contains($warning->message, 'too long') => self::PENALTY_LONG_DESCRIPTION,
            default => 5.0,
        };
    }

    private function penaltyForSuggestion(AnalysisSuggestion $suggestion): float
    {
        return match (true) {
            str_contains($suggestion->message, 'Focus keyword') => self::PENALTY_MISSING_KEYWORD,
            str_contains($suggestion->message, 'Duplicate') => self::PENALTY_DUPLICATE_DESCRIPTION,
            default => 3.0,
        };
    }
}
