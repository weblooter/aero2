<?php

namespace Local\Core\Inner\Robofeed\Schema\V1;

use Local\Core\Inner\Robofeed\SchemaFields;
use Local\Core\Model\Reference\CountryTable;
use Local\Core\Model\Reference\CurrencyTable;
use Local\Core\Model\Reference\MeasureTable;

class Schema extends \Local\Core\Inner\Robofeed\Schema\AbstractSchema
{
    public static function getVersion()
    {
        return 1;
    }

    /**
     * Не используется в схеме.<br/>
     * Вместо нее испольуется getSchemaMap()
     *
     * @see \Local\Core\Inner\Robofeed\Schema\AbstractSchema::getSchemaMap();
     *
     * @deprecated
     */
    public function run()
    {
    }

    /** @inheritdoc */
    protected static function getSchemaBody()
    {
        return [
            'defaultValues' => self::getSchemaDefaultValues('robofeed__defaultValues', 'robofeed->defaultValues'),
            'categories' => self::getSchemaCategories(),
            'offers' => [
                'offer' => [
                    '@attr' => [
                        'id' => new SchemaFields\IntegerField(
                            'robofeed__offers__offer__@id', [
                                'required' => true,
                                'title' => 'Идентификатор товара, уникален',
                                'xml_path' => 'robofeed->offers->offer->@id',
                                'site' => 9
                            ]
                        ),
                        'groupId' => new SchemaFields\IntegerField(
                            'robofeed__offers__offer__@groupId', [
                                'required' => false,
                                'title' => 'Идентификатор группы товара, которые повторяются в рамках робофида',
                                'xml_path' => 'robofeed->offers->offer->@groupId',
                                'site' => 9
                            ]
                        )
                    ],
                    '@value' => self::getSchemaOffer('robofeed__offers__offer', 'robofeed->offers->offer')
                ]
            ]
        ];
    }

    private static function getSchemaCategories()
    {
        return [
            'category' => [
                '@attr' => [
                    'id' => new SchemaFields\IntegerField(
                        'robofeed__categories__category__@id', [
                            'required' => true,
                            'title' => 'Идентификатор категории, уникален',
                            'xml_path' => 'robofeed->categories->category->@id',
                            'site' => 9
                        ]
                    ),
                    'parentId' => new SchemaFields\IntegerField(
                        'robofeed__categories__category__@parentId', [
                            'required' => false,
                            'title' => 'Идентификатор родительской категории',
                            'xml_path' => 'robofeed->categories->category->@parentId',
                            'site' => 9
                        ]
                    )
                ],
                '@value' => new SchemaFields\StringField(
                    'robofeed__categories__category__@value', [
                        'required' => true,
                        'title' => 'Название категории',
                        'xml_path' => 'robofeed->categories->category',
                    ]

                )
            ]
        ];
    }

    private static function getSchemaDefaultValues($name, $path)
    {
        return [
            'offer' => self::getSchemaOffer($name.'__offer', $path.'->offer')
        ];
    }

