<?php


namespace Local\Core\EventHandlers\Main;


class OnAfterUserAdd
{
    /**
     * Начисление бонусных баллов для тестирования площадки
     *
     * @param $arFields
     */
    public static function payToAccount(&$arFields)
    {
        if( !empty($arFields['ID']) )
        {
            \Local\Core\Inner\Balance\Base::payToAccount(3, $arFields['ID'], 'Спасибо за регистрацию');
        }
    }
}