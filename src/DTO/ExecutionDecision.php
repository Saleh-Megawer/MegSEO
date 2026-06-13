<?php

declare(strict_types=1);

namespace MegSEO\DTO;

final readonly class ExecutionDecision
{
    public function __construct(
        public string $action,
        public string $reason,
        public bool $recordFailure = false,
    ) {}
}
