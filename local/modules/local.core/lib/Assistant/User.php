<?php


namespace Local\Core\Assistant;

/**
 * Ассистент пользователя
 *
 * @package Local\Core\Assistant
 */
class User
{
    /**
     * Сверяет введенный пароль и пароль юзвера
     *
     * @param $intUserId
     * @param $strPassword
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function comparePassword($intUserId, $strPassword)
    {
        $boolRet = false;

        $arUser = \Bitrix\Main\UserTable::getByPrimary($intUserId, ['select' => ['PASSWORD']])->fetch();
        if( !empty( $arUser ) )
        {
            $salt = substr($arUser['PASSWORD'], 0, (strlen($arUser['PASSWORD']) - 32));

            $realPassword = substr($arUser['PASSWORD'], -32);
            $password = md5($salt.$strPassword);

            $boolRet = ($realPassword == $password);
        }

        return $boolRet;
    }
}