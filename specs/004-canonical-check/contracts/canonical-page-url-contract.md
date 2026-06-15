# Contract: Page URL Support

## Purpose

Define the page URL input requirements for canonical comparison.

## Behavioral Guarantees

- the page URL is optional — absence disables self-referencing and cross-domain checks
- when supplied, the page URL is normalized using the same normalization rules
- absence of page URL degrades gracefully (no errors, no false findings)

## Compatibility Rules

- page URL support is an enhancement, not a mandatory dependency
- missing page URL should not prevent core canonical validation
