<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\Scoring;

use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\ScoreSummary;

final readonly class CanonicalScoreContributionBuilder
{
    private const MAX_SCORE = 100.0;

    private const PENALTY_MISSING = 40.0;
    private const PENALTY_EMPTY = 35.0;
    private const PENALTY_INVALID = 30.0;
    private const PENALTY_MULTIPLE = 25.0;
    private const PENALTY_RELATIVE = 15.0;
    private const PENALTY_CROSS_DOMAIN = 10.0;
    private const PENALTY_NOT_SELF_REF = 5.0;

    /**
     * @param array<int, AnalysisIssue> $issues
     * @param array<int, AnalysisWarning> $warnings
     * @param array<int, AnalysisSuggestion> $suggestions
     */
    public function build(array $issues, array $warnings, array $suggestions, array $metadata = []): ScoreSummary
    {
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
            str_contains($issue->message, 'missing') => self::PENALTY_MISSING,
            str_contains($issue->message, 'empty') => self::PENALTY_EMPTY,
            str_contains($issue->message, 'invalid') => self::PENALTY_INVALID,
            str_contains($issue->message, 'Multiple') => self::PENALTY_MULTIPLE,
            default => 10.0,
        };
    }

    private function penaltyForWarning(AnalysisWarning $warning): float
    {
        return match (true) {
            str_contains($warning->message, 'relative') => self::PENALTY_RELATIVE,
            default => 5.0,
        };
    }

    private function penaltyForSuggestion(AnalysisSuggestion $suggestion): float
    {
        return match (true) {
            str_contains($suggestion->message, 'different domain') => self::PENALTY_CROSS_DOMAIN,
            str_contains($suggestion->message, 'self-reference') => self::PENALTY_NOT_SELF_REF,
            default => 3.0,
        };
    }
}
