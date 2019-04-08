<?

namespace Local\Core\Model\Robofeed\V1;

use \Bitrix\Main\ORM\Fields;
use Local\Core\Model\Robofeed\StoreProductFactory;

/**
 * Класс для хранения св-в товаров магазина
 *
 * @package Local\Core\Model\Robofeed
 */
class StoreProductParamTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Model\Robofeed\Traites\TableByStore;

    public static function getTableName()
    {
        if (is_null(self::$intStoreId)) {
            throw new \Exception('Необходимо задать ID магазина');
        }

        return 'c_robofeed_store_'.self::$intStoreId.'_product_param';
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
            new Fields\StringField('CODE', [
                'required' => true,
                'title' => 'Символьный код параметра'
            ]),
            new Fields\StringField('NAME', [
                'required' => true,
                'title' => 'Название параметра'
            ]),
            new Fields\StringField('VALUE', [
                'required' => true,
                'title' => 'Значение'
            ]),

            (new Fields\Relations\Reference('PRODUCT', get_class(StoreProductFactory::factory(1)
                ->setStoreId(self::$intStoreId)), \Bitrix\Main\ORM\Query\Join::on('this.PRODUCT_ID', 'ref.ID')))
        ];
    }
}