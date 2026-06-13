<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;

interface Pipeline
{
    /**
     * @return array<int, CheckOutcome>
     */
    public function execute(AnalysisContext $context): array;

    /**
     * @return array<int, array{check: \MegSEO\DTO\CheckReference, error: string}>
     */
    public function failures(): array;
}
