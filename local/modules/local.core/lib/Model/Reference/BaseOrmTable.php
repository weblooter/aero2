<?

namespace Local\Core\Model\Reference;

use \Bitrix\Main\ORM\Fields;

/**
 * Базовый Orm класс.<br/>
 * Существует для копирования и создании на его основе Model\Reference
 *
 * @package Local\Core\Model\Data
 */
class BaseOrmTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Inner\Traits\Reference\ClearReferenceCache;

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
            new Fields\DatetimeField('DATE_MODIFIED', [
                    'title' => 'Дата последнего изменения',
                    'required' => false,
                    'default_value' => function ()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]),
            new Fields\IntegerField('SORT', [
                    'required' => false,
                    'title' => 'Сортировка',
                    'default_value' => 50,
                    'save_data_modification' => function ()
                        {
                            return [
                                function ($value)
                                    {
                                        return ($value > 0) ? $value : 50;
                                    }
                            ];
                        }
                ]),
            new Fields\StringField('NAME', [
                    'required' => true,
                    'title' => 'Название'
                ]),
            new Fields\StringField('CODE', [
                    'required' => true,
                    'title' => 'Символьный код',
                    'validation' => function ()
                        {
                            return [
                                new Fields\Validators\UniqueValidator(),
                                new Fields\Validators\RegExpValidator('/[A-Z0-9_]+/')
                            ];
                        }
                ]),
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

    public static function OnAfterAdd(\Bitrix\Main\ORM\Event $event)
    {
        self::clearReferenceCache();
    }

    public static function OnAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        self::clearReferenceCache();
    }

    public static function OnAfterDelete(\Bitrix\Main\ORM\Event $event)
    {
        self::clearReferenceCache();
    }
}