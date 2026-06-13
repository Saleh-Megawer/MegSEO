<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Checks\Title\TitleCheck;
use MegSEO\Checks\Title\Support\TitleLengthPolicy;
use MegSEO\Contracts\RegistersChecks;

final readonly class TitleCheckRegistration
{
    /**
     * Resolves configuration values at the Laravel integration boundary
     * and registers the Title Check through the existing MegSEO mechanism.
     * Contains no business logic.
     *
     * @param array<string, mixed> $titleConfig The 'megseo.title' config section
     */
    public static function register(RegistersChecks $registry, array $titleConfig = []): void
    {
        $minLength = isset($titleConfig['min_length']) ? (int) $titleConfig['min_length'] : 30;
        $maxLength = isset($titleConfig['max_length']) ? (int) $titleConfig['max_length'] : 60;
        $shortThreshold = isset($titleConfig['short_threshold']) ? (int) $titleConfig['short_threshold'] : 20;
        $longThreshold = isset($titleConfig['long_threshold']) ? (int) $titleConfig['long_threshold'] : 70;

        $lengthPolicy = new TitleLengthPolicy(
            minLength: $minLength,
            maxLength: $maxLength,
            shortThreshold: $shortThreshold,
            longThreshold: $longThreshold,
        );

        $check = new TitleCheck(lengthPolicy: $lengthPolicy);

        $registry->register($check);
    }
}
