<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Support;

final readonly class MetaDescriptionLengthPolicy
{
    public function __construct(
        public int $minLength,
        public int $maxLength,
        public int $shortThreshold,
        public int $longThreshold,
    ) {}

    public function isShort(int $length): bool
    {
        return $length < $this->shortThreshold;
    }

    public function isLong(int $length): bool
    {
        return $length > $this->longThreshold;
    }

    public function getRecommendedMin(): int
    {
        return $this->minLength;
    }

    public function getRecommendedMax(): int
    {
        return $this->maxLength;
    }
}
