# Comprehensive Testing Plan - TYPO3 Bravo Handlebars Content Extension

**Date**: 2025-09-12  
**Target Coverage**: 100% (minimum 80% acceptable)  
**Framework**: TYPO3 Testing Framework v8.x + PHPUnit 10.5  
**TYPO3 Version**: 12.4 LTS  

## Testing Strategy Overview

This comprehensive testing plan establishes a multi-layered testing approach to achieve 100% code coverage while ensuring robust functionality and maintainability of the bravo-handlebars-content extension.

### Testing Pyramid Structure

1. **Unit Tests (70% of total tests)**
   - Fast, isolated tests of individual classes/methods
   - Mock external dependencies
   - Test business logic and algorithms

2. **Integration Tests (20% of total tests)**
   - Test component interactions
   - Database operations with test fixtures
   - Service integrations within TYPO3 context

3. **Functional Tests (10% of total tests)**
   - End-to-end testing of complete workflows
   - Template rendering with real TYPO3 environment
   - Content element processing pipeline

## Testing Framework Configuration

### Required Dependencies

```json
{
  "require-dev": {
    "typo3/testing-framework": "^8.0.9",
    "phpunit/phpunit": "^10.5",
    "friendsofphp/php-cs-fixer": "^3.45",
    "phpstan/phpstan": "^2.0",
    "roave/security-advisories": "dev-latest",
    "phpunit/php-code-coverage": "^11.0"
  }
}
```

### Testing Framework Setup

#### Directory Structure
```
Tests/
├── Unit/                          # Unit tests
│   ├── DataProcessing/           
│   │   ├── TextDataProcessorTest.php
│   │   ├── MediaProcessorTest.php
│   │   └── SerialDataProcessorTest.php
│   ├── Frontend/
│   │   └── ContentObject/
│   │       └── HandlebarsTemplateContentObjectTest.php
│   ├── Service/
│   │   ├── MediaDataServiceTest.php
│   │   └── LinkServiceTest.php
│   └── Utility/
│       └── StringUtilityTest.php
├── Functional/                   # Functional tests
│   ├── DataProcessing/
│   │   └── ProcessingPipelineTest.php
│   ├── Frontend/
│   │   └── ContentRenderingTest.php
│   └── Integration/
│       └── ExtensionConfigurationTest.php
└── Fixtures/                     # Test fixtures
    ├── Database/
    │   └── tt_content.xml
    ├── Files/
    │   ├── test-image.jpg
    │   └── test-video.mp4
    └── Templates/
        └── test-template.hbs
```

## Testing Configuration Files

### PHPUnit Configuration (`Tests/Build/phpunit/UnitTests.xml`)

```xml
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="../../vendor/phpunit/phpunit/phpunit.xsd"
    backupGlobals="true"
    bootstrap="../../vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php"
    colors="true"
    convertDeprecationsToExceptions="false"
    convertErrorsToExceptions="true"
    convertNoticesToExceptions="false"
    convertWarningsToExceptions="true"
    forceCoversAnnotation="false"
    processIsolation="false"
    stopOnError="false"
    stopOnFailure="false"
    stopOnIncomplete="false"
    stopOnSkipped="false"
    verbose="false">
    
    <testsuites>
        <testsuite name="Unit">
            <directory>../../Tests/Unit</directory>
        </testsuite>
    </testsuites>
    
    <source>
        <include>
            <directory suffix=".php">../../Classes</directory>
        </include>
    </source>
    
    <coverage>
        <report>
            <html outputDirectory="../../var/coverage/html"/>
            <clover outputFile="../../var/coverage/clover.xml"/>
            <text outputFile="../../var/coverage/coverage.txt"/>
        </report>
    </coverage>
</phpunit>
```

### Functional Tests Configuration (`Tests/Build/phpunit/FunctionalTests.xml`)

```xml
<phpunit
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="../../vendor/phpunit/phpunit/phpunit.xsd"
    backupGlobals="true"
    bootstrap="../../vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTestsBootstrap.php"
    colors="true">
    
    <testsuites>
        <testsuite name="Functional">
            <directory>../../Tests/Functional</directory>
        </testsuite>
    </testsuites>
    
    <source>
        <include>
            <directory suffix=".php">../../Classes</directory>
        </include>
    </source>
</phpunit>
```

