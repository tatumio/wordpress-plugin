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

use PHPUnit\Framework\TestCase;

/**
 * Test case for Path
 *
 * @coversDefaultClass Flyfinder\Path
 */
class PathTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToString() : void
    {
        $path = new Path('/my/Path');

        $this->assertSame('/my/Path', (string) $path);
    }
}
