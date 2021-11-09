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

namespace Flyfinder;

use Flyfinder\Specification\Glob;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\IsHidden;
use Generator;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use function array_map;
use function array_values;
use function iterator_to_array;
use function pathinfo;
use function sort;
use function substr;
use function trim;

/**
 * Test case for Finder
 *
 * @coversDefaultClass Flyfinder\Finder
 */
class FinderTest extends TestCase
{
    /** @var Finder */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp() : void
    {
        $this->fixture = new Finder();
    }

    public function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::getMethod
     */
    public function testGetMethod() : void
    {
        $this->assertSame('find', $this->fixture->getMethod());
    }

    public function testIfNotHiddenLetsSubpathsThrough() : void
    {
        $files = [ 'foo/bar/.hidden/baz/not-hidden.txt' ];
        $this->fixture->setFilesystem($this->mockFileSystem($files));
        $notHidden = (new IsHidden())->notSpecification();
        $this->assertEquals(
            $files,
            $this->generatorToFileList($this->fixture->handle($notHidden))
        );
    }

    public function testIfDoubleNotHiddenLetsSubpathsThrough() : void
    {
        $files = [ '.foo/.bar/not-hidden/.baz/.hidden.txt' ];
        $this->fixture->setFilesystem($this->mockFileSystem($files));
        $notHidden = (new IsHidden())->notSpecification()->notSpecification();
        $this->assertEquals(
            $files,
            $this->generatorToFileList($this->fixture->handle($notHidden))
        );
    }

    public function testIfNeitherHiddenNorExtLetsSubpathsThrough() : void
    {
        $files = [ 'foo/bar/.hidden/baz.ext/neither-hidden-nor.ext.zzz' ];
        $this->fixture->setFilesystem($this->mockFileSystem($files));

        $neitherHiddenNorExt =
            (new IsHidden())->notSpecification()
                ->andSpecification((new HasExtension(['ext']))->notSpecification());
        $this->assertEquals(
            $files,
            $this->generatorToFileList($this->fixture->handle($neitherHiddenNorExt))
        );

        $neitherHiddenNorExtDeMorgan = (new IsHidden())->orSpecification(new HasExtension(['ext']))->notSpecification();
        $this->assertEquals(
            $files,
            $this->generatorToFileList($this->fixture->handle($neitherHiddenNorExtDeMorgan))
        );
    }

    public function testIfNegatedOrCullsExactMatches() : void
    {
        $files = [
            'foo/bar/baz/whatever.txt',
            'foo/gen/pics/bottle.jpg',
            'foo/lou/time.txt',
        ];
        $this->fixture->setFilesystem($this->mockFileSystem($files, ['foo/bar', 'foo/gen']));

        $negatedOr =
            (new InPath(new Path('foo/gen')))
                ->orSpecification(new InPath(new Path('foo/bar')))
                ->notSpecification();

        $this->assertEquals(
            ['foo/lou/time.txt'],
            $this->generatorToFileList($this->fixture->handle($negatedOr))
        );

        $negatedOrDeMorgan =
            (new InPath(new Path('foo/gen')))->notSpecification()
            ->andSpecification((new InPath(new Path('foo/bar')))->notSpecification());

        $this->assertEquals(
            ['foo/lou/time.txt'],
            $this->generatorToFileList($this->fixture->handle($negatedOrDeMorgan))
        );
    }

    public function testIfNegatedAndCullsExactMatches() : void
    {
        $files    = [
            'foo/bar/baz/whatever.txt',
            'foo/gen/pics/bottle.jpg',
            'foo/lou/time.txt',
        ];
        $expected = [
            'foo/gen/pics/bottle.jpg',
            'foo/lou/time.txt',
        ];
        $this->fixture->setFilesystem($this->mockFileSystem($files, ['foo/bar']));

        $negatedAnd =
            (new InPath(new Path('foo/*')))
                ->andSpecification(new InPath(new Path('*/bar')))
                ->notSpecification();

        $this->assertEquals(
            $expected,
            $this->generatorToFileList($this->fixture->handle($negatedAnd))
        );

        $negatedAndDeMorgan =
            (new InPath(new Path('foo/*')))->notSpecification()
                ->orSpecification((new InPath(new Path('*/bar')))->notSpecification());

        $this->assertEquals(
            $expected,
            $this->generatorToFileList($this->fixture->handle($negatedAndDeMorgan))
        );
    }