## Unit Testing Strategy

### Priority Testing Targets

#### **High Priority (Critical Path Components)**

1. **HandlebarsTemplateContentObject** (`Classes/Frontend/ContentObject/`)
   - **Coverage Target**: 100%
   - **Test Focus**:
     - Template rendering with various configurations
     - Asset injection mechanisms
     - Error handling for missing templates
     - Configuration processing

2. **MediaDataService** (`Classes/Service/`)
   - **Coverage Target**: 100%
   - **Test Focus**:
     - Media processor selection logic
     - File type detection
     - Error handling for unsupported formats
     - Processor chain execution

3. **Core Data Processors** (`Classes/DataProcessing/`)
   - **Coverage Target**: 100%
   - **Test Components**:
     - `TextDataProcessor`
     - `TextMediaDataProcessor`
     - `MediaProcessor`
     - `LocalizationDataProcessor`
     - `DatabaseQueryProcessor`

#### **Medium Priority (Supporting Components)**

4. **Field Processors** (`Classes/DataProcessing/TtContent/Field/`)
   - **Coverage Target**: 95%
   - **Test Focus**:
     - Individual field transformation logic
     - Data type conversions
     - Validation and sanitization

5. **Utility Classes** (`Classes/Utility/`)
   - **Coverage Target**: 100%
   - **Test Focus**:
     - String manipulation functions
     - Helper methods
     - Static utility functions

#### **Lower Priority (Configuration and DTOs)**

6. **Exception Classes** (`Classes/Exception/`)
   - **Coverage Target**: 90%
   - **Test Focus**:
     - Exception instantiation
     - Message formatting

7. **Domain Models** (`Classes/Domain/Model/`)
   - **Coverage Target**: 95%
   - **Test Focus**:
     - Data integrity
     - Getter/setter functionality

### Unit Test Examples

#### Example 1: Service Class Test

```php
<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\Service;

use Cpsit\BravoHandlebarsContent\Service\MediaDataService;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class MediaDataServiceTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;
    
    private MediaDataService $subject;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new MediaDataService();
    }
    
    #[Test]
    public function getProcessorReturnsCorrectProcessorForImageFile(): void
    {
        $file = $this->createMock(FileInterface::class);
        $file->method('getMimeType')->willReturn('image/jpeg');
        
        $processor = $this->subject->getProcessor($file);
        
        self::assertInstanceOf(ImageProcessor::class, $processor);
    }
    
    #[Test]
    public function getProcessorThrowsExceptionForUnsupportedFormat(): void
    {
        $this->expectException(UnsupportedMediaTypeException::class);
        
        $file = $this->createMock(FileInterface::class);
        $file->method('getMimeType')->willReturn('application/unknown');
        
        $this->subject->getProcessor($file);
    }
}
```

#### Example 2: Data Processor Test

```php
<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\TextDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TextDataProcessorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;
    
    private TextDataProcessor $subject;
    private ContentObjectRenderer $contentObjectRenderer;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->subject = new TextDataProcessor();
    }
    
    #[Test]
    public function processReturnsProcessedDataWithTextContent(): void
    {
        $processorConfiguration = ['as' => 'textData'];
        $processedData = ['data' => ['bodytext' => 'Test content']];
        
        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
        
        self::assertArrayHasKey('textData', $result);
        self::assertSame('Test content', $result['textData']['bodytext']);
    }
    
    #[DataProvider('headerDataProvider')]
    public function processHandlesHeaderDataCorrectly(array $inputData, array $expectedOutput): void
    {
        $processorConfiguration = ['as' => 'textData'];
        $processedData = ['data' => $inputData];
        
        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
        
        self::assertEquals($expectedOutput, $result['textData']['headlines']);
    }
    
    public static function headerDataProvider(): array
    {
        return [
            'h1 header' => [
                ['header' => 'Test Header', 'header_layout' => 1],
                ['header' => 'Test Header', 'layout' => 'h1']
            ],
            'h2 header' => [
                ['header' => 'Another Header', 'header_layout' => 2],
                ['header' => 'Another Header', 'layout' => 'h2']
            ]
        ];
    }
}
```

## Integration Testing Strategy

### Database Integration Tests

