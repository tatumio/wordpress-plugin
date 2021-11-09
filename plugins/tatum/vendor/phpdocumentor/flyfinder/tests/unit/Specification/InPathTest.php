<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace Flyfinder\Specification;

use Flyfinder\Path;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use function dirname;

/**
 * Test case for InPath
 *
 * @coversDefaultClass \Flyfinder\Specification\InPath
 * @covers ::<private>
 */
class InPathTest extends TestCase
{
    /** @var InPath */
    private $fixture;

    public function tearDown() : void
    {
        m::close();
    }

    /**
     * @uses \Flyfinder\Path
     *
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     * @dataProvider validDirnames
     */
    public function testExactMatch() : void
    {
        $absolutePath = 'absolute/path/to/file.txt';
        $spec         = new InPath(new Path($absolutePath));
        $this->assertTrue($spec->isSatisfiedBy([
            'type' => 'file',
            'path' => $absolutePath,
            'dirname' => $absolutePath,
            'filename' => 'file',
            'extension' => 'txt',
            'basename' => 'file.txt',
        ]));
    }

    private function useWildcardPath() : void
    {
        $this->fixture = new InPath(new Path('*dden?ir/n'));
    }

    /**
     * @uses \Flyfinder\Path
     *
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     * @dataProvider validDirnames
     */
    public function testIfSpecificationIsSatisfied(string $dirname) : void
    {
        $this->useWildcardPath();
        $this->assertTrue($this->fixture->isSatisfiedBy(['dirname' => $dirname]));
    }

    /**
     * @uses \Flyfinder\Path
     *
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     * @dataProvider validDirnames
     */
    public function testWithSingleDotSpec(string $dirname) : void
    {
        $spec = new InPath(new Path('.'));
        $this->assertTrue($spec->isSatisfiedBy(['dirname' => $dirname]));
    }

    /**
     * @uses \Flyfinder\Path
     *
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     * @dataProvider validDirnames
     */
    public function testWithCurrentDirSpec(string $dirname) : void
    {
        $spec = new InPath(new Path('./'));
        $this->assertTrue($spec->isSatisfiedBy(['dirname' => $dirname]));
    }

    /**
     * Data provider for testIfSpecificationIsSatisfied. Contains a few valid directory names
     *
     * @return string[][]
     */
    public function validDirnames() : array
    {
        return [
            ['.hiddendir/n'],
            ['.hiddendir/n/'],
            ['.hiddendir/n/somedir'],
            ['.hiddendir/n/somedir.txt'],
            ['ddenxir/n'],
        ];
    }

    /**
     * @uses \Flyfinder\Path
     *
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     * @dataProvider invalidDirnames
     */
    public function testIfSpecificationIsNotSatisfied(string $dirname) : void
    {
        $this->useWildcardPath();
        $this->assertFalse($this->fixture->isSatisfiedBy(['dirname' => $dirname]));
    }

    /**
     * Data provider for testIfSpecificationIsNotSatisfied. Contains a few invalid directory names
     *
     * @return string[][]
     */
    public function invalidDirnames() : array
    {
        return [
            ['/hiddendir/n'],
            ['.hiddendir/normaldir'],
            ['.hiddendir.ext/n'],
            ['.hiddenxxir/n'],
            ['.hiddenir/n'],
        ];
    }

    /**
     * @uses \Flyfinder\Path
     *
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     */
    public function testNoFalsePositiveWithLongerDirName() : void
    {
        $prefixDir    = 'absolute/path';
        $absolutePath = 'absolute/pathMOAR/to/file.txt';
        $spec         = new InPath(new Path($prefixDir));
        $this->assertFalse($spec->isSatisfiedBy([
            'type' => 'file',
            'path' => $absolutePath,
            'dirname' => dirname($absolutePath),
            'filename' => 'file',
            'extension' => 'txt',
            'basename' => 'file.txt',
        ]));
    }
}
