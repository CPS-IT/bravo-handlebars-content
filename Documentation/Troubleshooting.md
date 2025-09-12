# Troubleshooting

## Common Issues

### Template Not Found
**Problem:** `Could not find template name for [templateName]`

**Solution:** 
1. Verify template path configuration in handlebars extension
2. Check template name spelling in TypoScript
3. Ensure template file exists at the correct path

**Debugging:**
```typoscript
# Enable debug mode to see template resolution
handlebarsContent.default {
    settings {
        debug = 1
    }
}
```

### Data Processor Not Found
**Problem:** Unknown data processor identifier

**Solution:**
1. Verify processor is registered in Services.yaml
2. Check the identifier matches the tag configuration
3. Clear system cache after adding new processors

**Example Registration:**
```yaml
Your\Extension\DataProcessing\CustomProcessor:
  tags:
    - name: 'data.processor'
      identifier: 'yourCustomProcessor'
```

### Media Files Not Processing
**Problem:** Images or videos not displaying correctly

**Solution:**
1. Check file permissions and accessibility
2. Verify media processor configuration
3. Ensure FAL records are properly configured
4. Check for missing image processing configuration

**Debug Configuration:**
```typoscript
tt_content.textmedia {
    dataProcessing {
        10 = ceTextMedia
        10 {
            settings {
                debug = 1
                fieldConfig {
                    assets {
                        image {
                            # Debug image processing
                            debug = 1
                        }
                    }
                }
            }
        }
    }
}
```

### Localization Not Working
**Problem:** Translation labels not appearing

**Solution:**
1. Verify XLIFF file paths in `handlebarsLocalization` processor
2. Check file permissions on language files  
3. Ensure proper language configuration in TYPO3
4. Clear language cache

**Debug Localization:**
```typoscript
handlebarsContent.default.dataProcessing.1 {
    # Enable debug output
    debug = 1
    
    # Verify file paths
    sources {
        10 = EXT:bravo_handlebars_content/Resources/Private/Language/locallang.xlf
        20 = EXT:your_extension/Resources/Private/Language/locallang.xlf
    }
    
    # Check include pattern
    includePattern = //
}
```

### Asset Files Not Loading
**Problem:** CSS/JS assets not included in page

**Solution:**
1. Verify asset paths in TypoScript configuration
2. Check file permissions and accessibility
3. Ensure AssetCollector is properly configured
4. Clear page cache

**Asset Debug:**
```typoscript
tt_content.textmedia {
    assets {
        css {
            textMediaStyles {
                source = EXT:your_extension/Resources/Public/Css/textmedia.css
                # Debug asset loading
                attributes {
                    data-debug = loaded
                }
            }
        }
    }
}
```

### Performance Issues

**Problem:** Slow page rendering with handlebars content

**Symptoms:**
- Long page load times
- High memory usage
- Database query bottlenecks

**Solutions:**

1. **Enable Template Caching:**
```typoscript
plugin.tx_handlebars {
    settings {
        cache {
            enable = 1
            lifetime = 3600
        }
    }
}
```

2. **Optimize Database Queries:**
```typoscript
# Avoid N+1 queries in database processors
dataProcessing {
    10 = handlebarsDatabaseQuery
    10 {
        # Use proper indexing
        where = indexed_field = 'value'
        # Limit results
        limit = 10
        # Cache results
        cache {
            lifetime = 300
        }
    }
}
```

3. **Reduce Data Processing:**
```typoscript
# Remove unnecessary processors
dataProcessing {
    # Only include needed processors
    10 = ceText
    # Remove debug processors in production
    # 20 = debugProcessor
}
```

### Content Not Updating
**Problem:** Changes to content not reflected on frontend

**Solution:**
1. Clear all TYPO3 caches
2. Clear handlebars template cache
3. Check if page caching is interfering
4. Verify content element configuration

**Cache Clearing Commands:**
```bash
# Clear all caches
vendor/bin/typo3 cache:flush

# Clear specific cache groups
vendor/bin/typo3 cache:flush --group=pages
vendor/bin/typo3 cache:flush --group=system
```

### Memory Limit Exceeded
**Problem:** PHP memory limit exceeded during rendering

**Solution:**
1. Increase PHP memory limit
2. Optimize data processors to use less memory
3. Implement pagination for large datasets
4. Use lazy loading for media content

**Memory Optimization:**
```typoscript
# Limit processed data
dataProcessing {
    10 = handlebarsUnsetPath
    10 {
        # Remove unnecessary data paths
        paths = data.internal, data.debug, data.unused
    }
    20 = handlebarsKeepPath
    20 {
        # Keep only needed data
        paths = data.header, data.bodytext, settings
    }
}
```

## Debug Mode

Enable comprehensive debugging:

```typoscript
handlebarsContent.default {
    settings {
        debug = 1
        debugLevel = 2
        debugOutput = file
        debugFile = typo3temp/logs/handlebars_debug.log
    }
}
```

## Logging

Configure logging in `LocalConfiguration.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Cpsit']['BravoHandlebarsContent'] = [
    'writerConfiguration' => [
        \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
            \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                'logFile' => 'typo3temp/logs/bravo_handlebars_content.log'
            ]
        ]
    ]
];
```

## Performance Monitoring

### Template Rendering Time
```typoscript
# Add timing information
handlebarsContent.default {
    settings {
        profiling = 1
    }
}
```

### Database Query Analysis
```typoscript
# Log database queries
config {
    debug = 1
    admPanel = 1
}
```

## Error Reporting

### Development Environment
```php
// LocalConfiguration.php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = 1;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask'] = '*';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['debugExceptionHandler'] = \TYPO3\CMS\Core\Error\DebugExceptionHandler::class;
```

### Production Environment
```php
// LocalConfiguration.php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] = 0;
$GLOBALS['TYPO3_CONF_VARS']['LOG'] = [
    'Cpsit' => [
        'BravoHandlebarsContent' => [
            'writerConfiguration' => [
                \TYPO3\CMS\Core\Log\LogLevel::ERROR => [
                    \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                        'logFile' => 'typo3temp/logs/errors.log'
                    ]
                ]
            ]
        ]
    ]
];
```

## Common Configuration Mistakes

### Missing Template Registration
```typoscript
# Wrong - template not found
tt_content.text {
    templateName = ce-text
}

# Correct - proper template registration  
tt_content.text {
    templateName = @ce-text
}
```

### Incorrect Data Processor Configuration
```typoscript
# Wrong - processor not found
dataProcessing {
    10 = invalidProcessor
}

# Correct - proper processor identifier
dataProcessing {
    10 = handlebarsMedia
}
```

### Missing Asset Configuration
```typoscript
# Wrong - assets not loaded
tt_content.textmedia {
    # Missing assets configuration
}

# Correct - proper asset configuration
tt_content.textmedia {
    assets {
        css {
            styles {
                source = EXT:your_extension/Resources/Public/Css/styles.css
            }
        }
    }
}
```

## Getting Help

1. **Check Extension Documentation** - Review all documentation files
2. **Enable Debug Mode** - Get detailed error information
3. **Check TYPO3 Logs** - Look for error messages in log files
4. **Verify Configuration** - Double-check TypoScript configuration
5. **Test with Minimal Setup** - Isolate issues by removing complexity
6. **Community Support** - Reach out to TYPO3 community forums
