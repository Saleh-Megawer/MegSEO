<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\ExecutionDecision;

interface ExecutionPolicy
{
    public function evaluate(\Throwable $error, Check $check, AnalysisContext $context): ExecutionDecision;
}
