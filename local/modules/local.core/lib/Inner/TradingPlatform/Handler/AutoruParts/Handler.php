<?php

namespace Local\Core\Inner\TradingPlatform\Handler\AutoruParts;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'autoru_parts';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Auto.ru/Запчасти';
    }

    /** @inheritDoc */
    public static function getMainCurrency()
    {
        return 'RUB';
    }

    /** @inheritDoc */
    public static function getSupportedCurrency()
    {
        return ['RUB'];
    }

    /** @inheritDoc */
    public function getExportFileFormat()
    {
        return 'xml';
    }

    protected static $arParamsListCache = null;

    protected function getHandlerFields()
    {
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

        $arRet = [
            '#header_1' => (new Field\Header())->setValue('Данные по товарам'),

            'part__id' => (new Field\Resource())->setTitle('Идентификатор товара в базе продавца')
                ->setName('HANDLER_RULES[part][id]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue( $this->getHandlerRules()['part']['id'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID'
                    ]),

            'part__stores__store' => ( new Field\Resource() )->setTitle('Идентификаторы магазинов, в которых есть товар')
                ->setDescription('Идентификаторы указаны в личном кабинете <i>Авто.ру</i>: Настройки → Пункты продаж и доставка → ID.')
                ->setName('HANDLER_RULES[part][stores][store]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setSimpleField(
                    (new Field\InputText())
                        ->setIsMultiple()
                        ->setIsCanAddNewInput()
                )
                ->setValue($this->getHandlerRules()['part']['stores']['store'] ?? [
                        'TYPE' => Field\Resource::TYPE_IGNORE
                    ])
                ->setEpilog(
                    (new Field\Infoblock())
                        ->setValue(
                            <<<DOCHERE
Допускается перечисление идентификаторов через запятую в случае выбора типа данных <b>"Поле Robofeed XML"</b>.
DOCHERE

                        )
                ),

            'part__part_number' => (new Field\Resource())->setTitle('Код товара в каталоге производителя')
                ->setName('HANDLER_RULES[part][part_number]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue($this->getHandlerRules()['part']['part_number'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_CODE'
                    ]),

            'part__manufacturer' => (new Field\Resource())->setTitle('Название производителя запчасти')
                ->setName('HANDLER_RULES[part][manufacturer]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue($this->getHandlerRules()['part']['manufacturer'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER'
                    ]),

            'part__description' => (new Field\Resource())->setTitle('Описание товара')
                ->setName('HANDLER_RULES[part][description]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue($this->getHandlerRules()['part']['description'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#DESCRIPTION'
                    ]),

            'part__price' => (new Field\Resource())->setTitle('Цена товара')
                ->setName('HANDLER_RULES[part][price]')
                ->setIsRequired()
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE
                ])
                ->setValue($this->getHandlerRules()['part']['price'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRICE'
                    ])
                ->setEpilog(
                    (new Field\Infoblock())
                        ->setValue(
                            <<<DOCHERE
Согласно требованиям <i>Авто.ру</i> цена должна передаваться в Российских рублях. Ввиду этого, если указанная Вами валюта стоимости товара отличается от Российского рубля, то мы принудительно переконвертируем стоимость согласно курсу на текущий момент.
DOCHERE
                        )
                ),

            'part__availability__isAvailable' => (new Field\Resource())->setTitle('Наличие товара')
                ->setName('HANDLER_RULES[part][availability][isAvailable]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue($this->getHandlerRules()['part']['availability']['isAvailable'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IN_STOCK'
                    ]),

            'part__availability__daysFrom' => (new Field\Resource())->setTitle('Минимальное количество дней ожидания заказа')
                ->setName('HANDLER_RULES[part][availability][daysFrom]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setSimpleField( (new Field\InputText()) )
                ->setValue($this->getHandlerRules()['part']['availability']['daysFrom'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'DELIVERY_FIELD#DAYS_FROM#MIN'
                    ]),

            'part__availability__daysTo' => (new Field\Resource())->setTitle('Максимальное количество дней ожидания заказа')
                ->setName('HANDLER_RULES[part][availability][daysTo]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setSimpleField( (new Field\InputText()) )
                ->setValue($this->getHandlerRules()['part']['availability']['daysTo'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'DELIVERY_FIELD#DAYS_TO#MAX'
                    ]),

            'part__properties' => (new Field\Select())->setTitle('Характеристики товара')
                ->setName('HANDLER_RULES[part][properties]')
                ->setIsMultiple()
                ->setOptions(['#ALL' => 'Передавать все характеристики'] + self::$arParamsListCache[$this->getTradingPlatformStoreId()])
                ->setDefaultOption('-- Выберите параметры --')
                ->setValue($this->getHandlerRules()['part']['properties'] ?? ['#ALL'])
                ->setEpilog(
                    (new Field\Infoblock())->setValue(
                        <<<DOCHERE
<blockquote class="blockquote border-warning mb-0">
    Обязательные характеристики:
    <ul>
        <li>для шин: ширина, высота, диаметр;</li>
        <li>
            для дисков: ширина, диаметр, вылет, ступица, количество крепежных отверстий, диаметр расположения крепежных отверстий;
        </li>
        <li>для масел: объем, вязкость;</li>
        <li>для колпаков: диаметр;</li>
        <li>
            для аккумуляторов: емкость, пусковой ток, полярность, длина, ширина, высота.
        </li>
    </ul>
    Объявления, в которых не указаны обязательные характеристики, не выгружаются на сайт.
    <div class="blockquote-footer">
        Авто.ру
    </div>
</blockquote>
DOCHERE
                    )
                ),

            'part__images__image' => (new Field\Resource())->setTitle('Фотографии товара')
                ->setName('HANDLER_RULES[part][images][image]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue($this->getHandlerRules()['part']['images']['image'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IMAGE'
                    ])
                ->setEpilog(
                    (new Field\Infoblock())->setValue(
                        <<<DOCHERE
<blockquote class="blockquote border-warning mb-0">
    При обработке прайс-листа фотографии скачиваются один раз. Чтобы обновить фотографии в объявлении, загрузите их по новым ссылкам. Новые фотографии по старым ссылкам не будут загружены.
    <div class="blockquote-footer">
        Авто.ру
    </div>
</blockquote>
DOCHERE
                    )
                ),

            'part__count' => (new Field\Resource())->setTitle('Количество единиц товара на складе')
                ->setName('HANDLER_RULES[part][count]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SIMPLE,
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setSimpleField( (new Field\InputText()) )
                ->setValue($this->getHandlerRules()['part']['count'] ?? [
                        'TYPE' => Field\Resource::TYPE_IGNORE
                    ]),

            'part__offer_url' => (new Field\Resource())->setTitle('URL ссылка страницы товара на вашем сайте')
                ->setDescription('Указывается только для новых товаров с оплатой за клики.')
                ->setName('HANDLER_RULES[part][offer_url]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setValue($this->getHandlerRules()['part']['offer_url'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#DETAIL_URL_WITH_UTM'
                    ]),

            'part__is_new' => (new Field\Resource())->setTitle('Признак нового товара')
                ->setName('HANDLER_RULES[part][is_new]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setSelectField(
                    (new Field\Select())
                        ->setOptions([
                            'true' => 'Новый товар',
                            'false' => 'Товар Б/У'
                        ])
                )
                ->setValue($this->getHandlerRules()['part']['is_new'] ?? [
                        'TYPE' => Field\Resource::TYPE_SELECT,
                        Field\Resource::TYPE_SELECT.'_VALUE' => 'true'
                    ]),

            'part__is_for_priority' => (new Field\Resource())->setTitle('Способ размещения товара')
                ->setName('HANDLER_RULES[part][is_for_priority]')
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SELECT,
                    Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_IGNORE
                ])
                ->setSelectField(
                    (new Field\Select())
                        ->setOptions([
                            'true' => 'Приоритетное размещение',
                            'false' => 'Обычное размещение'
                        ])
                )
                ->setValue($this->getHandlerRules()['part']['is_for_priority'] ?? [
                        'TYPE' => Field\Resource::TYPE_SELECT,
                        Field\Resource::TYPE_SELECT.'_VALUE' => 'false'
                    ])
                ->setEpilog(
                    (new Field\Infoblock())->setValue(
                        <<<DOCHERE
<blockquote class="blockquote border-warning mb-0">
    Доступно только для б/у товаров с платным размещением. Деньги списываются после подключения услуги в личном кабинете.
    <div class="blockquote-footer">
        Авто.ру
    </div>
</blockquote>
DOCHERE
                    )
                ),

            '#header_2' => (new Field\Header())->setValue('Формирование названия товара'),
            '#header_2.info' => (new Field\Infoblock())->setValue(<<<DOCHERE
<blockquote class="blockquote border-warning">
Чтобы объявление попало в нужную категорию (аккумуляторы, аксессуары, колеса и т. п.), укажите ее в названии товара. Если категория не указана в названии, объявление попадет в категорию <b>Разное</b>. Название категории и запчасти следует указывать полностью, иначе при обработке прайс-листа могут возникнуть ошибки. Например, объявление с названием «Диск торм.» попадет в категорию «Шины и диски», но с названием «Диск тормозной» — в категорию «Тормозные диски».<br/>
Полный список категорий вы можете найти на странице <a href="https://auto.ru/parts/" target="_blank">Запчасти</a>.
<div class="blockquote-footer"> Авто.ру</div>
</blockquote>
Ввиду данного требования мы можем предложить 2 варианта решения данного требования:<br/>
<span class="lead text-warning">1.</span> <b>Сформировать название самостоятельно</b>, согласно требованию <i>Авто.ру</i>, передать в Robofeed XML  в поле <code><nobr>robofeed->offers->offer->fullName</nobr></code> или <code><nobr>robofeed->offers->offer->param</nobr></code> и выбрать необходимое поле как источник данных.<br/>
<span class="lead text-warning">2.</span> <b>Построить название динамически</b> на основании данных, переданных в Robofeed XML. В таком случае необходимо корректно заполнить <code><nobr>robofeed->offers->offer->simpleName</nobr></code> и <code><nobr>robofeed->categories</nobr></code> в Robofeed XML согласно нашим требованиям. Вам будет предложено проставить соответствия между Вашими категориями и категориями <i>Авто.ру</i>, которые мы собрали заранее.<br/>
<br/>
Если Вам не важна категория размещения и Вы допускаете размещения позиций в <b>"Разное"</b> - рекомендуем выбрать способ формирования <b>"Названия товаров сформированы нами"</b> и в название передать источник "<b>Полное название товара</b>".<br/>
<br/>
Если выбран способ формирования <b>"Сформировать название динамически"</b> и у какой либо категории не проставлено соответствие, то при построении экспортного файла у такой категории будет использоваться ее название из Robofeed XML.
DOCHERE
            ),

            'part__title__@data-source' => (new Field\Select())->setTitle('Способ формирования названия')
                ->setName('HANDLER_RULES[part][title][@data-source]')
                ->setIsRequired()
                ->setOptions([
                    'MYSELF' => 'Названия товаров сформированы нами',
                    'DYNAMIC' => 'Сформировать название динамически',
                ])
                ->setDefaultOption('-- Выберите способ формирования --')
                ->setValue( $this->getHandlerRules()['part']['title']['@data-source'] )
                ->setEvent([
                    'onchange' => [
                        'PersonalTradingplatformFormComponent.refreshForm()'
                    ]
                ])
        ];

        switch ( $this->getHandlerRules()['part']['title']['@data-source'] )
        {
            case 'MYSELF':
                $arRet['part__title__fullName'] = (new Field\Resource())->setTitle('Полное название товара')
                    ->setName('HANDLER_RULES[part][title][fullName]')
                    ->setIsRequired()
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_LOGIC
                    ])
                    ->setValue( $this->getHandlerRules()['part']['title']['fullName'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#FULL_NAME'
                    ]);
                break;

            case 'DYNAMIC':
                $arRet['part__title__simpleName'] = (new Field\Resource())->setTitle('Простое название товара')
                    ->setName('HANDLER_RULES[part][title][simpleName]')
                    ->setIsRequired()
                    ->setStoreId($this->getTradingPlatformStoreId())
                    ->setAllowTypeList([
                        Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_LOGIC
                    ])
                    ->setValue( $this->getHandlerRules()['part']['title']['simpleName'] ?? [
                            'TYPE' => Field\Resource::TYPE_SOURCE,
                            Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#SIMPLE_NAME'
                        ]);


                $arRet['part__title__categoryTaxonomy'] = (new Field\Taxonomy())->setTitle('Соответствие категорий')
                    ->setName('HANDLER_RULES[part][title][categoryTaxonomy]')
                    ->setLeftColumn( $this->getStoreCategoriesTaxonomy() )
                    ->setRightColumn( \Local\Core\Inner\TaxonomyData\Base::getData('AutoruCategory') )
                    ->setAction('AutoruCategory')
                    ->setValue( $this->getHandlerRules()['part']['title']['categoryTaxonomy']);
                break;
        }

        $arRet['#header_3'] = (new Field\Header())->setValue('Формирование соответствий марки и модели');
        $arRet['#header_3.info'] = (new Field\Infoblock())->setValue(
            <<<DOCHERE
<i>Авто.ру</i> требует, что бы название марки и модели автомобилей передавались строго в их формате. Для этого Вам необходимо проставить соответствия между Вашими марками и моделями и <i>Авто.ру</i>.
<br/>
<br/>
Вам необходимо указать параметры, в которых Вы передаете марку, модель и года производства транспорта. На основании этих данных будет построена таблица соответствий для заполнения.
<br/>
<br/>
Товары, у которых не будут проставлены соответвия марки и модели, будут передавать данные в том виде, в котором получаем их мы. Отображение товаров на сайте, в таком случае, не гарантируется.
DOCHERE

        );

        $arRet['part__compatibility__@buildType'] = (new Field\Select())->setTitle('Способ передачи марки, модели и года производства')
            ->setName('HANDLER_RULES[part][compatibility][@buildType]')
            ->setIsRequired()
            ->setOptions([
                'ONE_PARAM' => 'Я передаю марку, модель и года производства в одном параметре',
                'MORE_THAN_ONE_PARAM' => 'Я передаю марку, модель и года производства в разных параметрах'
            ])
            ->setDefaultOption('-- Выберите один из вариантов --')
            ->setValue($this->getHandlerRules()['part']['compatibility']['@buildType'])
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);


        if( !\Local\Core\Inner\Store\Base::hasSuccessImport( $this->getTradingPlatformStoreId() ) )
        {
            $arRet['#header_3.successImportError'] = (new Field\Infoblock())
                ->setType(Field\Infoblock::TYPE_ERROR)
                ->setValue('Построить соответствие невозможно - у магазина не было ни одного успешного импорта!');
        }
        else
        {
            $arLeftColumnData = null;
            switch ( $this->getHandlerRules()['part']['compatibility']['@buildType'] )
            {
                case 'ONE_PARAM':

                    $arRet['part__compatibility__@paramSource'] = (new Field\Select())->setTitle('Укажите параметр с маркой, моделью и годом производства')
                        ->setName('HANDLER_RULES[part][compatibility][@paramSource]')
                        ->setIsRequired()
                        ->setOptions(self::$arParamsListCache[$this->getTradingPlatformStoreId()])
                        ->setDefaultOption('-- Выберите параметр --')
                        ->setValue($this->getHandlerRules()['part']['compatibility']['@paramSource'])
                        ->setEvent([
                            'onchange' => [
                                'PersonalTradingplatformFormComponent.refreshForm()'
                            ]
                        ]);

                    if( empty($this->getHandlerRules()['part']['compatibility']['@paramSource']) )
                    {
                        $strAttention = 'Для построение списка соответствий заполните поля:<br/>&middot; '
                                        .$arRet['part__compatibility__@paramSource']->getTitle();

                        $arRet['#markModelBuilderAttention'] = (new Field\Infoblock())
                            ->setType(Field\Infoblock::TYPE_ERROR)
                            ->setValue($strAttention);
                    }
                    else
                    {
                        $arLeftColumnData = $this->getStoreMarkModelOneParamsTaxonomy($this->getHandlerRules()['part']['compatibility']['@paramSource']);
                    }

                    break;

                case 'MORE_THAN_ONE_PARAM':

                    $arRet['part__compatibility__@markSource'] = (new Field\Select())->setTitle('Параметр с маркой')
                        ->setName('HANDLER_RULES[part][compatibility][@markSource]')
                        ->setIsRequired()
                        ->setOptions(self::$arParamsListCache[$this->getTradingPlatformStoreId()])
                        ->setDefaultOption('-- Выберите параметр --')
                        ->setValue($this->getHandlerRules()['part']['compatibility']['@markSource'])
                        ->setEvent([
                            'onchange' => [
                                'PersonalTradingplatformFormComponent.refreshForm()'
                            ]
                        ]);

                    $arRet['part__compatibility__@modelSource'] = (new Field\Select())->setTitle('Параметр с модель')
                        ->setName('HANDLER_RULES[part][compatibility][@modelSource]')
                        ->setIsRequired()
                        ->setOptions(self::$arParamsListCache[$this->getTradingPlatformStoreId()])
                        ->setDefaultOption('-- Выберите параметр --')
                        ->setValue($this->getHandlerRules()['part']['compatibility']['@modelSource'])
                        ->setEvent([
                            'onchange' => [
                                'PersonalTradingplatformFormComponent.refreshForm()'
                            ]
                        ]);

                    $arRet['part__compatibility__@yearSource'] = (new Field\Select())->setTitle('Параметр с годами производства')
                        ->setName('HANDLER_RULES[part][compatibility][@yearSource]')
                        ->setOptions(( ['#I_HAVENT_YEARS' => '-- Я не передаю года --'] + self::$arParamsListCache[$this->getTradingPlatformStoreId()] ) )
                        ->setDefaultOption('-- Выберите параметр --')
                        ->setValue($this->getHandlerRules()['part']['compatibility']['@yearSource'])
                        ->setEvent([
                            'onchange' => [
                                'PersonalTradingplatformFormComponent.refreshForm()'
                            ]
                        ]);


                    if(
                        empty($this->getHandlerRules()['part']['compatibility']['@markSource'])
                        || empty($this->getHandlerRules()['part']['compatibility']['@modelSource'])
                    )
                    {
                        $strAttention = 'Для построение списка соответствий заполните поля:';
                        if( empty($this->getHandlerRules()['part']['compatibility']['@markSource']) )
                        {
                            $strAttention .= '<br/>&middot; '.$arRet['part__compatibility__@markSource']->getTitle();
                        }
                        if( empty($this->getHandlerRules()['part']['compatibility']['@modelSource']) )
                        {
                            $strAttention .= '<br/>&middot; '.$arRet['part__compatibility__@modelSource']->getTitle();
                        }
                        $arRet['#markModelBuilderAttention'] = (new Field\Infoblock())
                            ->setType(Field\Infoblock::TYPE_ERROR)
                            ->setValue($strAttention);
                    }

                    if(
                        !empty( $this->getHandlerRules()['part']['compatibility']['@markSource'] )
                        && !empty( $this->getHandlerRules()['part']['compatibility']['@modelSource'] )
                    )
                    {
                        $arLeftColumnData = $this->getStoreMarkModelMoreThanOneParamsTaxonomy(
                            $this->getHandlerRules()['part']['compatibility']['@markSource'],
                            $this->getHandlerRules()['part']['compatibility']['@modelSource'],
                            $this->getHandlerRules()['part']['compatibility']['@yearSource']
                        );
                    }

                    break;
            }

            if( is_array($arLeftColumnData) )
            {
                $arRet['part__compatibility__@taxonomyData'] = (new Field\Taxonomy())->setTitle('Соответствия марок и моделей')
                    ->setName('HANDLER_RULES[part][compatibility][@taxonomyData]')
                    ->setIsRequired()
                    ->setLeftColumn($arLeftColumnData)
                    ->setRightColumn( \Local\Core\Inner\TaxonomyData\Base::getData('AutoruMarkModel') )
                    ->setAction('AutoruMarkModel')
                    ->setValue($this->getHandlerRules()['part']['compatibility']['@taxonomyData'])
                    ->setIsMultiple();
            }
        }

        return $arRet;
    }

    protected $arStoreCategoriesTaxonomy = null;

    /**
     * Получить категории магазина для таксономии
     *
     * @return array|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getStoreCategoriesTaxonomy()
    {
        if( is_null( $this->arStoreCategoriesTaxonomy ) )
        {
            $this->arStoreCategoriesTaxonomy = [];
            if( \Local\Core\Inner\Store\Base::hasSuccessImport( $this->getTradingPlatformStoreId() ) )
            {

                $rs = \Local\Core\Model\Robofeed\StoreCategoryFactory::factory( \Local\Core\Inner\Store\Base::getLastSuccessImportVersion( $this->getTradingPlatformStoreId() ) )
                    ->setStoreId( $this->getTradingPlatformStoreId() )::getList([
                        'select' => ['CATEGORY_ID', 'CATEGORY_NAME', 'CATEGORY_PARENT_ID'],
                        'order' => ['CATEGORY_PARENT_ID' => 'ASC', 'CATEGORY_NAME' => 'ASC']
                    ]);
                $arTmpCategory = [];
                while ($ar = $rs->fetch()) {

                    $arTmpCategory[] = [
                        'ID' => $ar['CATEGORY_ID'],
                        'NAME' => $ar['CATEGORY_NAME'],
                        'PARENT_ID' => $ar['CATEGORY_PARENT_ID'],
                    ];
                }

                if (!empty($arTmpCategory)) {

                    $arVals = $this->getCatChild(null, $arTmpCategory);

                    foreach ($arVals as $value => $label)
                    {
                        $this->arStoreCategoriesTaxonomy[ $value ] = $label;
                    }

                }
                unset($arTmpCategory);
            }
        }

        return $this->arStoreCategoriesTaxonomy;
    }
    protected function getCatChild($intParentId, $arTmpCategory, $parentStrLvl = '')
    {
        $arReturn = [];
        foreach ($arTmpCategory as $val) {
            if ($val['PARENT_ID'] == $intParentId) {
                $strChainName = ( !empty( $parentStrLvl ) ? $parentStrLvl.' / ' : '' ).htmlspecialchars($val['NAME']);
                $arReturn[ $val['ID'] ] = $strChainName;

                $arChilds = $this->getCatChild($val['ID'], $arTmpCategory, $strChainName );

                if (!empty($arChilds)) {
                    $arReturn += $arChilds;
                }
            }
        }

        return $arReturn;
    }

    /**
     * Получить таксономию левой колонки марки модели, если данные хранятся в одном св-ве
     *
     * @param $strPropCode
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getStoreMarkModelOneParamsTaxonomy($strPropCode)
    {
        $arReturn = [];

        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        if(
            $obCache->startDataCache(
                (60*60*24*7),
                __METHOD__.'#'.__LINE__.'#'.$strPropCode,
                \Local\Core\Inner\Cache::getCachePath(
                    ['Inner', 'TradingPlatform', 'Handler', 'AutoruParts', 'Handler'],
                    [
                        'MarkModelTaxonomy',
                        'storeId='.$this->getTradingPlatformStoreId(),
                        'oneParamCode='.$strPropCode,
                    ])
            )
        )
        {
            $obStoreParamClass = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(\Local\Core\Inner\Store\Base::hasSuccessImport( $this->getTradingPlatformStoreId() ))->setStoreId($this->getTradingPlatformStoreId());

            $rsMarkModels = $obStoreParamClass::getList([
                'filter' => [
                    'CODE' => $strPropCode,
                    '!VALUE' => false
                ],
                'order' => ['VALUE' => 'ASC'],
                'group' => ['VALUE'],
                'select' => [
                    'VALUE'
                ]
            ]);
            if( $rsMarkModels->getSelectedRowsCount() > 0 )
            {
                while ($ar = $rsMarkModels->fetch())
                {
                    $arReturn[ trim($ar['VALUE']) ] = trim($ar['VALUE']);
                }
            }
            else
            {
                $obCache->abortDataCache();
            }

        }
        else
        {
            $arReturn = $obCache->getVars();
        }

        return $arReturn;
    }

    /**
     * Получить таксономию левой колонки марки модели, если данные хранятся в разных св-вах
     *
     * @param      $strMarkCode
     * @param      $strModelCode
     * @param null $strYearCode
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getStoreMarkModelMoreThanOneParamsTaxonomy($strMarkCode, $strModelCode, $strYearCode = null)
    {
        $arReturn = [];

        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        if(
            $obCache->startDataCache(
                (60*60*24*7),
                __METHOD__.'#'.__LINE__.'#'.$strMarkCode.'#'.$strModelCode.'#'.$strYearCode,
                \Local\Core\Inner\Cache::getCachePath(
                    ['Inner', 'TradingPlatform', 'Handler', 'AutoruParts', 'Handler'],
                    [
                        'MarkModelTaxonomy',
                        'storeId='.$this->getTradingPlatformStoreId(),
                        'markCode='.$strMarkCode,
                        'modelCode='.$strModelCode,
                        'yearCode='.$strYearCode,
                    ])
            )
        )
        {
            $obStoreParamClass = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(\Local\Core\Inner\Store\Base::hasSuccessImport( $this->getTradingPlatformStoreId() ))->setStoreId($this->getTradingPlatformStoreId());

            $rsMarks = $obStoreParamClass::getList([
                'filter' => [
                    'CODE' => $strMarkCode,
                    '!VALUE' => false
                ],
                'group' => ['PRODUCT_ID', 'VALUE'],
                'select' => [
                    'PRODUCT_ID',
                    'VALUE'
                ]
            ]);
            while ($ar = $rsMarks->fetch())
            {
                $arReturn[ $ar['PRODUCT_ID'] ] = mb_strtoupper(trim($ar['VALUE']));
            }

            if( !empty( $arReturn ) )
            {
                $rsModels = $obStoreParamClass::getList([
                    'filter' => [
                        'CODE' => $strModelCode,
                        '!VALUE' => false
                    ],
                    'group' => ['PRODUCT_ID', 'VALUE'],
                    'select' => [
                        'PRODUCT_ID',
                        'VALUE'
                    ]
                ]);
                while ($ar = $rsModels->fetch())
                {
                    if(
                        !empty( $arReturn[ $ar['PRODUCT_ID'] ] )
                        && !empty( trim($ar['VALUE']) )
                    )
                    {
                        $arReturn[ $ar['PRODUCT_ID'] ] .= ' '.mb_strtoupper(trim($ar['VALUE']));
                    }
                }

                if( !empty($strYearCode) )
                {

                    $rsYears = $obStoreParamClass::getList([
                        'filter' => [
                            'CODE' => $strYearCode,
                            '!VALUE' => false
                        ],
                        'group' => ['PRODUCT_ID', 'VALUE'],
                        'select' => [
                            'PRODUCT_ID',
                            'VALUE'
                        ]
                    ]);
                    while ($ar = $rsYears->fetch())
                    {
                        if(
                            !empty( $arReturn[ $ar['PRODUCT_ID'] ] )
                            && !empty( trim($ar['VALUE']) )
                        )
                        {
                            $arReturn[ $ar['PRODUCT_ID'] ] .= ' '.mb_strtoupper(trim($ar['VALUE']));
                        }
                    }
                }


                $arReturn = array_unique($arReturn);
                sort($arReturn);

                $arReturn = array_combine($arReturn, $arReturn);
            }
            $obCache->endDataCache($arReturn);
        }
        else
        {
            $arReturn = $obCache->getVars();
        }

        return $arReturn;
    }

    /** @inheritDoc */
    protected function executeMakeExportFile(\Bitrix\Main\Result $obResult)
    {
        try {
            $this->addToTmpExportFile('<?xml version="1.0" encoding="UTF-8"?><parts>');

            $this->beginFilterProduct($obResult);

            $this->addToTmpExportFile('</parts>');

        } catch (\Throwable $e) {
            if (!empty($e->getMessage())) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
    }

    /** @inheritDoc */
    protected function beginOfferForeachBody(\Bitrix\Main\Result $obResult, $arExportProductData)
    {
        $arLog = $obResult->getData();
        $arLog['PRODUCTS_TOTAL']++;

        $arOfferXml = [];

        try
        {
            if (is_null($this->extractFilledValueFromRule($this->getFields()['part__title__@data-source'], $arExportProductData)))
            {
                throw new \Exception();
            }
            switch ($this->extractFilledValueFromRule($this->getFields()['part__title__@data-source'], $arExportProductData))
            {
                case 'MYSELF':

                    if( is_null($this->extractFilledValueFromRule($this->getFields()['part__title__fullName'], $arExportProductData)) )
                    {
                        throw new \Exception();
                    }

                    $arOfferXml['title'] = $this->extractFilledValueFromRule($this->getFields()['part__title__fullName'], $arExportProductData);

                break;
                case 'DYNAMIC':

                    $strName = $this->extractFilledValueFromRule($this->getFields()['part__title__simpleName'], $arExportProductData);
                    $strSection = $this->extractFilledValueFromRule($this->getFields()['part__title__categoryTaxonomy'], $arExportProductData['CATEGORY_ID']);

                    if( is_null($strName) )
                    {
                        throw new \Exception();
                    }

                    if( is_null($strSection) )
                    {
                        $strSection = ( new Field\Resource() )
                            ->setName('qq')
                            ->setAllowTypeList([
                                Field\Resource::TYPE_SOURCE
                            ])
                            ->setStoreId($this->getTradingPlatformStoreId())
                            ->extractValue(
                                [
                                    'TYPE' => Field\Resource::TYPE_SOURCE,
                                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#CATEGORY_NAME'
                                ], $arExportProductData);

                        if( is_null($strSection) )
                        {
                            throw new \Exception();
                        }
                    }

                    $arOfferXml['title'] = $strSection.' '.$strName;

                    break;
            }


            if (is_null($this->extractFilledValueFromRule($this->getFields()['part__compatibility__@buildType'], $arExportProductData)))
            {
                throw new \Exception();
            }
            $strLeftColumnValue = null;
            switch ($this->extractFilledValueFromRule($this->getFields()['part__compatibility__@buildType'], $arExportProductData))
            {
                case 'ONE_PARAM':
                    if( !is_null($this->extractFilledValueFromRule($this->getFields()['part__compatibility__@paramSource'], $arExportProductData)) )
                    {
                        $tmpVal = $arExportProductData['PARAMS'][ $this->extractFilledValueFromRule($this->getFields()['part__compatibility__@paramSource'], $arExportProductData) ]['VALUE'];
                        if( !is_null($tmpVal) )
                        {
                            $strLeftColumnValue = $tmpVal;
                        }
                    }
                    break;

                case 'MORE_THAN_ONE_PARAM':
                    $arTmpData = [];
                    if(
                        !is_null($this->extractFilledValueFromRule($this->getFields()['part__compatibility__@markSource'], $arExportProductData))
                        && !is_null($this->extractFilledValueFromRule($this->getFields()['part__compatibility__@modelSource'], $arExportProductData))
                    )
                    {
                        $arTmpData[] = $arExportProductData['PARAMS'][ $this->extractFilledValueFromRule($this->getFields()['part__compatibility__@markSource'], $arExportProductData) ]['VALUE'];
                        $arTmpData[] = $arExportProductData['PARAMS'][ $this->extractFilledValueFromRule($this->getFields()['part__compatibility__@modelSource'], $arExportProductData) ]['VALUE'];
                        if( $this->extractFilledValueFromRule($this->getFields()['part__compatibility__@yearSource'], $arExportProductData) != '#I_HAVENT_YEARS' )
                        {
                            $arTmpData[] = $arExportProductData['PARAMS'][ $this->extractFilledValueFromRule($this->getFields()['part__compatibility__@yearSource'], $arExportProductData) ]['VALUE'];
                        }
                        $arTmpData = array_diff($arTmpData, [''], [null]);
                        if( !empty($arTmpData) )
                        {
                            $strLeftColumnValue = implode(' ', $arTmpData);
                        }
                    }

                    break;
            }

            if(
                !is_null($strLeftColumnValue)
                && !is_null($this->extractFilledValueFromRule($this->getFields()['part__compatibility__@taxonomyData'], mb_strtoupper($strLeftColumnValue)))
            )
            {
                $arOfferXml['compatibility']['car'] = $this->extractFilledValueFromRule($this->getFields()['part__compatibility__@taxonomyData'], mb_strtoupper($strLeftColumnValue));
            }
            else
            {
                $arOfferXml['compatibility']['car'] = $strLeftColumnValue;
            }



            if( is_null($this->extractFilledValueFromRule($this->getFields()['part__price'], $arExportProductData)) )
            {
                throw new \Exception();
            }


            $intNewPrice = \Local\Core\Inner\Currency::convert(
                $this->extractFilledValueFromRule($this->getFields()['part__price'], $arExportProductData),
                $arExportProductData['CURRENCY_CODE'],
                $this->getFinalCurrency($arExportProductData['CURRENCY_CODE'])
            );

            $arOfferXml['price'] = (int)ceil($intNewPrice);


            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__id'], $arExportProductData)))
            {
                $arOfferXml['id'] = $this->extractFilledValueFromRule($this->getFields()['part__id'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__stores__store'], $arExportProductData)))
            {
                $arOfferXml['stores']['store'] = $this->extractFilledValueFromRule($this->getFields()['part__stores__store'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__part_number'], $arExportProductData)))
            {
                $arOfferXml['part_number'] = $this->extractFilledValueFromRule($this->getFields()['part__part_number'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__manufacturer'], $arExportProductData)))
            {
                $arOfferXml['manufacturer'] = $this->extractFilledValueFromRule($this->getFields()['part__manufacturer'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__description'], $arExportProductData)))
            {
                $arOfferXml['description'] = strip_tags($this->extractFilledValueFromRule($this->getFields()['part__description'], $arExportProductData));
            }

            if(!is_null($this->extractFilledValueFromRule($this->getFields()['part__availability__isAvailable'], $arExportProductData)))
            {
                $arAvailability = [
                    'isAvailable' => ( $this->extractFilledValueFromRule($this->getFields()['part__availability__isAvailable'], $arExportProductData) == 'Y' ) ? 'true' : 'false'
                ];
                if(!is_null($this->extractFilledValueFromRule($this->getFields()['part__availability__daysFrom'], $arExportProductData)))
                {
                    $arAvailability['daysFrom'] = $this->extractFilledValueFromRule($this->getFields()['part__availability__daysFrom'], $arExportProductData);
                }
                if(!is_null($this->extractFilledValueFromRule($this->getFields()['part__availability__daysTo'], $arExportProductData)))
                {
                    $arAvailability['daysTo'] = $this->extractFilledValueFromRule($this->getFields()['part__availability__daysTo'], $arExportProductData);
                }

                $arOfferXml['availability'] = $arAvailability;
            }


            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__properties'], $arExportProductData)))
            {
                foreach ($this->extractFilledValueFromRule($this->getFields()['part__properties'], $arExportProductData) as $propCode)
                {
                    if( !empty( $arExportProductData['PARAMS'][$propCode]['VALUE'] ) )
                    {
                        $arOfferXml['properties']['property'][] = [
                            '_attributes' => [
                                'name' => $arExportProductData['PARAMS'][$propCode]['NAME']
                            ],
                            '_value' => $arExportProductData['PARAMS'][$propCode]['VALUE'],
                        ];
                    }
                }
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__images__image'], $arExportProductData)))
            {
                $arOfferXml['images']['image'] = $this->extractFilledValueFromRule($this->getFields()['part__images__image'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__count'], $arExportProductData)))
            {
                $arOfferXml['count'] = $this->extractFilledValueFromRule($this->getFields()['part__count'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__offer_url'], $arExportProductData)))
            {
                $arOfferXml['offer_url'] = $this->extractFilledValueFromRule($this->getFields()['part__offer_url'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__is_new'], $arExportProductData)))
            {
                $arOfferXml['is_new'] = $this->extractFilledValueFromRule($this->getFields()['part__is_new'], $arExportProductData);
            }

            if (!is_null($this->extractFilledValueFromRule($this->getFields()['part__is_for_priority'], $arExportProductData)))
            {
                $arOfferXml['is_for_priority'] = $this->extractFilledValueFromRule($this->getFields()['part__is_for_priority'], $arExportProductData);
            }


            if (!empty($arOfferXml)) {
                $this->addToTmpExportFile($this->convertArrayToString($arOfferXml, 'part'));
                $arLog['PRODUCTS_EXPORTED']++;
            }
        }
        catch (\Exception $e)
        {
            unset($arOfferXml);
        }

        $obResult->setData($arLog);
    }
}