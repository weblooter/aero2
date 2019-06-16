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

namespace Jkphl\RdfaLiteMicrodata\Infrastructure\Parser;

use Jkphl\RdfaLiteMicrodata\Application\Context\ContextInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException;
use Jkphl\RdfaLiteMicrodata\Domain\Property\Property;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Property processor methods
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 * @property boolean $html HTML mode
 * @property array $propertyCache Property cache
 */
trait PropertyProcessorTrait
{
    /**
     * Create a property by prefix and name
     *
     * @param string $prefix Property prefix
     * @param string $name Property name
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @param boolean $last Last property
     * @return ContextInterface Local context for this element
     */
    protected function processPropertyPrefixName($prefix, $name, \DOMElement $element, ContextInterface $context, $last)
    {
        $vocabulary = $this->getVocabulary($prefix, $context);
        if ($vocabulary instanceof VocabularyInterface) {
            try {
                $context = $this->addProperty($element, $context, $name, $vocabulary, $last);
            } catch (RuntimeException $e) {
                // Skip invalid property
            }
        }

        return $context;
    }

    /**
     * Return a vocabulary by prefix with fallback to the default vocabulary
     *
     * @param string $prefix Vocabulary prefix
     * @param ContextInterface $context Context
     * @return VocabularyInterface Vocabulary
     */
    abstract protected function getVocabulary($prefix, ContextInterface $context);

    /**
     * Add a single property
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @param string $name Property name
     * @param VocabularyInterface $vocabulary Property vocabulary
     * @param boolean $last Last property
     * @return ContextInterface Local context for this element
     */
    protected function addProperty(
        \DOMElement $element,
        ContextInterface $context,
        $name,
        VocabularyInterface $vocabulary,
        $last
    ) {
        $resourceId = $this->getResourceId($element);

        // Get the property value
        $propertyValue = $this->getPropertyValue($element, $context);
        $property = new Property($name, $vocabulary, $propertyValue, $resourceId);

        // Add the property to the current parent thing
        $context->getParentThing()->addProperty($property);

        return $this->addPropertyChild($propertyValue, $context, $last);
    }

    /**
     * Return the resource ID
     *
     * @param \DOMElement $element DOM element
     * @return string|null Resource ID
     */
    abstract protected function getResourceId(\DOMElement $element);

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ThingInterface|string Property value
     */
    protected function getPropertyValue(\DOMElement $element, ContextInterface $context)
    {
        $value = $this->getPropertyCache($element);
        if ($value !== null) {
            return $value;
        }

        $propertyChild = $this->getPropertyChildValue($element, $context);
        if ($propertyChild instanceof ThingInterface) {
            return $this->setPropertyCache($element, $propertyChild);
        }

        // Return a string property value
        return $this->setPropertyCache($element, $this->getPropertyStringValue($element));
    }

    /**
     * Return a cached property value (if available)
     *
     * @param \DOMElement $element Element
     * @return mixed|null Property value
     */
    protected function getPropertyCache(\DOMElement $element)
    {
        $elementHash = $element->getNodePath();
        return isset($this->propertyCache[$elementHash]) ? $this->propertyCache[$elementHash] : null;
    }

    /**
     * Return a property child value
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ThingInterface|null Property child value
     */
    abstract protected function getPropertyChildValue(\DOMElement $element, ContextInterface $context);

    /**
     * Cache a property value
     *
     * @param \DOMElement $element DOM element
     * @param mixed $value Value
     * @return mixed $value Value
     */
    protected function setPropertyCache(\DOMElement $element, $value)
    {
        return $this->propertyCache[$element->getNodePath()] = $value;
    }

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @return string Property value
     */
    protected function getPropertyStringValue(\DOMElement $element)
    {
        // If HTML mode is active
        if ($this->html) {
            $value = $this->getPropertyContentAttrValue($element);
            if ($value === null) {
                $value = $this->getPropertyHtmlValue($element);
            }
            if ($value !== null) {
                return $value;
            }
        }

        // Return the text content
        return $element->textContent;
    }

    /**
     * Return a content attribute property value
     *
     * @param \DOMElement $element DOM element
     * @return null|string Property value
     */
    protected function getPropertyContentAttrValue(\DOMElement $element)
    {
        $value = trim($element->getAttribute('content'));
        return strlen($value) ? $value : null;
    }

    /**
     * Return a property value (type and tag name dependent)
     *
     * @param \DOMElement $element DOM element
     * @return string|null Property value
     */
    protected function getPropertyHtmlValue(\DOMElement $element)
    {
        $tagName = strtoupper($element->tagName);

        // Map to an attribute (if applicable)
        if (array_key_exists($tagName, self::$tagNameAttributes)) {
            $value = strval($element->getAttribute(self::$tagNameAttributes[$tagName]));
            return (($tagName != 'TIME') || strlen($value)) ? $value : null;
        }

        return null;
    }

    /**
     * Add a property child
     *
     * @param string|ThingInterface Property value
     * @param ContextInterface $context Inherited Context
     * @param boolean $last Last property
     * @return ContextInterface Local context for this element
     */
    protected function addPropertyChild($propertyValue, ContextInterface $context, $last)
    {
        // If the property value is a thing and this is the element's last property
        if (($propertyValue instanceof ThingInterface) && $last) {
            // Set the thing as parent thing for nested iterations
            $context = $context->setParentThing($propertyValue);
        }

        return $context;
    }

    /**
     * Create a property
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @return ContextInterface Local context for this element
     */
    abstract protected function processProperty(\DOMElement $element, ContextInterface $context);
}
