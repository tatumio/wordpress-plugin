<?php

declare(strict_types=1);

namespace Flyfinder\Specification;

/**
 * Interface PrunableInterface
 *
 * @psalm-immutable
 */
interface PrunableInterface
{
    /**
     * Checks if anything under the directory path in value can possibly satisfy the specification.
     *
     * @param mixed[] $value
     *
     * @psalm-param array{basename: string, path: string, stream: resource, dirname: string, type: string, extension: string} $value
     */
    public function canBeSatisfiedBySomethingBelow(array $value) : bool;

    /**
     * Returns true if it is known or can be deduced that everything under the directory path in value
     * will certainly satisfy the specification.
     *
     * @param mixed[] $value
     *
     * @psalm-param array{basename: string, path: string, stream: resource, dirname: string, type: string, extension: string} $value
     */
    public function willBeSatisfiedByEverythingBelow(array $value) : bool;
}
