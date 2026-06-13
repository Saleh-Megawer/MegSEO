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
use MegSEO\Checks\Title\Rules\DetectMissingTitle;
use MegSEO\Checks\Title\Rules\DetectEmptyTitle;
use MegSEO\Checks\Title\Rules\DetectSeparatorOnlyTitle;
use MegSEO\Checks\Title\Rules\EvaluateTitleLength;
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
            if ($issue !== null) {
                $issues[] = $issue;
            }

            $lengthRule = new EvaluateTitleLength($this->lengthPolicy);
            $warning = $lengthRule->evaluate($normalized);
            if ($warning !== null) {
                $warnings[] = $warning;
            }
        }

        $metadata = new TitleCheckMetadata(
            checkIdentifier: $this->ref->id,
            rawTitle: $normalized->rawTitle,
            normalizedTitle: $normalized->normalizedTitle,
            normalizedLength: $normalized->normalizedTitle !== null ? mb_strlen($normalized->normalizedTitle, 'UTF-8') : 0,
            focusKeywordSupplied: $input->hasFocusKeyword(),
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        return new CheckOutcome(
            check: $this->ref,
            issues: $issues,
            warnings: $warnings,
            metadata: [
                'checkIdentifier' => $metadata->checkIdentifier,
                'ruleIdentifiers' => $metadata->ruleIdentifiers,
                'normalizedLength' => $metadata->normalizedLength,
                'duplicateSupportUsed' => $metadata->duplicateSupportUsed,
                'focusKeywordSupplied' => $metadata->focusKeywordSupplied,
            ],
        );
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
