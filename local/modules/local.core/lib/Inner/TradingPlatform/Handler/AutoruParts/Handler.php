<?php

namespace Local\Core\Inner\TradingPlatform\Handler\AutoruParts;

use \Local\Core\Inner\Route;
use \Local\Core\Inner\TradingPlatform\Field;

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

    protected function getHandlerFields()
    {
        $arRet = [
            '#header_1' => (new Field\Header())->setValue('Данные по товарам'),

            'part__id' => (new Field\Resource())->setTitle('Идентификатор товара в базе продавца')
                ->setName('HANDLER_RULES[part][id]')
                ->setIsRequired()
                ->setStoreId($this->getTradingPlatformStoreId())
                ->setAllowTypeList([
                    Field\Resource::TYPE_SOURCE,
                    Field\Resource::TYPE_LOGIC
                ])
                ->setValue( $this->getHandlerRules()['part']['id'] ?? [
                        'TYPE' => Field\Resource::TYPE_SOURCE,
                        Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#PRODUCT_ID'
                    ]),

            '#header_2' => (new Field\Header())->setValue('Формирование названия товара'),
            '#header_2.info' => (new Field\Infoblock())->setValue(<<<DOCHERE
<blockquote class="blockquote border-warning">
Чтобы объявление попало в нужную категорию (аккумуляторы, аксессуары, колеса и т. п.), укажите ее в названии товара. Если категория не указана в названии, объявление попадет в категорию <b>Разное</b>. Название категории и запчасти следует указывать полностью, иначе при обработке прайс-листа могут возникнуть ошибки. Например, объявление с названием «Диск торм.» попадет в категорию «Шины и диски», но с названием «Диск тормозной» — в категорию «Тормозные диски».<br/>
Полный список категорий вы можете найти на странице <a href="https://auto.ru/parts/" target="_blank">Запчасти</a>.
<div class="blockquote-footer">Требования к названию товара в экпортном файле Авто.ру</div>
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


                    global $funGetChild;
                    $funGetChild = function ($intParentId, $intLvl = 1) use ($arTmpCategory)
                        {
                            $arReturn = [];
                            foreach ($arTmpCategory as $val) {
                                if ($val['PARENT_ID'] == $intParentId) {
                                    $arReturn[$val['ID']] = str_repeat('. ', $intLvl).htmlspecialchars($val['NAME']);
                                    global $funGetChild;
                                    $arChilds = $funGetChild($val['ID'], ($intLvl + 1));
                                    if (!empty($arChilds)) {
                                        $arReturn += $arChilds;
                                    }
                                }
                            }

                            return $arReturn;
                        };
                    $arVals = $funGetChild(null);
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

    /** @inheritDoc */
    protected function executeMakeExportFile(\Bitrix\Main\Result $obResult)
    {
        // TODO: Implement executeMakeExportFile() method.
    }

    /** @inheritDoc */
    protected function beginOfferForeachBody(\Bitrix\Main\Result $obResult, $arExportProductData)
    {
        // TODO: Implement beginOfferForeachBody() method.
    }
}