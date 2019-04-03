<?php

namespace Local\Core\Model\Data;


use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields;

/**
 * ORM таблицы лога баланса
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [03.04.2019 12:11:39] | Fields\DatetimeField</li><li>USER_ID - ID пользователя | Fields\IntegerField</li><li>OPERATION -
 * Операция | Fields\IntegerField</li><li>NOTE - Заметка | Fields\TextField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class BalanceLogTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_balance_log';
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
                    'default_value' => function ()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]),
            new Fields\IntegerField('USER_ID', [
                    'title' => 'ID пользователя',
                    'required' => true
                ]),
            new Fields\IntegerField('OPERATION', [
                    'title' => 'Операция',
                    'required' => true
                ]),
            new Fields\TextField('NOTE', [
                    'title' => 'Заметка',
                    'required' => true
                ])
        ];
    }

    public static function onAfterAdd(Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
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

    /** @inheritdoc */
    public static function clearComponentsCache($arFields)
    {
        \Local\Core\Inner\Cache::deleteCache(['balance'], ['user_id='.$arFields['USER_ID']]);
    }
}