```php
<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Functional\DataProcessing;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ProcessingPipelineTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'cpsit/typo3-handlebars',
        'cpsit/typo3-handlebars-components',
        'cpsit/bravo-handlebars-content'
    ];
    
    protected array $configurationToUseInTestInstance = [
        'EXTENSIONS' => [
            'bravo_handlebars_content' => [
                'debug' => true
            ]
        ]
    ];
    
    #[Test]
    public function dataProcessingPipelineProcessesContentElementCorrectly(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Database/tt_content.csv');
        
        // Test the complete data processing pipeline
        $contentObject = $this->get(ContentObjectRenderer::class);
        $result = $contentObject->cObjGetSingle('HANDLEBARSTEMPLATE', [
            'templateName' => '@ce-text',
            'dataProcessing.' => [
                '10' => 'ceText'
            ]
        ]);
        
        self::assertStringContainsString('Test Header', $result);
        self::assertStringContainsString('Test content', $result);
    }
}
```

## Functional Testing Strategy

### Template Rendering Tests

```php
<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Functional\Frontend;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentRenderingTest extends FunctionalTestCase
{
    #[Test]
    public function contentElementRenderingProducesValidHtml(): void
    {
        // Test complete rendering workflow
        $this->setUpFrontendRootPage(1);
        $response = $this->executeFrontendRequest(
            (new InternalRequest())->withPageId(1)
        );
        
        $html = (string)$response->getBody();
        
        // Validate HTML structure
        self::assertStringContainsString('<div class="ce-text">', $html);
        self::assertRegExp('/<h[1-6].*?>.*?<\/h[1-6]>/', $html);
    }
}
```

## Coverage Requirements and Metrics

### Minimum Coverage Targets by Component

| Component Type | Coverage Target | Rationale |
|----------------|-----------------|-----------|
| Service Classes | 100% | Critical business logic |
| Data Processors | 100% | Core functionality |
| Content Objects | 100% | Main rendering pipeline |
| Utility Classes | 100% | Pure functions, easily testable |
| Field Processors | 95% | Numerous simple methods |
| Exception Classes | 90% | Simple error classes |
| Domain Models | 95% | Data containers with logic |

### Quality Gates

#### Code Coverage Thresholds
- **Unit Tests**: Minimum 95% line coverage
- **Integration Tests**: Minimum 90% of service interactions
- **Functional Tests**: 100% of public API endpoints
- **Overall Target**: 100% (80% minimum acceptable)

#### Additional Quality Metrics
- **Cyclomatic Complexity**: < 10 per method
- **CRAP Index**: < 30 per method
- **Mutation Testing Score**: > 85%
- **Test Execution Time**: < 60 seconds total

### Performance Benchmarks

#### Test Suite Performance Targets
- **Unit Tests**: < 30 seconds execution
- **Integration Tests**: < 60 seconds execution  
- **Functional Tests**: < 120 seconds execution
- **Total Suite**: < 5 minutes including coverage

## Test Data Management

### Fixtures Structure

#### Database Fixtures (`Tests/Fixtures/Database/`)
```xml
<!-- tt_content.xml -->
<dataset>
    <tt_content>
        <uid>1</uid>
        <pid>1</pid>
        <CType>text</CType>
        <header>Test Header</header>
        <bodytext>Test content</bodytext>
        <header_layout>1</header_layout>
    </tt_content>
</dataset>
```

#### File Fixtures (`Tests/Fixtures/Files/`)
- Test images in various formats (JPG, PNG, WebP)
- Video files for media processor testing
- Audio files for audio processor testing
- Invalid/corrupted files for error handling tests

#### Template Fixtures (`Tests/Fixtures/Templates/`)
- Sample handlebars templates for rendering tests
- Invalid templates for error condition testing
- Templates with various complexity levels

## Continuous Integration Requirements

### Pre-commit Quality Gates
1. **Linting**: PHP CS Fixer, EditorConfig compliance
2. **Static Analysis**: PHPStan level 8 analysis
3. **Unit Tests**: Must pass with 100% coverage
4. **Security**: Composer audit must pass

### CI Pipeline Stages

#### Stage 1: Quality Gate (Required for all commits)
- Code style validation
- Static analysis
- Unit tests with coverage
- Security audit

#### Stage 2: Integration Testing
- Functional tests
- Database integration tests
- Template rendering tests

