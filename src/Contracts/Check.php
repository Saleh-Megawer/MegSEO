<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;

interface Check
{
    public function ref(): CheckReference;

    public function analyze(AnalysisContext $context): CheckOutcome;
}
