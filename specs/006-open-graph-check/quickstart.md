# Quickstart: Open Graph Check

## Goal

Use Open Graph Check as the first social metadata MegSEO feature.

## 1. Supply OG input

Provide Open Graph data as a structured array:

```php
$context = new AnalysisContext(subject: [
    'og:title'       => 'My Page Title',
    'og:description' => 'Page description for social sharing',
    'og:image'       => 'https://example.com/image.jpg',
]);
```

## 2. Run analysis

```php
$result = MegSEO::analyze($context);
```

## 3. Inspect outcomes

- Issues for missing og:title, og:description, og:image, empty values, or invalid image URLs
- Warnings for relative og:image URLs
- Suggestions for improvements
- Score contribution rationale
- Stable metadata (`seo.open_graph`)

## 4. Follow the established pattern

- Feature-scoped module under `src/Checks/OpenGraph/`
- Composable rule objects (6 rules)
- Reuses `CanonicalUrlValidator` for image URL validation
- Thin Laravel adapter for registration
