# API Reference

## Core Interfaces

### DataProcessorInterface (TYPO3 Core)
Standard TYPO3 data processor interface.

```php
interface DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array;
}
```

### FieldProcessorInterface
Interface for field-specific processors.

```php
interface FieldProcessorInterface
{
    public function process(string $fieldName, array $data, array $variables): array;
}
```

### MediaProcessorInterface  
Interface for media file processors.

```php
interface MediaProcessorInterface
{
    public function canProcess(FileInterface $file): bool;
    public function process(FileInterface $file, array $configuration): array;
}
```

### DataMapInterface
Interface for data mapping/transformation.

```php
interface DataMapInterface
{
    public function map(array $data): array;
}
```

## Core Classes

### HandlebarsTemplateContentObject
Main content object for rendering handlebars templates.

**Key Methods:**
- `render(array $conf = []): string` - Renders the template
- `resolveTemplateName(array $conf): string` - Resolves template name
- `addPageAssets(array $conf): void` - Adds CSS/JS assets to page

**Properties:**
- `$assetCollector: AssetCollector` - TYPO3 asset collector
- `$contentDataProcessor: ContentDataProcessor` - Content data processor
- `$renderer: HandlebarsRenderer` - Handlebars template renderer

### TtContentDataProcessor
Base processor for tt_content records.

**Key Properties:**
- `DEFAULT_FIELDS` - Array mapping field names to processors

**Key Methods:**
- `process()` - Processes content element data
- `processField()` - Processes individual fields

### MediaDataService
Service for processing media files with registered media processors.

**Key Methods:**
- `process(FileInterface $file, array $config): array` - Process media file
- `canProcess(FileInterface $file): bool` - Check if file can be processed
- `getProcessors(): array` - Get registered processors

## Traits

### ProcessorVariablesTrait
Handles common processor configuration reading.

**Methods:**
- `readSettingsFromConfig(array $config): void` - Read settings from TypoScript
- `getSettings(): array` - Get processed settings

**Properties:**
- `$settings: array` - Processed configuration settings

### IfAwareProcessorTrait
Adds conditional processing support.

**Methods:**
- `shouldProcess(array $config): bool` - Check if processor should run
- `checkCondition(array $config): bool` - Evaluate if conditions

### AsAwareProcessorTrait
Handles target variable naming.

**Methods:**
- `determineTargetVariableName(array $config, string $default = 'data'): string`
- `getTargetVariable(): string`

### FieldMappingTrait
Provides field mapping functionality.

**Methods:**
- `mapFields(array $data, array $mapping): array`
- `getFieldValue(array $data, string $fieldName): mixed`

## Exception Classes

### InvalidConfigurationException
Thrown when configuration is invalid or missing.

**Properties:**
- `$message: string` - Error message
- `$code: int` - Error code

### InvalidClassException  
Thrown when a required class cannot be instantiated.

### InvalidValueException
Thrown when a value does not meet requirements.

## Configuration Objects

### FieldProcessorConfiguration
Data transfer object for field processor configuration.

**Properties:**
- `$fieldName: string` - Field name being processed
- `$processorClass: string` - Processor class name
- `$configuration: array` - Processor configuration

### MediaConfiguration
Configuration object for media processing.

**Properties:**
- `$type: string` - Media type (image, video, etc.)
- `$settings: array` - Type-specific settings
- `$cropVariants: array` - Image crop variants

## Data Structures

### ProcessedMedia
Structure for processed media items.

```php
[
    'type' => 'image|youtube|vimeo|audio|video|file',
    'url' => 'string',
    'title' => 'string',
    'description' => 'string',
    'alt' => 'string', // for images
    'srcset' => 'string', // for responsive images
    'sizes' => 'string', // for responsive images
    'loading' => 'lazy|eager', // loading strategy
    'width' => 'int',
    'height' => 'int',
    'videoId' => 'string', // for YouTube/Vimeo
    'previewImage' => [...], // for videos
    'mimeType' => 'string',
    'fileSize' => 'int',
    'extension' => 'string'
]
```

### ProcessedLink
Structure for processed links.

```php
[
    'url' => 'string',
    'target' => '_blank|_self|_parent|_top',
    'title' => 'string',
    'class' => 'string',
    'additionalAttributes' => [...]
]
```

### Headlines
Structure for processed headlines.

```php
[
    'header' => 'string',
    'layout' => 'h1|h2|h3|h4|h5|h6',
    'link' => ProcessedLink|null
]
```

## Services

### ContentElementDataService
Service for processing content element data.

**Methods:**
- `processContentElement(array $data): array`
- `getFieldProcessors(): array`
- `processFields(array $data, array $fields): array`

### LocalizationDataService
Service for processing localization data.

**Methods:**
- `processLocalizationFiles(array $sources): array`
- `parseXliffFile(string $filePath): array`
- `filterLabels(array $labels, string $pattern): array`

## Hooks and Events

### ContentObjectRendererHook
Hook for modifying content object rendering.

**Methods:**
- `preProcess(array &$data, array $conf): void`
- `postProcess(string &$content, array $data, array $conf): void`

### MediaProcessingEvent
Event fired during media processing.

**Properties:**
- `$file: FileInterface` - File being processed
- `$configuration: array` - Processing configuration
- `$result: array` - Processing result (mutable)

## Utility Classes

### TemplatePathResolver
Resolves template paths and names.

**Methods:**
- `resolve(string $templateName): string`
- `getTemplatePaths(): array`
- `addTemplatePath(string $path): void`

### ConfigurationReader
Reads and validates TypoScript configuration.

**Methods:**
- `read(array $config, string $path): mixed`
- `validate(array $config, array $schema): bool`
- `merge(array ...$configs): array`