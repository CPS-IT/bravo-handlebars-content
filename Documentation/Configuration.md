# Configuration

## Default Content Element Configuration

The extension provides a default configuration that all content elements can inherit from:

```typoscript
handlebarsContent.default = HANDLEBARSTEMPLATE
handlebarsContent.default {
    # Default variables passed to any partial
    defaultDataVariables = lang
    
    dataProcessing {
        1 = handlebarsLocalization
        1 {
            as = lang
            sources {
                10 = EXT:bravo_handlebars_content/Resources/Private/Language/locallang.xlf
            }
            includePattern = //
            splitChar = .
        }
    }
}
```

## Template Path Configuration

Configure handlebars template paths:

```typoscript
plugin.tx_handlebars {
    view {
        templateRootPaths {
            10 = EXT:your_extension/Resources/Private/Templates/
        }
        partialRootPaths {
            10 = EXT:your_extension/Resources/Private/Partials/
        }
        layoutRootPaths {
            10 = EXT:your_extension/Resources/Private/Layouts/
        }
    }
}
```

## Asset Management

Include CSS and JavaScript assets directly in your TypoScript configuration:

```typoscript
tt_content.textmedia {
    assets {
        css {
            textMediaStyles {
                source = EXT:your_extension/Resources/Public/Css/textmedia.css
                attributes {
                    media = all
                }
                options {
                    priority = false
                }
            }
        }
        javaScript {
            textMediaScript {
                source = EXT:your_extension/Resources/Public/JavaScript/textmedia.js
                attributes {
                    defer = true
                }
                options {
                    priority = false
                }
            }
        }
    }
}
```

## Debug Configuration

Enable debug mode for troubleshooting:

```typoscript
handlebarsContent.default {
    settings {
        debug = 1
    }
}
```

## Content Element Override

Override specific content elements:

```typoscript
# Custom text element with additional processing
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

## Localization Configuration

Configure multiple language sources:

```typoscript
handlebarsContent.default.dataProcessing.1 {
    sources {
        10 = EXT:bravo_handlebars_content/Resources/Private/Language/locallang.xlf
        20 = EXT:your_extension/Resources/Private/Language/locallang.xlf
        30 = EXT:your_extension/Resources/Private/Language/custom.xlf
    }
}
```
