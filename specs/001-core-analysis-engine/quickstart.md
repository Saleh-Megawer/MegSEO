# Quickstart: MegSEO Core Analysis Engine

## Goal

Use the MegSEO core engine as a generic analysis foundation with no built-in SEO checks. This quickstart shows the intended integration flow for package consumers and feature authors using stub checks only.

## 1. Create an engine instance

The recommended entrypoint is `Engine::make()`, which assembles sensible defaults for the pipeline, aggregator, and registry:

```php
use MegSEO\Core\Engine;

$engine = Engine::make();
```

For scenarios requiring full control (dependency injection, test mocks, custom pipeline components), the constructor remains available:

```php
$engine = new Engine($customPipeline, $customAggregator, $customRegistry);
```

## 2. Create an analysis context

Construct an immutable `AnalysisContext` with the subject and optional metadata needed for analysis. The context should remain generic and free from SEO-specific business rules at the core level.

```php
use MegSEO\DTO\AnalysisContext;
use MegSEO\Support\DefaultContextFactory;

// Direct construction
$context = new AnalysisContext(
    subject: $pageData,
    attributes: ['url' => '/about'],
    options: ['detailed' => true],
);

// Or via factory
$factory = new DefaultContextFactory();
$context = $factory->create($pageData, attributes: ['url' => '/about']);
```

## 3. Create a check

Each check must implement the `Check` contract. The contract requires:

- A stable identifier via `ref(): CheckReference` (must not change once published)
- An `analyze(AnalysisContext): CheckOutcome` method
- No mutation of shared engine state
- Deterministic output for identical inputs

```php
use MegSEO\Contracts\Check;
use MegSEO\DTO\AnalysisContext;
use MegSEO\DTO\AnalysisIssue;
use MegSEO\DTO\CheckOutcome;
use MegSEO\DTO\CheckReference;
use MegSEO\DTO\ScoreSummary;

final readonly class ContentLengthCheck implements Check
{
    public function ref(): CheckReference
    {
        return new CheckReference(
            id: 'content.length',
            label: 'Content Length Check',
            version: '1.0.0',
        );
    }

    public function analyze(AnalysisContext $context): CheckOutcome
    {
        return new CheckOutcome(
            check: $this->ref(),
            scoreContribution: new ScoreSummary(value: 85.0),
            issues: [
                new AnalysisIssue(
                    message: 'Content is below recommended length.',
                    details: 'Page contains 120 words. Aim for 300+.',
                    sourceCheckId: $this->ref()->id,
                    confidence: 0.9,
                ),
            ],
        );
    }
}
```

### Using the result factory helper

For simpler check construction, use `DefaultCheckResultFactory`:

```php
use MegSEO\Support\DefaultCheckResultFactory;

$factory = new DefaultCheckResultFactory();
$outcome = $factory->createOutcome(
    checkId: 'check.id',
    scoreValue: 75.0,
    issues: [new AnalysisIssue('...', '...', 'check.id')],
);
```

## 4. Register checks

Register one or more checks through the `register()` or `registerCheck()` method. The engine implements `RegistersChecks`, providing `register()`, `all()`, `count()`, and `has()`:

```php
$engine->register(new ContentLengthCheck());
$engine->register(new MetaDescriptionCheck());

$engine->count();           // 2
$engine->has('content.length'); // true
$engine->all();             // array of Check instances
```

**Duplicate identifiers**: If a check is registered with an ID that already exists, the first registration wins and subsequent registrations are silently ignored. This protects against accidental double-registration from multiple feature modules.

## 5. Run analysis

Use the engine directly or the Laravel facade adapter:

```php
$result = $engine->analyze($context);

$result->score();
$result->issues();
$result->warnings();
$result->suggestions();
```

Laravel-oriented usage is intentionally an adapter over the same generic engine contract:

```php
$result = MegSEO::analyze($context);
```

## 6. Consume results as arrays

`AnalysisResult` implements `ArrayableResult`, providing a stable `toArray()` representation suitable for serialization, API responses, and dashboards:

```php
$array = $result->toArray();

// $array = [
//     'score' => ['value' => 85.0, 'contributors' => [...]],
//     'issues' => [['message' => '...', 'sourceCheckId' => '...', ...]],
//     'warnings' => [...],
//     'suggestions' => [...],
//     'failures' => [...],
//     'metadata' => [...],
// ];
```

The array shape is a stable public contract. Additive changes are preferred over breaking structural changes.

## 7. Configure execution policy

Choose an execution policy appropriate for the caller (via the full constructor):

- fail-fast when a single check failure should stop analysis
- isolate-failures when analysis should continue while preserving the overall `AnalysisResult` contract

## 8. Verify deterministic behavior

For identical inputs, configuration, and check ordering:

- repeated runs should return identical output
- result ordering should remain stable
- isolated failures should behave according to the configured execution policy

## 9. Extend safely

Future feature modules should add checks through contracts and registration mechanisms rather than modifying the engine internals. New checks should be covered by unit and contract tests before they participate in broader analysis flows.

### Extension Checklist

1. Implement `MegSEO\Contracts\Check` with a stable identifier
2. Return a `CheckOutcome` with findings (issues, warnings, suggestions)
3. Optionally contribute a score through `ScoreSummary`
4. Register through `$engine->register()`
5. Verify deterministic behavior with repeated runs
6. Ensure the identifier is stable before the first public release
