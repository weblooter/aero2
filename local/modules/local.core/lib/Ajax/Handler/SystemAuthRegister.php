<?php

namespace Local\Core\Ajax\Handler;

class SystemAuthRegister
{
    public static function try(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $login = $request["login"];
        $password = $request["password"];
        $resp = array('login'=>$login, 'password'=>$password);


        $response->setContentJson($resp);
    }
}