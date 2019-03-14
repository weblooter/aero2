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
        $sqlString = str_replace(
            ' NOT NULL ',
            ' ',
            $ormClassTable::getEntity()->compileDbTableStructureDump()[0]
        );
        if( !empty($sqlString) )
        {
            \Bitrix\Main\Application::getConnection()->query($sqlString);
        }
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
        if( !empty($sqlString) )
        {
            \Bitrix\Main\Application::getConnection()->dropTable($sqlString);
        }
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

    /**
     * Добавить класс агента
     *
     * @param string $strAgentClassName Класс агента
     * @param int    $intPeriod         Период запуска в секундах
     */
    public static function addAgent($strAgentClassName, $intPeriod = 3600)
    {
        \CAgent::AddAgent(
            $strAgentClassName.'::init()',
            'local.core',
            'N',
            $intPeriod,
            date('d.m.Y H:i:00'),
            'N',
            null,
            100
        );
    }

    public static function getOrmFieldsTable($strClass)
    {
        $str = '<ul>';
        foreach( $strClass::getMap() as $obField )
        {
            $str .= '<li>';
            $str .= $obField->getName();
            if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {
                if( !empty($obField->getTitle()) )
                {
                    $str .= ' - '.$obField->getTitle();
                }

                if( !empty($obField->getDefaultValue()) )
                {
                    $str .= ' ['.$obField->getDefaultValue().']';
                }

                $str .= ' | '.str_replace(
                        'Bitrix\Main\ORM\\',
                        '',
                        get_class($obField)
                    );

                if( $obField instanceof Bitrix\Main\ORM\Fields\EnumField )
                {
                    try
                    {
                        if( !method_exists( $strClass, 'getEnumFieldHtmlValues' ) )
                            throw new \Exception();

                        if( empty( $strClass::getEnumFieldHtmlValues( $obField->getName() ) ) )
                            throw new \Exception();

                        $str .= '<br/>';
                        foreach($strClass::getEnumFieldHtmlValues( $obField->getName() ) as $key => $value)
                        {
                            $str .= '&emsp;'.$key.' => '.$value."<br/>";
                        }
                    }
                    catch(\Exception $e)
                    {
                        $str .= '<br/>';
                        foreach($obField->getValues() as $key => $value)
                        {
                            $str .= '&emsp;'.$value."<br/>";
                        }
                    }
                }

            }
            else
            {

                if( !empty($obField->getRefEntityName()) )
                {
                    $str .= ' - '.$obField->getRefEntityName();
                }
                $str .= ' | '.str_replace(
                        'Bitrix\Main\ORM\\',
                        '',
                        get_class($obField)
                    );

            }

            $str .= '</li>';

        }
        $str .= '</ul>';
        return htmlspecialchars($str);
    }
}