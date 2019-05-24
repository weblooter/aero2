<?php

namespace Local\Core\Inner\TradingPlatform\Handler\GoogleMerchant;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'google_merchant';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Google Merchant Center';
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
        return $this->getChannelBaseFields() + $this->getChannelItemFields();
    }

    protected function getChannelBaseFields()
    {
        $arShopFields = [
            '#header_y1' => (new Field\Header())->setValue('Настройки магазина'),

            'channel__title' => (new Field\InputText())->setTitle('Короткое название магазина')
                ->setName('HANDLER_RULES[channel][name]')
                ->setIsRequired()
                ->setPlaceholder('Рога и копыта')
                ->setValue($this->getHandlerRules()['channel']['name']),

            'channel__link' => (new Field\InputText())->setTitle('Ссылка на сайт')
                ->setName('HANDLER_RULES[channel][link]')
                ->setIsRequired()
                ->setPlaceholder('https://www.example.com')
                ->setValue($this->getHandlerRules()['channel']['link']),

            'channel__description' => (new Field\InputText())->setTitle('Краткое описание магазина')
                ->setName('HANDLER_RULES[channel][description]')
                ->setIsRequired()
                ->setPlaceholder('Кратная информация о магазине и виде товаров')
                ->setValue($this->getHandlerRules()['channel']['description']),
        ];

        return $arShopFields;
    }

    protected static $arParamsListCache;

    protected function getChannelItemFields()
    {
        $arFields = [];

        $arFields = $this->getOfferBaseFields() + $this->getOfferPriceFields() + $this->getOfferCategoryFields() + $this->getOfferIdFields() + $this->getOfferDescriptionFields() + $this->getOfferParamsFields();

        return $arFields;
    }

    protected function getOfferBaseFields()
    {
        $arFields = [];

        $arFields = [
            '#header_y2' => (new Field\Header())->setValue('Основные сведения о товарах')
        ];

        $arFields['channel__item__g:id'] = (new Field\Resource())->setTitle('Уникальный идентификатор товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:id]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:id'] ?? ['TYPE' => Field\Resource::TYPE_SOURCE, Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID']);

        $arFields['channel__item__g:item_group_id '] = (new Field\Resource())->setTitle('Идентификатор группы товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setDescription('Используется, чтобы группировать варианты товара. Варианты – это позиции, которые в целом похожи, но различаются некоторыми атрибутами, такими как размер, цвет, материал, узор, возрастная группа, тип размера, система размеров и т.п.')
            ->setName('HANDLER_RULES[channel][item][g:item_group_id]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:item_group_id'] ?? [
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


        $arFields['channel__item__g:title'] = (new Field\Resource())->setTitle('Название товара')
            ->setDescription('Пример: Рубашка поло, мужская, пике. Не более 150 символов.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:title]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:title'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#FULL_NAME'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning">
<i class="zmdi zmdi-minus text-warning"></i> Название не должно содержать рекламный текст, например "бесплатная доставка". Также нельзя писать его заглавными буквами и использовать знаки иностранной письменности для привлечения внимания.<br/>
<i class="zmdi zmdi-minus text-warning"></i> Для информационной продукции, в том числе книг, перед названием необходимо указывать возраст целевой аудитории.<br/>
<br/>
<b>Рекомендации</b><br/>
<i class="zmdi zmdi-minus text-warning"></i> Используйте все 150 символов. Чем полнее вы охарактеризуете товар, тем чаще будет отображаться объявление. Постарайтесь не упустить ни одну важную деталь.<br/>
<i class="zmdi zmdi-minus text-warning"></i> Укажите самые важные характеристики в начале названия. Дело в том, что на большинстве экранов отображается не более 70 первых символов названия.<br/>
<i class="zmdi zmdi-minus text-warning"></i> Включите в название ключевые слова. Тогда пользователям будет легко найти ваше объявление и понять, что представляет из себя ваш товар. Вот что можно указать в ключевых словах:
<ul>
<li>название товара;</li>
<li>марку;</li>
<li>видовую характеристику товара, например "для беременных" (одежда), "водостойкая" (тушь).</li>
</ul>
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
            ));

        $arFields['channel__item__g:description'] = (new Field\Resource())->setTitle('Описание товара')
            ->setDescription('Не более 5000 символов.')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:description]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:description'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#DESCRIPTION'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning">
<i class="zmdi zmdi-minus text-warning"></i> Описание не должно содержать рекламный текст, например "бесплатная доставка". Также нельзя писать его заглавными буквами и использовать знаки иностранной письменности для привлечения внимания.<br/>
<i class="zmdi zmdi-minus text-warning"></i> В описании не должно быть ссылок на сайт магазина, сведений о продаже, а также информации о конкурентах, других товарах и аксессуарах к ним.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
            ));

        $arFields['channel__item__g:link'] = (new Field\Resource())->setTitle('Ссылка на целевую страницу товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:link]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:link'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'CUSTOM_FIELD#DETAIL_URL_WITH_UTM'
                ]);

        $arFields['channel__item__g:images'] = (new Field\Resource())->setTitle('Ссылки на изображения товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:images]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:images'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IMAGE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
<i class="lead font-weight-bold">Google Merchant Center</i> требует наличие хотя бы одного изображения товара. Если у товара не задано изображение - он будет пропущен.
DOCHERE
            ));

        return $arFields;
    }

    protected function getOfferPriceFields()
    {
        $arFields = [];

        $arFields['#header_y3'] = (new Field\Header())->setValue('Цена и наличие');

        $arFields['channel__item__g:availability'] = (new Field\Resource())->setTitle('Наличие товара в магазине')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:availability]')
            ->setIsRequired()
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                'Y' => 'В наличии',
                'N' => 'Нет в наличии',
                'P' => 'Предзаказ',
            ]))
            ->setValue($this->getHandlerRules()['channel']['item']['g:availability'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#IN_STOCK'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Если Вы выберете тип <b>"Поле Robofeed XML"</b> со значением <b>"Признак наличия товара"</b> то стоит учитывать, что мы по умолчанию выставляем значения либо "В наличии", либо "Нет в наличии" в зависимости от передаваемых значений. Если у Вам есть товары со статусом "Предзаказ", то стоит воспользоваться типом <b>"Сложное условие"</b> и проставить условия, при которых необходимо помечать товар как "Предзаказ".
DOCHERE
            ));

        $arFields['channel__item__@price'] = (new Field\Resource())->setTitle('Цена товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][@price]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['@price'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRICE'
                ]);

        $arFields['channel__item__@oldprice'] = (new Field\Resource())->setTitle('Старая цена товара')
            ->setDescription('Должна быть выше текущей.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][@oldprice]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['@oldprice'] ?? [
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
        $arFields['channel__item__@currencyId'] = (new Field\Resource())->setTitle('Валюта, в которой указана цена товара')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][@currencyId]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['@currencyId'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#CURRENCY_CODE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue('Должен быть передан <b>символьный код валюты из справочника валют</b>.<br/>Справочник - <a href="'.$strRouteReferenceCurrency.'" target="_blank">https://robofeed.ru'.$strRouteReferenceCurrency.'</a>'));

        return $arFields;
    }

    protected function getOfferCategoryFields()
    {
        $arFields = [];

        $arFields['#header_g3'] = (new Field\Header())->setValue('Категории товаров');


        $arFields['channel__item__g:google_product_category'] = (new Field\Taxonomy())->setTitle('Категория товара по классификации Google')
            ->setName('HANDLER_RULES[channel][item][g:google_product_category]')
            ->setLeftColumn($this->getStoreCategoriesTaxonomy())
            ->setRightColumn(\Local\Core\Inner\TaxonomyData\Base::getData('GoogleMerchantCategory'))
            ->setAction('GoogleMerchantCategory')
            ->setValue($this->getHandlerRules()['channel']['item']['g:google_product_category']);

        $arFields['#header_g3.info'] = (new Field\Infoblock())
            ->setValue(<<<DOCHERE
Проставление соответствий между Вашими категориями и классификацией Google необязательно — мы в любом случае также передаем Вашу цепочку категорий.<br/>
Однако стоит понимать, что чем точнее Вы заполните и передадите данные о товаре — тем эффективней будет реклама. Мы рекомендуем потратить время и проставить соответствия.<br/>
<br/>
Если в настройках данной торговой пощадки Вы использовали фильтрацию по товарам, то настраивать все соответствия не нужно - достаточно настроить соответствия тех категорияй, чьи товары попадут в экспортный файл.<br/>
Если Вы не можете найти классификацию своей категории при помощи функционала поиска, то мы рекомендуем Вам ознакомиться с полным списком классификаций Google, который доступен по ссылке <a href="/local/docs/Taxonomy/GoogleMetchantCategoryTaxonomy.txt" target="_blank">https://robofeed.ru/local/docs/Taxonomy/GoogleMetchantCategoryTaxonomy.txt</a>
DOCHERE
            );


        return $arFields;
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
        if (is_null($this->arStoreCategoriesTaxonomy)) {
            $this->arStoreCategoriesTaxonomy = [];
            if (\Local\Core\Inner\Store\Base::hasSuccessImport($this->getTradingPlatformStoreId())) {

                $rs = \Local\Core\Model\Robofeed\StoreCategoryFactory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion($this->getTradingPlatformStoreId()))
                    ->setStoreId($this->getTradingPlatformStoreId())::getList([
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

                    foreach ($arVals as $value => $label) {
                        $this->arStoreCategoriesTaxonomy[$value] = $label;
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
                $strChainName = (!empty($parentStrLvl) ? $parentStrLvl.' / ' : '').htmlspecialchars($val['NAME']);
                $arReturn[$val['ID']] = $strChainName;

                $arChilds = $this->getCatChild($val['ID'], $arTmpCategory, $strChainName);

                if (!empty($arChilds)) {
                    $arReturn += $arChilds;
                }
            }
        }
        return $arReturn;
    }


    protected function getOfferIdFields()
    {
        $arFields = [];

        $arFields['#header_y4'] = (new Field\Header())->setValue('Идентификаторы товара');

        $strFolderPath = str_replace(\Bitrix\Main\Application::getDocumentRoot(), '', dirname(__FILE__)).'/assets';
        $arFields['#info4'] = (new Field\Infoblock())->setValue(<<<DOCHERE
Давайте немного остановимся и внесем ясность в заполнение следующих полей.<br/>
Для каждого товара <i class="lead font-weight-bold">Google Merchant Center</i> требует передавать следующую идентификаторы:<br/>
- Марку / Название производителя.<br/>
- Номер GTIN <u>для новых товаров</u>, <b>кроме фильмов, книг и музыки</b>.<br/>
- Если у товара нет номера GTIN - передавать код производителя товара <u>для новых товаров</u>. Но, как показывает практика - лучше передавать вне зависимости, если или нет у товара код GTIN.<br/>
<br/>
Если Вы не знаете, что такое GTIN и где его искать, то следующая информация для Вас:<br/>
<div class="blockquote border-warning mt-3">
<b>GTIN</b> (англ. Global Trade Item Number) — международный код маркировки и учёта логистических единиц, разработанный и поддерживаемый GS1. Предложен для замены американского UPC и европейского EAN. GTIN-код имеет длину 8, 12, 13 или 14 цифр, каждая из схем построена по аналогу с предыдущими стандартами и включает в себя префикс компании, код товара и контрольную цифру. GTIN-8 кодируются в штрихкод EAN-8; GTIN-12 могут использовать штрихкод форматов UPC-A, ITF-14 или GS1-128. GTIN-13 кодируются как EAN-13, ITF-14 или GS1-128; а GTIN-14 кодируются в ITF-14 или GS1-128, в зависимости от назначения.
<div class="blockquote-footer">wikipedia.org, <a href="https://ru.wikipedia.org/wiki/GTIN" target="_blank">https://ru.wikipedia.org/wiki/GTIN</a></div>
</div>

<div class="blockquote border-warning mt-3">
В качестве значения атрибута <b>GTIN</b> принимаются следующие виды маркировки: <br/>
<i class="zmdi zmdi-minus text-warning"></i> UPC (GTIN-12) (в Северной Америке): 12-значный номер. Восьмизначные коды UPC-E необходимо конвертировать в 12-значные UPC-A.<br/>
<i class="zmdi zmdi-minus text-warning"></i> EAN (GTIN-13) (в Европе) – 13-значный номер.<br/>
<i class="zmdi zmdi-minus text-warning"></i> JAN (GTIN-13) (в Японии) – 8- или 13-значный номер.<br/>
<i class="zmdi zmdi-minus text-warning"></i> ISBN (для книг): 13-значный номер. Код ISBN-10 нужно конвертировать в формат ISBN-13. Если у вас есть оба этих кода, указывайте только 13-значный. <br/>
<i class="zmdi zmdi-minus text-warning"></i> ITF-14 (GTIN-14) (для мультиупаковок) – 14-значный номер.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
<a href="javascript:void(0)" class="btn btn-secondary" data-toggle="collapse" data-target="#collapseGoogleMerchantGTIN">Показать примеры заполнения данных и расположения GTIN</a>
<div class="container-fluid collapse" id="collapseGoogleMerchantGTIN">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Одежда</h4>
            <img src="$strFolderPath/gtin1.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>Little Black Dress</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>3234567890126</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>-</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Еда и напитки</h4>
            <img src="$strFolderPath/gtin2.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>Дедушкина козья ферма</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>3234567890126</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>-</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Печатные СМИ и книги</h4>
            <img src="$strFolderPath/gtin3.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>9781455582341</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>-</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Электроника</h4>
            <img src="$strFolderPath/gtin4.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>Google</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>7894892017139</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>H2G2-42</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Мобильные телефоны и планшеты</h4>
            <img src="$strFolderPath/gtin5.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>Google</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>0614141123452</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>00638NAGPE</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Товары собственного производства</h4>
            <img src="$strFolderPath/gtin8.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>Мой магазин</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>0614141123452</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>BM2JE9SU</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h4 class="text-center">Варианты</h4>
            <img src="$strFolderPath/gtin7.png" class="m-auto d-block" height="150" /><br/>
            <p>Если вы предлагаете товар разных цветов, размеров или других различающихся параметров - указывайте для вариантов свои уникальные идентификаторы.</p>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>Google</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>9504000059422</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>00638HAY</td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <h4 class="text-center">Товары без номера GTIN, марки и кода производителя</h4>
            <img src="$strFolderPath/gtin6.png" class="m-auto d-block" height="150" /><br/>
            <p>Таким товарам стоит будет проставлен параметр "Идентификатор не имеется" в автоматическом режиме.</p>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>-</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td>-</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br/>
<br/>
Если при создании экспортного файла мы обнаружим товар, у которого нет марки и кода GTIN или кода производителя (к примеру индивидуальные товары под заказ) - такой товар получит отметку "Идентификатор не имеется" в автоматическом режиме. Если же у товара не будет марки, но будет GTIN или код производителя (к примеру Книги) - он будет обработан в обычном режиме.
DOCHERE
        );

        $arFields['channel__item__g:brand'] = (new Field\Resource())->setTitle('Марка / Название производителя')
            ->setDescription('Не более 70 символов.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:brand]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:brand'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Поле обязательно для всех новых товаров у которых есть производитель, кроме фильмов, книг и музыки.
DOCHERE
            ));

        $arFields['channel__item__g:gtin'] = (new Field\Resource())->setTitle('Код международной маркировки и учета логистических единиц для товара (GTIN)')
            ->setDescription('Не более 70 символов.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:gtin]')
            ->setAllowTypeList([
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:gtin'] ?? ['TYPE' => Field\Resource::TYPE_IGNORE])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Поле обязательно для всех новых товаров, у которых есть код GTIN.
DOCHERE
            ));

        $arFields['channel__item__g:mpn'] = (new Field\Resource())->setTitle('Код производителя товара')
            ->setDescription('Не более 70 символов.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:mpn]')
            ->setAllowTypeList([
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:mpn'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#MANUFACTURER_CODE'
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Поле обязательно для новых товаров у которых есть код производителя и нет кода GTIN.
DOCHERE
            ));

        return $arFields;
    }

    protected function getOfferDescriptionFields()
    {
        $arFields = [];

        $arFields['#header_g5'] = (new Field\Header())->setValue('Подробное описание товара');

        $arFields['channel__item__g:condition'] = (new Field\Resource())->setTitle('Состояние товара в момент продажи')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:condition]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setSelectField(
                (new Field\Select())->setOptions([
                    'new' => 'Новый',
                    'refurbished' => 'Восстановленный',
                    'used' => 'Б/у',
                ])
                    ->setDefaultOption('-- Выберите состояние --')
            )
            ->setValue($this->getHandlerRules()['channel']['item']['g:condition'] ?? [
                    'TYPE' => Field\Resource::TYPE_SELECT
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning">
Допустимые значения:<br/>
<br/>
<span class="badge badge-secondary">Новый</span> - совершенно новый товар в исходной нераспечатанной упаковке<br/>
<span class="badge badge-secondary">Восстановленный</span> - товар, который был отремонтирован до исправного состояния и поставляется с гарантией; исходная упаковка может отсутствовать;<br/>
<span class="badge badge-secondary">Б/у</span> - товар ранее использовался, исходная упаковка отсутствует.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
            ));

        $arFields['channel__item__g:adult'] = (new Field\Resource())
            ->setTitle('Товар содержит материалы сексуального характера')
            ->setDescription('Товар относится к категории товаров, которые содержат изображения обнаженного тела или материалы сексуального характера либо предназначены для повышения сексуальной активности.')
            ->setIsRequired()
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:adult]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
            ])
            ->setSelectField((new Field\Select())->setOptions([
                'Y' => 'Да',
                'N' => 'Нет',
            ]))
            ->setValue($this->getHandlerRules()['channel']['item']['g:adult'] ?? [
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
                                            'CLASS_ID' => 'CondProdIsSex',
                                            'DATA' => [
                                                'logic' => 'Equal',
                                                'value' => 'Y',
                                            ],
                                        ],
                                    ],
                                ],
                                'VALUE' => [
                                    'TYPE' => Field\Resource::TYPE_SELECT,
                                    Field\Resource::TYPE_SELECT.'_VALUE' => 'Y'
                                ]
                            ]
                        ],
                        'ELSE' => [
                            'VALUE' => [
                                'TYPE' => Field\Resource::TYPE_SELECT,
                                Field\Resource::TYPE_SELECT.'_VALUE' => 'N'
                            ]
                        ]
                    ]
                ]);

        return $arFields;
    }

    protected function getOfferParamsFields()
    {
        $arFields = [];

        $arFields['#header_g6'] = (new Field\Header())->setValue('Характеристики товара');

        $arFields['channel__item__g:age_group'] = (new Field\Resource())->setTitle('Возрастная группа')
            ->setDescription('Рекомендуется заполнить в случае, если товар предназначени для детей и подростков.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:age_group]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField(
                (new Field\Select())->setOptions([
                    'newborn' => 'Новорожденные / до трех месяцев',
                    'infant' => 'Младенцы / от трех месяцев до года',
                    'toddler' => 'Маленькие дети / от года до пяти лет',
                    'kids' => 'Дети / от 5 до 13 лет',
                    'adult' => 'Взрослые / от 13 лет',
                ])
                    ->setDefaultOption('-- Выберите возрастную группу --')
            )
            ->setValue($this->getHandlerRules()['channel']['item']['g:age_group'] ?? [
                    'TYPE' => Field\Resource::TYPE_SELECT
                ]);

        $arFields['channel__item__g:color'] = (new Field\Resource())->setTitle('Цвет товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:color]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:color'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE
                ])
            ->setEpilog(
                [
                    (new Field\InputText())
                        ->setPlaceholder('Укажите Ваш разделитель')
                        ->setSmallText('Если Ваш разделитель отличается от "/" - укажите и мы заменим его.')
                        ->setName('HANDLER_RULES[channel][item][@colorDelimiter]')
                        ->setValue(trim($this->getHandlerRules()['channel']['item']['@colorDelimiter']))
                    ,
                    (new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning mb-0">
Является обязательным к заполнению для одинаковых товаров, которые <b>отличаются по цвету</b>, а так же для товаров категорий <b>"Предметы одежды и принадлежности"</b>.<br/>
<br/>
Вы можете указать основной цвет товара и до двух дополнительных через косую черту (/), вот так: Red/Green/Black [красный/зеленый/черный]. Использовать запятую (,) нельзя. Например, если вы продаете красные туфли с зелеными и черными вставками, значение должно выглядеть так: Red/Green/Black [красный/зеленый/черный]. Не пишите названия цветов слитно (RedGreenBlack [красно-зелено-черный]). Указывать их через запятую (Red [красный], Green [зеленый], Black [черный]) тоже не следует: в таком случае будет применено только одно значение.<br/>
<br/>
Не обозначайте цвета цифрами: 0 2 4 6 8. Не используйте символы, которые не относятся к буквенно-цифровым, например #fff000.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
                    )
                ]
            );

        $arFields['channel__item__g:gender'] = (new Field\Resource())->setTitle('Пол людей, для которых предназначен товар')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:gender]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField(
                (new Field\Select())->setOptions([
                    'male' => 'Мужской',
                    'female' => 'Женский',
                    'unisex' => 'Унисекс',
                ])
                    ->setDefaultOption('-- Выберите пол --')
            )
            ->setValue($this->getHandlerRules()['channel']['item']['g:gender'] ?? [
                    'TYPE' => Field\Resource::TYPE_LOGIC,
                    Field\Resource::TYPE_LOGIC.'_VALUE' => [
                        'IF' => [
                            [
                                'RULE' => [
                                    'CLASS_ID' => 'CondGroup',
                                    'DATA' => [
                                        'All' => 'AND',
                                        'True' => 'True',
                                    ]
                                ],
                                'VALUE' => [
                                    'TYPE' => Field\Resource::TYPE_SELECT,
                                    Field\Resource::TYPE_SELECT.'_VALUE' => 'male'
                                ]
                            ],
                            [
                                'RULE' => [
                                    'CLASS_ID' => 'CondGroup',
                                    'DATA' => [
                                        'All' => 'AND',
                                        'True' => 'True',
                                    ]
                                ],
                                'VALUE' => [
                                    'TYPE' => Field\Resource::TYPE_SELECT,
                                    Field\Resource::TYPE_SELECT.'_VALUE' => 'female'
                                ]
                            ]
                        ],
                        'ELSE' => [
                            'VALUE' => [
                                'TYPE' => Field\Resource::TYPE_SELECT,
                                Field\Resource::TYPE_SELECT.'_VALUE' => 'unisex'
                            ]
                        ]
                    ]
                ])
            ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning mb-0">
