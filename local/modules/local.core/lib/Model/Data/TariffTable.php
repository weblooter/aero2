<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields;

/**
 * Класс ORM с тарифами пользователей
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [N] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [02.04.2019 21:29:33] |
 * Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [02.04.2019 21:29:33] | Fields\DatetimeField</li><li>SORT - Сортировка [50] | Fields\IntegerField</li><li>DATE_ACTIVE_FROM -
 * Активен с | Fields\DatetimeField</li><li>DATE_ACTIVE_TO - Активен до | Fields\DatetimeField</li><li>NAME - Название | Fields\StringField</li><li>CODE - Символьный код |
 * Fields\StringField</li><li>LIMIT_TRADING_PLATFORM - Лимит торговых площадок [1000] | Fields\IntegerField</li><li>LIMIT_IMPORT_PRODUCTS - Лимит импортируемых товаров [50] |
 * Fields\IntegerField</li><li>PRICE_PER_TRADING_PLATFORM - Стоимость за торговую площадку [500] | Fields\IntegerField</li><li>IS_DEFAULT - Тариф по умолчанию [N] | Fields\EnumField<br/>&emsp;Y =>
 * Да<br/>&emsp;N => Нет<br/></li><li>TYPE - Тип тарифа [PER] | Fields\EnumField<br/>&emsp;PUB => Публичный<br/>&emsp;PER => Индивидуальный<br/></li><li>PERSONAL_BY_STORE - ID магазина, для которого
 * тариф персонализирован | Fields\IntegerField</li><li>SWITCH_AFTER_ACTIVE_TO - Тариф, на который переключать после "Активен до" (симв. код) | Fields\StringField</li><li>IS_ACTION - Акционный тариф
 * [N] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li></ul>
 *
 * @package Local\Core\Model\Data
 */
class TariffTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_tariff';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'IS_DEFAULT' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'IS_ACTION' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'TYPE' => [
            'PUB' => 'Публичный',
            'PER' => 'Индивидуальный'
        ]
    ];

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
                'default_value' => 'N'
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
            new Fields\DatetimeField('DATE_ACTIVE_FROM', [
                'title' => 'Активен с',
            ]),
            new Fields\DatetimeField('DATE_ACTIVE_TO', [
                'title' => 'Активен до',
            ]),
            new Fields\StringField('NAME', [
                'title' => 'Название',
                'required' => true
            ]),
            new Fields\StringField('CODE', [
                'unique' => true,
                'title' => 'Символьный код',
                'required' => true,
                'validation' => function ()
                    {
                        return [
                            new \Bitrix\Main\ORM\Fields\Validators\RegExpValidator('/[A-Z0-9\_]+/'),
                            function ($value, $primary, $row, $field)
                                {
                                    $rsCurrentCode = self::getList(['filter' => [$field->getName() => $value], 'select' => ['ID']]);
                                    $arCurrentCode = $rsCurrentCode->fetch();
                                    if (
                                        $rsCurrentCode->getSelectedRowsCount() > 1
                                        || (!empty($arCurrentCode) && $arCurrentCode['ID'] != $primary['ID'])
                                    ) {
                                        return 'Символьный код должен быть уникален!';
                                    } else {
                                        return true;
                                    }
                                }
                        ];
                    }
            ]),
            new Fields\IntegerField('LIMIT_TRADING_PLATFORM', [
                'title' => 'Лимит торговых площадок',
                'required' => true,
                'default_value' => 1000
            ]),
            new Fields\IntegerField('LIMIT_IMPORT_PRODUCTS', [
                'title' => 'Лимит импортируемых товаров',
                'required' => true,
                'default_value' => 50
            ]),
            new Fields\IntegerField('PRICE_PER_TRADING_PLATFORM', [
                'title' => 'Стоимость за торговую площадку',
                'required' => true,
                'default_value' => 500
            ]),
            new Fields\EnumField('IS_DEFAULT', [
                'title' => 'Тариф по умолчанию',
                'required' => true,
                'values' => self::getEnumFieldValues('IS_DEFAULT'),
                'default_value' => 'N',
                'validation' => function ()
                    {
                        return [
                            function ($value, $primary, $row, $field)
                                {
                                    $arCurrentDefaultTariff = self::getList(['filter' => [$field->getName() => 'Y'], 'select' => ['ID']])
                                        ->fetch();
                                    if (
                                        empty($arCurrentDefaultTariff)
                                        || ($value == 'Y' && $arCurrentDefaultTariff['ID'] == $primary['ID'])
                                        || $value == 'N'
                                    ) {
                                        return true;
                                    } else {
                                        return 'Может быть только 1 "тариф по умолчанию"!';
                                    }
                                }
                        ];
                    }
            ]),
            new Fields\EnumField('TYPE', [
                'title' => 'Тип тарифа',
                'required' => true,
                'values' => self::getEnumFieldValues('TYPE'),
                'default_value' => 'PER'
            ]),
            new Fields\IntegerField('PERSONAL_BY_STORE', [
                'title' => 'ID магазина, для которого тариф персонализирован'
            ]),
            new Fields\StringField('SWITCH_AFTER_ACTIVE_TO', [
                'title' => 'Тариф, на который переключать после "Активен до" (симв. код)',
                'validation' => function ()
                    {
                        return [
                            new \Bitrix\Main\ORM\Fields\Validators\RegExpValidator('/([A-Z0-9\_]+)?/')
                        ];
                    }
            ]),
            new Fields\EnumField('IS_ACTION', [
                'title' => 'Акционный тариф',
                'required' => true,
                'values' => self::getEnumFieldValues('IS_ACTION'),
                'default_value' => 'N'
            ]),

        ];
    }

    public static function clearCalcCache()
    {
        \Local\Core\Inner\Cache::deleteComponentCache(['mainpage.calc']);
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
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
        self::clearCalcCache();
    }

    public static function OnAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
        self::clearCalcCache();
    }

    public static function OnDelete(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
        self::clearCalcCache();
    }
}