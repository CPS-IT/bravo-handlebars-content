# Data Processors

The extension provides numerous data processors for different content processing needs:

## Core Data Processors

### handlebarsLocalization
Processes localization data from XLIFF files.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsLocalization
    10 {
        as = lang
        sources {
            10 = EXT:your_extension/Resources/Private/Language/locallang.xlf
            20 = EXT:your_extension/Resources/Private/Language/custom.xlf
        }
        includePattern = //
        splitChar = .
    }
}
```

**Configuration Options:**

| Option | Type | Description | Default |
|--------|------|-------------|---------|
| `as` | string | Target variable name | `lang` |
| `sources` | array | Array of XLIFF file paths | - |
| `includePattern` | string | Regex pattern for included labels | `//` |
| `splitChar` | string | Character to split keys into arrays | `.` |

### handlebarsMedia
Processes media files (images, videos, audio) with advanced configuration options.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsMedia
    10 {
        data = files
        as = processedMedia
        settings {
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
            youtube {
                width = 0
                height = 0
                controls = 1
                no-cookie = 1
                modestbranding = 1
                previewImage {
                    width = 320
                    height = 180
                    loading = lazy
                }
            }
            vimeo {
                width = 0
                height = 0
                controls = 1
                previewImage {
                    width = 320
                    height = 180
                    loading = lazy
                }
            }
        }
    }
}
```

### handlebarsSerial
Executes multiple data processors in sequence.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsSerial
    10 {
        processors {
            10 = handlebarsMedia
            10 {
                data = files
                as = media
            }
            20 = handlebarsMapFields
            20 {
                # field mapping configuration
            }
        }
    }
}
```

### handlebarsMapFields
Maps and transforms field values.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsMapFields
    10 {
        mapping {
            title = header
            content = bodytext
            modified = tstamp
        }
    }
}
```

## Content Element Specific Processors

### ceText
Specialized processor for text content elements.

**Usage:**
```typoscript
tt_content.text {
    dataProcessing {
        10 = ceText
    }
}
```

### ceTextMedia  
Processes text and media content elements with comprehensive media handling.

**Usage:**
```typoscript
tt_content.textmedia {
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

### ceHeader
Processes header content elements.

**Usage:**
```typoscript
tt_content.header {
    dataProcessing {
        10 = ceHeader
    }
}
```

### ceUploads
Processes file uploads and download lists.

**Usage:**
```typoscript  
tt_content.uploads {
    dataProcessing {
        10 = ceUploads
    }
}
```

## Utility Processors

### handlebarsContentObjects
Renders TypoScript content objects.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsContentObjects
    10 {
        as = menu
        10 = HMENU
        10 {
            # HMENU configuration
        }
    }
}
```

### handlebarsTypoScriptObjectPath
Accesses nested TypoScript object paths.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsTypoScriptObjectPath
    10 {
        as = config
        path = lib.config.settings
    }
}
```

### handlebarsUnset
Removes variables from the data array.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsUnset
    10 {
        fields = unwantedField1, unwantedField2
    }
}
```

### handlebarsKeepPath
Keeps only specified paths in the data array.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsKeepPath
    10 {
        paths = data.header, data.bodytext, settings
    }
}
```

### handlebarsUnsetPath
Removes specified paths from the data array.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsUnsetPath
    10 {
        paths = data.internal, data.debug
    }
}
```

## Advanced Processors

### handlebarsDatabaseQuery
Executes custom database queries.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsDatabaseQuery
    10 {
        as = relatedContent
        table = tt_content
        where = pid = 123 AND CType = 'text'
        orderBy = sorting ASC
        limit = 5
    }
}
```

### handlebarsFileLink
Processes file links and downloads.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsFileLink
    10 {
        data = media
        as = fileLinks
    }
}
```

### handlebarsLink
Processes various types of links (page, external, email, etc.).

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsLink
    10 {
        data = header_link
        as = headerLink
    }
}
```

### handlebarsLanguageMenu
Generates language menu data.

**Usage:**
```typoscript
dataProcessing {
    10 = handlebarsLanguageMenu
    10 {
        as = languageMenu
        languages = 0, 1, 2
    }
}
```

### cropText
Crops text to specified length with ellipsis.

**Usage:**
```typoscript
dataProcessing {
    10 = cropText
    10 {
        field = bodytext
        maxLength = 150
        as = excerpt
        append = ...
    }
}
```