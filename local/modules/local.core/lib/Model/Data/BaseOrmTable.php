<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields;

/**
 * Базовый Orm класс.<br/>
 * Существует для копирования и создании на его основе Model\Data
 *
 * @package Local\Core\Model\Data
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
            new Fields\IntegerField(
                'ID', [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => 'ID'
                ]
            ),
            new Fields\EnumField(
                'ACTIVE', [
                    'title' => 'Активность',
                    'values' => self::getEnumFieldValues('ACTIVE'),
                    'default_value' => 'Y'
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE', [
                    'title' => 'Дата создания',
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),
            new Fields\DatetimeField(
                'DATE_MODIFIED', [
                    'title' => 'Дата последнего изменения',
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),
        ];
    }


    public static function OnBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        $arModifiedFields = [];

        # Вызывается строго в конце
        self::_OnBeforeUpdateBase($event, $result, $arModifiedFields);

        return $result;
    }

    public static function OnAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    public static function OnDelete(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }
}