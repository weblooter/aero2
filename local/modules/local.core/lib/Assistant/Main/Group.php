<?php

namespace Local\Core\Assistant\Main;

/**
 * Класс помощник для групп пользователей
 *
 * Class Group
 * @package Local\Core\Assistant\Main
 */
class Group
{
    /**
     * Возвращает ID группы по коду
     *
     * @param string $groupCode
     *
     * @return int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getIdByCode( string $groupCode )
    {
        $groupCode = trim( $groupCode );

        static $arStorage = [];

        if ( !isset( $arStorage[ $groupCode ] ) )
        {
            $data = \Bitrix\Main\GroupTable::getList( [
                "select" => ["ID"],
                "filter" => [
                    "=STRING_ID" => $groupCode,
                ],
                "limit" => 1,
                "cache" => ["ttl" => 86400]
            ] )->fetch();

            $arStorage[ $groupCode ] = $data[ "ID" ] ?? null;
        }

        return $arStorage[ $groupCode ];
    }
}
