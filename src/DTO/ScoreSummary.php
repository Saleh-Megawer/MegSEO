<?php

declare(strict_types=1);

namespace MegSEO\DTO;

final readonly class ScoreSummary
{
    /**
     * @param array<int, array{value: float, sourceCheckId: string}> $contributors
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        public ?float $value = null,
        public array $contributors = [],
        public array $metadata = [],
    ) {}
}
