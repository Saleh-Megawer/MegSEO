<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\DTO;

final readonly class TitleDuplicateMatch
{
    public function __construct(
        public string $matchedTitle,
        public string $matchedReference,
        public string $matchReason = '',
    ) {}
}
