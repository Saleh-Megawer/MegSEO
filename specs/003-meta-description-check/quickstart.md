# Quickstart: Meta Description Check

## Goal

Use Meta Description Check as the second MegSEO feature, following the Title Check reference pattern.

## 1. Supply description analysis input

Provide meta description data through the existing MegSEO context:

- the raw description
- an optional focus keyword
- optional duplicate-description support data

## 2. Run the existing MegSEO analysis flow

Execute MegSEO analysis through the standard platform entrypoint. Meta Description Check participates as a normal registered check.

```php
$result = MegSEO::analyze($context);
```

## 3. Inspect Meta Description Check outcomes

Expect the feature to contribute:

- issues for severe description problems
- warnings for moderate description concerns
- suggestions for improvement opportunities
- score contribution rationale (derived from findings)
- stable metadata and identifiers

## 4. Validate multilingual behavior

Use English, Arabic, and mixed-language description cases to confirm:

- deterministic normalization
- correct handling of Unicode text
- correct handling of whitespace-only and separator-only descriptions

## 5. Validate optional support behaviors

Test with and without:

- focus keyword input
- duplicate-description support data

The feature should degrade safely when optional inputs are absent.

## 6. Follow the established pattern

Meta Description Check follows the same architectural shape as Title Check:

- feature-scoped module under `src/Checks/`
- deterministic normalization
- composable rule objects
- explicit rationale and confidence
- stable identifiers (`seo.meta_description`) and metadata
- thin Laravel adapter for registration
