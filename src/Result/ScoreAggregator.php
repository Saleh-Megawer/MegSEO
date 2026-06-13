<?php

declare(strict_types=1);

namespace MegSEO\Result;

use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\ScoreSummary;

final class ScoreAggregator
{
    /**
     * @param array<int, CheckOutcome> $outcomes
     */
    public function aggregate(array $outcomes): ScoreSummary
    {
        $contributors = [];
        $totalValue = 0.0;
        $hasScores = false;

        foreach ($outcomes as $outcome) {
            $score = $outcome->scoreContribution;

            if ($score !== null && $score->value !== null) {
                $hasScores = true;
                $totalValue += $score->value;
                $contributors[] = [
                    'value' => $score->value,
                    'sourceCheckId' => $outcome->check->id,
                ];
            }
        }

        return new ScoreSummary(
            value: $hasScores ? $totalValue : null,
            contributors: $contributors,
        );
    }
}
