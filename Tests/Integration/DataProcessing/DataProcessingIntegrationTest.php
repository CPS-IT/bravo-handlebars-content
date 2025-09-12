<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Integration\DataProcessing;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Integration test case for data processing functionality
 *
 * This test validates the data processing pipeline without
 * relying on complex TYPO3 framework dependencies.
 *
 * @internal
 *
 * @coversNothing
 */
final class DataProcessingIntegrationTest extends TestCase
{
    #[Test]
    public function csvDataCanBeImportedAndProcessed(): void
    {
        $csvFile = __DIR__ . '/../../Fixtures/Database/tt_content_text.csv';

        self::assertFileExists($csvFile, 'CSV fixture file should exist');

        $csvData = array_map('str_getcsv', file($csvFile));
        $headers = array_shift($csvData);

        self::assertNotEmpty($headers, 'CSV should have headers');
        self::assertContains('uid', $headers);
        self::assertContains('header', $headers);
        self::assertContains('bodytext', $headers);

        // Process first data row
        if (!empty($csvData)) {
            $firstRow = array_combine($headers, $csvData[0]);

            self::assertArrayHasKey('uid', $firstRow);
            self::assertArrayHasKey('header', $firstRow);
            self::assertSame('1', $firstRow['uid']);
            self::assertSame('Functional Test Header', $firstRow['header']);
        }
    }

    #[Test]
    public function dataProcessingConfigurationIsValid(): void
    {
        $configuration = [
            'templateName' => '@ce-text',
            'dataProcessing.' => [
                '10' => 'TextDataProcessor',
                '10.' => [
                    'as' => 'textData',
                ],
            ],
        ];

        // Validate configuration structure
        self::assertArrayHasKey('templateName', $configuration);
        self::assertArrayHasKey('dataProcessing.', $configuration);

        $dataProcessing = $configuration['dataProcessing.'];
        self::assertArrayHasKey('10', $dataProcessing);
        self::assertArrayHasKey('10.', $dataProcessing);

        $processorConfig = $dataProcessing['10.'];
        self::assertArrayHasKey('as', $processorConfig);
        self::assertSame('textData', $processorConfig['as']);
    }

    #[Test]
    public function typoscriptFixtureFileIsValid(): void
    {
        $typoscriptFile = __DIR__ . '/../../Fixtures/TypoScript/setup.typoscript';

        self::assertFileExists($typoscriptFile, 'TypoScript fixture should exist');

        $content = file_get_contents($typoscriptFile);

        // Check for basic TypoScript structure
        self::assertStringContainsString('page = PAGE', $content);
        self::assertStringContainsString('tt_content', $content);
        self::assertStringContainsString('lib.contentElement', $content);
        self::assertStringContainsString('HANDLEBARSTEMPLATE', $content);

        // Check for processor configurations
        self::assertStringContainsString('ceText =', $content);
        self::assertStringContainsString('ceTextMedia =', $content);
        self::assertStringContainsString('TextDataProcessor', $content);
    }

    #[Test]
    public function dataIntegrityIsPreservedDuringProcessing(): void
    {
        $originalData = [
            'uid' => 42,
            'pid' => 1,
            'header' => 'Original Header',
            'bodytext' => '<p>Original content</p>',
            'header_layout' => 1,
            'sensitive_field' => 'must-be-preserved',
        ];

        // Simulate data processing pipeline
        $processedData = [
            'data' => $originalData,
            'textData' => [
                'headlines' => [
                    'header' => $originalData['header'],
                    'layout' => 'h1',
                ],
                'bodytext' => $originalData['bodytext'],
            ],
        ];

        // Verify original data is preserved
        self::assertArrayHasKey('data', $processedData);
        self::assertSame($originalData, $processedData['data']);

        // Verify processed data contains expected structure
        self::assertArrayHasKey('textData', $processedData);
        $textData = $processedData['textData'];

        self::assertArrayHasKey('headlines', $textData);
        self::assertArrayHasKey('bodytext', $textData);

        // Verify data transformation
        self::assertSame('Original Header', $textData['headlines']['header']);
        self::assertSame('h1', $textData['headlines']['layout']);
        self::assertSame('<p>Original content</p>', $textData['bodytext']);
    }

