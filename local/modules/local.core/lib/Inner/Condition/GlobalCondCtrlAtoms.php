<?php
namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class GlobalCondCtrlAtoms extends GlobalCondCtrl
{
    /**
     * @return array|bool
     */
    public static function GetControlDescr()
    {
        $className = get_called_class();
        $controls = static::GetControls();
        if (empty($controls) || !is_array($controls))
            return false;
        $result = array();
        foreach ($controls as &$oneControl)
        {
            unset($oneControl['ATOMS']);
            $row = $oneControl;
            $row['GetControlShow'] = array($className, 'GetControlShow');
            $row['GetConditionShow'] = array($className, 'GetConditionShow');
            $row['IsGroup'] = array($className, 'IsGroup');
            $row['Parse'] = array($className, 'Parse');
            $row['Generate'] = array($className, 'Generate');
            $row['ApplyValues'] = array($className, 'ApplyValues');
            $row['InitParams'] = array($className, 'InitParams');

            $result[] = $row;
            unset($row);
        }
        unset($oneControl, $controls, $className);
        return $result;
    }

    public static function GetConditionShow($params)
    {
        if (!isset($params['ID']))
            return false;
        $atoms = static::GetAtomsEx($params['ID'], true);
        if (empty($atoms))
            return false;
        $control = array(
            'ID' => $params['ID'],
            'ATOMS' => $atoms
        );
        unset($atoms);
        return static::CheckAtoms($params['DATA'], $params, $control, true);
    }

    public static function Parse($condition)
    {
        if (!isset($condition['controlId']))
            return false;
        $atoms = static::GetAtomsEx($condition['controlId'], true);
        if (empty($atoms))
            return false;
        $control = array(
            'ID' => $condition['controlId'],
            'ATOMS' => $atoms
        );
        unset($atoms);
        return static::CheckAtoms($condition, $condition, $control, false);
    }

    public static function Generate($condition, $params, $control, $childrens = false)
    {
        return '';
    }

    public static function GetAtomsEx($controlId = false, $extendedMode = false)
    {
        return array();
    }

    public static function GetAtoms()
    {
        return static::GetAtomsEx(false, false);
    }

    /**
     * @return string|array
     */
    public static function GetControlID()
    {
        $atoms = static::GetAtomsEx(false, true);
        return (empty($atoms) ? array() : array_keys($atoms));
    }

    /**
     * @param bool|string $strControlID
     * @return array|bool
     */
    public static function GetControls($strControlID = false)
    {
        return array();
    }

    public static function GetControlShow($params)
    {
        $controls = static::GetControls();
        if (empty($controls) || !is_array($controls))
            return array();
        $result = array();
        foreach ($controls as $controlId => $data)
        {
            $row = array(
                'controlId' => $data['ID'],
                'group' => false,
                'label' => $data['LABEL'],
                'showIn' => static::GetShowIn($params['SHOW_IN_GROUPS']),
                'control' => array()
            );
            if (isset($data['PREFIX']))
                $row['control'][] = $data['PREFIX'];
            if (empty($row['control']))
            {
                $row['control'] = array_values($data['ATOMS']);
            }
            else
            {
                foreach ($data['ATOMS'] as &$atom)
                    $row['control'][] = $atom;
                unset($atom);
            }

            $result[] = $row;
        }
        unset($controlId, $data, $controls);
        return $result;
    }
}