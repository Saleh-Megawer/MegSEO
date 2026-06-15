<?php

declare(strict_types=1);

namespace MegSEO\Checks\OpenGraph;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckInput;
use MegSEO\Checks\OpenGraph\DTO\OpenGraphCheckMetadata;
use MegSEO\Checks\OpenGraph\Rules\DetectEmptyOgValues;
use MegSEO\Checks\OpenGraph\Rules\DetectMissingOgTitle;
use MegSEO\Checks\OpenGraph\Rules\DetectMissingOgDescription;
use MegSEO\Checks\OpenGraph\Rules\DetectMissingOgImage;
use MegSEO\Checks\OpenGraph\Rules\EvaluateOgImageUrl;
use MegSEO\Checks\OpenGraph\Rules\DetectConflictingOgValues;
use MegSEO\Checks\OpenGraph\Scoring\OpenGraphScoreContributionBuilder;

final class OpenGraphCheck implements Check
{
    private CheckReference $ref;

    /** @var array<int, string> */
    private array $ruleIdentifiers = [
        'detect-empty-og-values',
        'detect-missing-og-title',
        'detect-missing-og-description',
        'detect-missing-og-image',
        'evaluate-og-image-url',
        'detect-conflicting-og-values',
    ];

    public function __construct()
    {
        $this->ref = new CheckReference(
            id: 'seo.open_graph',
            label: 'Open Graph Check',
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
        $issues = [];
        $warnings = [];

        // Step 1: Detect empty values first (suppresses missing)
        $emptyRule = new DetectEmptyOgValues();
        $emptyIssues = $emptyRule->evaluate($input);
        foreach ($emptyIssues as $issue) {
            $issues[] = $issue;
        }

        // Track which keys had empty values
        $emptyTitle = $input->isEmpty('og:title');
        $emptyDesc  = $input->isEmpty('og:description');
        $emptyImage = $input->isEmpty('og:image');

        // Step 2: Missing rules — only for non-empty keys
        $missingTitle = new DetectMissingOgTitle();
        $issue = $missingTitle->evaluate($input, $emptyTitle);
        if ($issue !== null) $issues[] = $issue;

        $missingDesc = new DetectMissingOgDescription();
        $issue = $missingDesc->evaluate($input, $emptyDesc);
        if ($issue !== null) $issues[] = $issue;

        $missingImg = new DetectMissingOgImage();
        $issue = $missingImg->evaluate($input, $emptyImage);
        if ($issue !== null) $issues[] = $issue;

        $suggestions = [];

        $imageUrl = $input->get('og:image');
        $validImageUrl = false;
        $relativeImageUrl = false;
        if ($imageUrl !== null && $imageUrl !== '' && ! $emptyImage) {
            $imageRule = new EvaluateOgImageUrl();
            $imageFindings = $imageRule->evaluate($imageUrl);
            foreach ($imageFindings as $finding) {
                if ($finding instanceof \MegSEO\DTO\AnalysisWarning) {
                    $warnings[] = $finding;
                } else {
                    $suggestions[] = $finding;
                }
            }
            $validImageUrl = $imageFindings === [];
            $relativeImageUrl = ! $validImageUrl && count($imageFindings) > 0
                && str_contains($imageFindings[0]->message, 'relative');
        }

        $conflictRule = new DetectConflictingOgValues();
        $conflictSugs = $conflictRule->evaluate($input);
        $conflictsDetected = $conflictSugs !== [];
        foreach ($conflictSugs as $s) {
            $suggestions[] = $s;
        }

        $metadata = new OpenGraphCheckMetadata(
            checkIdentifier: $this->ref->id,
            ogTitleProvided: $input->has('og:title') && ! $emptyTitle,
            ogDescriptionProvided: $input->has('og:description') && ! $emptyDesc,
            ogImageProvided: $input->has('og:image') && ! $emptyImage,
            validImageUrl: $validImageUrl,
            relativeImageUrl: $relativeImageUrl,
            conflictingValuesDetected: $conflictsDetected,
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        $scoringBuilder = new OpenGraphScoreContributionBuilder();
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
                'ogTitleProvided' => $metadata->ogTitleProvided,
                'ogDescriptionProvided' => $metadata->ogDescriptionProvided,
                'ogImageProvided' => $metadata->ogImageProvided,
                'validImageUrl' => $metadata->validImageUrl,
                'relativeImageUrl' => $metadata->relativeImageUrl,
                'conflictingValuesDetected' => $metadata->conflictingValuesDetected,
            ],
        );
    }

    private function buildInput(AnalysisContext $context): OpenGraphCheckInput
    {
        if (is_array($context->subject)) {
            return new OpenGraphCheckInput($context->subject);
        }

        return new OpenGraphCheckInput([]);
    }
}
