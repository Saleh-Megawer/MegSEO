<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical\DTO;

final readonly class CanonicalUrlNormalizationResult
{
    /** @var array<string, mixed> */
    public array $flags;

    public function __construct(
        public ?string $rawCanonical,
        public ?string $normalizedCanonical,
        public ?string $rawPageUrl = null,
        public ?string $normalizedPageUrl = null,
        array $flags = [],
    ) {
        $this->flags = $flags;
    }
}
