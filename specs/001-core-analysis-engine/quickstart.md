# Quickstart: MegSEO Core Analysis Engine

## Goal

Use the MegSEO core engine as a generic analysis foundation with no built-in SEO checks. This quickstart shows the intended integration flow for package consumers and feature authors using stub checks only.

## 1. Create an analysis context

Construct an immutable `AnalysisContext` with the subject and optional metadata needed for analysis. The context should remain generic and free from SEO-specific business rules at the core level.

## 2. Register checks

Register one or more checks through the public registration contract. Each check must:

- expose a stable identifier
- accept the immutable context
- return a structured `CheckOutcome`
- avoid mutating shared engine state

## 3. Configure execution policy

Choose an execution policy appropriate for the caller:

- fail-fast when a single check failure should stop analysis
- isolate-failures when analysis should continue while preserving the overall `AnalysisResult` contract

## 4. Run analysis

Use the generic analyzer directly or the Laravel facade adapter:

```php
$result = $megseo->analyze($context);

$result->score();
$result->issues();
$result->warnings();
$result->suggestions();
```

Laravel-oriented usage is intentionally an adapter over the same generic engine contract:

```php
$result = MegSEO::analyze($context);
```

## 5. Verify deterministic behavior

For identical inputs, configuration, and check ordering:

- repeated runs should return identical output
- result ordering should remain stable
- isolated failures should behave according to the configured execution policy

## 6. Extend safely

Future feature modules should add checks through contracts and registration mechanisms rather than modifying the engine internals. New checks should be covered by unit and contract tests before they participate in broader analysis flows.
