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
use MegSEO\Pipeline\CheckPipeline;
use MegSEO\Pipeline\CheckRegistry;
use MegSEO\Pipeline\PipelineRunner;
use MegSEO\Pipeline\SequentialExecutionPlan;
use MegSEO\Result\ResultAggregator;
use MegSEO\Result\ResultNormalizer;
use MegSEO\Result\ScoreAggregator;

final class Engine implements AnalyzesContexts, RegistersChecks
{
    public function __construct(
        private readonly Pipeline $pipeline,
        private readonly AggregatesCheckResults $aggregator,
        private readonly RegistersChecks $registry,
    ) {}

    public static function make(): self
    {
        $registry = new CheckRegistry();

        return new self(
            pipeline: new SequentialExecutionPlan(
                new CheckPipeline($registry, new PipelineRunner()),
            ),
            aggregator: new ResultAggregator(
                new ScoreAggregator(),
                new ResultNormalizer(),
            ),
            registry: $registry,
        );
    }

    public function analyze(AnalysisContext $context): AnalysisResult
    {
        $outcomes = $this->pipeline->execute($context);
        $failures = $this->pipeline->failures();

        return $this->aggregator->aggregate($outcomes, $failures);
    }

    public function register(Check $check): void
    {
        $this->registry->register($check);
    }

    public function registerCheck(Check $check): void
    {
        $this->register($check);
    }

    /** @return array<int, Check> */
    public function all(): array
    {
        return $this->registry->all();
    }

    public function count(): int
    {
        return $this->registry->count();
    }
}
