<?php
namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);


class GlobalCondCtrlComplex extends GlobalCondCtrl
{
    public static function GetControlDescr()
    {
        $strClassName = get_called_class();
        return array(
            'COMPLEX' => 'Y',
            'GetControlShow' => array($strClassName, 'GetControlShow'),
            'GetConditionShow' => array($strClassName, 'GetConditionShow'),
            'IsGroup' => array($strClassName, 'IsGroup'),
            'Parse' => array($strClassName, 'Parse'),
            'Generate' => array($strClassName, 'Generate'),
            'ApplyValues' => array($strClassName, 'ApplyValues'),
            'InitParams' => array($strClassName, 'InitParams'),
            'CONTROLS' => static::GetControls()
        );
    }

    public static function GetConditionShow($arParams)
    {
        if (!isset($arParams['ID']))
            return false;
        $arControl = static::GetControls($arParams['ID']);
        if ($arControl === false)
            return false;
        if (!isset($arParams['DATA']))
            return false;
        return static::Check($arParams['DATA'], $arParams, $arControl, true);
    }

    public static function Parse($arOneCondition)
    {
        if (!isset($arOneCondition['controlId']))
            return false;
        $arControl = static::GetControls($arOneCondition['controlId']);
        if ($arControl === false)
            return false;
        return static::Check($arOneCondition, $arOneCondition, $arControl, false);
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $strResult = '';
        $resultValues = array();
        $arValues = false;

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
                                foreach ($arValues['value'] as &$value)
                                {
                                    $resultValues[] = str_replace(
                                        array('#FIELD#', '#VALUE#'),
                                        array($strField, $value),
                                        $arLogic['OP'][$arControl['MULTIPLE']]
                                    );
                                }
                                unset($value);
                                $strResult = '('.implode($arLogic['MULTI_SEP'], $resultValues).')';
                                unset($resultValues);
                            }
                        }
                        else
                        {
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
                            $strResult = str_replace(
                                array('#FIELD#', '#VALUE#'),
                                array($strField, $arValues['value']),
                                $arLogic['OP'][$arControl['MULTIPLE']]
                            );
                        }
                        break;
                }
            }
        }

        return (!$boolError ? $strResult : false);
    }

    /**
     * @param bool|string $strControlID
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {
        return false;
    }
}