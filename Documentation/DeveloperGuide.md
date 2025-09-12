# Developer Guide

## Creating Custom Data Processors

To create a custom data processor, implement the `DataProcessorInterface`:

```php
<?php

namespace Your\Extension\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class CustomProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // Check if condition allows processing
        if (isset($processorConfiguration['if.']) && 
            !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }
        
        // Your processing logic here
        $targetVariableName = $cObj->stdWrapValue(
            'as', 
            $processorConfiguration, 
            'customData'
        );
        
        $processedData[$targetVariableName] = [
            'customValue' => 'processed data'
        ];
        
        return $processedData;
    }
}
```

Register your processor in `Services.yaml`:

```yaml
Your\Extension\DataProcessing\CustomProcessor:
  tags:
    - name: 'data.processor'
      identifier: 'yourCustomProcessor'
```

## Creating Custom Field Processors

Field processors handle individual content element fields:

```php
<?php

namespace Your\Extension\DataProcessing\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;

class CustomFieldProcessor implements FieldProcessorInterface
{
    public function process(string $fieldName, array $data, array $variables): array
    {
        $fieldValue = $data['your_field'] ?? '';
        
        // Process the field value
        $processedValue = strtoupper($fieldValue);
        
        $variables[$fieldName] = $processedValue;
        return $variables;
    }
}
```

## Using Traits

The extension provides several useful traits:

### ProcessorVariablesTrait
Handles common processor configuration:

```php
use Cpsit\BravoHandlebarsContent\DataProcessing\ProcessorVariablesTrait;

class YourProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;
    
    public function process(...): array
    {
        $this->readSettingsFromConfig($processorConfiguration);
        // Access $this->settings for configuration
    }
}
```

### IfAwareProcessorTrait
Adds conditional processing support:

```php
use Cpsit\BravoHandlebarsContent\DataProcessing\IfAwareProcessorTrait;

class YourProcessor implements DataProcessorInterface  
{
    use IfAwareProcessorTrait;
    
    public function process(...): array
    {
        if (!$this->shouldProcess($processorConfiguration)) {
            return $processedData;
        }
        // Processing logic
    }
}
```

### AsAwareProcessorTrait
Handles target variable naming:

```php
use Cpsit\BravoHandlebarsContent\DataProcessing\AsAwareProcessorTrait;

class YourProcessor implements DataProcessorInterface
{
    use AsAwareProcessorTrait;
    
    public function process(...): array
    {
        $targetVariableName = $this->determineTargetVariableName($processorConfiguration);
        $processedData[$targetVariableName] = $yourData;
        return $processedData;
    }
}
```

## Handlebars Template Development

### Template Structure

Organize your handlebars templates in a logical structure:

```
Resources/Private/Templates/
├── Components/
│   ├── ce-text.hbs
│   ├── ce-textmedia.hbs
│   └── ce-header.hbs
├── Partials/
│   ├── media/
│   │   ├── image.hbs
│   │   ├── video.hbs
│   │   └── audio.hbs
│   └── navigation/
│       └── language-menu.hbs
└── Layouts/
    └── Default.hbs
```

### Template Examples

**Text Element Template** (`@ce-text.hbs`):
```handlebars
<div class="ce-text{{#if frame_class}} {{frame_class}}{{/if}}"{{#if uid}} id="c{{uid}}"{{/if}}>
    {{#if headlines.header}}
        <{{headlines.layout}} class="ce-text__header">
            {{#if headlines.link}}
                <a href="{{headlines.link.url}}"{{#if headlines.link.target}} target="{{headlines.link.target}}"{{/if}}>
                    {{headlines.header}}
                </a>
            {{else}}
                {{headlines.header}}
            {{/if}}
        </{{headlines.layout}}>
    {{/if}}
    
    {{#if bodytext}}
        <div class="ce-text__content">
            {{{bodytext}}}
        </div>
    {{/if}}
</div>
```

