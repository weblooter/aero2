<?

namespace Local\Core\Model\Data;

use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

/**
 * Класс ORM поддержки.
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Прочитано [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [2019-05-16 19:32:08] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [2019-05-16 19:32:08] | Fields\DatetimeField</li><li>SUPPORT_ID - ID обращения | Fields\IntegerField</li><li>OWN - Кто писал | Fields\EnumField<br/>&emsp;US => Пользователь<br/>&emsp;AD => Админ<br/></li><li>MSG - Сообщение | Fields\TextField</li></ul>
 *
 *
 * @package Local\Core\Model\Data
 */
class SupportMessageTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_support_message';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'OWN' => [
            'US' => 'Пользователь',
            'AD' => 'Админ',
        ],
        'READ_STATUS' => [
            'Y' => 'Да',
            'N' => 'Нет',
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
            new Fields\EnumField('ACTIVE', [
                'title' => 'Прочитано',
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
            new Fields\IntegerField('SUPPORT_ID', [
                'required' => true,
                'title' => 'ID обращения'
            ]),
            new Fields\EnumField('OWN', [
                'required' => true,
                'title' => 'Кто писал',
                'values' => self::getEnumFieldValues('OWN'),
            ]),
            new Fields\TextField('MSG', [
                'required' => true,
                'title' => 'Сообщение',
            ]),

            new \Bitrix\Main\ORM\Fields\Relations\Reference(
                'SUPPORT_DATA',
                \Local\Core\Model\Data\SupportMessageTable::class,
                \Bitrix\Main\ORM\Query\Join::on('ref.ID', 'this.SUPPORT_ID')
            )
        ];
    }

    /**
     * @inheritdoc
     */
    public static function clearComponentsCache($arFields)
    {
    }
}