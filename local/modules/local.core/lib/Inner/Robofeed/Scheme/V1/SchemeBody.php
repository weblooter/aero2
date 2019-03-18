<?php

namespace Local\Core\Inner\Robofeed\Scheme\V1;

use Local\Core\Inner\Robofeed\SchemeFields;
use Local\Core\Model\Reference\CountryTable;
use Local\Core\Model\Reference\CurrencyTable;
use Local\Core\Model\Reference\MeasureTable;

/**
 * Класс описывающий схему Fobofeed версии 1
 *
 * @package Local\Core\Inner\Robofeed\Base\V1
 */
class SchemeBody
{
    /**
     * Формирует и возвращает тело схемы
     *
     * @return array
     */
    public static function getSchemeBody()
    {
        return [
            'defaultValues' => self::getSchemeDefaultValues(),
            'categories' => self::getSchemeCategories(),
            'offers' => [
                'offer' => array_merge(
                    [
                        '@attr' => [
                            'id' => new SchemeFields\IntegerField(
                                'robofeed__offers__offer__@id', [
                                    'required' => true,
                                    'title' => 'Идентификатор товара, уникален',
                                    'xml_path' => 'robofeed->offers->offer->@id',
                                    'site' => 9
                                ]
                            ),
                            'groupId' => new SchemeFields\IntegerField(
                                'robofeed__offers__offer__@groupId', [
                                    'required' => false,
                                    'title' => 'Идентификатор группы товара, которые повторяются в рамках робофида',
                                    'xml_path' => 'robofeed->offers->offer->@groupId',
                                    'site' => 9
                                ]
                            )
                        ]
                    ],
                    self::getSchemeOffer()
                )
            ]
        ];
    }

    private static function getSchemeCategories()
    {
        return [
            'category' => [
                '@attr' => [
                    'id' => new SchemeFields\IntegerField(
                        'robofeed__categories__category__@id', [
                            'required' => true,
                            'title' => 'Идентификатор категории, уникален',
                            'xml_path' => 'robofeed->categories->category->@id',
                            'site' => 9
                        ]
                    ),
                    'parentId' => new SchemeFields\IntegerField(
                        'robofeed__categories__category__@parentId', [
                            'required' => false,
                            'title' => 'Идентификатор родительской категории',
                            'xml_path' => 'robofeed->categories->category->@parentId',
                            'site' => 9
                        ]
                    )
                ],
                '@value' => new SchemeFields\StringField(
                    'robofeed__categories__category__@value', [
                        'requited' => true,
                        'title' => 'Название категории',
                        'xml_path' => 'robofeed->categories->category',
                    ]

                )
            ]
        ];
    }

    private static function getSchemeDefaultValues()
    {
        return [
            'offer' => self::getSchemeOffer()
        ];
    }

