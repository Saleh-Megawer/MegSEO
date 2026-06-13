<?php

declare(strict_types=1);

namespace MegSEO\Checks\MetaDescription;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Checks\MetaDescription\Contracts\MetaDescriptionNormalizer;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionCheckInput;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionCheckMetadata;
use MegSEO\Checks\MetaDescription\DTO\MetaDescriptionDuplicateMatch;
use MegSEO\Checks\MetaDescription\Normalization\DeterministicMetaDescriptionNormalizer;
use MegSEO\Checks\MetaDescription\Rules\DetectMissingMetaDescription;
use MegSEO\Checks\MetaDescription\Rules\DetectEmptyMetaDescription;
use MegSEO\Checks\MetaDescription\Rules\DetectSeparatorOnlyMetaDescription;
use MegSEO\Checks\MetaDescription\Rules\EvaluateMetaDescriptionLength;
use MegSEO\Checks\MetaDescription\Rules\EvaluateFocusKeywordPresence;
use MegSEO\Checks\MetaDescription\Rules\EvaluateDuplicateMetaDescriptionSupport;
use MegSEO\Checks\MetaDescription\Scoring\MetaDescriptionScoreContributionBuilder;
use MegSEO\Checks\MetaDescription\Support\MetaDescriptionLengthPolicy;

final class MetaDescriptionCheck implements Check
{
    private CheckReference $ref;

    /** @var array<int, string> */
    private array $ruleIdentifiers = [
        'detect-missing-meta-description',
        'detect-empty-meta-description',
        'detect-separator-only-meta-description',
        'evaluate-meta-description-length',
        'evaluate-focus-keyword-presence',
        'evaluate-duplicate-meta-description-support',
    ];

