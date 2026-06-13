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

$context = new AnalysisContext(
    subject: $pageData,
    attributes: ['url' => '/about'],
    options: ['detailed' => true],
);
```

## 3. Register checks

Register one or more checks through the `register()` or `registerCheck()` method. Each check must:

- expose a stable identifier via `ref(): CheckReference`
- accept the immutable context
- return a structured `CheckOutcome`
- avoid mutating shared engine state

```php
use MegSEO\Contracts\Check;

$engine->register(new TitleCheck());
$engine->register(new MetaDescriptionCheck());
```

The engine implements `RegistersChecks`, so `count()` and `all()` are available for inspection:

```php
$engine->count(); // number of registered checks
$engine->all();   // array of Check instances
```

## 4. Run analysis

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

## 5. Configure execution policy

Choose an execution policy appropriate for the caller (via the full constructor):

- fail-fast when a single check failure should stop analysis
- isolate-failures when analysis should continue while preserving the overall `AnalysisResult` contract

## 6. Verify deterministic behavior

For identical inputs, configuration, and check ordering:

- repeated runs should return identical output
- result ordering should remain stable
- isolated failures should behave according to the configured execution policy

## 7. Extend safely

Future feature modules should add checks through contracts and registration mechanisms rather than modifying the engine internals. New checks should be covered by unit and contract tests before they participate in broader analysis flows.