    /**
     * @covers ::handle
     * @covers ::setFilesystem
     * @covers ::<private>
     */
    public function testIfCorrectFilesAreBeingYielded() : void
    {
        $isHidden   = m::mock(IsHidden::class);
        $filesystem = m::mock(Filesystem::class);

        $listContents1 = [
            0 => [
                'type' => 'dir',
                'path' => '.hiddendir',
                'dirname' => '',
                'basename' => '.hiddendir',
                'filename' => '.hiddendir',
            ],
            1 => [
                'type' => 'file',
                'path' => 'test.txt',
                'basename' => 'test.txt',
            ],
        ];

        $listContents2 = [
            0 => [
                'type' => 'file',
                'path' => '.hiddendir/.test.txt',
                'dirname' => '.hiddendir',
                'basename' => '.test.txt',
                'filename' => '.test',
                'extension' => 'txt',
            ],
        ];

        $filesystem->shouldReceive('listContents')
            ->with('')
            ->andReturn($listContents1);

        $filesystem->shouldReceive('listContents')
            ->with('.hiddendir')
            ->andReturn($listContents2);

        $isHidden->shouldReceive('isSatisfiedBy')
            ->with($listContents1[0])
            ->andReturn(true);

        $isHidden->shouldReceive('canBeSatisfiedBySomethingBelow')
            ->with($listContents1[0])
            ->andReturn(true);

        $isHidden->shouldReceive('isSatisfiedBy')
            ->with($listContents1[1])
            ->andReturn(false);

        $isHidden->shouldReceive('isSatisfiedBy')
            ->with($listContents2[0])
            ->andReturn(true);

        $this->fixture->setFilesystem($filesystem);
        $generator = $this->fixture->handle($isHidden);

        $result = [];

        foreach ($generator as $value) {
            $result[] = $value;
        }

        $expected = [
            0 => [
                'type' => 'file',
                'path' => '.hiddendir/.test.txt',
                'dirname' => '.hiddendir',
                'basename' => '.test.txt',
                'filename' => '.test',
                'extension' => 'txt',
            ],
        ];

        $this->assertSame($expected, $result);
    }

    public function testSubtreePruningOptimization() : void
    {
        $filesystem = $this->mockFileSystem(
            [
                'foo/bar/baz/file.txt',
                'foo/bar/baz/file2.txt',
                'foo/bar/baz/excluded/excluded.txt',
                'foo/bar/baz/excluded/culled/culled.txt',
                'foo/bar/baz/excluded/important/reincluded.txt',
                'foo/bar/file3.txt',
                'foo/lou/someSubdir/file4.txt',
                'foo/irrelevant1/',
                'irrelevant2/irrelevant3/irrelevantFile.txt',
            ],
            [
                'foo/irrelevant1',
                'irrelevant2',
                'foo/bar/baz/excluded/culled',
            ]
        );

        $inFooBar = new InPath(new Path('foo/bar'));
        $inFooLou = new InPath(new Path('foo/lou'));
        $inExcl   = new InPath(new Path('foo/bar/baz/excl*'));
        $inReincl = new InPath(new Path('foo/bar/baz/*/important'));
        $spec     =
            $inFooBar
                ->orSpecification($inFooLou)
                ->andSpecification($inExcl->notSpecification())
                ->orSpecification($inReincl);

        $finder = $this->fixture;
        $finder->setFilesystem($filesystem);
        $generator = $finder->handle($spec);

        $expected = [
            'foo/bar/baz/file.txt',
            'foo/bar/baz/file2.txt',
            'foo/bar/file3.txt',
            'foo/bar/baz/excluded/important/reincluded.txt',
            'foo/lou/someSubdir/file4.txt',
        ];
        sort($expected);

        $this->assertEquals($expected, $this->generatorToFileList($generator));
    }

