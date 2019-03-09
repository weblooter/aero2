<?php

namespace Local\Core\Inner\RetailStructure;


use Bitrix\Main;
use Bitrix\Catalog;
use Local\Core\Model;

class Relation
{

    /**
     * Много инфы для связи регионов с RetailStructure.
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getRelationRegion2Structure()
    {
        $arWarehouseID = array_keys(self::getRelationWarehouseToStore());

        $arReturn = [];

        $rs = Model\Reference\RegionTable::query()
            ->setFilter([
                '=ACTIVE' => 'Y',
                '!=___SHOP.ACTIVE' => 'N',
                '!=___STORE.ACTIVE' => 'N',
            ])
            ->setSelect([
                new Main\Entity\ExpressionField('___SOURCE', '"store"'),
                '___REGION_ID' => '___REGION.ID',
                '___STORE_ID' => '___STORE.ID',
                '___SHOP_ID' => '___SHOP.ID',
            ])
            ->registerRuntimeField(
                (new Main\ORM\Fields\Relations\Reference(
                    '___SHOP',
                    Model\Reference\RetailStructure\StoreTable::class,
                    Main\ORM\Query\Join::on('this.ID', 'ref.REGION_ID')
                ))->configureJoinType('left')
            )->registerRuntimeField(
                (new Main\ORM\Fields\Relations\Reference(
                    '___STORE',
                    Catalog\StoreTable::class,
                    Main\ORM\Query\Join::on('this.___SHOP.STORE_ID', 'ref.ID')
                ))->configureJoinType('left')
            )->registerRuntimeField(
                (new Main\ORM\Fields\Relations\Reference(
                    '___REGION',
                    Model\Reference\RegionTable::class,
                    Main\ORM\Query\Join::on('this.___SHOP.REGION_ID', 'ref.ID')
                ))->configureJoinType('inner')
            )->union(
                Model\Reference\RetailStructure\Warehouse2StoreTable::query()
                    ->setFilter([
                        '=WAREHOUSE_ID' => $arWarehouseID,
                        '=___SHOP.ACTIVE' => 'Y',
                        '=___STORE.ACTIVE' => 'Y',
                    ])
                    ->setSelect([
                        new Main\Entity\ExpressionField('___SOURCE', '"warehouse"'),
                        '___STORE_ID' => 'WAREHOUSE_ID',
                        '___REGION_ID' => '___SHOP.REGION_ID',
                        '___SHOP_ID' => '___SHOP.ID',
                    ])
                    ->registerRuntimeField(
                        (new Main\ORM\Fields\Relations\Reference(
                            '___SHOP',
                            Model\Reference\RetailStructure\StoreTable::class,
                            Main\ORM\Query\Join::on('this.STORE_ID', 'ref.ID')
                        ))->configureJoinType('inner')
                    )
                    ->registerRuntimeField(
                        (new Main\ORM\Fields\Relations\Reference(
                            '___STORE',
                            Catalog\StoreTable::class,
                            Main\ORM\Query\Join::on('this.___SHOP.STORE_ID', 'ref.ID')
                        ))->configureJoinType('inner')
                    )
                    ->registerRuntimeField(
                        (new Main\ORM\Fields\Relations\Reference(
                            '___REGION',
                            Model\Reference\RegionTable::class,
                            Main\ORM\Query\Join::on('this.___SHOP.REGION_ID', 'ref.ID')
                        ))->configureJoinType('inner')
                    )
            )->exec();

        while ($ar = $rs->fetch()) {
            if ($ar['___SOURCE'] == 'warehouse') {
                $arReturn['WAREHOUSE'][$ar['___REGION_ID']][$ar['___STORE_ID']] = true;
//                $arReturn['SHOP_TO_STORE'][$ar['___SHOP_ID']] = $ar['___STORE_ID'];
            } else {
                $arReturn['STORE'][$ar['___STORE_ID']] = $ar['___REGION_ID'];
                $arReturn['SHOP_TO_STORE'][$ar['___SHOP_ID']] = $ar['___STORE_ID'];
            }
        }

        return $arReturn;
    }

    /**
     * Возвращает массив. $ar[ID warehouse] => $bitrixStoreID
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getRelationWarehouseToStore()
    {
        static $arReturn;
        if (is_null($arReturn)) {
            $arReturn = [];
            $rs = self::getResultWarehouse();
            while ($ar = $rs->fetch()) {
                $arReturn[$ar['ID']] = $ar['STORE_ID'];
            }
        }
        return $arReturn;
    }

    /**
     *
     * @return Main\ORM\Query\Result
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getResultWarehouse(): Main\ORM\Query\Result
    {
        return Model\Reference\RetailStructure\WarehouseTable::getList([
            'filter' => [
                '=ACTIVE' => 'Y',
                '=___STORE.ACTIVE' => 'Y',
            ],
            'select' => [
                'ID',
                'STORE_ID',
            ],
            'runtime' => [
                (new Main\ORM\Fields\Relations\Reference(
                    '___STORE',
                    Model\Reference\RetailStructure\StoreTable::class,
                    Main\ORM\Query\Join::on('this.STORE_ID', 'ref.ID')
                ))->configureJoinType('inner'),
            ],
            'cache' => [
                'cache_joins' => true,
                'ttl' => 3600,
            ]
        ]);
    }

}
