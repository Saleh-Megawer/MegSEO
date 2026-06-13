# Quickstart: Title Check

## Goal

Use Title Check as the first production MegSEO feature and as the reference pattern for future checks.

## 1. Supply title analysis input

Provide title data through the existing MegSEO context in a way that allows the Title Check to read:

- the raw title
- an optional focus keyword
- optional duplicate-title support data

## 2. Run the existing MegSEO analysis flow

Execute MegSEO analysis through the standard platform entrypoint. Title Check participates as a normal registered check.

```php
$result = MegSEO::analyze($context);
```

## 3. Inspect Title Check outcomes

Expect the feature to contribute:

- issues for severe title problems
- warnings for moderate title concerns
- suggestions for improvement opportunities
- score contribution rationale
- stable metadata and identifiers

## 4. Validate multilingual behavior

Use English, Arabic, and mixed-language title cases to confirm:

- deterministic normalization
- correct handling of Unicode text
- correct handling of whitespace-only and separator-only titles

## 5. Validate optional support behaviors

Test with and without:

- focus keyword input
- duplicate-title support data

The feature should degrade safely when optional inputs are absent.

## 6. Use as a reference pattern

Future checks should follow the same design shape:

- feature-scoped module
- deterministic normalization where relevant
- composable rule objects
- explicit rationale and confidence
- stable identifiers and metadata
