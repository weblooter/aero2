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

namespace Jkphl\RdfaLiteMicrodata\Domain\Exceptions;

/**
 * RuntimeException
 *
 * @package Jkphl\RdfaLiteMicrodata
 * @subpackage Jkphl\RdfaLiteMicrodata\Domain
 */
class RuntimeException extends \RuntimeException implements RdfaLiteMicrodataDomainExceptionInterface
{
    /**
     * Invalid vocabulary URL
     *
     * @var string
     */
    const INVALID_VOCABULARY_URI_STR = 'Invalid vocabulary URL "%s"';
    /**
     * Invalid vocabulary
     *
     * @var int
     */
    const INVALID_VOCABULARY_URI = 1486823170;
    /**
     * Invalid type name
     *
     * @var string
     */
    const INVALID_TYPE_NAME_STR = 'Invalid type name "%s" (vocabulary %s)';
    /**
     * Invalid type name
     *
     * @var int
     */
    const INVALID_TYPE_NAME = 1487435276;
    /**
     * Invalid type
     *
     * @var string
     */
    const INVALID_TYPE_STR = 'Invalid type "%s"';
    /**
     * Invalid type
     *
     * @var int
     */
    const INVALID_TYPE = 1487435964;
    /**
     * Invalid property name
     *
     * @var string
     */
    const INVALID_PROPERTY_NAME_STR = 'Invalid property name "%s"';
    /**
     * Invalid property name
     *
     * @var int
     */
    const INVALID_PROPERTY_NAME = 1486848618;
}