#### Stage 3: Acceptance Testing (Release branches)
- End-to-end workflow tests
- Performance benchmarks
- Browser compatibility tests

## Testing Tools and Infrastructure

### Required Tools

#### Core Testing
- **PHPUnit 10.5**: Primary testing framework
- **TYPO3 Testing Framework v8.x**: TYPO3-specific testing utilities
- **php-code-coverage**: Coverage analysis

#### Code Quality
- **PHPStan**: Static analysis (Level 8)
- **PHP CS Fixer**: Code style enforcement
- **Rector**: PHP modernization checks
- **Roave Security Advisories**: Dependency security scanning

#### Additional Tools
- **Infection/Infection**: Mutation testing
- **PHPBench**: Performance benchmarking
- **Codeception**: Acceptance testing (optional)

### Development Environment Setup

#### Local Development
```bash
# Install dependencies
composer install

# Run quality checks
composer lint
composer sca

# Run tests
composer test:unit
composer test:functional
composer test:coverage
```

#### Docker Environment (Optional)
```dockerfile
FROM typo3/core-testing-docker:latest
COPY . /app
WORKDIR /app
RUN composer install
```

## Test Organization and Execution

### Composer Scripts Integration

The testing plan will integrate with composer scripts for easy execution:

```json
{
  "scripts": {
    "test": "phpunit --configuration Tests/Build/phpunit/UnitTests.xml --no-coverage",
    "test:unit": "phpunit --configuration Tests/Build/phpunit/UnitTests.xml --testsuite Unit",
    "test:functional": "phpunit --configuration Tests/Build/phpunit/FunctionalTests.xml --testsuite Functional",
    "test:coverage": "XDEBUG_MODE=coverage phpunit --configuration Tests/Build/phpunit/UnitTests.xml --coverage-html var/coverage --coverage-clover var/coverage/clover.xml --coverage-text",
    "test:mutation": "infection --configuration=infection.json.dist",
    "test:performance": "phpunit --group=performance --no-coverage"
  }
}
```

### Testing Workflow

#### Development Workflow
1. **Write failing test** for new functionality
2. **Implement minimum code** to pass test
3. **Refactor** while maintaining test coverage
4. **Run full test suite** before commit
5. **Verify coverage requirements** are met

#### Release Workflow
1. **All tests must pass** with 100% coverage
2. **Performance benchmarks** must meet targets
3. **Mutation testing score** must be > 85%
4. **Security audit** must show no vulnerabilities
5. **Manual testing** of critical user flows

## Implementation Timeline

### Phase 1: Foundation
- ✅ Set up testing infrastructure
- ✅ Configure PHPUnit and TYPO3 testing framework
- ✅ Create basic test structure
- ✅ Implement first unit tests for utility classes

### Phase 2: Core Testing
- ✅ Unit tests for service classes (100% coverage)
- ✅ Unit tests for data processors (100% coverage)
- ✅ Integration tests for processing pipeline
- ✅ Database integration tests

### Phase 3: Advanced Testing
- ✅ Functional tests for content rendering
- ✅ Template rendering tests
- ✅ Error condition testing
- ✅ Performance testing implementation

### Phase 4: Quality Assurance
- ✅ Mutation testing setup and optimization
- ✅ CI/CD pipeline implementation
- ✅ Coverage gap analysis and remediation
- ✅ Documentation and training materials

## Success Criteria

### Primary Goals (Must Achieve)
- ✅ **100% line coverage** across all production code
- ✅ **Zero critical bugs** identified in testing
- ✅ **All quality gates pass** in CI/CD pipeline
- ✅ **Test suite executes** in < 5 minutes

### Secondary Goals (Should Achieve)
- ✅ **> 85% mutation testing score**
- ✅ **< 30 seconds unit test execution**
- ✅ **Zero security vulnerabilities**
- ✅ **Comprehensive documentation** of testing approach

### Quality Maintenance (Ongoing)
- ✅ **Automated test execution** on all commits
- ✅ **Coverage regression prevention**
- ✅ **Regular dependency security updates**
- ✅ **Performance regression monitoring**

This comprehensive testing plan provides a clear roadmap to achieve 100% test coverage while maintaining high code quality standards. The multi-layered approach ensures robust testing at unit, integration, and functional levels, with appropriate tooling and automation to support continuous quality assurance.
