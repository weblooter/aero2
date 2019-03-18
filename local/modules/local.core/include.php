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
    public static function createDBTableByGetMap($ormClassTable)
    {

        foreach( $ormClassTable::getEntity()
                     ->compileDbTableStructureDump() as $sqlString )
        {
            $sqlString = str_replace(
                ' NOT NULL',
                ' ',
                $sqlString
            );
            \Bitrix\Main\Application::getConnection()
                ->query($sqlString);
        }
    }

    /**
     * Удаляет ORM талицу
     *
     * @param \Bitrix\Main\ORM\Data\DataManager $ormClassTable
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function dropDBTableByGetMap($ormClassTable)
    {
        $sqlString = $ormClassTable::getTableName();
        if( !empty($sqlString) )
        {
            \Bitrix\Main\Application::getConnection()
                ->dropTable($sqlString);
        }
    }

    /**
     * Удаляет ORM талицу
     *
     * @param \Bitrix\Main\ORM\Data\DataManager $ormClassTable
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function resetDBTableByGetMap($ormClassTable)
    {
        static::dropDBTableByGetMap($ormClassTable);
        static::createDBTableByGetMap($ormClassTable);
    }

    /**
     * Обновляет ORM таблицу, пока не работает
     *
     * @deprecated
     *
     * @param \Bitrix\Main\ORM\Data\DataManager $strClass
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function generateAlterTableSql($strClass)
    {
        $obConnection = \Bitrix\Main\Application::getConnection();

        /** @var \Bitrix\Main\ORM\Entity $obClassEntity */
        $obClassEntity = $strClass::getEntity();

        $arCurrentTableColumn = [];
        $obCurrentTableColumns = \Bitrix\Main\Application::getConnection()
            ->query('SHOW COLUMNS FROM `'.$obClassEntity->getDBTableName().'`');
        while( $ar = $obCurrentTableColumns->fetch() )
        {
            if( $ar['Type'] == 'int(11)' )
            {
                $ar['Type'] = 'int';
            }

            $arCurrentTableColumn[$ar['Field']] = [
                'NAME' => $ar['Field'],
                'TYPE' => $ar['Type'],
                'IS_NULL' => ( $ar['Null'] == 'YES' ),
                'IS_PRIMARY' => ( $ar['Key'] == 'PRI' ),
                'IS_AUTO_INCREMENT' => ( $ar['Extra'] == 'auto_increment' ),
            ];
        }
        dump($arCurrentTableColumn);

        $sqlBegin = 'ALTER TABLE '.$obConnection->getSqlHelper()
                ->quote($obClassEntity->getDBTableName()).' ';

        foreach( $obClassEntity->getScalarFields() as $obField )
        {
            if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
            {

                $strColumnName = $obField->getColumnName();

                if( !empty($arCurrentTableColumn[$strColumnName]) )
                {
                    /*
                     * Уже есть, проверим на модификации
                     */
                    if(
                        $arCurrentTableColumn[$strColumnName]['TYPE'] != $obConnection->getSqlHelper()
                            ->getColumnTypeByField($obField)
                        || $arCurrentTableColumn[$strColumnName]['IS_NULL'] != $obField->isRequired()
                        || $arCurrentTableColumn[$strColumnName]['IS_PRIMARY'] != in_array($strColumnName, $obClassEntity->getPrimaryArray(), true)
                        || $arCurrentTableColumn[$strColumnName]['IS_AUTO_INCREMENT'] != in_array($strColumnName, $obClassEntity->getPrimaryArray(), true)
                    )
                    {
                        $sqlEnd = $obConnection->getSqlHelper()
                                      ->quote($strColumnName).' '.$obConnection->getSqlHelper()
                                      ->getColumnTypeByField($obField).( in_array($strColumnName, $obClassEntity->getPrimaryArray(), true) ? ' AUTO_INCREMENT' : '' ).( $obField->isRequired()
                                                                                                                                                                        || !empty(
                            $obField->getDefaultValue()
                            ) ? ' NOT_NULL' : ' NULL ' ).( $obField->getDefaultValue() ? ' DEFAULT '.$obField->getDefaultValue() : '' );

                        dump($sqlBegin.' MODIFY COLUMN '.$sqlEnd);
                        //                                    $obConnection->query($sqlBegin.' MODIFY COLUMN '.$sqlEnd);
                    }
                }
                else
                {
                    /*
                     * Нет, надо добавить
                     */

                    $sqlEnd = $obConnection->getSqlHelper()
                                  ->quote($strColumnName).' '.$obConnection->getSqlHelper()
                                  ->getColumnTypeByField($obField).( in_array($strColumnName, $obClassEntity->getPrimaryArray(), true) ? ' AUTO_INCREMENT' : '' );

                    dump($sqlBegin.' ADD COLUMN '.$sqlEnd);
                    //                                $obConnection->query($sqlBegin.' ADD COLUMN '.$sqlEnd);
                }
            }
        }
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

    /**
     * Возвращает html (htmlspecialchars) описание getMap() ORM, для добалвения в PHPDoc
     *
     * @param string $strClass Полное название класса
     *
     * @return string
     */
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
                        if( !method_exists($strClass, 'getEnumFieldHtmlValues') )
                        {
                            throw new \Exception();
                        }

                        if( empty($strClass::getEnumFieldHtmlValues($obField->getName())) )
                        {
                            throw new \Exception();
                        }

                        $str .= '<br/>';
                        foreach( $strClass::getEnumFieldHtmlValues($obField->getName()) as $key => $value )
                        {
                            $str .= '&emsp;'.$key.' => '.$value."<br/>";
                        }
                    }
                    catch( \Exception $e )
                    {
                        $str .= '<br/>';
                        foreach( $obField->getValues() as $key => $value )
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

    /**
     * Функция добавления пункта в меню
     *
     * @param array  $arSubMenu         Массив с меню, к которому добавится пункт
     * @param string $strClassAdminList Полный путь до AdminList
     * @param string $strClassAdminEdit Полный путь до AdminEdit
     * @param string $strName           Название пункта в меню
     * @param string $strIcon           Класс иконки
     */
    public static function addItemToMenu(&$arSubMenu, $strClassAdminList, $strClassAdminEdit, $strName, $strIcon = '')
    {
        if( class_exists($strClassAdminList) )
        {
            $lDataList = ( new $strClassAdminList() )->getAdminUri();
            if( $lDataList->isSuccess() )
            {
                $lDataEdit = ( new $strClassAdminEdit() )->getAdminUri();

                $arSubMenu[] = [
                    "text" => $strName,
                    'url' => $lDataList->getData()['uri'],
                    "more_url" => ( $lDataEdit->isSuccess() ) ? [$lDataEdit->getData()["uri"]] : [],
                    "icon" => $strIcon
                ];
            }

        }
    }
}