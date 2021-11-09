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

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Test case for HasExtension
 *
 * @coversDefaultClass Flyfinder\Specification\HasExtension
 */
class HasExtensionTest extends TestCase
{
    /** @var HasExtension */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp() : void
    {
        $this->fixture = new HasExtension(['txt']);
    }

    public function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     */
    public function testIfSpecificationIsSatisfied() : void
    {
        $this->assertTrue($this->fixture->isSatisfiedBy(['extension' => 'txt']));
    }

    /**
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     */
    public function testIfSpecificationIsNotSatisfied() : void
    {
        $this->assertFalse($this->fixture->isSatisfiedBy(['extension' => 'php']));
    }
}
