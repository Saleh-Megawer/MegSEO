<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Checks\TwitterCard\TwitterCardCheck;
use MegSEO\Contracts\RegistersChecks;

final readonly class TwitterCardCheckRegistration
{
    public static function register(RegistersChecks $registry, array $config = []): void
    {
        $registry->register(new TwitterCardCheck());
    }
}
