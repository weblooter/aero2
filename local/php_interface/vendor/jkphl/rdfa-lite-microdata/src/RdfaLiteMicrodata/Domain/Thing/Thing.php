<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 * @author Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright Copyright © 2017 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Jkphl\RdfaLiteMicrodata\Domain\Thing;

use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyList;
use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyService;
use Jkphl\RdfaLiteMicrodata\Domain\Type\TypeInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Thing
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class Thing implements ThingInterface
{
    /**
     * Resource types
     *
     * @var TypeInterface[]
     */
    protected $types;
    /**
     * Resource ID
     *
     * @var string|null
     */
    protected $resourceId = null;
    /**
     * Property
     *
     * @var PropertyList
     */
    protected $properties;

    /**
     * Thing constructor
     *
     * @param TypeInterface|TypeInterface[] $types Type(s)
     * @param null|string $resourceId Resource id
     */
    public function __construct($types, $resourceId = null)
    {
        if (!is_array($types)) {
            $types = [$types];
        }

        // Run through all given types
        foreach ($types as $type) {
            if (!($type instanceof TypeInterface)) {
                throw new RuntimeException(
                    sprintf(RuntimeException::INVALID_TYPE_STR, gettype($type)),
                    RuntimeException::INVALID_TYPE
                );
            }
        }

        $this->types = $types;
        $this->resourceId = $resourceId;
        $this->properties = new PropertyList();
    }

    /**
     * Return the resource types
     *
     * @return TypeInterface[] Resource types
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Return the resource ID
     *
     * @return null|string Resource ID
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Add a property value
     *
     * @param PropertyInterface $property Property
     * @return Thing Self reference
     */
    public function addProperty(PropertyInterface $property)
    {
        $this->properties->add($property);
        return $this;
    }

    /**
     * Return all properties
     *
     * @return PropertyList Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Return the values of a single property
     *
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Vocabulary
     * @return array Property values
     */
    public function getProperty($name, VocabularyInterface $vocabulary)
    {
        $name = (new PropertyService())->validatePropertyName($name);
        return $this->properties[$vocabulary->expand($name)];
    }
}