    private static function getSchemeOffer()
    {
        return [
            'article' => new SchemeFields\StringField(
                'robofeed__offers__offer__article', [
                    'required' => true,
                    'title' => 'Артикул товара',
                    'xml_path' => 'robofeed->offers->offer->article',
                    'site' => 30
                ]
            ),
            'fullName' => new SchemeFields\StringField(
                'robofeed__offers__offer__fullName', [
                    'required' => true,
                    'title' => 'Полное название товара.',
                    'xml_path' => 'robofeed->offers->offer->fullName'
                ]
            ),
            'simpleName' => new SchemeFields\StringField(
                'robofeed__offers__offer__simpleName', [
                    'required' => true,
                    'title' => 'Простое название товара',
                    'xml_path' => 'robofeed->offers->offer->simpleName',
                    'site' => 30
                ]
            ),
            'manufacturer' => new SchemeFields\StringField(
                'robofeed__offers__offer__manufacturer', [
                    'required' => true,
                    'title' => 'Название компании производителя',
                    'xml_path' => 'robofeed->offers->offer->manufacturer',
                ]
            ),
            'model' => new SchemeFields\StringField(
                'robofeed__offers__offer__model', [
                    'required' => false,
                    'title' => 'Модель товара',
                    'xml_path' => 'robofeed->offers->offer->model',
                ]
            ),
            'url' => new SchemeFields\StringField(
                'robofeed__offers__offer__url', [
                    'required' => false,
                    'title' => 'Ссылка на детальную страницу товара',
                    'xml_path' => 'robofeed->offers->offer->url',
                    'format' => '/^https?\:\/\//'
                ]
            ),
            'manufacturerCode' => new SchemeFields\StringField(
                'robofeed__offers__offer__manufacturerCode', [
                    'required' => false,
                    'title' => 'Код производителя для данного товара',
                    'xml_path' => 'robofeed->offers->offer->manufacturerCode',
                    'size' => 50
                ]
            ),
            'price' => new SchemeFields\NumericField(
                'robofeed__offers__offer__price', [
                    'required' => true,
                    'title' => 'Текущая публичная стоимость товара',
                    'xml_path' => 'robofeed->offers->offer->price',
                    'scale' => 2
                ]
            ),
            'oldPrice' => new SchemeFields\NumericField(
                'robofeed__offers__offer__oldPrice', [
                    'required' => false,
                    'title' => 'Базовая / старая стоимость товара',
                    'xml_path' => 'robofeed->offers->offer->oldPrice',
                    'scale' => 2
                ]
            ),
            'currencyCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__currencyCode', [
                    'required' => true,
                    'title' => 'Символьный код валюты',
                    'xml_path' => 'robofeed->offers->offer->currencyCode',
                    'class' => CurrencyTable::class
                ]
            ),
            'quantity' => new SchemeFields\IntegerField(
                'robofeed__offers__offer__quantity', [
                    'required' => true,
                    'title' => 'Количество товара в единицах измерения',
                    'xml_path' => 'robofeed->offers->offer->quantity',
                    'size' => 9
                ]
            ),
            'unitOfMeasure' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__unitOfMeasure', [
                    'required' => true,
                    'title' => 'Символьный код единицы измерения',
                    'xml_path' => 'robofeed->offers->offer->unitOfMeasure',
                    'class' => MeasureTable::class
                ]
            ),
            'minQuantity' => new SchemeFields\IntegerField(
                'robofeed__offers__offer__minQuantity', [
                    'required' => true,
                    'title' => 'Минимальное кол-во товара в заказе',
                    'xml_path' => 'robofeed->offers->offer->minQuantity',
                    'size' => 9
                ]
            ),
            'categoryId' => new SchemeFields\IntegerField(
                'robofeed__offers__offer__categoryId', [
                    'required' => true,
                    'title' => 'ID категории товара',
                    'xml_path' => 'robofeed->offers->offer->categoryId',
                    'size' => 9
                ]
            ),
            'images' => [
                'image' => new SchemeFields\StringField(
                    'robofeed__offers__offer__images__image', [
                        'required' => false,
                        'title' => 'Ссылка на изображение',
                        'xml_path' => 'robofeed->offers->offer->images->image',
                        'format' => '/^https?\:\/\//'
                    ]
                ),
            ],
            'countryOfProductionCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__countryOfProductionCode', [
                    'required' => false,
                    'title' => 'Символьный код страны производства',
                    'xml_path' => 'robofeed->offers->offer->countryOfProductionCode',
                    'class' => CountryTable::class
                ]
            ),
            'description' => new SchemeFields\TextField(
                'robofeed__offers__offer__description', [
                    'required' => false,
                    'title' => 'Описание товара',
                    'xml_path' => 'robofeed->offers->offer->description',
                    'html' => true,
                    'size' => 3000
                ]
            ),
            'manufacturerWarranty' => new SchemeFields\BooleanField(
                'robofeed__offers__offer__manufacturerWarranty', [
                    'required' => true,
                    'title' => 'Официальная гарантия производителя',
                    'xml_path' => 'robofeed->offers->offer->manufacturerWarranty',
                ]
            ),
            'isSex' => new SchemeFields\BooleanField(
                'robofeed__offers__offer__isSex', [
                    'required' => false,
                    'title' => 'Товар имеет отношение к удовлетворению сексуальных потребностей либо иным образом эксплуатирует интерес к сексу',
                    'xml_path' => 'robofeed->offers->offer->isSex',
                ]
            ),
            'isSoftware' => new SchemeFields\BooleanField(
                'robofeed__offers__offer__isSoftware', [
                    'required' => false,
                    'title' => 'Товар является программным обеспечением',
                    'xml_path' => 'robofeed->offers->offer->isSoftware',
                ]
            ),
            'weight' => new SchemeFields\IntegerField(
                'robofeed__offers__offer__weight', [
                    'required' => false,
                    'title' => 'Вес товара в выбранных единицах измерения',
                    'xml_path' => 'robofeed->offers->offer->weight',
                    'size' => 9
                ]
            ),
            'weightUnitCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__weightUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения веса',
                    'xml_path' => 'robofeed->offers->offer->weightUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'width' => new SchemeFields\NumericField(
                'robofeed__offers__offer__width', [
                    'required' => false,
                    'title' => 'Ширина товара в выбранных единицах измерения',
                    'xml_path' => 'robofeed->offers->offer->width',
                    'size' => 9
                ]
            ),
            'widthUnitCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__widthUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения ширины',
                    'xml_path' => 'robofeed->offers->offer->widthUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'height' => new SchemeFields\NumericField(
                'robofeed__offers__offer__height', [
                    'required' => false,
                    'title' => 'Высота товара в выбранных единицах измерения',
                    'xml_path' => 'robofeed->offers->offer->height',
                    'size' => 9
                ]
            ),
            'heightUnitCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__heightUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения высоты',
                    'xml_path' => 'robofeed->offers->offer->heightUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'length' => new SchemeFields\NumericField(
                'robofeed__offers__offer__length', [
                    'required' => false,
                    'title' => 'Длина товара в выбранных единицах измерения',
                    'xml_path' => 'robofeed->offers->offer->length',
                    'size' => 9
                ]
            ),
            'lengthUnitCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__lengthUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения длины',
                    'xml_path' => 'robofeed->offers->offer->lengthUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'volume' => new SchemeFields\NumericField(
                'robofeed__offers__offer__volume', [
                    'required' => false,
                    'title' => 'Объем товара',
                    'xml_path' => 'robofeed->offers->offer->volume',
                    'size' => 9
                ]
            ),
            'volumeUnitCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__volumeUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения объема',
                    'xml_path' => 'robofeed->offers->offer->volumeUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'warrantyPeriod' => new SchemeFields\IntegerField(
                'robofeed__offers__offer__warrantyPeriod', [
                    'required' => false,
                    'title' => 'Срок официальной гарантии товара',
                    'xml_path' => 'robofeed->offers->offer->warrantyPeriod',
                    'size' => 9
                ]
            ),
            'warrantyPeriodCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__warrantyPeriodCode', [
                    'required' => false,
                    'title' => 'Единица измерения срока официальной гарантии товара',
                    'xml_path' => 'robofeed->offers->offer->warrantyPeriodCode',
                    'class' => MeasureTable::class
                ]
            ),
            'expiryPeriod' => new SchemeFields\IntegerField(
                'robofeed__offers__offer__expiryPeriod', [
                    'required' => false,
                    'title' => 'Срок годности / срок службы товара от даты производства',
                    'xml_path' => 'robofeed->offers->offer->expiryPeriod',
                    'size' => 9
                ]
            ),
            'expiryPeriodCode' => new SchemeFields\ReferenceField(
                'robofeed__offers__offer__expiryPeriodCode', [
                    'required' => false,
                    'title' => 'Единица измерения срока годности / срока службы товара',
                    'xml_path' => 'robofeed->offers->offer->expiryPeriodCode',
                    'class' => MeasureTable::class
                ]
            ),
            'expiryDate' => new SchemeFields\DatetimeField(
                'robofeed__offers__offer__expiryDate', [
                    'required' => false,
                    'title' => 'Дата истечения срока годности товара',
                    'xml_path' => 'robofeed->offers->offer->expiryDate',
                ]
            ),
            'delivery' => self::getSchemeDelivery(),
            'pickup' => self::getSchemePickup(),
            'inStock' => new SchemeFields\BooleanField(
                'robofeed__offers__offer__inStock', [
                    'required' => true,
                    'title' => 'Товар есть в наличии',
                    'xml_path' => 'robofeed->offers->offer->inStock',
                ]
            ),
            'salesNotes' => new SchemeFields\StringField(
                'robofeed__offers__offer__salesNotes', [
                    'required' => false,
                    'title' => 'Условия продажи товара',
                    'xml_path' => 'robofeed->offers->offer->salesNotes',
                    'size' => 50
                ]
            ),
            'params' => [
                'param' => [
                    '@attr' => [
                        'code' => new SchemeFields\StringField(
                            'robofeed__offers__offer__params__param__@code', [
                                'required' => true,
                                'title' => 'Символьный код параметра',
                                'xml_path' => 'robofeed->offers->offer->params->param->@code',
                                'format' => '/^[A-Z0-9\_]{1,50}$/',
                                'size' => 50
                            ]
                        ),
                        'name' => new SchemeFields\StringField(
                            'robofeed__offers__offer__params__param__@name', [
                                'required' => true,
                                'title' => 'Название параметра',
                                'xml_path' => 'robofeed->offers->offer->params->param->@name',
                                'size' => 100
                            ]
                        )
                    ],
                    '@value' => new SchemeFields\StringField(
                        'robofeed__offers__offer__params__param', [
                            'required' => true,
                            'title' => 'Значение параметра',
                            'xml_path' => 'robofeed->offers->offer->params->param',
                            'size' => 255
                        ]
                    ),
                ]
            ]
        ];
    }

    private static function getSchemeDelivery()
    {
        return [
            '@attr' => [
                'available' => new SchemeFields\BooleanField(
                    'robofeed__offers__offer__delivery__@available', [
                        'required' => true,
                        'title' => 'Имеется ли служба доставки',
                        'xml_path' => 'robofeed->offers->offer->delivery->@available',
                    ]
                )
            ],
            'option' => [
                '@attr' => [
                    'priceFrom' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__delivery__@priceFrom', [
                            'required' => true,
                            'title' => 'Стоимость доставки "от"',
                            'xml_path' => 'robofeed->offers->offer->delivery->@priceFrom',
                            'size' => 9
                        ]
                    ),
                    'priceTo' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__delivery__@priceTo', [
                            'required' => false,
                            'title' => 'Стоимость доставки "до"',
                            'xml_path' => 'robofeed->offers->offer->delivery->@priceTo',
                            'size' => 9
                        ]
                    ),
                    'currencyCode' => new SchemeFields\ReferenceField(
                        'robofeed__offers__offer__delivery__@currencyCode', [
                            'required' => true,
                            'title' => 'Символьный код валюты стоимости',
                            'xml_path' => 'robofeed->offers->offer->delivery->@currencyCode',
                            'class' => CurrencyTable::class
                        ]
                    ),
                    'daysFrom' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__delivery__@daysFrom', [
                            'required' => true,
                            'title' => 'Сроки доставки "от" в днях',
                            'xml_path' => 'robofeed->offers->offer->delivery->@daysFrom',
                            'size' => 2,
                        ]
                    ),
                    'daysTo' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__delivery__@daysTo', [
                            'required' => false,
                            'title' => 'Сроки доставки "до" в днях',
                            'xml_path' => 'robofeed->offers->offer->delivery->@daysTo',
                            'size' => 2,
                        ]
                    ),
                    'orderBefore' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__delivery__@orderBefore', [
                            'required' => false,
                            'title' => 'временные рамки "сделать заказ до N часов", что бы вариант доставки был актуален',
                            'xml_path' => 'robofeed->offers->offer->delivery->@orderBefore',
                            'size' => 2,
                        ]
                    ),
                    'orderAfter' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__delivery__@orderAfter', [
                            'required' => false,
                            'title' => 'временные рамки "сделать заказ после N часов", что бы вариант доставки был актуален',
                            'xml_path' => 'robofeed->offers->offer->delivery->@orderAfter',
                            'size' => 2,
                        ]
                    ),
                    'deliveryRegion' => new SchemeFields\EnumField(
                        'robofeed__offers__offer__delivery__@deliveryRegion', [
                            'required' => true,
                            'title' => 'признак региона, на которое распространяется правило',
                            'xml_path' => 'robofeed->offers->offer->delivery->@deliveryRegion',
                            'values' => [
                                'in',
                                'out',
                                'all'
                            ]
                        ]
                    ),
                ]
            ]
        ];
    }

    private static function getSchemePickup()
    {
        return [
            '@attr' => [
                'available' => new SchemeFields\BooleanField(
                    'robofeed__offers__offer__pickup__@available', [
                        'required' => true,
                        'title' => 'Имеется ли возможность самовывоза из магазина или со склада',
                        'xml_path' => 'robofeed->offers->offer->pickup->@available',
                    ]
                )
            ],
            'option' => [
                '@attr' => [
                    'price' => new SchemeFields\StringField(
                        'robofeed__offers__offer__pickup__@price', [
                            'required' => true,
                            'title' => 'Стоимость самовывоза',
                            'xml_path' => 'robofeed->offers->offer->pickup->@price',
                        ]
                    ),
                    'currencyCode' => new SchemeFields\ReferenceField(
                        'robofeed__offers__offer__pickup__@currencyCode', [
                            'required' => true,
                            'title' => 'Символьный код валюты стоимости',
                            'xml_path' => 'robofeed->offers->offer->pickup->@currencyCode',
                            'class' => CurrencyTable::class
                        ]
                    ),
                    'supplyFrom' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__pickup__@supplyFrom', [
                            'required' => true,
                            'title' => 'Сроки поступления товара в магазин/на склад "от" в днях',
                            'xml_path' => 'robofeed->offers->offer->pickup->@supplyFrom',
                            'size' => 2
                        ]
                    ),
                    'supplyTo' => new SchemeFields\IntegerField(
                        'robofeed__offers__offer__pickup__@supplyTo', [
                            'required' => true,
                            'title' => 'Сроки поступления товара в магазин/на склад "до" в днях',
                            'xml_path' => 'robofeed->offers->offer->pickup->@supplyTo',
                            'size' => 2
                        ]
                    ),
                    'orderBefore' => new SchemeFields\StringField(
                        'robofeed__offers__offer__pickup__@orderBefore', [
                            'required' => false,
                            'title' => 'временные рамки "сделать заказ до N часов", что бы вариант самовывоза был актуален',
                            'xml_path' => 'robofeed->offers->offer->pickup->@orderBefore',
                            'size' => 2
                        ]
                    ),
                    'orderAfter' => new SchemeFields\StringField(
                        'robofeed__offers__offer__pickup__@orderAfter', [
                            'required' => false,
                            'title' => 'временные рамки "сделать заказ после N часов", что бы вариант самовывоза был актуален',
                            'xml_path' => 'robofeed->offers->offer->pickup->@orderAfter',
                            'size' => 2
                        ]
                    )
                ]
            ]
        ];
    }

    public static function getXmlExample()
    {
        return file_get_contents(__DIR__.'/example.xml');
    }
}