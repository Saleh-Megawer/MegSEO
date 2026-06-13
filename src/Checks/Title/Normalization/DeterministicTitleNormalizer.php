<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title\Normalization;

use MegSEO\Checks\Title\Contracts\TitleNormalizer;
use MegSEO\Checks\Title\DTO\TitleNormalizationResult;
use Normalizer;

final class DeterministicTitleNormalizer implements TitleNormalizer
{
    public function normalize(?string $rawTitle, ?string $focusKeyword = null): TitleNormalizationResult
    {
        $flags = [];

        if ($rawTitle === null) {
            return new TitleNormalizationResult(null, null, null, ['action' => 'skip-null']);
        }

        $normalized = $this->applyNormalization($rawTitle, $flags);

        $normalizedKeyword = null;
        if ($focusKeyword !== null && $focusKeyword !== '') {
            $normalizedKeyword = $this->applyNormalization($focusKeyword, $flags);
        }

        return new TitleNormalizationResult(
            rawTitle: $rawTitle,
            normalizedTitle: $normalized,
            normalizedFocusKeyword: $normalizedKeyword,
            flags: $flags,
        );
    }

    private function applyNormalization(string $value, array &$flags): string
    {
        $normalized = $value;

        $normalized = trim($normalized);

        $normalized = (string) preg_replace('/\s+/u', ' ', $normalized);

        $normalized = (string) preg_replace('/[\x{200B}-\x{200F}\x{2028}\x{2029}\x{FEFF}]/u', '', $normalized);

        if (class_exists(Normalizer::class)) {
            $beforeNfkc = $normalized;
            $normalized = Normalizer::normalize($normalized, Normalizer::NFKC);
            if ($normalized === null) {
                $normalized = $beforeNfkc;
            }
            if ($beforeNfkc !== $normalized) {
                $flags['nfkc_applied'] = true;
            }
        }

        return $normalized;
    }
}