    public function testGlobSubtreePruning() : void
    {
        $filesystem  = $this->mockFileSystem(
            [
                'foo/bar/baz/file.txt',
                'foo/bar/baz/file2.txt',
                'foo/bar/baz/excluded/excluded.txt',
                'foo/bar/baz/excluded/culled/culled.txt',
                'foo/bar/baz/excluded/important/reincluded.txt',
                'foo/bar/file3.txt',
                'foo/lou/someSubdir/file4.txt',
                'foo/irrelevant1/',
                'irrelevant2/irrelevant3/irrelevantFile.txt',
            ],
            [
                'foo/irrelevant1',
                'irrelevant2',
                'foo/bar/baz/excluded/culled',
            ]
        );
        $txtInFooBar = new Glob('/foo/bar/**/*.txt');
        $inFooLou    = new Glob('/foo/lou/**/*');
        $inExcl      = new Glob('/foo/bar/baz/excl*/**/*');
        $inReincl    = new Glob('/foo/bar/baz/*/important/**/*');
        $spec        = $txtInFooBar
            ->orSpecification($inFooLou)
            ->andSpecification($inExcl->notSpecification())
            ->orSpecification($inReincl);

        $finder = $this->fixture;
        $finder->setFilesystem($filesystem);
        $generator = $finder->handle($spec);
        $expected  = [
            'foo/bar/baz/file.txt',
            'foo/bar/baz/file2.txt',
            'foo/bar/file3.txt',
            'foo/bar/baz/excluded/important/reincluded.txt',
            'foo/lou/someSubdir/file4.txt',
        ];
        sort($expected);

        $this->assertEquals($expected, $this->generatorToFileList($generator));
    }

    /**
     * @return string[]
     */
    protected function generatorToFileList(Generator $generator) : array
    {
        $actual = array_values(array_map(static function ($v) {
            return $v['path'];
        }, iterator_to_array($generator)));
        sort($actual);

        return $actual;
    }

    /**
     * @param string[] $pathList
     *
     * @return mixed[]
     */
    protected function mockFileTree(array $pathList) : array
    {
        $result = [
            '.' => [
                'type' => 'dir',
                'path' => '',
                'dirname' => '.',
                'basename' => '.',
                'filename' => '.',
                'contents' => [],
            ],
        ];
        foreach ($pathList as $path) {
            $isFile = substr($path, -1) !== '/';
            $child  = null;
            while (true) {
                $info = pathinfo($path);
                if ($isFile) {
                    $isFile        = false;
                    $result[$path] = [
                        'type' => 'file',
                        'path' => $path,
                        'dirname' => $info['dirname'],
                        'basename' => $info['basename'],
                        'filename' => $info['filename'],
                        'extension' => $info['extension'],
                    ];
                } else {
                    $existed = true;
                    if (!isset($result[$path])) {
                        $existed       = false;
                        $result[$path] = [
                            'type' => 'dir',
                            'path' => $path,
                            'basename' => $info['basename'],
                            'filename' => $info['filename'],
                            'contents' => [],
                        ];
                    }

                    if ($child!==null) {
                        $result[$path]['contents'][] = $child;
                    }

                    if ($existed) {
                        break;
                    }
                }

                $child = $info['basename'];
                $path  = $info['dirname'];
            }
        }

        return $result;
    }

    /**
     * @param mixed[] $fileTreeMock
     *
     * @return mixed[]
     */
    protected function mockListContents(array $fileTreeMock, string $path) : array
    {
        $path = trim($path, '/');
        if (substr($path . '  ', 0, 2)==='./') {
            $path = substr($path, 2);
        }

        if ($path==='') {
            $path = '.';
        }

        if (!isset($fileTreeMock[$path]) || $fileTreeMock[$path]['type'] === 'file') {
            return [];
        }

        $result = [];
        foreach ($fileTreeMock[$path]['contents'] as $basename) {
            $childPath = ($path==='.' ? '' : $path . '/') . $basename;
            if (!isset($fileTreeMock[$childPath])) {
                continue;
            }

            $result[$basename] = $fileTreeMock[$childPath];
        }

        return $result;
    }

    /**
     * @param string[] $paths
     * @param string[] $pathsThatShouldNotBeListed
     */
    protected function mockFileSystem(array $paths, array $pathsThatShouldNotBeListed = []) : FilesystemInterface
    {
        $fsData     = $this->mockFileTree($paths);
        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('listContents')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function (string $path) use ($fsData, $pathsThatShouldNotBeListed) : array {
                $this->assertNotContains($path, $pathsThatShouldNotBeListed);

                return array_values($this->mockListContents($fsData, $path));
            });

        return $filesystem;
    }
}
