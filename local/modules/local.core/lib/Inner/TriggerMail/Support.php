<?php


namespace Local\Core\Inner\TriggerMail;


class Support
{

    public static function addNewMessage($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_SUPPORT_NEW_MSG",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'MSG' => $arFields['MSG'],
                'TASK_ID' => $arFields['TASK_ID'],
                'TASK_LINK' => $arFields['TASK_LINK'],
            )
        ));
    }

    public static function taskClosed($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_SUPPORT_TASK_CLOSED",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'TASK_ID' => $arFields['TASK_ID'],
                'TASK_LINK' => $arFields['TASK_LINK'],
            )
        ));
    }
}