<?php

namespace Local\Core\Inner\TradingPlatform\Handler\YandexMarket;

use Local\Core\Inner\Fields;
use Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'yandex_market';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Яндекс маркет';
    }

    /** @inheritDoc */
    protected function getHandlerFields()
    {
        return $this->getShopBaseFields() + $this->getDefaultDeliveryFields() + $this->getDefaultPickupFields() + $this->getOfferFields();
    }

    private function getShopBaseFields()
    {
        return [
            '#header_y1' => (new Field\Header())->setValue('Настройки магазина'),

            'shop__name' => (new Field\InputText())->setTitle('Короткое название магазина')
                ->setDescription('Не более 20 символов.')
                ->setName('HANDLER_RULES[shop][name]')
                ->setIsRequired()
                ->setPlaceholder('Рога и копыта')
                ->setValue($this->getHandlerRules()['shop']['name']),

            'shop__company' => (new Field\InputText())->setTitle('Полное наименование компании, владеющей магазином')
                ->setDescription('Не публикуется, используется для внутренней идентификации.')
                ->setName('HANDLER_RULES[shop][company]')
                ->setIsRequired()
                ->setPlaceholder('ООО Рога и копыта')
                ->setValue($this->getHandlerRules()['shop']['company']),

            'shop__url' => (new Field\InputText())->setTitle('URL главной страницы магазина')
                ->setDescription('Максимум 50 символов. Допускаются кириллические ссылки.')
                ->setName('HANDLER_RULES[shop][url]')
                ->setIsRequired()
                ->setPlaceholder('https://example.com')
                ->setValue($this->getHandlerRules()['shop']['url']),

            'shop__platform' => (new Field\InputText())->setTitle('Система управления контентом, на основе которой работает магазин (CMS)')
                ->setName('HANDLER_RULES[shop][platform]')
                ->setPlaceholder('1C-Bitrix')
                ->setValue($this->getHandlerRules()['shop']['platform']),

            'shop__version' => (new Field\InputText())->setTitle('Версия CMS')
                ->setName('HANDLER_RULES[shop][version]')
                ->setPlaceholder('17')
                ->setValue($this->getHandlerRules()['shop']['version']),

            'shop__agency' => (new Field\InputText())->setTitle('Наименование агентства, которое оказывает техническую поддержку магазину и отвечает за работоспособность сайта.')
                ->setName('HANDLER_RULES[shop][agency]')
                ->setValue($this->getHandlerRules()['shop']['agency']),

            'shop__email' => (new Field\InputText())->setTitle('Контактный адрес разработчиков CMS или агентства, осуществляющего техподдержку')
                ->setName('HANDLER_RULES[shop][email]')
                ->setPlaceholder('info@example.com')
                ->setValue($this->getHandlerRules()['shop']['email'])
        ];
    }

    private function getDefaultDeliveryFields()
    {
        $arDeliveryFields = [];

        $arDeliveryFields['#header_y2'] = (new Field\Header())->setValue('Условия доставки');

        $arDeliveryFields['shop__offers__offer__@delivery_data_source'] = (new Field\Select())->setTitle('Источник данных для доставки курьером')
            ->setName('HANDLER_RULES[shop][offers][offer][@delivery_data_source]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@delivery_data_source'] ?? 'ROBOFEED')
            ->setIsRequired()
            ->setOptions([
                'ROBOFEED' => 'Использовать данные, загруженные в Robofeed XML',
                'CUSTOM' => 'Заполню самостоятельно'
            ])
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);

        if ($this->getHandlerRules()['shop']['offers']['offer']['@delivery_data_source'] == 'CUSTOM') {

            $arDeliveryFields['shop__offers__offer__delivery'] = (new Field\Select())->setTitle('Возможность курьерской доставки')
                ->setName('HANDLER_RULES[shop][offers][offer][delivery]')
                ->setIsRequired()
                ->setOptions([
                    'Y' => 'Да',
                    'N' => 'Нет'
                ])
                ->setEvent([
                    'onchange' => [
                        'PersonalTradingplatformFormComponent.refreshForm()'
                    ]
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery'] ?? 'Y');


            if ($this->getHandlerRules()['shop']['offers']['offer']['delivery'] == 'Y' || empty($this->getHandlerRules()['shop']['offers']['offer']['delivery'])) {
                $arDeliveryFields['shop__offers__offer__delivery-options__option__@attr__cost'] = (new Field\Resource())->setTitle('Стоимость доставки')
                    ->setDescription('В качестве значения используйте только целые числа. Для бесплатной доставки укажите значение 0.')
                    ->setName('HANDLER_RULES[shop][offers][offer][delivery-options][option][@attr][cost]')
                    ->setIsRequired()
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_LOGIC,
                    ])
                    ->setSimpleField((new Field\InputText()))
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery-options']['option']['@attr']['cost'] ??
                               ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => 0]);

                $arDeliveryFields['shop__offers__offer__delivery-options__option__@attr__days'] = (new Field\Resource())->setTitle('Срок доставки в рабочих днях')
                    ->setDescription('Можно указать как конкретное количество дней, так и период «от — до». Например, срок доставки от 2 до 4 дней опишите так: 2-4. <i>При указании периода «от — до» интервал срока доставки должен составлять <b>не более двух</b> дней.</i>')
                    ->setName('HANDLER_RULES[shop][offers][offer][delivery-options][option][@attr][days]')
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_IGNORE,
                    ])
                    ->setSimpleField((new Field\InputText()))
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery-options']['option']['@attr']['days'] ??
                               ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => '1-3'])
                    ->setEpilog((new Field\Infoblock())->setValue('<b>Неопределенный срок доставки (товары «на заказ»).</b><br/>
Если точный срок доставки неизвестен, используйте в атрибуте days значение 32 или больше (либо выберите значение <b>"Игнорировать поле"</b>). Для таких товаров на Маркете будет показана надпись «на заказ».
<br/>
Внимание. Магазин должен доставить товары «на заказ» в срок до двух месяцев. Точный срок необходимо согласовать с покупателем.'));

                $arDeliveryFields['shop__offers__offer__delivery-options__option__@attr__order-before'] = (new Field\Resource())->setTitle('Время, до которого нужно сделать заказ, чтобы получить его в этот срок')
                    ->setDescription('В качестве значения используйте только целое число от 0 до 24.<br/>
Если значение не указано или проигнорировано, ЯндекМаркет использует значение по умолчанию — 13.')
                    ->setName('HANDLER_RULES[shop][offers][offer][delivery-options][option][@attr][order-before]')
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_IGNORE,
                    ])
                    ->setSimpleField((new Field\InputText()))
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery-options']['option']['@attr']['order-before'] ?? ['TYPE' => Field\Resource::TYPE_IGNORE]);
            }
        }

        return $arDeliveryFields;
    }

    private function getDefaultPickupFields()
    {
        $arDeliveryFields = [];

        $arDeliveryFields['#header_y3'] = (new Field\Header())->setValue('Условия самовывоза');

        $arDeliveryFields['shop__offers__offer__@pickup_data_source'] = (new Field\Select())->setTitle('Источник данных для самовывоза')
            ->setName('HANDLER_RULES[shop][offers][offer][@pickup_data_source]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@pickup_data_source'] ?? 'ROBOFEED')
            ->setIsRequired()
            ->setOptions([
                'ROBOFEED' => 'Использовать данные, загруженные в Robofeed XML',
                'CUSTOM' => 'Заполню самостоятельно'
            ])
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);

        if ($this->getHandlerRules()['shop']['offers']['offer']['@pickup_data_source'] == 'CUSTOM') {
            $arDeliveryFields['shop__offers__offer__pickup'] = (new Field\Select())->setTitle('Возможность самовывоза')
                ->setName('HANDLER_RULES[shop][offers][offer][pickup]')
                ->setIsRequired()
                ->setOptions([
                    'Y' => 'Да',
                    'N' => 'Нет'
                ])
                ->setEvent([
                    'onchange' => [
                        'PersonalTradingplatformFormComponent.refreshForm()'
                    ]
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['pickup'] ?? 'Y');

            if ($this->getHandlerRules()['shop']['offers']['offer']['pickup'] == 'Y' || empty($this->getHandlerRules()['shop']['offers']['offer']['pickup'])) {
                $arDeliveryFields['shop__offers__offer__pickup-options__option__@attr__cost'] = (new Field\Resource())->setTitle('Стоимость самовывоза')
                    ->setDescription('В качестве значения используйте только целые числа. Для бесплатной доставки укажите значение 0')
                    ->setName('HANDLER_RULES[shop][offers][offer][pickup-options][option][@attr][cost]')
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setIsRequired()
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_LOGIC,
                    ])
                    ->setSimpleField((new Field\InputText()))
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery-options']['option']['@attr']['cost'] ??
                               ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => 0]);

                $arDeliveryFields['shop__offers__offer__pickup-options__option__@attr__days'] = (new Field\Resource())->setTitle('Срок поставки товара в пункт выдачи (магазин/склад) в рабочих днях')
                    ->setDescription('Если товар можно получить в пункте выдачи в день заказа (сегодня), используйте значение 0. Можно указать как конкретное количество дней, так и период «от — до». Например, срок доставки от 2 до 4 дней опишите так: 2-4. <i>При указании периода «от — до» интервал срока доставки должен составлять не более трех дней.</i>')
                    ->setName('HANDLER_RULES[shop][offers][offer][pickup-options][option][@attr][days]')
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_IGNORE,
                    ])
                    ->setSimpleField((new Field\InputText()))
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['pickup-options']['option']['@attr']['days'] ??
                               ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => '1-3'])
                    ->setEpilog((new Field\Infoblock())->setValue('<b>Неопределенный срок (товары «на заказ»).</b><br/>
Если точный срок поставки товара в пункт выдачи неизвестен, используйте значение 32 или больше (либо выберите значение <b>"Игнорировать поле"</b>). Для таких товаров на Маркете будет показана надпись «на заказ».'));

                $arDeliveryFields['shop__offers__offer__pickup-options__option__@attr__order-before'] = (new Field\Resource())->setTitle('Время, до которого нужно сделать заказ, чтобы получить товар в пункте выдачи в указанный срок')
                    ->setDescription('В качестве значения используйте только целое число от 0 до 24')
                    ->setName('HANDLER_RULES[shop][offers][offer][pickup-options][option][@attr][order-before]')
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_IGNORE,
                    ])
                    ->setSimpleField((new Field\InputText()))
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['pickup-options']['option']['@attr']['order-before'] ?? ['TYPE' => Field\Resource::TYPE_IGNORE]);
            }
        }

        return $arDeliveryFields;
    }

    private function getOfferFields()
    {
        $arFields = [
            '#header_y4' => (new Field\Header())->setValue('Торговые предложения')
        ];

        $arFields['shop__offers__offer__@offer_data_source'] = (new Field\Select())->setTitle('Заполнение данных')
            ->setName('HANDLER_RULES[shop][offers][offer][@offer_data_source]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@offer_data_source'] ?? 'ROBOFEED')
            ->setIsRequired()
            ->setOptions([
                'ROBOFEED' => 'Автоматческое заполнение на основании Robofeed XML',
                'CUSTOM' => 'Заполню самостоятельно'
            ])
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);

        if ($this->getHandlerRules()['shop']['offers']['offer']['@offer_data_source'] == 'CUSTOM') {

            $arFields['shop__offers__offer__@attr__id'] = (new Field\Resource())->setTitle('Идентификатор предложения')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setDescription('Полное название предложения, в которое входит: тип товара, производитель, модель и название товара, важные характеристики.')
                ->setName('HANDLER_RULES[shop][offers][offer][@attr][id]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['id'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['id'] ??
                           ['TYPE' => Field\Resource::TYPE_SOURCE, Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID']);

            $arFields['shop__offers__offer__@attr__group_id'] = (new Field\Resource())->setTitle('Идентификатор группы товара')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setDescription('Элемент объединяет все предложения, которые являются вариациями одной модели и должен иметь одинаковое значение.')
                ->setName('HANDLER_RULES[shop][offers][offer][@attr][group_id]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['group_id'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['group_id'] ?? [
                        'TYPE' => Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_LOGIC.'_VALUE' => [
                            'IF' => [
                                [
                                    'RULE' => [
                                        'CLASS_ID' => 'CondGroup',
                                        'DATA' => [
                                            'All' => 'AND',
                                            'True' => 'True',
                                        ],
                                        'CHILDREN' => [
                                            0 => [
                                                'CLASS_ID' => 'CondProdProductGroupId',
                                                'DATA' => [
                                                    'logic' => 'Great',
                                                    'value' => '0',
                                                ],
                                            ],
                                        ],
                                    ],
                                    'VALUE' => [
                                        'TYPE' => Field\Resource::TYPE_SOURCE,
                                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_GROUP_ID'
                                    ]
                                ]
                            ],
                            'ELSE' => [
                                'VALUE' => [
                                    'TYPE' => Field\Resource::TYPE_IGNORE
                                ]
                            ]
                        ]
                    ]);


            $arFields['shop__offers__offer__@attr__bid'] = (new Field\Resource())->setTitle('Размер ставки')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setDescription('Указывайте размер ставки в условных центах: например, значение 80 соответствует ставке 0,8 у.е. Значения должны быть целыми и положительными числами.')
                ->setName('HANDLER_RULES[shop][offers][offer][@attr][bid]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['bid'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['bid'] ?? ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => 10]);

            $arFields['shop__offers__offer__@attr__available'] = (new Field\Resource())->setTitle('Наличие товара')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][@attr][available]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['available'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setSelectField((new Field\Select())->setOptions([
                    'Y' => 'В наличии',
                    'N' => 'Под заказ',
                ]))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['available'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BOOL_FIELD#IN_STOCK'
                    ]);

            $arFields['shop__offers__offer__name'] = (new Field\Resource())->setTitle('Полное название предложения')
                ->setDescription('Полное название предложения, в которое входит: тип товара, производитель, модель и название товара, важные характеристики.')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][name]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#FULL_NAME'
                    ]);

            $arFields['shop__offers__offer__vendor'] = (new Field\Resource())->setTitle('Название производителя')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][vendor]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vendor'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vendor'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER'
                    ]);

            $arFields['shop__offers__offer__vendorCode'] = (new Field\Resource())->setTitle('Код производителя для данного товара')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][vendorCode]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vendorCode'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vendorCode'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_CODE'
                    ]);

            $arFields['shop__offers__offer__url'] = (new Field\Resource())->setTitle('URL страницы товара на сайте магазина')
                ->setDescription('Максимальная длина ссылки — 512 символов.')
                ->setIsRequired()
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][url]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['url'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['url'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#URL'
                    ]);

            $arFields['shop__offers__offer__price'] = (new Field\Resource())->setTitle('Актуальная цена товара')
                ->setDescription('Если товар продается по весу, метражу и т. п. (не штуками), указывайте цену за вашу единицу продажи. Например, если вы продаете кабель бухтами, указывайте цену за бухту.')
                ->setIsRequired()
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][price]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['price'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['price'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRICE'
                    ]);

            $arFields['shop__offers__offer__oldprice'] = (new Field\Resource())->setTitle('Старая цена товара')
                ->setDescription('Должна быть выше текущей.')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][oldprice]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['oldprice'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['oldprice'] ?? [
                        'TYPE' => Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_LOGIC.'_VALUE' => [
                            'IF' => [
                                [
                                    'RULE' => [
                                        'CLASS_ID' => 'CondGroup',
                                        'DATA' => [
                                            'All' => 'AND',
                                            'True' => 'True',
                                        ],
                                        'CHILDREN' => [
                                            1 => [
                                                'CLASS_ID' => 'CondProdOldPrice',
                                                'DATA' => [
                                                    'logic' => 'Great',
                                                    'value' => 0,
                                                ],
                                            ],
                                        ],
                                    ],
                                    'VALUE' => [
                                        'TYPE' => Field\Resource::TYPE_SOURCE,
                                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#OLD_PRICE'
                                    ]
                                ]
                            ],
                            'ELSE' => [
                                'VALUE' => [
                                    'TYPE' => Field\Resource::TYPE_IGNORE
                                ]
                            ]
                        ]
                    ]);

            $arFields['shop__offers__offer__description'] = (new Field\Resource())->setTitle('Описание предложения')
                ->setDescription('Длина текста не более 3000 символов (включая знаки препинания).')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][description]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['description'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['description'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#DESCRIPTION'
                    ]);

            $arFields['shop__offers__offer__sales_notes'] = (new Field\Resource())->setTitle('Условия продажи товара')
                ->setDescription('Обязателен, если у вас есть ограничения при заказе товара (например минимальная сумма заказа, минимальное количество товаров или необходимость предоплаты).<br/>Допустимая длина текста — 50 символов.')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][sales_notes]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['sales_notes'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['sales_notes'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#SALES_NOTES'
                    ]);

            $arFields['shop__offers__offer__min-quantity'] = (new Field\Resource())->setTitle('Минимальное количество одинаковых товаров в заказе')
                ->setDescription('Для случаев, когда покупка возможна только комплектом, а не поштучно. Используется только в категориях "Автошины", "Грузовые шины", "Мотошины", "Диски".')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][min-quantity]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['min-quantity'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['min-quantity'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MIN_QUANTITY'
                    ]);

            $arFields['shop__offers__offer__manufacturer_warranty'] = (new Field\Resource())->setTitle('Официальная гарантия производителя')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][manufacturer_warranty]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['manufacturer_warranty'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSelectField((new Field\Select())->setOptions([
                    'Y' => 'Товар имеет официальную гарантию производителя',
                    'N' => 'Товар не имеет официальной гарантии производителя',
                ]))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['manufacturer_warranty'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BOOL_FIELD#MANUFACTURER_WARRANTY'
                    ]);


            $strReferencesLink = Route::getRouteTo('development', 'references');
            $arFields['shop__offers__offer__country_of_origin'] = (new Field\Resource())->setTitle('Страна производства товара')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][country_of_origin]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['country_of_origin'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSelectField((new Field\Select())->setOptions($this->_getCountryListToSelect())
                    ->setDefaultOption('-- Выберите страну --'))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['country_of_origin'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'REFERENCE_FIELD#COUNTRY_OF_PRODUCTION_CODE#NAME'
                    ])
                ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Должен быть передан код страны из нашего справочника. В противном случае поле будет проигнорированно.<br/>
