<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\DTO;

final readonly class CanonicalUrlMatchReport
{
    public function __construct(
        public bool $isSelfReferencing = false,
        public bool $isCrossDomain = false,
        public bool $isRelative = false,
        public bool $hasPageUrl = false,
        public string $matchDetails = '',
    ) {}
}
