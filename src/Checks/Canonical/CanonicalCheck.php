<?php

declare(strict_types=1);

namespace MegSEO\Checks\Canonical;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Checks\Canonical\Contracts\CanonicalUrlNormalizer;
use MegSEO\Checks\Canonical\DTO\CanonicalCheckInput;
use MegSEO\Checks\Canonical\DTO\CanonicalCheckMetadata;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlMatchReport;
use MegSEO\Checks\Canonical\DTO\CanonicalUrlNormalizationResult;
use MegSEO\Checks\Canonical\Normalization\DeterministicCanonicalUrlNormalizer;
use MegSEO\Checks\Canonical\Rules\DetectMissingCanonical;
use MegSEO\Checks\Canonical\Rules\DetectEmptyCanonical;
use MegSEO\Checks\Canonical\Rules\DetectInvalidCanonicalUrl;
use MegSEO\Checks\Canonical\Rules\DetectMultipleCanonicals;
use MegSEO\Checks\Canonical\Rules\EvaluateSelfReferencingCanonical;
use MegSEO\Checks\Canonical\Rules\EvaluateRelativeCanonicalUrl;
use MegSEO\Checks\Canonical\Rules\EvaluateCrossDomainCanonical;
use MegSEO\Checks\Canonical\Scoring\CanonicalScoreContributionBuilder;
use MegSEO\Checks\Canonical\Support\CanonicalUrlValidator;

final class CanonicalCheck implements Check
{
    private CheckReference $ref;

    /** @var array<int, string> */
    private array $ruleIdentifiers = [
        'detect-missing-canonical',
        'detect-empty-canonical',
        'detect-invalid-canonical-url',
        'detect-multiple-canonicals',
        'evaluate-self-referencing-canonical',
        'evaluate-relative-canonical-url',
        'evaluate-cross-domain-canonical',
    ];

    public function __construct(
        private CanonicalUrlNormalizer $normalizer = new DeterministicCanonicalUrlNormalizer(),
    ) {
        $this->ref = new CheckReference(
            id: 'seo.canonical',
            label: 'Canonical Check',
            version: '1.0.0',
        );
    }

