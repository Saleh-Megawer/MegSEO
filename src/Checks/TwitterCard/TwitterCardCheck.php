<?php

declare(strict_types=1);

namespace MegSEO\Checks\TwitterCard;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckInput;
use MegSEO\Checks\TwitterCard\DTO\TwitterCardCheckMetadata;
use MegSEO\Checks\TwitterCard\Rules\DetectEmptyTwitterValues;
use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterCard;
use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterTitle;
use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterDescription;
use MegSEO\Checks\TwitterCard\Rules\DetectMissingTwitterImage;
use MegSEO\Checks\TwitterCard\Rules\EvaluateTwitterCardType;
use MegSEO\Checks\TwitterCard\Rules\EvaluateTwitterImageUrl;
use MegSEO\Checks\TwitterCard\Rules\DetectConflictingTwitterValues;
use MegSEO\Checks\TwitterCard\Scoring\TwitterCardScoreContributionBuilder;

final class TwitterCardCheck implements Check
{
    private CheckReference $ref;
    private array $ruleIdentifiers = [
        'detect-empty-twitter-values',
        'detect-missing-twitter-card',
        'detect-missing-twitter-title',
        'detect-missing-twitter-description',
        'detect-missing-twitter-image',
        'evaluate-twitter-card-type',
        'evaluate-twitter-image-url',
        'detect-conflicting-twitter-values',
    ];

    public function __construct()
    {
        $this->ref = new CheckReference(id: 'seo.twitter_card', label: 'Twitter Card Check', version: '1.0.0');
    }

    public function ref(): CheckReference { return $this->ref; }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        $input = $this->buildInput($context);
        $issues = [];
        $warnings = [];
        $suggestions = [];

        $emptyRule = new DetectEmptyTwitterValues();
        foreach ($emptyRule->evaluate($input) as $issue) $issues[] = $issue;

        $emptyCard  = $input->isEmpty('twitter:card');
        $emptyTitle = $input->isEmpty('twitter:title');
        $emptyDesc  = $input->isEmpty('twitter:description');
        $emptyImg   = $input->isEmpty('twitter:image');

        // Presence rules
        $presenceRules = [
            [new DetectMissingTwitterCard(), $emptyCard],
            [new DetectMissingTwitterTitle(), $emptyTitle],
            [new DetectMissingTwitterDescription(), $emptyDesc],
            [new DetectMissingTwitterImage(), $emptyImg],
        ];
        foreach ($presenceRules as [$rule, $isE]) {
            $issue = $rule->evaluate($input, $isE);
            if ($issue !== null) $issues[] = $issue;
        }

        // Card type validation — only if value present and not empty
        $cardTypeValue = $input->get('twitter:card');
        $cardTypeRule = new EvaluateTwitterCardType();
        $cardTypeWarning = $cardTypeRule->evaluate($cardTypeValue, $emptyCard);
        if ($cardTypeWarning !== null) $warnings[] = $cardTypeWarning;

        // Image URL validation — only if value present and not empty
        $imageUrl = $input->get('twitter:image');
        $validImageUrl = false;
        $relativeImageUrl = false;
        if ($imageUrl !== null && $imageUrl !== '' && ! $emptyImg) {
            $imageRule = new EvaluateTwitterImageUrl();
            foreach ($imageRule->evaluate($imageUrl) as $w) $warnings[] = $w;
            $validImageUrl = $imageRule->evaluate($imageUrl) === [];
            $relativeImageUrl = ! $validImageUrl;
        }

        // Conflict detection
        $conflictRule = new DetectConflictingTwitterValues();
        foreach ($conflictRule->evaluate($input) as $s) $suggestions[] = $s;

        $metadata = new TwitterCardCheckMetadata(
            checkIdentifier: $this->ref->id,
            twitterCardProvided: $input->has('twitter:card') && ! $emptyCard,
            twitterTitleProvided: $input->has('twitter:title') && ! $emptyTitle,
            twitterDescriptionProvided: $input->has('twitter:description') && ! $emptyDesc,
            twitterImageProvided: $input->has('twitter:image') && ! $emptyImg,
            validCardType: $cardTypeWarning === null,
            validImageUrl: $validImageUrl,
            relativeImageUrl: $relativeImageUrl,
            conflictingValuesDetected: $conflictRule->evaluate($input) !== [],
            ruleIdentifiers: $this->ruleIdentifiers,
        );

        $scoringBuilder = new TwitterCardScoreContributionBuilder();
        $scoreContribution = $scoringBuilder->build($issues, $warnings, $suggestions);

        return new CheckOutcome(check: $this->ref, scoreContribution: $scoreContribution, issues: $issues, warnings: $warnings, suggestions: $suggestions, metadata: [
            'checkIdentifier' => $metadata->checkIdentifier,
            'ruleIdentifiers' => $metadata->ruleIdentifiers,
            'twitterCardProvided' => $metadata->twitterCardProvided,
            'twitterTitleProvided' => $metadata->twitterTitleProvided,
            'twitterDescriptionProvided' => $metadata->twitterDescriptionProvided,
            'twitterImageProvided' => $metadata->twitterImageProvided,
            'validCardType' => $metadata->validCardType,
            'validImageUrl' => $metadata->validImageUrl,
            'relativeImageUrl' => $metadata->relativeImageUrl,
            'conflictingValuesDetected' => $metadata->conflictingValuesDetected,
        ]);
    }

    private function buildInput(AnalysisContext $context): TwitterCardCheckInput
    {
        return new TwitterCardCheckInput(is_array($context->subject) ? $context->subject : []);
    }
}