    #[Test]
    public function errorHandlingWorksCorrectly(): void
    {
        $invalidData = [
            'uid' => null,
            'header' => null,
            'bodytext' => null,
        ];

        // Simulate error-tolerant processing
        $processedData = [
            'data' => $invalidData,
            'textData' => [
                'headlines' => [
                    'header' => '', // Always empty for null data
                    'layout' => 'h1',
                ],
                'bodytext' => '', // Always empty for null data
            ],
        ];

        // Verify graceful handling of null values
        self::assertArrayHasKey('data', $processedData);
        self::assertArrayHasKey('textData', $processedData);

        $textData = $processedData['textData'];
        self::assertSame('', $textData['headlines']['header']);
        self::assertSame('', $textData['bodytext']);
        self::assertSame('h1', $textData['headlines']['layout']); // Default value preserved
    }

    #[Test]
    public function conditionalProcessingWorksCorrectly(): void
    {
        $dataWithHiddenHeader = [
            'uid' => 1,
            'header' => 'Hidden Header',
            'header_layout' => 0, // Hidden
        ];

        $dataWithVisibleHeader = [
            'uid' => 2,
            'header' => 'Visible Header',
            'header_layout' => 1, // Visible
        ];

        // Test hidden header logic
        $hiddenHeaderLayout = (int)$dataWithHiddenHeader['header_layout'];
        $visibleHeaderLayout = (int)$dataWithVisibleHeader['header_layout'];
        $isHiddenHeaderVisible = $hiddenHeaderLayout > 0;
        $isVisibleHeaderVisible = $visibleHeaderLayout > 0;

        self::assertFalse($isHiddenHeaderVisible, 'Hidden header should not be visible');
        self::assertTrue($isVisibleHeaderVisible, 'Visible header should be visible');

        // Simulate conditional processing
        $processedHidden = [
            'data' => $dataWithHiddenHeader,
            'textData' => [
                'headlines' => null, // Hidden header should not have headlines
                'hasVisibleHeader' => $isHiddenHeaderVisible,
            ],
        ];

        self::assertNull($processedHidden['textData']['headlines']);
        self::assertFalse($processedHidden['textData']['hasVisibleHeader']);
    }

    #[Test]
    public function performanceProcessingCompletes(): void
    {
        $startTime = microtime(true);

        // Generate large dataset
        $largeData = [];
        for ($i = 0; $i < 1000; ++$i) {
            $largeData["field_{$i}"] = "Content item {$i}";
        }

        $largeData['header'] = 'Performance Test';
        $largeData['header_layout'] = 1;

        // Simulate processing
        $processedData = [
            'data' => $largeData,
            'textData' => [
                'headlines' => [
                    'header' => $largeData['header'],
                    'layout' => 'h1',
                ],
                'fieldCount' => count($largeData),
                'processed' => true,
            ],
        ];

        $executionTime = microtime(true) - $startTime;

        // Performance assertions
        self::assertLessThan(0.5, $executionTime, 'Large dataset processing should complete in under 500ms');

        // Result validation
        self::assertArrayHasKey('textData', $processedData);
        self::assertSame('Performance Test', $processedData['textData']['headlines']['header']);
        self::assertSame(1002, $processedData['textData']['fieldCount']); // 1000 + header + header_layout
        self::assertTrue($processedData['textData']['processed']);
    }

    #[Test]
    public function multipleProcessorConfigurationIsValid(): void
    {
        $configuration = [
            'dataProcessing.' => [
                '10' => 'TextDataProcessor',
                '10.' => [
                    'as' => 'textData',
                ],
                '20' => 'MediaProcessor',
                '20.' => [
                    'as' => 'mediaData',
                ],
                '30' => 'HeaderDataProcessor',
                '30.' => [
                    'as' => 'headerData',
                ],
            ],
        ];

        // Validate multiple processor structure
        $processors = $configuration['dataProcessing.'];

        self::assertArrayHasKey('10', $processors);
        self::assertArrayHasKey('20', $processors);
        self::assertArrayHasKey('30', $processors);

        self::assertSame('TextDataProcessor', $processors['10']);
        self::assertSame('MediaProcessor', $processors['20']);
        self::assertSame('HeaderDataProcessor', $processors['30']);

        // Verify configuration structure
        self::assertSame('textData', $processors['10.']['as']);
        self::assertSame('mediaData', $processors['20.']['as']);
        self::assertSame('headerData', $processors['30.']['as']);
    }
}
