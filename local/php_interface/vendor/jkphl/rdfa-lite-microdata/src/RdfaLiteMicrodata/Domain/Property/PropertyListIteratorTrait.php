<?php

/**
 * rdfa-lite
 *
 * @category Jkphl
 * @package Jkphl\Micrometa
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain\Property
 * @author Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2017 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Jkphl\RdfaLiteMicrodata\Domain\Property;

use Jkphl\RdfaLiteMicrodata\Domain\Iri\IriInterface;

/**
 * Property list iterator trait
 *
 * @package Jkphl\Micrometa
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 * @property array[] $values Property values
 * @property IriInterface[] $names Property names
 * @property int $cursor Cursor
 */
trait PropertyListIteratorTrait
{
    /**
     * Return the current property values
     *
     * @return array Property values
     */
    public function current()
    {
        return $this->values[$this->cursor];
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        ++$this->cursor;
    }

    /**
     * Return the current IRI key
     *
     * @return IriInterface IRI key
     */
    public function key()
    {
        return $this->names[$this->cursor];
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The current position is valid
     */
    public function valid()
    {
        return isset($this->values[$this->cursor]);
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->cursor = 0;
    }
}
