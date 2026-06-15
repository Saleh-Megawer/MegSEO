<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph\DTO;

final readonly class OpenGraphCheckMetadata
{
    /** @var array<int, string> */
    public array $ruleIdentifiers;

    public function __construct(
        public string $checkIdentifier,
        public bool $ogTitleProvided = false,
        public bool $ogDescriptionProvided = false,
        public bool $ogImageProvided = false,
        public bool $validImageUrl = false,
        public bool $relativeImageUrl = false,
        public bool $conflictingValuesDetected = false,
        array $ruleIdentifiers = [],
    ) {
        $this->ruleIdentifiers = $ruleIdentifiers;
    }
}
