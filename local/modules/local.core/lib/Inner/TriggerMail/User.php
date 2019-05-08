<?php


namespace Local\Core\Inner\TriggerMail;


class User
{
    /**
     * Сообщение об успешной регистрации
     *
     * @param $arFields
     *
     * @return \Bitrix\Main\Entity\AddResult
     */
    public static function registration($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_USER_REGISTRATION",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
            )
        ));
    }

    /**
     * Сообщение о восстановлении пароля
     *
     * @param $arFields
     *
     * @return \Bitrix\Main\Entity\AddResult
     */
    public static function passwordRestored($arFields)
    {
        return \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "LOCAL_PASSWORD_RESTORED",
            "LID" => "s1",
            "C_FIELDS" => array(
                'EMAIL' => $arFields['EMAIL'],
                'NEW_PASSWORD' => $arFields['NEW_PASSWORD'],
            )
        ));
    }
}