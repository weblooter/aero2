<?php

namespace Local\Core\Inner\Iblock\UserProperty;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

class Rating
{
    public static function GetPropertyFieldHtml( $property, $value, $HTMLControlName )
    {

        $html = '<input type="number" name="'.$HTMLControlName[ "VALUE" ].'" value="'.$value[ "VALUE" ].'" />';
        return $html;
    }

    //сохраняем в базу
    public static function ConvertToDB( $arProperty, $value )
    {
        return $value;
    }

    //читаем из базы
    public static function ConvertFromDB( $arProperty, $value )
    {
        return $value;
    }
}
