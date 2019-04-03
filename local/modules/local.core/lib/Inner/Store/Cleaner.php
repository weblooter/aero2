<?php

namespace Local\Core\Inner\Store;

use Bitrix\Main\Config\Configuration;
use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Robofeed\ImportLogTable;

/**
 * Класс чистильщик для магазинов
 *
 * @package Local\Core\Inner\Store
 */
class Cleaner
{
    /**
     * Метод удаляет старые деактивированные магазины
     */
    public static function deleteDeactivatedStores()
    {
        $rsOldDeactivatedStores = StoreTable::getList([
            'filter' => [
                'ACTIVE' => 'N',
                '<=DATE_MODIFIED' => (new \Bitrix\Main\Type\DateTime())->add('-'.(Configuration::getInstance()
                                                                                      ->get('store')['cleaner']['delete_deactivated_after_days'] ?? 60).' days')
            ]
        ]);
        while ($ar = $rsOldDeactivatedStores->fetch()) {
            StoreTable::delete($ar['ID']);
        }
    }

    /**
     * Метод удаляет из базы следы удаленных магазинов
     */
    public static function clearDBFromDeletedStores()
    {
        $rsStores = StoreTable::getList([
            'select' => ['ID']
        ]);
        $arStores = [];
        while ($ar = $rsStores->fetch()) {
            $arStores[] = $ar['ID'];
        }

        $rsTablesInDb = \Bitrix\Main\Application::getConnection()
            ->query('SHOW TABLES FROM '.\Bitrix\Main\Application::getConnection()
                    ->getDatabase().' WHERE `Tables_in_'.\Bitrix\Main\Application::getConnection()
                        ->getDatabase().'` REGEXP \'^c\_robofeed\_store\_[0-9]+\_(.*)\'');
        while ($ar = $rsTablesInDb->fetch()) {
            $strTableName = current($ar);
            if (preg_match('/^c\_robofeed\_store\_([0-9]+)\_/', $strTableName, $matches) === 1) {
                if (!in_array($matches[1], $arStores)) {
                    \Bitrix\Main\Application::getConnection()
                        ->query('DROP TABLE IF EXISTS '.$strTableName);
                }
            }
        }

        $rsImportLogs = ImportLogTable::getList([
            'select' => ['ID', 'STORE_ID']
        ]);
        while ($ar = $rsImportLogs->fetch()) {
            if (!in_array($ar['STORE_ID'], $arStores)) {
                ImportLogTable::delete($ar['ID']);
            }
        }
    }
}