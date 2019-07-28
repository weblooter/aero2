<?php

namespace Local\Core\Inner\TriggerMail\Robofeed;


class Convert
{
    public static function convertCompleted($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_YML_CONVERT_COMPLETED",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'STATUS' => $arFields['STATUS'],
                'HEADER_MAIL' => $arFields['HEADER_MAIL'],
                'ERROR_MESSAGE' => $arFields['ERROR_MESSAGE'],
                'HOW_MADE_ROBOFEED_ROUTE' => $arFields['HOW_MADE_ROBOFEED_ROUTE'],
                'CONVERT_ROUTE' => $arFields['CONVERT_ROUTE'],
            )
        ));
    }
}