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
 * Test case for IsHidden
 *
 * @coversDefaultClass Flyfinder\Specification\IsHidden
 */
class IsHiddenTest extends TestCase
{
    /** @var IsHidden */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp() : void
    {
        $this->fixture = new IsHidden();
    }

    public function tearDown() : void
    {
        m::close();
    }

    /**
     * @covers ::isSatisfiedBy
     */
    public function testIfSpecificationIsSatisfied() : void
    {
        $this->assertTrue($this->fixture->isSatisfiedBy(['basename' => '.test']));
    }

    /**
     * @covers ::isSatisfiedBy
     */
    public function testIfSpecificationIsNotSatisfied() : void
    {
        $this->assertFalse($this->fixture->isSatisfiedBy(['basename' => 'test']));
    }
}
