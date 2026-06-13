<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Contracts;

use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionDuplicateMatch;

interface SupportsDuplicateDescriptions
{
    public function duplicateDataAvailable(): bool;

    /**
     * @return array<int, MetaDescriptionDuplicateMatch>
     */
    public function getDuplicateMatches(): array;
}
