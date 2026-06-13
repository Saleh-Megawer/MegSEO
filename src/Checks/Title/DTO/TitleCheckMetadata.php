<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\DTO;

final readonly class TitleCheckMetadata
{
    /** @var array<int, string> */
    public array $ruleIdentifiers;

    public function __construct(
        public string $checkIdentifier,
        public ?string $rawTitle = null,
        public ?string $normalizedTitle = null,
        public int $normalizedLength = 0,
        public bool $duplicateSupportUsed = false,
        public bool $focusKeywordSupplied = false,
        array $ruleIdentifiers = [],
    ) {
        $this->ruleIdentifiers = $ruleIdentifiers;
    }
}
