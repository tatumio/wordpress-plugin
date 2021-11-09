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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test case for CompositeSpecification
 *
 * @coversDefaultClass Flyfinder\Specification\CompositeSpecification
 */
class CompositeSpecificationTest extends TestCase
{
    /** @var m\MockInterface|HasExtension */
    private $hasExtension;

    /** @var CompositeSpecification|MockObject */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp() : void
    {
        $this->hasExtension = m::mock(HasExtension::class);
        $this->fixture      = $this->getMockForAbstractClass(CompositeSpecification::class);
    }

    public function tearDown() : void
    {
        m::close();
    }

    /**
     * @uses \Flyfinder\Specification\AndSpecification
     *
     * @covers ::andSpecification
     */
    public function testAndSpecification() : void
    {
        $this->assertInstanceOf(
            AndSpecification::class,
            $this->fixture->andSpecification($this->hasExtension)
        );
    }

    /**
     * @uses \Flyfinder\Specification\OrSpecification
     *
     * @covers ::orSpecification
     */
    public function testOrSpecification() : void
    {
        $this->assertInstanceOf(
            OrSpecification::class,
            $this->fixture->orSpecification($this->hasExtension)
        );
    }

    /**
     * @uses \Flyfinder\Specification\NotSpecification
     *
     * @covers ::notSpecification
     */
    public function testNotSpecification() : void
    {
        $this->assertInstanceOf(
            NotSpecification::class,
            $this->fixture->notSpecification()
        );
    }
}
