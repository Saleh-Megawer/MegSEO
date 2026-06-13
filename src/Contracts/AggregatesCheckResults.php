<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisResult;

interface AggregatesCheckResults
{
    /**
     * @param array<int, \MegSEO\DTO\CheckOutcome> $outcomes
     * @param array<int, array{check: \MegSEO\DTO\CheckReference, error: string}> $failures
     */
    public function aggregate(array $outcomes, array $failures): AnalysisResult;
}
