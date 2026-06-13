<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\DTO;

final readonly class TitleNormalizationResult
{
    /** @var array<string, mixed> */
    public array $flags;

    public function __construct(
        public ?string $rawTitle,
        public ?string $normalizedTitle,
        public ?string $normalizedFocusKeyword = null,
        array $flags = [],
    ) {
        $this->flags = $flags;
    }

    public function isNormalized(): bool
    {
        return $this->rawTitle !== $this->normalizedTitle;
    }
}
