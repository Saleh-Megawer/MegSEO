<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\DTO;

final readonly class MetaDescriptionNormalizationResult
{
    /** @var array<string, mixed> */
    public array $flags;

    public function __construct(
        public ?string $rawDescription,
        public ?string $normalizedDescription,
        public ?string $normalizedFocusKeyword = null,
        array $flags = [],
    ) {
        $this->flags = $flags;
    }

    public function isNormalized(): bool
    {
        return $this->rawDescription !== $this->normalizedDescription;
    }
}
