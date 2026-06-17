# Quickstart: Context Routing

## Legacy Usage (Unchanged)

```php
$context = new AnalysisContext(subject: 'My Title');
$result = MegSEO::analyze($context);
```

## Routed Multi-Check Usage (New)

```php
$context = new AnalysisContext(
    subject: null, // fallback
    inputs: [
        'seo.title'       => 'My Page Title',
        'seo.meta_description' => 'Page description...',
        'seo.canonical'   => 'https://example.com/page',
        'seo.open_graph'  => ['og:title'=>'OG','og:image'=>'https://...'],
        'seo.twitter_card'=> ['twitter:card'=>'summary','twitter:title'=>'TW'],
        'seo.hreflang'    => [['hreflang'=>'en','href'=>'https://...']],
    ],
);

$result = MegSEO::analyze($context);
```

## Mixed Mode

```php
$context = new AnalysisContext(
    subject: 'My Title', // TitleCheck and Canonical use this
    inputs: [
        'seo.open_graph' => ['og:title'=>'Custom OG'], // OG gets routed
    ],
);
// TitleCheck → 'My Title' (subject fallback)
// OpenGraphCheck → ['og:title'=>'Custom OG'] (routed)
```
