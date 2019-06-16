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

namespace Jkphl\RdfaLiteMicrodata\Domain\Property;

use Jkphl\RdfaLiteMicrodata\Domain\Iri\Iri;
use Jkphl\RdfaLiteMicrodata\Domain\Iri\IriFactory;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Property
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class Property implements PropertyInterface
{
    /**
     * Property name
     *
     * @var string
     */
    protected $name;
    /**
     * Vocabulary
     *
     * @var VocabularyInterface
     */
    protected $vocabulary;
    /**
     * Property value
     *
     * @var string|ThingInterface
     */
    protected $value;
    /**
     * Resource ID
     *
     * @var string|null
     */
    protected $resourceId = null;

    /**
     * Property constructor
     *
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Property vocabulary
     * @param string|ThingInterface $value Property value
     */
    public function __construct($name, VocabularyInterface $vocabulary, $value, $resourceId = null)
    {
        $this->name = (new PropertyService())->validatePropertyName($name);
        $this->vocabulary = $vocabulary;
        $this->value = $value;
        $this->resourceId = $resourceId;
    }

    /**
     * Return the property name
     *
     * @return string Property name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the property vocabulary
     *
     * @return VocabularyInterface Property vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * Return the property value
     *
     * @return string|ThingInterface Property value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the property resource ID
     *
     * @return null|string Property resource ID
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Return as IRI
     *
     * @return Iri Property IRI
     */
    public function toIri()
    {
        return IriFactory::createFromProperty($this);
    }
}
