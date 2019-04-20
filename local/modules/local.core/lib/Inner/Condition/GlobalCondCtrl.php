<?php
namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class GlobalCondCtrl
{
    public static $arInitParams = false;
    public static $boolInit = false;

    public static function GetClassName()
    {
        return get_called_class();
    }

    public static function GetControlDescr()
    {
        $strClassName = get_called_class();
        return array(
            'ID' => static::GetControlID(),
            'GetControlShow' => array($strClassName, 'GetControlShow'),
            'GetConditionShow' => array($strClassName, 'GetConditionShow'),
            'IsGroup' => array($strClassName, 'IsGroup'),
            'Parse' => array($strClassName, 'Parse'),
            'Generate' => array($strClassName, 'Generate'),
            'ApplyValues' => array($strClassName, 'ApplyValues'),
            'InitParams' => array($strClassName, 'InitParams')
        );
    }

    public static function GetControlShow($arParams)
    {
        return array();
    }

    public static function GetConditionShow($arParams)
    {
        return '';
    }

    public static function IsGroup($strControlID = false)
    {
        return 'N';
    }

    public static function Parse($arOneCondition)
    {
        return '';
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        return '';
    }

    public static function ApplyValues($arOneCondition, $arControl)
    {
        return array();
    }

    public static function InitParams($arParams)
    {
        if (!empty($arParams) && is_array($arParams))
        {
            static::$arInitParams = $arParams;
            static::$boolInit = true;
        }
    }

    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        return '';
    }

    public static function GetShowIn($arControls)
    {
        if (!is_array($arControls))
            $arControls = array($arControls);
        return array_values(array_unique($arControls));
    }

    /**
     * @param bool|string $strControlID
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {
        return false;
    }

    public static function GetAtoms()
    {
        return array();
    }

    public static function GetAtomsEx($strControlID = false, $boolEx = false)
    {
        return array();
    }

    public static function GetJSControl($arControl, $arParams = array())
    {
        return array();
    }

    public static function OnBuildConditionAtomList()
    {

    }

    /**
     * @param bool|array $arOperators
     * @return array
     */
    public static function GetLogic($arOperators = false)
    {
        $arOperatorsList = array(
            LOCAL_CORE_CONDITION_LOGIC_EQ => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_EQ,
                'OP' => array(
                    'Y' => 'in_array(#VALUE#, #FIELD#)',
                    'N' => '#FIELD# == #VALUE#'
                ),
                'PARENT' => ' || ',
                'MULTI_SEP' => ' || ',
                'VALUE' => 'Equal',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_EQ_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_NOT_EQ => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_NOT_EQ,
                'OP' => array(
                    'Y' => '!in_array(#VALUE#, #FIELD#)',
                    'N' => '#FIELD# != #VALUE#'
                ),
                'PARENT' => ' && ',
                'MULTI_SEP' => ' && ',
                'VALUE' => 'Not',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_NOT_EQ_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_GR => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_GR,
                'OP' => array(
                    'N' => '#FIELD# > #VALUE#',
                    'Y' => 'GlobalCondCtrl::LogicGreat(#FIELD#, #VALUE#)'
                ),
                'VALUE' => 'Great',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_GR_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_LS => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_LS,
                'OP' => array(
                    'N' => '#FIELD# < #VALUE#',
                    'Y' => 'GlobalCondCtrl::LogicLess(#FIELD#, #VALUE#)'
                ),
                'VALUE' => 'Less',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_LS_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_EGR => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_EGR,
                'OP' => array(
                    'N' => '#FIELD# >= #VALUE#',
                    'Y' => 'GlobalCondCtrl::LogicEqualGreat(#FIELD#, #VALUE#)'
                ),
                'VALUE' => 'EqGr',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_EGR_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_ELS => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_ELS,
                'OP' => array(
                    'N' => '#FIELD# <= #VALUE#',
                    'Y' => 'GlobalCondCtrl::LogicEqualLess(#FIELD#, #VALUE#)'
                ),
                'VALUE' => 'EqLs',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_ELS_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_CONT => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_CONT,
                'OP' => array(
                    'N' => 'false !== strpos(#FIELD#, #VALUE#)',
                    'Y' => 'GlobalCondCtrl::LogicContain(#FIELD#, #VALUE#)'
                ),
                'PARENT' => ' || ',
                'MULTI_SEP' => ' || ',
                'VALUE' => 'Contain',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_CONT_LABEL')
            ),
            LOCAL_CORE_CONDITION_LOGIC_NOT_CONT => array(
                'ID' => LOCAL_CORE_CONDITION_LOGIC_NOT_CONT,
                'OP' => array(
                    'N' => 'false === strpos(#FIELD#, #VALUE#)',
                    'Y' => 'GlobalCondCtrl::LogicNotContain(#FIELD#, #VALUE#)'
                ),
                'PARENT' => ' && ',
                'MULTI_SEP' => ' && ',
                'VALUE' => 'NotCont',
                'LABEL' => Loc::getMessage('LOCAL_CORE_CONDITION_LOGIC_NOT_CONT_LABEL')
            )
        );

        $boolSearch = false;
        $arSearch = array();
        if (!empty($arOperators) && is_array($arOperators))
        {
            foreach ($arOperators as &$intOneOp)
            {
                if (isset($arOperatorsList[$intOneOp]))
                {
                    $boolSearch = true;
                    $arSearch[$intOneOp] = $arOperatorsList[$intOneOp];
                }
            }
            unset($intOneOp);
        }
        return ($boolSearch ? $arSearch : $arOperatorsList);
    }

    /**
     * @param bool|array $arOperators
     * @param bool|array $arLabels
     * @return array
     */
    public static function GetLogicEx($arOperators = false, $arLabels = false)
    {
        $arOperatorsList = static::GetLogic($arOperators);
        if (!empty($arLabels) && is_array($arLabels))
        {
            foreach ($arOperatorsList as &$arOneOperator)
            {
                if (isset($arLabels[$arOneOperator['ID']]))
                    $arOneOperator['LABEL'] = $arLabels[$arOneOperator['ID']];
            }
            if (isset($arOneOperator))
                unset($arOneOperator);
        }
        return $arOperatorsList;
    }

    public static function GetLogicAtom($arLogic)
    {
        if (!empty($arLogic) && is_array($arLogic))
        {
            $arValues = array();
            foreach ($arLogic as &$arOneLogic)
            {
                $arValues[$arOneLogic['VALUE']] = $arOneLogic['LABEL'];
            }
            if (isset($arOneLogic))
                unset($arOneLogic);
            $arResult = array(
                'id' => 'logic',
                'name' =>  'logic',
                'type' => 'select',
                'values' => $arValues,
                'defaultText' => current($arValues),
                'defaultValue' => key($arValues)
            );
            return $arResult;
        }
        return false;
    }

    public static function GetValueAtom($arValue)
    {
        if (empty($arValue) || !isset($arValue['type']))
        {
            $arResult = array(
                'type' => 'input'
            );
        }
        else
        {
            $arResult = $arValue;
        }
        $arResult['id'] = 'value';
        $arResult['name'] = 'value';

        return $arResult;
    }

    public static function CheckLogic($strValue, $arLogic, $boolShow = false)
    {
        $boolShow = (true === $boolShow);
        if (empty($arLogic) || !is_array($arLogic))
            return false;
        $strResult = '';
        foreach ($arLogic as &$arOneLogic)
        {
            if ($strValue == $arOneLogic['VALUE'])
            {
                $strResult = $arOneLogic['VALUE'];
                break;
            }
        }
        if (isset($arOneLogic))
            unset($arOneLogic);
        if ($strResult == '')
        {
            if ($boolShow)
            {
                $arOneLogic = current($arLogic);
                $strResult = $arOneLogic['VALUE'];
            }
        }
        return ($strResult == '' ? false : $strResult);
    }

    public static function SearchLogic($strValue, $arLogic)
    {
        $mxResult = false;
        if (empty($arLogic) || !is_array($arLogic))
            return $mxResult;
        foreach ($arLogic as &$arOneLogic)
        {
            if ($strValue == $arOneLogic['VALUE'])
            {
                $mxResult = $arOneLogic;
                break;
            }
        }
        if (isset($arOneLogic))
            unset($arOneLogic);
        return $mxResult;
    }

    public static function Check($arOneCondition, $arParams, $arControl, $boolShow)
    {

        $boolShow = ($boolShow === true);
        $boolError = false;
        $boolFatalError = false;
        $arMsg = array();

        $arValues = array(
            'logic' => '',
            'value' => ''
        );
        $arLabels = array();

        static $intTimeOffset = false;
        if ($intTimeOffset === false)
            $intTimeOffset = \CTimeZone::GetOffset();

        if ($boolShow)
        {
            if (!isset($arOneCondition['logic']))
            {
                $arOneCondition['logic'] = '';
                $boolError = true;
            }
            if (!isset($arOneCondition['value']))
            {
                $arOneCondition['value'] = '';
                $boolError = true;
            }
            $strLogic = static::CheckLogic($arOneCondition['logic'], $arControl['LOGIC'], $boolShow);
            if ($strLogic === false)
            {
                $boolError = true;
                $boolFatalError = true;
                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_LOGIC_ABSENT');
            }
            else
            {
                $arValues['logic'] = $strLogic;
            }

            $boolValueError = static::ClearValue($arOneCondition['value']);
            if (!$boolValueError)
            {
                $boolMulti = is_array($arOneCondition['value']);
                switch ($arControl['FIELD_TYPE'])
                {
                    case 'int':
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$intOneValue)
                            {
                                $intOneValue = (int)$intOneValue;
                            }
                            if (isset($intOneValue))
                                unset($intOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = (int)$arOneCondition['value'];
                        }
                        break;
                    case 'double':
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$dblOneValue)
                            {
                                $dblOneValue = (float)$dblOneValue;
                            }
                            if (isset($dblOneValue))
                                unset($dblOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = (float)$arOneCondition['value'];
                        }
                        break;
                    case 'char':
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$strOneValue)
                            {
                                $strOneValue = substr($strOneValue, 0, 1);
                            }
                            if (isset($strOneValue))
                                unset($strOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = substr($arOneCondition['value'], 0, 1);
                        }
                        break;
                    case 'string':
                        $intMaxLen = (int)(isset($arControl['FIELD_LENGTH']) ? $arControl['FIELD_LENGTH'] : 255);
                        if ($intMaxLen <= 0)
                            $intMaxLen = 255;
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$strOneValue)
                            {
                                $strOneValue = substr($strOneValue, 0, $intMaxLen);
                            }
                            if (isset($strOneValue))
                                unset($strOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = substr($arOneCondition['value'], 0, $intMaxLen);
                        }
                        break;
                    case 'text':
                        break;
                    case 'date':
                    case 'datetime':
                        if ($arControl['FIELD_TYPE'] == 'date')
                        {
                            $strFormat = 'SHORT';
                            $intOffset = 0;
                        }
                        else
                        {
                            $strFormat = 'FULL';
                            $intOffset = $intTimeOffset;
                        }
                        $boolValueError = static::ConvertInt2DateTime($arOneCondition['value'], $strFormat, $intOffset);
                        break;
                    default:
                        $boolValueError = true;
                        break;
                }
            }
            if (!$boolValueError)
            {
                if ($boolMulti)
                    $arOneCondition['value'] = array_values(array_unique($arOneCondition['value']));
            }

            if (!$boolValueError)
            {
                if (isset($arControl['PHP_VALUE']) && isset($arControl['PHP_VALUE']['VALIDATE']) && !empty($arControl['PHP_VALUE']['VALIDATE']))
                {
                    $arValidate = static::Validate($arOneCondition, $arParams, $arControl, $boolShow);
                    if ($arValidate === false)
                    {
                        $boolValueError = true;
                    }
                    else
                    {
                        if (isset($arValidate['err_cond']) && $arValidate['err_cond'] == 'Y')
                        {
                            $boolValueError = true;
                            if (isset($arValidate['err_cond_mess']) && !empty($arValidate['err_cond_mess']))
                                $arMsg = array_merge($arMsg, $arValidate['err_cond_mess']);
                        }
                        else
                        {
                            $arValues['value'] = $arValidate['values'];
                            if (isset($arValidate['labels']))
                                $arLabels['value'] = $arValidate['labels'];
                        }
                    }
                }
                else
                {
                    $arValues['value'] = $arOneCondition['value'];
                }
            }

            if ($boolValueError)
                $boolError = $boolValueError;
        }
        else
        {
            if (!isset($arOneCondition['logic']) || !isset($arOneCondition['value']))
            {
                $boolError = true;
            }
            else
            {
                $strLogic = static::CheckLogic($arOneCondition['logic'], $arControl['LOGIC'], $boolShow);
                if (!$strLogic)
                {
                    $boolError = true;
                }
                else
                {
                    $arValues['logic'] = $arOneCondition['logic'];
                }
            }

            if (!$boolError)
            {
                $boolError = static::ClearValue($arOneCondition['value']);
            }

            if (!$boolError)
            {
                $boolMulti = is_array($arOneCondition['value']);
                switch ($arControl['FIELD_TYPE'])
                {
                    case 'int':
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$intOneValue)
                            {
                                $intOneValue = (int)$intOneValue;
                            }
                            if (isset($intOneValue))
                                unset($intOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = (int)$arOneCondition['value'];
                        }
                        break;
                    case 'double':
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$dblOneValue)
                            {
                                $dblOneValue = (float)$dblOneValue;
                            }
                            if (isset($dblOneValue))
                                unset($dblOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = (float)$arOneCondition['value'];
                        }
                        break;
                    case 'char':
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$strOneValue)
                            {
                                $strOneValue = substr($strOneValue, 0, 1);
                            }
                            if (isset($strOneValue))
                                unset($strOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = substr($arOneCondition['value'], 0, 1);
                        }
                        break;
                    case 'string':
                        $intMaxLen = (int)(isset($arControl['FIELD_LENGTH']) ? $arControl['FIELD_LENGTH'] : 255);
                        if ($intMaxLen <= 0)
                            $intMaxLen = 255;
                        if ($boolMulti)
                        {
                            foreach ($arOneCondition['value'] as &$strOneValue)
                            {
                                $strOneValue = substr($strOneValue, 0, $intMaxLen);
                            }
                            if (isset($strOneValue))
                                unset($strOneValue);
                        }
                        else
                        {
                            $arOneCondition['value'] = substr($arOneCondition['value'], 0, $intMaxLen);
                        }
                        break;
                    case 'text':
                        break;
                    case 'date':
                    case 'datetime':
                        if ($arControl['FIELD_TYPE'] == 'date')
                        {
                            $strFormat = 'SHORT';
                            $intOffset = 0;
                        }
                        else
                        {
                            $strFormat = 'FULL';
                            $intOffset = $intTimeOffset;
                        }
                        $boolError = static::ConvertDateTime2Int($arOneCondition['value'], $strFormat, $intOffset);
                        break;
                    default:
                        $boolError = true;
                        break;
                }
                if ($boolMulti)
                {
                    if (!$boolError)
                        $arOneCondition['value'] = array_values(array_unique($arOneCondition['value']));
                }
            }

            if (!$boolError)
            {
                if (isset($arControl['PHP_VALUE']) && isset($arControl['PHP_VALUE']['VALIDATE']) && !empty($arControl['PHP_VALUE']['VALIDATE']))
                {
                    $arValidate = static::Validate($arOneCondition, $arParams, $arControl, $boolShow);
                    if ($arValidate === false)
                    {
                        $boolError = true;
                    }
                    else
                    {
                        $arValues['value'] = $arValidate['values'];
                        if (isset($arValidate['labels']))
                            $arLabels['value'] = $arValidate['labels'];
                    }
                }
                else
                {
                    $arValues['value'] = $arOneCondition['value'];
                }
            }
        }

        if ($boolShow)
        {
            $arResult = array(
                'id' => $arParams['COND_NUM'],
                'controlId' => $arControl['ID'],
                'values' => $arValues,
            );
            if (!empty($arLabels))
                $arResult['labels'] = $arLabels;
            if ($boolError)
            {
                $arResult['err_cond'] = 'Y';
                if ($boolFatalError)
                    $arResult['fatal_err_cond'] = 'Y';
                if (!empty($arMsg))
                    $arResult['err_cond_mess'] = implode('. ', $arMsg);
            }

            return $arResult;
        }
        else
        {
            $arResult = $arValues;
            return (!$boolError ? $arResult : false);
        }
    }

    public static function Validate($arOneCondition, $arParams, $arControl, $boolShow)
    {
        static $userNameFormat = null;

        $boolShow = ($boolShow === true);
        $boolError = false;
        $arMsg = array();

        $arResult = array(
            'values' => '',
        );

        if (!(isset($arControl['PHP_VALUE']) && isset($arControl['PHP_VALUE']['VALIDATE']) && !empty($arControl['PHP_VALUE']['VALIDATE'])))
        {
            $boolError = true;
        }

        if (!$boolError)
        {
            if ($boolShow)
            {
                // validate for show
                $boolMulti = is_array($arOneCondition['value']);
                switch($arControl['PHP_VALUE']['VALIDATE'])
                {
                    case 'element':
                        $rsItems = \CIBlockElement::GetList(
                            array(),
                            array('ID' => $arOneCondition['value']),
                            false,
                            false,
                            array('ID', 'NAME')
                        );
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            while ($arItem = $rsItems->Fetch())
                            {
                                $arCheckResult[(int)$arItem['ID']] = $arItem['NAME'];
                            }
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = array_keys($arCheckResult);
                                $arResult['labels'] = array_values($arCheckResult);
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_ELEMENT_ABSENT_MULTI');
                            }
                        }
                        else
                        {
                            if ($arItem = $rsItems->Fetch())
                            {
                                $arResult['values'] = (int)$arItem['ID'];
                                $arResult['labels'] = $arItem['NAME'];
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_ELEMENT_ABSENT');
                            }
                        }
                        break;
                    case 'section':
                        $rsSections = \CIBlockSection::GetList(
                            array(),
                            array('ID' => $arOneCondition['value']),
                            false,
                            array('ID', 'NAME')
                        );
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            while ($arSection = $rsSections->Fetch())
                            {
                                $arCheckResult[(int)$arSection['ID']] = $arSection['NAME'];
                            }
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = array_keys($arCheckResult);
                                $arResult['labels'] = array_values($arCheckResult);
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_SECTION_ABSENT_MULTI');
                            }
                        }
                        else
                        {
                            if ($arSection = $rsSections->Fetch())
                            {
                                $arResult['values'] = (int)$arSection['ID'];
                                $arResult['labels'] = $arSection['NAME'];
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_SECTION_ABSENT');
                            }
                        }
                        break;
                    case 'iblock':
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            foreach ($arOneCondition['value'] as &$intIBlockID)
                            {
                                $strName = \CIBlock::GetArrayByID($intIBlockID, 'NAME');
                                if ($strName !== false && $strName !== null)
                                {
                                    $arCheckResult[$intIBlockID] = $strName;
                                }
                            }
                            if (isset($intIBlockID))
                                unset($intIBlockID);
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = array_keys($arCheckResult);
                                $arResult['labels'] = array_values($arCheckResult);
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_IBLOCK_ABSENT_MULTI');
                            }
                        }
                        else
                        {
                            $strName = \CIBlock::GetArrayByID($arOneCondition['value'], 'NAME');
                            if ($strName !== false && $strName !== null)
                            {
                                $arResult['values'] = $arOneCondition['value'];
                                $arResult['labels'] = $strName;
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_IBLOCK_ABSENT');
                            }
                        }
                        break;
                    case 'enumValue':
                        $iterator = Iblock\PropertyEnumerationTable::getList(array(
                            'select' => array('ID', 'VALUE'),
                            'filter' => array('@ID' => $arOneCondition['value'])
                        ));
                        if ($boolMulti)
                        {
                            $checkResult = array();
                            while ($row = $iterator->fetch())
                                $checkResult[$row['ID']] = $row['VALUE'];
                            unset($row);
                            if (!empty($checkResult))
                            {
                                $arResult['values'] = array_keys($checkResult);
                                $arResult['labels'] = array_values($checkResult);
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_ENUM_VALUE_ABSENT_MULTI');
                            }
                            unset($checkResult);
                        }
                        else
                        {
                            $row = $iterator->fetch();
                            if (!empty($row))
                            {
                                $arResult['values'] = $row['ID'];
                                $arResult['labels'] = $row['VALUE'];
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_ENUM_VALUE_ABSENT');
                            }
                        }
                        unset($iterator);
                        break;
                    case 'user':
                        if ($userNameFormat === null)
                            $userNameFormat = \CSite::GetNameFormat(true);
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            $userIterator = UserTable::getList(array(
                                'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
                                'filter' => array('ID' => $arOneCondition['value'])
                            ));
                            while ($user = $userIterator->fetch())
                            {
                                $user['ID'] = (int)$user['ID'];
                                $arCheckResult[$user['ID']] = \CUser::FormatName($userNameFormat, $user);
                            }
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = array_keys($arCheckResult);
                                $arResult['labels'] = array_values($arCheckResult);
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_USER_ABSENT_MULTI');
                            }
                        }
                        else
                        {
                            $userIterator = UserTable::getList(array(
                                'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
                                'filter' => array('ID' => $arOneCondition['value'])
                            ));
                            if ($user = $userIterator->fetch())
                            {
                                $arResult['values'] = (int)$user['ID'];
                                $arResult['labels'] = \CUser::FormatName($userNameFormat, $user);
                            }
                            else
                            {
                                $boolError = true;
                                $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_USER_ABSENT');
                            }
                        }
                        break;
                    case 'list':
                        if (isset($arControl['JS_VALUE']) && isset($arControl['JS_VALUE']['values']) && !empty($arControl['JS_VALUE']['values']))
                        {
                            if ($boolMulti)
                            {
                                $arCheckResult = array();
                                foreach ($arOneCondition['value'] as &$strValue)
                                {
                                    if (isset($arControl['JS_VALUE']['values'][$strValue]))
                                        $arCheckResult[] = $strValue;
                                }
                                if (isset($strValue))
                                    unset($strValue);
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'] = $arCheckResult;
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_LIST_ABSENT_MULTI');
                                }
                            }
                            else
                            {
                                if (isset($arControl['JS_VALUE']['values'][$arOneCondition['value']]))
                                {
                                    $arResult['values'] = $arOneCondition['value'];
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_LIST_ABSENT');
                                }
                            }
                        }
                        else
                        {
                            $boolError = true;
                        }
                        break;
                }
            }
            else
            {
                // validate for save
                $boolMulti = is_array($arOneCondition['value']);
                switch($arControl['PHP_VALUE']['VALIDATE'])
                {
                    case 'element':
                        $rsItems = \CIBlockElement::GetList(array(), array('ID' => $arOneCondition['value']), false, false, array('ID'));
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            while ($arItem = $rsItems->Fetch())
                            {
                                $arCheckResult[] = (int)$arItem['ID'];
                            }
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = $arCheckResult;
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        else
                        {
                            if ($arItem = $rsItems->Fetch())
                            {
                                $arResult['values'] = (int)$arItem['ID'];
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        break;
                    case 'section':
                        $rsSections = \CIBlockSection::GetList(array(), array('ID' => $arOneCondition['value']), false, array('ID'));
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            while ($arSection = $rsSections->Fetch())
                            {
                                $arCheckResult[] = (int)$arSection['ID'];
                            }
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = $arCheckResult;
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        else
                        {
                            if ($arSection = $rsSections->Fetch())
                            {
                                $arResult['values'] = (int)$arSection['ID'];
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        break;
                    case 'iblock':
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            foreach ($arOneCondition['value'] as &$intIBlockID)
                            {
                                $strName = \CIBlock::GetArrayByID($intIBlockID, 'NAME');
                                if ($strName !== false && $strName !== null)
                                {
                                    $arCheckResult[] = $intIBlockID;
                                }
                            }
                            if (isset($intIBlockID))
                                unset($intIBlockID);
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = $arCheckResult;
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        else
                        {
                            $strName = \CIBlock::GetArrayByID($arOneCondition['value'], 'NAME');
                            if ($strName !== false && $strName !== null)
                            {
                                $arResult['values'] = $arOneCondition['value'];
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        break;
                    case 'enumValue':
                        $iterator = Iblock\PropertyEnumerationTable::getList(array(
                            'select' => array('ID'),
                            'filter' => array('@ID' => $arOneCondition['value'])
                        ));
                        if ($boolMulti)
                        {
                            $checkResult = array();
                            while ($row = $iterator->fetch())
                                $checkResult[] = (int)$row['ID'];
                            unset($row);
                            if (!empty($checkResult))
                                $arResult['values'] = $checkResult;
                            else
                                $boolError = true;
                            unset($checkResult);
                        }
                        else
                        {
                            $row = $iterator->fetch();
                            if (!empty($row))
                                $arResult['values'] = (int)$row['ID'];
                            else
                                $boolError = true;
                            unset($row);
                        }
                        unset($iterator);
                        break;
                    case 'user':
                        if ($boolMulti)
                        {
                            $arCheckResult = array();
                            $userIterator = UserTable::getList(array(
                                'select' => array('ID'),
                                'filter' => array('ID' => $arOneCondition['value'])
                            ));
                            while ($user = $userIterator->fetch())
                            {
                                $arCheckResult[] = (int)$user['ID'];
                            }
                            if (!empty($arCheckResult))
                            {
                                $arResult['values'] = $arCheckResult;
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        else
                        {
                            $userIterator = UserTable::getList(array(
                                'select' => array('ID'),
                                'filter' => array('ID' => $arOneCondition['value'])
                            ));
                            if ($user = $userIterator->fetch())
                            {
                                $arResult['values'] = (int)$user['ID'];
                            }
                            else
                            {
                                $boolError = true;
                            }
                        }
                        break;
                    case 'list':
                        if (isset($arControl['JS_VALUE']) && isset($arControl['JS_VALUE']['values']) && !empty($arControl['JS_VALUE']['values']))
                        {
                            if ($boolMulti)
                            {
                                $arCheckResult = array();
                                foreach ($arOneCondition['value'] as &$strValue)
                                {
                                    if (isset($arControl['JS_VALUE']['values'][$strValue]))
                                        $arCheckResult[] = $strValue;
                                }
                                if (isset($strValue))
                                    unset($strValue);
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'] = $arCheckResult;
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            else
                            {
                                if (isset($arControl['JS_VALUE']['values'][$arOneCondition['value']]))
                                {
                                    $arResult['values'] = $arOneCondition['value'];
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                        }
                        else
                        {
                            $boolError = true;
                        }
                        break;
                }
            }
        }

        if ($boolShow)
        {
            if ($boolError)
            {
                $arResult['err_cond'] = 'Y';
                $arResult['err_cond_mess'] = $arMsg;
            }
            return $arResult;
        }
        else
        {
            return (!$boolError ? $arResult : false);
        }
    }

    public static function CheckAtoms($arOneCondition, $arParams, $arControl, $boolShow)
    {
        $boolShow = (true === $boolShow);
        $boolError = false;
        $boolFatalError = false;
        $arMsg = array();

        $arValues = array();
        $arLabels = array();

        static $intTimeOffset = false;
        if ($intTimeOffset === false)
            $intTimeOffset = \CTimeZone::GetOffset();

        if (!isset($arControl['ATOMS']) || empty($arControl['ATOMS']) || !is_array($arControl['ATOMS']))
        {
            $boolFatalError = true;
            $boolError = true;
            $arMsg[] = Loc::getMessage('BT_GLOBAL_COND_ERR_ATOMS_ABSENT');
        }
        if (!$boolError)
        {
            $boolValidate = false;
            if ($boolShow)
            {
                foreach ($arControl['ATOMS'] as &$arOneAtom)
                {
                    $boolAtomError = false;
                    $strID = $arOneAtom['ATOM']['ID'];
                    $boolMulti = false;
                    if (!isset($arOneCondition[$strID]))
                    {
                        $boolAtomError = true;
                    }
                    else
                    {
                        $boolMulti = is_array($arOneCondition[$strID]);
                        switch ($arOneAtom['ATOM']['FIELD_TYPE'])
                        {
                            case 'int':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strID] as &$intOneValue)
                                    {
                                        $intOneValue = (int)$intOneValue;
                                    }
                                    if (isset($intOneValue))
                                        unset($intOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strID] = (int)$arOneCondition[$strID];
                                }
                                break;
                            case 'double':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strID] as &$dblOneValue)
                                    {
                                        $dblOneValue = (float)$dblOneValue;
                                    }
                                    if (isset($dblOneValue))
                                        unset($dblOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strID] = doubleval($arOneCondition[$strID]);
                                }
                                break;
                            case 'strdouble':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strID] as &$dblOneValue)
                                    {
                                        if ($dblOneValue !== '')
                                            $dblOneValue = (float)$dblOneValue;
                                    }
                                    if (isset($dblOneValue))
                                        unset($dblOneValue);
                                }
                                else
                                {
                                    if ($arOneCondition[$strID] !== '')
                                        $arOneCondition[$strID] = (float)$arOneCondition[$strID];
                                }
                                break;
                            case 'char':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strID] as &$strOneValue)
                                    {
                                        $strOneValue = substr($strOneValue, 0, 1);
                                    }
                                    if (isset($strOneValue))
                                        unset($strOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strID] = substr($arOneCondition[$strID], 0, 1);
                                }
                                break;
                            case 'string':
                                $intMaxLen = (int)(isset($arOneAtom['ATOM']['FIELD_LENGTH']) ? $arOneAtom['ATOM']['FIELD_LENGTH'] : 255);
                                if ($intMaxLen <= 0)
                                    $intMaxLen = 255;
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strID] as &$strOneValue)
                                    {
                                        $strOneValue = substr($strOneValue, 0, $intMaxLen);
                                    }
                                    if (isset($strOneValue))
                                        unset($strOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strID] = substr($arOneCondition[$strID], 0, $intMaxLen);
                                }
                                break;
                            case 'text':
                                break;
                            case 'date':
                            case 'datetime':
                                if ($arOneAtom['ATOM']['FIELD_TYPE'] == 'date')
                                {
                                    $strFormat = 'SHORT';
                                    $intOffset = 0;
                                }
                                else
                                {
                                    $strFormat = 'FULL';
                                    $intOffset = $intTimeOffset;
                                }
                                $boolAtomError = static::ConvertInt2DateTime($arOneCondition[$strID], $strFormat, $intOffset);
                                break;
                            default:
                                $boolAtomError = true;
                        }
                    }
                    if (!$boolAtomError)
                    {
                        if ($boolMulti)
                            $arOneCondition[$strID] = array_values(array_unique($arOneCondition[$strID]));
                        $arValues[$strID] = $arOneCondition[$strID];
                        if (isset($arOneAtom['ATOM']['VALIDATE']) && !empty($arOneAtom['ATOM']['VALIDATE']))
                            $boolValidate = true;
                    }
                    else
                    {
                        $arValues[$strID] = '';
                    }
                    if ($boolAtomError)
                        $boolError = true;
                }
                if (isset($arOneAtom))
                    unset($arOneAtom);

                if (!$boolError)
                {
                    if ($boolValidate)
                    {
                        $arValidate = static::ValidateAtoms($arValues, $arParams, $arControl, $boolShow);
                        if ($arValidate === false)
                        {
                            $boolError = true;
                        }
                        else
                        {
                            if (isset($arValidate['err_cond']) && $arValidate['err_cond'] == 'Y')
                            {
                                $boolError = true;
                                if (isset($arValidate['err_cond_mess']) && !empty($arValidate['err_cond_mess']))
                                    $arMsg = array_merge($arMsg, $arValidate['err_cond_mess']);
                            }
                            else
                            {
                                $arValues = $arValidate['values'];
                                if (isset($arValidate['labels']))
                                    $arLabels = $arValidate['labels'];
                            }
                        }
                    }
                }
            }
            else
            {
                foreach ($arControl['ATOMS'] as &$arOneAtom)
                {
                    $boolAtomError = false;
                    $strID = $arOneAtom['ATOM']['ID'];
                    $strName = $arOneAtom['JS']['name'];
                    $boolMulti = false;
                    if (!isset($arOneCondition[$strName]))
                    {
                        $boolAtomError = true;
                    }
                    else
                    {
                        $boolMulti = is_array($arOneCondition[$strName]);
                    }
                    if (!$boolAtomError)
                    {
                        switch ($arOneAtom['ATOM']['FIELD_TYPE'])
                        {
                            case 'int':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strName] as &$intOneValue)
                                    {
                                        $intOneValue = (int)$intOneValue;
                                    }
                                    if (isset($intOneValue))
                                        unset($intOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strName] = (int)$arOneCondition[$strName];
                                }
                                break;
                            case 'double':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strName] as &$dblOneValue)
                                    {
                                        $dblOneValue = (float)$dblOneValue;
                                    }
                                    if (isset($dblOneValue))
                                        unset($dblOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strName] = (float)$arOneCondition[$strName];
                                }
                                break;
                            case 'strdouble':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strName] as &$dblOneValue)
                                    {
                                        if ($dblOneValue !== '')
                                            $dblOneValue = (float)$dblOneValue;
                                    }
                                    if (isset($dblOneValue))
                                        unset($dblOneValue);
                                }
                                else
                                {
                                    if ($arOneCondition[$strName] !== '')
                                    {
                                        $arOneCondition[$strName] = (float)$arOneCondition[$strName];
                                    }
                                }
                                break;
                            case 'char':
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strName] as &$strOneValue)
                                    {
                                        $strOneValue = substr($strOneValue, 0, 1);
                                    }
                                    if (isset($strOneValue))
                                        unset($strOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strName] = substr($arOneCondition[$strName], 0, 1);
                                }
                                break;
                            case 'string':
                                $intMaxLen = (int)(isset($arOneAtom['ATOM']['FIELD_LENGTH']) ? $arOneAtom['ATOM']['FIELD_LENGTH'] : 255);
                                if ($intMaxLen <= 0)
                                    $intMaxLen = 255;
                                if ($boolMulti)
                                {
                                    foreach ($arOneCondition[$strName] as &$strOneValue)
                                    {
                                        $strOneValue = substr($strOneValue, 0, $intMaxLen);
                                    }
                                    if (isset($strOneValue))
                                        unset($strOneValue);
                                }
                                else
                                {
                                    $arOneCondition[$strName] = substr($arOneCondition[$strName], 0, $intMaxLen);
                                }
                                break;
                            case 'text':
                                break;
                            case 'date':
                            case 'datetime':
                                if ($arOneAtom['ATOM']['FIELD_TYPE'] == 'date')
                                {
                                    $strFormat = 'SHORT';
                                    $intOffset = 0;
                                }
                                else
                                {
                                    $strFormat = 'FULL';
                                    $intOffset = $intTimeOffset;
                                }
                                $boolAtomError = static::ConvertDateTime2Int($arOneCondition[$strName], $strFormat, $intOffset);
                                break;
                            default:
                                $boolAtomError = true;
                        }
                        if (!$boolAtomError)
                        {
                            if ($boolMulti)
                                $arOneCondition[$strName] = array_values(array_unique($arOneCondition[$strName]));
                            $arValues[$strID] = $arOneCondition[$strName];
                            if (isset($arOneAtom['ATOM']['VALIDATE']) && !empty($arOneAtom['ATOM']['VALIDATE']))
                                $boolValidate = true;
                        }
                        else
                        {
                            $arValues[$strID] = '';
                        }
                    }
                    if ($boolAtomError)
                        $boolError = true;
                }
                if (isset($arOneAtom))
                    unset($arOneAtom);

                if (!$boolError)
                {
                    if ($boolValidate)
                    {
                        $arValidate = static::ValidateAtoms($arValues, $arParams, $arControl, $boolShow);
                        if ($arValidate === false)
                        {
                            $boolError = true;
                        }
                        else
                        {
                            $arValues = $arValidate['values'];
                            if (isset($arValidate['labels']))
                                $arLabels = $arValidate['labels'];
                        }
                    }
                }
            }
        }

        if ($boolShow)
        {
            $arResult = array(
                'id' => $arParams['COND_NUM'],
                'controlId' => $arControl['ID'],
                'values' => $arValues
            );
            if (!empty($arLabels))
                $arResult['labels'] = $arLabels;
            if ($boolError)
            {
                $arResult['err_cond'] = 'Y';
                if ($boolFatalError)
                    $arResult['fatal_err_cond'] = 'Y';
                if (!empty($arMsg))
                    $arResult['err_cond_mess'] = implode('. ', $arMsg);
            }
            return $arResult;
        }
        else
        {
            return (!$boolError ? $arValues : false);
        }
    }

    public static function ValidateAtoms($arValues, $arParams, $arControl, $boolShow)
    {
        static $userNameFormat = null;

        $boolShow = ($boolShow === true);
        $boolError = false;
        $arMsg = array();

        $arResult = array(
            'values' => array(),
            'labels' => array(),
            'titles' => array()
        );

        if (!isset($arControl['ATOMS']) || empty($arControl['ATOMS']) || !is_array($arControl['ATOMS']))
        {
            $boolError = true;
            $arMsg[] = Loc::getMessage('BT_GLOBAL_COND_ERR_ATOMS_ABSENT');
        }
        if (!$boolError)
        {
            if ($boolShow)
            {
                foreach ($arControl['ATOMS'] as &$arOneAtom)
                {
                    $strID = $arOneAtom['ATOM']['ID'];
                    if (!isset($arOneAtom['ATOM']['VALIDATE']) || empty($arOneAtom['ATOM']['VALIDATE']))
                    {
                        $arResult['values'][$strID] = $arValues[$strID];
                        continue;
                    }
                    switch ($arOneAtom['ATOM']['VALIDATE'])
                    {
                        case 'list':
                            if (isset($arOneAtom['JS']) && isset($arOneAtom['JS']['values']) && !empty($arOneAtom['JS']['values']))
                            {
                                if (is_array($arValues[$strID]))
                                {
                                    $arCheckResult = array();
                                    foreach ($arValues[$strID] as &$strValue)
                                    {
                                        if (isset($arOneAtom['JS']['values'][$strValue]))
                                            $arCheckResult[] = $strValue;
                                    }
                                    if (isset($strValue))
                                        unset($strValue);
                                    if (!empty($arCheckResult))
                                    {
                                        $arResult['values'][$strID] = $arCheckResult;
                                    }
                                    else
                                    {
                                        $boolError = true;
                                        $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_LIST_ABSENT_MULTI');
                                    }
                                }
                                else
                                {
                                    if (isset($arOneAtom['JS']['values'][$arValues[$strID]]))
                                    {
                                        $arResult['values'][$strID] = $arValues[$strID];
                                    }
                                    else
                                    {
                                        $boolError = true;
                                        $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_LIST_ABSENT');
                                    }
                                }
                            }
                            else
                            {
                                $boolError = true;
                            }
                            break;
                        case 'element':
                            $rsItems = \CIBlockElement::GetList(array(), array('ID' => $arValues[$strID]), false, false, array('ID', 'NAME'));
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                while ($arItem = $rsItems->Fetch())
                                {
                                    $arCheckResult[(int)$arItem['ID']] = $arItem['NAME'];
                                }
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = array_keys($arCheckResult);
                                    $arResult['labels'][$strID] = array_values($arCheckResult);
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_ELEMENT_ABSENT_MULTI');
                                }
                            }
                            else
                            {
                                if ($arItem = $rsItems->Fetch())
                                {
                                    $arResult['values'][$strID] = (int)$arItem['ID'];
                                    $arResult['labels'][$strID] = $arItem['NAME'];
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_ELEMENT_ABSENT');
                                }
                            }
                            break;
                        case 'section':
                            $rsSections = \CIBlockSection::GetList(array(), array('ID' => $arValues[$strID]), false, array('ID', 'NAME'));
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                while ($arSection = $rsSections->Fetch())
                                {
                                    $arCheckResult[(int)$arSection['ID']] = $arSection['NAME'];
                                }
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = array_keys($arCheckResult);
                                    $arResult['labels'][$strID] = array_values($arCheckResult);
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_SECTION_ABSENT_MULTI');
                                }
                            }
                            else
                            {
                                if ($arSection = $rsSections->Fetch())
                                {
                                    $arResult['values'][$strID] = (int)$arSection['ID'];
                                    $arResult['labels'][$strID] = $arSection['NAME'];
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_SECTION_ABSENT');
                                }
                            }
                            break;
                        case 'iblock':
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                foreach ($arValues[$strID] as &$intIBlockID)
                                {
                                    $strName = \CIBlock::GetArrayByID($intIBlockID, 'NAME');
                                    if ($strName !== false && $strName !== null)
                                    {
                                        $arCheckResult[$intIBlockID] = $strName;
                                    }
                                }
                                if (isset($intIBlockID))
                                    unset($intIBlockID);
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = array_keys($arCheckResult);
                                    $arResult['labels'][$strID] = array_values($arCheckResult);
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_IBLOCK_ABSENT_MULTI');
                                }
                            }
                            else
                            {
                                $strName = \CIBlock::GetArrayByID($arValues[$strID], 'NAME');
                                if ($strName !== false && $strName !== null)
                                {
                                    $arResult['values'][$strID] = $arValues[$strID];
                                    $arResult['labels'][$strID] = $strName;
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_IBLOCK_ABSENT');
                                }
                            }
                            break;
                        case 'user':
                            if ($userNameFormat === null)
                                $userNameFormat = \CSite::GetNameFormat(true);
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                $userIterator = UserTable::getList(array(
                                    'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
                                    'filter' => array('ID' => $arValues[$strID])
                                ));
                                while ($user = $userIterator->fetch())
                                {
                                    $user['ID'] = (int)$user['ID'];
                                    $arCheckResult[$user['ID']] = \CUser::FormatName($userNameFormat, $user);
                                }
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = array_keys($arCheckResult);
                                    $arResult['labels'][$strID] = array_values($arCheckResult);
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_USER_ABSENT_MULTI');
                                }
                            }
                            else
                            {
                                $userIterator = UserTable::getList(array(
                                    'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
                                    'filter' => array('ID' => $arValues[$strID])
                                ));
                                if ($user = $userIterator->fetch())
                                {
                                    $arResult['values'] = (int)$user['ID'];
                                    $arResult['labels'] = \CUser::FormatName($userNameFormat, $user);
                                }
                                else
                                {
                                    $boolError = true;
                                    $arMsg[] = Loc::getMessage('BT_MOD_COND_ERR_CHECK_DATA_USER_ABSENT');
                                }
                            }
                            break;
                    }
                }
                if (isset($arOneAtom))
                    unset($arOneAtom);
            }
            else
            {
                foreach ($arControl['ATOMS'] as &$arOneAtom)
                {
                    $strID = $arOneAtom['ATOM']['ID'];
                    if (!isset($arOneAtom['ATOM']['VALIDATE']) || empty($arOneAtom['ATOM']['VALIDATE']))
                    {
                        $arResult['values'][$strID] = $arValues[$strID];
                        continue;
                    }
                    switch ($arOneAtom['ATOM']['VALIDATE'])
                    {
                        case 'list':
                            if (isset($arOneAtom['JS']) && isset($arOneAtom['JS']['values']) && !empty($arOneAtom['JS']['values']))
                            {
                                if (is_array($arValues[$strID]))
                                {
                                    $arCheckResult = array();
                                    foreach ($arValues[$strID] as &$strValue)
                                    {
                                        if (isset($arOneAtom['JS']['values'][$strValue]))
                                            $arCheckResult[] = $strValue;
                                    }
                                    if (isset($strValue))
                                        unset($strValue);
                                    if (!empty($arCheckResult))
                                    {
                                        $arResult['values'][$strID] = $arCheckResult;
                                    }
                                    else
                                    {
                                        $boolError = true;
                                    }
                                }
                                else
                                {
                                    if (isset($arOneAtom['JS']['values'][$arValues[$strID]]))
                                    {
                                        $arResult['values'][$strID] = $arValues[$strID];
                                    }
                                    else
                                    {
                                        $boolError = true;
                                    }
                                }
                            }
                            else
                            {
                                $boolError = true;
                            }
                            break;
                        case 'element':
                            $rsItems = \CIBlockElement::GetList(array(), array('ID' => $arValues[$strID]), false, false, array('ID'));
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                while ($arItem = $rsItems->Fetch())
                                {
                                    $arCheckResult[] = (int)$arItem['ID'];
                                }
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = $arCheckResult;
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            else
                            {
                                if ($arItem = $rsItems->Fetch())
                                {
                                    $arResult['values'][$strID] = (int)$arItem['ID'];
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            break;
                        case 'section':
                            $rsSections = \CIBlockSection::GetList(array(), array('ID' => $arValues[$strID]), false, array('ID'));
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                while ($arSection = $rsSections->Fetch())
                                {
                                    $arCheckResult[] = (int)$arSection['ID'];
                                }
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = $arCheckResult;
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            else
                            {
                                if ($arSection = $rsSections->Fetch())
                                {
                                    $arResult['values'][$strID] = (int)$arSection['ID'];
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            break;
                        case 'iblock':
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                foreach ($arValues[$strID] as &$intIBlockID)
                                {
                                    $strName = \CIBlock::GetArrayByID($intIBlockID, 'NAME');
                                    if ($strName !== false && $strName !== null)
                                    {
                                        $arCheckResult[] = $intIBlockID;
                                    }
                                }
                                if (isset($intIBlockID))
                                    unset($intIBlockID);
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = $arCheckResult;
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            else
                            {
                                $strName = \CIBlock::GetArrayByID($arValues[$strID], 'NAME');
                                if ($strName !== false && $strName !== null)
                                {
                                    $arResult['values'][$strID] = $arValues[$strID];
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            break;
                        case 'user':
                            if (is_array($arValues[$strID]))
                            {
                                $arCheckResult = array();
                                $userIterator = UserTable::getList(array(
                                    'select' => array('ID'),
                                    'filter' => array('ID' => $arValues[$strID])
                                ));
                                while ($user = $userIterator->fetch())
                                {
                                    $arCheckResult[] = (int)$user['ID'];
                                }
                                if (!empty($arCheckResult))
                                {
                                    $arResult['values'][$strID] = $arCheckResult;
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            else
                            {
                                $userIterator = UserTable::getList(array(
                                    'select' => array('ID'),
                                    'filter' => array('ID' => $arValues[$strID])
                                ));
                                if ($user = $userIterator->fetch())
                                {
                                    $arCheckResult[] = (int)$user['ID'];
                                }
                                else
                                {
                                    $boolError = true;
                                }
                            }
                            break;
                    }
                }
                if (isset($arOneAtom))
                    unset($arOneAtom);
            }
        }

        if ($boolShow)
        {
            if ($boolError)
            {
                $arResult['err_cond'] = 'Y';
                $arResult['err_cond_mess'] = $arMsg;
            }
            return $arResult;
        }
        else
        {
            return (!$boolError ? $arResult : false);
        }
    }

    public static function UndefinedCondition($boolFatal = false)
    {
        $boolFatal = (true === $boolFatal);
        $arResult = array(
            ''
        );
    }

    static function LogicGreat($arField, $mxValue)
    {
        $boolResult = false;
        if (!is_array($arField))
            $arField = array($arField);
        if (!empty($arField))
        {
            foreach ($arField as &$mxOneValue)
            {
                if ($mxOneValue === null || $mxOneValue === false || $mxOneValue === '')
                    continue;
                if ($mxOneValue > $mxValue)
                {
                    $boolResult = true;
                    break;
                }
            }
            if (isset($mxOneValue))
                unset($mxOneValue);
        }
        return $boolResult;
    }

    static function LogicLess($arField, $mxValue)
    {
        $boolResult = false;
        if (!is_array($arField))
            $arField = array($arField);
        if (!empty($arField))
        {
            foreach ($arField as &$mxOneValue)
            {
                if ($mxOneValue === null || $mxOneValue === false || $mxOneValue === '')
                    continue;
                if ($mxOneValue < $mxValue)
                {
                    $boolResult = true;
                    break;
                }
            }
            if (isset($mxOneValue))
                unset($mxOneValue);
        }
        return $boolResult;
    }

    static function LogicEqualGreat($arField, $mxValue)
    {
        $boolResult = false;
        if (!is_array($arField))
            $arField = array($arField);
        if (!empty($arField))
        {
            foreach ($arField as &$mxOneValue)
            {
                if ($mxOneValue === null || $mxOneValue === false || $mxOneValue === '')
                    continue;
                if ($mxOneValue >= $mxValue)
                {
                    $boolResult = true;
                    break;
                }
            }
            if (isset($mxOneValue))
                unset($mxOneValue);
        }
        return $boolResult;
    }

    static function LogicEqualLess($arField, $mxValue)
    {
        $boolResult = false;
        if (!is_array($arField))
            $arField = array($arField);
        if (!empty($arField))
        {
            foreach ($arField as &$mxOneValue)
            {
                if ($mxOneValue === null || $mxOneValue === false || $mxOneValue === '')
                    continue;
                if ($mxOneValue <= $mxValue)
                {
                    $boolResult = true;
                    break;
                }
            }
            if (isset($mxOneValue))
                unset($mxOneValue);
        }
        return $boolResult;
    }

    static function LogicContain($arField, $mxValue)
    {
        $boolResult = false;
        if (!is_array($arField))
            $arField = array($arField);
        if (!empty($arField))
        {
            foreach ($arField as &$mxOneValue)
            {
                if (strpos($mxOneValue, $mxValue) !== false)
                {
                    $boolResult = true;
                    break;
                }
            }
            if (isset($mxOneValue))
                unset($mxOneValue);
        }
        return $boolResult;
    }

    static function LogicNotContain($arField, $mxValue)
    {
        $boolResult = true;
        if (!is_array($arField))
            $arField = array($arField);
        if (!empty($arField))
        {
            foreach ($arField as &$mxOneValue)
            {
                if (strpos($mxOneValue, $mxValue) !== false)
                {
                    $boolResult = false;
                    break;
                }
            }
            if (isset($mxOneValue))
                unset($mxOneValue);
        }
        return $boolResult;
    }

    public static function ClearValue(&$mxValues)
    {
        $boolLocalError = false;
        if (is_array($mxValues))
        {
            if (!empty($mxValues))
            {
                $arResult = array();
                foreach ($mxValues as &$strOneValue)
                {
                    $strOneValue = trim((string)$strOneValue);
                    if ($strOneValue !== '')
                        $arResult[] = $strOneValue;
                }
                if (isset($strOneValue))
                    unset($strOneValue);
                $mxValues = $arResult;
                if (empty($mxValues))
                    $boolLocalError = true;
            }
            else
            {
                $boolLocalError = true;
            }
        }
        else
        {
            $mxValues = trim((string)$mxValues);
            if ($mxValues === '')
            {
                $boolLocalError = true;
            }
        }
        return $boolLocalError;
    }

    static function ConvertInt2DateTime(&$mxValues, $strFormat, $intOffset)
    {
        global $DB;

        $boolValueError = false;
        if (is_array($mxValues))
        {
            foreach ($mxValues as &$strValue)
            {
                if ($strValue.'!' == (int)$strValue.'!')
                {
                    $strValue = \ConvertTimeStamp($strValue + $intOffset, $strFormat);
                }
                if (!$DB->IsDate($strValue, false, false, $strFormat))
                {
                    $boolValueError = true;
                }
            }
            if (isset($strValue))
                unset($strValue);
        }
        else
        {
            if ($mxValues.'!' == (int)$mxValues.'!')
            {
                $mxValues = \ConvertTimeStamp($mxValues + $intOffset, $strFormat);
            }
            $boolValueError = !$DB->IsDate($mxValues, false, false, $strFormat);
        }
        return $boolValueError;
    }

    static function ConvertDateTime2Int(&$mxValues, $strFormat, $intOffset)
    {
        global $DB;

        $boolError = false;
        if (is_array($mxValues))
        {
            $boolLocalErr = false;
            $arLocal = array();
            foreach ($mxValues as &$strValue)
            {
                if ($strValue.'!' != (int)$strValue.'!')
                {
                    if (!$DB->IsDate($strValue, false, false, $strFormat))
                    {
                        $boolError = true;
                        $boolLocalErr = true;
                        break;
                    }
                    $arLocal[] = MakeTimeStamp($strValue) - $intOffset;
                }
                else
                {
                    $arLocal[] = $strValue;
                }
            }
            if (isset($strValue))
                unset($strValue);
            if (!$boolLocalErr)
                $mxValues = $arLocal;
        }
        else
        {
            if ($mxValues.'!' != (int)$mxValues.'!')
            {
                if (!$DB->IsDate($mxValues, false, false, $strFormat))
                {
                    $boolError = true;
                }
                else
                {
                    $mxValues = MakeTimeStamp($mxValues) - $intOffset;
                }
            }
        }
        return $boolError;
    }

    /**
     * @param array $atoms
     * @param string|false $controlId
     * @param bool $extendedMode
     * @return array|false
     */
    protected static function searchControlAtoms(array $atoms, $controlId, $extendedMode)
    {
        if (empty($atoms))
            return false;

        $extendedMode = ($extendedMode === true);
        if (!$extendedMode)
        {
            foreach (array_keys($atoms) as $index)
            {
                foreach (array_keys($atoms[$index]) as $atomId)
                {
                    $atoms[$index][$atomId] = $atoms[$index][$atomId]['JS'];
                }
            }
            unset($atomId, $index);
        }

        if ($controlId === false)
            return $atoms;

        $controlId = (string)$controlId;
        return (isset($atoms[$controlId]) ? $atoms[$controlId] : false);
    }

    protected static function searchControl(array $controls, $controlId)
    {
        if (empty($controls))
            return false;

        if ($controlId === false)
            return $controls;

        $controlId = (string)$controlId;
        return (isset($controls[$controlId]) ? $controls[$controlId] : false);
    }
}