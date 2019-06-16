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

namespace Jkphl\RdfaLiteMicrodata\Application\Context;

use Jkphl\RdfaLiteMicrodata\Application\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Application\Exceptions\RuntimeException;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\Vocabulary;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyInterface;
use Jkphl\RdfaLiteMicrodata\Domain\Vocabulary\VocabularyService;

/**
 * RDFa Lite parsing context
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Application
 */
class RdfaLiteContext extends AbstractContext
{
    /**
     * Default vocabularies and their prefixes
     *
     * @var array
     * @see https://www.w3.org/2011/rdfa-context/rdfa-1.1
     * @link https://www.w3.org/2013/json-ld-context/rdfa11
     */
    protected static $defaultVocabularies = [
        'cat' => 'http://www.w3.org/ns/dcat#',
        'qb' => 'http://purl.org/linked-data/cube#',
        'grddl' => 'http://www.w3.org/2003/g/data-view#',
        'ma' => 'http://www.w3.org/ns/ma-ont#',
        'owl' => 'http://www.w3.org/2002/07/owl#',
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rdfa' => 'http://www.w3.org/ns/rdfa#',
        'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
        'rif' => 'http://www.w3.org/2007/rif#',
        'rr' => 'http://www.w3.org/ns/r2rml#',
        'skos' => 'http://www.w3.org/2004/02/skos/core#',
        'skosxl' => 'http://www.w3.org/2008/05/skos-xl#',
        'wdr' => 'http://www.w3.org/2007/05/powder#',
        'void' => 'http://rdfs.org/ns/void#',
        'wdrs' => 'http://www.w3.org/2007/05/powder-s#',
        'xhv' => 'http://www.w3.org/1999/xhtml/vocab#',
        'xml' => 'http://www.w3.org/XML/1998/namespace',
        'xsd' => 'http://www.w3.org/2001/XMLSchema#',
        'prov' => 'http://www.w3.org/ns/prov#',
        'sd' => 'http://www.w3.org/ns/sparql-service-description#',
        'org' => 'http://www.w3.org/ns/org#',
        'gldp' => 'http://www.w3.org/ns/people#',
        'cnt' => 'http://www.w3.org/2008/content#',
        'dcat' => 'http://www.w3.org/ns/dcat#',
        'earl' => 'http://www.w3.org/ns/earl#',
        'ht' => 'http://www.w3.org/2006/http#',
        'ptr' => 'http://www.w3.org/2009/pointers#',
        'cc' => 'http://creativecommons.org/ns#',
        'ctag' => 'http://commontag.org/ns#',
        'dc' => 'http://purl.org/dc/terms/',
        'dc11' => 'http://purl.org/dc/elements/1.1/',
        'dcterms' => 'http://purl.org/dc/terms/',
        'foaf' => 'http://xmlns.com/foaf/0.1/',
        'gr' => 'http://purl.org/goodrelations/v1#',
        'ical' => 'http://www.w3.org/2002/12/cal/icaltzd#',
        'og' => 'http://ogp.me/ns#',
        'rev' => 'http://purl.org/stuff/rev#',
        'sioc' => 'http://rdfs.org/sioc/ns#',
        'v' => 'http://rdf.data-vocabulary.org/#',
        'vcard' => 'http://www.w3.org/2006/vcard/ns#',
        'schema' => 'http://schema.org/',
        'describedby' => 'http://www.w3.org/2007/05/powder-s#describedby',
        'license' => 'http://www.w3.org/1999/xhtml/vocab#license',
        'role' => 'http://www.w3.org/1999/xhtml/vocab#role'
    ];

    /**
     * Registered vocabularies
     *
     * @var array
     */
    protected $vocabularies;

    /**
     * Context constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->vocabularies = self::$defaultVocabularies;
    }

    /**
     * Register a vocabulary and its prefix
     *
     * @param string $prefix Vocabulary prefix
     * @param string $uri Vocabulary URI
     * @return RdfaLiteContext New context
     *
     */
    public function registerVocabulary($prefix, $uri)
    {
        $prefix = self::validateVocabPrefix($prefix);
        $uri = (new VocabularyService())->validateVocabularyUri($uri);

        // Register the new URI
        if (empty($this->vocabularies[$prefix]) || ($this->vocabularies[$prefix] !== $uri)) {
            $context = clone $this;
            $context->vocabularies[$prefix] = $uri;
            return $context;
        }

        return $this;
    }

    /**
     * Validata a vocabulary prefix
     *
     * @param string $prefix Vocabulary prefix
     * @return string Valid vocabulary prefix
     * @throws RuntimeException If the vocabulary prefix is invalid
     */
    protected static function validateVocabPrefix($prefix)
    {
        $prefix = trim($prefix);

        // If the vocabulary prefix is invalid
        if (!strlen($prefix)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_VOCABULARY_PREFIX_STR, $prefix),
                RuntimeException::INVALID_VOCABULARY_PREFIX
            );
        }

        return $prefix;
    }

    /**
     * Return a particular vocabulary
     *
     * @param string $prefix Vocabulary Prefix
     * @return VocabularyInterface Vocabulary
     * @throws OutOfBoundsException If the prefix has not been registered
     */
    public function getVocabulary($prefix)
    {
        $prefix = self::validateVocabPrefix($prefix);

        // If the prefix has not been registered
        if (empty($this->vocabularies[$prefix])) {
            throw new OutOfBoundsException(
                sprintf(OutOfBoundsException::UNKNOWN_VOCABULARY_PREFIX_STR, $prefix),
                OutOfBoundsException::UNKNOWN_VOCABULARY_PREFIX
            );
        }

        return new Vocabulary($this->vocabularies[$prefix]);
    }

    /**
     * Return whether a particular vocabulary prefix has been registered
     *
     * @param string $prefix Vocabulary prefix
     * @return bool Whether the prefix has been registered
     */
    public function hasVocabulary($prefix)
    {
        return !empty($this->vocabularies[self::validateVocabPrefix($prefix)]);
    }

    /**
     * Set the default vocabulary by URI
     *
     * @param VocabularyInterface $vocabulary Current default vocabulary
     * @return RdfaLiteContext Self reference
     */
    public function setDefaultVocabulary(VocabularyInterface $vocabulary)
    {
        // If the new default vocabulary differs from the current one
        if ($this->defaultVocabulary !== $vocabulary) {
            $context = clone $this;
            $context->defaultVocabulary = $vocabulary;
            return $context;
        }

        return $this;
    }
}
