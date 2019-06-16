<?php

/**
 * rdfa-lite-microdata
 *
 * @category Jkphl
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Ports
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

use Jkphl\RdfaLiteMicrodata\Application\Context\RdfaLiteContext;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\DomDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\HtmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Factories\XmlDocumentFactory;
use Jkphl\RdfaLiteMicrodata\Infrastructure\Parser\RdfaLiteElementProcessor;

/**
 * RDFa Lite 1.1 parser
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Ports
 * @see https://www.w3.org/TR/rdfa-lite/
 */
class RdfaLite extends AbstractParser
{
    /**
     * Parse an XML file
     *
     * @param string $file XML file path
     * @return \stdClass Extracted things
     */
    public function parseXmlFile($file)
    {
        return $this->parseXml($this->getFileContents($file));
    }

    /**
     * Parse an XML string
     *
     * @param string $string XML string
     * @return \stdClass Extracted things
     */
    public function parseXml($string)
    {
        return $this->parseSource(
            $string,
            new XmlDocumentFactory(),
            new RdfaLiteElementProcessor(),
            new RdfaLiteContext()
        );
    }

    /**
     * Parse an HTML file
     *
     * @param string $file HTML file path
     * @return \stdClass Extracted things
     */
    public function parseHtmlFile($file)
    {
        return $this->parseHtml($this->getFileContents($file));
    }

    /**
     * Parse an HTML string
     *
     * @param string $string HTML string
     * @return \stdClass Extracted things
     */
    public function parseHtml($string)
    {
        return $this->parseSource(
            $string,
            new HtmlDocumentFactory(),
            (new RdfaLiteElementProcessor())->setHtml(true),
            new RdfaLiteContext()
        );
    }

    /**
     * Parse a DOM document
     *
     * @param \DOMDocument $dom DOM document
     * @return \stdClass Extracted things
     */
    public function parseDom(\DOMDocument $dom)
    {
        return $this->parseSource(
            $dom,
            new DomDocumentFactory(),
            (new RdfaLiteElementProcessor())->setHtml(true),
            new RdfaLiteContext()
        );
    }
}
