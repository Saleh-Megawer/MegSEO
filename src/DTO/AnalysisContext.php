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

    public function __construct(
        public mixed $subject,
        array $attributes = [],
        array $options = [],
        public ?string $requestId = null,
    ) {
        $this->attributes = new ImmutableMap($attributes);
        $this->options = $options;
    }
}
