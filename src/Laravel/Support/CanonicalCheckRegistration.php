<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Checks\Canonical\CanonicalCheck;
use MegSEO\Contracts\RegistersChecks;

final readonly class CanonicalCheckRegistration
{
    /**
     * @param array<string, mixed> $canonicalConfig The 'megseo.canonical' config section
     */
    public static function register(RegistersChecks $registry, array $canonicalConfig = []): void
    {
        $check = new CanonicalCheck();

        $registry->register($check);
    }
}
