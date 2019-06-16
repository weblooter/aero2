<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
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

namespace Jkphl\RdfaLiteMicrodata\Infrastructure\Service;

use Jkphl\RdfaLiteMicrodata\Domain\Property\PropertyInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Type\TypeInterface;

/**
 * Thing gateway
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
class ThingGateway
{
    /**
     * Treat types & property names as IRIs
     *
     * @var boolean
     */
    protected $iri;

    /**
     * Abstract parser constructor
     *
     * @param boolean $iri Treat types & property names as IRIs
     */
    public function __construct($iri = false)
    {
        $this->iri = $iri;
    }

    /**
     * Export things
     *
     * @param ThingInterface[] $things Things
     * @return array Exported things
     */
    public function export(array $things)
    {
        return array_map([$this, 'exportThing'], $things);
    }

    /**
     * Export a property
     *
     * @param PropertyInterface $property Property
     * @return ThingInterface|string Exported property
     */
    protected function exportProperty(PropertyInterface $property)
    {
        $value = $property->getValue();
        return ($value instanceof ThingInterface) ? $this->exportThing($value) : $value;
    }

    /**
     * Export a single thing
     *
     * @param ThingInterface $thing Thing
     * @return \stdClass Exported thing
     */
    protected function exportThing(ThingInterface $thing)
    {
        $iri = $this->iri;

        return (object)[
            'type' => array_map(
                function (TypeInterface $type) use ($iri) {
                    return $iri ?
                        (object)[
                            'profile' => $type->getVocabulary()->getUri(),
                            'name' => $type->getType(),
                        ] :
                        $type->getVocabulary()->expand($type->getType());
                },
                $thing->getTypes()
            ),
            'id' => $thing->getResourceId(),
            'properties' => $this->exportProperties($thing),
        ];
    }

    /**
     * Export the properties list
     *
     * @param ThingInterface $thing Thing
     * @return array Properties list
     */
    protected function exportProperties(ThingInterface $thing)
    {
        $properties = [];
        foreach ($thing->getProperties() as $propertyIri => $propertyValues) {
            if (count($propertyValues)) {
                $propertyValues = array_map([$this, 'exportProperty'], $propertyValues);
                $properties[strval($propertyIri)] = $this->iri ?
                    (object)[
                        'profile' => $propertyIri->getProfile(),
                        'name' => $propertyIri->getName(),
                        'values' => $propertyValues,
                    ] : $propertyValues;
            }
        }

        return $properties;
    }
}
