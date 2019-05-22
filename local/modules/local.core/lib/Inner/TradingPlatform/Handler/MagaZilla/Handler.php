<?php

namespace Local\Core\Inner\TradingPlatform\Handler\MagaZilla;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'magazilla';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'MagaZilla';
    }

    /** @inheritDoc */
    protected static $strMainCurrency = null;
    public static function getMainCurrency()
    {
        return self::$strMainCurrency;
    }

    /** @inheritDoc */
    public static function getSupportedCurrency()
    {
        return [
            'RUB',
            'UAH',
            'USD'
        ];
    }

    /** @inheritDoc */
    protected function getHandlerFields()
    {
        return $this->getShopBaseFields() + $this->getOfferFields();
    }

    protected function getShopBaseFields()
    {
        $arShopFields = [
            '#header_y1' => (new Field\Header())->setValue('Настройки магазина'),

            'shop__region' => (new Field\Select())->setTitle('Страна магазина')
                ->setName('HANDLER_RULES[shop][region]')
                ->setIsRequired()
                ->setOptions([
                    'RUS' => 'Россия',
                    'UKR' => 'Украина',
                ])
                ->setDefaultOption('-- Выберите страну --')
                ->setValue($this->getHandlerRules()['shop']['region'])
                ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
От выбора страны будет зависить какой будет являться основная валюта торговой площадки.<br/>
<table class="table table-striped">
    <tr>
        <th>Россия</th>
        <td>
            <b>Валюта:</b> Российский рубль (RUB/RUR).<br/>
            <b>Сайт торговой площадки:</b> <a href="https://magazilla.ru" target="_blank">https://magazilla.ru</a>
        </td>
    </tr>
    <tr>
        <th>Украина</th>
        <td>
            <b>Валюта:</b> Гривна (UAH).<br/>
            <b>Сайт торговой площадки:</b> <a href="https://m.ua" target="_blank">https://m.ua</a>
        </td>
    </tr>
</table>
DOCHERE
)),

            'shop__name' => (new Field\InputText())->setTitle('Короткое название магазина')
                ->setName('HANDLER_RULES[shop][name]')
                ->setIsRequired()
                ->setPlaceholder('Рога и копыта')
                ->setValue($this->getHandlerRules()['shop']['name']),

            'shop__company' => (new Field\InputText())->setTitle('Полное наименование компании, владеющей магазином')
                ->setName('HANDLER_RULES[shop][company]')
                ->setIsRequired()
                ->setPlaceholder('ООО Рога и копыта')
                ->setValue($this->getHandlerRules()['shop']['company']),

            'shop__url' => (new Field\InputText())->setTitle('URL главной страницы магазина')
                ->setName('HANDLER_RULES[shop][url]')
                ->setIsRequired()
                ->setPlaceholder('https://example.com')
                ->setValue($this->getHandlerRules()['shop']['url']),
        ];

        return $arShopFields;
    }

    protected static $arParamsListCache;

    protected function getOfferFields()
    {
        $arFields = [
            '#header_y4' => (new Field\Header())->setValue('Торговые предложения')
        ];

        $arFields['shop__items__item__@attr__id'] = (new Field\Resource())->setTitle('Идентификатор предложения')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][@attr][id]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['@attr']['id'] ?? ['TYPE' => Field\Resource::TYPE_SOURCE, Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID']);

        $arFields['shop__items__item__@attr__available'] = (new Field\Resource())->setTitle('Наличие товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][@attr][available]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                'Y' => 'В наличии',
                'N' => 'Под заказ',
            ]))
            ->setValue($this->getHandlerRules()['shop']['items']['item']['@attr']['available'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IN_STOCK'
                ]);

        $arFields['shop__items__item__vendor'] = (new Field\Resource())->setTitle('Производитель')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setIsRequired()
            ->setName('HANDLER_RULES[shop][items][item][vendor]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SIMPLE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSimpleField((new Field\InputText()))
            ->setValue($this->getHandlerRules()['shop']['items']['item']['vendor'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER'
                ]);

        $arFields['shop__items__item__name'] = (new Field\Resource())->setTitle('Модель товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][name]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['name'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#SIMPLE_NAME'
                ])
            ->setEpilog((new Field\Infoblock())->setValue( <<<DOCHERE
<i class="font-weight-bold">MagaZilla</i> в качестве названия использует комбинированию из производителя и модели.
<div class="blockquote border-warning mt-3">
Очень важно указывать название модели, как ее указывает производитель товара.
<div class="blockquote-footer">MagaZilla</div>
</div>
Говоря иначе - если Вы хотите передать товар "Apple iPhone 8 Plus 64гб", то в поле <b>"Производитель"</b> необходимо передать "Apple", в поле <b>"Модель товара"</b> - "iPhone 8 Plus 64гб". <br/>
<br/>
<b>Внимание!</b><br/>
Если в Robofeed XML в поле <code>robofeed->offers->offer->simpleName (Простое название товара)</code> Вы по какой-то причине передаете "Apple iPhone 8 Plus 64гб", то мы рекомендуем воспользоваться механизмом <b>"Сложное значение"</b> и строить название динамически, использую поле "Модель" и добавляя к нему важные характеристики.
DOCHERE
            ));

        $arFields['shop__items__item__typePrefix'] = (new Field\Resource())->setTitle('Тип / категория товара')
            ->setDescription('Например: Утюг, Чайник, Ковеварка')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][typePrefix]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['typePrefix'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#CATEGORY_NAME'
                ]);

        $arFields['shop__items__item__vendorCode'] = (new Field\Resource())->setTitle('Код производителя для данного товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][vendorCode]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['vendorCode'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_CODE'
                ]);

        $arFields['shop__items__item__url'] = (new Field\Resource())->setTitle('Ссылка на карточку товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][url]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['url'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#DETAIL_URL_WITH_UTM'
                ]);

        $arFields['shop__items__item__price'] = (new Field\Resource())->setTitle('Актуальная цена товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][price]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['price'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRICE'
                ]);

        $strRouteReferenceCurrency = Route::getRouteTo('development', 'references').'#currency';
        $strDefaultCurrencyCode = self::getMainCurrency();
        $arFields['shop__items__item__currencyId'] = (new Field\Resource())->setTitle('Валюта, в которой указана цена товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][currencyId]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['currencyId'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#CURRENCY_CODE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue( <<<DOCHERE
Должен быть передан <b>символьный код валюты из справочника валют</b>.<br/>Справочник - <a href="$strRouteReferenceCurrency" target="_blank">https://robofeed.ru$strRouteReferenceCurrency</a> .<br/>
<br/>
<b>Внимание!</b><br/>
<i class="font-weight-bold">MagaZilla</i> требует передавать стоимость товара в одной валюте. Ввиду этого, если значением поля <b>"Конвертация цен"</b> является <b>"Оставлять цены в переданных валютах"</b>, то мы будет вынуждены конвертировать все цены в основную валюту торговой площадки. Для <i class="font-weight-bold">MagaZilla</i> основная валюта зависит от значения поля <b>"Страна магазина"</b>.
DOCHERE
            ));

        $arFields['shop__items__item__oldprice'] = (new Field\Resource())->setTitle('Старая цена товара')
            ->setDescription('Должна быть выше текущей.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][oldprice]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['oldprice'] ?? [
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

        $arFields['shop__items__item__description'] = (new Field\Resource())->setTitle('Описание товара')
            ->setDescription('Длина текста не более 3000 символов (включая знаки препинания).')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][description]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['description'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#DESCRIPTION'
                ]);

        $arFields['shop__items__item__manufacturer_warranty'] = (new Field\Resource())->setTitle('Официальная гарантия производителя')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][items][item][manufacturer_warranty]')
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
            ->setValue($this->getHandlerRules()['shop']['items']['item']['manufacturer_warranty'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_WARRANTY'
                ]);


        $arFields['#header_y5'] = (new Field\Header())->setValue('Дополнительные поля товара');

        $arFields['shop__items__item__image'] = (new Field\Resource())->setTitle('Ссылки на картинки товара')
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
            ->setName('HANDLER_RULES[shop][items][item][image]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['items']['item']['image'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IMAGE'
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

        $arFields['shop__items__item__param'] = (new Field\Select())->setTitle('Характеристики товара')
            ->setDescription('Выберите характеристики, которые необходимо передавать в <i class="font-weight-bold">Яндекс.Маркет</i>.')
            ->setName('HANDLER_RULES[shop][items][item][param]')
            ->setIsMultiple()
            ->setOptions(['#ALL' => 'Передавать все характеристики'] + self::$arParamsListCache[$this->getTradingPlatformStoreId()])
            ->setDefaultOption('-- Выберите параметры --')
            ->setValue($this->getHandlerRules()['shop']['items']['item']['param'] ?? ['#ALL']);

        return $arFields;
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

            switch ($this->extractFilledValueFromRule($this->getFields()['shop__region']))
            {
                case 'RUS':
                    self::$strMainCurrency = 'RUB';
                    break;

                case 'UKR':
                    self::$strMainCurrency = 'UAH';
                    break;
            }

            if( empty( self::getMainCurrency() ) )
            {
                $obResult->addError( new \Bitrix\Main\Error('Необходимо выбрать страну магазина.') );
            }

            $this->fillShopHeader($obResult);
            $this->fillCurrencies($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->fillCategories($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->addToTmpExportFile('<items>');
            $this->beginFilterProduct($obResult);
            $this->addToTmpExportFile('</items>');

            $this->addToTmpExportFile('</shop></yml_catalog>');

        } catch (\Throwable $e) {
            if (!empty($e->getMessage())) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
    }

    protected function fillShopHeader(\Bitrix\Main\Result $obResult)
    {
        $this->addToTmpExportFile('<name>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__name'])).'</name>');
        $this->addToTmpExportFile('<company>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__company'])).'</company>');
        $this->addToTmpExportFile('<url>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__url'])).'</url>');
    }

    protected function fillCurrencies(\Bitrix\Main\Result $obResult)
    {
        $strFinalCurrency = ( $this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']) == 'NOT_CONVERT' ) ? self::getMainCurrency() : $this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']);

        if( !in_array($strFinalCurrency, static::getSupportedCurrency()) )
        {
            $strFinalCurrency = static::getMainCurrency();
        }

        $this->addToTmpExportFile('<currencies>');
        $this->addToTmpExportFile('<currency id="'.htmlspecialchars($strFinalCurrency).'" rate="1"/>');
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

        $arOfferXml = [];
        /** ****
         * OFFER
         * *** */

        if(
            !empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__@attr__id'], $arExportProductData))
            && !empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__@attr__available'], $arExportProductData))
            && !empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__vendor'], $arExportProductData))
            && !empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__name'], $arExportProductData))
            && !empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__url'], $arExportProductData))
            && !empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__price'], $arExportProductData))
        )
        {
            $arOfferXml['_attributes']['id'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__@attr__id'], $arExportProductData);
            $arOfferXml['_attributes']['available'] = ($this->extractFilledValueFromRule($this->getFields()['shop__items__item__@attr__available'], $arExportProductData) == 'Y') ? 'true' : 'false';
            $arOfferXml['vendor'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__vendor'], $arExportProductData);
            $arOfferXml['name'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__name'], $arExportProductData);

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__items__item__typePrefix'], $arExportProductData))) {
                $arOfferXml['typePrefix'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__typePrefix'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__items__item__vendorCode'], $arExportProductData))) {
                $arOfferXml['vendorCode'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__vendorCode'], $arExportProductData);
            }

            $arOfferXml['url'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__url'], $arExportProductData);

            $this->fillPriceAndCurrency($this->extractFilledValueFromRule($this->getFields()['shop__items__item__price'], $arExportProductData),
                $this->extractFilledValueFromRule($this->getFields()['shop__items__item__currencyId'], $arExportProductData),
                $this->extractFilledValueFromRule($this->getFields()['shop__items__item__oldprice'], $arExportProductData), $arOfferXml);

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__items__item__description'], $arExportProductData))) {
                $arOfferXml['description'] = trim( strip_tags($this->extractFilledValueFromRule($this->getFields()['shop__items__item__description'], $arExportProductData)) );
            }
            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__items__item__manufacturer_warranty'], $arExportProductData))) {
                $arOfferXml['manufacturer_warranty'] = ($this->extractFilledValueFromRule($this->getFields()['shop__items__item__manufacturer_warranty'], $arExportProductData) == 'Y') ? 'true' : 'false';
            }

            /** ***************
             * OFFER ADDITIONAL
             * ************** */
            $arOfferXml['categoryId'] = $arExportProductData['CATEGORY_ID'];

            if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__items__item__image'], $arExportProductData))) {
                $arOfferXml['image'] = $this->extractFilledValueFromRule($this->getFields()['shop__items__item__image'], $arExportProductData);
            }

            /** **
             * END
             *** */
            $arOfferXml = array_diff($arOfferXml, [''], [null]);

            /** ****
             * PARAM
             ***** */
            $arSelectedParams = $this->getTPFieldDataByFieldName($this->getFields()['shop__items__item__param']->getName());
            if (is_array($this->getTPFieldDataByFieldName($this->getFields()['shop__items__item__param']->getName()))) {
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

            if (!empty($arOfferXml)) {
                $this->addToTmpExportFile($this->convertArrayToString($arOfferXml, 'item'));

                $arLog['PRODUCTS_EXPORTED']++;
            }

        }

        $obResult->setData($arLog);
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
        $strFinalCurrency = ( $this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']) == 'NOT_CONVERT' ) ? self::getMainCurrency() : $this->extractFilledValueFromRule($this->getFields()['@handler_settings__CONVERT_CURRENCY_TO']);

        if( !in_array($strFinalCurrency, static::getSupportedCurrency()) )
        {
            $strFinalCurrency = static::getMainCurrency();
        }

        $intNewPrice = \Local\Core\Inner\Currency::convert($intPrice, $strCurrencyCode, $strFinalCurrency);
        if (!is_null($intNewPrice)) {
            $arOfferXml['price'] = $intNewPrice;
            $arOfferXml['oldprice'] = \Local\Core\Inner\Currency::convert($intOldPrice, $strCurrencyCode, $strFinalCurrency);
            $arOfferXml['currencyId'] = ($strFinalCurrency == 'RUB') ? 'RUR' : $strFinalCurrency;
        }

        if(
            $arOfferXml['price'] >= $arOfferXml['oldprice']
        )
        {
            unset($arOfferXml['oldprice']);
        }
    }
}