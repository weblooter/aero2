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
use Jkphl\RdfaLiteMicrodata\Application\Parser\DOMIterator;
use Jkphl\RdfaLiteMicrodata\Application\Parser\RootThing;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Type\TypeInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Microdata element processor
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
class MicrodataElementProcessor extends AbstractElementProcessor
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setHtml(true);
    }

    /**
     * Process a DOM element
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @return ContextInterface Local context for this element
     */
    public function processElement(\DOMElement $element, ContextInterface $context)
    {
        // Create a property
        $propertyContext = $this->processProperty($element, $context);

        // Process the element in case it's an anonymous thing with no children
        if (!$element->childNodes->length) {
            $this->processChild($element, $context);
        }

        return $propertyContext;
    }

    /**
     * Create a property
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @return ContextInterface Local context for this element
     */
    protected function processProperty(\DOMElement $element, ContextInterface $context)
    {
        if ($element->hasAttribute('itemprop') && !($context->getParentThing() instanceof RootThing)) {
            $itemprops = trim($element->getAttribute('itemprop'));
            $itemprops = strlen($itemprops) ? preg_split('/\s+/', $itemprops) : [];
            $context = $this->processProperties($itemprops, $element, $context);
        }

        return $context;
    }

    /**
     * Process properties
     *
     * @param array $itemprops Properties
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Inherited Context
     * @return ContextInterface Local context for this element
     */
    protected function processProperties(array $itemprops, \DOMElement $element, ContextInterface $context)
    {
        foreach ($itemprops as $index => $itemprop) {
            $context = $this->processPropertyPrefixName(
                null,
                $itemprop,
                $element,
                $context,
                $index == (count($itemprops) - 1)
            );
        }

        return $context;
    }

    /**
     * Create a nested child
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ContextInterface Context for children
     */
    protected function processChild(\DOMElement $element, ContextInterface $context)
    {
        if ($element->hasAttribute('itemscope') && empty($element->getAttribute('itemprop'))) {
            $context = $this->createAndAddChild($element, $context);
        }

        return $context;
    }

    /**
     * Create and add a nested child
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ContextInterface Context for children
     */
    protected function createAndAddChild(\DOMElement $element, ContextInterface $context)
    {
        $thing = $this->getThing(
            trim($element->getAttribute('itemtype')) ?: null,
            trim($element->getAttribute('itemid')) ?: null,
            $context
        );

        // Process item references
        $this->processItemReferences($element, $context, $thing);

        // Add the new thing as a child to the current context
        // and set the thing as parent thing for nested iterations
        return $context->addChild($thing)->setParentThing($thing);
    }

    /**
     * Process item references
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @param ThingInterface $thing Thing
     * @return ThingInterface Thing
     */
    protected function processItemReferences(\DOMElement $element, ContextInterface $context, ThingInterface $thing)
    {
        // If the element has item references
        if ($element->hasAttribute('itemref')) {
            $itemrefElements = $this->getItemReferenceElements($element);
            if (count($itemrefElements)) {
                $this->processItemReferenceElements($itemrefElements, $context, $thing);
            }
        }

        return $thing;
    }

    /**
     * Find all reference elements
     *
     * @param \DOMElement $element DOM element
     * @return \DOMElement[] Reference elements
     */
    protected function getItemReferenceElements(\DOMElement $element)
    {
        $itemrefElements = [];
        $itemrefs = trim($element->getAttribute('itemref'));
        $itemrefs = strlen($itemrefs) ? preg_split('/\s+/', $itemrefs) : [];
        foreach ($itemrefs as $itemref) {
            $itemrefElement = $element->ownerDocument->getElementById($itemref);
            if ($itemrefElement instanceof \DOMElement) {
                $itemrefElements[] = $itemrefElement;
            }
        }
        return $itemrefElements;
    }

    /**
     * Process item reference elements
     *
     * @param \DOMElement[] $itemrefElements Item reference DOM elements
     * @param ContextInterface $context Context
     * @param ThingInterface $thing Thing
     * @return void
     */
    protected function processItemReferenceElements(
        array $itemrefElements,
        ContextInterface $context,
        ThingInterface $thing
    ) {
        $iterator = new DOMIterator(
            $itemrefElements,
            $context->setParentThing($thing),
            new MicrodataElementProcessor()
        );

        // Iterate through all $node
        foreach ($iterator->getRecursiveIterator() as $node) {
            $node || true;
        }
    }

    /**
     * Return the resource ID
     *
     * @param \DOMElement $element DOM element
     * @return string|null Resource ID
     */
    protected function getResourceId(\DOMElement $element)
    {
        return trim($element->getAttribute('itemid')) ?: null;
    }

    /**
     * Return a property child value
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ThingInterface|null Property child value
     */
    protected function getPropertyChildValue(\DOMElement $element, ContextInterface $context)
    {
        // If the property creates a new type: Return the element itself
        if ($element->hasAttribute('itemscope')) {
            $thing = $this->getThing($element->getAttribute('itemtype'), null, $context);
            return $this->processItemReferences($element, $context, $thing);
        }

        return null;
    }

    /**
     * Return a vocabulary by prefix with fallback to the default vocabulary
     *
     * @param string $prefix Vocabulary prefix
     * @param ContextInterface $context Context
     * @return VocabularyInterface Vocabulary
     */
    protected function getVocabulary($prefix, ContextInterface $context)
    {
        /** @var VocabularyInterface $vocabulary */
        $vocabulary = $prefix ?: $context->getDefaultVocabulary();
        return $vocabulary;
    }

    /**
     * Split a value into a vocabulary prefix and a name
     *
     * @param string $prefixName Prefixed name
     * @return array Prefix and name
     */
    protected function getPrefixName($prefixName)
    {
        return [null, $prefixName];
    }

    /**
     * Instanciate a type
     *
     * @param string $prefixedType Prefixed type
     * @param ContextInterface $context Context
     * @return TypeInterface Type
     */
    protected function getType($prefixedType, ContextInterface $context)
    {
        $defaultVocabulary = $context->getDefaultVocabulary();
        if ($defaultVocabulary && strpos($prefixedType, $defaultVocabulary->getUri()) === 0) {
            $prefixedType = substr($prefixedType, strlen($defaultVocabulary->getUri()));
        }
        return parent::getType($prefixedType, $context);
    }
}
