<?php

namespace Local\Core\Inner\TriggerMail;


class Store
{
    public static function tariffChanged($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_STORE_TARIFF_CHANGED",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'STORE_NAME' => $arFields['STORE_NAME'],
                'NEW_TARIFF_NAME' => $arFields['NEW_TARIFF_NAME'],
                'NEW_TARIFF_PRODUCT_LIMIT' => $arFields['NEW_TARIFF_PRODUCT_LIMIT'],
                'DATE_ACTIVE_TO' => $arFields['DATE_ACTIVE_TO'],
                'NEXT_TARIFF_NAME' => $arFields['NEXT_TARIFF_NAME'],
            )
        ));
    }
}