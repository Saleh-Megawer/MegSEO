# Implementation Plan: ADR-001 — Shared AnalysisContext Subject Routing

**Branch**: `009-core-context-routing` | **Date**: 2026-06-13 | **Spec**: [spec.md](./spec.md)

## Summary

Enhance `AnalysisContext` and `Engine` to support per-check routed inputs while preserving full backward compatibility. Addresses the shared-context interference discovered in v0.8 multi-check validation. Changes are strictly additive — no existing code is modified, only extended.

## Technical Context

**Language/Version**: PHP 8.2+  
**Affected Components**: `AnalysisContext` (DTO), `PipelineRunner` (Pipeline), `Engine` (Core)  
**Unaffected**: All checks, contracts, Laravel adapters, policies, result aggregation  
**Testing**: Existing 568 Pest tests must pass unchanged + new routing tests

## Constitution Check

All 9 gates pass:
- **Architecture Before Features**: Core enhancement precedes dependent features.
- **Open for Extension**: Additive changes, no modifications to existing code.
- **Quality, Documentation, Stability**: Backward compatibility is the primary constraint.
- **Determinism**: Immutable context derivation preserves deterministic behavior.

## Design

### AnalysisContext Changes

```php
final readonly class AnalysisContext
{
    public function __construct(
        public mixed $subject,
        array $attributes = [],
        array $options = [],
        public ?string $requestId = null,
        public array $inputs = [],  // NEW: per-check inputs
    ) { ... }

    // NEW
    public function inputFor(string $identifier): mixed
    {
        return $this->inputs[$identifier] ?? $this->subject;
    }

    // NEW
    public function hasInputFor(string $identifier): bool
    {
        return array_key_exists($identifier, $this->inputs);
    }

    // NEW
    public function withSubject(mixed $subject): self
    {
        return new self(
            subject: $subject,
            attributes: $this->attributes->toArray(),
            options: $this->options,
            requestId: $this->requestId,
            inputs: $this->inputs,
        );
    }
}
```

### Engine Changes

The `PipelineRunner` resolves per-check input before calling `$check->analyze()`. When the context carries routed `inputs`, a derived context is created for each check; otherwise the original context is passed through unchanged.

```php
// PipelineRunner::run()
public function run(array $checks, AnalysisContext $context): array
{
    $results = [];

    foreach ($checks as $check) {
        // Derive context only if THIS check has a routed input
        $checkContext = $context->hasInputFor($check->ref()->id)
            ? $context->withSubject($resolved)
            : $context;

        try {
            $results[] = $check->analyze($checkContext);
        } catch (\Throwable $error) {
            // ... existing failure isolation unchanged
        }
    }

    return $results;
}
```

The pipeline (CheckPipeline, SequentialExecutionPlan) remains the sole execution mechanism. Failure isolation, execution ordering, abort behavior, and future hooks all continue to operate through the same pipeline infrastructure.

### Immutability Rationale

`AnalysisContext` is `final readonly`. Mutating `$subject` (even if PHP 8.2 allowed setting readonly properties) would break the contract that contexts are immutable snapshots. `withSubject()` creates an explicit, traceable derivation — each check gets its own context instance.

### Migration: Zero User Impact

| Existing Pattern | Behavior After Enhancement |
|-----------------|---------------------------|
| `new AnalysisContext(subject: 'title')` | Identical — `inputs` defaults to `[]`, legacy path used |
| `$engine->analyze($context)` | Identical — no routed inputs detected, pipeline unchanged |
| `$context->subject` | Identical — public readonly property is read, never mutated |
| `$result->toArray()` | Identical — serialization unchanged |

## Project Structure

```text
src/DTO/AnalysisContext.php       → +3 methods, +1 ctor param
src/Pipeline/PipelineRunner.php   → +input resolution before check call
tests/Contract/                    → +routing contract tests
tests/Integration/Core/            → +routing integration tests
```

## Testing Strategy

1. **Legacy execution**: All 568 existing tests pass without modification
2. **Routed execution**: 6 checks receive per-check inputs via `inputs` map
3. **Mixed mode**: Some checks routed, some legacy fallback
4. **Multi-check routing**: 6 checks executed via routed inputs simultaneously
5. **Determinism**: Repeated routed execution produces identical outputs
6. **Immutability**: `withSubject()` returns independent instance
7. **Backward compat**: `subject`-only contexts route to legacy pipeline path
