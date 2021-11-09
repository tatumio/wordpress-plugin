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

use Flyfinder\Path;
use function array_slice;
use function count;
use function explode;
use function implode;
use function in_array;
use function min;
use function preg_match;
use function str_replace;

/**
 * Files *and directories* meet the specification if they are in the given path.
 * Note this behavior is different than in Finder, in that directories *can* meet the spec,
 * whereas Finder would never return a directory as "found".
 *
 * @psalm-immutable
 */
class InPath extends CompositeSpecification
{
    /** @var Path */
    private $path;

    /**
     * Initializes the InPath specification
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfiedBy(array $value) : bool
    {
        if (in_array($this->path, ['', '.', './'], false)) {
            /*
             * since flysystem stuff is always relative to the filesystem object's root,
             * a spec of "current" dir should always be a match anything being considered
             */
            return true;
        }

        $path       = (string) $this->path;
        $validChars = '[a-zA-Z0-9\\\/\.\<\>\,\|\:\(\)\&\;\#]';

        /*
         * a FILE spec would have to match on 'path',
         *   e.g. value path 'src/Cilex/Provider/MonologServiceProvider.php' should match FILE spec of same path...
         * this should also hit a perfect DIR=DIR_SPEC match,
         *   e.g. value path 'src/Cilex/Provider' should match DIR spec of 'src/Cilex/Provider'
         */
        if (isset($value['path'])) {
            $pattern = '(^(?!\/)'
                . str_replace(['?', '*'], [$validChars . '{1}', $validChars . '*'], $path)
                . '(?:/' . $validChars . '*)?$)';
            if (preg_match($pattern, $value['path'])) {
                return true;
            }
        }

        /* a DIR spec that wasn't an exact match should be able to match on dirname,
         *   e.g. value dirname 'src' of path 'src/Cilex' should match DIR spec of 'src'
         */
        if (isset($value['dirname'])) {
            $pattern = '(^(?!\/)'
                . str_replace(['?', '*'], [$validChars . '{1}', $validChars . '*'], $path . '/')
                . $validChars . '*)';
            if (preg_match($pattern, $value['dirname'] . '/')) {
                return true;
            }
        }

        return false;
    }

    /** @inheritDoc */
    public function canBeSatisfiedBySomethingBelow(array $value) : bool
    {
        $pathSegments       = explode('/', (string) $this->path);
        $valueSegments      = explode('/', $value['path']);
        $pathPrefixSegments = array_slice($pathSegments, 0, min(count($pathSegments), count($valueSegments)));
        $spec               = new InPath(new Path(implode('/', $pathPrefixSegments)));

        return $spec->isSatisfiedBy($value);
    }

    /** @inheritDoc */
    public function willBeSatisfiedByEverythingBelow(array $value) : bool
    {
        return $this->isSatisfiedBy($value);
    }
}
