<?php


namespace Local\Core\Inner\TriggerMail\TradingPlatform;


class Export
{
    public static function errorDuringExport($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_TP_ERROR_DURING_EXPORT",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'TP_NAME' => $arFields['TP_NAME'],
                'ERROR_TEXT' => $arFields['ERROR_TEXT'],
                'STORE_ROUTE' => $arFields['STORE_ROUTE'],
            )
        ));
    }
}