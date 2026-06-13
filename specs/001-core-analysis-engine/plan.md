# Implementation Plan: MegSEO Core Analysis Engine

**Branch**: `001-core-analysis-engine` | **Date**: 2026-06-13 | **Spec**: [spec.md](/abs/path/c:/laragon/www/MegSEO/specs/001-core-analysis-engine/spec.md)
**Input**: Feature specification from `/specs/001-core-analysis-engine/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/plan-template.md` for the execution workflow.

## Summary

Build the generic MegSEO analysis foundation as a Composer-distributed PHP 8.2+ package with a framework-agnostic core and Laravel-first integration layer. The design centers on immutable input/output DTOs, explicit contracts for checks and extension points, deterministic pipeline execution, configurable execution policy, and stable aggregated result contracts that future SEO features can extend without modifying core behavior.

## Technical Context

**Language/Version**: PHP 8.2+  
**Primary Dependencies**: PHP core language features, Composer package metadata, Laravel integration surface via service provider, facade, config publishing, and artisan command support  
**Storage**: N/A  
**Testing**: PHPUnit or Pest for unit and integration coverage, with Orchestra Testbench or equivalent Laravel package integration harness  
**Target Platform**: PHP 8.2+ environments consuming Composer packages, with Laravel as the primary integration target  
**Project Type**: Library / Laravel package  
**Performance Goals**: Analysis overhead beyond registered checks should remain minimal and proportional to pipeline coordination and aggregation only  
**Constraints**: Framework-agnostic core, deterministic behavior for identical inputs and configuration, immutable DTOs whenever practical, stable public contracts, minimal hidden state, no SEO-specific business rules in core  
**Scale/Scope**: Foundational package layer for all future MegSEO feature modules, including multiple independently contributed checks and long-term backward-compatible result consumption

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- **Architecture Before Features**: Pass. The plan implements only the approved core engine specification and keeps SEO checks out of scope.
- **Feature-Driven Development**: Pass. The structure is organized around the `CoreAnalysisEngine` feature and its public contracts rather than generic cross-project buckets.
- **Generic Core, Domain-Specific Features**: Pass. Core responsibilities are limited to context intake, pipeline execution, aggregation, execution policy, and extension points.
- **Open for Extension**: Pass. Checks, aggregators, policies, and Laravel adapters rely on contracts and composition rather than inheritance-heavy extension.
- **Quality, Documentation, and Stability**: Pass. The plan includes unit tests, integration tests, contract documentation, quickstart guidance, and backward compatibility rules.
- **Actionable, Educational Output Philosophy**: Pass. The foundation supports issues, warnings, suggestions, score channels, and confidence-ready contracts without imposing SEO-specific content.
- **Arabic and Future Language Support**: Pass. No language logic is embedded in the core; future language-aware features can extend the engine through checks and context data.
- **Laravel-First Developer Experience**: Pass. Laravel integration is explicit through service provider registration, facade entrypoint, config publishing, and artisan-friendly tooling while preserving a generic core.
- **Determinism and Human Override**: Pass. Deterministic ordering, immutable DTOs, and explicit execution policy limit hidden behavior; the engine surfaces guidance and results without claiming domain truth.

## Project Structure

### Documentation (this feature)

```text
specs/001-core-analysis-engine/
├── plan.md
├── research.md
├── data-model.md
├── quickstart.md
├── contracts/
│   ├── analysis-api.md
│   ├── check-contract.md
│   ├── execution-policy-contract.md
│   └── result-contract.md
└── tasks.md
```

### Source Code (repository root)

```text
src/
├── CoreAnalysisEngine/
│   ├── Contracts/
│   │   ├── AnalyzesContexts.php
│   │   ├── AggregatesCheckResults.php
│   │   ├── Check.php
│   │   ├── CheckResultFactory.php
│   │   ├── ConfiguresExecutionPolicy.php
│   │   ├── ContextFactory.php
│   │   ├── ExecutionPolicy.php
│   │   ├── Pipeline.php
│   │   └── RegistersChecks.php
│   ├── DTO/
│   │   ├── AnalysisContext.php
│   │   ├── AnalysisIssue.php
│   │   ├── AnalysisResult.php
│   │   ├── AnalysisSuggestion.php
│   │   ├── AnalysisWarning.php
│   │   ├── CheckOutcome.php
│   │   ├── CheckReference.php
│   │   ├── ExecutionDecision.php
│   │   └── ScoreSummary.php
│   ├── Pipeline/
│   │   ├── CheckPipeline.php
│   │   ├── CheckRegistry.php
│   │   ├── PipelineRunner.php
│   │   └── SequentialExecutionPlan.php
│   ├── Policy/
│   │   ├── FailFastExecutionPolicy.php
│   │   ├── IsolateFailuresExecutionPolicy.php
│   │   └── StandardExecutionPolicies.php
│   ├── Result/
│   │   ├── ResultAggregator.php
│   │   ├── ScoreAggregator.php
│   │   └── ResultNormalizer.php
│   ├── Support/
│   │   ├── ImmutableMap.php
│   │   └── OrderedChecks.php
│   └── MegSEOEngine.php
├── Laravel/
│   ├── Facades/
│   │   └── MegSEO.php
│   ├── Console/
│   │   └── AnalyzeContextCommand.php
│   ├── Providers/
│   │   └── MegSEOServiceProvider.php
│   ├── Configuration/
│   │   └── megseo.php
│   └── Support/
│       └── LaravelCheckRegistration.php
└── Shared/
    └── Contracts/
        └── ArrayableResult.php

tests/
├── Unit/
│   └── CoreAnalysisEngine/
├── Contract/
│   ├── CoreAnalysisEngine/
│   └── Laravel/
└── Integration/
    ├── CoreAnalysisEngine/
    └── Laravel/
```

