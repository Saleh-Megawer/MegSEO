<?php

declare(strict_types=1);

namespace MegSEO\Tests\Fixtures\Core;

use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\AnalysisSuggestion;
use MegSEO\DTO\AnalysisWarning;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

final class StubPassingCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(
            check: $this->ref(),
            scoreContribution: new ScoreSummary(value: 100.0),
        );
    }
}

final class StubIssueCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
        private readonly string $message = 'Issue found',
        private readonly string $details = 'Detailed issue',
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(
            check: $this->ref(),
            issues: [new AnalysisIssue($this->message, $this->details, $this->id)],
        );
    }
}

final class StubWarningCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
        private readonly string $message = 'Warning found',
        private readonly string $details = 'Warning details',
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(
            check: $this->ref(),
            warnings: [new AnalysisWarning($this->message, $this->details, $this->id)],
        );
    }
}

final class StubSuggestionCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
        private readonly string $message = 'Suggestion offered',
        private readonly string $details = 'Suggestion details',
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(
            check: $this->ref(),
            suggestions: [new AnalysisSuggestion($this->message, $this->details, $this->id)],
        );
    }
}

final class StubFailingCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
        private readonly string $errorMessage = 'Check execution failed',
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        throw new \RuntimeException($this->errorMessage);
    }
}

final class StubScoreContributingCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
        private readonly float $scoreValue,
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(
            check: $this->ref(),
            scoreContribution: new ScoreSummary(value: $this->scoreValue),
        );
    }
}

final class StubEmptyFindingsCheck implements Check
{
    public function __construct(
        private readonly string $id,
        private readonly string $label,
    ) {}

    public function ref(): CheckReference
    {
        return new CheckReference($this->id, $this->label);
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(check: $this->ref());
    }
}
