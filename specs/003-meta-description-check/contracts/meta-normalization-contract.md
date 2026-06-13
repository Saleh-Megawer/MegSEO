# Contract: Meta Description Normalization

## Purpose

Define the deterministic normalization expectations used by Meta Description Check.

## Behavioral Guarantees

- identical inputs produce identical normalized outputs
- normalization happens before rule evaluation
- normalization supports Unicode and Arabic safely
- normalization should not introduce nondeterministic ordering or hidden state

## Compatibility Rules

- normalization behavior changes that affect observable findings must be documented
- normalization metadata should remain additive where possible
