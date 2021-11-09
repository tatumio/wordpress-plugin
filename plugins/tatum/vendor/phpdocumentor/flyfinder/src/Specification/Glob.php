<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Many thanks to webmozart by providing the original code in webmozart/glob
 *
 * @link https://github.com/webmozart/glob/blob/master/src/Glob.php
 * @link      http://phpdoc.org
 */

namespace Flyfinder\Specification;

use InvalidArgumentException;
use function array_slice;
use function count;
use function explode;
use function implode;
use function max;
use function min;
use function preg_match;
use function rtrim;
use function sprintf;
use function strlen;
use function strpos;
use function substr;

/**
 * Glob specification class
 *
 * @psalm-immutable
 */
final class Glob extends CompositeSpecification
{
    /** @var string */
    private $regex;

    /**
     * The "static prefix" is the part of the glob up to the first wildcard "*".
     * If the glob does not contain wildcards, the full glob is returned.
     *
     * @var string
     */
    private $staticPrefix;

    /**
     * The "bounded prefix" is the part of the glob up to the first recursive wildcard "**".
     * It is the longest prefix for which the number of directory segments in the partial match
     * is known. If the glob does not contain the recursive wildcard "**", the full glob is returned.
     *
     * @var string
     */
    private $boundedPrefix;

    /**
     * The "total prefix" is the part of the glob before the trailing catch-all wildcard sequence if the glob
     * ends with one, otherwise null. It is needed for implementing the A-quantifier pruning hint.
     *
     * @var string|null
     */
    private $totalPrefix;

