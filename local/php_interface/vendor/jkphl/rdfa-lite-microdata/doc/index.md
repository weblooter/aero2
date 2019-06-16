# About jkphl/rdfa-lite-microdata

[![Build Status][travis-image]][travis-url] [![Coverage Status][coveralls-image]][coveralls-url] [![Scrutinizer Code Quality][scrutinizer-image]][scrutinizer-url]  [![Code climate][codeclimate-image]][codeclimate-url]  [![Documentation Status][readthedocs-image]][readthedocs-url]  [![Clear architecture][clear-architecture-image]][clear-architecture-url]

> RDFa Lite 1.1 and HTML Microdata parser for web documents (HTML, SVG, XML)

*rdfa-lite-microdata* is used for extracting [RDFa Lite 1.1](https://www.w3.org/TR/rdfa-lite/ "RDFa Lite 1.1 - Second Edition") and [HTML Microdata](https://www.w3.org/TR/microdata/) information out of web documents (HTML / SVG / XML). The embedded structures may use arbitrary vocabularies (e.g. [schema.org](https://schema.org/)) and are returned as a Plain Old PHP Object (POPO) which is compliant with the JSON serialization [described for HTML Microdata](https://www.w3.org/TR/microdata/#json).

### RDFa Lite 1.1

To extract [RDFa Lite 1.1](https://www.w3.org/TR/rdfa-lite/ "RDFa Lite 1.1 - Second Edition") data out of a web document, instantiate an `RdfaLite` parser and call the appropriate parse method:

```php
$rdfaParser = new \Jkphl\RdfaLiteMicrodata\Ports\Parser\RdfaLite();

// Parse an HTML file
$rdfaItems = $rdfaParser->parseHtmlFile('/path/to/file.html');

// Parse an HTML string
$rdfaItems = $rdfaParser->parseHtml('<html><head>...</head><body vocab="http://schema.org/">...</body>');

// Parse a DOM document (here: created from an HTML string)
$rdfaDom = new \DOMDocument();
$rdfaDom->loadHTML('<html><head>...</head><body vocab="http://schema.org/">...</body>');
$rdfaItems = $rdfaParser->parseDom($rdfaDom);

// Parse an XML file (e.g. SVG)
$rdfaItems = $rdfaParser->parseXmlFile('/path/to/file.svg');

// Parse an XML string (e.g. SVG)
$rdfaItems = $rdfaParser->parseXml('<svg viewBox="0 0 100 100" vocab="http://schema.org/">...</svg>');

echo json_encode($rdfaItems, JSON_PRETTY_PRINT);
```

The resulting JSON serialization will look something like this (JSON serialization):

```json
{
    "items": [
        {
            "type": [
                "http://schema.org/Movie"
            ],
            "id": "http://www.imdb.com/title/tt0499549/",
            "properties": {
                "http://schema.org/name": [
                    "Avatar"
                ],
                "http://schema.org/director": [
                    {
                        "type": [
                            "http://schema.org/Person"
                        ],
                        "id": null,
                        "properties": {
                            "http://schema.org/name": [
                                "James Cameron"
                            ],
                            "http://schema.org/birthDate": [
                                "August 16, 1954"
                            ]
                        }
                    }
                ],
                "http://schema.org/genre": [
                    "Science fiction"
                ],
                "http://schema.org/trailer": [
                    "../movies/avatar-theatrical-trailer.html"
                ]
            }
        }
    ]
}
```

Item types and property names can be treated as references consisting of a profile IRI and a separate name. To enable IRI mode, instantiate the parser with `true` as argument:

```php
$rdfaParser = new \Jkphl\RdfaLiteMicrodata\Ports\Parser\RdfaLite(true);
$rdfaItems = $rdfaParser->parseHtmlFile('/path/to/file.html');
```

With IRI mode enabled, the result will look like more verbose (JSON serialization):

```json
{
    "items": [
        {
            "type": [
                {
                    "profile": "http://schema.org/",
                    "name": "Movie"
                }
            ],
            "id": "http://www.imdb.com/title/tt0499549/",
            "properties": {
                "http://schema.org/name": {
                    "profile": "http://schema.org/",
                    "name": "name",
                    "values": [
                        "Avatar"
                    ]
                },
                "http://schema.org/director": {
                    "profile": "http://schema.org/",
                    "name": "director",
                    "values": [
                        {
                            "type": [
                                {
                                    "profile": "http://schema.org/",
                                    "name": "Person"
                                }
                            ],
                            "id": null,
                            "properties": {
                                "http://schema.org/name": {
                                    "profile": "http://schema.org/",
                                    "name": "name",
                                    "values": [
                                        "James Cameron"
                                    ]
                                },
                                "http://schema.org/birthDate": {
                                    "profile": "http://schema.org/",
                                    "name": "birthDate",
                                    "values": [
                                        "August 16, 1954"
                                    ]
                                }
                            }
                        }
                    ]
                },
                "http://schema.org/genre": {
                    "profile": "http://schema.org/",
                    "name": "genre",
                    "values": [
                        "Science fiction"
                    ]
                },
                "http://schema.org/trailer": {
                    "profile": "http://schema.org/",
                    "name": "trailer",
                    "values": [
                        "../movies/avatar-theatrical-trailer.html"
                    ]
                }
            }
        }
    ]
}
```

### HTML Microdata

The [Microdata](https://www.w3.org/TR/microdata/) format isn't specified for non-HTML host formats, so the `Microdata` parser only supports HTML processing:
   

```php
$microdataParser = new \Jkphl\RdfaLiteMicrodata\Ports\Parser\Microdata();

// Parse an HTML file
$microdataItems = $microdataParser->parseHtmlFile('/path/to/file.html');

// Parse an HTML string
$microdataItems = $microdataParser->parseHtml('<html><head>...</head><body itemscope itemtype="http://schema.org/Movie">...</body>');

// Parse a DOM document created from an HTML string
$microdataDom = new \DOMDocument();
$microdataDom->loadHTML('<html><head>...</head><body itemscope itemtype="http://schema.org/Movie">...</body>');
$microdataItems = $microdataParser->parseDom($microdataDom);

// Parse an HTML string with types / property names treated as IRIs
$microdataParserIri = new \Jkphl\RdfaLiteMicrodata\Ports\Parser\Microdata(true);
$microdataItems = $microdataParser->parseHtmlFile('/path/to/file.html');
```

## Installation

This library requires PHP >=5.5 or later. I recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies. It's installable and autoloadable via [Composer](https://getcomposer.org/) as [jkphl/rdfa-lite-microdata](https://packagist.org/packages/jkphl/rdfa-lite-microdata).

```bash
composer require jkphl/rdfa-lite-microdata
```

Alternatively, [download a release](https://github.com/jkphl/rdfa-lite-microdata/releases) or clone [the repository](https://github.com/jkphl/rdfa-lite-microdata), then require or include its [`autoload.php`](https://github.com/jkphl/rdfa-lite-microdata/blob/master/autoload.php) file.


## Dependencies

![Composer dependency graph](https://rawgit.com/jkphl/rdfa-lite-microdata/master/doc/dependencies.svg)


## License

Copyright © 2017 [Joschi Kuphal][author-url] / joschi@tollwerk.de. Licensed under the terms of the [MIT license](../LICENSE).


[codeclimate-image]: https://lima.codeclimate.com/github/jkphl/rdfa-lite-microdata/badges/gpa.svg
[codeclimate-url]: https://lima.codeclimate.com/github/jkphl/rdfa-lite-microdata
[readthedocs-url]: http://jkphlrdfa-lite-microdata.readthedocs.io/en/latest/
[coveralls-url]: https://coveralls.io/github/jkphl/rdfa-lite-microdata?branch=master
[clear-architecture-url]: https://github.com/jkphl/clear-architecture
[travis-url]: https://travis-ci.org/jkphl/rdfa-lite-microdata
[scrutinizer-url]: https://scrutinizer-ci.com/g/jkphl/rdfa-lite-microdata/?branch=master
[clear-architecture-image]: https://img.shields.io/badge/Clear%20Architecture-%E2%9C%94-brightgreen.svg
[travis-image]: https://secure.travis-ci.org/jkphl/rdfa-lite-microdata.svg
[scrutinizer-image]: https://scrutinizer-ci.com/g/jkphl/rdfa-lite-microdata/badges/quality-score.png?b=master
[readthedocs-image]: https://readthedocs.org/projects/jkphlrdfa-lite-microdata/badge/?version=latest
[coveralls-image]: https://coveralls.io/repos/github/jkphl/rdfa-lite-microdata/badge.svg?branch=master


[author-url]: https://jkphl.is
