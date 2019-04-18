<?php

namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondCtrlIBlockProps extends CondCtrlComplex
{
    public static function GetControlDescr()
    {
        $description = parent::GetControlDescr();
        $description['SORT'] = 300;
        return $description;
    }

    /**
     * @param bool|string $strControlID
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {
        $arControlList = array();
        $arIBlockList = array();
        //        $iterator = Catalog\CatalogIblockTable::getList(array(
        //            'select' => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID')
        //        ));
        //        while ($arIBlock = $iterator->fetch())
        //        {
        //            $arIBlock['IBLOCK_ID'] = (int)$arIBlock['IBLOCK_ID'];
        //            $arIBlock['PRODUCT_IBLOCK_ID'] = (int)$arIBlock['PRODUCT_IBLOCK_ID'];
        //            if ($arIBlock['IBLOCK_ID'] > 0)
        //                $arIBlockList[$arIBlock['IBLOCK_ID']] = true;
        //            if ($arIBlock['PRODUCT_IBLOCK_ID'] > 0)
        //                $arIBlockList[$arIBlock['PRODUCT_IBLOCK_ID']] = true;
        //        }
        //        unset($arIBlock, $iterator);
        if (!empty($arIBlockList))
        {
            $arIBlockList = array_keys($arIBlockList);
            sort($arIBlockList);
            foreach ($arIBlockList as $intIBlockID)
            {
                $strName = CIBlock::GetArrayByID($intIBlockID, 'NAME');
                if (false !== $strName)
                {
                    $boolSep = true;
                    $rsProps = CIBlockProperty::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'), array('IBLOCK_ID' => $intIBlockID));
                    while ($arProp = $rsProps->Fetch())
                    {
                        if ('CML2_LINK' == $arProp['XML_ID'] || 'F' == $arProp['PROPERTY_TYPE'])
                            continue;
                        if ('L' == $arProp['PROPERTY_TYPE'])
                            $arProp['VALUES'] = array();

                        $strFieldType = '';
                        $arLogic = array();
                        $arValue = array();
                        $arPhpValue = '';

                        $boolUserType = false;
                        if (isset($arProp['USER_TYPE']) && !empty($arProp['USER_TYPE']))
                        {
                            switch ($arProp['USER_TYPE'])
                            {
                                case 'DateTime':
                                    $strFieldType = 'datetime';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS));
                                    $arValue = array(
                                        'type' => 'datetime',
                                        'format' => 'datetime'
                                    );
                                    $boolUserType = true;
                                    break;
                                case 'Date':
                                    $strFieldType = 'date';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS));
                                    $arValue = array(
                                        'type' => 'datetime',
                                        'format' => 'date'
                                    );
                                    $boolUserType = true;
                                    break;
                                case 'directory':
                                    $strFieldType = 'text';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ));
                                    $arValue = array(
                                        'type' => 'lazySelect',
                                        'load_url' => '/bitrix/tools/local.core/get_property_values.php',
                                        'load_params' => array(
                                            'lang' => LANGUAGE_ID,
                                            'propertyId' => $arProp['ID']
                                        )
                                    );
                                    $boolUserType = true;
                                    break;
                                default:
                                    $boolUserType = false;
                                    break;
                            }
                        }

                        if (!$boolUserType)
                        {
                            switch ($arProp['PROPERTY_TYPE'])
                            {
                                case 'N':
                                    $strFieldType = 'double';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_GR, LOCAL_CORE_CONDITION_LOGIC_LS, LOCAL_CORE_CONDITION_LOGIC_EGR, LOCAL_CORE_CONDITION_LOGIC_ELS));
                                    $arValue = array('type' => 'input');
                                    break;
                                case 'S':
                                    $strFieldType = 'text';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ, LOCAL_CORE_CONDITION_LOGIC_CONT, LOCAL_CORE_CONDITION_LOGIC_NOT_CONT));
                                    $arValue = array('type' => 'input');
                                    break;
                                case 'L':
                                    $strFieldType = 'int';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ));
                                    $arValue = array(
                                        'type' => 'lazySelect',
                                        'load_url' => '/bitrix/tools/local.core/get_property_values.php',
                                        'load_params' => array(
                                            'lang' => LANGUAGE_ID,
                                            'propertyId' => $arProp['ID']
                                        )
                                    );
                                    $arPhpValue = array('VALIDATE' => 'enumValue');
                                    break;
                                case 'E':
                                    $strFieldType = 'int';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ));
                                    $arValue = array(
                                        'type' => 'popup',
                                        'popup_url' => 'iblock_element_search.php',
                                        'popup_params' => array(
                                            'lang' => LANGUAGE_ID,
                                            'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
                                            'discount' => 'Y'
                                        ),
                                        'param_id' => 'n'
                                    );
                                    $arPhpValue = array('VALIDATE' => 'element');
                                    break;
                                case 'G':
                                    $popupParams = array(
                                        'lang' => LANGUAGE_ID,
                                        'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'],
                                        'discount' => 'Y',
                                        'simplename' => 'Y',
                                    );
                                    if ($arProp['LINK_IBLOCK_ID'] > 0)
                                        $popupParams['iblockfix'] = 'y';
                                    $strFieldType = 'int';
                                    $arLogic = static::GetLogic(array(LOCAL_CORE_CONDITION_LOGIC_EQ, LOCAL_CORE_CONDITION_LOGIC_NOT_EQ));
                                    $arValue = array(
                                        'type' => 'popup',
                                        'popup_url' => 'iblock_section_search.php',
                                        'popup_params' => $popupParams,
                                        'param_id' => 'n'
                                    );
                                    unset($popupParams);
                                    $arPhpValue = array('VALIDATE' => 'section');
                                    break;
                            }
                        }
                        $arControlList['CondIBProp:'.$intIBlockID.':'.$arProp['ID']] = array(
                            'ID' => 'CondIBProp:'.$intIBlockID.':'.$arProp['ID'],
                            'PARENT' => false,
                            'EXIST_HANDLER' => 'Y',
                            'MODULE_ID' => 'local.core',
                            'MODULE_ENTITY' => 'iblock',
                            'ENTITY' => 'ELEMENT_PROPERTY',
                            'IBLOCK_ID' => $intIBlockID,
                            'PROPERTY_ID' => $arProp['ID'],
                            'FIELD' => 'PROPERTY_'.$arProp['ID'].'_VALUE',
                            'FIELD_TABLE' => $intIBlockID.':'.$arProp['ID'],
                            'FIELD_TYPE' => $strFieldType,
                            'MULTIPLE' => 'Y',
                            'GROUP' => 'N',
                            'SEP' => ($boolSep ? 'Y' : 'N'),
                            'SEP_LABEL' => ($boolSep
                                ? str_replace(
                                    array('#ID#', '#NAME#'),
                                    array($intIBlockID, $strName),
                                    Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_PROP_LABEL')
                                )
                                : ''
                            ),
                            'LABEL' => $arProp['NAME'],
                            'PREFIX' => str_replace(
                                array('#NAME#', '#IBLOCK_ID#', '#IBLOCK_NAME#'),
                                array($arProp['NAME'], $intIBlockID, $strName),
                                Loc::getMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ONE_PROP_PREFIX')
                            ),
                            'LOGIC' => $arLogic,
                            'JS_VALUE' => $arValue,
                            'PHP_VALUE' => $arPhpValue
                        );

                        $boolSep = false;
                    }
                }
            }
            unset($intIBlockID);
        }
        unset($arIBlockList);

        return static::searchControl($arControlList, $strControlID);
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array();
        $intCount = -1;
        foreach ($arControls as &$arOneControl)
        {
            if (isset($arOneControl['SEP']) && 'Y' == $arOneControl['SEP'])
            {
                $intCount++;
                $arResult[$intCount] = array(
                    'controlgroup' => true,
                    'group' =>  false,
                    'label' => $arOneControl['SEP_LABEL'],
                    'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                    'children' => array()
                );
            }
            $arLogic = static::GetLogicAtom($arOneControl['LOGIC']);
            $arValue = static::GetValueAtom($arOneControl['JS_VALUE']);

            $arResult[$intCount]['children'][] = array(
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
                    $arLogic,
                    $arValue
                )
            );
        }
        if (isset($arOneControl))
            unset($arOneControl);

        return $arResult;
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strResult = '';

        if (is_string($arControl))
        {
            $arControl = static::GetControls($arControl);
        }
        $boolError = !is_array($arControl);

        if (!$boolError)
        {
            $strResult = parent::Generate($arOneCondition, $arParams, $arControl, $arSubs);
            if (false === $strResult || '' == $strResult)
            {
                $boolError = true;
            }
            else
            {
                $strField = 'isset('.$arParams['FIELD'].'[\''.$arControl['FIELD'].'\'])';
                $strResult = $strField.' && '.$strResult;
            }
        }

        return (!$boolError ? $strResult : false);
    }

    public static function ApplyValues($arOneCondition, $arControl)
    {
        $arResult = array();
        $arValues = false;

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
            if ($arValues === false)
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