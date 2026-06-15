<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Checks\OpenGraph\OpenGraphCheck;
use MegSEO\Contracts\RegistersChecks;

final readonly class OpenGraphCheckRegistration
{
    public static function register(RegistersChecks $registry, array $config = []): void
    {
        $registry->register(new OpenGraphCheck());
    }
}
