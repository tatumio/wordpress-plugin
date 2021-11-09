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
 * Interface for FlyFinder specifications
 *
 * @psalm-immutable
 */
interface SpecificationInterface
{
    /**
     * Checks if the value meets the specification
     *
     * @param mixed[] $value
     *
     * @psalm-param array{basename: string, path: string, stream: resource, dirname: string, type: string, extension: string} $value
     */
    public function isSatisfiedBy(array $value) : bool;
}
