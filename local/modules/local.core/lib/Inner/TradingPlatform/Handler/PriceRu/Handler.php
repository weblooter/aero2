<?php

namespace Local\Core\Inner\TradingPlatform\Handler\PriceRu;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'priceru';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Price.ru';
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
            'EUR',
            'USD'
        ];
    }

    /** @inheritDoc */
    protected function getHandlerFields()
    {
        return $this->getShopBaseFields() + $this->getDefaultDeliveryFields() + $this->getOfferFields();
    }

    protected function getShopBaseFields()
    {
        $arShopFields = [
            '#header_y1' => (new Field\Header())->setValue('Настройки магазина'),

            'shop__company' => (new Field\InputText())->setTitle('Название вашей компании')
                ->setDescription('Не публикуется, используется для внутренней идентификации.')
                ->setName('HANDLER_RULES[shop][company]')
                ->setIsRequired()
                ->setPlaceholder('ООО "Рога и копыта"')
                ->setValue($this->getHandlerRules()['shop']['company']),

            'shop__url' => (new Field\InputText())->setTitle('Ссылка на главную страницу вашего магазина')
                ->setName('HANDLER_RULES[shop][url]')
                ->setIsRequired()
                ->setPlaceholder('https://example.com')
                ->setValue($this->getHandlerRules()['shop']['url']),
        ];

        return $arShopFields;
    }

    protected function getDefaultDeliveryFields()
    {
        $arDeliveryFields = [];

        $arDeliveryFields['#header_y2'] = (new Field\Header())->setValue('Условия доставки товара');

        $arDeliveryFields['shop__@delivery'] = (new Field\Select())->setTitle('Имеется ли у магазина курьерская доставка?')
            ->setDescription('Если выбрать значение "Нет", то возможности передавать значения доставки в товары будет нельзя.')
            ->setName('HANDLER_RULES[shop][@delivery]')
            ->setValue($this->getHandlerRules()['shop']['@delivery'] ?? 'Y')
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

        if (($this->getHandlerRules()['shop']['@delivery'] ?? 'Y') == 'Y') {

            $arDeliveryFields['shop__offers__offer__delivery-options__option__@attr__cost'] = (new Field\Resource())->setTitle('Стоимость доставки')
                ->setDescription('В качестве значения используйте только целые числа. Для бесплатной доставки укажите значение 0.')
                ->setName('HANDLER_RULES[shop][offers][offer][delivery-options][option][@attr][cost]')
                ->setIsRequired()
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                ])
                ->setSimpleField((new Field\InputText()))
                ->setValue($this->getHandlerRules()['shop']['offers']['offer']['delivery-options']['option']['@attr']['cost'] ??
                           [
                               'TYPE' => Field\Resource::TYPE_SOURCE,
                               Field\Resource::TYPE_SOURCE.'_VALUE' => 'DELIVERY_FIELD#PRICE_FROM#MAX'
                           ]);
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

        $arFields['shop__offers__offer__picture'] = (new Field\Resource())->setTitle('Ссылка на картинки данного товарного предложения')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][picture]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['picture'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IMAGE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote mb-0">
Требования к изображениям
<ul>
<li>Допустимы изображения следующих форматов: GIF, JPG, JPEG, PNG.</li>
<li>Изображение должно иметь геометрический размер не менее 100 пикселей по любой из сторон.</li>
<li>Ограничение на размер файла одной картинки: не более 10 МБ.</li>
<li>Ссылка должна быть именно на картинку, а не на страницу с картинкой.</li>
<li>Недопустима ссылка на изображение другого товара, пиктограмму, заглушку, замещающее изображение,</li>
<li>логотип бренда, магазина и т.п.</li>
<li>Недопустимо изображение, несоответствующее товару.</li>
<li>Ограничение на время скачивания картинки – 5 секунд.</li>
<li>Недопустимы одноцветные изображения (за исключением категорий вида "краски", "материалы").</li>
<li>Недопустимы изображения, содержащие элементы рекламы и побуждения к покупке, например, "дёшево" или</li>
<li>сведения о доставке, гарантии и т.п.</li>
<li>Недопустимы изображения с водяными знаками, названиями бренда.</li>
<li>Недопустимы изображения в рамке.</li>
<li>Недопустимы изображения комплекта, не показывающие полный комплект.</li>
</ul>
    <div class="blockquote-footer">Price.ru</div>
</div>
DOCHERE
            ));

        $arFields['shop__offers__offer__barcode'] = (new Field\Resource())->setTitle('Штрихкод товара от производителя')
            ->setDescription('Допустимые форматы: EAN-13, EAN-8, UPC-A, UPC-E.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][barcode]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['barcode'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ]);

        $arFields['shop__offers__offer__vert'] = (new Field\Resource())->setTitle('Ставка за товар в рублях')
            ->setDescription('Допускаются только целые числа больше 0. В случае игноририрования поля будут применяться ставки, указанные в личном кабинете <i class="font-weight-bold">Price.ru</i>')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][vert]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SIMPLE,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSimpleField((new Field\InputText()))
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vert'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ]);

        if (is_null(self::$arParamsListCache[$this->getTradingPlatformStoreId()])) {
            $rsProductProps = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                ->setStoreId($this->getTradingPlatformStoreId())::getList([
                    'select' => ['CODE', 'NAME'],
                    'group' => ['CODE'],
                    'order' => ['NAME' => 'ASC']
                ]);
            if ($rsProductProps->getSelectedRowsCount() > 0) {
                while ($ar = $rsProductProps->fetch()) {
                    self::$arParamsListCache[$this->getTradingPlatformStoreId()][$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
            } else {
                self::$arParamsListCache[$this->getTradingPlatformStoreId()] = [];
            }
        }

        $arFields['shop__offers__offer__param'] = (new Field\Select())->setTitle('Характеристики товара')
            ->setDescription('Выберите характеристики, которые необходимо передавать в <i class="font-weight-bold">Price.ru</i>.')
            ->setName('HANDLER_RULES[shop][offers][offer][param]')
            ->setIsMultiple()
            ->setOptions(['#ALL' => 'Передавать все характеристики'] + self::$arParamsListCache[$this->getTradingPlatformStoreId()])
            ->setDefaultOption('-- Выберите параметры --')
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['param'] ?? ['#ALL']);

        return $arFields;
    }

    protected function getOfferDefaultFields()
    {
        $arFields = [];
        $arFields['shop__offers__offer__@attr__id'] = (new Field\Resource())->setTitle('Идентификатор предложения')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][@attr][id]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['@attr']['id'] ?? ['TYPE' => Field\Resource::TYPE_SOURCE, Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID']);

        $arFields['shop__offers__offer__@attr__available'] = (new Field\Resource())->setTitle('Наличие товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][@attr][available]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                'Y' => 'В наличии',
                'N' => 'Нет в наличии',
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
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#FULL_NAME'
                ]);

        $arFields['shop__offers__offer__description'] = (new Field\Resource())->setTitle('Описание товарного предложения')
            ->setDescription('Укажите здесь характеристики, цвет, размер, материал, особенности – только
существенные признаки, описывающие товар. Не указывайте здесь условия акций, доставки, гарантии и т.п.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][description]')
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

        $arFields['shop__offers__offer__url'] = (new Field\Resource())->setTitle('Ссылка на страницу товарного предложения')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][url]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['url'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#DETAIL_URL_WITH_UTM'
                ]);

        $arFields['shop__offers__offer__price'] = (new Field\Resource())->setTitle('Цена товарного предложения')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][price]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['price'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRICE'
                ]);

        $arFields['shop__offers__offer__oldprice'] = (new Field\Resource())->setTitle('Старая цена товара')
            ->setDescription(' Старая цена должна быть выше текущей.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][oldprice]')
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

        $strRouteReferenceCurrency = Route::getRouteTo('development', 'references').'#currency';
        $arFields['shop__offers__offer__currencyId'] = (new Field\Resource())->setTitle('Код валюты')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][currencyId]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['currencyId'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#CURRENCY_CODE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue('Должен быть передан <b>символьный код валюты из справочника валют</b>.<br/>Справочник - <a href="'.$strRouteReferenceCurrency
                                                          .'" target="_blank">https://robofeed.ru'.$strRouteReferenceCurrency.'</a>'));

        $arFields['shop__offers__offer__vendor'] = (new Field\Resource())->setTitle('Производитель товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][vendor]')
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

        $arFields['shop__offers__offer__model'] = (new Field\Resource())->setTitle('Модель товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][model]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['sales_notes'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MODEL'
                ]);

        $arFields['shop__offers__offer__vendorCode'] = (new Field\Resource())->setTitle('Партнамбер (артикул) товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[shop][offers][offer][vendorCode]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['shop']['offers']['offer']['vendorCode'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_CODE'
                ]);

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
            $this->addToTmpExportFile('<?xml version="1.0" encoding="UTF-8"?><priceru_feed date="'.date('Y-m-d H:i').'"><shop>');

            $this->fillShopHeader($obResult);
            $this->fillCurrencies($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->fillCategories($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->addToTmpExportFile('<offers>');
            $this->beginFilterProduct($obResult);
            $this->addToTmpExportFile('</offers>');

            $this->addToTmpExportFile('</shop></priceru_feed>');

        } catch (\Throwable $e) {
            if (!empty($e->getMessage())) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
    }

    protected function fillShopHeader(\Bitrix\Main\Result $obResult)
    {
        $this->addToTmpExportFile('<company>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__company'])).'</company>');
        $this->addToTmpExportFile('<url>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__url'])).'</url>');
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

                    if (!in_array($strCurrencyCode, static::getSupportedCurrency())) {
                        $strCurrencyCode = static::getMainCurrency();
                    }

                    if ($strCurrencyCode == 'RUB') {
                        $strCurrencyCode = 'RUR';
                    }

                    if (!in_array($strCurrencyCode, $arUsedCurrencies)) {
                        $this->addToTmpExportFile('<currency id="'.htmlspecialchars($strCurrencyCode).'" rate="'.(round(\Local\Core\Inner\Currency::getRate($strCurrencyCode, static::getMainCurrency()), 3)).'"/>');
                        $arUsedCurrencies[] = $strCurrencyCode;
                    }
                }

                if (!in_array(((static::getMainCurrency() == 'RUB') ? 'RUR' : static::getMainCurrency()), $arUsedCurrencies)) {
                    $this->addToTmpExportFile('<currency id="'.htmlspecialchars(((static::getMainCurrency() == 'RUB') ? 'RUR' : static::getMainCurrency())).'" rate="1"/>');
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
                $str .= ' parentId="'.(!empty(trim($ar['CATEGORY_PARENT_ID'])) ? htmlspecialchars(trim($ar['CATEGORY_PARENT_ID'])) : 0).'"';
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

                $arOfferXml['_attributes']['id'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__id'], $arExportProductData);
                $arOfferXml['_attributes']['available'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__available'], $arExportProductData) == 'Y') ? 'true' : 'false';

                $arOfferXml['name'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__name'], $arExportProductData);

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__description'], $arExportProductData))) {
                    $arOfferXml['description'] = trim(strip_tags($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__description'], $arExportProductData)));
                }

                $arOfferXml['url'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__url'], $arExportProductData);

                $this->fillPriceAndCurrency($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__price'], $arExportProductData),
                    $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__currencyId'], $arExportProductData),
                    $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__oldprice'], $arExportProductData), $arOfferXml);


                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendor'], $arExportProductData))) {
                    $arOfferXml['vendor'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendor'], $arExportProductData);
                }

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__model'], $arExportProductData))) {
                    $arOfferXml['model'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__model'], $arExportProductData);
                }

                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendorCode'], $arExportProductData))) {
                    $arOfferXml['vendorCode'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vendorCode'], $arExportProductData);
                }

                break;

            case 'ROBOFEED':

                $arOfferXml['_attributes']['id'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__id']);

                $arOfferXml['_attributes']['available'] = ($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__@attr__available']) == 'Y') ? 'true' : 'false';

                $arOfferXml['name'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__name']);

                if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__description']))) {
                    $arOfferXml['description'] = trim(strip_tags($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__description'])));
                }

                $arOfferXml['url'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__url']);

                $this->fillPriceAndCurrency($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__price']),
                    $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__currencyId']), $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__oldprice']),
                    $arOfferXml);

                if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendor']))) {
                    $arOfferXml['vendor'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendor']);
                }

                if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__model']))) {
                    $arOfferXml['model'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__model']);
                }

                if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendorCode']))) {
                    $arOfferXml['vendorCode'] = $funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__vendorCode']);
                }
                break;

            default:
                $arOfferXml = null;
                break;
        }

        if (!is_null($arOfferXml)) {

            /** ***************
             * OFFER ADDITIONAL
             * ************** */
            $arOfferXml['categoryId'] = $arExportProductData['CATEGORY_ID'];

            if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__picture'], $arExportProductData))) {
                $arOfferXml['picture'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__picture'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__barcode'], $arExportProductData))) {
                $arOfferXml['barcode'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__barcode'], $arExportProductData);
            }

            if ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vert'], $arExportProductData) > 0) {
                $arOfferXml['vert']['_attributes']['bid'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__vert'], $arExportProductData) * 100);
            }

            /** *******
             * DELIVERY
             ******** */

            if ($this->getHandlerRules()['shop']['@delivery'] == 'Y') {
                $arOption = [];
                $arOption['cost'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__delivery-options__option__@attr__cost'], $arExportProductData);

                $arOfferXml['delivery-options']['option'] = [
                    '_attributes' => $arOption
                ];
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

            if (!empty($arOfferXml)) {
                $this->addToTmpExportFile($this->convertArrayToString($arOfferXml, 'offer'));

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

        $intNewPrice = \Local\Core\Inner\Currency::convert($intPrice, $strCurrencyCode, $this->getFinalCurrency($strCurrencyCode));
        if (!is_null($intNewPrice)) {
            $arOfferXml['price'] = $intNewPrice;
            $arOfferXml['oldprice'] = \Local\Core\Inner\Currency::convert($intOldPrice, $strCurrencyCode, $this->getFinalCurrency($strCurrencyCode));
            $arOfferXml['currencyId'] = ($this->getFinalCurrency($strCurrencyCode) == 'RUB') ? 'RUR' : $this->getFinalCurrency($strCurrencyCode);
        }

        if (
            $arOfferXml['price'] >= $arOfferXml['oldprice']
        ) {
            unset($arOfferXml['oldprice']);
        }
    }
}