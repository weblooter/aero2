<?

namespace Local\Core\Model\Data;

use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

/**
 * Класс ORM поддержки.
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [2019-05-16 19:34:37] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [2019-05-16 19:34:37] | Fields\DatetimeField</li><li>USER_ID - ID пользователя | Fields\IntegerField</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class SupportTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{

    public static function getTableName()
    {
        return 'a_model_data_support';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'STATUS' => [
            'OP' => 'Открыто',
            'CL' => 'Закрыто',
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
            new Fields\IntegerField('USER_ID', [
                'required' => true,
                'title' => 'ID пользователя'
            ])
        ];
    }

    /**
     * @inheritdoc
     */
    public static function clearComponentsCache($arFields)
    {
    }
}