<?php

declare(strict_types=1);

namespace MegSEO\DTO;

use MegSEO\Support\ImmutableMap;

final readonly class AnalysisContext
{
    /** @var ImmutableMap */
    public ImmutableMap $attributes;

    /** @var array<string, mixed> */
    public array $options;

    /** @var array<string, mixed> */
    public array $inputs;

    public function __construct(
        public mixed $subject,
        array $attributes = [],
        array $options = [],
        public ?string $requestId = null,
        array $inputs = [],
    ) {
        $this->attributes = new ImmutableMap($attributes);
        $this->options = $options;
        $this->inputs = $inputs;
    }

    public function inputFor(string $identifier): mixed
    {
        return $this->inputs[$identifier] ?? $this->subject;
    }

    public function hasInputFor(string $identifier): bool
    {
        return array_key_exists($identifier, $this->inputs);
    }

    public function withSubject(mixed $subject): self
    {
        return new self(
            subject: $subject,
            attributes: $this->attributes->toArray(),
            options: $this->options,
            requestId: $this->requestId,
            inputs: $this->inputs,
        );
    }
}
