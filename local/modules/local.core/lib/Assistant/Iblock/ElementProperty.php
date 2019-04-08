<?php

namespace Local\Core\Assistant\Iblock;

use Bitrix\Iblock;

/**
 * Класс помощник для свойств элементов инфоблока
 *
 * class ElementProperty
 * @package Local\Core\Assistant\Iblock
 */
class ElementProperty
{
    /**
     * Возвращает ID свойства
     *
     * @param string $iblockId - id инфоблока
     * @param string $code     - код свойства
     *
     * @return null|int
     */
    public static function getIdByCode(string $iblockId, string $code)
    {
        $iblockId = trim($iblockId);
        $code = trim($code);

        static $arStorage = [];

        if (!isset($arStorage[$iblockId][$code])) {
            $data = Iblock\PropertyTable::getList([
                "select" => ["ID"],
                "filter" => [
                    "=IBLOCK_ID" => $iblockId,
                    "=CODE" => $code,
                ],
                "limit" => 1,
                "cache" => ["ttl" => 86400]
            ])
                ->fetch();

            $arStorage[$iblockId][$code] = $data["ID"] ?? null;
        }

        return $arStorage[$iblockId][$code];
    }

    /**
     * Возвращает перечень значений св-ва типа список
     *
     * @param string $iblockId
     * @param string $code
     *
     * @return mixed
     */
    public static function getEnumerationPropByCode(string $iblockId, string $code)
    {
        $iblockId = trim($iblockId);
        $code = trim($code);

        static $arStorage = [];

        if (!isset($arStorage[$iblockId][$code])) {
            $propertyId = \Local\Core\Assistant\Iblock\ElementProperty::getIdByCode(\Local\Core\Assistant\Iblock\Iblock::getIdByCode('catalog', 'catalog'), $code);
            if ($propertyId) {
                $data = \Bitrix\Iblock\PropertyEnumerationTable::getList([
                    'order' => ['SORT' => 'ASC'],
                    'filter' => ['=PROPERTY_ID' => $propertyId],
                ])
                    ->fetchAll();
            }

            $arStorage[$iblockId][$code] = !empty($data) ? $data : null;
        }

        return $arStorage[$iblockId][$code];
    }
}
