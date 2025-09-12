# Installation

## Via Composer (Recommended)

```bash
composer require cpsit/bravo-handlebars-content
```

## Manual Installation

1. Download the extension from the TYPO3 Extension Repository
2. Upload and install via the Extension Manager
3. Activate the extension

## Post-Installation Steps

1. Include the static TypoScript template "Bravo handlebars content" in your root template
2. Clear all caches
3. Configure your handlebars template paths (see Configuration section)

## Requirements

- TYPO3 12.4 LTS
- PHP 8.2 or higher
- cpsit/typo3-handlebars extension
- cpsit/typo3-handlebars-components extension

## Verification

After installation, verify the extension is working by:

1. Check that the extension is active in the Extension Manager
2. Verify that the static TypoScript template is available
3. Test a simple content element configuration
