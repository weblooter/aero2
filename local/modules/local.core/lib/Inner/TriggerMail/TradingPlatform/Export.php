<?php


namespace Local\Core\Inner\TriggerMail\TradingPlatform;


class Export
{
    /**
     * Ошибка при создании экспортного файла
     *
     * @param $arFields
     *
     * @return \Bitrix\Main\Entity\AddResult
     */
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

    /**
     * Экспортный файл успешно создан
     *
     * @param $arFields
     *
     * @return \Bitrix\Main\Entity\AddResult
     */
    public static function firstTimeSuccessExport($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_TP_FIRST_TIME_SUCCESS",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'TP_NAME' => $arFields['TP_NAME'],
                'PRODUCTS_TOTAL' => $arFields['PRODUCTS_TOTAL'],
                'PRODUCTS_EXPORTED' => $arFields['PRODUCTS_EXPORTED'],
                'EXPORT_LINK' => $arFields['EXPORT_LINK'],
                'STORE_LINK' => $arFields['STORE_LINK'],
            )
        ));
    }
}