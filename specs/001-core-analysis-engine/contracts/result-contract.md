# Contract: Analysis Result

## Purpose

Define the stable aggregated output contract returned from every completed analysis session.

## Required Accessors

- `score()`
- `issues()`
- `warnings()`
- `suggestions()`

## Behavioral Guarantees

- always returns a valid contract, including for empty pipelines
- preserves deterministic ordering for aggregated findings
- may include isolated failure metadata when allowed by execution policy
- does not require built-in SEO checks to be meaningful as a contract

## Compatibility Rules

- required accessors are stable public API
- additive metadata is preferred over breaking structural changes
- backward-incompatible output shape changes require explicit versioning and documentation
