<?php

namespace Local\Core\Ajax\Handler;

class SystemAuthAuthorize
{
    public static function try(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $login = $request->getPost('login');
        $password = $request->getPost('password');
        $resp = array();
        if($login && $password){
            //Проверка прошла успешно
            global $USER;
            if (!is_object($USER)) $USER = new \CUser;
            $arAuthResult = $USER->Login($login, $password, "N");
            if($arAuthResult["ERROR_TYPE"])
            {
                $resp[ "RESULT" ] = "Неверный логин или пароль";
            } else {
                $resp[ "RESULT" ] = "success";
            }
        } else {
            if(!$login){
                $resp["ERROR"] .= "Поле логин не заполнено";
            }
            if(!$password){
                $resp["ERROR"] .= "Поле пароль не заполнено";
            }
        }

        $response->setContentJson($resp);
    }
}