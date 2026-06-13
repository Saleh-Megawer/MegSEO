<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\CheckOutcome;

interface CheckResultFactory
{
    public function createOutcome(
        string $checkId,
        ?float $scoreValue = null,
        array $issues = [],
        array $warnings = [],
        array $suggestions = [],
        array $metadata = [],
    ): CheckOutcome;
}
