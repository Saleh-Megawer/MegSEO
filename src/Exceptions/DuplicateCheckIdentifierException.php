<?php

declare(strict_types=1);

namespace MegSEO\Exceptions;

use MegSEO\Contracts\Check;

final class DuplicateCheckIdentifierException extends \RuntimeException
{
    public readonly string $identifier;

    public function __construct(Check $check, int $code = 0, ?\Throwable $previous = null)
    {
        $this->identifier = $check->ref()->id;

        parent::__construct(
            message: sprintf(
                'Duplicate check identifier "%s". A check with this identifier is already registered. '
                . 'Each registered check must have a unique, stable identifier.',
                $this->identifier,
            ),
            code: $code,
            previous: $previous,
        );
    }
}
