<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\Service;

use Cpsit\BravoHandlebarsContent\Service\LinkService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResult;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for LinkService
 *
 * @covers \Cpsit\BravoHandlebarsContent\Service\LinkService
 *
 * @internal
 */
final class LinkServiceTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private LinkService $subject;

    private ContentObjectRenderer|MockObject $contentObjectRenderer;

    private LinkResultInterface|MockObject $linkResult;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->contentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $this->linkResult = $this->createMock(LinkResultInterface::class);

        $this->subject = new LinkService($this->contentObjectRenderer);
    }

    #[Test]
    public function resolveTypoLinkReturnsLinkResultForValidLink(): void
    {
        $typoLink = 't3://page?uid=123';

        $this->contentObjectRenderer->expects(self::once())
            ->method('typoLink')
            ->with('', [
                'parameter' => $typoLink,
                'returnLast' => 'result',
            ])
            ->willReturn($this->linkResult);

        $result = $this->subject->resolveTypoLink($typoLink);

        self::assertSame($this->linkResult, $result);
    }

    #[Test]
    public function resolveTypoLinkReturnsEmptyLinkResultForInvalidLink(): void
    {
        $typoLink = 'invalid-link';

        $this->contentObjectRenderer->expects(self::once())
            ->method('typoLink')
            ->with('', [
                'parameter' => $typoLink,
                'returnLast' => 'result',
            ])
            ->willReturn('invalid-result'); // Not a LinkResultInterface

        $result = $this->subject->resolveTypoLink($typoLink);

        self::assertInstanceOf(LinkResult::class, $result);
        self::assertSame('', $result->getUrl());
        self::assertSame('', $result->getLinkText());
    }

    #[Test]
    public function parseTypoLinkReturnsLinkResultForValidLink(): void
    {
        $typoLink = 't3://page?uid=456';

        $this->contentObjectRenderer->expects(self::once())
            ->method('typoLink')
            ->with('', [
                'parameter' => $typoLink,
                'returnLast' => 'result',
            ])
            ->willReturn($this->linkResult);

        $result = $this->subject->parseTypoLink($typoLink);

        self::assertSame($this->linkResult, $result);
    }

    #[Test]
    public function parseTypoLinkReturnsNullForInvalidLink(): void
    {
        $typoLink = 'invalid-link';

        $this->contentObjectRenderer->expects(self::once())
            ->method('typoLink')
            ->willReturn('invalid-result'); // Not a LinkResultInterface

        $result = $this->subject->parseTypoLink($typoLink);

        self::assertNull($result);
    }

    #[Test]
    public function linkResultToArrayConvertsLinkResultCorrectly(): void
    {
        $attributes = [
            'href' => 'https://example.com',
            'title' => 'Example Link',
            'class' => 'link-class',
            'target' => '_blank',
            'data-test' => 'test-value',
            'aria-label' => 'Accessible label',
        ];

        $this->linkResult->expects(self::once())
            ->method('getUrl')
            ->willReturn('https://example.com');

        $this->linkResult->expects(self::once())
            ->method('getLinkText')
            ->willReturn('Example Link Text');

        $this->linkResult->expects(self::exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                ['title', 'Example Link'],
                ['class', 'link-class'],
            ]);

        $this->linkResult->expects(self::once())
            ->method('getTarget')
            ->willReturn('_blank');

        $this->linkResult->expects(self::once())
            ->method('getType')
            ->willReturn('page');

        $this->linkResult->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);

        $result = $this->subject->linkResultToArray($this->linkResult);

        $expectedResult = [
            'url' => 'https://example.com',
            'label' => 'Example Link Text',
            'title' => 'Example Link',
            'class' => 'link-class',
            'target' => '_blank',
            'type' => 'page',
            'additionalAttributes' => [
                'dataTest' => 'test-value',
                'ariaLabel' => 'Accessible label',
            ],
        ];

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function linkResultToArrayHandlesEmptyAttributes(): void
    {
        $this->linkResult->expects(self::once())
            ->method('getUrl')
            ->willReturn('https://example.com');

        $this->linkResult->expects(self::once())
            ->method('getLinkText')
            ->willReturn('Link Text');

        $this->linkResult->expects(self::exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                ['title', ''],
                ['class', ''],
            ]);

        $this->linkResult->expects(self::once())
            ->method('getTarget')
            ->willReturn('');

        $this->linkResult->expects(self::once())
            ->method('getType')
            ->willReturn('url');

        $this->linkResult->expects(self::once())
            ->method('getAttributes')
            ->willReturn([
                'href' => 'https://example.com',
            ]);

        $result = $this->subject->linkResultToArray($this->linkResult);

        $expectedResult = [
            'url' => 'https://example.com',
            'label' => 'Link Text',
            'title' => '',
            'class' => '',
            'target' => '',
            'type' => 'url',
            'additionalAttributes' => [],
        ];

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function linkResultToArrayHandlesNullAttributes(): void
    {
        $this->linkResult->expects(self::once())
            ->method('getUrl')
            ->willReturn('https://example.com');

        $this->linkResult->expects(self::once())
            ->method('getLinkText')
            ->willReturn('Link Text');

        $this->linkResult->expects(self::exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                ['title', null],
                ['class', null],
            ]);

        $this->linkResult->expects(self::once())
            ->method('getTarget')
            ->willReturn('_self');

        $this->linkResult->expects(self::once())
            ->method('getType')
            ->willReturn('page');

        $this->linkResult->expects(self::once())
            ->method('getAttributes')
            ->willReturn([]);

        $result = $this->subject->linkResultToArray($this->linkResult);

        $expectedResult = [
            'url' => 'https://example.com',
            'label' => 'Link Text',
            'title' => '',
            'class' => '',
            'target' => '_self',
            'type' => 'page',
            'additionalAttributes' => [],
        ];

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function setContentObjectRendererUpdatesRenderer(): void
    {
        $newContentObjectRenderer = $this->createMock(ContentObjectRenderer::class);
        $typoLink = 't3://page?uid=789';

        $this->subject->setContentObjectRenderer($newContentObjectRenderer);

        $newContentObjectRenderer->expects(self::once())
            ->method('typoLink')
            ->with('', [
                'parameter' => $typoLink,
                'returnLast' => 'result',
            ])
            ->willReturn($this->linkResult);

        $result = $this->subject->resolveTypoLink($typoLink);

        self::assertSame($this->linkResult, $result);
    }

    #[Test]
    #[DataProvider('linkTypesDataProvider')]
    public function resolveTypoLinkHandlesDifferentLinkTypes(string $typoLink, string $expectedType, string $expectedUrl): void
    {
        $this->contentObjectRenderer->expects(self::once())
            ->method('typoLink')
            ->willReturn($this->linkResult);

        $result = $this->subject->resolveTypoLink($typoLink);

        self::assertSame($this->linkResult, $result);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function linkTypesDataProvider(): array
    {
        return [
            'page link' => [
                'typoLink' => 't3://page?uid=123',
                'expectedType' => 'page',
                'expectedUrl' => 'https://example.com/page',
            ],
            'external link' => [
                'typoLink' => 'https://external.com',
                'expectedType' => 'url',
                'expectedUrl' => 'https://external.com',
            ],
            'email link' => [
                'typoLink' => 'mailto:test@example.com',
                'expectedType' => 'email',
                'expectedUrl' => 'mailto:test@example.com',
            ],
            'file link' => [
                'typoLink' => 't3://file?uid=456',
                'expectedType' => 'file',
                'expectedUrl' => 'https://example.com/fileadmin/test.pdf',
            ],
        ];
    }

    #[Test]
    public function filterAdditionalAttributesConvertsAttributeNames(): void
    {
        $attributes = [
            'href' => 'https://example.com', // Should be filtered out
            'title' => 'Example Title', // Should be filtered out
            'class' => 'example-class', // Should be filtered out
            'target' => '_blank', // Should be filtered out
            'data-test' => 'test-value', // Should be converted to dataTest
            'aria-label' => 'Accessible label', // Should be converted to ariaLabel
            'custom-attribute' => 'custom-value', // Should be converted to customAttribute
        ];

        $this->linkResult->expects(self::once())
            ->method('getUrl')
            ->willReturn('https://example.com');

        $this->linkResult->expects(self::once())
            ->method('getLinkText')
            ->willReturn('Link Text');

        $this->linkResult->expects(self::exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                ['title', 'Example Title'],
                ['class', 'example-class'],
            ]);

        $this->linkResult->expects(self::once())
            ->method('getTarget')
            ->willReturn('_blank');

        $this->linkResult->expects(self::once())
            ->method('getType')
            ->willReturn('url');

        $this->linkResult->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);

        $result = $this->subject->linkResultToArray($this->linkResult);

        $expectedAdditionalAttributes = [
            'dataTest' => 'test-value',
            'ariaLabel' => 'Accessible label',
            'customAttribute' => 'custom-value',
        ];

        self::assertSame($expectedAdditionalAttributes, $result['additionalAttributes']);
    }

    #[Test]
    public function linkResultToArrayHandlesComplexScenario(): void
    {
        $attributes = [
            'href' => 'https://example.com/complex-page',
            'title' => 'Complex Example Link',
            'class' => 'btn btn-primary',
            'target' => '_blank',
            'data-toggle' => 'modal',
            'data-target' => '#exampleModal',
            'aria-describedby' => 'tooltip-123',
            'role' => 'button',
        ];

        $this->linkResult->expects(self::once())
            ->method('getUrl')
            ->willReturn('https://example.com/complex-page');

        $this->linkResult->expects(self::once())
            ->method('getLinkText')
            ->willReturn('Click me');

        $this->linkResult->expects(self::exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                ['title', 'Complex Example Link'],
                ['class', 'btn btn-primary'],
            ]);

        $this->linkResult->expects(self::once())
            ->method('getTarget')
            ->willReturn('_blank');

        $this->linkResult->expects(self::once())
            ->method('getType')
            ->willReturn('page');

        $this->linkResult->expects(self::once())
            ->method('getAttributes')
            ->willReturn($attributes);

        $result = $this->subject->linkResultToArray($this->linkResult);

        $expectedResult = [
            'url' => 'https://example.com/complex-page',
            'label' => 'Click me',
            'title' => 'Complex Example Link',
            'class' => 'btn btn-primary',
            'target' => '_blank',
            'type' => 'page',
            'additionalAttributes' => [
                'dataToggle' => 'modal',
                'dataTarget' => '#exampleModal',
                'ariaDescribedby' => 'tooltip-123',
                'role' => 'button',
            ],
        ];

        self::assertSame($expectedResult, $result);
    }
}
