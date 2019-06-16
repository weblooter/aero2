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

namespace Jkphl\RdfaLiteMicrodata\Ports\Parser;

use Jkphl\RdfaLiteMicrodata\Application\Context\ContextInterface;
use Jkphl\RdfaLiteMicrodata\Application\Contract\DocumentFactoryInterface;
use Jkphl\RdfaLiteMicrodata\Application\Contract\ElementProcessorInterface;
use Jkphl\RdfaLiteMicrodata\Application\Parser\Parser;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Parser\ParserInterface;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Service\ThingGateway;
use Jkphl\RdfaLiteMicrodata\Ports\Exceptions\OutOfBoundsException;
use Jkphl\RdfaLiteMicrodata\Ports\Exceptions\RuntimeException;

/**
 * Abstract parser
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Infrastructure
 */
abstract class AbstractParser implements ParserInterface
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
     * Get the contents of a file
     *
     * @param string $file File
     * @return string File contents
     * @throws RuntimeException If the file is not readable
     */
    protected function getFileContents($file)
    {
        // If the file is not readable
        if (!is_readable($file)) {
            throw new RuntimeException(
                sprintf(RuntimeException::INVALID_FILE_STR, $file),
                RuntimeException::INVALID_FILE
            );
        }

        return file_get_contents($file);
    }

    /**
     * Parse a source
     *
     * @param mixed $source Source
     * @param DocumentFactoryInterface $documentFactory Document factory
     * @param ElementProcessorInterface $elementProcessor Element processor
     * @param ContextInterface $context Context
     * @return \stdClass Extracted things
     */
    protected function parseSource(
        $source,
        DocumentFactoryInterface $documentFactory,
        ElementProcessorInterface $elementProcessor,
        ContextInterface $context
    ) {
        try {
            $parser = new Parser($documentFactory, $elementProcessor, $context);
            $things = $parser->parse($source);
            $gateway = new ThingGateway($this->iri);
            return (object)['items' => $gateway->export($things)];
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException($e->getMessage(), $e->getCode());
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }
    }
}