Является обязательным к заполнению для всех товаров, которые предназначены <b>или для мужчин, или для женщин</b>, а так же для товаров категорий <b>"Предметы одежды и принадлежности"</b>.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
            ));

        $arFields['channel__item__g:material'] = (new Field\Resource())->setTitle('Материал, из которого изготовлен товар')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:material]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:material'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE
                ])
            ->setEpilog(
                [
                    (new Field\InputText())
                        ->setPlaceholder('Укажите Ваш разделитель')
                        ->setSmallText('Если Ваш разделитель отличается от "/" - укажите и мы заменим его.')
                        ->setName('HANDLER_RULES[channel][item][@materialDelimiter]')
                        ->setValue(trim($this->getHandlerRules()['channel']['item']['@materialDelimiter']))
                    ,
                    (new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning mb-0">
Является обязательным к заполнению для вариантов товара, которые изготовлены из разных материалов.<br/>
<br/>
Вы можете указать основной и до двух дополнительных через косую черту (/), например: хлопок/полиэстер/эластан.<br/>
<br/>
Лучше избегать сокращений и сложных терминов. Указывайте общепринятые названия материалов, например: телячья кожа, а не теленок, кожа, а не кж.<br/>
<br/>
Не более 200 символов.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
                    )
                ]
            );

        $arFields['channel__item__g:pattern'] = (new Field\Resource())->setTitle('Узор или рисунок на товаре')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:pattern]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_BUILDER,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:pattern'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE
                ])
            ->setEpilog(
                (new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning mb-0">
Является обязательным к заполнению для вариантов товара, которые отличаются узорами, а так же для товаров, узор на которых является важным отличительным признаком.<br/>
<br/>
В качестве значения указывайте узор или изображение на товаре. Не включайте информацию о цвете, размере или материале изделия.<br/>
<br/>
Не более 100 символов.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
                )
            );

        $arFields['channel__item__g:size'] = (new Field\Resource())->setTitle('Размер товара')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:size]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setValue($this->getHandlerRules()['channel']['item']['g:size'] ?? [
                    'TYPE' => Field\Resource::TYPE_SOURCE
                ])
            ->setEpilog([
                (new Field\InputText())
                    ->setPlaceholder('Укажите Ваш разделитель')
                    ->setSmallText('Если Ваш разделитель отличается от "/" - укажите и мы заменим его.')
                    ->setName('HANDLER_RULES[channel][item][@materialDelimiter]')
                    ->setValue(trim($this->getHandlerRules()['channel']['item']['@sizeDelimiter']))
                ,
                (new Field\Infoblock())->setValue(<<<DOCHERE
<div class="blockquote border-warning mb-0">
Является обязательным к заполнению для одинаковых товаров, которые отличаются по размеру, а так же для товаров категорий <b>"Предметы одежды и принадлежности > Одежда"</b> и <b>"Предметы одежды и принадлежности > Обувь"</b>.<br/>
<br/>
Каждому товару может соответствовать <b>только одно значение</b>. Если вы укажете больше, будет применено только первое значение. Остальные мы удалим, и вы получите предупреждение.<br/>
<br/>
Если вы продаете <b>предмет одежды</b>, представленный в разных размерах (например, красную футболку размера S, а также красную футболку размера L), укажите значение для каждого варианта товара. Предоставлять в одном значении несколько размеров через запятую (S, M, L) нельзя. <b>Однако</b> можно перечислить их через косую черту: S/M/L.<br/>
<br/>
<b>Объединяйте компоненты составного размера в одно значение</b>, например 41/86 для размера воротника 41 см и для длины рукава 86 см.<br/>
<br/>
Допускаются числовые размеры и диапазоны размеров: 000–100<br/>
<br/>
Не более 100 символов.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
                )
            ]);

        $arFields['channel__item__g:size_type'] = (new Field\Resource())->setTitle('Тип фигуры, для которой сшит предмет одежды')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:size_type]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField(
                (new Field\Select())
                    ->setOptions([
                        'regular' => 'Стандартный',
                        'petite' => 'Для миниатюрных',
                        'plus' => 'Для полных',
                        'big and tall' => 'Для крупных и высоких',
                        'maternity' => 'Для беременных',
                    ])
                    ->setDefaultOption('-- Выберите тип фигуры --')
            )
            ->setValue($this->getHandlerRules()['channel']['item']['g:size_type'])
            ->setEpilog(
                (new Field\Infoblock())->setValue(<<<DOCHERE
Является <b>необязательным</b> для всех товаров! Заполняется по желанию и необходимости.
<div class="blockquote border-warning mb-0 mt-3">
В значении этого атрибута указывайте размер, обозначенный производителем. Если тип размера не совпадает с допустимыми значениями, выберите наиболее близкий к одному из них.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
                )
            );

        $arFields['channel__item__g:size_system'] = (new Field\Resource())->setTitle('Система размеров')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:size_system]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SELECT,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSelectField(
                (new Field\Select())
                    ->setOptions([
                        'AU' => 'AU',
                        'BR' => 'BR',
                        'CN' => 'CN',
                        'DE' => 'DE',
                        'EU' => 'EU',
                        'FR' => 'FR',
                        'IT' => 'IT',
                        'JP' => 'JP',
                        'MEX' => 'MEX',
                        'UK' => 'UK',
                        'US' => 'US',
                    ])
                    ->setDefaultOption('-- Выберите систему размеров --')
            )
            ->setValue($this->getHandlerRules()['channel']['item']['g:size_system'])
            ->setEpilog(
                (new Field\Infoblock())->setValue(<<<DOCHERE
Является <b>необязательным</b> для всех товаров! Заполняется по желанию и необходимости.
<div class="blockquote border-warning mb-0 mt-3">
Используйте, чтобы указать, стандарты какой страны вы используете. Так потенциальные покупатели смогут использовать фильтры, чтобы найти нужный товар. Значение этого атрибута влияет на то, как разные варианты товара отображаются в результатах поиска.<br/>
<br/>
Используйте для всех предметов одежды. Это важно, чтобы в объявлении отображался правильный размер товара.<br/>
<br/>
<b>Допустимые значения:</b> AU, BR, CN, DE, EU, FR, IT, JP, MEX, UK, US.
<div class="blockquote-footer">Google Merchant Center</div>
</div>
DOCHERE
                )
            );

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
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__platform']))) {
            $this->addToTmpExportFile('<platform>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__platform'])).'</platform>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__version']))) {
            $this->addToTmpExportFile('<version>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__version'])).'</version>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__agency']))) {
            $this->addToTmpExportFile('<agency>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__agency'])).'</agency>');
        }
        if (!empty($this->extractFilledValueFromRule($this->getFields()['shop__email']))) {
            $this->addToTmpExportFile('<email>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__email'])).'</email>');
        }
    }

    protected function fillShopDeliveryDefault(\Bitrix\Main\Result $obResult)
    {
        if ($this->getHandlerRules()['shop']['@delivery'] == 'Y') {
            $this->addToTmpExportFile('<delivery-options>');

            $strDeliveryOption = '<option cost="'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__cost'])).'"';
            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__days']))) {
                $strDeliveryOption .= ' days="'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__days'])).'"';
            }
            if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__delivery-options__option__@attr__order-before']))) {
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
                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__weight'], $arExportProductData)) && !empty($arExportProductData['WEIGHT_UNIT_CODE'])) {
                    $arOfferXml['weight'] = \Local\Core\Inner\Measure::convert($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__weight'], $arExportProductData),
                        $arExportProductData['WEIGHT_UNIT_CODE'], 'KGM', 3);
                }
                if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__dimensions'], $arExportProductData))) {

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
                break;

            case 'ROBOFEED':

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
                if (!is_null($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__weight'])) && !empty($arExportProductData['WEIGHT_UNIT_CODE'])) {
                    $arOfferXml['weight'] = \Local\Core\Inner\Measure::convert($funGetDefaultValue($this->getOfferDefaultFields()['shop__offers__offer__weight']),
                        $arExportProductData['WEIGHT_UNIT_CODE'], 'KGM', 3);
                }
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

            if ($this->getHandlerRules()['shop']['@delivery'] == 'Y') {
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
            } else {
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
                        if (!empty($arExportProductData['PICKUP_OPTIONS'])) {
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

            if (!empty($arOfferXml)) {
                $this->addToTmpExportFile($this->convertArrayToString($arOfferXml, 'offer'));

                $arLog['PRODUCTS_EXPORTED']++;
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

        if (
            $arOfferXml['price'] >= $arOfferXml['oldprice']
        ) {
            unset($arOfferXml['oldprice']);
        }
    }
}