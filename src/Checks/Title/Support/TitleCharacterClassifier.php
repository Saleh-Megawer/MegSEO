<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Support;

final readonly class TitleCharacterClassifier
{
    private const SEPARATOR_PATTERN = '/[\p{P}\p{Z}\p{C}\p{S}]/u';

    public function containsMeaningfulText(string $text): bool
    {
        $stripped = (string) preg_replace(self::SEPARATOR_PATTERN, '', $text);

        return $stripped !== '' && $this->hasAnyLetter($stripped);
    }

    public function isPunctuationOnly(string $text): bool
    {
        $stripped = (string) preg_replace('/[\s]+/u', '', $text);
        if ($stripped === '') {
            return false;
        }

        return (bool) preg_match('/^[\p{P}]+$/u', $stripped);
    }

    private function hasAnyLetter(string $text): bool
    {
        return (bool) preg_match('/\p{L}/u', $text);
    }
}
