<?

namespace Local\Core\Model\Robofeed\V1;

use \Bitrix\Main\ORM\Fields;

/**
 * Класс для хранения категорий магазина
 *
 * @package Local\Core\Model\Robofeed
 */
class StoreCategoryTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Model\Robofeed\Traites\TableByStore;

    public static function getTableName()
    {
        if (is_null(self::$intStoreId)) {
            throw new \Exception('Необходимо задать ID магазина');
        }

        return 'c_robofeed_store_'.self::$intStoreId.'_category';
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
            new Fields\IntegerField('CATEGORY_ID', [
                'required' => true,
                'title' => 'ID категории'
            ]),
            new Fields\IntegerField('CATEGORY_PARENT_ID', [
                'required' => false,
                'title' => 'ID родительской категории'
            ]),
            new Fields\StringField('CATEGORY_NAME', [
                'required' => true,
                'title' => 'Название категории'
            ]),
        ];
    }
}