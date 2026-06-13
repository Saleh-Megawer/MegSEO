<?php

declare(strict_types=1);

namespace MegSEO\Laravel\Support;

use MegSEO\Contracts\Check;
use MegSEO\Contracts\RegistersChecks;
use MegSEO\Exceptions\DuplicateCheckIdentifierException;

final class LaravelCheckRegistration
{
    /**
     * Register checks from configuration into the registry.
     *
     * @param RegistersChecks $registry
     * @param array<class-string<Check>> $checkClasses
     * @throws DuplicateCheckIdentifierException
     */
    public static function registerFromConfig(RegistersChecks $registry, array $checkClasses): void
    {
        foreach ($checkClasses as $class) {
            if (!is_subclass_of($class, Check::class, true)) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" must implement %s.', $class, Check::class),
                );
            }

            $registry->register(new $class());
        }
    }

    /**
     * Register individual checks into the registry.
     *
     * @param RegistersChecks $registry
     * @param array<int, Check> $checks
     */
    public static function registerChecks(RegistersChecks $registry, array $checks): void
    {
        foreach ($checks as $check) {
            $registry->register($check);
        }
    }
}
