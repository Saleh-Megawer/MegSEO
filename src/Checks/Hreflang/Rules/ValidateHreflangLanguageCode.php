<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang\Rules;

use MegSEO\DTO\AnalysisWarning;

final readonly class ValidateHreflangLanguageCode
{
    private const LANG_PATTERN = '/^[A-Za-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/';

    public function evaluate(?string $code, bool $isEmptyDetected): ?AnalysisWarning
    {
        if ($isEmptyDetected || $code === null || $code === '') return null;
        if ($code === 'x-default') return null;
        if (preg_match(self::LANG_PATTERN, $code)) return null;

        return new AnalysisWarning('Invalid hreflang language code', "The language code \"{$code}\" is not a valid hreflang value. Use valid language codes like en, en-US, zh-Hans, or x-default.", 'seo.hreflang');
    }
}
