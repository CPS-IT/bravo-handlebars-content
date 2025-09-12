<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\Service;

use Cpsit\BravoHandlebarsContent\Service\FileLinkService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for FileLinkService
 *
 * @covers \Cpsit\BravoHandlebarsContent\Service\FileLinkService
 */
final class FileLinkServiceTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private MockObject $fileReference;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for a file reference object
        $this->fileReference = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getPublicUrl', 'hasProperty', 'getProperty'])
            ->getMock();
    }

    #[Test]
    public function resolveFileLikReturnsBasicFileInfo(): void
    {
        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/test.pdf');

        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('hasProperty')
            ->willReturnCallback(function ($property) {
                return in_array($property, ['title', 'name', 'size', 'extension']);
            });

        $this->fileReference->expects(self::exactly(4))
            ->method('getProperty')
            ->willReturnMap([
                ['title', 'Test Document'],
                ['name', 'test.pdf'],
                ['size', 1048576], // 1MB in bytes
                ['extension', 'pdf']
            ]);

        $result = FileLinkService::resolveFileLik($this->fileReference);

        $expected = [
            'url' => 'https://example.com/fileadmin/test.pdf',
            'title' => 'Test Document',
            'name' => 'test.pdf',
            'size' => '1.00 MB',
            'extension' => 'pdf'
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function resolveFileLikUsesFileNameAsTitleWhenTitleEmpty(): void
    {
        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/document.docx');

        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('hasProperty')
            ->willReturnCallback(function ($property) {
                return in_array($property, ['title', 'name', 'extension']);
            });

        $this->fileReference->expects(self::exactly(3))
            ->method('getProperty')
            ->willReturnMap([
                ['title', ''], // Empty title
                ['name', 'document.docx'],
                ['extension', 'docx']
            ]);

        $result = FileLinkService::resolveFileLik($this->fileReference);

        self::assertSame('document.docx', $result['title']); // Should use name as title
        self::assertSame('document.docx', $result['name']);
        self::assertSame('docx', $result['extension']);
    }

    #[Test]
    public function resolveFileLikWithCustomProperties(): void
    {
        $customProperties = ['title', 'size', 'extension'];

        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/custom.jpg');

        $this->fileReference->expects(self::exactly(count($customProperties)))
            ->method('hasProperty')
            ->willReturnCallback(function ($property) use ($customProperties) {
                return in_array($property, $customProperties);
            });

        $this->fileReference->expects(self::exactly(3))
            ->method('getProperty')
            ->willReturnMap([
                ['title', 'Custom Image'],
                ['size', 512000], // 500KB
                ['extension', 'jpg']
            ]);

        $result = FileLinkService::resolveFileLik($this->fileReference, $customProperties);

        $expected = [
            'url' => 'https://example.com/fileadmin/custom.jpg',
            'title' => 'Custom Image',
            'size' => '500.00 KB',
            'extension' => 'jpg'
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function resolveFileLikSkipsPropertiesNotAvailable(): void
    {
        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/minimal.txt');

        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('hasProperty')
            ->willReturnCallback(function ($property) {
                return $property === 'name'; // Only name property is available
            });

        $this->fileReference->expects(self::once())
            ->method('getProperty')
            ->with('name')
            ->willReturn('minimal.txt');

        $result = FileLinkService::resolveFileLik($this->fileReference);

        $expected = [
            'url' => 'https://example.com/fileadmin/minimal.txt',
            'name' => 'minimal.txt'
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function resolveFileLikHandlesAllFileProperties(): void
    {
        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/complete.pdf');

        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('hasProperty')
            ->willReturn(true); // All properties available

        $propertyValues = [
            'title' => 'Complete Document',
            'name' => 'complete.pdf',
            'description' => 'A complete test document',
            'download_name' => 'download-complete.pdf',
            'size' => 2048000, // ~2MB
            'extension' => 'pdf',
            'language' => 'en',
            'copyright' => '© 2024 Test Corp',
            'url' => 'https://example.com/fileadmin/complete.pdf',
            'accessible' => 1
        ];

        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('getProperty')
            ->willReturnCallback(function ($property) use ($propertyValues) {
                return $propertyValues[$property] ?? null;
            });

        $result = FileLinkService::resolveFileLik($this->fileReference);

        $expected = [
            'url' => 'https://example.com/fileadmin/complete.pdf',
            'title' => 'Complete Document',
            'name' => 'complete.pdf',
            'description' => 'A complete test document',
            'downloadName' => 'download-complete.pdf', // Converted to camelCase
            'size' => '2.00 MB', // Formatted
            'extension' => 'pdf',
            'language' => 'en',
            'copyright' => '© 2024 Test Corp',
            'accessible' => 1
        ];

        self::assertEquals($expected, $result);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function formatFileSizeDataProvider(): array
    {
        return [
            'zero bytes' => [0, '0.00 B'],
            'bytes' => [512, '512.00 B'],
            'kilobytes' => [1024, '1.00 KB'],
            '1.5 KB' => [1536, '1.50 KB'],
            'megabytes' => [1048576, '1.00 MB'], // 1024^2
            '2.5 MB' => [2621440, '2.50 MB'],
            'gigabytes' => [1073741824, '1.00 GB'], // 1024^3
            '1.25 GB' => [1342177280, '1.25 GB'],
            'terabytes' => [1099511627776, '1.00 TB'], // 1024^4
            'large file' => [5368709120, '5.00 GB'] // 5GB
        ];
    }

    #[Test]
    #[DataProvider('formatFileSizeDataProvider')]
    public function formatFileSizeFormatsCorrectly(int $size, string $expected): void
    {
        $result = FileLinkService::formatFileSize($size);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function formatFileSizeHandlesSmallFiles(): void
    {
        // Test various small file sizes
        self::assertSame('1.00 B', FileLinkService::formatFileSize(1));
        self::assertSame('100.00 B', FileLinkService::formatFileSize(100));
        self::assertSame('999.00 B', FileLinkService::formatFileSize(999));
    }

    #[Test]
    public function formatFileSizeHandlesMediumFiles(): void
    {
        // Test various KB sizes
        self::assertSame('1.50 KB', FileLinkService::formatFileSize(1536));
        self::assertSame('10.00 KB', FileLinkService::formatFileSize(10240));
        self::assertSame('500.50 KB', FileLinkService::formatFileSize(512512));
    }

    #[Test]
    public function formatFileSizeHandlesLargeFiles(): void
    {
        // Test various MB and GB sizes
        self::assertSame('10.00 MB', FileLinkService::formatFileSize(10485760));
        self::assertSame('100.00 MB', FileLinkService::formatFileSize(104857600));
        self::assertSame('1.50 GB', FileLinkService::formatFileSize(1610612736));
    }

    #[Test]
    public function resolveFileLikConvertsUnderscoreProperties(): void
    {
        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/underscore.pdf');

        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('hasProperty')
            ->willReturnCallback(function ($property) {
                return in_array($property, ['download_name', 'name']);
            });

        $this->fileReference->expects(self::exactly(2))
            ->method('getProperty')
            ->willReturnMap([
                ['download_name', 'custom_download_name.pdf'],
                ['name', 'underscore.pdf']
            ]);

        $result = FileLinkService::resolveFileLik($this->fileReference);

        // download_name should be converted to downloadName
        self::assertArrayHasKey('downloadName', $result);
        self::assertSame('custom_download_name.pdf', $result['downloadName']);
        self::assertArrayNotHasKey('download_name', $result);
    }

    #[Test]
    public function resolveFileLikHandlesComplexScenario(): void
    {
        $this->fileReference->expects(self::once())
            ->method('getPublicUrl')
            ->willReturn('https://example.com/fileadmin/user_upload/reports/annual-report-2024.pdf');

        // Simulate a scenario where only some properties are available
        $availableProperties = ['title', 'name', 'description', 'size', 'extension', 'download_name'];
        
        $this->fileReference->expects(self::exactly(count(FileLinkService::FILE_PROPERTIES)))
            ->method('hasProperty')
            ->willReturnCallback(function ($property) use ($availableProperties) {
                return in_array($property, $availableProperties);
            });

        $this->fileReference->expects(self::exactly(count($availableProperties)))
            ->method('getProperty')
            ->willReturnMap([
                ['title', ''], // Empty title - should use name
                ['name', 'annual-report-2024.pdf'],
                ['description', 'Annual financial report for 2024'],
                ['size', 15728640], // 15MB
                ['extension', 'pdf'],
                ['download_name', 'Annual_Report_2024_Final.pdf']
            ]);

        $result = FileLinkService::resolveFileLik($this->fileReference);

        $expected = [
            'url' => 'https://example.com/fileadmin/user_upload/reports/annual-report-2024.pdf',
            'title' => 'annual-report-2024.pdf', // Uses name because title was empty
            'name' => 'annual-report-2024.pdf',
            'description' => 'Annual financial report for 2024',
            'size' => '15.00 MB', // Formatted size
            'extension' => 'pdf',
            'downloadName' => 'Annual_Report_2024_Final.pdf' // Converted from download_name
        ];

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function filePropertiesConstantContainsExpectedProperties(): void
    {
        $expectedProperties = [
            'title', 'name', 'description', 'download_name', 'size',
            'extension', 'language', 'copyright', 'url', 'accessible'
        ];

        self::assertSame($expectedProperties, FileLinkService::FILE_PROPERTIES);
    }

    #[Test]
    public function fileSizeUnitsConstantContainsExpectedUnits(): void
    {
        $expectedUnits = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        self::assertSame($expectedUnits, FileLinkService::FILE_SIZE_UNITS);
    }
}