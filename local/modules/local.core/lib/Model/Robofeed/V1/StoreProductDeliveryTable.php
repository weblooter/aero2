<?

namespace Local\Core\Model\Robofeed\V1;

use \Bitrix\Main\ORM\Fields, \Local\Core\Model\Reference, Local\Core\Inner\Cache;
use Local\Core\Model\Robofeed\StoreProductFactory;

/**
 * Класс для хранения условий доставки
 *
 * @package Local\Core\Model\Robofeed
 */
class StoreProductDeliveryTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Model\Robofeed\Traites\TableByStore;

    public static function getTableName()
    {
        if (is_null(self::$intStoreId)) {
            throw new \Exception('Необходимо задать ID магазина');
        }

        return 'c_robofeed_store_'.self::$intStoreId.'_product_delivery';
    }

    public function __construct()
    {
        if (empty(self::$arEnumFieldsValues)) {
            self::$arEnumFieldsValues['CURRENCY_CODE'] = $this->__getOrmValues(Reference\CurrencyTable::class, 'CODE');
            self::$arEnumFieldsValues['CURRENCY_CODE'] = array_combine(self::$arEnumFieldsValues['CURRENCY_CODE'], self::$arEnumFieldsValues['CURRENCY_CODE']);
            self::$arEnumFieldsValues['DELIVERY_REGION'] = [
                'in' => 'in',
                'out' => 'out',
                'all' => 'all'
            ];
        }
    }

    private static function __getOrmValues(string $strClass, string $strColumnName)
    {

        $arResult = [];

        if (class_exists($strClass)) {
            $obCache = \Bitrix\Main\Application::getInstance()
                ->getCache();
            if (
            $obCache->startDataCache(60 * 60 * 24 * 7, '\Local\Core\Inner\Robofeed\SchemeFields\ReferenceField_class='.$strClass, Cache::getCachePath([
                'Model',
                'Reference',
                array_slice(explode('\\', $strClass), -1),
            ], ['getOrmValueToValidateRobofeed']))
            ) {
                /** @var \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager $strClass */

                $rs = $strClass::getList([
                    'order' => ['SORT' => 'ASC'],
                    'select' => [$strColumnName]
                ]);
                if ($rs->getSelectedRowsCount() < 1) {
                    $obCache->abortDataCache();
                } else {
                    while ($ar = $rs->fetch()) {
                        $arResult[] = $ar[$strColumnName];
                    }
                    $obCache->endDataCache($arResult);
                }
            } else {
                $arResult = $obCache->getVars();
            }
        }

        return $arResult;
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [];


    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new Fields\DatetimeField('DATE_CREATE', [
                'title' => 'Дата создания',
                'required' => false,
                'default_value' => function ()
                    {
                        return new \Bitrix\Main\Type\DateTime();
                    }
            ]),
            new Fields\IntegerField('ROBOFEED_VERSION', [
                'required' => false,
                'title' => 'Версия Robofeed XML'
            ]),
            new Fields\IntegerField('PRODUCT_ID', [
                'required' => true,
                'title' => 'ID товара'
            ]),
            new Fields\IntegerField('PRICE_FROM', [
                'required' => true,
                'title' => 'Стоимость доставки "от"'
            ]),
            new Fields\IntegerField('PRICE_TO', [
                'required' => false,
                'title' => 'Стоимость доставки "до"'
            ]),
            new Fields\EnumField('CURRENCY_CODE', [
                'required' => true,
                'title' => 'Символьный код валюты стоимости',
                'values' => self::getEnumFieldValues('CURRENCY_CODE')
            ]),
            new Fields\IntegerField('DAYS_FROM', [
                'required' => true,
                'title' => 'Сроки доставки "от" в днях'
            ]),
            new Fields\IntegerField('DAYS_TO', [
                'required' => false,
                'title' => 'Сроки доставки "до" в днях'
            ]),
            new Fields\IntegerField('ORDER_BEFORE', [
                'required' => false,
                'title' => 'Временные рамки "сделать заказ до N часов", что бы вариант доставки был актуален'
            ]),
            new Fields\IntegerField('ORDER_AFTER', [
                'required' => false,
                'title' => 'Временные рамки "сделать заказ после N часов", что бы вариант доставки был актуален'
            ]),
            new Fields\EnumField('DELIVERY_REGION', [
                'required' => true,
                'title' => 'Признак региона, на которое распространяется правило',
                'values' => self::getEnumFieldValues('DELIVERY_REGION')
            ]),

            (new Fields\Relations\Reference('PRODUCT', get_class(StoreProductFactory::factory(1)
                ->setStoreId(self::$intStoreId)), \Bitrix\Main\ORM\Query\Join::on('this.PRODUCT_ID', 'ref.ID')))
        ];
    }
}