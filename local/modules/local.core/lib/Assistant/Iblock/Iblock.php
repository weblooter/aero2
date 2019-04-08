<?php

namespace Local\Core\Assistant\Iblock;

use Bitrix\Iblock\IblockTable;

/**
 * Класс помощник для инфоблоков
 *
 * class Iblock
 * @package Local\Core\Assistant\Iblock
 */
class Iblock
{

    /**
     * Возвращает ID инфоблока по коду и типу
     *
     * @param string $iblockTypeId
     * @param string $iblockCode - код инфоблока
     *
     * @return int|null
     */
    public static function getIdByCode(string $iblockTypeId, string $iblockCode)
    {
        $iblockTypeId = trim($iblockTypeId);
        $iblockCode = trim($iblockCode);

        static $arStorage = [];

        if (!isset($arStorage[$iblockTypeId][$iblockCode])) {
            $data = IblockTable::getList([
                "select" => ["ID"],
                "filter" => [
                    "=IBLOCK_TYPE_ID" => $iblockTypeId,
                    "=CODE" => $iblockCode,
                ],
                "limit" => 1,
                "cache" => ["ttl" => 86400]
            ])
                ->fetch();

            $arStorage[$iblockTypeId][$iblockCode] = $data["ID"] ?? null;
        }

        return $arStorage[$iblockTypeId][$iblockCode];
    }
}
