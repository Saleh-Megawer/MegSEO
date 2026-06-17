# Data Model: Context Routing

## AnalysisContext (Enhanced)

- `subject`: mixed — legacy input (unchanged)
- `attributes`: ImmutableMap — metadata (unchanged)
- `options`: array — behavioral flags (unchanged)
- `requestId`: ?string — trace ID (unchanged)
- **`inputs`: array<string, mixed>`** — NEW: per-check inputs keyed by check identifier

## Input Resolution

```
inputFor('seo.title')
  → inputs['seo.title']  if present
  → subject              fallback
```

## Context Derivation

```
$derived = $ctx->withSubject($newSubject)
  → new AnalysisContext(
      subject: $newSubject,
      attributes: clone of $ctx->attributes,
      options: $ctx->options,
      requestId: $ctx->requestId,
      inputs: $ctx->inputs,
    )
```
