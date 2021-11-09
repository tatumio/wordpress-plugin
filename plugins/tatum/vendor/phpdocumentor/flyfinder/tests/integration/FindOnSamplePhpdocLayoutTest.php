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
 * Integration test against examples/03-sample-phpdoc-layout.php
 *
 * @coversNothing
 */
class FindOnSamplePhpdocLayoutTest extends TestCase
{
    public function testFindingOnSamplePhpdocLayout() : void
    {
        $result = [];
        include __DIR__ . '/../../examples/03-sample-phpdoc-layout.php';

        $this->assertCount(4, $result);
        $this->assertSame('JmsSerializerServiceProvider.php', $result[0]['basename']);
        $this->assertSame('MonologServiceProvider.php', $result[1]['basename']);
        $this->assertSame('Application.php', $result[2]['basename']);
        $this->assertSame('Bootstrap.php', $result[3]['basename']);
    }
}
