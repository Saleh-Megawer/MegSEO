# Contract: Duplicate Description Support

## Purpose

Define the expectations for optional duplicate-description support used by Meta Description Check.

## Behavioral Guarantees

- duplicate-description evaluation is optional and contextual
- absence of duplicate support data does not break the feature
- when support data is available, the feature can emit duplicate-related findings or metadata consistently

## Compatibility Rules

- duplicate support remains an enhancement, not a mandatory dependency
- public duplicate-related metadata should remain stable once exposed
