# Data Model: Hreflang Check

## HreflangCheckInput
- **entries**: Array of hreflang entries, each with `hreflang` (string) and `href` (string)
- **pageUrl**: Optional page URL for self-referencing comparison
- Methods: `hasEntries()`, `getEntries()`, `entryCount()`

## HreflangEntryReport
- Per-entry analysis result: `index`, `hreflang`, `href`, `status`, `message`

## HreflangCheckMetadata
- `checkIdentifier` (`seo.hreflang`)
- `entryCount`, `hasXDefault`
- `selfReferencingCount`, `invalidLangCodes`, `invalidUrls`
- `conflictingEntriesDetected`
