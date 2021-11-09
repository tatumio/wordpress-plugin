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

use function substr;

/**
 * Files or directories meet the specification if they are hidden
 *
 * @psalm-immutable
 */
class IsHidden extends CompositeSpecification
{
    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(array $value) : bool
    {
        return isset($value['basename']) && substr($value['basename'], 0, 1) === '.';
    }
}
