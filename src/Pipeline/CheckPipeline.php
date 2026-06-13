<?php

declare(strict_types=1);

namespace MegSEO\Pipeline;

use MegSEO\Contracts\Pipeline;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;

final class CheckPipeline implements Pipeline
{
    /** @var array<int, array{check: \MegSEO\DTO\CheckReference, error: string}> */
    private array $collectedFailures = [];

    public function __construct(
        private readonly CheckRegistry $registry,
        private readonly PipelineRunner $runner,
    ) {}

    /** @return array<int, CheckOutcome> */
    public function execute(AnalysisContext $context): array
    {
        $this->collectedFailures = [];
        $checks = $this->registry->all();

        $results = $this->runner->run($checks, $context);

        $outcomes = [];
        foreach ($results as $result) {
            if ($result instanceof CheckOutcome) {
                $outcomes[] = $result;
            } else {
                $this->collectedFailures[] = $result;
            }
        }

        return $outcomes;
    }

    /** @return array<int, array{check: \MegSEO\DTO\CheckReference, error: string}> */
    public function failures(): array
    {
        return $this->collectedFailures;
    }
}