    public function ref(): CheckReference
    {
        return $this->ref;
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        $input = $this->buildInput($context);
        $normalized = $this->normalizer->normalize($input->canonical, $input->pageUrl);

        $issues = [];
        $warnings = [];
        $suggestions = [];

        $missingRule = new DetectMissingCanonical();
        $issue = $missingRule->evaluate($normalized);
        if ($issue !== null) {
            $issues[] = $issue;
        }

        if ($normalized->normalizedCanonical !== null) {
            $emptyRule = new DetectEmptyCanonical();
            $issue = $emptyRule->evaluate($normalized);
            if ($issue !== null) {
                $issues[] = $issue;
            }

            $invalidRule = new DetectInvalidCanonicalUrl();
            $issue = $invalidRule->evaluate($normalized);
            if ($issue !== null) {
                $issues[] = $issue;
            }
        }

        $multipleRule = new DetectMultipleCanonicals();
        $issue = $multipleRule->evaluate($input);
        if ($issue !== null) {
            $issues[] = $issue;
        }

        $matchReport = $this->buildMatchReport($normalized, $input);

        $relativeRule = new EvaluateRelativeCanonicalUrl();
        $warning = $relativeRule->evaluate($matchReport);
        if ($warning !== null) {
            $warnings[] = $warning;
        }

        $crossDomainRule = new EvaluateCrossDomainCanonical();
        $suggestion = $crossDomainRule->evaluate($matchReport);
        if ($suggestion !== null) {
            $suggestions[] = $suggestion;
        }

        $selfRefRule = new EvaluateSelfReferencingCanonical();
        $suggestion = $selfRefRule->evaluate($matchReport);
        if ($suggestion !== null) {
            $suggestions[] = $suggestion;
        }

        $metadata = new CanonicalCheckMetadata(
            checkIdentifier: $this->ref->id,
            isSelfReferencing: $matchReport->isSelfReferencing,
            isCrossDomain: $matchReport->isCrossDomain,
            isRelative: $matchReport->isRelative,
            multipleCanonicalsDetected: $input->hasMultipleCanonicals(),
            normalizationApplied: ($normalized->normalizedCanonical !== $normalized->rawCanonical),
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        $scoringBuilder = new CanonicalScoreContributionBuilder();
        $scoreContribution = $scoringBuilder->build($issues, $warnings, $suggestions);

        return new CheckOutcome(
            check: $this->ref,
            scoreContribution: $scoreContribution,
            issues: $issues,
            warnings: $warnings,
            suggestions: $suggestions,
            metadata: [
                'checkIdentifier' => $metadata->checkIdentifier,
                'ruleIdentifiers' => $metadata->ruleIdentifiers,
                'isSelfReferencing' => $metadata->isSelfReferencing,
                'isCrossDomain' => $metadata->isCrossDomain,
                'isRelative' => $metadata->isRelative,
                'multipleCanonicalsDetected' => $metadata->multipleCanonicalsDetected,
                'normalizationApplied' => $metadata->normalizationApplied,
                'hasPageUrl' => $matchReport->hasPageUrl,
                'canonicalCount' => count($input->canonicalUrls),
            ],
        );
    }

    private function buildMatchReport(CanonicalUrlNormalizationResult $normalized, CanonicalCheckInput $input): CanonicalUrlMatchReport
    {
        $canonical = $normalized->normalizedCanonical;
        $pageUrl = $normalized->normalizedPageUrl;

        $isRelative = false;
        $isCrossDomain = false;
        $isSelfReferencing = false;

        if ($canonical !== null && $canonical !== '') {
            $validator = new CanonicalUrlValidator();
            $isRelative = $validator->isRelative($canonical);
        }

        if ($canonical !== null && $canonical !== '' && $pageUrl !== null && $pageUrl !== '' && ! $isRelative) {
            $canonicalHost = parse_url($canonical, PHP_URL_HOST);
            $pageHost = parse_url($pageUrl, PHP_URL_HOST);

            if ($canonicalHost !== false && $pageHost !== false) {
                $isCrossDomain = strtolower((string) $canonicalHost) !== strtolower((string) $pageHost);
                $isSelfReferencing = ! $isCrossDomain && $canonical === $pageUrl;
            }
        }

        return new CanonicalUrlMatchReport(
            isSelfReferencing: $isSelfReferencing,
            isCrossDomain: $isCrossDomain,
            isRelative: $isRelative,
            hasPageUrl: $pageUrl !== null && $pageUrl !== '',
        );
    }

    private function buildInput(AnalysisContext $context): CanonicalCheckInput
    {
        $canonical = null;
        $canonicalUrls = [];

        if ($context->subject !== null) {
            if (is_string($context->subject)) {
                $canonical = $context->subject;
                $canonicalUrls = [$context->subject];
            } elseif (is_array($context->subject)) {
                if (isset($context->subject['canonical'])) {
                    $canonical = (string) $context->subject['canonical'];
                }
                if (isset($context->subject['canonicals']) && is_array($context->subject['canonicals'])) {
                    $canonicalUrls = array_map('strval', $context->subject['canonicals']);
                    if ($canonical === null && $canonicalUrls !== []) {
                        $canonical = $canonicalUrls[0];
                    }
                } elseif ($canonical !== null) {
                    $canonicalUrls = [$canonical];
                }
            }
        }

        $pageUrl = null;
        if ($context->attributes->has('page_url')) {
            $pageUrl = $context->attributes->get('page_url');
        }

        return new CanonicalCheckInput(
            canonical: $canonical,
            canonicalUrls: $canonicalUrls,
            pageUrl: $pageUrl,
        );
    }
}
