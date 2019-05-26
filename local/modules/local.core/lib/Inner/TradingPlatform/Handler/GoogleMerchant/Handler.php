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
            '#header_g1' => (new Field\Header())->setValue('Настройки магазина'),

            'channel__title' => (new Field\InputText())->setTitle('Короткое название магазина')
                ->setName('HANDLER_RULES[channel][title]')
                ->setIsRequired()
                ->setPlaceholder('Рога и копыта')
                ->setValue($this->getHandlerRules()['channel']['title']),

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

        $arFields = $this->getChannelItemBaseFields() + $this->getChannelItemPriceFields() + $this->getChannelItemCategoryFields() + $this->getChannelItemIdFields() + $this->getChannelItemDescriptionFields() + $this->getChannelItemParamsFields() + $this->getChannelItemTradingCampaignsFields();

        return $arFields;
    }

    protected function getChannelItemBaseFields()
    {
        $arFields = [];

        $arFields = [
            '#header_g2' => (new Field\Header())->setValue('Основные сведения о товарах')
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

        $arFields['channel__item__g:item_group_id'] = (new Field\Resource())->setTitle('Идентификатор группы товара')
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

    protected function getChannelItemPriceFields()
    {
        $arFields = [];

        $arFields['#header_g3'] = (new Field\Header())->setValue('Цена и наличие');

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

    protected function getChannelItemCategoryFields()
    {
        $arFields = [];

        $arFields['#header_g4'] = (new Field\Header())->setValue('Категории товаров');

        $arFields['channel__item__@setCategoryTaxonomy'] = (new Field\Select())
            ->setTitle('Желаете дополнить категории классификациями Google?')
            ->setName('HANDLER_RULES[channel][item][@setCategoryTaxonomy]')
            ->setOptions([
                'Y' => 'Да',
                'N' => 'Нет',
            ])
            ->setDefaultOption('-- Выберите вариант --')
            ->setValue($this->getHandlerRules()['channel']['item']['@setCategoryTaxonomy'] ?? 'N')
            ->setEvent([
                'onchange' => [
                    'PersonalTradingplatformFormComponent.refreshForm()'
                ]
            ]);

        if( $this->getHandlerRules()['channel']['item']['@setCategoryTaxonomy'] == 'Y' )
        {
            $arFields['channel__item__g:google_product_category'] = (new Field\Taxonomy())->setTitle('Категория товара по классификации Google')
                ->setName('HANDLER_RULES[channel][item][g:google_product_category]')
                ->setLeftColumn($this->getStoreCategoriesTaxonomy())
                ->setRightColumn(\Local\Core\Inner\TaxonomyData\Base::getData('GoogleMerchantCategory'))
                ->setAction('GoogleMerchantCategory')
                ->setValue($this->getHandlerRules()['channel']['item']['g:google_product_category']);
        }

        $arFields['#header_g4.info'] = (new Field\Infoblock())
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


    protected function getChannelItemIdFields()
    {
        $arFields = [];

        $arFields['#header_g5'] = (new Field\Header())->setValue('Идентификаторы товара');

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
                    <td></td>
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
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <h4 class="text-center">Печатные СМИ и книги</h4>
            <img src="$strFolderPath/gtin3.png" class="m-auto d-block" height="150" /><br/>
            <table class="table table-striped">
                <tr>
                    <th>Марка:</th>
                    <td></td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td>9781455582341</td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td></td>
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
                    <td></td>
                </tr>
                <tr>
                    <th>GTIN:</th>
                    <td></td>
                </tr>
                <tr>
                    <th>Код производителя:</th>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br/>
<br/>
Если при создании экспортного файла мы обнаружим товар, у которого нет марки и кода GTIN И марки и кода производителя (к примеру индивидуальные товары под заказ) - такой товар получит отметку "Идентификатор не имеется" в автоматическом режиме. Если же у товара не будет марки, но будет GTIN или код производителя (к примеру Книги) - он будет обработан в обычном режиме.
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

    protected function getChannelItemDescriptionFields()
    {
        $arFields = [];

        $arFields['#header_g6'] = (new Field\Header())->setValue('Подробное описание товара');

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

    protected function getChannelItemParamsFields()
    {
        $arFields = [];

        $arFields['#header_g7'] = (new Field\Header())->setValue('Характеристики товара');

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
                    'channel__item__@colorDelimiter' => (new Field\InputText())
                        ->setPlaceholder('Укажите Ваш разделитель')
                        ->setSmallText('Если Ваш разделитель отличается от "/" - укажите и мы заменим его.')
                        ->setPlaceholder('К пример ,')
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
                    'channel__item__@materialDelimiter' => (new Field\InputText())
                        ->setPlaceholder('Укажите Ваш разделитель')
                        ->setSmallText('Если Ваш разделитель отличается от "/" - укажите и мы заменим его.')
                        ->setPlaceholder('К пример ,')
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
                'channel__item__@sizeDelimiter' => (new Field\InputText())
                    ->setPlaceholder('Укажите Ваш разделитель')
                    ->setSmallText('Если Ваш разделитель отличается от "/" - укажите и мы заменим его.')
                    ->setPlaceholder('К пример ,')
                    ->setName('HANDLER_RULES[channel][item][@sizeDelimiter]')
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
                Field\Resource::TYPE_SOURCE,
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

    protected function getChannelItemTradingCampaignsFields()
    {
        $arFields = [];

        $arFields['#header_g8'] = (new Field\Header())->setValue('Торговые кампании и целевые сервисы');

        $arFields['channel__item__g:custom_label'] = (new Field\Resource())->setTitle('Ярлык, по которому можно группировать товары в рамках кампании')
            ->setDescription('С помощью особых ярлыков в торговых кампаниях можно выделять различные группы товаров: например, сезонные, акционные или самые продаваемые. Это удобно для создания отчетов и назначения ставок. Информация, связанная с этими атрибутами, не будет видна пользователям. К одному товару можно добавить до пяти меток продавца.')
            ->setStoreId($this->getTradingPlatformStoreId())
            ->setName('HANDLER_RULES[channel][item][g:custom_label]')
            ->setAllowTypeList([
                Field\Resource::TYPE_SIMPLE,
                Field\Resource::TYPE_SOURCE,
                Field\Resource::TYPE_LOGIC,
                Field\Resource::TYPE_IGNORE,
            ])
            ->setSimpleField( (new Field\InputText()) )
            ->setValue($this->getHandlerRules()['channel']['item']['g:custom_label'] ?? [
                    'TYPE' => Field\Resource::TYPE_IGNORE
                ])
            ->setEpilog([
                'channel__item__@customLabelDelimiter' => (new Field\InputText())
                    ->setSmallText('Если в указанном вашем значении перечислены ярлыки - укажите символ-разделитель, что бы мы могли разделить значения.')
                    ->setPlaceholder('К пример ,')
                    ->setName('HANDLER_RULES[channel][item][@customLabelDelimiter]')
                    ->setValue(trim($this->getHandlerRules()['channel']['item']['@customLabelDelimiter']))
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
            $this->addToTmpExportFile('<?xml version="1.0" encoding="UTF-8"?><rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"><channel>');

            $this->fillShopHeader($obResult);

            if (!$obResult->isSuccess()) {
                throw new \Exception();
            }

            $this->beginFilterProduct($obResult);

            $this->addToTmpExportFile('</channel></rss>');

        } catch (\Throwable $e) {
            if (!empty($e->getMessage())) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }
    }

    protected function fillShopHeader(\Bitrix\Main\Result $obResult)
    {
        try
        {
            if ( empty($this->extractFilledValueFromRule($this->getFields()['channel__title']))){
                throw new \Local\Core\Inner\TradingPlatform\Exceptions\ContinueException('Обязательное поле "'.$this->getFields()['channel__title']->getTitle().'" должно быть заполнено.');
            }
            if ( empty($this->extractFilledValueFromRule($this->getFields()['channel__link']))){
                throw new \Local\Core\Inner\TradingPlatform\Exceptions\ContinueException('Обязательное поле "'.$this->getFields()['channel__link']->getTitle().'" должно быть заполнено.');
            }
            if ( empty($this->extractFilledValueFromRule($this->getFields()['channel__description']))){
                throw new \Local\Core\Inner\TradingPlatform\Exceptions\ContinueException('Обязательное поле "'.$this->getFields()['channel__description']->getTitle().'" должно быть заполнено.');
            }

            $this->addToTmpExportFile('<title>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['channel__title'])).'</title>');
            $this->addToTmpExportFile('<link>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['channel__link'])).'</link>');
            $this->addToTmpExportFile('<description>'.htmlspecialchars($this->extractFilledValueFromRule($this->getFields()['channel__description'])).'</description>');
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\ContinueException $e)
        {
            $obResult->addError( new \Bitrix\Main\Error($e->getMessage()) );
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
            if (
                empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:id'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:title'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:description'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:link'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:images'], $arExportProductData))
                || (
                    !is_array($this->extractFilledValueFromRule($this->getFields()['channel__item__g:images'], $arExportProductData))
                    && !is_string($this->extractFilledValueFromRule($this->getFields()['channel__item__g:images'], $arExportProductData))
                )
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:availability'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__@price'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__@currencyId'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:condition'], $arExportProductData))
                || empty($this->extractFilledValueFromRule($this->getFields()['channel__item__g:adult'], $arExportProductData))
            )
            {
                throw new \Local\Core\Inner\TradingPlatform\Exceptions\ContinueException();
            }

            $arOfferXml['g:id'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:id'], $arExportProductData);

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:item_group_id'], $arExportProductData) ) )
            {
                $arOfferXml['g:item_group_id'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:item_group_id'], $arExportProductData);
            }
            $arOfferXml['g:title'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:title'], $arExportProductData);
            $arOfferXml['g:description']['_cdata'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:description'], $arExportProductData);
            $arOfferXml['g:link'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:link'], $arExportProductData);

            $mixImages = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:images'], $arExportProductData);
            if( is_array( $mixImages ) )
            {
                $mixImages = array_values($mixImages);
                $arOfferXml['g:image_link'] = $mixImages[0];
                if( sizeof($mixImages) > 1 )
                {
                    for($i = 1; $i < sizeof($mixImages) && $i < 11; $i++)
                    {
                        if( !empty( $mixImages[$i] ) )
                        {
                            $arOfferXml['g:additional_image_link'][] = $mixImages[$i];
                        }
                    }
                }

            }
            else
            {
                $arOfferXml['g:image_link'] = $mixImages;
            }


            switch ($this->extractFilledValueFromRule($this->getFields()['channel__item__g:availability'], $arExportProductData))
            {
                case 'Y':
                    $arOfferXml['g:availability'] = 'in stock';
                    break;
                case 'N':
                    $arOfferXml['g:availability'] = 'out of stock';
                    break;
                case 'P':
                    $arOfferXml['g:availability'] = 'preorder';
                    break;
            }

            $this->fillPriceAndCurrency(
                $this->extractFilledValueFromRule($this->getFields()['channel__item__@price'], $arExportProductData),
                $this->extractFilledValueFromRule($this->getFields()['channel__item__@currencyId'], $arExportProductData),
                $this->extractFilledValueFromRule($this->getFields()['channel__item__@oldprice'], $arExportProductData),
                $arOfferXml
            );

            if( $this->extractFilledValueFromRule($this->getFields()['channel__item__@setCategoryTaxonomy']) == 'Y' )
            {
                $arGoogleCategoryTaxonomy = $this->getHandlerRules()['channel']['item']['g:google_product_category'];
                if( !empty( $arGoogleCategoryTaxonomy[ $arExportProductData['CATEGORY_ID'] ] ) )
                {
                    $arOfferXml['g:google_product_category'] = $arGoogleCategoryTaxonomy[ $arExportProductData['CATEGORY_ID'] ];
                }
            }

            $arOfferXml['g:product_type'] = str_replace(' / ', ' > ', $this->getStoreCategoriesTaxonomy()[ $arExportProductData['CATEGORY_ID'] ]);
            $arOfferXml['g:condition'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:condition'], $arExportProductData);
            if( mb_strtoupper($arOfferXml['g:condition']) == 'NEW' )
            {
                if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:brand'], $arExportProductData) ) )
                {
                    $arOfferXml['g:brand'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:brand'], $arExportProductData);
                }
                if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:gtin'], $arExportProductData) ) )
                {
                    $arOfferXml['g:gtin'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:gtin'], $arExportProductData);
                }
                if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:mpn'], $arExportProductData) ) )
                {
                    $arOfferXml['g:mpn'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:mpn'], $arExportProductData);
                }

                if(
                    (empty( $arOfferXml['g:brand'] ) && empty($arOfferXml['g:gtin']))
                    && (empty( $arOfferXml['g:brand'] ) && empty($arOfferXml['g:mpn']))
                )
                {
                    $arOfferXml['g:identifier_exists'] = 'no';
                }
            }

            $arOfferXml['g:adult'] = ( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:adult'], $arExportProductData) == 'Y' ) ? 'true' : 'false';

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:age_group'], $arExportProductData) ) )
            {
                $arOfferXml['g:age_group'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:age_group'], $arExportProductData);
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:color'], $arExportProductData) ) )
            {
                $strTmpDelimiter = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:color']->getEpilog()['channel__item__@colorDelimiter']);
                if( !empty( $strTmpDelimiter ) )
                {
                    $arOfferXml['g:color'] = str_replace($strTmpDelimiter, '/', $this->extractFilledValueFromRule($this->getFields()['channel__item__g:color'], $arExportProductData) );
                }
                else
                {
                    $arOfferXml['g:color'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:color'], $arExportProductData);
                }
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:gender'], $arExportProductData) ) )
            {
                $arOfferXml['g:gender'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:gender'], $arExportProductData);
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:material'], $arExportProductData) ) )
            {
                $strTmpDelimiter = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:material']->getEpilog()['channel__item__@materialDelimiter']);
                if( !empty( $strTmpDelimiter ) )
                {
                    $arOfferXml['g:material'] = str_replace($strTmpDelimiter, '/', $this->extractFilledValueFromRule($this->getFields()['channel__item__g:material'], $arExportProductData) );
                }
                else
                {
                    $arOfferXml['g:material'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:material'], $arExportProductData);
                }
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:pattern'], $arExportProductData) ) )
            {
                $arOfferXml['g:pattern'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:pattern'], $arExportProductData);
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size'], $arExportProductData) ) )
            {
                $strTmpDelimiter = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size']->getEpilog()['channel__item__@sizeDelimiter']);
                if( !empty( $strTmpDelimiter ) )
                {
                    $arOfferXml['g:size'] = str_replace($strTmpDelimiter, '/', $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size'], $arExportProductData) );
                }
                else
                {
                    $arOfferXml['g:size'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size'], $arExportProductData);
                }
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size_type'], $arExportProductData) ) )
            {
                $arOfferXml['g:size_type'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size_type'], $arExportProductData);
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size_system'], $arExportProductData) ) )
            {
                $arOfferXml['g:size_system'] = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:size_system'], $arExportProductData);
            }

            if( !empty( $this->extractFilledValueFromRule($this->getFields()['channel__item__g:custom_label'], $arExportProductData) ) )
            {
                $tmpVal = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:custom_label'], $arExportProductData);

                $strTmpDelimiter = $this->extractFilledValueFromRule($this->getFields()['channel__item__g:custom_label']->getEpilog()['channel__item__@customLabelDelimiter']);
                if( !empty( $strTmpDelimiter ) && is_string($tmpVal) )
                {
                    $tmpVal = explode($strTmpDelimiter, $tmpVal );
                }

                if( is_string($tmpVal) )
                {
                    $tmpVal = [$tmpVal];
                }
                $tmpVal = array_map(function ($v)
                    {
                        return trim($v);
                    }, $tmpVal);
                $tmpVal = array_diff($tmpVal, [''], [null]);
                $tmpVal = array_values($tmpVal);


                for($i = 0; $i < 5 && $i < sizeof($tmpVal); $i++)
                {
                    if( !empty( $tmpVal[$i] ) )
                    {
                        $arOfferXml['g:custom_label_'.$i] = $tmpVal[$i];
                    }
                }
            }

            if (!empty($arOfferXml)) {
                $this->addToTmpExportFile($this->convertArrayToString($arOfferXml, 'item'));

                $arLog['PRODUCTS_EXPORTED']++;
            }

        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\ContinueException $e)
        {
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

        $arPrice = [];

        $intNewPrice = \Local\Core\Inner\Currency::convert($intPrice, $strCurrencyCode, $this->getFinalCurrency($strCurrencyCode));
        if (!is_null($intNewPrice)) {
            $arPrice['PRICE'] = $intNewPrice;
            $arPrice['OLD_PRICE'] = \Local\Core\Inner\Currency::convert($intOldPrice, $strCurrencyCode, $this->getFinalCurrency($strCurrencyCode));
            $arPrice['CURRENCY'] = $this->getFinalCurrency($strCurrencyCode);
        }

        if (
            $arPrice['PRICE'] >= $arPrice['OLD_PRICE']
        ) {
            unset($arPrice['OLD_PRICE']);
        }


        if( $arPrice['OLD_PRICE'] > 0 )
        {
            $arOfferXml['g:price'] = $arPrice['OLD_PRICE'].' '.$arPrice['CURRENCY'];
            $arOfferXml['g:sale_price'] = $arPrice['PRICE'].' '.$arPrice['CURRENCY'];
        }
        else
        {
            $arOfferXml['g:price'] = $arPrice['PRICE'].' '.$arPrice['CURRENCY'];
        }
    }
}