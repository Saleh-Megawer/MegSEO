<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

interface ConfiguresExecutionPolicy
{
    public function configure(ExecutionPolicy $policy): void;
}
