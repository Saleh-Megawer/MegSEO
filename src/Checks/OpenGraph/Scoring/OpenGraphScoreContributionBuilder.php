<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\Scoring;

use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\ScoreSummary;

final readonly class OpenGraphScoreContributionBuilder
{
    private const MAX_SCORE = 100.0;
    private const PENALTY_MISSING_TITLE = 25.0;
    private const PENALTY_MISSING_DESC = 25.0;
    private const PENALTY_MISSING_IMAGE = 25.0;
    private const PENALTY_EMPTY = 20.0;
    private const PENALTY_INVALID_IMAGE = 15.0;
    private const PENALTY_RELATIVE_IMAGE = 10.0;
    private const PENALTY_CONFLICT = 15.0;

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
            metadata: ['max_score' => self::MAX_SCORE, 'total_deductions' => round($deductions, 1), 'rationale' => $rationale],
        );
    }

    private function penaltyForIssue(AnalysisIssue $issue): float
    {
        return match (true) {
            str_contains($issue->message, 'title') && str_contains($issue->message, 'missing') => self::PENALTY_MISSING_TITLE,
            str_contains($issue->message, 'description') && str_contains($issue->message, 'missing') => self::PENALTY_MISSING_DESC,
            str_contains($issue->message, 'image') && str_contains($issue->message, 'missing') => self::PENALTY_MISSING_IMAGE,
            str_contains($issue->message, 'empty') => self::PENALTY_EMPTY,
            default => 10.0,
        };
    }

    private function penaltyForWarning(AnalysisWarning $warning): float
    {
        return match (true) {
            str_contains($warning->message, 'invalid') => self::PENALTY_INVALID_IMAGE,
            str_contains($warning->message, 'relative') => self::PENALTY_RELATIVE_IMAGE,
            default => 5.0,
        };
    }

    private function penaltyForSuggestion(AnalysisSuggestion $suggestion): float
    {
        return str_contains($suggestion->message, 'Conflicting') ? self::PENALTY_CONFLICT : 3.0;
    }
}
