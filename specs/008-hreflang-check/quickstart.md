# Quickstart: Hreflang Check

## 1. Supply input

```php
$context = new AnalysisContext(subject: [
    ['hreflang' => 'en', 'href' => 'https://example.com/en'],
    ['hreflang' => 'ar', 'href' => 'https://example.com/ar'],
    ['hreflang' => 'x-default', 'href' => 'https://example.com/'],
], attributes: ['page_url' => 'https://example.com/en']);
```

## 2. Run

```php
$result = MegSEO::analyze($context);
```

## 3. Outcomes

- Issues for missing entries, empty values
- Warnings for invalid language codes, relative URLs
- Suggestions for missing x-default, non-self-referencing, conflicts
- Score: 100 max
- Identifier: `seo.hreflang`