    private static function getSchemaOffer($name, $path)
    {
        return [
            'article' => new SchemaFields\StringField(
                $name.'__article', [
                    'required' => true,
                    'title' => 'Артикул товара',
                    'xml_path' => $path.'->article',
                    'site' => 30
                ]
            ),
            'fullName' => new SchemaFields\StringField(
                $name.'__fullName', [
                    'required' => true,
                    'title' => 'Полное название товара',
                    'xml_path' => $path.'->fullName'
                ]
            ),
            'simpleName' => new SchemaFields\StringField(
                $name.'__simpleName', [
                    'required' => true,
                    'title' => 'Простое название товара',
                    'xml_path' => $path.'->simpleName',
                    'site' => 30
                ]
            ),
            'manufacturer' => new SchemaFields\StringField(
                $name.'__manufacturer', [
                    'required' => true,
                    'title' => 'Название компании производителя',
                    'xml_path' => $path.'->manufacturer',
                ]
            ),
            'model' => new SchemaFields\StringField(
                $name.'__model', [
                    'required' => false,
                    'title' => 'Модель товара',
                    'xml_path' => $path.'->model',
                ]
            ),
            'url' => new SchemaFields\StringField(
                $name.'__url', [
                    'required' => false,
                    'title' => 'Ссылка на детальную страницу товара',
                    'xml_path' => $path.'->url',
                    'format' => '/^https?\:\/\//'
                ]
            ),
            'manufacturerCode' => new SchemaFields\StringField(
                $name.'__manufacturerCode', [
                    'required' => false,
                    'title' => 'Код производителя для данного товара',
                    'xml_path' => $path.'->manufacturerCode',
                    'size' => 50
                ]
            ),
            'price' => new SchemaFields\NumericField(
                $name.'__price', [
                    'required' => true,
                    'title' => 'Текущая публичная стоимость товара',
                    'xml_path' => $path.'->price',
                    'scale' => 2
                ]
            ),
            'oldPrice' => new SchemaFields\NumericField(
                $name.'__oldPrice', [
                    'required' => false,
                    'title' => 'Базовая / старая стоимость товара',
                    'xml_path' => $path.'->oldPrice',
                    'scale' => 2
                ]
            ),
            'currencyCode' => new SchemaFields\ReferenceField(
                $name.'__currencyCode', [
                    'required' => true,
                    'title' => 'Символьный код валюты',
                    'xml_path' => $path.'->currencyCode',
                    'class' => CurrencyTable::class
                ]
            ),
            'quantity' => new SchemaFields\IntegerField(
                $name.'__quantity', [
                    'required' => true,
                    'title' => 'Количество товара в единицах измерения',
                    'xml_path' => $path.'->quantity',
                    'size' => 9
                ]
            ),
            'unitOfMeasure' => new SchemaFields\ReferenceField(
                $name.'__unitOfMeasure', [
                    'required' => true,
                    'title' => 'Символьный код единицы измерения',
                    'xml_path' => $path.'->unitOfMeasure',
                    'class' => MeasureTable::class
                ]
            ),
            'minQuantity' => new SchemaFields\IntegerField(
                $name.'__minQuantity', [
                    'required' => true,
                    'title' => 'Минимальное кол-во товара в заказе',
                    'xml_path' => $path.'->minQuantity',
                    'size' => 9
                ]
            ),
            'categoryId' => new SchemaFields\IntegerField(
                $name.'__categoryId', [
                    'required' => true,
                    'title' => 'ID категории товара',
                    'xml_path' => $path.'->categoryId',
                    'size' => 9
                ]
            ),
            'image' => new SchemaFields\StringField(
                $name.'__image', [
                    'required' => false,
                    'title' => 'Ссылка на изображение',
                    'xml_path' => $path.'->image',
                    'format' => '/^https?\:\/\//'
                ]
            ),
            'countryOfProductionCode' => new SchemaFields\ReferenceField(
                $name.'__countryOfProductionCode', [
                    'required' => false,
                    'title' => 'Символьный код страны производства',
                    'xml_path' => $path.'->countryOfProductionCode',
                    'class' => CountryTable::class
                ]
            ),
            'description' => new SchemaFields\TextField(
                $name.'__description', [
                    'required' => false,
                    'title' => 'Описание товара',
                    'xml_path' => $path.'->description',
                    'html' => true,
                    'size' => 3000
                ]
            ),
            'manufacturerWarranty' => new SchemaFields\BooleanField(
                $name.'__manufacturerWarranty', [
                    'required' => true,
                    'title' => 'Официальная гарантия производителя',
                    'xml_path' => $path.'->manufacturerWarranty',
                ]
            ),
            'isSex' => new SchemaFields\BooleanField(
                $name.'__isSex', [
                    'required' => false,
                    'title' => 'Товар имеет отношение к удовлетворению сексуальных потребностей либо иным образом эксплуатирует интерес к сексу',
                    'xml_path' => $path.'->isSex',
                ]
            ),
            'isSoftware' => new SchemaFields\BooleanField(
                $name.'__isSoftware', [
                    'required' => false,
                    'title' => 'Товар является программным обеспечением',
                    'xml_path' => $path.'->isSoftware',
                ]
            ),
            'weight' => new SchemaFields\IntegerField(
                $name.'__weight', [
                    'required' => false,
                    'title' => 'Вес товара в выбранных единицах измерения',
                    'xml_path' => $path.'->weight',
                    'size' => 9
                ]
            ),
            'weightUnitCode' => new SchemaFields\ReferenceField(
                $name.'__weightUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения веса',
                    'xml_path' => $path.'->weightUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'width' => new SchemaFields\NumericField(
                $name.'__width', [
                    'required' => false,
                    'title' => 'Ширина товара в выбранных единицах измерения',
                    'xml_path' => $path.'->width',
                    'size' => 9
                ]
            ),
            'widthUnitCode' => new SchemaFields\ReferenceField(
                $name.'__widthUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения ширины',
                    'xml_path' => $path.'->widthUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'height' => new SchemaFields\NumericField(
                $name.'__height', [
                    'required' => false,
                    'title' => 'Высота товара в выбранных единицах измерения',
                    'xml_path' => $path.'->height',
                    'size' => 9
                ]
            ),
            'heightUnitCode' => new SchemaFields\ReferenceField(
                $name.'__heightUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения высоты',
                    'xml_path' => $path.'->heightUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'length' => new SchemaFields\NumericField(
                $name.'__length', [
                    'required' => false,
                    'title' => 'Длина товара в выбранных единицах измерения',
                    'xml_path' => $path.'->length',
                    'size' => 9
                ]
            ),
            'lengthUnitCode' => new SchemaFields\ReferenceField(
                $name.'__lengthUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения длины',
                    'xml_path' => $path.'->lengthUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'volume' => new SchemaFields\NumericField(
                $name.'__volume', [
                    'required' => false,
                    'title' => 'Объем товара',
                    'xml_path' => $path.'->volume',
                    'size' => 9
                ]
            ),
            'volumeUnitCode' => new SchemaFields\ReferenceField(
                $name.'__volumeUnitCode', [
                    'required' => false,
                    'title' => 'Единица измерения объема',
                    'xml_path' => $path.'->volumeUnitCode',
                    'class' => MeasureTable::class
                ]
            ),
            'warrantyPeriod' => new SchemaFields\IntegerField(
                $name.'__warrantyPeriod', [
                    'required' => false,
                    'title' => 'Срок официальной гарантии товара',
                    'xml_path' => $path.'->warrantyPeriod',
                    'size' => 9
                ]
            ),
            'warrantyPeriodCode' => new SchemaFields\ReferenceField(
                $name.'__warrantyPeriodCode', [
                    'required' => false,
                    'title' => 'Единица измерения срока официальной гарантии товара',
                    'xml_path' => $path.'->warrantyPeriodCode',
                    'class' => MeasureTable::class
                ]
            ),
            'expiryPeriod' => new SchemaFields\IntegerField(
                $name.'__expiryPeriod', [
                    'required' => false,
                    'title' => 'Срок годности / срок службы товара от даты производства',
                    'xml_path' => $path.'->expiryPeriod',
                    'size' => 9
                ]
            ),
            'expiryPeriodCode' => new SchemaFields\ReferenceField(
                $name.'__expiryPeriodCode', [
                    'required' => false,
                    'title' => 'Единица измерения срока годности / срока службы товара',
                    'xml_path' => $path.'->expiryPeriodCode',
                    'class' => MeasureTable::class
                ]
            ),
            'expiryDate' => new SchemaFields\DatetimeField(
                $name.'__expiryDate', [
                    'required' => false,
                    'title' => 'Дата истечения срока годности товара',
                    'xml_path' => $path.'->expiryDate',
                ]
            ),
            'delivery' => self::getSchemaDelivery($name, $path),
            'pickup' => self::getSchemaPickup($name, $path),
            'inStock' => new SchemaFields\BooleanField(
                $name.'__inStock', [
                    'required' => true,
                    'title' => 'Товар есть в наличии',
                    'xml_path' => $path.'->inStock',
                ]
            ),
            'salesNotes' => new SchemaFields\StringField(
                $name.'__salesNotes', [
                    'required' => false,
                    'title' => 'Условия продажи товара',
                    'xml_path' => $path.'->salesNotes',
                    'size' => 50
                ]
            ),
            'param' => [
                '@attr' => [
                    'code' => new SchemaFields\StringField(
                        $name.'__param__@code', [
                            'required' => true,
                            'title' => 'Символьный код параметра',
                            'xml_path' => $path.'->param->@code',
                            'format' => '/^[A-Z0-9\_]{1,50}$/',
                            'size' => 50
                        ]
                    ),
                    'name' => new SchemaFields\StringField(
                        $name.'__param__@name', [
                            'required' => true,
                            'title' => 'Название параметра',
                            'xml_path' => $path.'->param->@name',
                            'size' => 100
                        ]
                    )
                ],
                '@value' => new SchemaFields\StringField(
                    $name.'__param', [
                        'required' => true,
                        'title' => 'Значение параметра',
                        'xml_path' => $path.'->param',
                        'size' => 255
                    ]
                ),
            ]
        ];
    }

