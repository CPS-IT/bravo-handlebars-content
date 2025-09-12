<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\CollectionDataProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\NullDataProcessor;
use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;
use TYPO3\CMS\Frontend\ContentObject\TextContentObject;
use TYPO3\CMS\Frontend\DataProcessing\DataProcessorRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use InvalidArgumentException;

/**
 * Test case for CollectionDataProcessor
 *
 * @covers \Cpsit\BravoHandlebarsContent\DataProcessing\CollectionDataProcessor
 */
final class CollectionDataProcessorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private CollectionDataProcessor $subject;
    private ContentObjectRenderer|MockObject $contentObjectRenderer;
    private ContainerInterface|MockObject $container;
    private DataProcessorRegistry|MockObject $dataProcessorRegistry;
    private DataProcessorInterface|MockObject $mockDataProcessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->dataProcessorRegistry = $this->createMock(DataProcessorRegistry::class);
        $this->mockDataProcessor = $this->createMock(DataProcessorInterface::class);

        $this->subject = new CollectionDataProcessor(
            $this->contentObjectRenderer,
            $this->container,
            $this->dataProcessorRegistry
        );
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test]
    public function processReturnsProcessedDataWithVariables(): void
    {
        $processorConfiguration = [
            'as' => 'collection',
            'variables.' => [
                'title' => 'TEXT',
                'title.' => ['value' => 'Test Title'],
                'description' => 'TEXT',
                'description.' => ['value' => 'Test Description']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('as', $processorConfiguration)
            ->willReturn('collection');

        $mockTextContentObject = $this->createMock(TextContentObject::class);

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('getContentObject')
            ->with('TEXT')
            ->willReturn($mockTextContentObject);

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('cObjGetSingle')
            ->willReturnOnConsecutiveCalls('Test Title', 'Test Description');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('collection', $result);
        self::assertArrayHasKey('title', $result['collection']);
        self::assertArrayHasKey('description', $result['collection']);
        self::assertSame('Test Title', $result['collection']['title']);
        self::assertSame('Test Description', $result['collection']['description']);
        self::assertArrayHasKey('existing', $result);
    }

    #[Test]
    public function processSkipsWhenIfConditionIsFalse(): void
    {
        $processorConfiguration = [
            'if.' => ['value' => '0'],
            'as' => 'collection',
            'variables.' => [
                'test' => 'TEXT',
                'test.' => ['value' => 'Test']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(false);

        $this->contentObjectRenderer->expects(self::never())
            ->method('stdWrapValue');

        $this->contentObjectRenderer->expects(self::never())
            ->method('getContentObject');

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
            'as' => 'collection',
            'variables.' => [
                'content' => 'TEXT',
                'content.' => ['value' => 'Conditional Content']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(true);

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('collection');

        $mockTextContentObject = $this->createMock(TextContentObject::class);

        $this->contentObjectRenderer->expects(self::once())
            ->method('getContentObject')
            ->willReturn($mockTextContentObject);

        $this->contentObjectRenderer->expects(self::once())
            ->method('cObjGetSingle')
            ->willReturn('Conditional Content');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('collection', $result);
        self::assertSame('Conditional Content', $result['collection']['content']);
    }

    #[Test]
    public function processThrowsExceptionWhenAsConfigurationMissing(): void
    {
        $processorConfiguration = [
            'variables.' => [
                'test' => 'TEXT',
                'test.' => ['value' => 'Test']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('as', $processorConfiguration)
            ->willReturn('');

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1713766921);
        $this->expectExceptionMessage('Missing configuration "as"');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
    }

    #[Test]
    public function processHandlesDataProcessors(): void
    {
        $processorConfiguration = [
            'as' => 'processed',
            'variables.' => [
                'processedData' => 'SomeDataProcessor',
                'processedData.' => ['as' => 'processedData', 'config' => 'value']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('processed');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getContentObject')
            ->with('SomeDataProcessor')
            ->willThrowException(new ContentRenderingException('Not a content object'));

        $this->dataProcessorRegistry->expects(self::atLeastOnce())
            ->method('getDataProcessor')
            ->with('SomeDataProcessor')
            ->willReturn($this->mockDataProcessor);

        $this->mockDataProcessor->expects(self::once())
            ->method('process')
            ->with(
                $this->contentObjectRenderer,
                $processorConfiguration,
                ['as' => 'processedData', 'config' => 'value'],
                $processedData
            )
            ->willReturn(['processedData' => ['processed' => 'result']]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('processed', $result);
        self::assertArrayHasKey('processedData', $result['processed']);
        self::assertSame(['processed' => 'result'], $result['processed']['processedData']);
    }

    #[Test]
    public function processHandlesDataProcessorsFromContainer(): void
    {
        $processorConfiguration = [
            'as' => 'containerProcessed',
            'variables.' => [
                'containerData' => 'ContainerProcessor',
                'containerData.' => ['as' => 'containerData']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('containerProcessed');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getContentObject')
            ->willThrowException(new ContentRenderingException('Not a content object'));

        $this->dataProcessorRegistry->expects(self::exactly(2))
            ->method('getDataProcessor')
            ->with('ContainerProcessor')
            ->willReturn(null);

        $this->container->expects(self::atLeastOnce())
            ->method('has')
            ->with('ContainerProcessor')
            ->willReturn(true);

        $this->container->expects(self::atLeastOnce())
            ->method('get')
            ->with('ContainerProcessor')
            ->willReturn($this->mockDataProcessor);

        $this->mockDataProcessor->expects(self::once())
            ->method('process')
            ->willReturn(['containerData' => ['container' => 'result']]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('containerProcessed', $result);
        self::assertArrayHasKey('containerData', $result['containerProcessed']);
        self::assertSame(['container' => 'result'], $result['containerProcessed']['containerData']);
    }

    #[Test]
    public function processUsesNullDataProcessorAsFallback(): void
    {
        $processorConfiguration = [
            'as' => 'nullProcessed',
            'variables.' => [
                'nullData' => 'NonExistentProcessor',
                'nullData.' => ['as' => 'nullData']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('nullProcessed');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getContentObject')
            ->willThrowException(new ContentRenderingException('Not a content object'));

        // isDataProcessor() calls getDataProcessor once, but since it returns false,
        // the main getDataProcessor() is never called
        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->with('NonExistentProcessor')
            ->willReturn(null);

        $this->container->expects(self::once())
            ->method('has')
            ->with('NonExistentProcessor')
            ->willReturn(false);

        // NonExistentProcessor is neither in registry, container, nor a valid class
        // so isDataProcessor() returns false and the processor is never called
        
        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('nullProcessed', $result);
        // nullData is not present because the processor was never called
        self::assertArrayNotHasKey('nullData', $result['nullProcessed']);
    }

    #[Test]
    public function processThrowsExceptionForReservedVariableNames(): void
    {
        $processorConfiguration = [
            'as' => 'reserved',
            'variables.' => [
                'data' => 'TEXT',
                'data.' => ['value' => 'Test']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('reserved');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1713637292);
        $this->expectExceptionMessage('Invalid variable name data. This name is reserved');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
    }

    #[Test]
    public function processSkipsArrayVariables(): void
    {
        $processorConfiguration = [
            'as' => 'filtered',
            'variables.' => [
                'validVar' => 'TEXT',
                'validVar.' => ['value' => 'Valid'],
                'arrayVar.' => ['nested' => 'array'], // Should be skipped
                'anotherVar' => 'TEXT',
                'anotherVar.' => ['value' => 'Another']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('filtered');

        $mockTextContentObject = $this->createMock(TextContentObject::class);

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('getContentObject')
            ->with('TEXT')
            ->willReturn($mockTextContentObject);

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('cObjGetSingle')
            ->willReturnOnConsecutiveCalls('Valid', 'Another');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('filtered', $result);
        self::assertCount(2, $result['filtered']); // Only 2 variables, arrayVar should be skipped
        self::assertArrayHasKey('validVar', $result['filtered']);
        self::assertArrayHasKey('anotherVar', $result['filtered']);
        self::assertArrayNotHasKey('arrayVar', $result['filtered']);
    }

    #[Test]
    public function processHandlesCustomAsConfiguration(): void
    {
        $processorConfiguration = [
            'as' => 'customCollection',
            'variables.' => [
                'item' => 'TEXT',
                'item.' => ['value' => 'Item Value', 'as' => 'customName']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('customCollection');

        $mockTextContentObject = $this->createMock(TextContentObject::class);

        $this->contentObjectRenderer->expects(self::once())
            ->method('getContentObject')
            ->willReturn($mockTextContentObject);

        $this->contentObjectRenderer->expects(self::once())
            ->method('cObjGetSingle')
            ->willReturn('Item Value');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('customCollection', $result);
        self::assertArrayHasKey('customName', $result['customCollection']); // Uses custom 'as' name
        self::assertArrayNotHasKey('item', $result['customCollection']);
        self::assertSame('Item Value', $result['customCollection']['customName']);
    }

    #[Test]
    public function processHandlesEmptyVariablesConfiguration(): void
    {
        $processorConfiguration = [
            'as' => 'empty'
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('empty');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('empty', $result);
        self::assertEmpty($result['empty']);
        self::assertArrayHasKey('existing', $result);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function processReservedVariableNamesDataProvider(): array
    {
        return [
            'data variable' => [
                'variableName' => 'data',
                'expectedMessage' => 'Invalid variable name data. This name is reserved'
            ],
            'current variable' => [
                'variableName' => 'current',
                'expectedMessage' => 'Invalid variable name current. This name is reserved'
            ]
        ];
    }

    #[Test]
    #[DataProvider('processReservedVariableNamesDataProvider')]
    public function processThrowsExceptionForAllReservedVariableNames(string $variableName, string $expectedMessage): void
    {
        $processorConfiguration = [
            'as' => 'reserved',
            'variables.' => [
                $variableName => 'TEXT',
                $variableName . '.' => ['value' => 'Test']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('reserved');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1713637292);
        $this->expectExceptionMessage($expectedMessage);

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
    }

    #[Test]
    public function processMixesContentObjectsAndDataProcessors(): void
    {
        $processorConfiguration = [
            'as' => 'mixed',
            'variables.' => [
                'textVar' => 'TEXT',
                'textVar.' => ['value' => 'Text Content'],
                'processedVar' => 'SomeProcessor',
                'processedVar.' => ['as' => 'processedVar', 'config' => 'test']
            ]
        ];
        $processedData = ['base' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('mixed');

        $mockTextContentObject = $this->createMock(TextContentObject::class);

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('getContentObject')
            ->willReturnOnConsecutiveCalls(
                $mockTextContentObject,
                $this->throwException(new ContentRenderingException('Not a content object'))
            );

        $this->contentObjectRenderer->expects(self::once())
            ->method('cObjGetSingle')
            ->willReturn('Text Content');

        $this->dataProcessorRegistry->expects(self::atLeastOnce())
            ->method('getDataProcessor')
            ->willReturn($this->mockDataProcessor);

        $this->mockDataProcessor->expects(self::atLeastOnce())
            ->method('process')
            ->willReturn(['processedVar' => ['processed' => 'data']]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('mixed', $result);
        self::assertArrayHasKey('textVar', $result['mixed']);
        self::assertArrayHasKey('processedVar', $result['mixed']);
        self::assertSame('Text Content', $result['mixed']['textVar']);
        self::assertSame(['processed' => 'data'], $result['mixed']['processedVar']);
    }
}