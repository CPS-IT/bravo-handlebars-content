<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\Tests\Unit\Utility;

use Cpsit\BravoHandlebarsContent\Utility\StringUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for StringUtility
 *
 * @covers \Cpsit\BravoHandlebarsContent\Utility\StringUtility
 */
final class StringUtilityTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function hyphenToLowerCamelCaseConvertsSimpleString(): void
    {
        $input = 'hello-world';
        $expected = 'helloWorld';

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function hyphenToLowerCamelCaseConvertsMultipleHyphens(): void
    {
        $input = 'this-is-a-test-string';
        $expected = 'thisIsATestString';

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function hyphenToLowerCamelCaseHandlesEmptyString(): void
    {
        $input = '';
        $expected = '';

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function hyphenToLowerCamelCaseHandlesStringWithoutHyphens(): void
    {
        $input = 'alreadycamelcase';
        $expected = 'alreadycamelcase';

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function hyphenToLowerCamelCaseHandlesStringWithLeadingHyphen(): void
    {
        $input = '-leading-hyphen';
        $expected = 'leadingHyphen'; // lcfirst makes it lowercase first letter

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function hyphenToLowerCamelCaseHandlesStringWithTrailingHyphen(): void
    {
        $input = 'trailing-hyphen-';
        $expected = 'trailingHyphen';

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function hyphenToLowerCamelCaseHandlesConsecutiveHyphens(): void
    {
        $input = 'double--hyphen';
        $expected = 'doubleHyphen';

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    #[DataProvider('specialCharacterDataProvider')]
    public function hyphenToLowerCamelCaseHandlesSpecialCharacters(string $input, string $expected): void
    {
        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }

    public static function specialCharacterDataProvider(): array
    {
        return [
            'numbers' => ['test-123-string', 'test123String'],
            'mixed case' => ['Test-MIXED-Case', 'testMIXEDCase'],
            'unicode' => ['test-ä-string', 'testÄString'],
            'special chars' => ['test-@-string', 'test@String'],
        ];
    }

    #[Test]
    public function hyphenToLowerCamelCasePreservesExistingCamelCase(): void
    {
        $input = 'already-camelCase-here';
        $expected = 'alreadyCamelcaseHere'; // strtolower() affects the whole string

        $result = StringUtility::hyphenToLowerCamelCase($input);

        self::assertSame($expected, $result);
    }
}
