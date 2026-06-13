<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

interface RegistersChecks
{
    public function register(Check $check): void;

    /**
     * @return array<int, Check>
     */
    public function all(): array;

    public function count(): int;
}
