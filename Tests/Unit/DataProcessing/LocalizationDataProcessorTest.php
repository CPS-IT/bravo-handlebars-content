<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\LocalizationDataProcessor;
use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for LocalizationDataProcessor
 *
 * @covers \Cpsit\BravoHandlebarsContent\DataProcessing\LocalizationDataProcessor
 *
 * @internal
 */
final class LocalizationDataProcessorTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private LocalizationDataProcessor $subject;

    private ContentObjectRenderer|MockObject $contentObjectRenderer;

    private LanguageServiceFactory|MockObject $languageServiceFactory;

    private LanguageService|MockObject $languageService;

    private MockObject|ServerRequestInterface $request;

    private MockObject|SiteLanguage $siteLanguage;

    private MockObject|Site $site;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->languageServiceFactory = $this->createMock(LanguageServiceFactory::class);
        $this->languageService = $this->createMock(LanguageService::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->siteLanguage = $this->createMock(SiteLanguage::class);
        $this->site = $this->createMock(Site::class);

        $this->subject = new LocalizationDataProcessor(
            $this->contentObjectRenderer,
            $this->languageServiceFactory
        );
    }

    #[Test]
    public function processReturnsProcessedDataWithLocalizedStrings(): void
    {
        $processorConfiguration = [
            'as' => 'labels',
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->with('as', $processorConfiguration)
            ->willReturn('labels');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::once())
            ->method('getAttribute')
            ->with('language')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->with($this->siteLanguage)
            ->willReturn($this->languageService);

        $this->languageService->expects(self::once())
            ->method('getLabelsFromResource')
            ->with('EXT:site/Resources/Private/Language/locallang.xlf')
            ->willReturn(['welcome' => 'Welcome!', 'goodbye' => 'Goodbye!']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('labels', $result);
        self::assertArrayHasKey('welcome', $result['labels']);
        self::assertArrayHasKey('goodbye', $result['labels']);
        self::assertSame('Welcome!', $result['labels']['welcome']);
        self::assertSame('Goodbye!', $result['labels']['goodbye']);
        self::assertArrayHasKey('existing', $result);
    }

    #[Test]
    public function processSkipsWhenIfConditionIsFalse(): void
    {
        $processorConfiguration = [
            'if.' => ['value' => '0'],
            'as' => 'labels',
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(false);

        $this->contentObjectRenderer->expects(self::never())
            ->method('stdWrapValue');

        $this->languageServiceFactory->expects(self::never())
            ->method('createFromSiteLanguage');

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
            'as' => 'labels',
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('checkIf')
            ->with($processorConfiguration['if.'])
            ->willReturn(true);

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('labels');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::once())
            ->method('getAttribute')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->willReturn($this->languageService);

        $this->languageService->expects(self::once())
            ->method('getLabelsFromResource')
            ->willReturn(['test' => 'Test Label']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('labels', $result);
        self::assertArrayHasKey('test', $result['labels']);
    }

    #[Test]
    public function processThrowsExceptionWhenAsConfigurationMissing(): void
    {
        $processorConfiguration = [
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
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
    public function processThrowsExceptionWhenSourcesConfigurationMissing(): void
    {
        $processorConfiguration = [
            'as' => 'labels',
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('labels');

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1717584873);
        $this->expectExceptionMessage('Missing or invalid configuration key `sources`');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
    }

    #[Test]
    public function processThrowsExceptionWhenSourcesConfigurationInvalid(): void
    {
        $processorConfiguration = [
            'as' => 'labels',
            'sources' => 'invalid_string',
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('labels');

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(1717584873);
        $this->expectExceptionMessage('Missing or invalid configuration key `sources`');

        $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );
    }

    #[Test]
    public function processHandlesMultipleSources(): void
    {
        $processorConfiguration = [
            'as' => 'translations',
            'sources' => [
                'EXT:site/Resources/Private/Language/locallang.xlf',
                'EXT:site/Resources/Private/Language/custom.xlf',
            ],
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('translations');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::once())
            ->method('getAttribute')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->willReturn($this->languageService);

        $this->languageService->expects(self::exactly(2))
            ->method('getLabelsFromResource')
            ->willReturnOnConsecutiveCalls(
                ['nav' => 'Navigation', 'home' => 'Home'],
                ['custom' => 'Custom Label', 'special' => 'Special']
            );

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('translations', $result);
        self::assertCount(4, $result['translations']);
        self::assertSame('Navigation', $result['translations']['nav']);
        self::assertSame('Home', $result['translations']['home']);
        self::assertSame('Custom Label', $result['translations']['custom']);
        self::assertSame('Special', $result['translations']['special']);
    }

    #[Test]
    public function processHandlesIncludePattern(): void
    {
        $processorConfiguration = [
            'as' => 'filteredLabels',
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
            'includePattern' => '/^nav\..*/',
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('filteredLabels');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::once())
            ->method('getAttribute')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->willReturn($this->languageService);

        $this->languageService->expects(self::once())
            ->method('getLabelsFromResource')
            ->willReturn([
                'nav.home' => 'Home',
                'nav.about' => 'About',
                'footer.copyright' => 'Copyright',
                'nav.contact' => 'Contact',
            ]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('filteredLabels', $result);
        self::assertCount(3, $result['filteredLabels']);
        self::assertArrayHasKey('nav.home', $result['filteredLabels']);
        self::assertArrayHasKey('nav.about', $result['filteredLabels']);
        self::assertArrayHasKey('nav.contact', $result['filteredLabels']);
        self::assertArrayNotHasKey('footer.copyright', $result['filteredLabels']);
    }

    #[Test]
    public function processHandlesSplitChar(): void
    {
        $processorConfiguration = [
            'as' => 'nestedLabels',
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
            'splitChar' => '.',
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('nestedLabels');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::once())
            ->method('getAttribute')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->willReturn($this->languageService);

        $this->languageService->expects(self::once())
            ->method('getLabelsFromResource')
            ->willReturn([
                'nav.home' => 'Home',
                'nav.about' => 'About',
                'form.submit' => 'Submit',
                'form.cancel' => 'Cancel',
            ]);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('nestedLabels', $result);
        self::assertArrayHasKey('nav', $result['nestedLabels']);
        self::assertArrayHasKey('form', $result['nestedLabels']);
        self::assertSame('Home', $result['nestedLabels']['nav']['home']);
        self::assertSame('About', $result['nestedLabels']['nav']['about']);
        self::assertSame('Submit', $result['nestedLabels']['form']['submit']);
        self::assertSame('Cancel', $result['nestedLabels']['form']['cancel']);
    }

    #[Test]
    public function processUsesDefaultLanguageWhenLanguageAttributeNotSet(): void
    {
        $processorConfiguration = [
            'as' => 'labels',
            'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
        ];
        $processedData = ['existing' => 'data'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn('labels');

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::exactly(2))
            ->method('getAttribute')
            ->willReturnCallback(function ($attribute) {
                if ($attribute === 'language') {
                    return;
                }
                if ($attribute === 'site') {
                    return $this->site;
                }
            });

        $this->site->expects(self::once())
            ->method('getDefaultLanguage')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->with($this->siteLanguage)
            ->willReturn($this->languageService);

        $this->languageService->expects(self::once())
            ->method('getLabelsFromResource')
            ->willReturn(['test' => 'Test']);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $processorConfiguration,
            $processedData
        );

        self::assertArrayHasKey('labels', $result);
        self::assertSame('Test', $result['labels']['test']);
    }

    #[Test]
    #[DataProvider('processWithComplexConfigurationsDataProvider')]
    public function processHandlesComplexConfigurations(array $config, array $sourceLabels, array $expectedResult): void
    {
        $processedData = ['data' => 'test'];

        $this->contentObjectRenderer->expects(self::once())
            ->method('stdWrapValue')
            ->willReturn($config['as']);

        $this->contentObjectRenderer->expects(self::once())
            ->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects(self::once())
            ->method('getAttribute')
            ->willReturn($this->siteLanguage);

        $this->languageServiceFactory->expects(self::once())
            ->method('createFromSiteLanguage')
            ->willReturn($this->languageService);

        $sourceCount = count($config['sources']);
        $this->languageService->expects(self::exactly($sourceCount))
            ->method('getLabelsFromResource')
            ->willReturn($sourceLabels);

        $result = $this->subject->process(
            $this->contentObjectRenderer,
            [],
            $config,
            $processedData
        );

        self::assertArrayHasKey($config['as'], $result);
        self::assertEquals($expectedResult, $result[$config['as']]);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function processWithComplexConfigurationsDataProvider(): array
    {
        return [
            'with include pattern and split char' => [
                'config' => [
                    'as' => 'processedLabels',
                    'sources' => ['EXT:site/Resources/Private/Language/locallang.xlf'],
                    'includePattern' => '/^form\..*/',
                    'splitChar' => '.',
                ],
                'sourceLabels' => [
                    'form.fields.name' => 'Name',
                    'form.fields.email' => 'Email',
                    'form.validation.required' => 'Required',
                    'nav.home' => 'Home',
                ],
                'expectedResult' => [
                    'form' => [
                        'fields' => ['name' => 'Name', 'email' => 'Email'],
                        'validation' => ['required' => 'Required'],
                    ],
                ],
            ],
            'multiple sources with pattern filter' => [
                'config' => [
                    'as' => 'filteredLabels',
                    'sources' => [
                        'EXT:site/Resources/Private/Language/locallang.xlf',
                        'EXT:site/Resources/Private/Language/custom.xlf',
                    ],
                    'includePattern' => '/^btn\..*/',
                ],
                'sourceLabels' => [
                    'btn.submit' => 'Submit',
                    'btn.cancel' => 'Cancel',
                    'nav.home' => 'Home',
                ],
                'expectedResult' => [
                    'btn.submit' => 'Submit',
                    'btn.cancel' => 'Cancel',
                ],
            ],
        ];
    }
}
