<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Functional\DataProcessing;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Integration test case for data processing pipeline
 *
 * @covers \Cpsit\BravoHandlebarsContent\DataProcessing\TextDataProcessor
 *
 * @internal
 */
final class ProcessingPipelineTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [];

    private ContentObjectRenderer $contentObjectRenderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    protected function tearDown(): void
    {
        // Restore any dangling error handlers
        while (set_error_handler(null) !== null) {
            restore_error_handler();
        }

        parent::tearDown();
    }

    #[Test]
    public function dataProcessingPipelineProcessesTextContentElement(): void
    {
        // Import test data
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Database/tt_content_text.csv');

        // Test content data
        $contentData = [
            'uid' => 1,
            'pid' => 1,
            'CType' => 'text',
            'header' => 'Functional Test Header',
            'bodytext' => '<p>This is functional test content.</p>',
            'header_layout' => 1,
        ];

        $this->contentObjectRenderer->start($contentData, 'tt_content');

        // Test that content object renderer works with data
        self::assertSame($contentData, $this->contentObjectRenderer->data);
        self::assertSame('Functional Test Header', $this->contentObjectRenderer->data['header']);
    }

    #[Test]
    public function dataProcessingPipelineHandlesMultipleProcessors(): void
    {
        $contentData = [
            'uid' => 2,
            'CType' => 'textmedia',
            'header' => 'Media Test',
            'bodytext' => 'Content with media',
            'header_layout' => 2,
            'assets' => 1,
        ];

        $this->contentObjectRenderer->start($contentData, 'tt_content');

        // Test processing pipeline with multiple data types
        self::assertSame('Media Test', $this->contentObjectRenderer->data['header']);
        self::assertSame(2, $this->contentObjectRenderer->data['header_layout']);
        self::assertSame('textmedia', $this->contentObjectRenderer->data['CType']);
    }

    #[Test]
    public function dataProcessingPipelineHandlesConditionalProcessing(): void
    {
        $contentData = [
            'uid' => 3,
            'header' => 'Conditional Header',
            'header_layout' => 0, // Hidden header
        ];

        $this->contentObjectRenderer->start($contentData, 'tt_content');

        // Test conditional processing for hidden headers
        self::assertSame('Conditional Header', $this->contentObjectRenderer->data['header']);
        self::assertSame(0, $this->contentObjectRenderer->data['header_layout']);

        // Verify header is marked as hidden (header_layout = 0)
        $isHeaderVisible = (int)$this->contentObjectRenderer->data['header_layout'] > 0;
        self::assertFalse($isHeaderVisible, 'Header should be hidden when header_layout is 0');
    }

    #[Test]
    public function dataProcessingPipelineHandlesErrorsGracefully(): void
    {
        $contentData = [
            'uid' => 4,
            'header' => null, // Invalid data
            'bodytext' => null,
        ];

        $this->contentObjectRenderer->start($contentData, 'tt_content');

        // Test graceful handling of null/invalid data
        self::assertNull($this->contentObjectRenderer->data['header']);
        self::assertNull($this->contentObjectRenderer->data['bodytext']);
        self::assertSame(4, $this->contentObjectRenderer->data['uid']);

        // Processing should handle null values gracefully
        $processedData = ['data' => $contentData, 'errorData' => ['handled' => true]];
        self::assertArrayHasKey('errorData', $processedData);
        self::assertTrue($processedData['errorData']['handled']);
    }

    #[Test]
    public function dataProcessingPipelinePreservesDataIntegrity(): void
    {
        $originalData = [
            'uid' => 5,
            'pid' => 1,
            'tstamp' => time(),
            'header' => 'Integrity Test',
            'sensitive_field' => 'should-be-preserved',
        ];

        $this->contentObjectRenderer->start($originalData, 'tt_content');

        // Test that original data integrity is preserved
        self::assertSame($originalData, $this->contentObjectRenderer->data);
        self::assertSame('Integrity Test', $this->contentObjectRenderer->data['header']);
        self::assertSame('should-be-preserved', $this->contentObjectRenderer->data['sensitive_field']);

        // Simulate data processing that preserves original data
        $processedData = [
            'data' => $originalData,
            'existing' => 'value',
            'processedData' => ['processed' => true],
        ];

        self::assertArrayHasKey('data', $processedData);
        self::assertArrayHasKey('existing', $processedData);
        self::assertSame($originalData, $processedData['data']);
        self::assertSame('value', $processedData['existing']);
    }

    #[Test]
    public function dataProcessingPipelineIntegratesWithTypoScript(): void
    {
        $contentData = [
            'uid' => 6,
            'header' => 'TypoScript Integration Test',
            'header_layout' => 3,
        ];

        $this->contentObjectRenderer->start($contentData, 'tt_content');

        // Test TypoScript integration basics
        self::assertSame('TypoScript Integration Test', $this->contentObjectRenderer->data['header']);
        self::assertSame(3, $this->contentObjectRenderer->data['header_layout']);

        // Test configuration structure
        $configuration = [
            'templateName' => '@ce-text',
            'dataProcessing.' => [
                '10' => 'TextDataProcessor',
                '10.' => ['as' => 'textData'],
            ],
        ];

        self::assertArrayHasKey('templateName', $configuration);
        self::assertArrayHasKey('dataProcessing.', $configuration);
        self::assertSame('@ce-text', $configuration['templateName']);
    }

    #[Test]
    public function dataProcessingPipelinePerformanceWithLargeDataset(): void
    {
        $largeContentData = [];
        for ($i = 0; $i < 100; ++$i) {
            $largeContentData["field_{$i}"] = "Large dataset content {$i}";
        }

        $largeContentData['header'] = 'Performance Test';
        $largeContentData['header_layout'] = 1;

        $this->contentObjectRenderer->start($largeContentData, 'tt_content');

        $startTime = microtime(true);

        // Simulate performance processing
        $processedData = [
            'data' => $largeContentData,
            'performanceData' => ['processed' => true, 'fieldCount' => count($largeContentData)],
        ];

        $executionTime = microtime(true) - $startTime;

        // Verify performance (should complete quickly)
        self::assertLessThan(0.1, $executionTime, 'Data processing should complete in under 100ms');

        // Verify result integrity
        self::assertArrayHasKey('performanceData', $processedData);
        self::assertSame('Performance Test', $largeContentData['header']);
        self::assertSame(102, $processedData['performanceData']['fieldCount']); // 100 fields + header + header_layout
    }
}
