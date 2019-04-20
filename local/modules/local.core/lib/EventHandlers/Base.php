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
        self::registerLocalCore();
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

    /**
     * Регистриует все обработчики локального ядра
     */
    private static function registerLocalCore()
    {
        $eventManager = EventManager::getInstance();

        /* *************** */
        /* Inner\Condition */
        /* *************** */

        define('LOCAL_CORE_CONDITION_LOGIC_EQ', 0);						// = (equal)
        define('LOCAL_CORE_CONDITION_LOGIC_NOT_EQ', 1);					// != (not equal)
        define('LOCAL_CORE_CONDITION_LOGIC_GR', 2);						// > (great)
        define('LOCAL_CORE_CONDITION_LOGIC_LS', 3);						// < (less)
        define('LOCAL_CORE_CONDITION_LOGIC_EGR', 4);						// => (great or equal)
        define('LOCAL_CORE_CONDITION_LOGIC_ELS', 5);						// =< (less or equal)
        define('LOCAL_CORE_CONDITION_LOGIC_CONT', 6);					// contain
        define('LOCAL_CORE_CONDITION_LOGIC_NOT_CONT', 7);				// not contain

        define('LOCAL_CORE_CONDITION_MODE_DEFAULT', 0);					// full mode
        define('LOCAL_CORE_CONDITION_MODE_PARSE', 1);					// parsing mode
        define('LOCAL_CORE_CONDITION_MODE_GENERATE', 2);					// generate mode
        define('LOCAL_CORE_CONDITION_MODE_SQL', 3);						// generate getlist mode
        define('LOCAL_CORE_CONDITION_MODE_SEARCH', 4);					// info mode

        define('LOCAL_CORE_CONDITION_BUILD_CATALOG', 0);					// local.core conditions
        define('LOCAL_CORE_CONDITION_BUILD_SALE', 1);					// sale conditions
        define('LOCAL_CORE_CONDITION_BUILD_SALE_ACTIONS', 2);			// sale actions conditions

    }
}
