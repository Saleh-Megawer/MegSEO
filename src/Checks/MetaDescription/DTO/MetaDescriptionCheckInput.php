<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\DTO;

final readonly class MetaDescriptionCheckInput
{
    /** @var array<string, mixed> */
    public array $duplicateSupportData;

    /** @var array<string, mixed> */
    public array $attributes;

    public function __construct(
        public ?string $description,
        public ?string $focusKeyword = null,
        array $duplicateSupportData = [],
        array $attributes = [],
    ) {
        $this->duplicateSupportData = $duplicateSupportData;
        $this->attributes = $attributes;
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }

    public function hasFocusKeyword(): bool
    {
        return $this->focusKeyword !== null && $this->focusKeyword !== '';
    }

    public function hasDuplicateSupportData(): bool
    {
        return $this->duplicateSupportData !== [];
    }
}
