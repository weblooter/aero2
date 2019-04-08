<?php

namespace Local\Core\Model\Reference;


use \Bitrix\Main\ORM\Fields;

/**
 * Справочник с валютами<br/>
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [15.03.2019 12:48:02] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [15.03.2019 12:48:02] |
 * Fields\DatetimeField</li><li>SORT - Сортировка [50] | Fields\IntegerField</li><li>NAME - Название | Fields\StringField</li><li>CODE - Символьный код | Fields\StringField</li><li>NUMBER_OF_CURRENCY
 * - Код валюты | Fields\IntegerField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class CurrencyTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Inner\Traits\Reference\ClearReferenceCache;

    public static function getTableName()
    {
        return 'a_model_reference_currency';
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
            new Fields\IntegerField('NUMBER_OF_CURRENCY', [
                'required' => true,
                'title' => 'Код валюты',
                'validation' => function ()
                    {
                        return [
                            new Fields\Validators\UniqueValidator(),
                            new Fields\Validators\RegExpValidator('/[0-9]+/')
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