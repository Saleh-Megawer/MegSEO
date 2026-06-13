<?php

declare(strict_types=1);

namespace MegSEO\Pipeline;

use MegSEO\Contracts\Pipeline;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;

final class SequentialExecutionPlan implements Pipeline
{
    public function __construct(
        private readonly Pipeline $pipeline,
    ) {}

    /** @return array<int, CheckOutcome> */
    public function execute(AnalysisContext $context): array
    {
        return $this->pipeline->execute($context);
    }

    /** @return array<int, array{check: \MegSEO\DTO\CheckReference, error: string}> */
    public function failures(): array
    {
        return $this->pipeline->failures();
    }
}
