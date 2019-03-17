<?php

namespace Local\Core\Assistant\HighLoadBlock;

/**
 * Класс помощник для highloadblock
 *
 * Class HighLoadBlock
 * @package Local\Core\Assistant\HighLoadBlock
 */
class HighLoadBlock
{
    /**
     * Хранилище сущностей
     *
     * @var array
     */
    private static $entities = [];
    private static $entitiesTables = [];

    /**
     * Возвращает строку highloadblock класс готовый к работе по \`b_hlblock_entity\`.\`NAME\`<br/>
     * Пример:<br/>
     * <code>
     * $hlProductClass = \Local\Core\Assistant\HighLoadBlock\HighLoadBlock::getEntity('ProductType');
     * $rs = $hlProductClass::getList([
     *   'filter' => ['UF_XML_ID' => $mixProductIdOrProductType],
     *   'select' => ['ID']
     * ]);
     * </code>
     *
     * @param string $highLoadBlockName
     *
     * @return \Bitrix\Main\Entity\Base|null
     * @throws \Exception
     */
    public static function getEntity($highLoadBlockName)
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        if( self::$entities[$highLoadBlockName] == null )
        {
            $rs = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                [
                    'filter' => ['NAME' => $highLoadBlockName],
                    'select' => ['ID']
                ]
            );
            $ar = $rs->fetch();
            if( empty($ar['ID']) )
            {
                throw new \Exception('HL by code '.$highLoadBlockName.' not found!');
            }

            $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($ar['ID'])
                ->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            self::$entities[$highLoadBlockName] = $entity->getDataClass();
        }

        return self::$entities[$highLoadBlockName];
    }

    /**
     * Возвращает строку highloadblock класс готовый к работе по \`b_hlblock_entity\`.\`TABLE_NAME\`<br/>
     * Пример:<br/>
     * <code>
     * $hlProductClass =
     * \Local\Core\Assistant\HighLoadBlock\HighLoadBlock::getEntityByTableName('a_hl_weight_dimensions_product');
     * $rs = $hlProductClass::getList([
     *   'filter' => ['UF_XML_ID' => $mixProductIdOrProductType],
     *   'select' => ['ID']
     * ]);
     * </code>
     *
     * @param string $highLoadBlockTableName
     *
     * @return mixed string|null
     * @throws \Exception
     */
    public static function getEntityByTableName($highLoadBlockTableName)
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        if( self::$entities[$highLoadBlockTableName] == null )
        {
            $rs = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                [
                    'filter' => ['TABLE_NAME' => $highLoadBlockTableName],
                    'select' => ['ID']
                ]
            );
            $ar = $rs->fetch();
            if( empty($ar['ID']) )
            {
                throw new \Exception('HL by table name '.$highLoadBlockTableName.' not found!');
            }

            $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($ar['ID'])
                ->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            self::$entities[$highLoadBlockTableName] = $entity->getDataClass();
        }

        return self::$entities[$highLoadBlockTableName];
    }

    /**
     * Получить название таблицы по названию HL
     *
     * @param $highLoadBlockName
     *
     * @return string
     * @throws \Exception
     */
    public static function getTableNameByEntityCode($highLoadBlockName)
    {

        if( static::$entitiesTables[$highLoadBlockName] == null )
        {
            $rs = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                [
                    'filter' => ['NAME' => $highLoadBlockName],
                    'select' => ['TABLE_NAME']
                ]
            );
            $ar = $rs->fetch();
            if( empty($ar['TABLE_NAME']) )
            {
                throw new \Exception('HL by code '.$highLoadBlockName.' not found!');
            }

            static::$entitiesTables[$highLoadBlockName] = $ar['TABLE_NAME'];
        }

        return static::$entitiesTables[$highLoadBlockName];
    }
}
