<?php

declare(strict_types=1);

namespace MegSEO\Checks\Hreflang;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckInput;
use MegSEO\Checks\Hreflang\DTO\HreflangCheckMetadata;
use MegSEO\Checks\Hreflang\Rules\DetectMissingHreflang;
use MegSEO\Checks\Hreflang\Rules\DetectEmptyHreflangValues;
use MegSEO\Checks\Hreflang\Rules\ValidateHreflangLanguageCode;
use MegSEO\Checks\Hreflang\Rules\ValidateHreflangUrl;
use MegSEO\Checks\Hreflang\Rules\DetectMissingXDefault;
use MegSEO\Checks\Hreflang\Rules\EvaluateSelfReferencingHreflang;
use MegSEO\Checks\Hreflang\Rules\DetectConflictingHreflangEntries;
use MegSEO\Checks\Hreflang\Scoring\HreflangScoreContributionBuilder;

final class HreflangCheck implements Check
{
    private CheckReference $ref;
    private array $ruleIdentifiers = [
        'detect-missing-hreflang',
        'detect-empty-hreflang-values',
        'validate-hreflang-language-code',
        'validate-hreflang-url',
        'detect-missing-x-default',
        'evaluate-self-referencing-hreflang',
        'detect-conflicting-hreflang-entries',
    ];

    public function __construct()
    {
        $this->ref = new CheckReference(id: 'seo.hreflang', label: 'Hreflang Check', version: '1.0.0');
    }

    public function ref(): CheckReference { return $this->ref; }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        $input = $this->buildInput($context);
        $issues = [];
        $warnings = [];
        $suggestions = [];

        $missingRule = new DetectMissingHreflang();
        $issue = $missingRule->evaluate($input);
        if ($issue !== null) $issues[] = $issue;

        if ($input->hasEntries()) {
            $emptyRule = new DetectEmptyHreflangValues();
            foreach ($emptyRule->evaluate($input) as $i) $issues[] = $i;

            $langRule = new ValidateHreflangLanguageCode();
            $urlRule = new ValidateHreflangUrl();
            foreach ($input->getEntries() as $idx => $e) {
                $w = $langRule->evaluate($input->getHreflang($idx), $input->isHreflangEmpty($idx));
                if ($w !== null) $warnings[] = $w;
                $w = $urlRule->evaluate($input->getHref($idx), $input->isHrefEmpty($idx));
                if ($w !== null) $warnings[] = $w;
            }

            // US2: x-default, self-referencing, conflicts
            $xDefRule = new DetectMissingXDefault();
            $s = $xDefRule->evaluate($input);
            if ($s !== null) $suggestions[] = $s;

            $pageUrl = $context->attributes->has('page_url') ? $context->attributes->get('page_url') : null;
            $pageLang = $context->attributes->has('page_language') ? $context->attributes->get('page_language') : null;
            $selfRefRule = new EvaluateSelfReferencingHreflang();
            foreach ($selfRefRule->evaluate($input, $pageUrl, $pageLang) as $s) $suggestions[] = $s;

            $conflictRule = new DetectConflictingHreflangEntries();
            foreach ($conflictRule->evaluate($input) as $f) {
                if ($f instanceof \MegSEO\DTO\AnalysisSuggestion) $suggestions[] = $f;
                else $warnings[] = $f;
            }
        }

        $metadata = new HreflangCheckMetadata(
            checkIdentifier: $this->ref->id,
            entryCount: $input->entryCount(),
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        $scoringBuilder = new HreflangScoreContributionBuilder();
        $scoreContribution = $scoringBuilder->build($issues, $warnings, $suggestions);

        return new CheckOutcome(check: $this->ref, scoreContribution: $scoreContribution, issues: $issues, warnings: $warnings, suggestions: $suggestions, metadata: [
            'checkIdentifier' => $metadata->checkIdentifier,
            'ruleIdentifiers' => $metadata->ruleIdentifiers,
            'entryCount' => $metadata->entryCount,
        ]);
    }

    private function buildInput(AnalysisContext $context): HreflangCheckInput
    {
        if ($context->subject === null) return new HreflangCheckInput([]);
        if (is_array($context->subject)) return new HreflangCheckInput($context->subject);
        return new HreflangCheckInput([]);
    }
}
