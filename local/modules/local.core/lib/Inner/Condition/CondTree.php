<?php

namespace Local\Core\Inner\Condition;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\UserTable,
    \Bitrix\Main,
    \Bitrix\Iblock;

Loc::loadLanguageFile(__FILE__);

class CondTree extends GlobalCondTree
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }
}