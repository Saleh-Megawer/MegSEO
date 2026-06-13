<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Contracts;

use MegSEO\Checks\Title\DTO\TitleDuplicateMatch;

interface SupportsDuplicateTitles
{
    public function duplicateDataAvailable(): bool;

    /**
     * @return array<int, TitleDuplicateMatch>
     */
    public function getDuplicateMatches(): array;
}
