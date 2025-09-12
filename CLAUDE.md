# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the **bravo-handlebars-content** TYPO3 extension - a content rendering system that integrates handlebars  
templating with TYPO3 12.4 LTS. It provides a comprehensive data processing pipeline and content element  
configurations specifically designed for handlebars-based TYPO3 websites.

## Development Commands

This extension follows standard TYPO3 extension development practices. No custom build commands are defined in the 
composer.json.

### Standard TYPO3 Commands
```bash
# Install dependencies
composer install

# Run tests (if configured in parent project)
composer test

# Follow PSR-12 coding standards for any new code
```

## Architecture Overview

### Core Components

The extension is built around several key architectural components:

1. **HandlebarsTemplateContentObject** (`Classes/Frontend/ContentObject/`) - Main content object that renders 
handlebars templates
2. **Data Processors** (`Classes/DataProcessing/`) - 20+ specialized processors that transform content data for 
templates
3. **Field Processors** (`Classes/DataProcessing/TtContent/Field/`) - Handle individual content element field 
transformations
4. **Media Processors** (`Classes/DataProcessing/Media/`) - Process different media types (images, videos, audio)
5. **Service Layer** (`Classes/Service/`) - Core services like MediaDataService

### Data Processing Pipeline

The extension uses a sophisticated data processing pipeline where multiple processors can be chained together:

- **Core Processors**: `handlebarsLocalization`, `handlebarsMedia`, `handlebarsSerial`, `handlebarsMapFields`
- **Content Element Processors**: `ceText`, `ceTextMedia`, `ceHeader`, `ceUploads`
- **Utility Processors**: `handlebarsContentObjects`, `handlebarsUnset`, `handlebarsKeepPath`, `cropText`
- **Advanced Processors**: `handlebarsDatabaseQuery`, `handlebarsFileLink`, `handlebarsLanguageMenu`

### Dependency Injection

All components are registered via Symfony DI in `Configuration/Services.yaml` with:
- Autowiring enabled for automatic dependency resolution
- Tagged services for processors (`data.processor`) and media processors (`Handlerbars.MediaProcessor`)
- Public services for field processors to allow runtime access

### Template System Integration

The extension integrates with the TYPO3 handlebars ecosystem:
- Uses `cpsit/typo3-handlebars` for core templating functionality  
- Extends with `cpsit/typo3-handlebars-components` for component system
- Provides the `HANDLEBARSTEMPLATE` TypoScript content object

## Key Patterns and Traits

### Common Traits
- **ProcessorVariablesTrait** - Handles processor configuration reading
- **IfAwareProcessorTrait** - Adds conditional processing support  
- **AsAwareProcessorTrait** - Manages target variable naming
- **FieldAwareProcessorTrait** - Provides field processing capabilities

### Media Processing
The extension includes a sophisticated media processing system supporting:
- Images (with responsive srcsets, WebP, lazy loading)
- YouTube/Vimeo videos (with privacy-friendly embedding)
- HTML5 video and audio
- File downloads with automatic type detection

### Content Element Support
Pre-configured support for TYPO3 content elements:
- `text` - Text content with optional headers
- `textmedia` - Text with media gallery
- `header` - Standalone headers with linking
- `uploads` - File downloads and document lists
- `html` - Raw HTML content

## Extension Configuration

### TypoScript Structure
- Main configuration in `Configuration/TypoScript/`
- TCA overrides for content element configurations in `Configuration/TCA/`
- Field mapping configurations via dedicated DataMap classes

### Service Registration
Services are auto-registered with specific identifiers:
- Data processors use the `data.processor` tag with unique identifiers
- Media processors implement `MediaProcessorInterface` and are auto-tagged
- Field processors are registered as public services for runtime access

## Development Guidelines

### Creating Custom Processors
1. Implement `DataProcessorInterface` from TYPO3 core
2. Use appropriate traits (`ProcessorVariablesTrait`, `IfAwareProcessorTrait`, etc.)
3. Register in `Services.yaml` with `data.processor` tag and unique identifier
4. Follow the conditional processing pattern for `if.` configurations

### Template Development
1. Create handlebars templates with `.hbs` extension
2. Use semantic HTML5 elements and BEM CSS methodology
3. Implement responsive design patterns
4. Add proper ARIA attributes for accessibility
5. Reference templates with `@` prefix (e.g., `@ce-text`)

### Field Processing
1. Implement `FieldProcessorInterface` for custom field transformations
2. Register as public service in `Services.yaml`
3. Use in data maps to specify field-specific processing logic

## Dependencies and Integration

### Required Extensions
- TYPO3 CMS Core `^12.4`
- TYPO3 CMS Frontend `^12.4`  
- `cpsit/typo3-handlebars` - Core handlebars integration
- `cpsit/typo3-handlebars-components` - Component system

### Integration Points
- Integrates with TYPO3's content object rendering system
- Uses TYPO3's asset management for CSS/JS injection
- Leverages TYPO3's localization system for multi-language support
- Extends TYPO3's file abstraction layer for media processing

## Testing and Quality

Follow TYPO3 extension development best practices:
- Write unit tests for custom processors using TYPO3 Testing Framework
- Follow PSR-12 coding standards
- Use proper type hints and return types (PHP 8.1+)
- Document public APIs with appropriate docblocks