**Structure Decision**: Use a single library/package structure rooted in `src/`, with the generic engine isolated under `src/CoreAnalysisEngine` and Laravel integration under `src/Laravel`. This keeps feature ownership clear, preserves a framework-agnostic core, and avoids scattering engine concerns across unrelated technical layers.

## Phase 0: Research Summary

### Key Decisions

- Use immutable DTO-style value objects for context, outcomes, and result payloads to support determinism, debuggability, and stable contracts.
- Use explicit contracts for checks, registries, aggregators, analyzers, and execution policies so future features extend behavior through composition rather than core edits.
- Use a sequential deterministic pipeline as the baseline execution model. Parallelism is intentionally out of scope for the foundation because it adds ordering ambiguity, failure complexity, and hidden concurrency state.
- Treat execution policy as a first-class collaborator rather than a boolean option. This keeps error handling configurable without embedding special cases throughout the pipeline runner.
- Separate the generic analysis engine from Laravel adapters. Laravel-facing APIs bind the generic analyzer and registry through the container, facade, configuration, and console tools.

### Rationale

- Immutable DTOs reduce accidental mutation across checks and aggregators, which improves determinism and testability.
- Interface-driven composition keeps the core open for extension while maintaining stable public seams.
- Sequential execution provides the clearest baseline for reproducible ordering, failure isolation, and regression testing.
- First-class execution policy objects align directly with the spec's configurable failure behavior and allow future policy expansion without changing analyzer code.
- A dedicated Laravel layer gives package consumers a native DX while protecting the core from framework leakage.

### Alternatives Considered

- **Mutable arrays for results and context**: Rejected because they weaken contract clarity and make deterministic behavior harder to enforce.
- **Inheritance-based base check classes as the primary extension point**: Rejected because they over-constrain future features and couple extension to internal implementation.
- **Parallel pipeline execution in v1**: Rejected because the foundation should optimize for determinism and clarity before concurrency.
- **Laravel-only core implementation**: Rejected because it conflicts with the constitution's generic core boundary.

## Phase 1: Design

### Proposed Package Structure

- `src/CoreAnalysisEngine/Contracts`: Public extension interfaces for analyzers, checks, policies, registries, pipelines, and aggregators.
- `src/CoreAnalysisEngine/DTO`: Immutable transport and output contracts.
- `src/CoreAnalysisEngine/Pipeline`: Deterministic execution orchestration and check registration.
- `src/CoreAnalysisEngine/Policy`: Failure-handling policies and policy presets.
- `src/CoreAnalysisEngine/Result`: Aggregation, normalization, and result assembly logic.
- `src/Laravel`: Service provider, facade, configuration, and artisan-facing adapters.
- `tests/Unit`, `tests/Contract`, `tests/Integration`: Layered test suites mapped to constitution expectations.

### Core Components and Responsibilities

- `MegSEOEngine`: Primary generic entrypoint implementing `AnalyzesContexts`.
- `AnalysisContext`: Immutable input DTO containing normalized analysis data, metadata, and optional engine configuration hints.
- `CheckRegistry`: Ordered source of registered checks and their stable identifiers.
- `CheckPipeline` / `PipelineRunner`: Executes checks in deterministic order against a context and hands outcomes to the aggregator.
- `ExecutionPolicy`: Determines whether failures stop analysis or are isolated while preserving `AnalysisResult`.
- `ResultAggregator`: Collects successful check outcomes, isolated failures, warnings, issues, suggestions, and score contributions into a normalized `AnalysisResult`.
- `ResultNormalizer`: Ensures stable ordering and shape guarantees for consumer-facing output.
- `LaravelCheckRegistration`: Bridges Laravel config/container registration to the generic registry contract.

### Public Contracts and Interfaces

- `AnalyzesContexts`: `analyze(AnalysisContext $context): AnalysisResult`
- `Check`: exposes a stable identifier plus a deterministic `analyze(AnalysisContext $context): CheckOutcome`
- `ExecutionPolicy`: decides how to react to thrown errors, unsupported contexts, and interruption signals
- `Pipeline`: executes an ordered set of checks for a given context
- `AggregatesCheckResults`: assembles a final immutable `AnalysisResult`
- `RegistersChecks`: adds, orders, filters, and resolves checks for analysis
- DTO contracts:
  - `AnalysisResult` exposes `score()`, `issues()`, `warnings()`, `suggestions()`, plus optional metadata accessors
  - `CheckReference` preserves the stable check identifier for debugging, filtering, and future dashboards
  - `CheckOutcome` carries one check's structured output without embedding SEO-specific semantics

