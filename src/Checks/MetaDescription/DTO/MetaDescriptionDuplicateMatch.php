<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\DTO;

final readonly class MetaDescriptionDuplicateMatch
{
    public function __construct(
        public string $matchedDescription,
        public string $matchedReference,
        public string $matchReason = '',
    ) {}
}