Справочник - <a href="https://robofeed.ru$strReferencesLink#country" target="_blank">https://robofeed.ru$strReferencesLink#country</a>
DOCHERE
                ));

            $arFields['shop__offers__offer__adult'] = (new Field\Resource())->setTitle('Товар имеет отношение к удовлетворению сексуальных потребностей, либо иным образом эксплуатирует интерес к сексу')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][adult]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['adult'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSelectField((new Field\Select())->setOptions([
                    'Y' => 'Да',
                    'N' => 'Нет',
                ]))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['adult'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BOOL_FIELD#IS_SEX'
                    ]);

            $arFields['shop__offers__offer__weight'] = (new Field\Resource())->setTitle('Вес товара в килограммах с учетом упаковки')
                ->setDescription('При выборе поля <b>"Вес товара"</b> из <b>Robofeed XML</b> мы автоматически сконвертируем вес.')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][weight]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['weight'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['weight'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#WEIGHT'
                    ]);

            $arFields['shop__offers__offer__dimensions'] = (new Field\Resource())->setTitle('Габариты товара (длина, ширина, высота) в упаковке.')
                ->setDescription('Указывается в сантиметрах. Числа должны быть разделены символом «/» без пробелов.')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setName('HANDLER_RULES[shop][offers][offer][dimensions]')
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['dimensions'])
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['dimensions'] ?? [
                        'TYPE' => Field\Resource::TYPE_LOGIC,
                        Field\Resource::TYPE_LOGIC.'_VALUE' => [
                            'IF' => [
                                [
                                    'RULE' => [
                                        'CLASS_ID' => 'CondGroup',
                                        'DATA' => [
                                            'All' => 'AND',
                                            'True' => 'True',
                                        ],
                                        'CHILDREN' => [
                                            0 => [
                                                'CLASS_ID' => 'CondGroup',
                                                'DATA' => [
                                                    'All' => 'AND',
                                                    'True' => 'True',
                                                ],
                                                'CHILDREN' => [
                                                    1 => [
                                                        'CLASS_ID' => 'CondProdWidth',
                                                        'DATA' => [
                                                            'logic' => 'Great',
                                                            'value' => 0,
                                                        ],
                                                    ],
                                                    2 => [
                                                        'CLASS_ID' => 'CondProdHeight',
                                                        'DATA' => [
                                                            'logic' => 'Great',
                                                            'value' => 0,
                                                        ],
                                                    ],
                                                    3 => [
                                                        'CLASS_ID' => 'CondProdLength',
                                                        'DATA' => [
                                                            'logic' => 'Great',
                                                            'value' => 0,
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    'VALUE' => [
                                        'TYPE' => Field\Resource::TYPE_BUILDER,
                                        Field\Resource::TYPE_BUILDER.'_VALUE' => '{{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}}'
                                    ]
                                ]
                            ],
                            'ELSE' => [
                                'VALUE' => [
                                    'TYPE' => Field\Resource::TYPE_IGNORE
                                ]
                            ]
                        ]

                    ])
                ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Если Вы передаете нам габариты во всех товарах, то мы рекомендуем установить тип данных <b>"Сложное значение"</b> и выставить ему значение:<br/>
<b>{{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}}</b><br/>
Имея это значение мы автоматически приведет габариты в сантиметры, исходя из указанных Вами единиц измерений.<br/>
<br/>
В противном случае Вы можете указать тип <b>"Простое значение"</b> или <b>"Сложное условие"</b> и указать усредненные габариты товаров.
DOCHERE
                ));

        }


        $arFields['#header_y5'] = (new Field\Header())->setValue('Дополнительные поля товара');

        $arFields['shop__offers__offer__picture'] = (new Field\Resource())->setTitle('URL-ссылка на картинку товара')
            ->setDescription(<<<HEREDOC
<b>* Ссылка на изображение обязательна для категорий:</b><br/>
"Мягкая мебель", 
"Чехлы для мобильных телефонов", 
"Компьютерные столы", 
"Защитные пленки и наклейки для телефонов", 
"Массажные столы", 
"Зарядные устройства для телефонов", 
"Одежда, обувь и аксессуары", 
"Переходники для мобильных телефонов", 
"Косметика и парфюмерия", 
"Сумки и чехлы для планшетов"
HEREDOC
            )
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][picture]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['picture'])
            ->setIsMultiple()
            ->setSize(7)
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['picture'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => [
                        'BASE_FIELD#IMAGE'
                    ]
                ]);

        $arFields['shop__offers__offer__store'] = (new Field\Resource())->setTitle('Возможность купить товар без предварительного заказа')
            ->setDescription('При выбранном значении <b>"Игнорировать поле"</b> значение не будет передано. Если в личном кабинете ЯндексМаркета указана соответствующая точка продаж (торговый зал, пункт выдачи), то он автоматически воспримет покупку как возможную.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][store]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['store'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                'Y' => 'Товар можно купить без предварительного заказа',
                'N' => 'Товар нельзя купить без предварительного заказа'
            ]))
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['store'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ]);

        $arFields['shop__offers__offer__barcode'] = (new Field\Resource())->setTitle('Штрихкод товара от производителя')
            ->setDescription('Допустимые форматы: EAN-13, EAN-8, UPC-A, UPC-E.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][barcode]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['barcode'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['barcode'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ]);

        $arFields['shop__offers__offer__downloadable'] = (new Field\Resource())->setTitle('Продукт можно скачать')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][downloadable]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['downloadable'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                'Y' => 'Да',
                'N' => 'Нет',
            ]))
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['downloadable'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BOOL_FIELD#IS_SOFTWARE'
                ]);

        $arFields['shop__offers__offer__age__@attr__year'] = (new Field\Resource())->setTitle('Возрастная категория товара (лет)')
            ->setDescription('Допустимые значения: 0, 6, 12, 16, 18')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][age][@attr][year]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['age']['@attr']['year'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                '0' => 'От 0 лет',
                '6' => 'От 6 лет',
                '12' => 'От 12 лет',
                '16' => 'От 16 лет',
                '18' => 'От 18 лет',
            ]))
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['age']['@attr']['year'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ]);

        return $arFields;
    }

    private function _getCountryListToSelect()
    {
        $arCountries = [];
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        if (
        $obCache->startDataCache(60 * 60 * 24 * 7, __METHOD__.__LINE__, \Local\Core\Inner\Cache::getCachePath(['Model', 'Reference', 'CountryTable'], ['GetCountryListToYandexMarketHandler']))
        ) {
            $rs = \Local\Core\Model\Reference\CountryTable::getList([
                'order' => ['NAME' => 'ASC'],
                'select' => ['NAME', 'CODE']
            ]);
            if ($rs->getSelectedRowsCount() < 1) {
                $obCache->abortDataCache();
            } else {
                while ($ar = $rs->fetch()) {
                    $arCountries[$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
                $obCache->endDataCache($arCountries);
            }
        } else {
            $arCountries = $obCache->getVars();
        }

        return $arCountries;
    }
}