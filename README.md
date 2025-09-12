# TYPO3 Extension: bravo-handlebars-content

**Version:** Compatible with TYPO3 12.4 LTS  
**License:** GPL-2.0-or-later  
**Author:** CPS-IT  

## Overview

The **bravo-handlebars-content** extension is a powerful content rendering component for TYPO3 that seamlessly integrates with the handlebars templating system. It provides a comprehensive set of data processors and content element configurations specifically designed for handlebars-based TYPO3 websites.

## Key Features

- **🎯 Handlebars Integration** - Full integration with the TYPO3 handlebars ecosystem
- **🔧 Rich Data Processing Pipeline** - Over 20 specialized data processors for various content types
- **📄 Content Element Support** - Pre-configured support for standard TYPO3 content elements
- **🎬 Media Processing** - Advanced media handling for images, videos (YouTube/Vimeo), and audio files
- **🔀 Flexible Field Mapping** - Configurable field mapping and transformation capabilities
- **🌍 Localization Support** - Built-in localization data processor with multilingual support
- **🎨 Asset Management** - Automatic CSS/JavaScript asset injection
- **📱 Responsive Media** - Built-in responsive image and media configurations

## Quick Start

1. **Install the extension:**
   ```bash
   composer require cpsit/bravo-handlebars-content
   ```

2. **Include static TypoScript template:**
   - Go to Template → Edit → Includes
   - Add "Bravo handlebars content" to "Include static (from extensions)"

3. **Configure a content element:**
   ```typoscript
   tt_content.text =< handlebarsContent.default
   tt_content.text {
       templateName = @ce-text
       dataProcessing {
           10 = ceText
       }
   }
   ```

4. **Create a handlebars template** (`@ce-text.hbs`):
   ```handlebars
   <div class="ce-text">
       {{#if headlines.header}}
           <{{headlines.layout}}>{{headlines.header}}</{{headlines.layout}}>
       {{/if}}
       {{#if bodytext}}
           <div class="content">{{{bodytext}}}</div>
       {{/if}}
   </div>
   ```

## Requirements

- **TYPO3** 12.4 LTS
- **PHP** 8.1 or higher  
- **cpsit/typo3-handlebars** extension
- **cpsit/typo3-handlebars-components** extension

## Documentation

### 📚 User Documentation

| Document | Description |
|----------|-------------|
| **[Installation](Documentation/Installation.md)** | Installation instructions and requirements |
| **[Quick Start](Documentation/QuickStart.md)** | Getting started guide with basic setup |
| **[Configuration](Documentation/Configuration.md)** | TypoScript configuration and customization |
| **[Content Elements](Documentation/ContentElements.md)** | Supported content elements and field mapping |

### 🔧 Developer Documentation

| Document | Description |
|----------|-------------|
| **[Data Processors](Documentation/DataProcessors.md)** | Available data processors and their usage |
| **[Developer Guide](Documentation/DeveloperGuide.md)** | Creating custom processors and templates |
| **[API Reference](Documentation/API.md)** | Complete API documentation and class reference |
| **[Troubleshooting](Documentation/Troubleshooting.md)** | Common issues and debugging techniques |

### 📋 Examples

| Resource | Description |
|----------|-------------|
| **[Template Examples](Resources/Public/Examples/Templates/)** | Ready-to-use Handlebars templates for content elements |
| **[Media Partials](Resources/Public/Examples/Templates/partials/media/)** | Reusable media rendering templates |

## Available Data Processors

The extension includes 20+ specialized data processors:

### Core Processors
- **handlebarsLocalization** - XLIFF file processing and multi-language support
- **handlebarsMedia** - Advanced media processing (images, videos, audio)
- **handlebarsSerial** - Sequential processor execution
- **handlebarsMapFields** - Field mapping and transformation

### Content Element Processors  
- **ceText** - Text content elements
- **ceTextMedia** - Text with media elements
- **ceHeader** - Header elements
- **ceUploads** - File download/upload lists

### Utility Processors
- **handlebarsContentObjects** - TypoScript object rendering
- **handlebarsUnset** - Remove unwanted data
- **handlebarsKeepPath** - Keep only specified data paths
- **cropText** - Text cropping with ellipsis

### Advanced Processors
- **handlebarsDatabaseQuery** - Custom database queries
- **handlebarsFileLink** - File link processing
- **handlebarsLanguageMenu** - Language menu generation

[➤ View complete processor documentation](Documentation/DataProcessors.md)

## Supported Content Elements

- ✅ **text** - Text content with optional headers
- ✅ **textmedia** - Text with media gallery (images, videos, audio)
- ✅ **header** - Standalone headers with linking
- ✅ **html** - Raw HTML content
- ✅ **uploads** - File downloads and document lists
- ✅ **menu** - Page and section navigation
- ✅ **shortcut** - Content references and shortcuts
- ✅ **plugin** - Extension and plugin integration

[➤ View content element documentation](Documentation/ContentElements.md)

## Media Support

### Image Processing
- **Responsive Images** - Automatic srcset generation
- **Crop Variants** - Multiple image crops and aspect ratios  
- **Lazy Loading** - Performance-optimized image loading
- **WebP Support** - Modern image format support

### Video Integration
- **YouTube** - Privacy-friendly embedding with preview images
- **Vimeo** - Professional video platform integration
- **HTML5 Video** - Native video player support
- **Audio Files** - HTML5 audio player integration

### File Management
- **Download Lists** - Formatted file download presentations
- **File Icons** - Automatic file type icons
- **File Metadata** - Size, type, and description display

## Development

### Creating Custom Processors

```php
<?php
namespace Your\Extension\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class CustomProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // Your processing logic here
        return $processedData;
    }
}
```

### Template Development

```handlebars
{{!-- Content Element Template --}}
<div class="ce-custom{{#if frame_class}} {{frame_class}}{{/if}}">
    {{#if headlines.header}}
        <{{headlines.layout}} class="ce-custom__header">
            {{headlines.header}}
        </{{headlines.layout}}>
    {{/if}}
    
    {{#if assets}}
        {{#each assets}}
            {{>media/item this}}
        {{/each}}
    {{/if}}
    
    {{#if bodytext}}
        <div class="ce-custom__content">{{{bodytext}}}</div>
    {{/if}}
</div>
```

[➤ View developer guide](Documentation/DeveloperGuide.md)

## Performance & Optimization

### Caching
- **Template Caching** - Compiled handlebars templates
- **Data Caching** - Processed content data caching
- **Asset Caching** - CSS/JS asset optimization

### Best Practices
- **Lazy Loading** - Deferred media loading for performance
- **Responsive Design** - Mobile-optimized content rendering
- **Semantic HTML** - Accessible and SEO-friendly markup
- **Progressive Enhancement** - Fallback support for older browsers

## Contributing

This extension is maintained by CPS-IT. For questions, issues, or contributions, please refer to the project's repository.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Follow PSR-12 coding standards
5. Add tests for new features

## License

This extension is licensed under GPL-2.0-or-later. See the LICENSE file for details.

---

**Need Help?** 
- 📖 [Read the Documentation](Documentation/)
- 🐛 [Troubleshooting Guide](Documentation/Troubleshooting.md)
- 💡 [View Examples](Resources/Public/Examples/Templates/)

**Last Updated:** 2025-09-12  
**TYPO3 Compatibility:** 12.4 LTS