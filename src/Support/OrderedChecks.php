<?php

declare(strict_types=1);

namespace MegSEO\Support;

use Countable;
use IteratorAggregate;
use MegSEO\Contracts\Check;
use MegSEO\Exceptions\DuplicateCheckIdentifierException;
use Traversable;

/**
 * @implements IteratorAggregate<int, Check>
 */
final class OrderedChecks implements Countable, IteratorAggregate
{
    /** @var array<string, array{index: int, check: Check}> */
    private array $checks = [];

    private int $nextIndex = 0;

    public function add(Check $check): void
    {
        $id = $check->ref()->id;

        if (isset($this->checks[$id])) {
            throw new DuplicateCheckIdentifierException($check);
        }

        $this->checks[$id] = [
            'index' => $this->nextIndex++,
            'check' => $check,
        ];
    }

    public function remove(string $id): void
    {
        unset($this->checks[$id]);
    }

    public function has(string $id): bool
    {
        return isset($this->checks[$id]);
    }

    /** @return array<int, Check> */
    public function all(): array
    {
        $items = array_values($this->checks);

        usort($items, fn (array $a, array $b): int => $a['index'] <=> $b['index']);

        return array_map(fn (array $item): Check => $item['check'], $items);
    }

    public function count(): int
    {
        return count($this->checks);
    }

    public function getIterator(): Traversable
    {
        yield from $this->all();
    }
}
