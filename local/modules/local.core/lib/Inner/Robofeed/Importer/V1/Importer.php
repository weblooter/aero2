<?php
namespace Local\Core\Inner\Robofeed\Importer\V1;


use Local\Core\Inner\Exception\FatalException;
use Local\Core\Model\Robofeed\StoreCategoryFactory;
use Local\Core\Model\Robofeed\StoreProductFactory;
use Local\Core\Model\Robofeed\StoreProductParamFactory;
use Local\Core\Model\Robofeed\StoreProductDeliveryFactory;
use Local\Core\Model\Robofeed\StoreProductPickupFactory;

class Importer extends \Local\Core\Inner\Robofeed\Importer\AbstractImporter
{
    /** @inheritdoc */
    public static function getVersion()
    {
        return 1;
    }

    /** @deprecated  */
    public function run()
    {
    }

    /**
     * @param $arFields
     *
     * @throws \Exception
     */
    public function importCategories($arFields)
    {
        if( is_null($this->intStoreId) )
            throw new FatalException('Необходимо задать STORE_ID');

        $obOrmStoreCategoryTable = ( StoreCategoryFactory::factory(self::getVersion()) )->setStoreId($this->intStoreId);

        foreach($arFields as $arField)
        {
            $arAdd = [
                'ROBOFEED_VERSION' => self::getVersion(),
                'CATEGORY_ID' => $arField['@attr']['id'],
                'CATEGORY_PARENT_ID' => $arField['@attr']['parentId'],
                'CATEGORY_NAME' => $arField['@value']
            ];
            $obOrmStoreCategoryTable::add($arAdd);
        }
    }

    /**
     * @param $arFields
     *
     * @return bool
     * @throws \Exception
     */
    public function importOffer($arFields)
    {
        if( is_null($this->intStoreId) )
            throw new FatalException('Необходимо задать STORE_ID');

        $obOrmStoreProductTable = ( StoreProductFactory::factory(self::getVersion()) )->setStoreId($this->intStoreId);

        $arAddOffer = [
            'ROBOFEED_VERSION' => self::getVersion(),
            'PRODUCT_ID' => $arFields['@attr']['id'],
            'PRODUCT_GROUP_ID' => $arFields['@attr']['groupId'],
        ];
        foreach($arFields['@value'] as $key=>$val)
        {
            preg_match_all('/([a-z]+|[A-Z][a-z]+)/', $key, $matches);
            $strFieldCode = implode('_', array_map('strtoupper', $matches[1]));

            switch($key)
            {
                case 'delivery':
                case 'pickup':
                case 'param':
                    continue;
                    break;

                case 'image':
                    $arAddOffer[ $strFieldCode ] = implode("\r\n", $val);
                    break;

                default:
                    $arAddOffer[ $strFieldCode ] = $val;
                    break;
            }
        }

        $arAddOffer['DELIVERY_AVAILABLE'] = $arFields['@value']['delivery']['@attr']['available'];
        $arAddOffer['PICKUP_AVAILABLE'] = $arFields['@value']['pickup']['@attr']['available'];

        $obAddResult = $obOrmStoreProductTable::add($arAddOffer);

        if( $obAddResult->isSuccess() )
        {
            $intProductTableId = $obAddResult->getId();
            $this->importParams($arFields['@value']['param'], $intProductTableId);

            if( $arAddOffer['DELIVERY_AVAILABLE'] == 'Y' )
            {
                $this->importDelivery($arFields['@value']['delivery']['option'], $intProductTableId);
            }

            if( $arAddOffer['PICKUP_AVAILABLE'] == 'Y' )
            {
                $this->importPickup($arFields['@value']['pickup']['option'], $intProductTableId);
            }
        }

        return $obAddResult->isSuccess();
    }

    private function importParams($arFields, $intProductTableId)
    {
        $obOrmStoreProductParamTable = ( StoreProductParamFactory::factory(self::getVersion()) )->setStoreId($this->intStoreId);

        foreach($arFields as $arProductParam)
        {
            $arAddParam = [
                'ROBOFEED_VERSION' => self::getVersion(),
                'PRODUCT_ID' => $intProductTableId,
                'CODE' => $arProductParam['@attr']['code'],
                'NAME' => $arProductParam['@attr']['name'],
                'VALUE' => $arProductParam['@value'],
            ];
            $obOrmStoreProductParamTable::add($arAddParam);
        }
    }

    private function importDelivery($arFields, $intProductTableId)
    {
        $obOrmStoreProductDeliveryTable = ( StoreProductDeliveryFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);

        foreach($arFields as $arOption)
        {
            $arAddDelivery = [
                'ROBOFEED_VERSION' => self::getVersion(),
                'PRODUCT_ID' => $intProductTableId,
                'PRICE_FROM' => $arOption['@attr']['priceFrom'],
                'PRICE_TO' => $arOption['@attr']['priceTo'],
                'CURRENCY_CODE' => $arOption['@attr']['currencyCode'],
                'DAYS_FROM' => $arOption['@attr']['daysFrom'],
                'DAYS_TO' => $arOption['@attr']['daysTo'],
                'ORDER_BEFORE' => $arOption['@attr']['orderBefore'],
                'ORDER_AFTER' => $arOption['@attr']['orderAfter'],
                'DELIVERY_REGION' => $arOption['@attr']['deliveryRegion'],
            ];
            $obOrmStoreProductDeliveryTable::add($arAddDelivery);
        }
    }

    private function importPickup($arFields, $intProductTableId)
    {
        $obOrmStoreProductPickupTable = ( StoreProductPickupFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);

        foreach($arFields as $arOption)
        {
            $arAddPickup = [
                'ROBOFEED_VERSION' => self::getVersion(),
                'PRODUCT_ID' => $intProductTableId,
                'PRICE' => $arOption['@attr']['price'],
                'CURRENCY_CODE' => $arOption['@attr']['currencyCode'],
                'SUPPLY_FROM' => $arOption['@attr']['supplyFrom'],
                'SUPPLY_TO' => $arOption['@attr']['supplyTo'],
                'ORDER_BEFORE' => $arOption['@attr']['orderBefore'],
                'ORDER_AFTER' => $arOption['@attr']['orderAfter'],
                'DELIVERY_REGION' => $arOption['@attr']['deliveryRegion'],
            ];
            $obOrmStoreProductPickupTable::add($arAddPickup);
        }
    }

    public function resetTables()
    {
        if( is_null($this->intStoreId) )
            throw new FatalException('Необходимо задать STORE_ID');

        $obOrmStoreCategoryTable = ( StoreCategoryFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);
        \CLocalCore::resetDBTableByGetMap($obOrmStoreCategoryTable);

        $obOrmStoreProductTable = ( StoreProductFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);
        \CLocalCore::resetDBTableByGetMap($obOrmStoreProductTable);

        $obOrmStoreProductParamTable = ( StoreProductParamFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);
        \CLocalCore::resetDBTableByGetMap($obOrmStoreProductParamTable);

        $obOrmStoreProductDeliveryTable = ( StoreProductDeliveryFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);
        \CLocalCore::resetDBTableByGetMap($obOrmStoreProductDeliveryTable);

        $obOrmStoreProductPickupTable = ( StoreProductPickupFactory::factory( self::getVersion() ) )->setStoreId($this->intStoreId);
        \CLocalCore::resetDBTableByGetMap($obOrmStoreProductPickupTable);
    }
}