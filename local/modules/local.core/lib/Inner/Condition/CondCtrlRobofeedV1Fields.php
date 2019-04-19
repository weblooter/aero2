<?php

namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc, \Bitrix\Main\UserTable, \Bitrix\Main, \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondCtrlRobofeedV1Fields extends CondCtrlComplex
{
    const LOCAL_CORE_CONDITION_LOGIC_EQ = LOCAL_CORE_CONDITION_LOGIC_EQ; // = (equal)
    const LOCAL_CORE_CONDITION_LOGIC_NOT_EQ = LOCAL_CORE_CONDITION_LOGIC_NOT_EQ; // != (not equal)
    const LOCAL_CORE_CONDITION_LOGIC_GR = LOCAL_CORE_CONDITION_LOGIC_GR; // > (great)
    const LOCAL_CORE_CONDITION_LOGIC_LS = LOCAL_CORE_CONDITION_LOGIC_LS; // < (less)
    const LOCAL_CORE_CONDITION_LOGIC_EGR = LOCAL_CORE_CONDITION_LOGIC_EGR;    // => (great or equal)
    const LOCAL_CORE_CONDITION_LOGIC_ELS = LOCAL_CORE_CONDITION_LOGIC_ELS;    // =< (less or equal)
    const LOCAL_CORE_CONDITION_LOGIC_CONT = LOCAL_CORE_CONDITION_LOGIC_CONT; // contain
    const LOCAL_CORE_CONDITION_LOGIC_NOT_CONT = LOCAL_CORE_CONDITION_LOGIC_NOT_CONT; // not contain

    protected static $arCurrency = [];
    protected static $arCountry = [];
    protected static $arMeasure = [];
    protected static $arCategories = [];


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
                    self::$arCategories[self::$intStoreId] = $funGetChild(null);
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
        return array(
            'CondProductId',
            'CondProductGroupId',
            //            'CondIBElement',
            //            'CondIBIBlock',
            //            'CondIBSection',
            //            'CondIBCode',
            //            'CondIBXmlID',
            //            'CondIBName',
            //            'CondIBDateActiveFrom',
            //            'CondIBDateActiveTo',
            //            'CondIBSort',
            //            'CondIBPreviewText',
            //            'CondIBDetailText',
            //            'CondIBDateCreate',
            //            'CondIBCreatedBy',
            //            'CondIBTimestampX',
            //            'CondIBModifiedBy',
            //            'CondIBTags',
            //            'CondCatQuantity',
            //            'CondCatWeight',
            //            'CondCatVatID',
            //            'CondCatVatIncluded',
        );
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' => false,
            'label' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CONTROLGROUP_LABEL'),
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
            'CondProductId' => array(
                'ID' => 'CondProductId',
                'FIELD' => 'PRODUCT_ID',
                'FIELD_TYPE' => 'input',
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

            'CondProductGroupId' => array(
                'ID' => 'CondProductGroupId',
                'FIELD' => 'PRODUCT_GROUP_ID',
                'FIELD_TYPE' => 'input',
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
            ),

            /*
            'CondIBElement' => array(
                'ID' => 'CondIBElement',
                'FIELD' => 'ID',
                'FIELD_TYPE' => 'int',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ELEMENT_ID_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ELEMENT_ID_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ)),
                'JS_VALUE' => array(
                    'type' => 'multiDialog',
                    'popup_url' => 'cat_product_search_dialog.php',
                    'popup_params' => array(
                        'lang' => LANGUAGE_ID,
                        'caller' => 'discount_rules',
                        'allow_select_parent' => 'Y'
                    ),
                    'param_id' => 'n',
                    'show_value' => 'Y'
                ),
                'PHP_VALUE' => array(
                    'VALIDATE' => 'element'
                )
            ),
            'CondIBIBlock' => array(
                'ID' => 'CondIBIBlock',
                'FIELD' => 'IBLOCK_ID',
                'FIELD_TYPE' => 'int',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_IBLOCK_ID_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_IBLOCK_ID_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ)),
                'JS_VALUE' => array(
                    'type' => 'popup',
                    'popup_url' => 'cat_iblock_search.php',
                    'popup_params' => array(
                        'lang' => LANGUAGE_ID,
                        'discount' => 'Y'
                    ),
                    'param_id' => 'n',
                    'show_value' => 'Y'
                ),
                'PHP_VALUE' => array(
                    'VALIDATE' => 'iblock'
                )
            ),
            'CondIBSection' => array(
                'ID' => 'CondIBSection',
                'PARENT' => false,
                'FIELD' => 'SECTION_ID',
                'FIELD_TYPE' => 'int',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SECTION_ID_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SECTION_ID_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ)),
                'JS_VALUE' => array(
                    'type' => 'popup',
                    'popup_url' => 'iblock_section_search.php',
                    'popup_params' => array(
                        'lang' => LANGUAGE_ID,
                        'discount' => 'Y',
                        'simplename' => 'Y'
                    ),
                    'param_id' => 'n',
                    'show_value' => 'Y'
                ),
                'PHP_VALUE' => array(
                    'VALIDATE' => 'section'
                )
            ),
            'CondIBCode' => array(
                'ID' => 'CondIBCode',
                'FIELD' => 'CODE',
                'FIELD_TYPE' => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CODE_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CODE_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBXmlID' => array(
                'ID' => 'CondIBXmlID',
                'FIELD' => 'XML_ID',
                'FIELD_TYPE' => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_XML_ID_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_XML_ID_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBName' => array(
                'ID' => 'CondIBName',
                'FIELD' => 'NAME',
                'FIELD_TYPE' => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_NAME_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_NAME_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBDateActiveFrom' => array(
                'ID' => 'CondIBDateActiveFrom',
                'FIELD' => 'DATE_ACTIVE_FROM',
                'FIELD_TYPE' => 'datetime',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_FROM_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_FROM_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'datetime',
                    'format' => 'datetime'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBDateActiveTo' => array(
                'ID' => 'CondIBDateActiveTo',
                'FIELD' => 'DATE_ACTIVE_TO',
                'FIELD_TYPE' => 'datetime',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_TO_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_TO_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'datetime',
                    'format' => 'datetime'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBSort' => array(
                'ID' => 'CondIBSort',
                'FIELD' => 'SORT',
                'FIELD_TYPE' => 'int',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SORT_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SORT_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBPreviewText' => array(
                'ID' => 'CondIBPreviewText',
                'FIELD' => 'PREVIEW_TEXT',
                'FIELD_TYPE' => 'text',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_PREVIEW_TEXT_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_PREVIEW_TEXT_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBDetailText' => array(
                'ID' => 'CondIBDetailText',
                'FIELD' => 'DETAIL_TEXT',
                'FIELD_TYPE' => 'text',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DETAIL_TEXT_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DETAIL_TEXT_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBDateCreate' => array(
                'ID' => 'CondIBDateCreate',
                'FIELD' => 'DATE_CREATE',
                'FIELD_TYPE' => 'datetime',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_CREATE_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_CREATE_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'datetime',
                    'format' => 'datetime'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBCreatedBy' => array(
                'ID' => 'CondIBCreatedBy',
                'FIELD' => 'CREATED_BY',
                'FIELD_TYPE' => 'int',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CREATED_BY_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CREATED_BY_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => array(
                    'VALIDATE' => 'user'
                )
            ),
            'CondIBTimestampX' => array(
                'ID' => 'CondIBTimestampX',
                'FIELD' => 'TIMESTAMP_X',
                'FIELD_TYPE' => 'datetime',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TIMESTAMP_X_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TIMESTAMP_X_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'datetime',
                    'format' => 'datetime'
                ),
                'PHP_VALUE' => ''
            ),
            'CondIBModifiedBy' => array(
                'ID' => 'CondIBModifiedBy',
                'FIELD' => 'MODIFIED_BY',
                'FIELD_TYPE' => 'int',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_MODIFIED_BY_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_MODIFIED_BY_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => array(
                    'VALIDATE' => 'user'
                )
            ),
            'CondIBTags' => array(
                'ID' => 'CondIBTags',
                'FIELD' => 'TAGS',
                'FIELD_TYPE' => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TAGS_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TAGS_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            'CondCatQuantity' => array(
                'ID' => 'CondCatQuantity',
                'PARENT' => false,
                'MODULE_ENTITY' => 'local.core',
                'ENTITY' => 'PRODUCT',
                'FIELD' => 'CATALOG_QUANTITY',
                'FIELD_TABLE' => 'QUANTITY',
                'FIELD_TYPE' => 'double',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_QUANTITY_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_QUANTITY_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'input'
                )
            ),
            'CondCatWeight' => array(
                'ID' => 'CondCatWeight',
                'PARENT' => false,
                'MODULE_ENTITY' => 'local.core',
                'ENTITY' => 'PRODUCT',
                'FIELD' => 'CATALOG_WEIGHT',
                'FIELD_TABLE' => 'WEIGHT',
                'FIELD_TYPE' => 'double',
                'LABEL' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_WEIGHT_LABEL'),
                'PREFIX' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_WEIGHT_PREFIX'),
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
            */
        );

        $obRobofeedSchema = \Local\Core\Inner\Robofeed\Schema\Factory::factory(\Local\Core\Inner\Store\Base::getLastSuccessImportVersion(self::getStoreId()))
            ->getSchemaMap();

        foreach ($obRobofeedSchema['robofeed']['offers']['offer']['@value'] as $k => $v) {
            if ($v instanceof \Local\Core\Inner\Robofeed\SchemaFields\ScalarField) {
                preg_match_all('/([a-z]+|[A-Z][a-z]+)/', $k, $strColumnNameInTable);
                $strColumnNameInTable = implode('_', array_map('strtoupper', $strColumnNameInTable[1]));
                $strControlKey = 'Cond'.mb_strtoupper(substr($k, 0, 1)).substr($k, 1);

                switch ($k) {
                    case 'article':
                        $arControlList[$strControlKey] = [
                            'ID' => $strControlKey,
                            'FIELD' => $strColumnNameInTable,
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
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
                            'FIELD_TYPE' => 'input',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Единица измерения кол-ва товара',
                            'PREFIX' => 'Единица измерения кол-ва товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS
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
                            'FIELD_TYPE' => 'input',
                            'FIELD_LENGTH' => 255,
                            'LABEL' => 'Категория товара',
                            'PREFIX' => 'Категория товара',
                            'LOGIC' => static::GetLogic(array(
                                self::LOCAL_CORE_CONDITION_LOGIC_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                                self::LOCAL_CORE_CONDITION_LOGIC_GR,
                                self::LOCAL_CORE_CONDITION_LOGIC_EGR,
                                self::LOCAL_CORE_CONDITION_LOGIC_LS,
                                self::LOCAL_CORE_CONDITION_LOGIC_ELS
                            )),
                            'JS_VALUE' => array(
                                'type' => 'select',
                                'values' => self::$arCategories[self::$intStoreId]
                            )
                        ];
                        break;

                    case 'image':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ссылка на изображение';
                        break;

                    case 'countryOfProductionCode':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Страна производства (код)';
                        $arBaseOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#NAME'] = 'Страна производства (название)';
                        break;

                    case 'description':
                        $arBaseOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Описание товара';
                        break;

                    case 'manufacturerWarranty':
                        $arWarrantyAndExpiryOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак наличия официальной гарантии производителя';
                        break;

                    case 'isSex':
                        $arBaseOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак отношения товара к удовлетворению сексуальных потребностей';
                        break;

                    case 'isSoftware':
                        $arBaseOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак отношения товара к программному обеспечению';
                        break;

                    case 'weight':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Вес товара (без ед. измер.)';
                        break;

                    case 'weightUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения веса товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения веса товара (короткое название)';
                        break;

                    case 'width':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Ширина товара (без ед. измер.)';
                        break;

                    case 'widthUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения ширины товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения ширины товара (короткое название)';
                        break;

                    case 'height':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Высота товара (без ед. измер.)';
                        break;

                    case 'heightUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения высоты товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения высоты товара (короткое название)';
                        break;

                    case 'length':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Длина товара (без ед. измер.)';
                        break;

                    case 'lengthUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения длины товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения длины товара (короткое название)';
                        break;

                    case 'volume':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Объем товара (без ед. измер.)';
                        break;

                    case 'volumeUnitCode':
                        $arSizeAndDimensionsOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения объема товара (код)';
                        $arSizeAndDimensionsOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения объема товара (короткое название)';
                        break;

                    case 'warrantyPeriod':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Срок официальной гарантии товара (без ед. измер.)';
                        break;

                    case 'warrantyPeriodCode':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения срока официальной гарантии товара (код)';
                        $arWarrantyAndExpiryOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения срока официальной гарантии товара (короткое название)';
                        break;

                    case 'expiryPeriod':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Срок годности / срок службы товара от даты производстава (без ед. измер.)';
                        break;

                    case 'expiryPeriodCode':
                        $arWarrantyAndExpiryOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Единица измерения срока годности / срока службы товара (код)';
                        $arWarrantyAndExpiryOptions['REFERENCE_FIELD#'.$strColumnNameInTable.'#SHORT_NAME'] = 'Единица измерения срока годности / срока службы товара (короткое название)';
                        break;

                    case 'expiryDate':
                        $arWarrantyAndExpiryOptions['DATE_FIELD#'.$strColumnNameInTable] = 'Дата истечения срока годности товара (формат YYYY-MM-DD HH:MM:SS)';
                        break;

                    case 'inStock':
                        $arPriceOptions['BOOL_FIELD#'.$strColumnNameInTable] = 'Признак наличия товара';
                        break;

                    case 'salesNotes':
                        $arPriceOptions['BASE_FIELD#'.$strColumnNameInTable] = 'Условия продажи товара';
                        break;
                }
            }
        }


        foreach ($arControlList as &$control) {
            //            if (!isset($control['PARENT']))
            //                $control['PARENT'] = true;
            $control['EXIST_HANDLER'] = 'Y';
            $control['MODULE_ID'] = 'local.core';
            if (!isset($control['MODULE_ENTITY'])) {
                $control['MODULE_ENTITY'] = 'iblock';
            }
            if (!isset($control['ENTITY'])) {
                $control['ENTITY'] = 'ELEMENT';
            }
            if (!isset($control['FIELD_TABLE'])) {
                $control['FIELD_TABLE'] = false;
            }

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