    public function __construct(string $glob)
    {
        $this->regex         = self::toRegEx($glob);
        $this->staticPrefix  = self::getStaticPrefix($glob);
        $this->boundedPrefix = self::getBoundedPrefix($glob);
        $this->totalPrefix   = self::getTotalPrefix($glob);
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(array $value) : bool
    {
        //Flysystem paths are not absolute, so make it that way.
        $path = '/' . $value['path'];
        if (strpos($path, $this->staticPrefix) !== 0) {
            return false;
        }

        return preg_match($this->regex, $path) === 1;
    }

    /**
     * Returns the static prefix of a glob.
     *
     * The "static prefix" is the part of the glob up to the first wildcard "*".
     * If the glob does not contain wildcards, the full glob is returned.
     *
     * @param string $glob The canonical glob. The glob should contain forward
     *                      slashes as directory separators only. It must not
     *                      contain any "." or ".." segments.
     *
     * @return string The static prefix of the glob.
     *
     * @psalm-pure
     */
    private static function getStaticPrefix(string $glob) : string
    {
        self::assertValidGlob($glob);
        $prefix = '';
        $length = strlen($glob);
        for ($i = 0; $i < $length; ++$i) {
            $c = $glob[$i];
            switch ($c) {
                case '/':
                    $prefix .= '/';
                    if (self::isRecursiveWildcard($glob, $i)) {
                        break 2;
                    }

                    break;
                case '*':
                case '?':
                case '{':
                case '[':
                    break 2;
                case '\\':
                    [$unescaped, $consumedChars] = self::scanBackslashSequence($glob, $i);
                    $prefix                     .= $unescaped;
                    $i                          += $consumedChars;
                    break;
                default:
                    $prefix .= $c;
                    break;
            }
        }

        return $prefix;
    }

    private static function getBoundedPrefix(string $glob) : string
    {
        self::assertValidGlob($glob);
        $prefix = '';
        $length = strlen($glob);

        for ($i = 0; $i < $length; ++$i) {
            $c = $glob[$i];
            switch ($c) {
                case '/':
                    $prefix .= '/';
                    if (self::isRecursiveWildcard($glob, $i)) {
                        break 2;
                    }

                    break;
                case '\\':
                    [$unescaped, $consumedChars] = self::scanBackslashSequence($glob, $i);
                    $prefix                     .= $unescaped;
                    $i                          += $consumedChars;
                    break;
                default:
                    $prefix .= $c;
                    break;
            }
        }

        return $prefix;
    }

    private static function getTotalPrefix(string $glob) : ?string
    {
        self::assertValidGlob($glob);
        $matches = [];

        return preg_match('~(?<!\\\\)/\\*\\*(?:/\\*\\*?)+$~', $glob, $matches)
            ? substr($glob, 0, strlen($glob)-strlen($matches[0]))
            : null;
    }

    /**
     * @return mixed[]
     *
     * @psalm-return array{0: string, 1:int}
     * @psalm-pure
     */
    private static function scanBackslashSequence(string $glob, int $offset) : array
    {
        $startOffset = $offset;
        $result      = '';
        switch ($c = $glob[$offset + 1] ?? '') {
            case '*':
            case '?':
            case '{':
            case '}':
            case '[':
            case ']':
            case '-':
            case '^':
            case '$':
            case '~':
            case '\\':
                $result .= $c;
                ++$offset;
                break;
            default:
                $result .= '\\';
        }

        return [$result, $offset - $startOffset];
    }

    /**
     * Asserts that glob is well formed
     *
     * @psalm-pure
     */
    private static function assertValidGlob(string $glob) : void
    {
        if (strpos($glob, '/') !== 0 && strpos($glob, '://') === false) {
            throw new InvalidArgumentException(sprintf(
                'The glob "%s" is not absolute and not a URI.',
                $glob
            ));
        }
    }

    /**
     * Checks if the current position the glob is start of a Recursive directory wildcard
     *
     * @psalm-pure
     */
    private static function isRecursiveWildcard(string $glob, int $i) : bool
    {
        return isset($glob[$i + 3]) && $glob[$i + 1] . $glob[$i + 2] . $glob[$i + 3] === '**/';
    }

    /**
     * Converts a glob to a regular expression.
     *
     * @param string $glob The canonical glob. The glob should contain forward
     *                      slashes as directory separators only. It must not
     *                      contain any "." or ".." segments.
     *
     * @return string The regular expression for matching the glob.
     *
     * @psalm-pure
     */
    private static function toRegEx(string $glob) : string
    {
        $delimiter   = '~';
        $inSquare    = false;
        $curlyLevels = 0;
        $regex       = '';
        $length      = strlen($glob);
        for ($i = 0; $i < $length; ++$i) {
            $c = $glob[$i];
            switch ($c) {
                case '.':
                case '(':
                case ')':
                case '|':
                case '+':
                case '^':
                case '$':
                case $delimiter:
                    $regex .= '\\' . $c;
                    break;
                case '/':
                    if (self::isRecursiveWildcard($glob, $i)) {
                        $regex .= '/([^/]+/)*';
                        $i     += 3;
                    } else {
                        $regex .= '/';
                    }

                    break;
                case '*':
                    $regex .= '[^/]*';
                    break;
                case '?':
                    $regex .= '.';
                    break;
                case '{':
                    $regex .= '(';
                    ++$curlyLevels;
                    break;
                case '}':
                    if ($curlyLevels > 0) {
                        $regex .= ')';
                        --$curlyLevels;
                    } else {
                        $regex .= '}';
                    }

                    break;
                case ',':
                    $regex .= $curlyLevels > 0 ? '|' : ',';
                    break;
                case '[':
                    $regex   .= '[';
                    $inSquare = true;
                    if (isset($glob[$i + 1]) && $glob[$i + 1] === '^') {
                        $regex .= '^';
                        ++$i;
                    }

                    break;
                case ']':
                    $regex   .= $inSquare ? ']' : '\\]';
                    $inSquare = false;
                    break;
                case '-':
                    $regex .= $inSquare ? '-' : '\\-';
                    break;
                case '\\':
                    if (isset($glob[$i + 1])) {
                        switch ($glob[$i + 1]) {
                            case '*':
                            case '?':
                            case '{':
                            case '}':
                            case '[':
                            case ']':
                            case '-':
                            case '^':
                            case '$':
                            case '~':
                            case '\\':
                                $regex .= '\\' . $glob[$i + 1];
                                ++$i;
                                break;
                            default:
                                $regex .= '\\\\';
                        }
                    }

                    break;
                default:
                    $regex .= $c;
                    break;
            }
        }

        if ($inSquare) {
            throw new InvalidArgumentException(sprintf(
                'Invalid glob: missing ] in %s',
                $glob
            ));
        }

        if ($curlyLevels > 0) {
            throw new InvalidArgumentException(sprintf(
                'Invalid glob: missing } in %s',
                $glob
            ));
        }

        return $delimiter . '^' . $regex . '$' . $delimiter;
    }

    /** @inheritDoc */
    public function canBeSatisfiedBySomethingBelow(array $value) : bool
    {
        $valueSegments             = explode('/', '/' . $value['path']);
        $boundedPrefixSegments     = explode('/', rtrim($this->boundedPrefix, '/'));
        $howManySegmentsToConsider = min(count($valueSegments), count($boundedPrefixSegments));
        $boundedPrefixGlob         = implode('/', array_slice($boundedPrefixSegments, 0, $howManySegmentsToConsider));
        $valuePathPrefix           = implode('/', array_slice($valueSegments, 1, max($howManySegmentsToConsider-1, 0)));
        $prefixValue               = $value;
        $prefixValue['path']       = $valuePathPrefix;

        if ($boundedPrefixGlob === '') {
            $boundedPrefixGlob = '/';
        }

        $spec = new Glob($boundedPrefixGlob);

        return $spec->isSatisfiedBy($prefixValue);
    }

    /** @inheritDoc */
    public function willBeSatisfiedByEverythingBelow(array $value) : bool
    {
        if ($this->totalPrefix === null) {
            return false;
        }

        $spec                    = new Glob(rtrim($this->totalPrefix, '/') . '/**/*');
        $terminatedValue         = $value;
        $terminatedValue['path'] = rtrim($terminatedValue['path'], '/') . '/x/x';

        return $spec->isSatisfiedBy($terminatedValue);
    }
}
