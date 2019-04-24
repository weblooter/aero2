<?php

namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc, \Bitrix\Main\UserTable, \Bitrix\Main, \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondCtrlRobofeedV1ProductFields extends CondCtrlComplex
{
    const LOCAL_CORE_CONDITION_LOGIC_EQ = LOCAL_CORE_CONDITION_LOGIC_EQ; // = (equal)
    const LOCAL_CORE_CONDITION_LOGIC_NOT_EQ = LOCAL_CORE_CONDITION_LOGIC_NOT_EQ; // != (not equal)
    const LOCAL_CORE_CONDITION_LOGIC_GR = LOCAL_CORE_CONDITION_LOGIC_GR; // > (great)
    const LOCAL_CORE_CONDITION_LOGIC_LS = LOCAL_CORE_CONDITION_LOGIC_LS; // < (less)
    const LOCAL_CORE_CONDITION_LOGIC_EGR = LOCAL_CORE_CONDITION_LOGIC_EGR;    // => (great or equal)
    const LOCAL_CORE_CONDITION_LOGIC_ELS = LOCAL_CORE_CONDITION_LOGIC_ELS;    // =< (less or equal)
    const LOCAL_CORE_CONDITION_LOGIC_CONT = LOCAL_CORE_CONDITION_LOGIC_CONT; // contain
    const LOCAL_CORE_CONDITION_LOGIC_NOT_CONT = LOCAL_CORE_CONDITION_LOGIC_NOT_CONT; // not contain


    protected static $intStoreId;

    protected static function getStoreId()
    {
        return self::$intStoreId;
    }

    public static function GetControlDescr($intStoreId = 0)
    {
        self::$intStoreId = $intStoreId;

        self::_fillReferences();

        $description = parent::GetControlDescr();
        $description['SORT'] = 200;
        return $description;
    }


    protected static $arCurrency = [];
    protected static $arCountry = [];
    protected static $arMeasure = [];
    protected static $arCategories = [];

    protected static function _fillReferences()
    {
        if (is_null(self::$arCurrency[self::$intStoreId])) {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();
            if (
            $obCache->startDataCache(60 * 60 * 24 * 7, __METHOD__.'#CURRENCY_LIST', \Local\Core\Inner\Cache::getCachePath([
                'Model',
                'Reference',
                'CurrencyTable'
            ], ['getOptionsToCondition']))
            ) {
                $rs = \Local\Core\Model\Reference\CurrencyTable::getList([
                    'order' => ['NAME' => 'ASC'],
                    'select' => ['CODE', 'NAME']
                ]);
                while ($ar = $rs->fetch()) {
                    self::$arCurrency[self::$intStoreId][$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
                if (empty(self::$arCurrency[self::$intStoreId])) {
                    $obCache->abortDataCache();
                } else {
                    $obCache->endDataCache(self::$arCurrency[self::$intStoreId]);
                }
            } else {
                self::$arCurrency[self::$intStoreId] = $obCache->getVars();
            }
        }

        if (is_null(self::$arCountry[self::$intStoreId])) {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();
            if (
            $obCache->startDataCache(60 * 60 * 24 * 7, __METHOD__.'#CURRENCY_LIST', \Local\Core\Inner\Cache::getCachePath([
                'Model',
                'Reference',
                'CountryTable'
            ], ['getOptionsToCondition']))
            ) {
                $rs = \Local\Core\Model\Reference\CountryTable::getList([
                    'order' => ['NAME' => 'ASC'],
                    'select' => ['CODE', 'NAME']
                ]);
                while ($ar = $rs->fetch()) {
                    self::$arCountry[self::$intStoreId][$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
                if (empty(self::$arCountry[self::$intStoreId])) {
                    $obCache->abortDataCache();
                } else {
                    $obCache->endDataCache(self::$arCountry[self::$intStoreId]);
                }
            } else {
                self::$arCountry[self::$intStoreId] = $obCache->getVars();
            }
        }


        if (is_null(self::$arMeasure[self::$intStoreId])) {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();
            if (
            $obCache->startDataCache(60 * 60 * 24 * 7, __METHOD__.'#CURRENCY_LIST', \Local\Core\Inner\Cache::getCachePath([
                'Model',
                'Reference',
                'MeasureTable'
            ], ['getOptionsToCondition']))
            ) {
                $rs = \Local\Core\Model\Reference\MeasureTable::getList([
                    'order' => ['NAME' => 'ASC'],
                    'select' => ['CODE', 'NAME']
                ]);
                while ($ar = $rs->fetch()) {
                    self::$arMeasure[self::$intStoreId][$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
                if (empty(self::$arMeasure[self::$intStoreId])) {
                    $obCache->abortDataCache();
                } else {
                    $obCache->endDataCache(self::$arMeasure[self::$intStoreId]);
                }
            } else {
                self::$arMeasure[self::$intStoreId] = $obCache->getVars();
            }
        }


        if (is_null(self::$arCategories[self::$intStoreId])) {

            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();

            if (
            $obCache->startDataCache(60 * 60 * 24, __METHOD__.'#CATEGORY#STORE_ID='.self::getStoreId(),
                \Local\Core\Inner\Cache::getCachePath(['Model', 'Robofeed', 'V1', 'StoreCategoryTable'], ['CategoryConditionList', 'storeId='.self::$intStoreId]))
            ) {
                $rsCategories = \Local\Core\Model\Robofeed\StoreCategoryFactory::factory(1)
                    ->setStoreId(self::$intStoreId)::getList([
                        'select' => ['CATEGORY_ID', 'CATEGORY_NAME', 'CATEGORY_PARENT_ID'],
                        'order' => ['CATEGORY_PARENT_ID' => 'ASC', 'CATEGORY_NAME' => 'ASC']
                    ]);

                $arTmpCategory = [];
                while ($ar = $rsCategories->fetch()) {
                    $arTmpCategory[] = [
                        'ID' => $ar['CATEGORY_ID'],
                        'NAME' => $ar['CATEGORY_NAME'],
                        'PARENT_ID' => $ar['CATEGORY_PARENT_ID'],
                    ];
                }

                if (empty($arTmpCategory)) {
                    $obCache->abortDataCache();
                    self::$arCategories[self::$intStoreId] = [];
                } else {

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
                    $i = 1;
                    foreach ($arVals as $value => $label)
                    {
                        self::$arCategories[self::$intStoreId][$i] = ['value' => $value, 'label' => $label];
                        $i++;
                    }
                    $obCache->endDataCache(self::$arCategories[self::$intStoreId]);
                }
            } else {
                self::$arCategories[self::$intStoreId] = $obCache->getVars();
            }
        }
    }

    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        return [
            'CondProdProductId',
            'CondProdProductGroupId',
            'CondProdArticle',
            'CondProdFullName',
            'CondProdSimpleName',
            'CondProdManufacturer',
            'CondProdModel',
            'CondProdUrl',
            'CondProdManufacturerCode',
            'CondProdPrice',
            'CondProdOldPrice',
            'CondProdCurrencyCode',
            'CondProdQuantity',
            'CondProdUnitOfMeasure',
            'CondProdCategoryId',
            'CondProdImage',
            'CondProdCountryOfProductionCode',
            'CondProdDescription',
            'CondProdManufacturerWarranty',
            'CondProdIsSex',
            'CondProdIsSoftware',
            'CondProdWeight',
            'CondProdWeightUnitCode',
            'CondProdWidth',
            'CondProdWidthUnitCode',
            'CondProdHeight',
            'CondProdHeightUnitCode',
            'CondProdLength',
            'CondProdLengthUnitCode',
            'CondProdVolume',
            'CondProdVolumeUnitCode',
            'CondProdWarrantyPeriod',
            'CondProdWarrantyPeriodCode',
            'CondProdExpiryPeriod',
            'CondProdExpiryPeriodCode',
            'CondProdExpiryDate',
            'CondProdInStock',
            'CondProdSalesNotes',
            'CondProdDeliveryAvailable',
            'CondProdPickupAvailable',
        ];
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' => false,
            'label' => 'Основные поля товара',
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array()
        );
        foreach ($arControls as $arOneControl) {
            $arResult['children'][] = array(
                'controlId' => $arOneControl['ID'],
                'group' => false,
                'label' => $arOneControl['LABEL'],
                'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control' => array(
                    array(
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $arOneControl['PREFIX']
                    ),
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE'])
                )
            );
        }
        unset($arOneControl);

        return $arResult;
    }

    /**
     * @param bool|string $strControlID
     *
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {


        $arControlList = array(
            'CondProdProductId' => array(
                'ID' => 'CondProdProductId',
                'FIELD' => 'PRODUCT_ID',
                'FIELD_TYPE' => 'text',
                'FIELD_LENGTH' => 255,
                'LABEL' => 'Идентификатор товара',
                'PREFIX' => 'Идентификатор товара',
                'LOGIC' => static::GetLogic(array(
                    self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                    self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                    self::LOCAL_CORE_CONDITION_LOGIC_GR,
                    self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                    self::LOCAL_CORE_CONDITION_LOGIC_LS,
                    self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                    self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                    self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                )),
                'JS_VALUE' => array(
                    'type' => 'input'
                )
            ),

            'CondProdProductGroupId' => array(
                'ID' => 'CondProdProductGroupId',
                'FIELD' => 'PRODUCT_GROUP_ID',
                'FIELD_TYPE' => 'text',
                'FIELD_LENGTH' => 255,
                'LABEL' => 'Идентификатор группы товара, которые повторяются',
                'PREFIX' => 'Идентификатор группы товара, которые повторяются',
                'LOGIC' => static::GetLogic(array(
                    self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                    self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                    self::LOCAL_CORE_CONDITION_LOGIC_GR,
                    self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                    self::LOCAL_CORE_CONDITION_LOGIC_LS,
                    self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                    self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                    self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                )),
                'JS_VALUE' => array(
                    'type' => 'input'
                )
            )
        );

        $obRobofeedSchema = \Local\Core\Inner\Robofeed\Schema\Factory::factory(1)
            ->getSchemaMap();

        foreach ($obRobofeedSchema['robofeed']['offers']['offer']['@value'] as $k => $v) {

            if ($v instanceof \Local\Core\Inner\Robofeed\SchemaFields\ScalarField) {
                preg_match_all('/([a-z]+|[A-Z][a-z]+)/', $k, $strColumnNameInTable);
                $strColumnNameInTable = implode('_', array_map('strtoupper', $strColumnNameInTable[1]));
                $strControlKey = 'CondProd'.mb_strtoupper(substr($k, 0, 1)).substr($k, 1);

                switch ($k) {
                    case 'article':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Артикул',
                            'PREFIX' => 'Артикул',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'fullName':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Полное название товара (к примеру "Смартфон Apple iPhone 8S 64GB")',
                            'PREFIX' => 'Полное название товара (к примеру "Смартфон Apple iPhone 8S 64GB")',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'simpleName':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Простое название товара (к примеру "iPhone 8S")',
                            'PREFIX' => 'Простое название товара (к примеру "iPhone 8S")',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'manufacturer':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Название компании производителя',
                            'PREFIX' => 'Название компании производителя',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'model':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Модель',
                            'PREFIX' => 'Модель',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'url':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Ссылка на детальную страницу',
                            'PREFIX' => 'Ссылка на детальную страницу',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'manufacturerCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Код производителя для данного товара',
                            'PREFIX' => 'Код производителя для данного товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'price':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Текущая стоимость товара',
                            'PREFIX' => 'Текущая стоимость товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'oldPrice':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Старая стоимость товара',
                            'PREFIX' => 'Старая стоимость товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'currencyCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Валюта',
                            'PREFIX' => 'Валюта',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arCurrency[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'quantity':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Количество товара',
                            'PREFIX' => 'Количество товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;

                    case 'unitOfMeasure':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения кол-ва товара',
                            'PREFIX' => 'Единица измерения кол-ва товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'categoryId':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Категория товара',
                            'PREFIX' => 'Категория товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arCategories[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'image':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Ссылка на изображение',
                            'PREFIX' => 'Ссылка на изображение',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'countryOfProductionCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Страна производства',
                            'PREFIX' => 'Страна производства',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arCountry[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'description':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Описание товара',
                            'PREFIX' => 'Описание товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'manufacturerWarranty':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Официальная гарантия производителя',
                            'PREFIX' => 'Официальная гарантия производителя',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => [
                                    'Y' => 'Есть',
                                    'N' => 'Нет',
                                ]
                            )
                        ];
                        break;

                    case 'isSex':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Товар имеет отношение к удовлетворению сексуальных потребностей',
                            'PREFIX' => 'Товар имеет отношение к удовлетворению сексуальных потребностей',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => [
                                    'Y' => 'Да',
                                    'N' => 'Нет',
                                ]
                            )
                        ];
                        break;

                    case 'isSoftware':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Товар является программным обеспечением',
                            'PREFIX' => 'Товар является программным обеспечением',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => [
                                    'Y' => 'Да',
                                    'N' => 'Нет',
                                ]
                            )
                        ];
                        break;

                    case 'weight':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Вес товара (без ед. измер.)',
                            'PREFIX' => 'Вес товара (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'weightUnitCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения веса товара',
                            'PREFIX' => 'Единица измерения веса товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'width':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Ширина товара (без ед. измер.)',
                            'PREFIX' => 'Ширина товара (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'widthUnitCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения ширины товара',
                            'PREFIX' => 'Единица измерения ширины товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'height':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Высота товара (без ед. измер.)',
                            'PREFIX' => 'Высота товара (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'heightUnitCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения высоты товара',
                            'PREFIX' => 'Единица измерения высоты товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'length':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Длина товара (без ед. измер.)',
                            'PREFIX' => 'Длина товара (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'lengthUnitCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения длины товара',
                            'PREFIX' => 'Единица измерения длины товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'volume':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Объем товара (без ед. измер.)',
                            'PREFIX' => 'Объем товара (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'volumeUnitCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения объема товара',
                            'PREFIX' => 'Единица измерения объема товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'warrantyPeriod':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Срок официальной гарантии товара (без ед. измер.)',
                            'PREFIX' => 'Срок официальной гарантии товара (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'warrantyPeriodCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения срока официальной гарантии товара',
                            'PREFIX' => 'Единица измерения срока официальной гарантии товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'expiryPeriod':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'int',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Срок годности / срок службы товара от даты производстава (без ед. измер.)',
                            'PREFIX' => 'Срок годности / срок службы товара от даты производстава (без ед. измер.)',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input',
                            )
                        ];
                        break;

                    case 'expiryPeriodCode':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения срока годности / срока службы товара',
                            'PREFIX' => 'Единица измерения срока годности / срока службы товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arMeasure[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'expiryDate':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'datetime',
                            'LABEL' => 'Дата истечения срока годности товара',
                            'PREFIX' => 'Дата истечения срока годности товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'datetime',
                                'format' => 'datetime'
                            )
                        ];
                        break;

                    case 'inStock':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Товар есть в наличии',
                            'PREFIX' => 'Товар есть в наличии',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => [
                                    'Y' => 'Да',
                                    'N' => 'Нет',
                                ]
                            )
                        ];
                        break;

                    case 'salesNotes':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'text',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Условия продажи товара',
                            'PREFIX' => 'Условия продажи товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_CONT,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_CONT,
                            )),
                            'JS_VALUE' => array(
                                'type' => 'input'
                            )
                        ];
                        break;
                }
            }
        }


        $arControlList['CondProdDeliveryAvailable'] = [
            'ID' => 'CondProdDeliveryAvailable',
            'FIELD' => 'DELIVERY_AVAILABLE',
            'FIELD_TYPE' => 'string',
            'LABEL' => 'Имеется ли служба доставки',
            'PREFIX' => 'Имеется ли служба доставки',
            'LOGIC' => static::GetLogic(array(
                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
            )),
            'JS_VALUE' => array(
                'type' => 'select',
                'values' => [
                    'Y' => 'Да',
                    'N' => 'Нет',
                ]
            )
        ];

        $arControlList['CondProdPickupAvailable'] = [
            'ID' => 'CondProdPickupAvailable',
            'FIELD' => 'PICKUP_AVAILABLE',
            'FIELD_TYPE' => 'string',
            'LABEL' => 'Имеется ли возможность самовывоза',
            'PREFIX' => 'Имеется ли возможность самовывоза',
            'LOGIC' => static::GetLogic(array(
                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
            )),
            'JS_VALUE' => array(
                'type' => 'select',
                'values' => [
                    'Y' => 'Да',
                    'N' => 'Нет',
                ]
            )
        ];


        foreach ($arControlList as &$control) {
            //            if (!isset($control['PARENT']))
            //                $control['PARENT'] = true;
            $control['EXIST_HANDLER'] = 'Y';
            $control['MODULE_ID'] = 'local.core';

            if (empty($control['MULTIPLE'])) {
                $control['MULTIPLE'] = 'N';
            }

            $control['GROUP'] = 'N';
        }
        unset($control);

        return static::searchControl($arControlList, $strControlID);
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strParentResult = '';
        $strResult = '';
        $parentResultValues = array();
        $resultValues = array();

        if (is_string($arControl)) {
            $arControl = static::GetControls($arControl);
        }
        $boolError = !is_array($arControl);

        if (!$boolError) {
            $arValues = static::Check($arOneCondition, $arOneCondition, $arControl, false);
            $boolError = ($arValues === false);
        }

        if (!$boolError) {
            $boolError = !isset($arControl['MULTIPLE']);
        }

        if (!$boolError) {
            $arLogic = static::SearchLogic($arValues['logic'], $arControl['LOGIC']);
            if (!isset($arLogic['OP'][$arControl['MULTIPLE']]) || empty($arLogic['OP'][$arControl['MULTIPLE']])) {
                $boolError = true;
            } else {
                $useParent = ($arControl['PARENT'] && isset($arLogic['PARENT']));
                $strParent = $arParams['FIELD'].'[\'PARENT_'.$arControl['FIELD'].'\']';
                $strField = $arParams['FIELD'].'[\''.$arControl['FIELD'].'\']';
                switch ($arControl['FIELD_TYPE']) {
                    case 'int':
                    case 'double':
                        if (is_array($arValues['value'])) {
                            if (!isset($arLogic['MULTI_SEP'])) {
                                $boolError = true;
                            } else {
                                foreach ($arValues['value'] as $value) {
                                    if ($useParent) {
                                        $parentResultValues[] = str_replace(array('#FIELD#', '#VALUE#'), array($strParent, $value), $arLogic['OP'][$arControl['MULTIPLE']]);
                                    }
                                    $resultValues[] = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $value), $arLogic['OP'][$arControl['MULTIPLE']]);
                                }
                                unset($value);
                                if ($useParent) {
                                    $strParentResult = '('.implode($arLogic['MULTI_SEP'], $parentResultValues).')';
                                }
                                $strResult = '('.implode($arLogic['MULTI_SEP'], $resultValues).')';
                                unset($resultValues, $parentResultValues);
                            }
                        } else {
                            if ($useParent) {
                                $strParentResult = str_replace(array('#FIELD#', '#VALUE#'), array($strParent, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
                            }
                            $strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
                        }
                        break;
                    case 'char':
                    case 'string':
                    case 'text':
                        if (is_array($arValues['value'])) {
                            $boolError = true;
                        } else {
                            if ($useParent) {
                                $strParentResult = str_replace(array('#FIELD#', '#VALUE#'), array($strParent, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
                            }
                            $strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
                        }
                        break;
                    case 'date':
                    case 'datetime':
                        if (is_array($arValues['value'])) {
                            $boolError = true;
                        } else {
                            if ($useParent) {
                                $strParentResult = str_replace(array('#FIELD#', '#VALUE#'), array($strParent, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
                            }
                            $strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
                            if (!(LOCAL_CORE_CONDITION_LOGIC_EQ == $arLogic['ID'] || LOCAL_CORE_CONDITION_LOGIC_NOT_EQ == $arLogic['ID'])) {
                                if ($useParent) {
                                    $strParentResult = 'null !== '.$strParent.' && \'\' !== '.$strParent.' && '.$strResult;
                                }
                                $strResult = 'null !== '.$strField.' && \'\' !== '.$strField.' && '.$strResult;
                            }
                        }
                        break;
                }
                $strResult = 'isset('.$strField.') && ('.$strResult.')';
                if ($useParent) {
                    $strResult = '(isset('.$strParent.') ? (('.$strResult.')'.$arLogic['PARENT'].$strParentResult.') : ('.$strResult.'))';
                }
            }
        }

        return (!$boolError ? $strResult : false);
    }

    public static function ApplyValues($arOneCondition, $arControl)
    {
        $arResult = array();

        $arLogicID = array(
            LOCAL_CORE_CONDITION_LOGIC_EQ,
            LOCAL_CORE_CONDITION_LOGIC_EGR,
            LOCAL_CORE_CONDITION_LOGIC_ELS,
        );

        if (is_string($arControl)) {
            $arControl = static::GetControls($arControl);
        }
        $boolError = !is_array($arControl);

        if (!$boolError) {
            $arValues = static::Check($arOneCondition, $arOneCondition, $arControl, false);
            if (false === $arValues) {
                $boolError = true;
            }
        }

        if (!$boolError) {
            $arLogic = static::SearchLogic($arValues['logic'], $arControl['LOGIC']);
            if (in_array($arLogic['ID'], $arLogicID)) {
                $arResult = array(
                    'ID' => $arControl['ID'],
                    'FIELD' => $arControl['FIELD'],
                    'FIELD_TYPE' => $arControl['FIELD_TYPE'],
                    'VALUES' => (is_array($arValues['value']) ? $arValues['value'] : array($arValues['value']))
                );
            }
        }

        return (!$boolError ? $arResult : false);
    }
}
