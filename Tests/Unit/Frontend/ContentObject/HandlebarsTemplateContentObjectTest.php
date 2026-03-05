<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\Frontend\ContentObject;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use Cpsit\BravoHandlebarsContent\Frontend\ContentObject\HandlebarsTemplateContentObject;
use Fr\Typo3Handlebars\Renderer\HandlebarsRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for HandlebarsTemplateContentObject
 *
 * @covers \Cpsit\BravoHandlebarsContent\Frontend\ContentObject\HandlebarsTemplateContentObject
 *
 * @internal
 */
final class HandlebarsTemplateContentObjectTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private HandlebarsTemplateContentObject $subject;

    private HandlebarsRenderer|MockObject $handlebarsRenderer;

    private AssetCollector|MockObject $assetCollector;

    private ContentObjectRenderer|MockObject $contentObjectRenderer;

    private ContentDataProcessor|MockObject $contentDataProcessor;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->handlebarsRenderer = $this->createMock(HandlebarsRenderer::class);
        $this->assetCollector = $this->createMock(AssetCollector::class);
        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->contentDataProcessor = $this->createMock(ContentDataProcessor::class);

        // Create subject instance
        $this->subject = new HandlebarsTemplateContentObject(
            $this->assetCollector,
            $this->contentDataProcessor,
            $this->handlebarsRenderer
        );

        // Mock the request first to avoid initialization issues
        $request = $this->createMock(ServerRequest::class);
        $request->expects(self::any())
            ->method('withAttribute')
            ->willReturnSelf();
        $this->subject->setRequest($request);

        // Set the content object renderer after request is set
        $this->subject->setContentObjectRenderer($this->contentObjectRenderer);
    }

    #[Test]
    public function renderReturnsRenderedTemplate(): void
    {
        $conf = [
            'templateName' => '@ce-text',
        ];

        $expectedHtml = '<div class="ce-text"><h1>Test Header</h1><p>Test content</p></div>';
        $data = ['header' => 'Test Header', 'bodytext' => 'Test content'];
        $variables = ['data' => $data, 'current' => null];

        // Mock the content object renderer data and stdWrapValue
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;
        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('templateName', $conf)
            ->willReturn('@ce-text');

        // Mock data processing
        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($variables);

        // Mock renderer setDefaultData and render
        $this->handlebarsRenderer->expects(self::once())
            ->method('setDefaultData')
            ->with([]);

        $this->handlebarsRenderer->expects(self::once())
            ->method('render')
            ->with('@ce-text', $variables)
            ->willReturn($expectedHtml);

        $result = $this->subject->render($conf);

        self::assertSame($expectedHtml, $result);
    }

    #[Test]
    public function renderHandlesDataProcessing(): void
    {
        $conf = [
            'templateName' => '@ce-textmedia',
            'dataProcessing.' => [
                '10' => 'ceTextMedia',
                '10.' => [
                    'as' => 'mediaData',
                ],
            ],
        ];

        $data = ['header' => 'Media Header'];
        $variables = ['data' => $data, 'current' => null];
        $processedData = [
            'data' => $data,
            'current' => null,
            'mediaData' => [
                'headlines' => ['header' => 'Media Header'],
                'assets' => [],
            ],
        ];

        // Mock the content object renderer data and stdWrapValue
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;
        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('templateName', $conf)
            ->willReturn('@ce-textmedia');

        // Mock data processing to return processed data
        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($processedData);

        $expectedHtml = '<div class="ce-textmedia"><h1>Media Header</h1></div>';

        $this->handlebarsRenderer->expects(self::once())
            ->method('setDefaultData')
            ->with([]);

        $this->handlebarsRenderer->expects(self::once())
            ->method('render')
            ->with('@ce-textmedia', $processedData)
            ->willReturn($expectedHtml);

        $result = $this->subject->render($conf);

        self::assertSame($expectedHtml, $result);
    }

    #[Test]
    public function renderHandlesAssets(): void
    {
        $conf = [
            'templateName' => '@ce-with-assets',
            'assets.' => [
                'css.' => [
                    'custom' => [
                        'source' => 'fileadmin/css/custom.css',
                    ],
                ],
                'javaScript.' => [
                    'custom' => [
                        'source' => 'fileadmin/js/custom.js',
                    ],
                ],
            ],
        ];

        $data = ['header' => 'Asset Header'];
        $variables = ['data' => $data, 'current' => null];

        // Mock the content object renderer data and stdWrapValue
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;
        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('templateName', $conf)
            ->willReturn('@ce-with-assets');

        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($variables);

        $this->assetCollector->expects(self::once())
            ->method('addStyleSheet')
            ->with('custom', 'fileadmin/css/custom.css', [], ['priority' => false, 'useNonce' => false]);

        $this->assetCollector->expects(self::once())
            ->method('addJavaScript')
            ->with('custom', 'fileadmin/js/custom.js', [], ['priority' => false, 'useNonce' => false]);

        $this->handlebarsRenderer->expects(self::once())
            ->method('setDefaultData')
            ->with([]);

        $this->handlebarsRenderer->expects(self::once())
            ->method('render')
            ->with('@ce-with-assets', $variables)
            ->willReturn('<div>Content with assets</div>');

        $result = $this->subject->render($conf);

        self::assertSame('<div>Content with assets</div>', $result);
    }

    #[Test]
    public function renderThrowsExceptionWhenTemplateNameMissing(): void
    {
        $conf = [];
        $data = ['header' => 'Test'];
        $variables = ['data' => $data, 'current' => null];

        // Mock the content object renderer data
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;
        // stdWrapValue won't be called when templateName is empty
        $this->contentObjectRenderer->expects(self::never())
            ->method('stdWrapValue');

        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($variables);

        $this->handlebarsRenderer->expects(self::never())
            ->method('render');

        $this->expectException(InvalidConfigurationException::class);

        $this->subject->render($conf);
    }

    #[Test]
    public function renderHandlesRenderingException(): void
    {
        $conf = [
            'templateName' => '@invalid-template',
        ];

        $data = [];
        $variables = ['data' => $data, 'current' => null];

        // Mock the content object renderer data and stdWrapValue
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;
        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('templateName', $conf)
            ->willReturn('@invalid-template');

        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($variables);

        $this->handlebarsRenderer->expects(self::once())
            ->method('setDefaultData')
            ->with([]);

        $this->handlebarsRenderer->expects(self::once())
            ->method('render')
            ->with('@invalid-template', $variables)
            ->willThrowException(new \RuntimeException('Template not found'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Template not found');

        $this->subject->render($conf);
    }

    #[Test]
    public function renderHandlesEmptyConfiguration(): void
    {
        $conf = [];
        $data = [];
        $variables = ['data' => $data, 'current' => null];

        // Mock the content object renderer data
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;
        // stdWrapValue won't be called when templateName is empty
        $this->contentObjectRenderer->expects(self::never())
            ->method('stdWrapValue');

        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($variables);

        $this->expectException(InvalidConfigurationException::class);

        $this->subject->render($conf);
    }

    #[Test]
    public function renderProcessesTypoScriptConfiguration(): void
    {
        $conf = [
            'templateName' => '@ce-complex',
            'templateName.' => [
                'stdWrap.' => [
                    'wrap' => '@|',
                ],
            ],
        ];

        $data = ['header' => 'Complex Header'];
        $variables = ['data' => $data, 'current' => null];

        // Mock the content object renderer data
        $this->contentObjectRenderer->data = $data;
        $this->contentObjectRenderer->currentValKey = null;

        // Mock TypoScript processing
        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('templateName', $conf)
            ->willReturn('@ce-complex');

        $this->contentDataProcessor->expects(self::once())
            ->method('process')
            ->with($this->contentObjectRenderer, $conf, $variables)
            ->willReturn($variables);

        $this->handlebarsRenderer->expects(self::once())
            ->method('setDefaultData')
            ->with([]);

        $this->handlebarsRenderer->expects(self::once())
            ->method('render')
            ->with('@ce-complex', $variables)
            ->willReturn('<div>Complex content</div>');

        $result = $this->subject->render($conf);

        self::assertSame('<div>Complex content</div>', $result);
    }
}
