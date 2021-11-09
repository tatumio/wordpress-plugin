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

use function in_array;

/**
 * Files and directories meet the specification if they have the given extension
 *
 * @psalm-immutable
 */
class HasExtension extends CompositeSpecification
{
    /** @var string[] */
    private $extensions;

    /**
     * Receives the file extensions you want to find
     *
     * @param string[] $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(array $value) : bool
    {
        return isset($value['extension']) && in_array($value['extension'], $this->extensions, false);
    }
}
