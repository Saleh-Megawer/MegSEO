<?php

declare(strict_types=1);

namespace MegSEO\Core;

use MegSEO\Contracts\AnalyzesContexts;
use MegSEO\Contracts\AggregatesCheckResults;
use MegSEO\Contracts\Check;
use MegSEO\Contracts\Pipeline;
use MegSEO\Contracts\RegistersChecks;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisResult;

final class Engine implements AnalyzesContexts
{
    public function __construct(
        private readonly Pipeline $pipeline,
        private readonly AggregatesCheckResults $aggregator,
        private readonly ?RegistersChecks $registry = null,
    ) {}

    public function analyze(AnalysisContext $context): AnalysisResult
    {
        $outcomes = $this->pipeline->execute($context);
        $failures = $this->pipeline->failures();

        return $this->aggregator->aggregate($outcomes, $failures);
    }

    public function registerCheck(Check $check): void
    {
        if ($this->registry !== null) {
            $this->registry->register($check);
        }
    }
}
