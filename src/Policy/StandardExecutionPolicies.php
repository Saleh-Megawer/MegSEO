<?php

declare(strict_types=1);

namespace MegSEO\Policy;

use MegSEO\Contracts\ExecutionPolicy;

final class StandardExecutionPolicies
{
    public static function failFast(): ExecutionPolicy
    {
        return new FailFastExecutionPolicy();
    }

    public static function isolateFailures(): ExecutionPolicy
    {
        return new IsolateFailuresExecutionPolicy();
    }
}
