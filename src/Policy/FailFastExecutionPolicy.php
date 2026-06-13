<?php

declare(strict_types=1);

namespace MegSEO\Policy;

use MegSEO\Contracts\Check;
use MegSEO\Contracts\ExecutionPolicy;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\ExecutionDecision;

final readonly class FailFastExecutionPolicy implements ExecutionPolicy
{
    public function evaluate(
        \Throwable $error,
        Check $check,
        AnalysisContext $context,
    ): ExecutionDecision {
        return new ExecutionDecision(
            action: 'abort',
            reason: sprintf(
                'Check "%s" failed: %s',
                $check->ref()->id,
                $error->getMessage(),
            ),
            recordFailure: true,
        );
    }
}
