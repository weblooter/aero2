<?php

/**
 * rdfa-lite
 *
 * @category Jkphl
 * @package Jkphl\Micrometa
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain\Iri
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

namespace Jkphl\RdfaLiteMicrodata\Domain\Iri;

use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * IRI
 *
 * @package Jkphl\Micrometa
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class Iri implements IriInterface
{
    /**
     * Name
     *
     * @var string
     */
    protected $name;
    /**
     * Profile
     *
     * @var string
     */
    protected $profile;

    /**
     * IRI constructor
     *
     * @param string $name Name
     * @param VocabularyInterface $vocabulary Vocabulary
     */
    public function __construct($name, VocabularyInterface $vocabulary)
    {
        $this->name = $name;
        $this->profile = $vocabulary->getUri();
    }

    /**
     * Return the name
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the profile
     *
     * @return string Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Return a string serialization
     *
     * @return string String serialization
     */
    public function __toString()
    {
        return $this->profile.$this->name;
    }
}
