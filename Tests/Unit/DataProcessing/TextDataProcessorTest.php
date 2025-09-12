<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\Dto\FieldProcessorConfiguration;
use Cpsit\BravoHandlebarsContent\DataProcessing\Map\CeTextDataMap;
use Cpsit\BravoHandlebarsContent\DataProcessing\TextDataProcessor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for TextDataProcessor
 *
 * @covers \Cpsit\BravoHandlebarsContent\DataProcessing\TextDataProcessor
 */
final class TextDataProcessorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private TextDataProcessor|MockObject $subject;
    private ContentObjectRenderer|MockObject $contentObjectRenderer;
    private CeTextDataMap|MockObject $dataMap;
    private FieldProcessorConfiguration|MockObject $fieldProcessorConfiguration;
    private LanguageServiceFactory|MockObject $languageServiceFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->dataMap = $this->createMock(CeTextDataMap::class);
        $this->fieldProcessorConfiguration = $this->createMock(FieldProcessorConfiguration::class);
        $this->languageServiceFactory = $this->createMock(LanguageServiceFactory::class);

        // Create a partial mock to avoid complex field processing behavior
        $this->subject = $this->getMockBuilder(TextDataProcessor::class)
            ->setConstructorArgs([
                $this->fieldProcessorConfiguration,
                $this->dataMap,
                $this->contentObjectRenderer,
                $this->languageServiceFactory
            ])
            ->onlyMethods(['processFields'])
            ->getMock();
    }

    #[Test]
    public function processReturnsProcessedDataWithDefaultVariableName(): void
    {
        $processorConfiguration = [];
        $processedData = [
            'data' => [
                'header' => 'Test Header',
                'bodytext' => 'Test content',
                'header_layout' => 1
            ]
        ];

        // Mock processFields to return processed variables
        $this->subject->expects(self::once())
            ->method('processFields')
            ->willReturn([
                'data' => $processedData['data']
            ]);

        // Mock the dataMap getFieldMaps to return an empty storage for mapping
        $this->dataMap->expects(self::once())
            ->method('getFieldMaps')
            ->willReturn(new \SplObjectStorage());

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertIsArray($result);
        self::assertArrayHasKey('data', $result);
        // After processing, the data should be merged back into processedData
        self::assertSame($processedData['data']['header'], $result['data']['header']);
        self::assertSame($processedData['data']['bodytext'], $result['data']['bodytext']);
    }

    #[Test]
    public function processReturnsProcessedDataWithCustomVariableName(): void
    {
        $processorConfiguration = ['as' => 'textData'];
        $processedData = [
            'data' => [
                'header' => 'Custom Header',
                'bodytext' => 'Custom content'
            ]
        ];

        // Mock processFields to return processed variables
        $this->subject->expects(self::once())
            ->method('processFields')
            ->willReturn([
                'textData' => $processedData['data']
            ]);

        // Mock the dataMap getFieldMaps to return an empty storage for mapping
        $this->dataMap->expects(self::once())
            ->method('getFieldMaps')
            ->willReturn(new \SplObjectStorage());

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertIsArray($result);
        // With custom variable name 'as' => 'textData', the processed data should be under that key
        self::assertArrayHasKey('textData', $result);
    }

    #[Test]
    public function processSkipsWhenIfConditionIsFalse(): void
    {
        $processorConfiguration = [
            'if.' => ['value' => '0']
        ];
        $processedData = ['data' => ['header' => 'Test']];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(false);

        $this->subject->expects(self::never())
            ->method('processFields');

        $this->dataMap->expects(self::never())
            ->method('getFieldMaps');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertSame($processedData, $result);
    }

    #[Test]
    public function processExecutesWhenIfConditionIsTrue(): void
    {
        $processorConfiguration = [
            'if.' => ['value' => '1'],
            'as' => 'textContent'
        ];
        $processedData = ['data' => ['header' => 'Conditional Test']];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(true);

        // Mock processFields to return processed data
        $this->subject->expects(self::once())
            ->method('processFields')
            ->willReturn([
                'textContent' => ['processed' => 'data']
            ]);

        $this->dataMap->expects(self::once())
            ->method('getFieldMaps')
            ->willReturn(new \SplObjectStorage());

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('textContent', $result);
        self::assertIsArray($result['textContent']);
    }

    #[Test]
    public function processHandlesEmptyData(): void
    {
        $processorConfiguration = ['as' => 'emptyData'];
        $processedData = ['data' => []];

        // Mock processFields to return empty processed data
        $this->subject->expects(self::once())
            ->method('processFields')
            ->willReturn([
                'emptyData' => []
            ]);

        $this->dataMap->expects(self::once())
            ->method('getFieldMaps')
            ->willReturn(new \SplObjectStorage());

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('emptyData', $result);
        self::assertSame([], $result['emptyData']);
    }

    #[Test]
    public function processPreservesExistingProcessedData(): void
    {
        $processorConfiguration = ['as' => 'newData'];
        $processedData = [
            'data' => ['header' => 'Test'],
            'existingData' => ['preserve' => 'me']
        ];

        // Mock processFields to return processed data
        $this->subject->expects(self::once())
            ->method('processFields')
            ->willReturn([
                'newData' => ['preserved' => 'data']
            ]);

        $this->dataMap->expects(self::once())
            ->method('getFieldMaps')
            ->willReturn(new \SplObjectStorage());

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('existingData', $result);
        self::assertSame(['preserve' => 'me'], $result['existingData']);
        self::assertArrayHasKey('newData', $result);
        self::assertIsArray($result['newData']);
    }

    #[Test]
    public function processHandlesNullValues(): void
    {
        $processorConfiguration = ['as' => 'nullData'];
        $processedData = [
            'data' => [
                'header' => null,
                'bodytext' => null
            ]
        ];

        // Mock processFields to return null data
        $this->subject->expects(self::once())
            ->method('processFields')
            ->willReturn([
                'nullData' => ['null' => null]
            ]);

        $this->dataMap->expects(self::once())
            ->method('getFieldMaps')
            ->willReturn(new \SplObjectStorage());

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('nullData', $result);
        self::assertIsArray($result['nullData']);
    }
}
