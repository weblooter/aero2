<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Application
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

namespace Jkphl\RdfaLiteMicrodata\Application\Parser;

use Jkphl\RdfaLiteMicrodata\Domain\Thing\Thing;
use Jkphl\RdfaLiteMicrodata\Domain\Thing\ThingInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Type\Type;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Root thing
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Application
 */
class RootThing extends Thing
{
    /**
     * Child things
     *
     * @var ThingInterface[]
     */
    protected $children = [];

    /**
     * Root thing constructor
     *
     * @param VocabularyInterface $vocabulary Vocabulary
     */
    public function __construct(VocabularyInterface $vocabulary = null)
    {
        parent::__construct(new Type('Root', $vocabulary ?: new NullVocabulary()), null);
    }

    /**
     * Add a child
     *
     * @param ThingInterface $child Child
     * @return Thing Self reference
     */
    public function addChild(ThingInterface $child)
    {
        $this->children[spl_object_hash($child)] = $child;
        return $this;
    }

    /**
     * Return all children
     *
     * @return ThingInterface[] Children
     */
    public function getChildren()
    {
        return array_values($this->children);
    }
}
