<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\DTO;

final readonly class MetaDescriptionCheckMetadata
{
    /** @var array<int, string> */
    public array $ruleIdentifiers;

    public function __construct(
        public string $checkIdentifier,
        public ?string $rawDescription = null,
        public ?string $normalizedDescription = null,
        public int $normalizedLength = 0,
        public bool $duplicateSupportUsed = false,
        public bool $focusKeywordSupplied = false,
        array $ruleIdentifiers = [],
    ) {
        $this->ruleIdentifiers = $ruleIdentifiers;
    }
}
