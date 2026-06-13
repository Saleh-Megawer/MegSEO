<?php

declare(strict_types=1);

namespace MegSEO\Result;

use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\CheckOutcome;

final class ResultNormalizer
{
    /**
     * @param array<int, CheckOutcome> $outcomes
     * @return array<int, AnalysisIssue>
     */
    public function normalizeIssues(array $outcomes): array
    {
        $issues = [];

        foreach ($outcomes as $outcome) {
            foreach ($outcome->issues as $issue) {
                $issues[] = $issue;
            }
        }

        return $issues;
    }

    /**
     * @param array<int, CheckOutcome> $outcomes
     * @return array<int, AnalysisWarning>
     */
    public function normalizeWarnings(array $outcomes): array
    {
        $warnings = [];

        foreach ($outcomes as $outcome) {
            foreach ($outcome->warnings as $warning) {
                $warnings[] = $warning;
            }
        }

        return $warnings;
    }

    /**
     * @param array<int, CheckOutcome> $outcomes
     * @return array<int, AnalysisSuggestion>
     */
    public function normalizeSuggestions(array $outcomes): array
    {
        $suggestions = [];

        foreach ($outcomes as $outcome) {
            foreach ($outcome->suggestions as $suggestion) {
                $suggestions[] = $suggestion;
            }
        }

        return $suggestions;
    }
}
