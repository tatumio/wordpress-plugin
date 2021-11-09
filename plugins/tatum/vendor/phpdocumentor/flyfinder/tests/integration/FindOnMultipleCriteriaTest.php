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
 * Integration test against examples/02-find-on-multiple-criteria.php
 *
 * @coversNothing
 */
class FindOnMultipleCriteriaTest extends TestCase
{
    public function testFindingFilesOnMultipleCriteria() : void
    {
        $result = [];
        include __DIR__ . '/../../examples/02-find-on-multiple-criteria.php';

        $this->assertCount(2, $result);
        $this->assertSame('found.txt', $result[0]['basename']);
        $this->assertSame('example.txt', $result[1]['basename']);
    }
}
