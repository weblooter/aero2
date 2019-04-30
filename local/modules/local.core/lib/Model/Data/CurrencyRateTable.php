<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields;

/**
 * Класс ORM курсов валют.<br/>
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [2019-04-27 10:13:48] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [2019-04-27 10:13:48] | Fields\DatetimeField</li><li>CURRENCY_FROM - Из валюты | Fields\StringField</li><li>CURRENCY_TO - В валюту | Fields\StringField</li><li>RATE - Курс | Fields\StringField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class CurrencyRateTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_currency_rate';
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
            new Fields\EnumField('ACTIVE', [
                'title' => 'Активность',
                'values' => self::getEnumFieldValues('ACTIVE'),
                'default_value' => 'Y'
            ]),
            new Fields\DatetimeField('DATE_CREATE', [
                'title' => 'Дата создания',
                'default_value' => function ()
                    {
                        return new \Bitrix\Main\Type\DateTime();
                    }
            ]),
            new Fields\DatetimeField('DATE_MODIFIED', [
                'title' => 'Дата последнего изменения',
                'default_value' => function ()
                    {
                        return new \Bitrix\Main\Type\DateTime();
                    }
            ]),
            new Fields\StringField('CURRENCY_FROM', [
                'title' => 'Из валюты'
            ]),
            new Fields\StringField('CURRENCY_TO', [
                'title' => 'В валюту'
            ]),
            new Fields\StringField('RATE', [
                'title' => 'Курс'
            ])
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