### Pipeline Execution Approach

- Default to deterministic sequential execution.
- Resolve checks from the registry in a documented stable order.
- Pass the same immutable `AnalysisContext` instance to each check.
- Capture each `CheckOutcome` or failure event.
- Defer failure handling to `ExecutionPolicy`.
- Normalize aggregated output ordering before returning the final `AnalysisResult`.

### Result Aggregation Strategy

- Aggregate score contributions through a dedicated `ScoreAggregator` so score behavior can evolve without altering non-score result assembly.
- Collect issues, warnings, and suggestions into separate immutable lists.
- Preserve check identifiers alongside findings for traceability.
- Normalize ordering by registry order and per-check emission order to preserve determinism.
- Return a valid empty `AnalysisResult` when there are no checks or no findings.
- Preserve the overall result contract even when isolated check failures occur under a non-failing execution policy.

### Execution Policy Strategy

- Provide at least two baseline policies:
  - `FailFastExecutionPolicy`: aborts the analysis session on the first policy-relevant check failure.
  - `IsolateFailuresExecutionPolicy`: records or surfaces failure metadata while preserving the final `AnalysisResult`.
- Keep policy decisions external to pipeline internals through a dedicated contract.
- Treat unsupported scenarios and interruptions as policy-resolved events rather than implicit engine behavior.
- Make the active policy configurable through the generic engine constructor/configuration object and through Laravel config bindings.

### Extension Mechanism for Future Checks

- New checks implement the `Check` contract and register through `RegistersChecks`.
- Feature modules can contribute checks without editing existing engine code.
- Aggregation extension points remain explicit and versioned; if future modules need new metadata, they should contribute through documented contracts rather than mutate `AnalysisResult` ad hoc.
- Check identifiers must remain stable once public to protect filtering, reporting, and dashboard compatibility.

### Laravel Integration Approach

- `MegSEOServiceProvider` binds the generic analyzer, registry, aggregator, and execution policy into Laravel's container.
- `MegSEO` facade delegates to the generic `AnalyzesContexts` implementation for a Laravel-friendly API:
  - `MegSEO::analyze($context)`
- Publish a `megseo.php` configuration file for check registration, execution policy selection, and package defaults.
- Provide at least one artisan command focused on analysis orchestration/debugging without embedding any SEO-specific checks.
- Keep Laravel adapters thin so core behavior remains testable outside Laravel.

### Testing Strategy

- **Unit tests**
  - DTO immutability and accessor behavior
  - deterministic pipeline ordering
  - result aggregation correctness
  - execution policy branching behavior
  - empty pipeline and empty finding scenarios
- **Contract tests**
  - `Check` contract compliance
  - `AnalysisResult` output shape stability
  - `ExecutionPolicy` decision semantics
  - Laravel facade/service container bindings
- **Integration tests**
  - end-to-end analysis flow with multiple stub checks
  - fail-fast vs isolate-failures behavior
  - repeated identical runs produce identical outputs
  - Laravel package bootstrapping, config publishing, and facade-based analysis

### Backward Compatibility Considerations

- Treat `AnalysisContext`, `AnalysisResult`, public interfaces, and emitted result shapes as versioned contracts.
- Additive changes are preferred over breaking shape changes.
- Stable check identifiers become part of the observable contract once exposed.
- New extension points must be additive and should not require existing checks to change behavior unless explicitly versioned.
- Result metadata for isolated failures must be documented carefully to avoid accidental contract drift.

### Risks, Trade-offs, and Rationale

- **Risk**: Over-designing extension points too early.
  - **Mitigation**: Start with a focused contract set aligned to the approved spec and keep expansion additive.
- **Risk**: Laravel adapters leaking framework concerns into the core.
  - **Mitigation**: Constrain Laravel code to `src/Laravel` and keep interfaces/framework-neutral DTOs in the core namespace.
- **Risk**: Ambiguity in score aggregation semantics.
  - **Mitigation**: Isolate score aggregation behind a dedicated contract and avoid SEO-specific scoring rules in the foundation.
- **Trade-off**: Sequential execution may be slower than future parallel approaches.
  - **Rationale**: Determinism, debuggability, and simpler failure handling are more important for the initial foundation.
- **Trade-off**: Immutable DTOs may increase object creation overhead.
  - **Rationale**: The predictability and testability gains are worth the small coordination cost at this stage.

## Phase 2: Implementation Strategy

1. Establish package skeleton, namespaces, and Composer autoload boundaries for `CoreAnalysisEngine` and `Laravel`.
2. Implement immutable DTOs and public contracts first so the engine grows around stable seams.
3. Implement deterministic check registry, sequential pipeline runner, and execution policy contracts.
4. Implement result aggregation and normalization with contract tests protecting output stability.
5. Add Laravel integration bindings, facade, configuration publishing, and console entrypoint.
6. Complete unit, contract, and integration coverage before any future feature modules add real SEO checks.
7. Document quickstart usage and extension examples focused only on generic stub checks.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |
