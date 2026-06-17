<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Checks\Hreflang\HreflangCheck;
use MegSEO\Contracts\RegistersChecks;

final readonly class HreflangCheckRegistration
{
    public static function register(RegistersChecks $registry, array $config = []): void
    {
        $registry->register(new HreflangCheck());
    }
}
