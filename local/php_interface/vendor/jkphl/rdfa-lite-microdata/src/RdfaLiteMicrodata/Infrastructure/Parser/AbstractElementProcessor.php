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
use Jkphl\RdfaLiteMicrodata\Application\Contract\ElementProcessorInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\Thing;
use Jkphl\RdfaLiteMicrodata\Domain\Type\Type;
use Jkphl\RdfaLiteMicrodata\Domain\Type\TypeInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Exceptions\RuntimeException;

/**
 * Abstract element processor
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
abstract class AbstractElementProcessor implements ElementProcessorInterface
{
    /**
     * Use the property processor methods
     */
    use PropertyProcessorTrait;

    /**
     * Tag name / attribute map
     *
     * @var array
     */
    protected static $tagNameAttributes = [
        'META' => 'content',
        'AUDIO' => 'src',
        'EMBED' => 'src',
        'IFRAME' => 'src',
        'IMG' => 'src',
        'SOURCE' => 'src',
        'TRACK' => 'src',
        'VIDEO' => 'src',
        'A' => 'href',
        'AREA' => 'href',
        'LINK' => 'href',
        'OBJECT' => 'data',
        'DATA' => 'value',
        'METER' => 'value',
        'TIME' => 'datetime'
    ];
    /**
     * Property cache
     *
     * @var array
     */
    protected $propertyCache = [];
    /**
     * HTML mode
     *
     * @var boolean
     */
    protected $html;

    /**
     * Enable / disable HTML element value resolution
     *
     * @param bool $html Enable HTML element value resolution
     * @return ElementProcessorInterface Self reference
     */
    public function setHtml($html)
    {
        $this->html = boolval($html);
        return $this;
    }

    /**
     * Process a DOM element's child
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ContextInterface Context for children
     */
    public function processElementChildren(\DOMElement $element, ContextInterface $context)
    {
        // Process a child
        return $this->processChild($element, $context);
    }

    /**
     * Create a nested child
     *
     * @param \DOMElement $element DOM element
     * @param ContextInterface $context Context
     * @return ContextInterface Context for children
     */
    abstract protected function processChild(\DOMElement $element, ContextInterface $context);

    /**
     * Return a thing by typeof value
     *
     * @param string|null $typeof Thing type
     * @param string|null $resourceId Resource ID
     * @param ContextInterface $context Context
     * @return Thing Thing
     * @throws RuntimeException If the default vocabulary is empty
     */
    protected function getThing($typeof, $resourceId, ContextInterface $context)
    {
        /** @var TypeInterface[] $types */
        $types = [];
        if (strlen($typeof)) {
            foreach (preg_split('/\s+/', $typeof) as $prefixedType) {
                $types[] = $this->getType($prefixedType, $context);
            }
        }

        return new Thing($types, $resourceId);
    }

    /**
     * Instantiate a type
     *
     * @param string $prefixedType Prefixed type
     * @param ContextInterface $context Context
     * @return TypeInterface Type
     */
    protected function getType($prefixedType, ContextInterface $context)
    {
        list($prefix, $typeName) = $this->getPrefixName($prefixedType);
        $vocabulary = $this->getVocabulary($prefix, $context);
        if ($vocabulary instanceof VocabularyInterface) {
            return new Type($typeName, $vocabulary);
        }

        // If the default vocabulary is empty
        throw new RuntimeException(
            RuntimeException::EMPTY_DEFAULT_VOCABULARY_STR,
            RuntimeException::EMPTY_DEFAULT_VOCABULARY
        );
    }

    /**
     * Split a value into a vocabulary prefix and a name
     *
     * @param string $prefixName Prefixed name
     * @return array Prefix and name
     */
    abstract protected function getPrefixName($prefixName);
}
