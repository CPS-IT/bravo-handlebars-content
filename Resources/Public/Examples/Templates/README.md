# Example Templates

This directory contains example Handlebars templates for the `bravo-handlebars-content` extension. These templates 
demonstrate how to create content element templates that work with the extension's data processors.

## Available Templates

### Content Elements

- **ce-text.hbs** - Text content element with optional header
- **ce-textmedia.hbs** - Text with media (images, videos, audio)  
- **ce-header.hbs** - Standalone header element
- **ce-html.hbs** - Raw HTML content element
- **ce-uploads.hbs** - File downloads/uploads list
- **ce-menu.hbs** - Navigation menu
- **ce-shortcut.hbs** - Content shortcuts/references
- **ce-plugin.hbs** - Plugin/extension content

### Partials

- **partials/media/item.hbs** - Media item partial for different media types (image, YouTube, Vimeo, audio, video)

## Usage

1. Copy the templates you need to your extension's template directory
2. Configure the template paths in your TypoScript:

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

3. Configure your content elements to use the templates:

```typoscript
tt_content.text =< handlebarsContent.default
tt_content.text {
    templateName = @ce-text
    dataProcessing {
        10 = ceText
    }
}
```

## Template Structure

### Content Element Wrapper
Each content element template includes:
- Semantic CSS classes following BEM methodology
- Conditional `id` attribute using content element UID
- Frame classes for styling variations
- Proper HTML5 semantic structure

### Data Structure
Templates expect data processed by the extension's data processors:
- `headlines` - Processed header data with layout and linking
- `bodytext` - Processed RTE content 
- `assets` - Processed media files
- `frame_class` - CSS classes for wrapper styling
- `uid` - Content element unique identifier

### Media Handling
Media items are processed through the `handlebarsMedia` processor and rendered using the media/item partial. 
Supported media types:
- **Images** - With responsive srcset and lazy loading
- **YouTube videos** - With privacy-friendly embedding
- **Vimeo videos** - With preview images
- **Audio files** - HTML5 audio player
- **Video files** - HTML5 video player
- **Other files** - Download links

## Customization

These templates are starting points. Customize them for your project by:

1. **Styling** - Add your own CSS classes and structure
2. **Content** - Modify the HTML output to match your design
3. **Data** - Add custom data processors for additional functionality
4. **Accessibility** - Enhance ARIA attributes and semantic markup

## Best Practices

- Use semantic HTML5 elements
- Include proper ARIA attributes for accessibility
- Implement responsive design patterns
- Follow consistent naming conventions
- Add loading="lazy" for images below the fold
- Include fallback content for media elements
- Escape user content appropriately (use `{{}}` for escaped, `{{{}}}` for unescaped HTML)
