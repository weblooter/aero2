<?php

namespace Local\Core\Ajax\Handler;

class SystemAuthRegister
{
    public static function try(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $login = $request["login"];
        $password = $request["password"];

        global $USER;
        $rsUser = \CUser::GetByLogin($login);
        $arUser = $rsUser->Fetch();
        if(!$arUser){
            $user = new \CUser;
            $arFields = Array(
                "EMAIL"             => $login,
                "LOGIN"             => $login,
                "LID"               => "ru",
                "ACTIVE"            => "Y",
                "GROUP_ID"          => array(11),
                "PASSWORD"          => $password,
                "CONFIRM_PASSWORD"  => $password
            );

            $ID = $user->Add($arFields);
            if (intval($ID) > 0)
            {
                $USER->Authorize($ID);
                $resp[ "RESULT" ] = "success";
            } else
            {
                $resp["ERROR"] .= $user->LAST_ERROR;
            }
        } else {
            $resp["ERROR"] .= "Такой электронный адрес уже зарегистрирован";
        }



        $response->setContentJson($resp);
    }
}