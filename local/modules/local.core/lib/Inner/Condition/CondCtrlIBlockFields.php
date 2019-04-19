<?php
namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondCtrlIBlockFields extends CondCtrlComplex
{
    public static function GetControlDescr()
    {
        $description = parent::GetControlDescr();
        $description['SORT'] = 200;
        return $description;
    }

    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        return array(
            'CondIBCodeWBL',
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
            'group' =>  false,
            'label' => Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CONTROLGROUP_LABEL'),
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array()
        );
        foreach ($arControls as $arOneControl)
        {
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
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {

        $arControlList = array(
            'CondIBCodeWBL' => array(
                'ID' => 'CondIBCodeWBL',
                'FIELD' => 'WBL_TEST_CODE',
                'FIELD_TYPE' => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL' => 'test',
                'PREFIX' => 'test',
                'LOGIC' => static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT)),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => 'qw'
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
        if (empty($vatList))
        {
            unset($arControlList['CondCatVatID']);
            unset($arControlList['CondCatVatIncluded']);
        }
        foreach ($arControlList as &$control)
        {
            //            if (!isset($control['PARENT']))
            //                $control['PARENT'] = true;
            $control['EXIST_HANDLER'] = 'Y';
            $control['MODULE_ID'] = 'local.core';
            if (!isset($control['MODULE_ENTITY']))
                $control['MODULE_ENTITY'] = 'iblock';
            if (!isset($control['ENTITY']))
                $control['ENTITY'] = 'ELEMENT';
            if (!isset($control['FIELD_TABLE']))
                $control['FIELD_TABLE'] = false;
            $control['MULTIPLE'] = 'N';
            $control['GROUP'] = 'N';
        }
        unset($control);
        $arControlList['CondIBSection']['MULTIPLE'] = 'Y';

        return static::searchControl($arControlList, $strControlID);
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strParentResult = '';
        $strResult = '';
        $parentResultValues = array();
        $resultValues = array();

        if (is_string($arControl))
        {
            $arControl = static::GetControls($arControl);
        }
        $boolError = !is_array($arControl);

        if (!$boolError)
        {
            $arValues = static::Check($arOneCondition, $arOneCondition, $arControl, false);
            $boolError = ($arValues === false);
        }

        if (!$boolError)
        {
            $boolError = !isset($arControl['MULTIPLE']);
        }

        if (!$boolError)
        {
            $arLogic = static::SearchLogic($arValues['logic'], $arControl['LOGIC']);
            if (!isset($arLogic['OP'][$arControl['MULTIPLE']]) || empty($arLogic['OP'][$arControl['MULTIPLE']]))
            {
                $boolError = true;
            }
            else
            {
                $useParent = ($arControl['PARENT'] && isset($arLogic['PARENT']));
                $strParent = $arParams['FIELD'].'[\'PARENT_'.$arControl['FIELD'].'\']';
                $strField = $arParams['FIELD'].'[\''.$arControl['FIELD'].'\']';
                switch ($arControl['FIELD_TYPE'])
                {
                    case 'int':
                    case 'double':
                        if (is_array($arValues['value']))
                        {
                            if (!isset($arLogic['MULTI_SEP']))
                            {
                                $boolError = true;
                            }
                            else
                            {
                                foreach ($arValues['value'] as $value)
                                {
                                    if ($useParent)
                                        $parentResultValues[] = str_replace(
                                            array('#FIELD#', '#VALUE#'),
                                            array($strParent, $value),
                                            $arLogic['OP'][$arControl['MULTIPLE']]
                                        );
                                    $resultValues[] = str_replace(
                                        array('#FIELD#', '#VALUE#'),
                                        array($strField, $value),
                                        $arLogic['OP'][$arControl['MULTIPLE']]
                                    );
                                }
                                unset($value);
                                if ($useParent)
                                    $strParentResult = '('.implode($arLogic['MULTI_SEP'], $parentResultValues).')';
                                $strResult = '('.implode($arLogic['MULTI_SEP'], $resultValues).')';
                                unset($resultValues, $parentResultValues);
                            }
                        }
                        else
                        {
                            if ($useParent)
                                $strParentResult = str_replace(
                                    array('#FIELD#', '#VALUE#'),
                                    array($strParent, $arValues['value']),
                                    $arLogic['OP'][$arControl['MULTIPLE']]
                                );
                            $strResult = str_replace(
                                array('#FIELD#', '#VALUE#'),
                                array($strField, $arValues['value']),
                                $arLogic['OP'][$arControl['MULTIPLE']]
                            );
                        }
                        break;
                    case 'char':
                    case 'string':
                    case 'text':
                        if (is_array($arValues['value']))
                        {
                            $boolError = true;
                        }
                        else
                        {
                            if ($useParent)
                                $strParentResult = str_replace(
                                    array('#FIELD#', '#VALUE#'),
                                    array($strParent, '"'.EscapePHPString($arValues['value']).'"'),
                                    $arLogic['OP'][$arControl['MULTIPLE']]
                                );
                            $strResult = str_replace(
                                array('#FIELD#', '#VALUE#'),
                                array($strField, '"'.EscapePHPString($arValues['value']).'"'),
                                $arLogic['OP'][$arControl['MULTIPLE']]
                            );
                        }
                        break;
                    case 'date':
                    case 'datetime':
                        if (is_array($arValues['value']))
                        {
                            $boolError = true;
                        }
                        else
                        {
                            if ($useParent)
                                $strParentResult = str_replace(array('#FIELD#', '#VALUE#'), array($strParent, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
                            $strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
                            if (!(LOCAL_CORE_CONDITION_LOGIC_EQ == $arLogic['ID'] || LOCAL_CORE_CONDITION_LOGIC_NOT_EQ == $arLogic['ID']))
                            {
                                if ($useParent)
                                    $strParentResult = 'null !== '.$strParent.' && \'\' !== '.$strParent.' && '.$strResult;
                                $strResult = 'null !== '.$strField.' && \'\' !== '.$strField.' && '.$strResult;
                            }
                        }
                        break;
                }
                $strResult = 'isset('.$strField.') && ('.$strResult.')';
                if ($useParent)
                {
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

        if (is_string($arControl))
        {
            $arControl = static::GetControls($arControl);
        }
        $boolError = !is_array($arControl);

        if (!$boolError)
        {
            $arValues = static::Check($arOneCondition, $arOneCondition, $arControl, false);
            if (false === $arValues)
            {
                $boolError = true;
            }
        }

        if (!$boolError)
        {
            $arLogic = static::SearchLogic($arValues['logic'], $arControl['LOGIC']);
            if (in_array($arLogic['ID'], $arLogicID))
            {
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
