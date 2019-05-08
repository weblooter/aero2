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
            \Local\Core\Inner\Balance\Base::payToAccount(3, $arFields['ID'], 'Спасибо за регистрацию. Вам начислен баланс для тестирования сервиса.');
        }

        \Local\Core\Inner\TriggerMail\User::registration([
            'EMAIL' => $arFields['EMAIL']
        ]);
    }
}