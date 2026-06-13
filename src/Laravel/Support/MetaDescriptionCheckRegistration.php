<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Checks\MetaDescription\MetaDescriptionCheck;
use MegSEO\Checks\MetaDescription\Support\MetaDescriptionLengthPolicy;
use MegSEO\Contracts\RegistersChecks;

final readonly class MetaDescriptionCheckRegistration
{
    /**
     * @param array<string, mixed> $descriptionConfig The 'megseo.meta_description' config section
     */
    public static function register(RegistersChecks $registry, array $descriptionConfig = []): void
    {
        $minLength = isset($descriptionConfig['min_length']) ? (int) $descriptionConfig['min_length'] : 120;
        $maxLength = isset($descriptionConfig['max_length']) ? (int) $descriptionConfig['max_length'] : 160;
        $shortThreshold = isset($descriptionConfig['short_threshold']) ? (int) $descriptionConfig['short_threshold'] : 80;
        $longThreshold = isset($descriptionConfig['long_threshold']) ? (int) $descriptionConfig['long_threshold'] : 170;

        $lengthPolicy = new MetaDescriptionLengthPolicy(
            minLength: $minLength,
            maxLength: $maxLength,
            shortThreshold: $shortThreshold,
            longThreshold: $longThreshold,
        );

        $check = new MetaDescriptionCheck(lengthPolicy: $lengthPolicy);

        $registry->register($check);
    }
}