    private static function getSchemaDelivery($name, $path)
    {
        return [
            '@attr' => [
                'available' => new SchemaFields\BooleanField(
                    $name.'__delivery__@available', [
                        'required' => true,
                        'title' => 'Имеется ли служба доставки',
                        'xml_path' => $path.'->delivery->@available',
                    ]
                )
            ],
            'option' => [
                '@attr' => [
                    'priceFrom' => new SchemaFields\IntegerField(
                        $name.'__delivery__option__@priceFrom', [
                            'required' => true,
                            'title' => 'Стоимость доставки "от"',
                            'xml_path' => $path.'->delivery->option->@priceFrom',
                            'size' => 9
                        ]
                    ),
                    'priceTo' => new SchemaFields\IntegerField(
                        $name.'__delivery__option__@priceTo', [
                            'required' => false,
                            'title' => 'Стоимость доставки "до"',
                            'xml_path' => $path.'->delivery->option->@priceTo',
                            'size' => 9
                        ]
                    ),
                    'currencyCode' => new SchemaFields\ReferenceField(
                        $name.'__delivery__option__@currencyCode', [
                            'required' => true,
                            'title' => 'Символьный код валюты стоимости',
                            'xml_path' => $path.'->delivery->option->@currencyCode',
                            'class' => CurrencyTable::class
                        ]
                    ),
                    'daysFrom' => new SchemaFields\IntegerField(
                        $name.'__delivery__option__@daysFrom', [
                            'required' => true,
                            'title' => 'Сроки доставки "от" в днях',
                            'xml_path' => $path.'->delivery->option->@daysFrom',
                            'size' => 2,
                        ]
                    ),
                    'daysTo' => new SchemaFields\IntegerField(
                        $name.'__delivery__option__@daysTo', [
                            'required' => false,
                            'title' => 'Сроки доставки "до" в днях',
                            'xml_path' => $path.'->delivery->option->@daysTo',
                            'size' => 2,
                        ]
                    ),
                    'orderBefore' => new SchemaFields\IntegerField(
                        $name.'__delivery__option__@orderBefore', [
                            'required' => false,
                            'title' => 'Временные рамки "сделать заказ до N часов", что бы вариант доставки был актуален',
                            'xml_path' => $path.'->delivery->option->@orderBefore',
                            'size' => 2,
                        ]
                    ),
                    'orderAfter' => new SchemaFields\IntegerField(
                        $name.'__delivery__option__@orderAfter', [
                            'required' => false,
                            'title' => 'Временные рамки "сделать заказ после N часов", что бы вариант доставки был актуален',
                            'xml_path' => $path.'->delivery->option->@orderAfter',
                            'size' => 2,
                        ]
                    ),
                    'deliveryRegion' => new SchemaFields\EnumField(
                        $name.'__delivery__option__@deliveryRegion', [
                            'required' => true,
                            'title' => 'Признак региона, на которое распространяется правило',
                            'xml_path' => $path.'->delivery->option->@deliveryRegion',
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

    private static function getSchemaPickup($name, $path)
    {
        return [
            '@attr' => [
                'available' => new SchemaFields\BooleanField(
                    $name.'__pickup__@available', [
                        'required' => true,
                        'title' => 'Имеется ли возможность самовывоза из магазина или со склада',
                        'xml_path' => $path.'->pickup->@available',
                    ]
                )
            ],
            'option' => [
                '@attr' => [
                    'price' => new SchemaFields\StringField(
                        $name.'__pickup__option__@price', [
                            'required' => true,
                            'title' => 'Стоимость самовывоза',
                            'xml_path' => $path.'->pickup->option->@price',
                        ]
                    ),
                    'currencyCode' => new SchemaFields\ReferenceField(
                        $name.'__pickup__option__@currencyCode', [
                            'required' => true,
                            'title' => 'Символьный код валюты стоимости',
                            'xml_path' => $path.'->pickup->option->@currencyCode',
                            'class' => CurrencyTable::class
                        ]
                    ),
                    'supplyFrom' => new SchemaFields\IntegerField(
                        $name.'__pickup__option__@supplyFrom', [
                            'required' => true,
                            'title' => 'Сроки поступления товара в магазин/на склад "от" в днях',
                            'xml_path' => $path.'->pickup->option->@supplyFrom',
                            'size' => 2
                        ]
                    ),
                    'supplyTo' => new SchemaFields\IntegerField(
                        $name.'__pickup__option__@supplyTo', [
                            'required' => true,
                            'title' => 'Сроки поступления товара в магазин/на склад "до" в днях',
                            'xml_path' => $path.'->pickup->option->@supplyTo',
                            'size' => 2
                        ]
                    ),
                    'orderBefore' => new SchemaFields\StringField(
                        $name.'__pickup__option__@orderBefore', [
                            'required' => false,
                            'title' => 'Временные рамки "сделать заказ до N часов", что бы вариант самовывоза был актуален',
                            'xml_path' => $path.'->pickup->option->@orderBefore',
                            'size' => 2
                        ]
                    ),
                    'orderAfter' => new SchemaFields\StringField(
                        $name.'__pickup__@orderAfter', [
                            'required' => false,
                            'title' => 'Временные рамки "сделать заказ после N часов", что бы вариант самовывоза был актуален',
                            'xml_path' => $path.'->pickup->option->@orderAfter',
                            'size' => 2
                        ]
                    )
                ]
            ]
        ];
    }

    public function getXmlExample()
    {
        return file_get_contents(__DIR__.'/example.xml');
    }
}