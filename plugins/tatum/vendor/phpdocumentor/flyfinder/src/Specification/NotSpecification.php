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

/**
 * @psalm-immutable
 */
final class NotSpecification extends CompositeSpecification
{
    /** @var SpecificationInterface */
    private $wrapped;

    /**
     * Initializes the NotSpecification object
     */
    public function __construct(SpecificationInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(array $value) : bool
    {
        return !$this->wrapped->isSatisfiedBy($value);
    }

    /** @inheritDoc */
    public function canBeSatisfiedBySomethingBelow(array $value) : bool
    {
        return !self::thatWillBeSatisfiedByEverythingBelow($this->wrapped, $value);
    }

    /** @inheritDoc */
    public function willBeSatisfiedByEverythingBelow(array $value) : bool
    {
        return !self::thatCanBeSatisfiedBySomethingBelow($this->wrapped, $value);
    }
}
