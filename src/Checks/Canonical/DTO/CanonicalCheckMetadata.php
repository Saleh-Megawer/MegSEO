<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\DTO;

final readonly class CanonicalCheckMetadata
{
    /** @var array<int, string> */
    public array $ruleIdentifiers;

    public function __construct(
        public string $checkIdentifier,
        public bool $isSelfReferencing = false,
        public bool $isCrossDomain = false,
        public bool $isRelative = false,
        public bool $multipleCanonicalsDetected = false,
        public bool $normalizationApplied = false,
        array $ruleIdentifiers = [],
    ) {
        $this->ruleIdentifiers = $ruleIdentifiers;
    }
}
