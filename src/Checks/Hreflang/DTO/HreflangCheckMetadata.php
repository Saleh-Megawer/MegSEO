<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\DTO;

final readonly class HreflangCheckMetadata
{
    public array $ruleIdentifiers;

    public function __construct(
        public string $checkIdentifier,
        public int $entryCount = 0,
        public bool $hasXDefault = false,
        public int $selfReferencingCount = 0,
        public int $invalidLangCodes = 0,
        public int $invalidUrls = 0,
        public bool $conflictingEntriesDetected = false,
        array $ruleIdentifiers = [],
    ) {
        $this->ruleIdentifiers = $ruleIdentifiers;
    }
}