    public function __construct(
        private MetaDescriptionNormalizer $normalizer = new DeterministicMetaDescriptionNormalizer(),
        private MetaDescriptionLengthPolicy $lengthPolicy = new MetaDescriptionLengthPolicy(120, 160, 80, 170),
    ) {
        $this->ref = new CheckReference(
            id: 'seo.meta_description',
            label: 'Meta Description Check',
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
        $normalized = $this->normalizer->normalize($input->description, $input->focusKeyword);

        $issues = [];
        $warnings = [];
        $suggestions = [];
        $duplicateSupportUsed = false;

        $missingRule = new DetectMissingMetaDescription();
        $issue = $missingRule->evaluate($normalized);
        if ($issue !== null) {
            $issues[] = $issue;
        }

        if ($normalized->normalizedDescription !== null) {
            $emptyRule = new DetectEmptyMetaDescription();
            $issue = $emptyRule->evaluate($normalized);
            if ($issue !== null) {
                $issues[] = $issue;
            }

            $separatorRule = new DetectSeparatorOnlyMetaDescription();
            $issue = $separatorRule->evaluate($normalized);
            $hasSeparatorIssue = $issue !== null;
            if ($hasSeparatorIssue) {
                $issues[] = $issue;
            }

            if (! $hasSeparatorIssue) {
                $lengthRule = new EvaluateMetaDescriptionLength($this->lengthPolicy);
                $warning = $lengthRule->evaluate($normalized);
                if ($warning !== null) {
                    $warnings[] = $warning;
                }
            }

            $keywordRule = new EvaluateFocusKeywordPresence();
            $suggestion = $keywordRule->evaluate($normalized);
            if ($suggestion !== null) {
                $suggestions[] = $suggestion;
            }

            $duplicateMatches = $this->buildDuplicateMatches($input);
            if ($duplicateMatches !== []) {
                $duplicateSupportUsed = true;
                $duplicateRule = new EvaluateDuplicateMetaDescriptionSupport();
                $suggestion = $duplicateRule->evaluate($normalized->normalizedDescription, $duplicateMatches);
                if ($suggestion !== null) {
                    $suggestions[] = $suggestion;
                }
            }
        }

        $metadata = new MetaDescriptionCheckMetadata(
            checkIdentifier: $this->ref->id,
            rawDescription: $normalized->rawDescription,
            normalizedDescription: $normalized->normalizedDescription,
            normalizedLength: $normalized->normalizedDescription !== null ? mb_strlen($normalized->normalizedDescription, 'UTF-8') : 0,
            duplicateSupportUsed: $duplicateSupportUsed,
            focusKeywordSupplied: $input->hasFocusKeyword(),
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        $scoringBuilder = new MetaDescriptionScoreContributionBuilder();
        $scoreContribution = $scoringBuilder->build($issues, $warnings, $suggestions, [
            'normalizedLength' => $metadata->normalizedLength,
            'focusKeywordSupplied' => $metadata->focusKeywordSupplied,
            'duplicateSupportUsed' => $metadata->duplicateSupportUsed,
        ]);

        $containsFocusKeyword = false;
        if ($input->hasFocusKeyword() && $normalized->normalizedDescription !== null && $normalized->normalizedFocusKeyword !== null) {
            $containsFocusKeyword = mb_stripos($normalized->normalizedDescription, $normalized->normalizedFocusKeyword) !== false;
        }

        $duplicateMatchCount = 0;
        if ($duplicateSupportUsed) {
            $duplicateMatchCount = count($this->buildDuplicateMatches($input));
        }

        return new CheckOutcome(
            check: $this->ref,
            scoreContribution: $scoreContribution,
            issues: $issues,
            warnings: $warnings,
            suggestions: $suggestions,
            metadata: [
                'checkIdentifier' => $metadata->checkIdentifier,
                'ruleIdentifiers' => $metadata->ruleIdentifiers,
                'normalizedLength' => $metadata->normalizedLength,
                'duplicateSupportUsed' => $metadata->duplicateSupportUsed,
                'focusKeywordSupplied' => $metadata->focusKeywordSupplied,
                'containsFocusKeyword' => $containsFocusKeyword,
                'duplicateMatchesCount' => $duplicateMatchCount,
                'normalizationApplied' => ($normalized->normalizedDescription !== $normalized->rawDescription),
            ],
        );
    }

    private function buildDuplicateMatches(MetaDescriptionCheckInput $input): array
    {
        if (! $input->hasDuplicateSupportData()) {
            return [];
        }

        $matches = [];
        foreach ($input->duplicateSupportData as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $title = isset($entry['title']) ? (string) $entry['title'] : '';

            if ($title !== '' && $input->description !== null) {
                $normalizedInput = $this->normalizer->normalize($title);
                $normalizedCurrent = $this->normalizer->normalize($input->description);

                if ($normalizedInput->normalizedDescription === $normalizedCurrent->normalizedDescription) {
                    $matches[] = new MetaDescriptionDuplicateMatch(
                        matchedDescription: $title,
                        matchedReference: isset($entry['reference']) ? (string) $entry['reference'] : '',
                        matchReason: 'exact',
                    );
                }
            }
        }

        return $matches;
    }

    private function buildInput(AnalysisContext $context): MetaDescriptionCheckInput
    {
        $description = null;
        if ($context->subject !== null) {
            if (is_string($context->subject)) {
                $description = $context->subject;
            } elseif (is_array($context->subject) && isset($context->subject['description'])) {
                $description = (string) $context->subject['description'];
            }
        }

        $focusKeyword = null;
        if ($context->attributes->has('focus_keyword')) {
            $focusKeyword = $context->attributes->get('focus_keyword');
        }

        $duplicateSupportData = [];
        if ($context->attributes->has('duplicate_support_data')) {
            $duplicateSupportData = $context->attributes->get('duplicate_support_data') ?? [];
        }

        return new MetaDescriptionCheckInput(
            description: $description,
            focusKeyword: $focusKeyword,
            duplicateSupportData: $duplicateSupportData,
            attributes: [],
        );
    }
}
