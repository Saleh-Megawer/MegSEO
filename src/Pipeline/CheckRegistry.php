<?php

declare(strict_types=1);

namespace MegSEO\Pipeline;

use MegSEO\Contracts\Check;
use MegSEO\Contracts\RegistersChecks;
use MegSEO\Support\OrderedChecks;

final class CheckRegistry implements RegistersChecks
{
    private OrderedChecks $checks;

    public function __construct()
    {
        $this->checks = new OrderedChecks();
    }

    public function register(Check $check): void
    {
        $this->checks->add($check);
    }

    /** @return array<int, Check> */
    public function all(): array
    {
        return $this->checks->all();
    }

    public function count(): int
    {
        return $this->checks->count();
    }
}
