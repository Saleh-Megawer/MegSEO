<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription\Normalization;

use MegSEO\Checks\MetaDescription\Contracts\MetaDescriptionNormalizer;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionNormalizationResult;
use Normalizer;

final class DeterministicMetaDescriptionNormalizer implements MetaDescriptionNormalizer
{
    public function normalize(?string $rawDescription, ?string $focusKeyword = null): MetaDescriptionNormalizationResult
    {
        $flags = [];

        if ($rawDescription === null) {
            return new MetaDescriptionNormalizationResult(null, null, null, ['action' => 'skip-null']);
        }

        $normalized = $this->applyNormalization($rawDescription, $flags);

        $normalizedKeyword = null;
        if ($focusKeyword !== null && $focusKeyword !== '') {
            $normalizedKeyword = $this->applyNormalization($focusKeyword, $flags);
        }

        return new MetaDescriptionNormalizationResult(
            rawDescription: $rawDescription,
            normalizedDescription: $normalized,
            normalizedFocusKeyword: $normalizedKeyword,
            flags: $flags,
        );
    }

    private function applyNormalization(string $value, array &$flags): string
    {
        $normalized = trim($value);
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
