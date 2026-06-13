<?php

declare(strict_types=1);

namespace MegSEO\Support;

use MegSEO\Contracts\CheckResultFactory;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

final class DefaultCheckResultFactory implements CheckResultFactory
{
    public function createOutcome(
        string $checkId,
        ?float $scoreValue = null,
        array $issues = [],
        array $warnings = [],
        array $suggestions = [],
        array $metadata = [],
    ): CheckOutcome {
        return new CheckOutcome(
            check: new CheckReference($checkId, $checkId),
            scoreContribution: $scoreValue !== null
                ? new ScoreSummary(value: $scoreValue)
                : null,
            issues: $issues,
            warnings: $warnings,
            suggestions: $suggestions,
            metadata: $metadata,
        );
    }
}
