<?php

namespace Local\Core\EventHandlers;

use Bitrix\Main\EventManager;

class Base
{
    /**
     * Регистрирует все обработчики событий
     */
    public static function register()
    {
        self::registerMain();
    }

    /**
     * Рестрирует все обработчики событий для модуля main
     */
    private static function registerMain()
    {
        $eventManager = EventManager::getInstance();

        /** @see \Local\Core\EventHandlers\Main\OnBeforeProlog::initializeRegionHost() */
//        $eventManager->addEventHandler('main', 'OnBeforeProlog', [Main\OnBeforeProlog::class, 'initializeRegionHost']);

    }
}