**Text/Media Element Template** (`@ce-textmedia.hbs`):
```handlebars
<div class="ce-textmedia{{#if frame_class}} {{frame_class}}{{/if}}"{{#if uid}} id="c{{uid}}"{{/if}}>
    {{#if headlines.header}}
        <{{headlines.layout}} class="ce-textmedia__header">{{headlines.header}}</{{headlines.layout}}>
    {{/if}}
    
    {{#if assets}}
        <div class="ce-textmedia__media">
            {{#each assets}}
                {{>media/item this}}
            {{/each}}
        </div>
    {{/if}}
    
    {{#if bodytext}}
        <div class="ce-textmedia__content">
            {{{bodytext}}}
        </div>
    {{/if}}
</div>
```

**Media Item Partial** (`media/item.hbs`):
```handlebars
{{#if (eq type 'image')}}
    <figure class="media-item media-item--image">
        <img src="{{url}}" alt="{{alt}}" 
             {{#if srcset}}srcset="{{srcset}}"{{/if}}
             {{#if sizes}}sizes="{{sizes}}"{{/if}}
             loading="{{loading}}">
        {{#if caption}}
            <figcaption>{{caption}}</figcaption>
        {{/if}}
    </figure>
{{else if (eq type 'youtube')}}
    <div class="media-item media-item--youtube" data-video-id="{{videoId}}">
        {{#if previewImage}}
            <img src="{{previewImage.url}}" alt="{{labels.accessibility}}" class="video-preview">
        {{/if}}
        <button class="video-play-button" aria-label="{{labels.accessibility}}">
            Play Video
        </button>
        {{#if labels.textHTML}}
            <div class="cookie-disclaimer">
                {{{labels.textHTML}}}
            </div>
        {{/if}}
    </div>
{{else if (eq type 'vimeo')}}
    <div class="media-item media-item--vimeo" data-video-id="{{videoId}}">
        {{#if previewImage}}
            <img src="{{previewImage.url}}" alt="{{labels.accessibility}}" class="video-preview">
        {{/if}}
        <button class="video-play-button" aria-label="{{labels.accessibility}}">
            Play Video  
        </button>
    </div>
{{else if (eq type 'audio')}}
    <div class="media-item media-item--audio">
        <audio {{#if controls}}controls{{/if}} {{#if loop}}loop{{/if}}>
            <source src="{{url}}" type="{{mimeType}}">
            Your browser does not support the audio element.
        </audio>
    </div>
{{/if}}
```

## Extension Architecture

### Core Components

The extension is built around several key components:

1. **HandlebarsTemplateContentObject** - Main content object for rendering
2. **Data Processors** - Transform content data for templates
3. **Field Processors** - Handle individual field transformations
4. **Media Processors** - Process different media types
5. **Configuration System** - Manage TypoScript configuration

### Class Hierarchy

```
HandlebarsTemplateContentObject
├── Uses ProcessorVariablesTrait
├── Depends on HandlebarsRenderer
├── Depends on ContentDataProcessor
└── Depends on AssetCollector

DataProcessors
├── Implement DataProcessorInterface
├── May use ProcessorVariablesTrait
├── May use IfAwareProcessorTrait
└── May use AsAwareProcessorTrait

FieldProcessors
└── Implement FieldProcessorInterface

MediaProcessors
└── Implement MediaProcessorInterface
```

## Best Practices

### Performance
- Use appropriate caching strategies
- Minimize database queries in processors  
- Pre-process complex data transformations
- Use lazy loading for media content

### Security
- Always escape user input in templates
- Validate processor configuration
- Sanitize file uploads and media content
- Use secure media embedding practices

### Maintainability
- Follow SOLID principles
- Use dependency injection
- Write unit tests for processors
- Document custom implementations
- Follow PSR-12 coding standards

### Template Development
- Use semantic HTML5 elements
- Follow BEM CSS methodology
- Implement responsive design patterns
- Add proper ARIA attributes for accessibility
- Include loading="lazy" for images below the fold
