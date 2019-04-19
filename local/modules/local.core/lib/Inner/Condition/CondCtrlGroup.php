<?php
namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondCtrlGroup extends GlobalCondCtrlGroup
{
    public static function GetControlDescr()
    {
        $description = parent::GetControlDescr();
        $description['SORT'] = 100;
        return $description;
    }
}