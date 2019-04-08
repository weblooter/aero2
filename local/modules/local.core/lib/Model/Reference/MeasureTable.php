<?php

namespace Local\Core\Model\Reference;


use \Bitrix\Main\ORM\Fields;

/**
 * Справочник с единицами измерения<br/>
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [14.03.2019 20:21:00] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [14.03.2019 20:21:00] |
 * Fields\DatetimeField</li><li>GROUP - Группа | Fields\EnumField<br/>&emsp;ECONOM => Экономические единицы<br/>&emsp;TIME => Единицы времени<br/>&emsp;LENGTH => Единицы длины<br/>&emsp;WEIGHT =>
 * Единицы массы<br/>&emsp;VOLUME => Единицы объема<br/>&emsp;AREA => Единицы площади<br/></li><li>NAME - Название | Fields\StringField</li><li>CODE - Символьный код | Fields\StringField</li><li>SORT
 * - Сортировка [500] | Fields\IntegerField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class MeasureTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    use \Local\Core\Inner\Traits\Reference\ClearReferenceCache;

    public static function getTableName()
    {
        return 'a_model_reference_measure';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'GROUP' => [
            'ECONOM' => 'Экономические единицы',
            'TIME' => 'Единицы времени',
            'LENGTH' => 'Единицы длины',
            'WEIGHT' => 'Единицы массы',
            'VOLUME' => 'Единицы объема',
            'AREA' => 'Единицы площади'
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
            new Fields\EnumField('GROUP', [
                'required' => true,
                'title' => 'Группа',
                'values' => self::getEnumFieldValues('GROUP')
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