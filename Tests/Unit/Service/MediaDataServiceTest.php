<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\Service;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\MediaDataService;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for MediaDataService
 *
 * @covers \Cpsit\BravoHandlebarsContent\Service\MediaDataService
 *
 * @internal
 */
final class MediaDataServiceTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private MediaDataService $subject;

    private ContentObjectRenderer|MockObject $contentObjectRenderer;

    private MediaProcessorInterface|MockObject $mediaProcessor1;

    private MediaProcessorInterface|MockObject $mediaProcessor2;

    private FileInterface|MockObject $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->mediaProcessor1 = $this->createMock(MediaProcessorInterface::class);
        $this->mediaProcessor2 = $this->createMock(MediaProcessorInterface::class);
        $this->file = $this->createMock(FileInterface::class);

        $processors = [$this->mediaProcessor1, $this->mediaProcessor2];

        $this->subject = new MediaDataService(
            new \ArrayIterator($processors),
            $this->contentObjectRenderer
        );
    }

    #[Test]
    public function processReturnsProcessedMediaData(): void
    {
        $config = [
            'width' => '800',
            'height' => '600',
            'quality' => '85',
        ];

        $expectedResult = [
            'type' => 'image',
            'url' => '/fileadmin/user_upload/test.jpg',
            'width' => 800,
            'height' => 600,
            'alt' => 'Test Image',
            'title' => 'Test Title',
        ];

        $this->mediaProcessor1->expects(self::once())
            ->method('canProcess')
            ->with($this->file)
            ->willReturn(true);

        $this->mediaProcessor1->expects(self::once())
            ->method('process')
            ->with($this->file, $config)
            ->willReturn($expectedResult);

        $this->mediaProcessor2->expects(self::never())
            ->method('canProcess');

        $result = $this->subject->process($this->file, $config);

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function processUsesFirstCompatibleProcessor(): void
    {
        $config = ['maxWidth' => '1200'];

        $this->mediaProcessor1->expects(self::once())
            ->method('canProcess')
            ->with($this->file)
            ->willReturn(false);

        $this->mediaProcessor2->expects(self::once())
            ->method('canProcess')
            ->with($this->file)
            ->willReturn(true);

        $this->mediaProcessor2->expects(self::once())
            ->method('process')
            ->with($this->file, $config)
            ->willReturn(['type' => 'video', 'url' => '/test.mp4']);

        $result = $this->subject->process($this->file, $config);

        self::assertSame(['type' => 'video', 'url' => '/test.mp4'], $result);
    }

    #[Test]
    public function processWorksWithEmptyConfig(): void
    {
        $expectedResult = [
            'type' => 'image',
            'url' => '/fileadmin/test.jpg',
        ];

        $this->mediaProcessor1->expects(self::once())
            ->method('canProcess')
            ->willReturn(true);

        $this->mediaProcessor1->expects(self::once())
            ->method('process')
            ->with($this->file, [])
            ->willReturn($expectedResult);

        $result = $this->subject->process($this->file);

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function processSetsContentObjectRendererOnAwareProcessors(): void
    {
        // Create a processor that implements ContentRendererAwareInterface
        $contentRendererAwareProcessor = $this->createMockForIntersectionOfInterfaces([
            MediaProcessorInterface::class,
            ContentRendererAwareInterface::class,
        ]);

        $processors = [$contentRendererAwareProcessor];

        $this->subject = new MediaDataService(
            new \ArrayIterator($processors),
            $this->contentObjectRenderer
        );

        $contentRendererAwareProcessor->expects(self::once())
            ->method('canProcess')
            ->with($this->file)
            ->willReturn(true);

        $contentRendererAwareProcessor->expects(self::once())
            ->method('setContentObjectRenderer')
            ->with($this->contentObjectRenderer);

        $contentRendererAwareProcessor->expects(self::once())
            ->method('process')
            ->with($this->file, [])
            ->willReturn(['processed' => true]);

        $result = $this->subject->process($this->file);

        self::assertSame(['processed' => true], $result);
    }

    #[Test]
    public function processDoesNotSetContentObjectRendererOnNonAwareProcessors(): void
    {
        $this->mediaProcessor1->expects(self::once())
            ->method('canProcess')
            ->willReturn(true);

        // Ensure setContentObjectRenderer is never called on regular processors

        $this->mediaProcessor1->expects(self::once())
            ->method('process')
            ->willReturn(['type' => 'image']);

        $this->subject->process($this->file);
    }

    #[Test]
    public function setContentObjectRendererUpdatesRenderer(): void
    {
        $newContentObjectRenderer = $this->createMock(ContentObjectRenderer::class);

        $this->subject->setContentObjectRenderer($newContentObjectRenderer);

        // Test that the new renderer is used when processing content-renderer-aware processors
        $contentRendererAwareProcessor = $this->createMockForIntersectionOfInterfaces([
            MediaProcessorInterface::class,
            ContentRendererAwareInterface::class,
        ]);

        $processors = [$contentRendererAwareProcessor];

        $this->subject = new MediaDataService(
            new \ArrayIterator($processors),
            $this->contentObjectRenderer
        );

        // Set the new renderer
        $this->subject->setContentObjectRenderer($newContentObjectRenderer);

        $contentRendererAwareProcessor->expects(self::once())
            ->method('canProcess')
            ->willReturn(true);

        $contentRendererAwareProcessor->expects(self::once())
            ->method('setContentObjectRenderer')
            ->with($newContentObjectRenderer); // Should use the new renderer

        $contentRendererAwareProcessor->expects(self::once())
            ->method('process')
            ->willReturn(['updated' => true]);

        $result = $this->subject->process($this->file);

        self::assertSame(['updated' => true], $result);
    }

    #[Test]
    public function processHandlesMultipleProcessorsFallback(): void
    {
        $processor3 = $this->createMock(MediaProcessorInterface::class);

        $processors = [$this->mediaProcessor1, $this->mediaProcessor2, $processor3];

        $this->subject = new MediaDataService(
            new \ArrayIterator($processors),
            $this->contentObjectRenderer
        );

        $this->mediaProcessor1->expects(self::once())
            ->method('canProcess')
            ->willReturn(false);

        $this->mediaProcessor2->expects(self::once())
            ->method('canProcess')
            ->willReturn(false);

        $processor3->expects(self::once())
            ->method('canProcess')
            ->willReturn(true);

        $processor3->expects(self::once())
            ->method('process')
            ->willReturn(['fallback' => 'processor']);

        $result = $this->subject->process($this->file);

        self::assertSame(['fallback' => 'processor'], $result);
    }

    #[Test]
    public function processWithComplexConfiguration(): void
    {
        $complexConfig = [
            'image' => [
                'cropVariants' => [
                    'desktop' => ['maxWidth' => 1200, 'quality' => 85],
                    'tablet' => ['maxWidth' => 768, 'quality' => 80],
                    'mobile' => ['maxWidth' => 320, 'quality' => 75],
                ],
            ],
            'additionalAttributes' => [
                'loading' => 'lazy',
                'decoding' => 'async',
            ],
        ];

        $expectedResult = [
            'type' => 'image',
            'url' => '/fileadmin/processed.jpg',
            'cropVariants' => [
                'desktop' => ['url' => '/processed_desktop.jpg', 'width' => 1200],
                'tablet' => ['url' => '/processed_tablet.jpg', 'width' => 768],
                'mobile' => ['url' => '/processed_mobile.jpg', 'width' => 320],
            ],
            'additionalAttributes' => [
                'loading' => 'lazy',
                'decoding' => 'async',
            ],
        ];

        $this->mediaProcessor1->expects(self::once())
            ->method('canProcess')
            ->willReturn(true);

        $this->mediaProcessor1->expects(self::once())
            ->method('process')
            ->with($this->file, $complexConfig)
            ->willReturn($expectedResult);

        $result = $this->subject->process($this->file, $complexConfig);

        self::assertSame($expectedResult, $result);
    }
}
