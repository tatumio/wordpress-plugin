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
 * Test case for NotSpecification
 *
 * @coversDefaultClass Flyfinder\Specification\NotSpecification
 */
class NotSpecificationTest extends TestCase
{
    /** @var m\MockInterface|HasExtension */
    private $hasExtension;

    /** @var NotSpecification */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp() : void
    {
        $this->hasExtension = m::mock(HasExtension::class);
        $this->fixture      = new NotSpecification($this->hasExtension);
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
        $this->hasExtension->shouldReceive('isSatisfiedBy')->once()->andReturn(false);

        $this->assertTrue($this->fixture->isSatisfiedBy(['test']));
    }

    /**
     * @covers ::__construct
     * @covers ::isSatisfiedBy
     */
    public function testIfSpecificationIsNotSatisfied() : void
    {
        $this->hasExtension->shouldReceive('isSatisfiedBy')->once()->andReturn(true);

        $this->assertFalse($this->fixture->isSatisfiedBy(['test']));
    }
}
