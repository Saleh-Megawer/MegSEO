<?php

declare(strict_types=1);

namespace MegSEO\Checks\Title;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Checks\Title\Contracts\TitleNormalizer;
use MegSEO\Checks\Title\DTO\TitleCheckInput;
use MegSEO\Checks\Title\DTO\TitleCheckMetadata;
use MegSEO\Checks\Title\Normalization\DeterministicTitleNormalizer;
use MegSEO\Checks\Title\DTO\TitleDuplicateMatch;
use MegSEO\Checks\Title\Rules\DetectMissingTitle;
use MegSEO\Checks\Title\Rules\DetectEmptyTitle;
use MegSEO\Checks\Title\Rules\DetectSeparatorOnlyTitle;
use MegSEO\Checks\Title\Rules\EvaluateTitleLength;
use MegSEO\Checks\Title\Rules\EvaluateFocusKeywordPresence;
use MegSEO\Checks\Title\Rules\EvaluateDuplicateTitleSupport;
use MegSEO\Checks\Title\Scoring\TitleScoreContributionBuilder;
use MegSEO\Checks\Title\Support\TitleLengthPolicy;

final class TitleCheck implements Check
{
    private CheckReference $ref;

    /** @var array<int, string> */
    private array $ruleIdentifiers = [
        'detect-missing-title',
        'detect-empty-title',
        'detect-separator-only-title',
        'evaluate-title-length',
        'evaluate-focus-keyword-presence',
        'evaluate-duplicate-title-support',
    ];

    public function __construct(
        private TitleNormalizer $normalizer = new DeterministicTitleNormalizer(),
        private TitleLengthPolicy $lengthPolicy = new TitleLengthPolicy(30, 60, 20, 70),
    ) {
        $this->ref = new CheckReference(
            id: 'seo.title',
            label: 'Title Check',
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

        $normalized = $this->normalizer->normalize($input->title, $input->focusKeyword);

        $issues = [];
        $warnings = [];
        $suggestions = [];
        $duplicateSupportUsed = false;

        $missingRule = new DetectMissingTitle();
        $issue = $missingRule->evaluate($normalized);
        if ($issue !== null) {
            $issues[] = $issue;
        }

        if ($normalized->normalizedTitle !== null) {
            $emptyRule = new DetectEmptyTitle();
            $issue = $emptyRule->evaluate($normalized);
            if ($issue !== null) {
                $issues[] = $issue;
            }

            $separatorRule = new DetectSeparatorOnlyTitle();
            $issue = $separatorRule->evaluate($normalized);
            $hasSeparatorIssue = $issue !== null;
            if ($hasSeparatorIssue) {
                $issues[] = $issue;
            }

            if (! $hasSeparatorIssue) {
                $lengthRule = new EvaluateTitleLength($this->lengthPolicy);
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
                $duplicateRule = new EvaluateDuplicateTitleSupport();
                $suggestion = $duplicateRule->evaluate($normalized->normalizedTitle, $duplicateMatches);
                if ($suggestion !== null) {
                    $suggestions[] = $suggestion;
                }
            }
        }

        $metadata = new TitleCheckMetadata(
            checkIdentifier: $this->ref->id,
            rawTitle: $normalized->rawTitle,
            normalizedTitle: $normalized->normalizedTitle,
            normalizedLength: $normalized->normalizedTitle !== null ? mb_strlen($normalized->normalizedTitle, 'UTF-8') : 0,
            duplicateSupportUsed: $duplicateSupportUsed,
            focusKeywordSupplied: $input->hasFocusKeyword(),
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        $scoringBuilder = new TitleScoreContributionBuilder();
        $scoreContribution = $scoringBuilder->build($issues, $warnings, $suggestions, [
            'normalizedLength' => $metadata->normalizedLength,
            'focusKeywordSupplied' => $metadata->focusKeywordSupplied,
            'duplicateSupportUsed' => $metadata->duplicateSupportUsed,
        ]);

        $containsFocusKeyword = false;
        if ($input->hasFocusKeyword() && $normalized->normalizedTitle !== null && $normalized->normalizedFocusKeyword !== null) {
            $containsFocusKeyword = mb_stripos($normalized->normalizedTitle, $normalized->normalizedFocusKeyword) !== false;
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
                'normalizationApplied' => ($normalized->normalizedTitle !== $normalized->rawTitle),
            ],
        );
    }

    private function buildDuplicateMatches(TitleCheckInput $input): array
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

            if ($title !== '' && $input->title !== null) {
                $normalizedInput = $this->normalizer->normalize($title);
                $normalizedCurrent = $this->normalizer->normalize($input->title);

                if ($normalizedInput->normalizedTitle === $normalizedCurrent->normalizedTitle) {
                    $matches[] = new TitleDuplicateMatch(
                        matchedTitle: $title,
                        matchedReference: isset($entry['reference']) ? (string) $entry['reference'] : '',
                        matchReason: 'exact',
                    );
                }
            }
        }

        return $matches;
    }

    private function buildInput(AnalysisContext $context): TitleCheckInput
    {
        $title = null;
        if ($context->subject !== null) {
            if (is_string($context->subject)) {
                $title = $context->subject;
            } elseif (is_array($context->subject) && isset($context->subject['title'])) {
                $title = (string) $context->subject['title'];
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

        return new TitleCheckInput(
            title: $title,
            focusKeyword: $focusKeyword,
            duplicateSupportData: $duplicateSupportData,
            attributes: [],
        );
    }
}
