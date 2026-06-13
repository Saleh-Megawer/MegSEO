<?php

declare(strict_types=1);

namespace MegSEO\Pipeline;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;

final class PipelineRunner
{
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
                $results[] = [
                    'check' => $check->ref(),
                    'error' => $error->getMessage(),
                ];
            }
        }

        return $results;
    }
}
