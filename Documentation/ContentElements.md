# Content Elements

The extension provides pre-configured support for standard TYPO3 content elements:

## Supported Content Elements

* [x] **text** - Text content element
* [x] **textmedia** - Text with media (images, videos, audio)  
* [x] **header** - Header/headline element
* [x] **html** - HTML content element
* [x] **list** - Plugin/list content element
* [x] **menuPages** - Page menu
* [x] **menuSubpages** - Subpage menu
* [x] **uploads** - File uploads/downloads
* [x] **shortcut** - Content shortcuts

## Content Element Configuration

Each content element inherits from `handlebarsContent.default` and can be customized:

```typoscript
# Example: Custom text element with additional processing
tt_content.text =< handlebarsContent.default
tt_content.text {
    templateName = @ce-text-custom
    
    # Add custom data processing
    dataProcessing {
        10 = ceText
        20 = handlebarsMapFields
        20 {
            mapping {
                customTitle = header
                customContent = bodytext
            }
        }
    }
    
    # Add custom assets
    assets {
        css {
            customTextStyles {
                source = EXT:your_extension/Resources/Public/Css/custom-text.css
            }
        }
    }
}
```

## Field Mapping

Content elements use field processors to transform TYPO3 database fields into handlebars-friendly data structures:

| TYPO3 Field | Processor | Description |
|------------|-----------|-------------|
| `bodytext` | BodytextProcessor | Processes RTE content with parseFunc |
| `header` | PassThrough | Passes header text unchanged |
| `header_layout` | HeaderLayoutProcessor | Converts header layout to HTML tag |
| `header_link` | HeaderLinkProcessor | Processes header links |
| `space_before` | SpaceBeforeProcessor | Handles space before element |
| `frame_class` | FrameClassProcessor | Processes frame/wrapper CSS classes |
| `assets` | MediaProcessor | Processes media files (images, videos) |

## Content Element Examples

### Text Element

**TypoScript Configuration:**
```typoscript
tt_content.text =< handlebarsContent.default
tt_content.text {
    templateName = @ce-text
    dataProcessing {
        10 = ceText
    }
}
```

**Template Data Structure:**
```json
{
    "uid": 123,
    "headlines": {
        "header": "Sample Header",
        "layout": "h2",
        "link": {
            "url": "https://example.com",
            "target": "_blank",
            "title": "Link title"
        }
    },
    "bodytext": "<p>Processed RTE content</p>",
    "frame_class": "custom-frame-class"
}
```

### Text & Media Element

**TypoScript Configuration:**
```typoscript
tt_content.textmedia =< handlebarsContent.default
tt_content.textmedia {
    templateName = @ce-textmedia
    dataProcessing {
        10 = ceTextMedia
        10 {
            settings {
                fieldConfig {
                    assets {
                        image {
                            cropVariants {
                                default {
                                    srcset {
                                        sourceS.maxWidth = 320
                                        sourceM.maxWidth = 640
                                        sourceL.maxWidth = 1280
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
```

**Template Data Structure:**
```json
{
    "uid": 124,
    "headlines": {
        "header": "Media Gallery",
        "layout": "h3"
    },
    "bodytext": "<p>Caption text</p>",
    "assets": [
        {
            "type": "image",
            "url": "/path/to/image.jpg",
            "alt": "Image description",
            "srcset": "/path/to/image-320.jpg 320w, /path/to/image-640.jpg 640w",
            "sizes": "(max-width: 320px) 320px, 640px",
            "loading": "lazy"
        },
        {
            "type": "youtube",
            "videoId": "dQw4w9WgXcQ",
            "previewImage": {
                "url": "/path/to/preview.jpg",
                "width": 320,
                "height": 180
            }
        }
    ],
    "gallery": {
        "position": "above",
        "columns": 2
    }
}
```

### Upload/Downloads Element

**TypoScript Configuration:**
```typoscript
tt_content.uploads =< handlebarsContent.default
tt_content.uploads {
    templateName = @ce-uploads
    dataProcessing {
        10 = ceUploads
    }
}
```

**Template Data Structure:**
```json
{
    "uid": 125,
    "headlines": {
        "header": "Downloads",
        "layout": "h3"
    },
    "fileLinks": [
        {
            "url": "/path/to/document.pdf",
            "title": "Sample Document",
            "description": "PDF document description",
            "extension": "pdf",
            "size": "1.2 MB",
            "icon": "/typo3/sysext/frontend/Resources/Public/Icons/FileIcons/pdf.png"
        }
    ]
}
```

## Custom Content Elements

To create custom content elements:

1. **Define TypoScript Configuration:**
```typoscript
tt_content.my_custom_element =< handlebarsContent.default
tt_content.my_custom_element {
    templateName = @ce-custom-element
    dataProcessing {
        10 = handlebarsSerial
        10 {
            processors {
                10 = handlebarsMapFields
                10 {
                    mapping {
                        customField = tx_myext_custom_field
                        anotherField = tx_myext_another_field
                    }
                }
                20 = handlebarsMedia
                20 {
                    data = media
                    as = processedMedia
                }
            }
        }
    }
}
```

2. **Create Handlebars Template:**
```handlebars
<div class="ce-custom-element{{#if frame_class}} {{frame_class}}{{/if}}"{{#if uid}} id="c{{uid}}"{{/if}}>
    {{#if headlines.header}}
        <{{headlines.layout}} class="ce-custom-element__header">
            {{headlines.header}}
        </{{headlines.layout}}>
    {{/if}}
    
    {{#if customField}}
        <div class="ce-custom-element__custom">
            {{customField}}
        </div>
    {{/if}}
    
    {{#if processedMedia}}
        <div class="ce-custom-element__media">
            {{#each processedMedia}}
                {{>media/item this}}
            {{/each}}
        </div>
    {{/if}}
</div>
```

3. **Add TCA Configuration** (in your extension):
```php
// Configuration/TCA/Overrides/tt_content.php
$GLOBALS['TCA']['tt_content']['types']['my_custom_element'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
            tx_myext_custom_field,
            tx_myext_another_field,
            media,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
    '
];
```