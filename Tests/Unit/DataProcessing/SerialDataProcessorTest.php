<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\SerialDataProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\DataProcessing\DataProcessorRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use UnexpectedValueException;

/**
 * Test case for SerialDataProcessor
 *
 * @covers \Cpsit\BravoHandlebarsContent\DataProcessing\SerialDataProcessor
 */
final class SerialDataProcessorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private SerialDataProcessor $subject;
    private ContainerInterface|MockObject $container;
    private DataProcessorRegistry|MockObject $dataProcessorRegistry;
    private ContentObjectRenderer|MockObject $contentObjectRenderer;
    private DataProcessorInterface|MockObject $mockProcessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->createMock(ContainerInterface::class);
        $this->dataProcessorRegistry = $this->createMock(DataProcessorRegistry::class);
        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->mockProcessor = $this->createMock(DataProcessorInterface::class);

        $this->subject = new SerialDataProcessor(
            $this->container,
            $this->dataProcessorRegistry
        );
    }

    #[Test]
    public function processReturnsUnmodifiedDataWhenNoProcessorsConfigured(): void
    {
        $processorConfiguration = [];
        $processedData = ['existing' => 'data'];

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertSame($processedData, $result);
    }

    #[Test]
    public function processReturnsUnmodifiedDataWhenDataProcessingArrayEmpty(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => []
        ];
        $processedData = ['existing' => 'data'];

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertSame($processedData, $result);
    }

    #[Test]
    public function processSkipsWhenIfConditionIsFalse(): void
    {
        $processorConfiguration = [
            'if.' => ['value' => '0'],
            'dataProcessing.' => [
                '10' => 'SomeProcessor',
                '10.' => ['config' => 'value']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(false);

        $this->dataProcessorRegistry->expects(self::never())
            ->method('getDataProcessor');

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
            'dataProcessing.' => [
                '10' => 'SomeProcessor',
                '10.' => ['config' => 'value']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(true);

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->with('SomeProcessor')
            ->willReturn($this->mockProcessor);

        $this->mockProcessor->expects(self::once())
            ->method('process')
            ->with(
                $this->contentObjectRenderer,
                $processorConfiguration,
                ['config' => 'value'],
                []
            )
            ->willReturn(['processed' => 'data']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        // When no target variable name is given, results get merged into processedData
        self::assertArrayHasKey('existing', $result);
        self::assertArrayHasKey('processed', $result);
        self::assertSame('data', $result['existing']);
        self::assertSame('data', $result['processed']);
    }

    #[Test]
    public function processProcessesSequentialProcessorsFromRegistry(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => 'FirstProcessor',
                '10.' => ['first' => 'config'],
                '20' => 'SecondProcessor',
                '20.' => ['second' => 'config']
            ]
        ];
        $processedData = ['initial' => 'data'];

        $firstProcessor = $this->createMock(DataProcessorInterface::class);
        $secondProcessor = $this->createMock(DataProcessorInterface::class);

        $this->dataProcessorRegistry->expects(self::exactly(2))
            ->method('getDataProcessor')
            ->withConsecutive(['FirstProcessor'], ['SecondProcessor'])
            ->willReturnOnConsecutiveCalls($firstProcessor, $secondProcessor);

        $firstProcessor->expects(self::once())
            ->method('process')
            ->with(
                $this->contentObjectRenderer,
                $processorConfiguration,
                ['first' => 'config'],
                []
            )
            ->willReturn(['first' => 'processed']);

        $secondProcessor->expects(self::once())
            ->method('process')
            ->with(
                $this->contentObjectRenderer,
                $processorConfiguration,
                ['second' => 'config'],
                ['first' => 'processed']
            )
            ->willReturn(['first' => 'processed', 'second' => 'processed']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        // Data should be merged back into processedData
        self::assertArrayHasKey('initial', $result);
        self::assertArrayHasKey('first', $result);
        self::assertArrayHasKey('second', $result);
        self::assertSame('data', $result['initial']);
        self::assertSame('processed', $result['first']);
        self::assertSame('processed', $result['second']);
    }

    #[Test]
    public function processFallsBackToContainerWhenRegistryReturnsNull(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => 'ContainerProcessor',
                '10.' => ['config' => 'value']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->with('ContainerProcessor')
            ->willReturn(null);

        $this->container->expects(self::once())
            ->method('has')
            ->with('ContainerProcessor')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->with('ContainerProcessor')
            ->willReturn($this->mockProcessor);

        $this->mockProcessor->expects(self::once())
            ->method('process')
            ->willReturn(['processed' => 'data']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('processed', $result);
    }

    #[Test]
    public function processFallsBackToClassInstantiationWhenContainerDoesNotHaveService(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => DataProcessorInterface::class,
                '10.' => ['config' => 'value']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->willReturn(null);

        $this->container->expects(self::once())
            ->method('has')
            ->willReturn(false);

        // Mock GeneralUtility::makeInstance to return our mock processor
        GeneralUtility::addInstance(DataProcessorInterface::class, $this->mockProcessor);

        $this->mockProcessor->expects(self::once())
            ->method('process')
            ->willReturn(['instantiated' => 'data']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('instantiated', $result);
    }

    #[Test]
    public function processSkipsNonExistentProcessor(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => 'NonExistentProcessor',
                '10.' => ['config' => 'value'],
                '20' => 'ExistingProcessor',
                '20.' => ['other' => 'config']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->dataProcessorRegistry->expects(self::exactly(2))
            ->method('getDataProcessor')
            ->withConsecutive(['NonExistentProcessor'], ['ExistingProcessor'])
            ->willReturnOnConsecutiveCalls(null, $this->mockProcessor);

        $this->container->expects(self::once())
            ->method('has')
            ->with('NonExistentProcessor')
            ->willReturn(false);

        $this->mockProcessor->expects(self::once())
            ->method('process')
            ->willReturn(['existing_processed' => 'data']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('existing_processed', $result);
    }

    #[Test]
    public function processThrowsExceptionWhenContainerServiceIsNotDataProcessor(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => 'InvalidService',
                '10.' => []
            ]
        ];

        $invalidService = new \stdClass();

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->willReturn(null);

        $this->container->expects(self::once())
            ->method('has')
            ->willReturn(true);

        $this->container->expects(self::once())
            ->method('get')
            ->willReturn($invalidService);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1635927108);
        $this->expectExceptionMessage('Processor with service name "InvalidService" must implement interface "TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface"');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            []
        );
    }

    #[Test]
    public function processThrowsExceptionWhenClassDoesNotExist(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => 'NonExistentClass',
                '10.' => []
            ]
        ];

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->willReturn(null);

        $this->container->expects(self::once())
            ->method('has')
            ->willReturn(false);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1427455378);
        $this->expectExceptionMessage('Processor class or service name "NonExistentClass" does not exist!');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            []
        );
    }

    #[Test]
    public function processThrowsExceptionWhenClassDoesNotImplementInterface(): void
    {
        $processorConfiguration = [
            'dataProcessing.' => [
                '10' => \stdClass::class,
                '10.' => []
            ]
        ];

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->willReturn(null);

        $this->container->expects(self::once())
            ->method('has')
            ->willReturn(false);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionCode(1427455377);
        $this->expectExceptionMessage('Processor with class name "stdClass" must implement interface "TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface"');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            []
        );
    }

    #[Test]
    public function processWithTargetVariableNameStoresDataInSpecificKey(): void
    {
        $processorConfiguration = [
            'as' => 'serialData',
            'dataProcessing.' => [
                '10' => 'TestProcessor',
                '10.' => ['config' => 'value']
            ]
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('as', $processorConfiguration, '')
            ->willReturn('serialData');

        $this->dataProcessorRegistry->expects(self::once())
            ->method('getDataProcessor')
            ->willReturn($this->mockProcessor);

        $this->mockProcessor->expects(self::once())
            ->method('process')
            ->willReturn(['processed' => 'serial_data']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('serialData', $result);
        self::assertSame(['processed' => 'serial_data'], $result['serialData']);
        self::assertArrayHasKey('existing', $result);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function processComplexConfigurationsDataProvider(): array
    {
        return [
            'single processor' => [
                'config' => [
                    'dataProcessing.' => [
                        '10' => 'SingleProcessor',
                        '10.' => ['single' => 'config']
                    ]
                ],
                'expectedProcessorCalls' => 1
            ],
            'multiple processors' => [
                'config' => [
                    'dataProcessing.' => [
                        '10' => 'FirstProcessor',
                        '10.' => ['first' => 'config'],
                        '20' => 'SecondProcessor',
                        '20.' => ['second' => 'config'],
                        '30' => 'ThirdProcessor',
                        '30.' => ['third' => 'config']
                    ]
                ],
                'expectedProcessorCalls' => 3
            ],
            'mixed keys' => [
                'config' => [
                    'dataProcessing.' => [
                        'text' => 'TextProcessor',
                        'text.' => ['text' => 'config'],
                        'media' => 'MediaProcessor',
                        'media.' => ['media' => 'config']
                    ]
                ],
                'expectedProcessorCalls' => 2
            ]
        ];
    }

    #[Test]
    #[DataProvider('processComplexConfigurationsDataProvider')]
    public function processHandlesComplexConfigurations(array $config, int $expectedProcessorCalls): void
    {
        $processedData = ['initial' => 'data'];

        $this->dataProcessorRegistry->expects(self::exactly($expectedProcessorCalls))
            ->method('getDataProcessor')
            ->willReturn($this->mockProcessor);

        $this->mockProcessor->expects(self::exactly($expectedProcessorCalls))
            ->method('process')
            ->willReturnCallback(function ($cObj, $contentObjectConfig, $processorConfig, $data) {
                return array_merge($data, ['processed' => true]);
            });

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $config,
            $processedData
        );

        self::assertArrayHasKey('initial', $result);
        self::assertArrayHasKey('processed', $result);
        self::assertTrue($result['processed']);
    }
}