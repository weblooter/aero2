<?php

namespace Local\Core\Ajax\Handler;


class Example
{
    public static function echoRed(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
    {
        $response->setContentJson(['key' => 'red'], 200);
    }
}