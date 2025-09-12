# Quick Start

## Basic Setup

### 1. Include Static TypoScript

In your root template, include the static template:
- Go to Template → Edit → Includes
- Add "Bravo handlebars content" to "Include static (from extensions)"

### 2. Basic Content Element Configuration

```typoscript
# Simple text content element configuration
tt_content.text =< handlebarsContent.default
tt_content.text {
    templateName = @ce-text
    dataProcessing {
        10 = ceText
    }
}
```

### 3. Create Handlebars Template

Create a handlebars template file at `@ce-text.hbs`:

```handlebars
<div class="ce-text">
    {{#if headlines.header}}
        <{{headlines.layout}} class="ce-text__header">{{headlines.header}}</{{headlines.layout}}>
    {{/if}}
    {{#if bodytext}}
        <div class="ce-text__content">{{{bodytext}}}</div>
    {{/if}}
</div>
```

## Template Path Configuration

Configure your handlebars template paths in TypoScript:

```typoscript
plugin.tx_handlebars {
    view {
        templateRootPaths {
            10 = EXT:your_extension/Resources/Private/Templates/
        }
        partialRootPaths {
            10 = EXT:your_extension/Resources/Private/Partials/
        }
    }
}
```

## First Content Element

1. Create a new content element "Text" on any page
2. Add some content to the header and bodytext fields
3. Save and view the page
4. Your content should now be rendered using the handlebars template

## Next Steps

- [Configuration](Configuration.md) - Learn about advanced configuration options
- [DataProcessors](DataProcessors.md) - Understand data processing capabilities
- [ContentElements](ContentElements.md) - Explore available content elements