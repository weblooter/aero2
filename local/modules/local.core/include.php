<?

class CLocalCore
{
    /**
     * Создает таблицу по ORM
     *
     * @param \Bitrix\Main\ORM\Data\DataManager $ormClassTable
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function initDBTable($ormClassTable)
    {
        $sqlString = str_replace(' NOT NULL ', ' ', $ormClassTable::getEntity()->compileDbTableStructureDump()[0] );
        if( !empty( $sqlString ) )
            \Bitrix\Main\Application::getConnection()->query( $sqlString );
    }

    /**
     * Удаляет ORM талицу
     *
     * @param \Bitrix\Main\ORM\Data\DataManager $ormClassTable
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function dropDBTable($ormClassTable)
    {
        $sqlString = $ormClassTable::getTableName();
        if( !empty( $sqlString ) )
            \Bitrix\Main\Application::getConnection()->dropTable( $sqlString );
    }

    /**
     * Удаляет ORM талицу
     *
     * @param \Bitrix\Main\ORM\Data\DataManager $ormClassTable
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function resetDBTable($ormClassTable)
    {
        static::dropDBTable($ormClassTable);
        static::initDBTable($ormClassTable);
    }
}