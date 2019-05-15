<?php

namespace Local\Core\Inner\TradingPlatform\Handler\Beru;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'beru';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Беру!';
    }

    /** @inheritDoc */
    public static function getMainCurrency()
    {
        return 'RUB';
    }

    /** @inheritDoc */
    public static function getSupportedCurrency()
    {
        return [
            'RUB',
            'BYN',
            'UAH',
            'KZT',
            'EUR',
            'USD'
        ];
    }

    /** @inheritDoc */
    protected function getHandlerFields()
    {
        return $this->getShopBaseFields() + $this->getDefaultDeliveryFields() + $this->getDefaultPickupFields() + $this->getOfferFields();
    }

    protected function getShopBaseFields()
    {
        $arShopFields = [
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
        ];


        $arShopFields['#header_y1.1'] = (new Field\Header())->setValue('Условия доставки магазина по умолчанию');

        $arShopFields['shop__@delivery'] = (new Field\Select())->setTitle('Имеется ли у магазина курьерская доставка?')
            ->setDescription('Если выбрать значение "Нет", то возможности передавать значения доставки в товары будет нельзя.')
            ->setName('HANDLER_RULES[shop][@delivery]')
            ->setValue($this->getHandlerRules()['shop']['@delivery'] ??  'Y')
            ->setIsRequired()
            ->setOptions([
                'Y' => 'Да',
                'N' => 'Нет'
            ])
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);

        if( ( $this->getHandlerRules()['shop']['@delivery'] ?? 'Y' ) == 'Y' )
        {
            $arShopFields['#header_y1.1_info'] = (new Field\Infoblock())->setValue(<<<DOCHERE
<i class="font-weight-bold">Беру!</i> требует указывать условия доставки магазина по умолчанию вне зависимости, указаны ли условия у каждого товара или нет. Эти условия распространяются на все предложения магазина, кроме предложений с индивидуальными условиями доставки.<br/>
Этот блок работает отдельно от блока <b>"Условия доставки товара"</b>.
DOCHERE
);

            $arShopFields['shop__delivery-options__option__@attr__cost'] = (new Field\InputText())->setTitle('Стоимость доставки по умолчанию')
                ->setDescription('В качестве значения используйте только целые числа. Для бесплатной доставки укажите значение 0.')
                ->setName('HANDLER_RULES[shop][delivery-options][option][@attr][cost]')
                ->setIsRequired()
                ->setValue($this->getHandlerRules()['shop']['delivery-options']['option']['@attr']['cost']);

            $arShopFields['shop__delivery-options__option__@attr__days'] = (new Field\Resource())->setTitle('Срок доставки в рабочих днях по умолчанию')
                ->setDescription('Можно указать как конкретное количество дней, так и период «от — до». Например, срок доставки от 2 до 4 дней опишите так: 2-4. <i>При указании периода «от — до» интервал срока доставки должен составлять <b>не более двух</b> дней.</i>')
                ->setName('HANDLER_RULES[shop][delivery-options][option][@attr][days]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['delivery-options']['option']['@attr']['days'] ?? [
                        'TYPE' => Field\Resource::TYPE_SIMPLE,
                        Field\Resource::TYPE_SIMPLE.'_VALUE' => '1-3'
                    ])
                ->setEpilog((new Field\Infoblock())->setValue('<b>Неопределенный срок доставки (товары «на заказ»).</b><br/>
Если точный срок доставки неизвестен, используйте значение 32 или больше (либо выберите значение <b>"Игнорировать поле"</b>). Для таких товаров на Маркете будет показана надпись «на заказ».
<br/>
Внимание. Магазин должен доставить товары «на заказ» в срок до двух месяцев. Точный срок необходимо согласовать с покупателем.'));

            $arShopFields['shop__delivery-options__option__@attr__order-before'] = (new Field\Resource())->setTitle('Время, до которого нужно сделать заказ, чтобы получить его в этот срок, по умолчанию')
                ->setDescription('В качестве значения используйте только целое число от 0 до 24.<br/>
Если значение не указано или проигнорировано, <i class="font-weight-bold">Беру!</i> использует значение по умолчанию — 13.')
                ->setName('HANDLER_RULES[shop][delivery-options][option][@attr][order-before]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_IGNORE,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['delivery-options']['option']['@attr']['order-before'] ?? ['TYPE' => Field\Resource::TYPE_IGNORE]);
        }

        return $arShopFields;
    }

    protected function getDefaultDeliveryFields()
    {
        $arDeliveryFields = [];

        if( ( $this->getHandlerRules()['shop']['@delivery'] ?? 'N' ) == 'Y' )
        {

            $arDeliveryFields['#header_y2'] = (new Field\Header())->setValue('Условия доставки товара');

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

                $arDeliveryFields['shop__offers__offer__delivery'] = (new Field\Resource())->setTitle('Возможность курьерской доставки')
                    ->setName('HANDLER_RULES[shop][offers][offer][delivery]')
                    ->setIsRequired()
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SELECT,
                        Field\Resource::TYPE_LOGIC,
                    ])
                    ->setSelectField(
                        (new Field\Select())
                            ->setOptions([
                                'Y' => 'Да',
                                'N' => 'Нет'
                            ])
                    )
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery'] ?? [
                            'TYPE' => Field\Resource::TYPE_SELECT,
                            Field\Resource::TYPE_SELECT.'_VALUE' => 'Y'
                        ]);

                $arDeliveryFields['shop__offers__offer__delivery-options__option__@info'] = (new Field\Infoblock())->setValue(<<<DOCHERE
Условия, описанные ниже, будут применены к товарам, которые будут иметь признак возможности курьерской доставки.
DOCHERE
);
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
                               ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => null]);

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
Если точный срок доставки неизвестен, используйте значение 32 или больше (либо выберите значение <b>"Игнорировать поле"</b>). Для таких товаров на Маркете будет показана надпись «на заказ».
<br/>
Внимание. Магазин должен доставить товары «на заказ» в срок до двух месяцев. Точный срок необходимо согласовать с покупателем.'));

                $arDeliveryFields['shop__offers__offer__delivery-options__option__@attr__order-before'] = (new Field\Resource())->setTitle('Время, до которого нужно сделать заказ, чтобы получить его в этот срок')
                    ->setDescription('В качестве значения используйте только целое число от 0 до 24.<br/>
Если значение не указано или проигнорировано, <i class="font-weight-bold">Беру!</i> использует значение по умолчанию — 13.')
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

    protected function getDefaultPickupFields()
    {
        $arDeliveryFields = [];

        $arDeliveryFields['#header_y3'] = (new Field\Header())->setValue('Условия самовывоза товара');

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
                    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['pickup-options']['option']['@attr']['cost'] ??
                               ['TYPE' => Field\Resource::TYPE_SIMPLE, Field\Resource::TYPE_SIMPLE.'_VALUE' => null]);

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

    protected static $arParamsListCache;

    protected function getOfferFields()
    {
        $arFields = [
            '#header_y4' => (new Field\Header())->setValue('Торговые предложения')
        ];

        $arFields['shop__offers__offer__@offer_data_source'] = (new Field\Select())->setTitle('Заполнение данных')
            ->setName('HANDLER_RULES[shop][offers][offer][@offer_data_source]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@offer_data_source'] ?? 'ROBOFEED')
            ->setIsRequired()
            ->setOptions([
                'ROBOFEED' => 'Автоматическое заполнение на основе данных из Robofeed XML',
                'CUSTOM' => 'Заполню самостоятельно'
            ])
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);

        if ($this->getHandlerRules()['shop']['offers']['offer']['@offer_data_source'] == 'CUSTOM') {
            $arFields = array_merge($arFields, $this->getOfferDefaultFields());
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
            ->setSize(7)
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['picture'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IMAGE'
                ]);

        $arFields['shop__offers__offer__store'] = (new Field\Resource())->setTitle('Возможность купить товар без предварительного заказа')
            ->setDescription('При выбранном значении <b>"Игнорировать поле"</b> значение не будет передано. Если в личном кабинете <i class="font-weight-bold">Беру!</i> указана соответствующая точка продаж (торговый зал, пункт выдачи), то он автоматически воспримет покупку как возможную.')
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
                    'TYPE' => Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_SELECT.'_VALUE' => 'Y'
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
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IS_SOFTWARE'
                ]);

        $arFields['shop__offers__offer__age__@attr__year'] = (new Field\Resource())->setTitle('Возрастная категория товара (лет)')
            ->setDescription('Допустимые значения: 0, 6, 12, 16 или 18')
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

        if (is_null(self::$arParamsListCache[$this->getTradingPlatformStoreId()])) {
            $rsProductProps = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'select' => ['CODE', 'NAME'],
                    'group' => ['CODE'],
                    'order' => ['NAME' => 'ASC']
                ]);
            if( $rsProductProps->getSelectedRowsCount() > 0 )
            {
                while ($ar = $rsProductProps->fetch()) {
                    self::$arParamsListCache[$this->getTradingPlatformStoreId()][$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
            }
            else
            {
                self::$arParamsListCache[$this->getTradingPlatformStoreId()] = [];
            }
        }

        $arFields['shop__offers__offer__param'] = (new Field\Select())->setTitle('Характеристики товара')
            ->setDescription('Выберите характеристики, которые необходимо передавать в <i class="font-weight-bold">Беру!</i>.')
            ->setName('HANDLER_RULES[shop][offers][offer][param]')
            ->setIsMultiple()
            ->setOptions(['#ALL' => 'Передавать все характеристики'] + self::$arParamsListCache[$this->getTradingPlatformStoreId()])
            ->setDefaultOption('-- Выберите параметры --')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['param'] ?? ['#ALL']);


        $arFields['shop__offers__offer__vat'] = (new Field\Resource())->setTitle('Значение ставки НДС для товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][vat]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vat'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setSelectField(
                (new Field\Select())->setOptions([
                    'NO_VAT' => 'НДС не облагается',
                    'VAT_0' => '0%',
                    'VAT_10' => '10%',
                    'VAT_20' => '20%',
                ])->setDefaultOption('-- Выберите ставку --')
            )
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vat'] ?? [
                    'TYPE' => Field\Resource::TYPE_SELECT
                ]);

        $arFields['shop__offers__offer__disabled'] = (new Field\Resource())->setTitle('Доступность товара для продажи')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][disabled]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['disabled'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField(
                (new Field\Select())->setOptions([
                    'true' => 'Приостановить продажу товара',
                    'false' => 'Товар доступен для продажи',
                ])->setDefaultOption('-- Выберите ставку --')
            )
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['disabled'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ])
            ->setEpilog(
                (new Field\Infoblock())->setValue(
                    <<<DOCHERE
Значение данного поля по смыслу отличается от поля <b>"Наличие товара"</b>. Поле <b>"Наличие товара"</b> позволяет регулировать метки "В наличии" и "Под заказ", в то время как данное поле позволяет остановить продажу товара на сервисе <i class="font-weight-bold">Беру!</i> (не отображать предложение) в случае какой либо необходимости.<br/>
<br/>
В случае игнорирования поля <i class="font-weight-bold">Беру!</i> будет воспринимать товар как доступный к продаже.
DOCHERE
                )
            );

        return $arFields;
    }

    protected function getOfferDefaultFields()
    {
        $arFields = [];
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
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['id'] ?? ['TYPE' => Field\Resource::TYPE_SOURCE, Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID']);

        $arFields['shop__offers__offer__shop-sku'] = (new Field\Resource())->setTitle('Ваш идентификатор товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][shop-sku]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['shop-sku'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['shop-sku'] ?? ['TYPE' => Field\Resource::TYPE_SOURCE, Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID']);

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
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IN_STOCK'
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
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#DETAIL_URL_WITH_UTM'
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

        $strRouteReferenceCurrency = Route::getRouteTo('development', 'references').'#currency';
        $arFields['shop__offers__offer__currencyId'] = (new Field\Resource())->setTitle('Валюта, в которой указана цена товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][currencyId]')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['currencyId'])
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['currencyId'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#CURRENCY_CODE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue('Должен быть передан <b>код валюты из справочника валют</b>.<br/>Справочник - <a href="'.$strRouteReferenceCurrency
                                                          .'" target="_blank">https://robofeed.ru'.$strRouteReferenceCurrency.'</a>'));

        $arFields['shop__offers__offer__oldprice'] = (new Field\Resource())->setTitle('Старая цена товара')
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
                ])
            ->setEpilog(
                (new Field\Infoblock())
                    ->setValue(
                        <<<DOCHERE
<blockquote class="blockquote border-warning mb-0">
Более высокая цена товара, по которой он продавался раньше. Используется для расчета скидки и отображается зачеркнутой. Требования:
<ol>
    <li>Старая цена должна быть выше текущей.</li>
    <li>
        Скидка (разница между старой и текущей ценой) должна быть:
        <ul>
            <li>не меньше 5% и не больше 95% или</li>
            <li>не меньше 500 рублей (но минимум 1%) и не больше 95%. Процент округляется до целого числа.</li>
        </ul>
    </li>
    <li>Товар продавался по старой цене (или дороже) как минимум 5 дней подряд за последние:
        <ul>
            <li>60 дней — для детской одежды;</li>
            <li>30 дней — для остальных товаров.</li>
        </ul>
    </li>
</ol>
<div class="blockquote-footer">
 Беру!
</div>
</blockquote>
DOCHERE

                    )
            );

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
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_WARRANTY'
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
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#COUNTRY_OF_PRODUCTION_CODE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Должен быть передан <b>код страны из нашего справочника</b>. В противном случае поле будет проигнорированно.<br/>
Справочник - <a href="$strReferencesLink#country" target="_blank">https://robofeed.ru$strReferencesLink#country</a>
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
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IS_SEX'
                ]);

        $arFields['shop__offers__offer__weight'] = (new Field\Resource())->setTitle('Вес товара в килограммах с учетом упаковки')
            ->setDescription('При выборе поля <b>"Вес товара"</b> из <b>Robofeed XML</b> мы автоматически сконвертируем вес.')
            ->setIsRequired()
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

        $arFields['shop__offers__offer__dimensions'] = (new Field\Resource())->setTitle('Габариты товара (длина, ширина, высота) в упаковке')
            ->setDescription('Указывается в сантиметрах. Числа должны быть разделены символом «/» без пробелов.')
            ->setIsRequired()
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
                    'VALUE' => [
                        'TYPE' => Field\Resource::TYPE_BUILDER,
                        Field\Resource::TYPE_BUILDER.'_VALUE' => '{{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}}'
                    ]
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Мы рекомендуем установить тип данных <b>"Сложное значение"</b> и выставить ему значение:<br/>
<b>{{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}}</b><br/>
Имея это значение мы автоматически приведет габариты в сантиметры, исходя из указанных Вами единиц измерений. Если значения габаритов не были указаны в товаре в Robofeed XML - значения будут взяты из значений блока <b>"Общие настройки обработки"</b>.<br/>
<br/>
Вы также можете указать тип <b>"Простое значение"</b> и указать усредненные габариты товаров в сантиметрах.
DOCHERE
            ));

        return $arFields;
    }

    protected function _getCountryListToSelect()
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


    /** *****
     * EXPORT
     ****** */

    /** @inheritDoc */
    public function getExportFileFormat()
    {
        return 'xml';
    }

    /** @inheritDoc */
    protected function executeMakeExportFile(\Bitrix\Main\Result $obResult)
    {
        try {
            $this->addToTmpExportFile('<?xml version="1.0" encoding="UTF-8"?><yml_catalog date="'.date('Y-m-d H:i').'"><shop>');

            $this->fillShopHeader($obResult);
            $this->fillCurrencies($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->fillCategories($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->fillShopDeliveryDefault($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->addToTmpExportFile('<offers>');
            $this->beginFilterProduct($obResult);
            $this->addToTmpExportFile('</offers>');

            $this->addToTmpExportFile('</shop></yml_catalog>');

        } catch (\Throwable $e) {
            if (!empty($e->getMessage())) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
    }

    protected function fillShopHeader(\Bitrix\Main\Result $obResult)
    {
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__name']))) {
            $this->addToTmpExportFile('<name>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__name'])).'</name>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__company']))) {
            $this->addToTmpExportFile('<company>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__company'])).'</company>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__url']))) {
            $this->addToTmpExportFile('<url>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__url'])).'</url>');
        }
    }

    protected function fillShopDeliveryDefault(\Bitrix\Main\Result $obResult){
        if( $this->getHandlerRules()['shop']['@delivery'] == 'Y' )
        {
            $this->addToTmpExportFile('<delivery-options>');

            $strDeliveryOption = '<option cost="'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__cost'])).'"';
            if( !is_null($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__days'])) )
            {
                $strDeliveryOption .= ' days="'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__days'])).'"';
            }
            if( !is_null($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__order-before'])) )
            {
                $strDeliveryOption .= ' order-before="'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__order-before'])).'"';
            }
            $strDeliveryOption .= ' />';
            $this->addToTmpExportFile($strDeliveryOption);

            $this->addToTmpExportFile('</delivery-options>');
        }
    }

    protected function fillCurrencies(\Bitrix\Main\Result $obResult)
    {
        $this->addToTmpExportFile('<currencies>');
        if ($this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']) == 'NOT_CONVERT') {
            $rsCurrencies = \Local\Core\Model\Robofeed\StoreProductFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'select' => ['CURRENCY_CODE'],
                    'group' => ['CURRENCY_CODE']
                ]);
            if ($rsCurrencies->getSelectedRowsCount() < 1) {
                $obResult->addError(new \Bitrix\Main\Error('Не обнаружен ни один код валюты среди товаров.'));
            } else {

                $arUsedCurrencies = [];
                while ($ar = $rsCurrencies->fetch()) {

                    $strCurrencyCode = $ar['CURRENCY_CODE'];

                    if( !in_array( $strCurrencyCode, static::getSupportedCurrency() ) )
                    {
                        $strCurrencyCode = static::getMainCurrency();
                    }

                    if( $strCurrencyCode == 'RUB' )
                    {
                        $strCurrencyCode = 'RUR';
                    }

                    if( !in_array($strCurrencyCode, $arUsedCurrencies) )
                    {
                        $this->addToTmpExportFile('<currency id="'.htmlspecialchars($strCurrencyCode).'" rate="'.( round( \Local\Core\Inner\Currency::getRate($strCurrencyCode, static::getMainCurrency()), 3 ) ).'"/>');
                        $arUsedCurrencies[] = $strCurrencyCode;
                    }
                }

                if( !in_array( ( ( static::getMainCurrency() == 'RUB' ) ? 'RUR' : static::getMainCurrency() ) , $arUsedCurrencies) )
                {
                    $this->addToTmpExportFile('<currency id="'.htmlspecialchars( ( ( static::getMainCurrency() == 'RUB' ) ? 'RUR' : static::getMainCurrency() ) ).'" rate="1"/>');
                }
            }
        } else {
            $this->addToTmpExportFile('<currency id="'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO'])).'" rate="1"/>');
        }
        $this->addToTmpExportFile('</currencies>');
    }

    protected function fillCategories(\Bitrix\Main\Result $obResult)
    {
        $rsSections = \Local\Core\Model\Robofeed\StoreCategoryFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
            ->setStoreId($this->getTradingPlatformStoreId())::getList([]);
        if ($rsSections->getSelectedRowsCount() < 1) {
            $obResult->addError(new \Bitrix\Main\Error('Не обнаружена ни одна категория товаров.'));
        } else {
            $this->addToTmpExportFile('<categories>');
            while ($ar = $rsSections->fetch()) {
                $str = '<category id="'.htmlspecialchars(trim($ar['CATEGORY_ID'])).'"';
                $str .= (!empty(trim($ar['CATEGORY_PARENT_ID']))) ? ' parentId="'.htmlspecialchars(trim($ar['CATEGORY_PARENT_ID'])).'"' : '';
                $str .= '>'.htmlspecialchars(trim($ar['CATEGORY_NAME'])).'</category>';
                $this->addToTmpExportFile($str);
            }
            $this->addToTmpExportFile('</categories>');
        }
    }

    /** @inheritDoc */
    protected function beginOfferForeachBody(\Bitrix\Main\Result $obResult, $arExportProductData)
    {
        $arLog = $obResult->getData();
        $arLog['PRODUCTS_TOTAL']++;

        $funGetDefaultValue = function (\Local\Core\Inner\TradingPlatform\Field\AbstractField $obField) use ($arExportProductData)
            {
                return $obField->extractValue($obField->getValue(), $arExportProductData);
            };

        $arOfferXml = [];


        /** ****
         * OFFER
         * *** */
        switch ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@offer_data_source'])) {
            case 'CUSTOM':

                if (
                    !is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__id'], $arExportProductData))
                    && !is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__shop-sku'], $arExportProductData))
                    && !empty($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__name'], $arExportProductData))
                    && !empty($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__weight'], $arExportProductData))
                    && !empty($arExportProductData['WEIGHT_UNIT_CODE'])
                    && !empty($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__dimensions'], $arExportProductData))
                ) {

                    $arOfferXml['_attributes']['id'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__id'], $arExportProductData);
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__group_id'], $arExportProductData))) {
                        $arOfferXml['_attributes']['group_id'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__group_id'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__bid'], $arExportProductData))) {
                        $arOfferXml['_attributes']['bid'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__bid'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__available'], $arExportProductData))) {
                        $arOfferXml['_attributes']['available'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__available'], $arExportProductData)
                                                                   == 'Y') ? 'true' : 'false';
                    }
                    $arOfferXml['shop-sku'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__shop-sku'], $arExportProductData);

                    $arOfferXml['name'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__name'], $arExportProductData);
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendor'], $arExportProductData))) {
                        $arOfferXml['vendor'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendor'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendorCode'], $arExportProductData))) {
                        $arOfferXml['vendorCode'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendorCode'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__url'], $arExportProductData))) {
                        $arOfferXml['url'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__url'], $arExportProductData);
                    }

                    $this->fillPriceAndCurrency($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__price'], $arExportProductData),
                        $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__currencyId'], $arExportProductData),
                        $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__oldprice'], $arExportProductData), $arOfferXml);

                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__description'], $arExportProductData))) {
                        $arOfferXml['description']['_cdata'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__description'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__sales_notes'], $arExportProductData))) {
                        $arOfferXml['sales_notes'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__sales_notes'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__min-quantity'], $arExportProductData))) {
                        $arOfferXml['min-quantity'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__min-quantity'], $arExportProductData);
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__manufacturer_warranty'], $arExportProductData))) {
                        $arOfferXml['manufacturer_warranty'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__manufacturer_warranty'], $arExportProductData)
                                                                == 'Y') ? 'true' : 'false';
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__country_of_origin'], $arExportProductData))) {

                        $arOfferXml['country_of_origin'] = $this->extractYandexCountry($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__country_of_origin'], $arExportProductData));
                    }
                    if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__adult'], $arExportProductData))) {
                        $arOfferXml['adult'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__adult'], $arExportProductData) == 'Y') ? 'true' : 'false';
                    }

                    $arOfferXml['weight'] = \Local\Core\Inner\Measure::convert($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__weight'], $arExportProductData),
                        $arExportProductData['WEIGHT_UNIT_CODE'], 'KGM', 3);

                    try {
                        if (!($this->getFields()['shop__offers__offer__dimensions'] instanceof Field\Resource)) {
                            throw new \Exception();
                        }

                        $arSearchBuilderValue = null;

                        if ($this->getTPFieldDataByFieldName($this->getFields()['shop__offers__offer__dimensions']->getName())['TYPE'] == Field\Resource::TYPE_LOGIC) {

                            $arSuccessLogicValue = $this->getFields()['shop__offers__offer__dimensions']->extractLogicValidValue($this->getTPFieldDataByFieldName($this->getFields()['shop__offers__offer__dimensions']->getName()),
                                $arExportProductData);
                            if ($arSuccessLogicValue['TYPE'] == Field\Resource::TYPE_BUILDER) {
                                $arSearchBuilderValue = $arSuccessLogicValue;
                                unset($arSuccessLogicValue);
                            } else {
                                throw new \Exception();
                            }

                        } elseif ($this->getTPFieldDataByFieldName($this->getFields()['shop__offers__offer__dimensions']->getName())['TYPE'] == Field\Resource::TYPE_BUILDER) {
                            $arSearchBuilderValue = $this->getTPFieldDataByFieldName($this->getFields()['shop__offers__offer__dimensions']->getName());
                        }

                        if (is_null($arSearchBuilderValue)) {
                            throw new \Exception();
                        }


                        $arBuilderParts = explode('/', $arSearchBuilderValue[Field\Resource::TYPE_BUILDER.'_VALUE']);
                        $arBuilderParts = array_map('trim', $arBuilderParts);
                        if (sizeof($arBuilderParts) != 3) {
                            throw new \Exception();
                        }

                        if (
                            !in_array('{{BASE_FIELD#LENGTH}}', $arBuilderParts)
                            || !in_array('{{BASE_FIELD#WIDTH}}', $arBuilderParts)
                            || !in_array('{{BASE_FIELD#HEIGHT}}', $arBuilderParts)
                        ) {
                            throw new \Exception();
                        }

                        $lenK = array_search('{{BASE_FIELD#LENGTH}}', $arBuilderParts);
                        $widK = array_search('{{BASE_FIELD#WIDTH}}', $arBuilderParts);
                        $heiK = array_search('{{BASE_FIELD#HEIGHT}}', $arBuilderParts);

                        $arOfferXml['dimensions'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__dimensions'], $arExportProductData);
                        $ar = explode('/', $arOfferXml['dimensions']);
                        $ar = array_map('trim', $ar);

                        $intLength = $ar[$lenK];
                        $intWidth = $ar[$widK];
                        $intHeight = $ar[$heiK];

                        if ($intLength > 0 && $intWidth > 0 && $intHeight > 0) {
                            $arOfferXml['dimensions'] = \Local\Core\Inner\Measure::convert($intLength, $arExportProductData['LENGTH_UNIT_CODE'], 'CMT', 3).'/'
                                                        .\Local\Core\Inner\Measure::convert($intWidth, $arExportProductData['WIDTH_UNIT_CODE'], 'CMT', 3).'/'
                                                        .\Local\Core\Inner\Measure::convert($intHeight, $arExportProductData['HEIGHT_UNIT_CODE'], 'CMT', 3);
                        } else {
                            $arOfferXml['dimensions'] = null;
                        }


                    } catch (\Exception $e) {
                        $arOfferXml['dimensions'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__dimensions'], $arExportProductData);
                    }

                }
                else
                {
                    $arOfferXml = null;
                }

                break;

            case 'ROBOFEED':

                if (
                    !is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__id']))
                    && !is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__shop-sku']))
                    && !empty($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__name']))
                    && !empty($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__weight']))
                    && !empty($arExportProductData['WEIGHT_UNIT_CODE'])
                    && !empty($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__dimensions']))
                )
                {

                    $arOfferXml['_attributes']['id'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__id']);
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__group_id']))) {
                        $arOfferXml['_attributes']['group_id'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__group_id']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__bid']))) {
                        $arOfferXml['_attributes']['bid'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__bid']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__available']))) {
                        $arOfferXml['_attributes']['available'] = ($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__available']) == 'Y') ? 'true' : 'false';
                    }

                    $arOfferXml['shop-sku'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__shop-sku']);

                    $arOfferXml['name'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__name']);
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendor']))) {
                        $arOfferXml['vendor'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendor']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendorCode']))) {
                        $arOfferXml['vendorCode'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendorCode']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__url']))) {
                        $arOfferXml['url'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__url']);
                    }

                    $this->fillPriceAndCurrency($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__price']),
                        $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__currencyId']), $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__oldprice']),
                        $arOfferXml);

                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__description']))) {
                        $arOfferXml['description']['_cdata'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__description']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__sales_notes']))) {
                        $arOfferXml['sales_notes'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__sales_notes']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__min-quantity']))) {
                        $arOfferXml['min-quantity'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__min-quantity']);
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__manufacturer_warranty']))) {
                        $arOfferXml['manufacturer_warranty'] = ($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__manufacturer_warranty']) == 'Y') ? 'true' : 'false';
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__country_of_origin']))) {

                        $arOfferXml['country_of_origin'] = $this->extractYandexCountry($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__country_of_origin']));
                    }
                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__adult']))) {
                        $arOfferXml['adult'] = ($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__adult']) == 'Y') ? 'true' : 'false';
                    }

                    $arOfferXml['weight'] = \Local\Core\Inner\Measure::convert($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__weight']),
                        $arExportProductData['WEIGHT_UNIT_CODE'], 'KGM', 3);

                    if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__dimensions']))) {
                        $arOfferXml['dimensions'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__dimensions']);
                        list($intLength, $intWidth, $intHeight) = explode('/', $arOfferXml['dimensions']);
                        if ($intLength > 0 && $intWidth > 0 && $intHeight > 0) {
                            $arOfferXml['dimensions'] = \Local\Core\Inner\Measure::convert($intLength, $arExportProductData['LENGTH_UNIT_CODE'], 'CMT', 3).'/'.\Local\Core\Inner\Measure::convert($intWidth,
                                    $arExportProductData['WIDTH_UNIT_CODE'], 'CMT', 3).'/'.\Local\Core\Inner\Measure::convert($intHeight, $arExportProductData['HEIGHT_UNIT_CODE'], 'CMT', 3);
                        } else {
                            $arOfferXml['dimensions'] = null;
                        }
                    }
                }
                else
                {
                    $arOfferXml = null;
                }
                break;

            default:
                $arOfferXml = null;
                break;
        }

        if (!is_null($arOfferXml)) {

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vat'], $arExportProductData))) {

                $arOfferXml['vat'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vat'], $arExportProductData);

                if( !is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__disabled'], $arExportProductData)) )
                {
                    $arOfferXml['disabled'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__disabled'], $arExportProductData);
                }

                /** ***************
                 * OFFER ADDITIONAL
                 * ************** */
                $arOfferXml['categoryId'] = $arExportProductData['CATEGORY_ID'];

                if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__picture'], $arExportProductData))) {
                    $arOfferXml['picture'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__picture'], $arExportProductData);
                }

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__store'], $arExportProductData))) {
                    $arOfferXml['store'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__store'], $arExportProductData) == 'Y') ? 'true' : 'false';
                }

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__barcode'], $arExportProductData))) {
                    $arOfferXml['barcode'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__barcode'], $arExportProductData);
                }

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__downloadable'], $arExportProductData))) {
                    $arOfferXml['downloadable'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__downloadable'], $arExportProductData) == 'Y') ? 'true' : 'false';
                }

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__age__@attr__year'], $arExportProductData))) {
                    $arOfferXml['age'] = [
                        '_attributes' => [
                            'unit' => 'year'
                        ],
                        '_value' => $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__age__@attr__year'], $arExportProductData)
                    ];
                }

                /** *******
                 * DELIVERY
                 ******** */

                if( $this->getHandlerRules()['shop']['@delivery'] == 'Y' )
                {
                    switch ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@delivery_data_source'], $arExportProductData)) {
                        case 'CUSTOM':

                            $arOfferXml['delivery'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery'], $arExportProductData) == 'Y') ? 'true' : 'false';

                            if ($arOfferXml['delivery'] == 'true') {
                                $arOption = [];
                                $arOption['cost'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery-options__option__@attr__cost'], $arExportProductData);
                                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery-options__option__@attr__days'], $arExportProductData))) {
                                    $arOption['days'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery-options__option__@attr__days'], $arExportProductData);
                                }
                                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery-options__option__@attr__order-before'], $arExportProductData))) {
                                    $arOption['order-before'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery-options__option__@attr__order-before'],
                                        $arExportProductData);
                                }

                                $arOfferXml['delivery-options']['option'] = [
                                    '_attributes' => $arOption
                                ];
                            }

                            break;

                        case 'ROBOFEED':

                            $arOfferXml['delivery'] = ($arExportProductData['DELIVERY_AVAILABLE'] == 'Y') ? 'true' : 'false';
                            if ($arExportProductData['DELIVERY_AVAILABLE'] == 'Y' && !empty($arExportProductData['DELIVERY_OPTIONS'])) {
                                foreach ($arExportProductData['DELIVERY_OPTIONS'] as $arOptionData) {
                                    $arOption = [];
                                    $arOption['cost'] = (!empty($arOptionData['PRICE_TO'])
                                                         && $arOptionData['PRICE_TO'] > $arOptionData['PRICE_FROM']) ? $arOptionData['PRICE_TO'] : $arOptionData['PRICE_FROM'];

                                    if (!empty($arOptionData['DAYS_FROM']) && !empty($arOptionData['DAYS_TO']) && ($arOptionData['DAYS_TO'] - $arOptionData['DAYS_FROM']) <= 2) {
                                        $arOption['days'] = $arOptionData['DAYS_FROM'].'-'.$arOptionData['DAYS_TO'];
                                    } elseif (!empty($arOptionData['DAYS_TO'])) {
                                        $arOption['days'] = $arOptionData['DAYS_TO'];
                                    } elseif (!empty($arOptionData['DAYS_FROM'])) {
                                        $arOption['days'] = $arOptionData['DAYS_FROM'];
                                    }

                                    if ($arOptionData['ORDER_BEFORE'] >= 0 && $arOptionData['ORDER_BEFORE'] <= 23) {
                                        $arOption['order-before'] = $arOptionData['ORDER_BEFORE'];
                                    }

                                    $arOfferXml['delivery-options']['option'][] = [
                                        '_attributes' => $arOption
                                    ];

                                }
                            }

                            break;
                    }
                }
                else
                {
                    $arOfferXml['delivery'] = 'false';
                }

                /** *****
                 * PICKUP
                 ****** */

                switch ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@pickup_data_source'], $arExportProductData)) {
                    case 'CUSTOM':

                        $arOfferXml['pickup'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__pickup'], $arExportProductData) == 'Y') ? 'true' : 'false';

                        if ($arOfferXml['pickup'] == 'true') {
                            $arOption = [];
                            $arOption['cost'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__pickup-options__option__@attr__cost'], $arExportProductData);
                            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__pickup-options__option__@attr__days'], $arExportProductData))) {
                                $arOption['days'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__pickup-options__option__@attr__days'], $arExportProductData);
                            }
                            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__pickup-options__option__@attr__order-before'], $arExportProductData))) {
                                $arOption['order-before'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__pickup-options__option__@attr__order-before'], $arExportProductData);
                            }

                            $arOfferXml['pickup-options']['option'] = [
                                '_attributes' => $arOption
                            ];
                        }

                        break;

                    case 'ROBOFEED':

                        $arOfferXml['pickup'] = ($arExportProductData['PICKUP_AVAILABLE'] == 'Y') ? 'true' : 'false';
                        if ($arExportProductData['PICKUP_AVAILABLE'] == 'Y' && !empty($arExportProductData['PICKUP_OPTIONS'])) {
                            if( !empty( $arExportProductData['PICKUP_OPTIONS'] ) )
                            {
                                $arOptionData = $arExportProductData['PICKUP_OPTIONS'][0];

                                $arOption = [];
                                $arOption['cost'] = $arOptionData['PRICE'];

                                if (!empty($arOptionData['SUPPLY_FROM']) && !empty($arOptionData['SUPPLY_TO'])) {
                                    $arOption['days'] = $arOptionData['SUPPLY_FROM'].'-'.$arOptionData['SUPPLY_TO'];
                                } elseif (!empty($arOptionData['SUPPLY_TO'])) {
                                    $arOption['days'] = $arOptionData['SUPPLY_TO'];
                                } elseif (!empty($arOptionData['SUPPLY_FROM'])) {
                                    $arOption['days'] = $arOptionData['SUPPLY_FROM'];
                                }

                                if ($arOptionData['ORDER_BEFORE'] >= 0 && $arOptionData['ORDER_BEFORE'] <= 23) {
                                    $arOption['order-before'] = $arOptionData['ORDER_BEFORE'];
                                }

                                $arOfferXml['pickup-options']['option'][] = [
                                    '_attributes' => $arOption
                                ];
                            }
                        }

                        break;
                }

                /** **
                 * END
                 *** */
                $arOfferXml = array_diff($arOfferXml, [''], [null]);

                /** ****
                 * PARAM
                 ***** */
                $arSelectedParams = $this->getTPFieldDataByFieldName($this->getFields()['shop__offers__offer__param']->getName());
                if (is_array($this->getTPFieldDataByFieldName($this->getFields()['shop__offers__offer__param']->getName()))) {
                    if (in_array('#ALL', $arSelectedParams)) {
                        foreach ($arExportProductData['PARAMS'] as $arParam) {
                            $arOfferXml['param'][] = [
                                '_attributes' => [
                                    'name' => trim($arParam['NAME'])
                                ],
                                '_value' => trim($arParam['VALUE'])
                            ];
                        }
                    } else {
                        foreach ($arSelectedParams as $strParam) {
                            if (!empty($arExportProductData['PARAMS'][$strParam])) {
                                $arOfferXml['param'][] = [
                                    '_attributes' => [
                                        'name' => trim($arExportProductData['PARAMS'][$strParam]['NAME'])
                                    ],
                                    '_value' => trim($arExportProductData['PARAMS'][$strParam]['VALUE'])
                                ];
                            }
                        }
                    }
                }



                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__age__@attr__year'], $arExportProductData))) {
                    $arOfferXml['age'] = [
                        '_attributes' => [
                            'unit' => 'year'
                        ],
                        '_value' => $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__age__@attr__year'], $arExportProductData)
                    ];
                }

                if (!empty($arOfferXml)) {
                    $this->addToTmpExportFile($this->convertArrayToString($arOfferXml, 'offer'));

                    $arLog['PRODUCTS_EXPORTED']++;
                }

            }



        }

        $obResult->setData($arLog);
    }

    /**
     * Получает название старны в формате Яндекса
     *
     * @param string $strCountry Символьный код страны из справочника
     *
     * @return string|null
     */
    protected function extractYandexCountry($strCountry)
    {
        $strReturn = null;
        switch ($strCountry) {
            case 'AUS':
                $strReturn = 'АВСТРАЛИЯ';
                break;
            case 'AUT':
                $strReturn = 'АВСТРИЯ';
                break;
            case 'AZE':
                $strReturn = 'АЗЕРБАЙДЖАН';
                break;
            case 'ALB':
                $strReturn = 'АЛБАНИЯ';
                break;
            case 'DZA':
                $strReturn = 'АЛЖИР';
                break;
            case 'VIR':
                $strReturn = 'АМЕРИКАНСКИЕ ВИРГИНСКИЕ ОСТРОВА';
                break;
            case 'AIA':
                $strReturn = 'АНГИЛЬЯ';
                break;
            case 'AGO':
                $strReturn = 'АНГОЛА';
                break;
            case 'AND':
                $strReturn = 'АНДОРРА';
                break;
            case 'ATG':
                $strReturn = 'АНТИГУА И БАРБУ';
                break;
            case 'DEU':
                $strReturn = 'ГЕРМАНИЯ';
                break;
            case 'GIB':
                $strReturn = 'ГИБРАЛТАР';
                break;
            case 'HND':
                $strReturn = 'ГОНДУРАС';
                break;
            case 'HKG':
                $strReturn = 'ГОНКОНГ';
                break;
            case 'GRD':
                $strReturn = 'ГРЕНАДА';
                break;
            case 'GRL':
                $strReturn = 'ГРЕНЛАНДИЯ';
                break;
            case 'GRC':
                $strReturn = 'ГРЕЦИЯ';
                break;
            case 'GEO':
                $strReturn = 'ГРУЗИЯ';
                break;
            case 'DNK':
                $strReturn = 'ДАНИЯ';
                break;
            case 'COD':
                $strReturn = 'ДЕМОКРАТИЧЕСКАЯ РЕСПУБЛИКА КОНГО';
                break;
            case 'DJI':
                $strReturn = 'ДЖИБУТИ';
                break;
            case 'DMA':
                $strReturn = 'ДОМИНИКА';
                break;
            case 'DOM':
                $strReturn = 'ДОМИНИКАНСКАЯ РЕСПУБЛИКА';
                break;
            case 'EGY':
                $strReturn = 'ЕГИПЕТ';
                break;
            case 'ZMB':
                $strReturn = 'ЗАМБИЯ';
                break;
            case 'ZWE':
                $strReturn = 'ЗИМБАБВЕ';
                break;
            case 'YEM':
                $strReturn = 'ЙЕМЕН';
                break;
            case 'ISR':
                $strReturn = 'ИЗРАИЛЬ';
                break;
            case 'IND':
                $strReturn = 'ИНДИЯ';
                break;
            case 'IDN':
                $strReturn = 'ИНДОНЕЗИЯ';
                break;
            case 'JOR':
                $strReturn = 'ИОРДАНИЯ';
                break;
            case 'IRQ':
                $strReturn = 'ИРАК';
                break;
            case 'IRN':
                $strReturn = 'ИРАН';
                break;
            case 'IRL':
                $strReturn = 'ИРЛАНДИЯ';
                break;
            case 'ISL':
                $strReturn = 'ИСЛАНДИЯ';
                break;
            case 'ESP':
                $strReturn = 'ИСПАНИЯ';
                break;
            case 'ITA':
                $strReturn = 'ИТАЛИЯ';
                break;
            case 'CPV':
                $strReturn = 'КАБО-ВЕРДЕ';
                break;
            case 'KAZ':
                $strReturn = 'КАЗАХСТАН';
                break;
            case 'CYM':
                $strReturn = 'КАЙМАНОВЫ ОСТРОВА';
                break;
            case 'KHM':
                $strReturn = 'КАМБОДЖА';
                break;
            case 'CMR':
                $strReturn = 'КАМЕРУН';
                break;
            case 'CAN':
                $strReturn = 'КАНАДА';
                break;
            case 'QAT':
                $strReturn = 'КАТАР';
                break;
            case 'KEN':
                $strReturn = 'КЕНИЯ';
                break;
            case 'CYP':
                $strReturn = 'КИПР';
                break;
            case 'KGZ':
                $strReturn = 'КИРГИЗИЯ';
                break;
            case 'KIR':
                $strReturn = 'КИРИБАТИ';
                break;
            case 'CHN':
                $strReturn = 'КИТАЙ';
                break;
            case 'COL':
                $strReturn = 'КОЛУМБИЯ';
                break;
            case 'COM':
                $strReturn = 'КОМОРСКИЕ ОСТРОВА';
                break;
            case 'CRI':
                $strReturn = 'КОСТА-РИКА';
                break;
            case 'CIV':
                $strReturn = 'КОТ-Д’ИВУАР';
                break;
            case 'CUB':
                $strReturn = 'КУБА';
                break;
            case 'KWT':
                $strReturn = 'КУВЕЙТ';
                break;
            case 'LAO':
                $strReturn = 'ЛАОС';
                break;
            case 'LVA':
                $strReturn = 'ЛАТВИЯ';
                break;
            case 'LSO':
                $strReturn = 'ЛЕСОТО';
                break;
            case 'LBR':
                $strReturn = 'ЛИБЕРИЯ';
                break;
            case 'LBN':
                $strReturn = 'ЛИВАН';
                break;
            case 'LBY':
                $strReturn = 'ЛИВИЯ';
                break;
            case 'LTU':
                $strReturn = 'ЛИТВА';
                break;
            case 'LIE':
                $strReturn = 'ЛИХТЕНШТЕЙН';
                break;
            case 'LUX':
                $strReturn = 'ЛЮКСЕМБУРГ';
                break;
            case 'MUS':
                $strReturn = 'МАВРИКИЙ';
                break;
            case 'MRT':
                $strReturn = 'МАВРИТАНИЯ';
                break;
            case 'MDG':
                $strReturn = 'МАДАГАСКАР';
                break;
            case 'MYT':
                $strReturn = 'МАЙОТТА';
                break;
            case 'MAC':
                $strReturn = 'МАКАО';
                break;
            case 'MKD':
                $strReturn = 'МАКЕДОНИЯ';
                break;
            case 'MWI':
                $strReturn = 'МАЛАВИ';
                break;
            case 'MYS':
                $strReturn = 'МАЛАЙЗИЯ';
                break;
            case 'MLI':
                $strReturn = 'МАЛИ';
                break;
            case 'MDV':
                $strReturn = 'МАЛЬДИВЫ';
                break;
            case 'MLT':
                $strReturn = 'МАЛЬТА';
                break;
            case 'MAR':
                $strReturn = 'МАРОККО';
                break;
            case 'MHL':
                $strReturn = 'МАРШАЛЛОВЫ ОСТРОВА';
                break;
            case 'MEX':
                $strReturn = 'МЕКСИКА';
                break;
            case 'MOZ':
                $strReturn = 'МОЗАМБИК';
                break;
            case 'MDA':
                $strReturn = 'МОЛДОВА';
                break;
            case 'MCO':
                $strReturn = 'МОНАКО';
                break;
            case 'MNG':
                $strReturn = 'МОНГОЛИЯ';
                break;
            case 'MMR':
                $strReturn = 'МЬЯНМА';
                break;
            case 'NAM':
                $strReturn = 'НАМИБИЯ';
                break;
            case 'NRU':
                $strReturn = 'НАУРУ';
                break;
            case 'NPL':
                $strReturn = 'НЕПАЛ';
                break;
            case 'NER':
                $strReturn = 'НИГЕР';
                break;
            case 'NGA':
                $strReturn = 'НИГЕРИЯ';
                break;
            case 'NLD':
                $strReturn = 'НИДЕРЛАНДЫ';
                break;
            case 'NIC':
                $strReturn = 'НИКАРАГУА';
                break;
            case 'NZL':
                $strReturn = 'НОВАЯ ЗЕЛАНДИЯ';
                break;
            case 'NCL':
                $strReturn = 'НОВАЯ КАЛЕДОНИЯ';
                break;
            case 'NOR':
                $strReturn = 'НОРВЕГИЯ';
                break;
            case 'ARE':
                $strReturn = 'ОБЪЕДИНЁННЫЕ АРАБСКИЕ ЭМИРАТЫ';
                break;
            case 'OMN':
                $strReturn = 'ОМАН';
                break;
            case 'COK':
                $strReturn = 'ОСТРОВА КУКА';
                break;
            case 'PAK':
                $strReturn = 'ПАКИСТАН';
                break;
            case 'PLW':
                $strReturn = 'ПАЛАУ';
                break;
            case 'PAN':
                $strReturn = 'ПАНАМА';
                break;
            case 'PNG':
                $strReturn = 'ПАПУА - НОВАЯ ГВИНЕЯ';
                break;
            case 'PRY':
                $strReturn = 'ПАРАГВАЙ';
                break;
            case 'PER':
                $strReturn = 'ПЕРУ';
                break;
            case 'POL':
                $strReturn = 'ПОЛЬША';
                break;
            case 'PRT':
                $strReturn = 'ПОРТУГАЛИЯ';
                break;
            case 'COG':
                $strReturn = 'РЕСПУБЛИКА КОНГО';
                break;
            case 'REU':
                $strReturn = 'РЕЮНЬОН';
                break;
            case 'RUS':
                $strReturn = 'РОССИЯ';
                break;
            case 'RWA':
                $strReturn = 'РУАНДА';
                break;
            case 'ROU':
                $strReturn = 'РУМЫНИЯ';
                break;
            case 'WSM':
                $strReturn = 'САМОА';
                break;
            case 'SMR':
                $strReturn = 'САН-МАРИНО';
                break;
            case 'STP':
                $strReturn = 'САН-ТОМЕ И ПРИНСИПИ';
                break;
            case 'SAU':
                $strReturn = 'САУДОВСКАЯ АРАВИЯ';
                break;
            case 'SWZ':
                $strReturn = 'СВАЗИЛЕНД';
                break;
            case 'PRK':
                $strReturn = 'СЕВЕРНАЯ КОРЕЯ';
                break;
            case 'SYC':
                $strReturn = 'СЕЙШЕЛЬСКИЕ ОСТРОВА';
                break;
            case 'SEN':
                $strReturn = 'СЕНЕГАЛ';
                break;
            case 'VCT':
                $strReturn = 'СЕНТ-ВИНСЕНТ И ГРЕНАДИНЫ';
                break;
            case 'KNA':
                $strReturn = 'СЕНТ-КИТС И НЕВИС';
                break;
            case 'LCA':
                $strReturn = 'СЕНТ-ЛЮСИЯ';
                break;
            case 'SRB':
                $strReturn = 'СЕРБИЯ';
                break;
            case 'SGP':
                $strReturn = 'СИНГАПУР';
                break;
            case 'SYR':
                $strReturn = 'СИРИЯ';
                break;
            case 'SVK':
                $strReturn = 'СЛОВАКИЯ';
                break;
            case 'SVN':
                $strReturn = 'СЛОВЕНИЯ';
                break;
            case 'SOM':
                $strReturn = 'СОМАЛИ';
                break;
            case 'SDN':
                $strReturn = 'СУДАН';
                break;
            case 'SUR':
                $strReturn = 'СУРИНАМ';
                break;
            case 'USA':
                $strReturn = 'США';
                break;
            case 'SLE':
                $strReturn = 'СЬЕРРА-ЛЕОНЕ';
                break;
            case 'TJK':
                $strReturn = 'ТАДЖИКИСТАН';
                break;
            case 'THA':
                $strReturn = 'ТАИЛАНД';
                break;
            case 'TZA':
                $strReturn = 'ТАНЗАНИЯ';
                break;
            case 'TCA':
                $strReturn = 'ТЁРКС И КАЙКОС';
                break;
            case 'TGO':
                $strReturn = 'ТОГО';
                break;
            case 'TON':
                $strReturn = 'ТОНГА';
                break;
            case 'TTO':
                $strReturn = 'ТРИНИДАД И ТОБАГО';
                break;
            case 'TUV':
                $strReturn = 'ТУВАЛУ';
                break;
            case 'TUN':
                $strReturn = 'ТУНИС';
                break;
            case 'TKM':
                $strReturn = 'ТУРКМЕНИСТАН';
                break;
            case 'TUR':
                $strReturn = 'ТУРЦИЯ';
                break;
            case 'UGA':
                $strReturn = 'УГАНДА';
                break;
            case 'UZB':
                $strReturn = 'УЗБЕКИСТАН';
                break;
            case 'UKR':
                $strReturn = 'УКРАИНА';
                break;
            case 'URY':
                $strReturn = 'УРУГВАЙ';
                break;
            case 'FSM':
                $strReturn = 'ФЕДЕРАТИВНЫЕ ШТАТЫ МИКРОНЕЗИИ';
                break;
            case 'FJI':
                $strReturn = 'ФИДЖИ';
                break;
            case 'PHL':
                $strReturn = 'ФИЛИППИНЫ';
                break;
            case 'FIN':
                $strReturn = 'ФИНЛЯНДИЯ';
                break;
            case 'FRA':
                $strReturn = 'ФРАНЦИЯ';
                break;
            case 'GUF':
                $strReturn = 'ФРАНЦУЗСКАЯ ГВИАНА';
                break;
            case 'PYF':
                $strReturn = 'ФРАНЦУЗСКАЯ ПОЛИНЕЗИЯ';
                break;
            case 'HRV':
                $strReturn = 'ХОРВАТИЯ';
                break;
            case 'TCD':
                $strReturn = 'ЧАД';
                break;
            case 'MNE':
                $strReturn = 'ЧЕРНОГОРИЯ';
                break;
            case 'CZE':
                $strReturn = 'ЧЕХИЯ';
                break;
            case 'CHL':
                $strReturn = 'ЧИЛИ';
                break;
            case 'CHE':
                $strReturn = 'ШВЕЙЦАРИЯ';
                break;
            case 'SWE':
                $strReturn = 'ШВЕЦИЯ';
                break;
            case 'LKA':
                $strReturn = 'ШРИ-ЛАНКА';
                break;
            case 'ECU':
                $strReturn = 'ЭКВАДОР';
                break;
            case 'GNQ':
                $strReturn = 'ЭКВАТОРИАЛЬНАЯ ГВИНЕЯ';
                break;
            case 'ERI':
                $strReturn = 'ЭРИТРЕЯ';
                break;
            case 'EST':
                $strReturn = 'ЭСТОНИЯ';
                break;
            case 'ETH':
                $strReturn = 'ЭФИОПИЯ';
                break;
            case 'ZAF':
                $strReturn = 'ЮАР';
                break;
            case 'KOR':
                $strReturn = 'ЮЖНАЯ КОРЕЯ';
                break;
            case 'JAM':
                $strReturn = 'ЯМАЙКА';
                break;
            case 'JPN':
                $strReturn = 'ЯПОНИЯ';
                break;
        }

        if (!is_null($strReturn)) {
            $strReturn = htmlspecialchars(trim($strReturn));
        }

        return $strReturn;
    }

    /**
     * Заполняет цену, валюту  старую цену
     *
     * @param string $intPrice            Актуальная соимость
     * @param string $strCurrencyCode     Текущий код валюты
     * @param string $intOldPrice         Старая цена или null
     * @param array  $arOfferXml          Массив, в который новые значения будут дописаны, ссылка
     * @param array  $arExportProductData Массив полей текущего товара
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function fillPriceAndCurrency($intPrice, $strCurrencyCode, $intOldPrice, &$arOfferXml)
    {

        $intNewPrice = \Local\Core\Inner\Currency::convert($intPrice, $strCurrencyCode, $this->getFinalCurrency($strCurrencyCode));
        if (!is_null($intNewPrice)) {
            $arOfferXml['price'] = $intNewPrice;
            $arOfferXml['oldprice'] = \Local\Core\Inner\Currency::convert($intOldPrice, $strCurrencyCode, $this->getFinalCurrency($strCurrencyCode));
            $arOfferXml['currencyId'] = ($this->getFinalCurrency($strCurrencyCode) == 'RUB') ? 'RUR' : $this->getFinalCurrency($strCurrencyCode);
        }

        if(
            $arOfferXml['price'] >= $arOfferXml['oldprice']
        )
        {
            unset($arOfferXml['oldprice']);
        }
    }
}