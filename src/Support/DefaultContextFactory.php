<?php

declare(strict_types=1);

namespace MegSEO\Support;

use MegSEO\Contracts\ContextFactory;
use MegSEO\DTO\AnalysisContext;

final class DefaultContextFactory implements ContextFactory
{
    public function create(
        mixed $subject,
        array $attributes = [],
        array $options = [],
        ?string $requestId = null,
    ): AnalysisContext {
        return new AnalysisContext(
            subject: $subject,
            attributes: $attributes,
            options: $options,
            requestId: $requestId,
        );
    }
}
