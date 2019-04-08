<?php

namespace Local\Core\Inner\TriggerMail\Robofeed;


class Import
{
    public static function successWithWarning($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_SUCCESS_WITH_WARNING",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'ERROR_MSG' => $arFields['ERROR_MSG']
            )
        ));
    }

    public static function success($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_AGAIN_SUCCESS",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'STORE_LINK' => $arFields['STORE_LINK']
            )
        ));
    }

    public static function error($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_ERROR",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'ERROR_MSG' => $arFields['ERROR_MSG']
            )
        ));
    }

    public static function xmlNotModified($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_NOT_MODIFIED",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME']
            )
        ));
    }
}