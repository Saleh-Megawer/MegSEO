<?php

declare(strict_types=1);

namespace MegSEO\DTO;

final readonly class AnalysisIssue
{
    public function __construct(
        public string $message,
        public string $details,
        public string $sourceCheckId,
        public ?float $confidence = null,
    ) {}
}
