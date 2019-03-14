<?php

namespace Local\Core\Inner\AdminHelper;


/**
 * Помощник в реализации страниц администрирования в одном файле
 * Class AdminRoute
 * @package Local\Core\Inner\AdminHelper
 */
class AdminRoute
{
    const ADMIN_ROUTE_FILE = 'admin_helper_route.php';
    const ADMIN_ROUTE_DIR = 'bitrix/admin';
    const ADMIN_ENTITY = 'adminEntity';
    const ADMIN_ACTION = 'adminAction';

    static public function getUri(array $arData = []): string
    {
        $uri = new \Bitrix\Main\Web\Uri(
            join(
                '/',
                ['', self::ADMIN_ROUTE_DIR, self::ADMIN_ROUTE_FILE]
            )
        );
        $uri->addParams($arData);
        return $uri->getUri();
    }

    static public function getQuery(array $arData = []): string
    {
        $uri = new \Bitrix\Main\Web\Uri(
            join(
                '/',
                [self::ADMIN_ROUTE_DIR, self::ADMIN_ROUTE_FILE]
            )
        );
        $uri->addParams($arData);
        return $uri->getQuery();
    }
}
