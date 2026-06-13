<?php

declare(strict_types=1);

namespace MegSEO\Support;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements ArrayAccess<string, mixed>
 * @implements IteratorAggregate<string, mixed>
 */
final readonly class ImmutableMap implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array<string, mixed> */
    private array $items;

    /** @param array<string, mixed> $items */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('ImmutableMap is read-only.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('ImmutableMap is read-only.');
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        yield from $this->items;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->items;
    }
}
