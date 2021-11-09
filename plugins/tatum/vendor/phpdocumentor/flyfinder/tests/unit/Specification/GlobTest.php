<?php

declare(strict_types=1);

namespace Flyfinder\Specification;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function is_array;
use function sprintf;

/**
 * @coversDefaultClass \Flyfinder\Specification\Glob
 * @covers ::<private>
 * @covers ::isSatisfiedBy
 * @covers ::__construct
 */
final class GlobTest extends TestCase
{
    /**
     * @param mixed[] $file
     *
     * @dataProvider matchingPatternFileProvider
     * @dataProvider matchingPatternFileWithEscapeCharProvider
     *
     * @psalm-param array{basename: string, path: string, stream: resource, dirname: string, type: string, extension: string} $file
     */
    public function testGlobIsMatching(string $pattern, array $file) : void
    {
        $glob = new Glob($pattern);

        $this->assertTrue(
            $glob->isSatisfiedBy($file),
            sprintf('Failed: %s to match %s', $pattern, $file['path'])
        );
    }

    /**
     * @param mixed[] $file
     *
     * @dataProvider notMatchingPatternFileProvider
     * @psalm-param array{basename: string, path: string, stream: resource, dirname: string, type: string, extension: string} $file
     */
    public function testGlobIsNotMatching(string $pattern, array $file) : void
    {
        $glob = new Glob($pattern);

        $this->assertFalse(
            $glob->isSatisfiedBy($file),
            sprintf('Failed: %s to match %s', $pattern, $file['path'])
        );
    }

    /**
     * @dataProvider invalidPatternProvider
     */
    public function testInvalidGlobThrows(string $pattern) : void
    {
        $this->expectException(InvalidArgumentException::class);
        new Glob($pattern);
    }

    /**
     * @covers ::canBeSatisfiedBySomethingBelow
     */
    public function testCanBeSatisfiedBySomethingBelow() : void
    {
        $glob = new Glob('/**/*');
        $this->assertTrue($glob->canBeSatisfiedBySomethingBelow(['path' => 'src']));
    }

    public function invalidPatternProvider() : Generator
    {
        $invalidPatterns = [
            '[aaa',
            '{aaa',
            '{a,{b}',
            'aaaa', //path must be absolute
        ];

        foreach ($invalidPatterns as $pattern) {
            yield $pattern => [$pattern];
        }
    }

    public function matchingPatternFileProvider() : Generator
    {
        $input = [
            '/*.php' => 'test.php',
            '/src/*' => 'src/test.php',
            '/src/**/*.php' => 'src/subdir/test.php',
            '/src/**/*' => 'src/subdir/second/test.php',
            '/src/{subdir,other}/*' => [
                'src/subdir/test.php',
                'src/other/test.php',
            ],
            '/src/subdir/test-[a-c].php' => [
                'src/subdir/test-a.php',
                'src/subdir/test-b.php',
                'src/subdir/test-c.php',
            ],
            '/src/subdir/test-[^a-c].php' => 'src/subdir/test-d.php',
            '/src/subdir/test-?.php' => [
                'src/subdir/test-a.php',
                'src/subdir/test-b.php',
                'src/subdir/test-c.php',
                'src/subdir/test-~.php',
            ],
            '/src/subdir/test-}.php' => 'src/subdir/test-}.php',
        ];

        yield from $this->toTestData($input);
    }

    public function matchingPatternFileWithEscapeCharProvider() : Generator
    {
        $escapeChars = [
            '*',
            '?',
            '{',
            '}',
            '[',
            ']',
            '-',
            '^',
            '$',
            '~',
            '\\',
            '\\\\',
        ];

        foreach ($escapeChars as $char) {
            $file = sprintf('/src/test\\%s.php', $char);

            yield $file => [
                $file,
                ['path' => sprintf('src/test%s.php', $char)],
            ];
        }
    }

    public function notMatchingPatternFileProvider() : Generator
    {
        $input = [
            '/*.php' => 'test.css',
            '/src/*' => 'src/subdir/test.php',
            '/src/**/*.php' => 'src/subdir/test.css',
            '/src/subdir/test-[a-c].php' => 'src/subdir/test-d.php',
            '/src/subdir/test-[^a-c].php' => [
                'src/subdir/test-a.php',
                'src/subdir/test-b.php',
                'src/subdir/test-c.php',
            ],
            '/src' => 'test/file.php',
        ];

        yield from $this->toTestData($input);
    }

    /**
     * @param mixed[] $input
     */
    private function toTestData(array $input) : Generator
    {
        foreach ($input as $glob => $path) {
            if (!is_array($path)) {
                $path = [$path];
            }

            foreach ($path as $key => $item) {
                yield ($key !== 0 ? $key . ' - ' : '') . $glob => [
                    $glob,
                    ['path' => $item],
                ];
            }
        }
    }
}
