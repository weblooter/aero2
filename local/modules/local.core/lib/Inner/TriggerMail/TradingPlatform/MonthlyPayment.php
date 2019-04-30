<?php


namespace Local\Core\Inner\TriggerMail\TradingPlatform;


class MonthlyPayment
{
    public static function payTradingPlatformError($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_PAY_TRADING_PLATFORM_ERROR",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'TP_NAME' => $arFields['TP_NAME'],
                'ERROR_TEXT' => $arFields['ERROR_TEXT'],
            )
        ));
    }
}