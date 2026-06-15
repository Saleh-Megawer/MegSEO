<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard\DTO;

final readonly class TwitterCardCheckMetadata
{
    public array $ruleIdentifiers;

    public function __construct(
        public string $checkIdentifier,
        public bool $twitterCardProvided = false,
        public bool $twitterTitleProvided = false,
        public bool $twitterDescriptionProvided = false,
        public bool $twitterImageProvided = false,
        public bool $validCardType = false,
        public bool $validImageUrl = false,
        public bool $relativeImageUrl = false,
        public bool $conflictingValuesDetected = false,
        array $ruleIdentifiers = [],
    ) {
        $this->ruleIdentifiers = $ruleIdentifiers;
    }
}
