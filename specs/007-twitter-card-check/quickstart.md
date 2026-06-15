# Quickstart: Twitter Card Check

## 1. Supply input

```php
$context = new AnalysisContext(subject: [
    'twitter:card'  => 'summary_large_image',
    'twitter:title' => 'My Title',
    'twitter:description' => 'Description',
    'twitter:image' => 'https://example.com/image.jpg',
]);
```

## 2. Run analysis

```php
$result = MegSEO::analyze($context);
```

## 3. Outcomes

- Issues for missing/empty required tags
- Warnings for invalid card types, relative image URLs
- Suggestions for conflicting values
- Score: 100 max
- Identifier: `seo.twitter_card`
