# Contract: Analysis API

## Purpose

Define the stable public analysis entrypoints exposed by the generic engine and Laravel adapter.

## Generic Contract

- Analyzer contract name: `AnalyzesContexts`
- Primary operation: `analyze(AnalysisContext $context): AnalysisResult`

## Behavioral Guarantees

- accepts a single immutable analysis context
- executes registered checks in deterministic order
- returns a valid `AnalysisResult` even for empty pipelines
- preserves stable output accessors for score, issues, warnings, and suggestions
- delegates failure handling to the configured execution policy

## Laravel Adapter Expectations

- facade name: `MegSEO`
- facade method: `analyze($context)`
- service container binding resolves the same generic analyzer contract used outside Laravel
- Laravel configuration selects or configures the active execution policy and check registration inputs
