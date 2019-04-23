<?php

namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc, \Bitrix\Main\UserTable, \Bitrix\Main, \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondCtrlRobofeedV1ProductParam extends CondCtrlComplex
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

        self::_fillProps();

        $description = parent::GetControlDescr();
        $description['SORT'] = 200;
        return $description;
    }


    protected static $arProps = [];
    
    protected static function _fillProps()
    {
        if (is_null(self::$arProps[self::$intStoreId])) {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();

            if (
            $obCache->startDataCache(60 * 60 * 24 * 7, __METHOD__.'#CURRENCY_LIST',
                \Local\Core\Inner\Cache::getCachePath(['Model', 'Robofeed', 'V1', 'StoreProductParamTable'], ['ParamsConditionList', 'storeId='.self::$intStoreId]))
            ) {
                $rsProps = \Local\Core\Model\Robofeed\StoreProductParamFactory::factory(1)
                    ->setStoreId(self::$intStoreId)::getList([
                        'select' => ['CODE', 'NAME'],
                        'order' => ['NAME' => 'ASC'],
                        'group' => ['CODE']
                    ]);

                while ($ar = $rsProps->fetch()) {
                    self::$arProps[self::$intStoreId][$ar['CODE']] = $ar['NAME'].' ['.$ar['CODE'].']';
                }
                if (empty(self::$arProps[self::$intStoreId])) {
                    $obCache->abortDataCache();
                } else {
                    $obCache->endDataCache(self::$arProps[self::$intStoreId]);
                }
            } else {
                self::$arProps[self::$intStoreId] = $obCache->getVars();
            }
        }
    }


    protected static $arControlIdCache = [];

    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        if( is_null( self::$arControlIdCache[ self::$intStoreId ]  ) )
        {
            self::$arControlIdCache[ self::$intStoreId ] = array_unique(array_map(function ($v)
                {
                    return 'CondParam'.preg_replace('/[^a-zA-Z0-9]/', '', $v);
                }, array_keys(self::$arProps[self::$intStoreId]) ));
        }

        return self::$arControlIdCache[ self::$intStoreId ];
    }

    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' => false,
            'label' => 'Характеристики',
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


    protected static $arControlsCache = [];

    /**
     * @param bool|string $strControlID
     *
     * @return bool|array
     */
    public static function GetControls($strControlID = false)
    {
        if( is_null( self::$arControlsCache[ self::$intStoreId ] ) )
        {
            foreach (self::$arProps[self::$intStoreId] as $k => $v)
            {
                $strId = preg_replace('/[^a-zA-Z0-9_]/', '', $k);
                $strId = explode('_',  $strId);
                $strId = array_map(
                    function($v){
                        return ( mb_strtoupper(substr($v, 0, 1)).mb_strtolower(substr($v, 1)) );
                        },
                    $strId
                );
                $strId = 'CondParam'.implode('', $strId);

                self::$arControlsCache[ self::$intStoreId ][$strId] = [
                    'ID' => $strId,
                    'FIELD' => 'PARAM_'.$k,
                    'FIELD_TYPE' => 'text',
                    'FIELD_LENGTH' => 255,
                    'LABEL' => $v,
                    'PREFIX' => $v,
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
                ];
            }

            foreach (self::$arControlsCache[ self::$intStoreId ] as &$control) {
                $control['EXIST_HANDLER'] = 'Y';
                $control['MODULE_ID'] = 'local.core';

                if (empty($control['MULTIPLE'])) {
                    $control['MULTIPLE'] = 'N';
                }

                $control['GROUP'] = 'N';
            }
            unset($control);
        }



        return static::searchControl(self::$arControlsCache[ self::$intStoreId ], $strControlID);
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
