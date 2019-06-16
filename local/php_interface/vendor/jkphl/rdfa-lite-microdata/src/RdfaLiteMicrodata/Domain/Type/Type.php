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

namespace Jkphl\RdfaLiteMicrodata\Domain\Type;

use Jkphl\RdfaLiteMicrodata\Domain\Exceptions\RuntimeException;
use Jkphl\RdfaLiteMicrodata\Domain\Iri\Iri;
use Jkphl\RdfaLiteMicrodata\Domain\Iri\IriFactory;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;

/**
 * Type
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class Type implements TypeInterface
{
    /**
     * Type
     *
     * @var string
     */
    protected $type;
    /**
     * Vocabulary
     *
     * @var VocabularyInterface
     */
    protected $vocabulary;

    /**
     * Type constructor
     *
     * @param string $type Type
     * @param VocabularyInterface $vocabulary Vocabulary
     * @throws RuntimeException If the type is invalid
     */
    public function __construct($type, VocabularyInterface $vocabulary)
    {
        $type = trim($type);

        // If the type is invalid
        if (!strlen($type)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_TYPE_NAME_STR, $type, $vocabulary->getUri()),
                RuntimeException::INVALID_TYPE_NAME
            );
        }

        $this->type = $type;
        $this->vocabulary = $vocabulary;
    }

    /**
     * Return the type
     *
     * @return string Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the vocabulary
     *
     * @return VocabularyInterface Vocabulary
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * Return as IRI
     *
     * @return Iri Type IRI
     */
    public function toIri()
    {
        return IriFactory::createFromType($this);
    }
}
