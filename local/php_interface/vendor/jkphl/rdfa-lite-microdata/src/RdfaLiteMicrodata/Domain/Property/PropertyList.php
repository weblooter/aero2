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

use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\ErrorException;
use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Domain\Iri\IriInterface;

/**
 * Property list
 *
 * @package Jkphl\Micrometa
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class PropertyList implements PropertyListInterface
{
    /**
     * Use iterator methods
     */
    use PropertyListIteratorTrait;

    /**
     * Property values
     *
     * @var array[]
     */
    protected $values = [];
    /**
     * Property names
     *
     * @var IriInterface[]
     */
    protected $names = [];
    /**
     * Name cursor mapping
     *
     * @var int[]
     */
    protected $nameToCursor = [];
    /**
     * Internal cursor
     *
     * @var int
     */
    protected $cursor = 0;

    /**
     * Unset a property
     *
     * @param IriInterface|string $iri IRI
     * @throws ErrorException
     */
    public function offsetUnset($iri)
    {
        throw new ErrorException(
            sprintf(ErrorException::CANNOT_UNSET_PROPERTY_STR, $iri),
            ErrorException::CANNOT_UNSET_PROPERTY,
            E_WARNING
        );
    }

    /**
     * Return the number of properties
     *
     * @return int Number of properties
     */
    public function count()
    {
        return count($this->nameToCursor);
    }

    /**
     * Return an array form
     *
     * @return array Array form
     */
    public function toArray()
    {
        $values = $this->values;
        return array_map(
            function ($cursor) use ($values) {
                return $values[$cursor];
            },
            $this->nameToCursor
        );
    }

    /**
     * Add a property
     *
     * @param PropertyInterface $property Property
     */
    public function add(PropertyInterface $property)
    {
        $iri = $property->toIri();

        // Create the property values list if necessary
        if (!$this->offsetExists($iri)) {
            $this->offsetSet($iri, [$property]);
            return;
        }

        $propertyStore =& $this->offsetGet($iri);
        $propertyStore[] = $property;
    }

    /**
     * Return whether a property exists
     *
     * @param IriInterface|string $iri IRI
     * @return boolean Property exists
     */
    public function offsetExists($iri)
    {
        return array_key_exists(strval($iri), $this->nameToCursor);
    }

    /**
     * Set a particular property
     *
     * @param IriInterface|string $iri IRI
     * @param array $value Property values
     */
    public function offsetSet($iri, $value)
    {
        $iriStr = strval($iri);
        $cursor = array_key_exists($iriStr, $this->nameToCursor) ?
            $this->nameToCursor[$iriStr] : ($this->nameToCursor[$iriStr] = count($this->nameToCursor));
        $this->names[$cursor] = $iri;
        $this->values[$cursor] = $value;
    }

    /**
     * Get a particular property
     *
     * @param IriInterface|string $iri IRI
     * @return array Property values
     */
    public function &offsetGet($iri)
    {
        $iriStr = strval($iri);

        // If the property name is unknown
        if (!isset($this->nameToCursor[$iriStr])) {
            throw new OutOfBoundsException(
                sprintf(OutOfBoundsException::UNKNOWN_PROPERTY_NAME_STR, $iriStr),
                OutOfBoundsException::UNKNOWN_PROPERTY_NAME
            );
        }

        $cursor = $this->nameToCursor[strval($iri)];
        return $this->values[$cursor];
    }
}
