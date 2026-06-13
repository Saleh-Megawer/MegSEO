# Contract: Check

## Purpose

Define how future feature modules contribute independent checks to the core engine.

## Required Shape

- every check exposes a stable identifier
- every check accepts an `AnalysisContext`
- every check returns a `CheckOutcome`

## Behavioral Guarantees

- checks operate independently
- checks do not mutate shared engine state
- checks do not require SEO-specific behavior from the core engine
- checks may return empty findings
- checks must be deterministic for identical context input unless explicitly documented otherwise

## Compatibility Rules

- published check identifiers are stable once public
- changing the observable shape of `CheckOutcome` is a contract change
- future optional metadata must be additive
