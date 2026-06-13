<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\DTO;

final readonly class TitleCheckInput
{
    /** @var array<string, mixed> */
    public array $duplicateSupportData;

    /** @var array<string, mixed> */
    public array $attributes;

    public function __construct(
        public ?string $title,
        public ?string $focusKeyword = null,
        array $duplicateSupportData = [],
        array $attributes = [],
    ) {
        $this->duplicateSupportData = $duplicateSupportData;
        $this->attributes = $attributes;
    }

    public function hasTitle(): bool
    {
        return $this->title !== null;
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
