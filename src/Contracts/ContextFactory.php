<?php

declare(strict_types=1);

namespace MegSEO\Contracts;

use MegSEO\DTO\AnalysisContext;

interface ContextFactory
{
    public function create(mixed $subject, array $attributes = [], array $options = [], ?string $requestId = null): AnalysisContext;
}
