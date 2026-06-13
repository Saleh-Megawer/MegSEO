<?php

declare(strict_types=1);

namespace MegSEO\Pipeline;

use MegSEO\Contracts\Check;
use MegSEO\Contracts\ExecutionPolicy;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;

final class PipelineRunner
{
    public function __construct(
        private readonly ExecutionPolicy $policy = new \MegSEO\Policy\IsolateFailuresExecutionPolicy(),
    ) {}

    /**
     * @param array<int, Check> $checks
     * @return array<int, CheckOutcome|array{check: \MegSEO\DTO\CheckReference, error: string}>
     */
    public function run(array $checks, AnalysisContext $context): array
    {
        $results = [];

        foreach ($checks as $check) {
            try {
                $results[] = $check->analyze($context);
            } catch (\Throwable $error) {
                $decision = $this->policy->evaluate($error, $check, $context);

                if ($decision->recordFailure) {
                    $results[] = [
                        'check' => $check->ref(),
                        'error' => $error->getMessage(),
                    ];
                }

                if ($decision->action === 'abort') {
                    break;
                }
            }
        }

        return $results;
    }
}
