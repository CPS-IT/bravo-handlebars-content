<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\MediaProcessor;
use Cpsit\BravoHandlebarsContent\Service\MediaDataService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

// Test-specific MediaDataService that can be instantiated without constructor arguments
class TestableMediaDataService extends MediaDataService
{
    private MockObject $mock;

    public function __construct()
    {
        // Empty constructor for testing - will be configured via setMock()
    }

    public function setMock(MockObject $mock): void
    {
        $this->mock = $mock;
    }

    public function setContentObjectRenderer($cObj): void
    {
        if (isset($this->mock)) {
            $this->mock->setContentObjectRenderer($cObj);
        }
    }

    public function process($file, $config = []): array
    {
        if (isset($this->mock)) {
            return $this->mock->process($file, $config);
        }

        return [];
    }
}

/**
 * Test case for MediaProcessor
 *
 * @covers \Cpsit\BravoHandlebarsContent\DataProcessing\MediaProcessor
 *
 * @internal
 */
final class MediaProcessorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private MediaProcessor $subject;

    private ContentObjectRenderer|MockObject $contentObjectRenderer;

    private MediaDataService|MockObject $mediaDataService;

    private FileInterface|MockObject $fileInterface;

    private MockObject|TypoScriptService $typoScriptService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->mediaDataService = $this->createMock(MediaDataService::class);
        $this->fileInterface = $this->createMock(FileInterface::class);
        $this->typoScriptService = $this->createMock(TypoScriptService::class);

        $this->subject = new MediaProcessor();
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test]
    public function processReturnsProcessedDataWithDefaultVariableName(): void
    {
        $processorConfiguration = [
            'data' => 'files',
        ];

        $processedData = [
            'files' => [$this->fileInterface],
        ];

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('stdWrapValue')
            ->willReturnMap([
                ['data', $processorConfiguration, '', 'files'],
                ['as', $processorConfiguration, 'files', 'files'],
            ]);

        $expectedMediaData = [
            'type' => 'image',
            'url' => '/fileadmin/user_upload/test.jpg',
            'alt' => 'Test image',
        ];

        // Create a testable MediaDataService that can be instantiated without constructor arguments
        $mediaDataServiceStub = new TestableMediaDataService();
        $mediaDataServiceStub->setMock($this->mediaDataService);

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub);
        GeneralUtility::addInstance(TypoScriptService::class, $this->typoScriptService);

        // TypoScriptService is not called when there are no settings

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer')
            ->with($this->contentObjectRenderer);

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->with($this->fileInterface, [])
            ->willReturn($expectedMediaData);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertIsArray($result);
        self::assertArrayHasKey('files', $result);
        self::assertCount(1, $result['files']);
        self::assertSame($expectedMediaData, $result['files'][0]);
    }

    #[Test]
    public function processReturnsProcessedDataWithCustomVariableName(): void
    {
        $processorConfiguration = [
            'data' => 'media',
            'as' => 'processedMedia',
        ];

        $processedData = [
            'media' => [$this->fileInterface],
        ];

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('stdWrapValue')
            ->willReturnMap([
                ['data', $processorConfiguration, '', 'media'],
                ['as', $processorConfiguration, 'files', 'processedMedia'],
            ]);

        $expectedMediaData = [
            'type' => 'video',
            'url' => '/fileadmin/user_upload/test.mp4',
        ];

        // Create a testable MediaDataService that can be instantiated without constructor arguments
        $mediaDataServiceStub = new TestableMediaDataService();
        $mediaDataServiceStub->setMock($this->mediaDataService);

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub);
        GeneralUtility::addInstance(TypoScriptService::class, $this->typoScriptService);

        // TypoScriptService is not called when there are no settings

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer')
            ->with($this->contentObjectRenderer);

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->with($this->fileInterface, [])
            ->willReturn($expectedMediaData);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('processedMedia', $result);
        self::assertCount(1, $result['processedMedia']);
        self::assertSame($expectedMediaData, $result['processedMedia'][0]);
    }

    #[Test]
    public function processSkipsWhenIfConditionIsFalse(): void
    {
        $processorConfiguration = [
            'if.' => ['value' => '0'],
            'data' => 'files',
        ];
        $processedData = ['files' => [$this->fileInterface]];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(false);

        $this->contentObjectRenderer->expects(self::never())
            ->method('stdWrapValue');

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
            'data' => 'files',
        ];
        $processedData = ['files' => [$this->fileInterface]];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(true);

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('stdWrapValue')
            ->willReturnMap([
                ['data', $processorConfiguration, '', 'files'],
                ['as', $processorConfiguration, 'files', 'files'],
            ]);

        // Create a testable MediaDataService that can be instantiated without constructor arguments
        $mediaDataServiceStub = new TestableMediaDataService();
        $mediaDataServiceStub->setMock($this->mediaDataService);

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub);
        GeneralUtility::addInstance(TypoScriptService::class, $this->typoScriptService);

        // TypoScriptService is not called when there are no settings

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer');

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->willReturn(['type' => 'image']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('files', $result);
    }

    #[Test]
    public function processReturnsOriginalDataWhenDataPathIsEmpty(): void
    {
        $processorConfiguration = ['data' => ''];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('data', $processorConfiguration, '')
            ->willReturn('');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertSame($processedData, $result);
    }

    #[Test]
    public function processReturnsOriginalDataWhenDataPathDoesNotExist(): void
    {
        $processorConfiguration = ['data' => 'nonexistent'];
        $processedData = ['files' => [$this->fileInterface]];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('data', $processorConfiguration, '')
            ->willReturn('nonexistent');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertSame($processedData, $result);
    }

    #[Test]
    public function processReturnsOriginalDataWhenDataPathIsEmptyArray(): void
    {
        $processorConfiguration = ['data' => 'files'];
        $processedData = ['files' => []];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('data', $processorConfiguration, '')
            ->willReturn('files');

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertSame($processedData, $result);
    }

    #[Test]
    public function processHandlesMultipleFiles(): void
    {
        $file1 = $this->createMock(FileInterface::class);
        $file2 = $this->createMock(FileInterface::class);

        $processorConfiguration = ['data' => 'files'];
        $processedData = ['files' => [$file1, $file2]];

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('stdWrapValue')
            ->willReturnMap([
                ['data', $processorConfiguration, '', 'files'],
                ['as', $processorConfiguration, 'files', 'files'],
            ]);

        // Create testable MediaDataService instances for each file (2 files = 2 instances needed)
        $mediaDataServiceStub1 = new TestableMediaDataService();
        $mediaDataServiceStub1->setMock($this->mediaDataService);
        $mediaDataServiceStub2 = new TestableMediaDataService();
        $mediaDataServiceStub2->setMock($this->mediaDataService);

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub1);
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub2);
        GeneralUtility::addInstance(TypoScriptService::class, $this->typoScriptService);

        // TypoScriptService is not called when there are no settings

        $this->mediaDataService->expects(self::exactly(2))
            ->method('setContentObjectRenderer');

        $this->mediaDataService->expects(self::exactly(2))
            ->method('process')
            ->willReturnOnConsecutiveCalls(
                ['type' => 'image', 'url' => '/file1.jpg'],
                ['type' => 'image', 'url' => '/file2.jpg']
            );

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertCount(2, $result['files']);
        self::assertSame(['type' => 'image', 'url' => '/file1.jpg'], $result['files'][0]);
        self::assertSame(['type' => 'image', 'url' => '/file2.jpg'], $result['files'][1]);
    }

    #[Test]
    public function mapMediaCallsMediaDataServiceCorrectly(): void
    {
        $mediaRendererConfig = [
            'image' => ['cropVariants' => ['desktop' => ['maxWidth' => 1200]]],
        ];

        $expectedResult = [
            'type' => 'image',
            'url' => '/fileadmin/test.jpg',
            'cropVariants' => ['desktop' => ['url' => '/processed.jpg']],
        ];

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $this->mediaDataService);

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer')
            ->with($this->contentObjectRenderer);

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->with($this->fileInterface, $mediaRendererConfig)
            ->willReturn($expectedResult);

        $result = $this->subject->mapMedia(
            $this->fileInterface,
            $this->contentObjectRenderer,
            $mediaRendererConfig
        );

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function mapMediaWorksWithEmptyConfig(): void
    {
        $expectedResult = ['type' => 'image', 'url' => '/fileadmin/test.jpg'];

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $this->mediaDataService);

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer')
            ->with($this->contentObjectRenderer);

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->with($this->fileInterface, [])
            ->willReturn($expectedResult);

        $result = $this->subject->mapMedia($this->fileInterface, $this->contentObjectRenderer);

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function processHandlesSettingsConfiguration(): void
    {
        $processorConfiguration = [
            'data' => 'files',
            'settings.' => [
                'image.' => [
                    'maxWidth' => '800',
                    'quality' => '85',
                ],
            ],
        ];
        $processedData = ['files' => [$this->fileInterface]];

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('stdWrapValue')
            ->willReturnMap([
                ['data', $processorConfiguration, '', 'files'],
                ['as', $processorConfiguration, 'files', 'files'],
            ]);

        $convertedSettings = [
            'image' => [
                'maxWidth' => '800',
                'quality' => '85',
            ],
        ];

        // Create a testable MediaDataService that can be instantiated without constructor arguments
        $mediaDataServiceStub = new TestableMediaDataService();
        $mediaDataServiceStub->setMock($this->mediaDataService);

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub);
        GeneralUtility::addInstance(TypoScriptService::class, $this->typoScriptService);

        $this->typoScriptService->expects(self::once())
            ->method('convertTypoScriptArrayToPlainArray')
            ->with($processorConfiguration['settings.'])
            ->willReturn($convertedSettings);

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer');

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->with($this->fileInterface, $convertedSettings)
            ->willReturn(['type' => 'image', 'processed' => true]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('files', $result);
        self::assertSame(['type' => 'image', 'processed' => true], $result['files'][0]);
    }

    #[Test]
    #[DataProvider('processVariousConfigurationsDataProvider')]
    public function processWorksWithVariousConfigurations(array $config, string $expectedTargetName): void
    {
        $dataPath = $config['data'];
        $processedData = [$dataPath => [$this->fileInterface]];

        $this->contentObjectRenderer->expects(self::exactly(2))
            ->method('stdWrapValue')
            ->willReturnMap([
                ['data', $config, '', $dataPath],
                ['as', $config, 'files', $expectedTargetName],
            ]);

        // Create a testable MediaDataService that can be instantiated without constructor arguments
        $mediaDataServiceStub = new TestableMediaDataService();
        $mediaDataServiceStub->setMock($this->mediaDataService);

        // Mock GeneralUtility::makeInstance for this test
        GeneralUtility::addInstance(MediaDataService::class, $mediaDataServiceStub);
        GeneralUtility::addInstance(TypoScriptService::class, $this->typoScriptService);

        // TypoScriptService is not called when there are no settings

        $this->mediaDataService->expects(self::once())
            ->method('setContentObjectRenderer');

        $this->mediaDataService->expects(self::once())
            ->method('process')
            ->with($this->fileInterface, [])
            ->willReturn(['processed' => true]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $config,
            $processedData
        );

        self::assertArrayHasKey($expectedTargetName, $result);
        self::assertSame([['processed' => true]], $result[$expectedTargetName]);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function processVariousConfigurationsDataProvider(): array
    {
        return [
            'with minimal config' => [
                'config' => [
                    'data' => 'files',
                ],
                'expectedTargetName' => 'files',
            ],
            'with different data path' => [
                'config' => [
                    'data' => 'gallery',
                    'as' => 'images',
                ],
                'expectedTargetName' => 'images',
            ],
        ];
    }
}
