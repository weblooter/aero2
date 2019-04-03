<?php

namespace Local\Core\EventHandlers;

use Bitrix\Main\EventManager, Local\Core\Model\Data;

class Base
{
    /**
     * Регистрирует все обработчики событий
     */
    public static function register()
    {
        self::registerMain();
        self::registerIblock();
    }

    /**
     * Рестрирует все обработчики событий для модуля main
     */
    private static function registerMain()
    {
        $eventManager = EventManager::getInstance();

        /** @see \Local\Core\EventHandlers\Main\OnBuildGlobalMenu::addGlobalMenu(); */
        $eventManager->addEventHandler('main', 'OnBuildGlobalMenu', [Main\OnBuildGlobalMenu::class, 'addGlobalMenu']);

        /** @see \Local\Core\EventHandlers\Main\OnBeforeEventSend::executeCondition() */
        $eventManager->addEventHandler('main', 'OnBeforeEventSend', [Main\OnBeforeEventSend::class, 'executeCondition']);

    }

    /**
     * Рестрирует все обработчики событий для модуля iblock
     */
    private static function registerIblock()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $eventManager = EventManager::getInstance();

            /** @see \Local\Core\EventHandlers\Iblock\OnIBlockPropertyBuildList::getLinkToORM(); */
            $eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', [
                    Iblock\OnIBlockPropertyBuildList::class,
                    'getLinkToORM'
                ]);
        }
    }
}
