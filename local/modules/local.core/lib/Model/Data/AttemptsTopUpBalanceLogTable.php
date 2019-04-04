<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields;

/**
 * ORM попыток пополнить баланс
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>DATE_CREATE - Дата создания [04.04.2019 13:42:02] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [04.04.2019 13:42:02] |
 * Fields\DatetimeField</li><li>USER_ID - ID пользователя | Fields\IntegerField</li><li>HANDLER - HANDLER | Fields\StringField</li><li>QUERY_DATA - Данные по из запроса |
 * Fields\TextField</li><li>ADDITIONAL_DATA - Дополнительные данные, возникающие в ходе проверки | Fields\TextField</li><li>QUERY_CHECK_RESULT - Результат проверки запроса |
 * Fields\EnumField<br/>&emsp;SU => Успех<br/>&emsp;ER => Ошибка<br/></li><li>QUERY_CHECK_ERROR_TEXT - Ошибка проверки запроса | Fields\TextField</li><li>TRY_TOP_UP_BALANCE_RESULT - Результат попытки
 * пополнить баланс | Fields\EnumField<br/>&emsp;SU => Успех<br/>&emsp;ER => Ошибка<br/></li><li>TRY_TOP_UP_BALANCE_ERROR_TEXT - Ошибка попытки пополнить баланс | Fields\TextField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class AttemptsTopUpBalanceLogTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_attempts_top_up_balance_log';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'QUERY_CHECK_RESULT' => [
            'SU' => 'Успех',
            'ER' => 'Ошибка'
        ],
        'TRY_TOP_UP_BALANCE_RESULT' => [
            'SU' => 'Успех',
            'ER' => 'Ошибка'
        ],
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

            new Fields\IntegerField('USER_ID', [
                'title' => 'ID пользователя'
            ]),
            new Fields\StringField('HANDLER', [
                'title' => 'Обработчик'
            ]),
            new Fields\TextField('QUERY_DATA', [
                'title' => 'Данные по из запроса'
            ]),
            new Fields\TextField('ADDITIONAL_DATA', [
                'title' => 'Дополнительные данные, возникающие в ходе проверки'
            ]),
            new Fields\EnumField('QUERY_CHECK_RESULT', [
                'title' => 'Результат проверки запроса',
                'values' => self::getEnumFieldValues('QUERY_CHECK_RESULT')
            ]),
            new Fields\TextField('QUERY_CHECK_ERROR_TEXT', [
                'title' => 'Ошибка проверки запроса'
            ]),
            new Fields\EnumField('TRY_TOP_UP_BALANCE_RESULT', [
                'title' => 'Результат попытки пополнить баланс',
                'values' => self::getEnumFieldValues('TRY_TOP_UP_BALANCE_RESULT')
            ]),
            new Fields\TextField('TRY_TOP_UP_BALANCE_ERROR_TEXT', [
                'title' => 'Ошибка попытки пополнить баланс'
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
}