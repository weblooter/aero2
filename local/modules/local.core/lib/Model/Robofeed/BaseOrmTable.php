<?

namespace Local\Core\Model\Robofeed;

use \Bitrix\Main\ORM\Fields;

/**
 * Базовый Orm класс.<br/>
 * Существует для копирования и создании на его основе Model\Robofeed
 *
 * @package Local\Core\Model\v
 */
class BaseOrmTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return '';
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
            new Fields\IntegerField('STORE_ID', [
                'required' => true,
                'title' => 'ID магазина'
            ]),
        ];
    }
}