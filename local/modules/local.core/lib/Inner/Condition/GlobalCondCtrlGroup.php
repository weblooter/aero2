<?php
namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);


class GlobalCondCtrlGroup extends GlobalCondCtrl
{
    public static function GetControlDescr()
    {
        $className = get_called_class();
        return array(
            'ID' => static::GetControlID(),
            'GROUP' => 'Y',
            'GetControlShow' => array($className, 'GetControlShow'),
            'GetConditionShow' => array($className, 'GetConditionShow'),
            'IsGroup' => array($className, 'IsGroup'),
            'Parse' => array($className, 'Parse'),
            'Generate' => array($className, 'Generate'),
            'ApplyValues' => array($className, 'ApplyValues')
        );
    }

    public static function GetControlShow($arParams)
    {
        return array(
            'controlId' => static::GetControlID(),
            'group' => true,
            'label' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_LABEL'),
            'defaultText' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_DEF_TEXT'),
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'visual' => static::GetVisual(),
            'control' => array_values(static::GetAtoms())
        );
    }

    public static function GetConditionShow($arParams)
    {
        $error = false;
        $values = array();
        foreach (static::GetAtoms() as $atom)
        {
            if (
                !isset($arParams['DATA'][$atom['id']])
                || !is_string($arParams['DATA'][$atom['id']])
                || !isset($atom['values'][$arParams['DATA'][$atom['id']]])
            )
                $error = true;

            $values[$atom['id']] = ($error ? '' : $arParams['DATA'][$atom['id']]);
        }
        unset($atom);

        $result = array(
            'id' => $arParams['COND_NUM'],
            'controlId' => static::GetControlID(),
            'values' => $values
        );
        if ($error)
            $result['err_cond'] = 'Y';
        unset($values);

        return $result;
    }

    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        return 'CondGroup';
    }

    public static function GetAtoms()
    {
        return array(
            'All' => array(
                'id' => 'All',
                'name' => 'aggregator',
                'type' => 'select',
                'values' => array(
                    'AND' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_SELECT_ALL'),
                    'OR' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_SELECT_ANY')
                ),
                'defaultText' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_SELECT_DEF'),
                'defaultValue' => 'AND',
                'first_option' => '...'
            ),
            'True' => array(
                'id' => 'True',
                'name' => 'value',
                'type' => 'select',
                'values' => array(
                    'True' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_SELECT_TRUE'),
                    'False' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_SELECT_FALSE')
                ),
                'defaultText' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_SELECT_DEF'),
                'defaultValue' => 'True',
                'first_option' => '...'
            )
        );
    }

    public static function GetVisual()
    {
        return array(
            'controls' => array(
                'All',
                'True'
            ),
            'values' => array(
                array(
                    'All' => 'AND',
                    'True' => 'True'
                ),
                array(
                    'All' => 'AND',
                    'True' => 'False'
                ),
                array(
                    'All' => 'OR',
                    'True' => 'True'
                ),
                array(
                    'All' => 'OR',
                    'True' => 'False'
                )
            ),
            'logic' => array(
                array(
                    'style' => 'condition-logic-and',
                    'message' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_LOGIC_AND')
                ),
                array(
                    'style' => 'condition-logic-and',
                    'message' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_LOGIC_NOT_AND')
                ),
                array(
                    'style' => 'condition-logic-or',
                    'message' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_LOGIC_OR')
                ),
                array(
                    'style' => 'condition-logic-or',
                    'message' => Loc::getMessage('LOCAL_CORE_CONDITION_GROUP_LOGIC_NOT_OR')
                )
            )
        );
    }

    public static function IsGroup($strControlID = false)
    {
        return 'Y';
    }

    public static function Parse($arOneCondition)
    {
        $error = false;
        $result = array();
        foreach (static::GetAtoms() as $atom)
        {
            if (
                !isset($arOneCondition[$atom['name']])
                || !is_string($arOneCondition[$atom['name']])
                || !isset($atom['values'][$arOneCondition[$atom['name']]])
            )
            {
                $error = true;
                break;
            }
            $result[$atom['id']] = $arOneCondition[$atom['name']];
        }
        unset($atom);

        return (!$error ? $result : false);
    }

    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        $result = '';
        $error = false;

        foreach (static::GetAtoms() as $atom)
        {
            if (
                !isset($arOneCondition[$atom['id']])
                || !is_string($arOneCondition[$atom['id']])
                || !isset($atom['values'][$arOneCondition[$atom['id']]])
            )
                $error = true;
        }
        unset($atom);

        if (!isset($arSubs) || !is_array($arSubs))
        {
            $error = true;
        }
        elseif (empty($arSubs))
        {
            return '(1 == 1)';
        }

        if (!$error)
        {
            if ('AND' == $arOneCondition['All'])
            {
                $prefix = '';
                $logic = ' && ';
                $itemPrefix = ($arOneCondition['True'] == 'True' ? '' : '!');
            }
            else
            {
                $itemPrefix = '';
                if ($arOneCondition['True'] == 'True')
                {
                    $prefix = '';
                    $logic = ' || ';
                }
                else
                {
                    $prefix = '!';
                    $logic = ' && ';
                }
            }

            $commandLine = $itemPrefix.implode($logic.$itemPrefix, $arSubs);
            if ($prefix != '')
                $commandLine = $prefix.'('.$commandLine.')';
            if ($commandLine != '')
                $commandLine = '('.$commandLine.')';
            $result = $commandLine;
            unset($commandLine);
        }

        return $result;
    }

    public static function ApplyValues($arOneCondition, $arControl)
    {
        return (isset($arOneCondition['True']) && $arOneCondition['True'] == 'True');
    }
}