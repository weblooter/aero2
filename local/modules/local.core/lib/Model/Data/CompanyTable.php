<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

// TODO сделать OnBeforeDelete проверку на наличие сайтов у компании и стрелять Exception если их более 0. Пусть удалять сайты сначала!

/**
 * Класс ORM компаний.
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>USER_OWN_ID - Владелец компании [1] |
 * Fields\IntegerField</li><li>DATE_CREATE - Дата создания [14.03.2019 20:32:44] | Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [14.03.2019 20:32:44] |
 * Fields\DatetimeField</li><li>VERIFIED - Верифицирована [N] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/>&emsp;E => Ошибка верификации<br/></li><li>VERIFIED_NOTE - Комментарий
 * верификации, выводится в случае ошибки верификации | Fields\TextField</li><li>COMPANY_INN - ИНН | Fields\StringField</li><li>COMPANY_NAME_SHORT - Сокращенное наименование организации |
 * Fields\TextField</li><li>COMPANY_NAME_FULL - Полное наименование организации | Fields\TextField</li><li>COMPANY_OGRN - ОГРН/ОГРНИП | Fields\StringField</li><li>COMPANY_KPP - КПП |
 * Fields\StringField</li><li>COMPANY_OKPO - ОКПО | Fields\StringField</li><li>COMPANY_OKTMO - ОКТМО | Fields\StringField</li><li>COMPANY_DIRECTOR - Ген. директор |
 * Fields\TextField</li><li>COMPANY_ACCOUNTANT - Гл. бухгалтер | Fields\TextField</li><li>COMPANY_ADDRESS_COUNTRY - Страна | Fields\TextField</li><li>COMPANY_ADDRESS_REGION - Область |
 * Fields\TextField</li><li>COMPANY_ADDRESS_AREA - Район | Fields\TextField</li><li>COMPANY_ADDRESS_CITY - Город | Fields\TextField</li><li>COMPANY_ADDRESS_ADDRESS - Улица, дом, корпус, строение |
 * Fields\TextField</li><li>COMPANY_ADDRESS_OFFICE - Квартира / офис | Fields\TextField</li><li>COMPANY_ADDRESS_ZIP - Почтовый индекс | Fields\IntegerField</li><li>STORES - \Local\Core\Model\Data\Store
 * | Fields\Relations\OneToMany</li></ul>
 *
 * @package Local\Core\Model\Data
 */
class CompanyTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{

    public static function getTableName()
    {
        return 'a_model_data_company';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'VERIFIED' => [
            'N' => 'Верификация не проводилась',
            'Y' => 'Верификация успешно пройдена',
            'E' => 'Ошибка верификации',
        ]
    ];

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
                    'required' => false,
                    'values' => self::getEnumFieldValues('ACTIVE'),
                    'default_value' => 'Y'
                ]
            ),
            new Fields\IntegerField(
                'USER_OWN_ID', [
                    'title' => 'Владелец компании',
                    'required' => false,
                    'default_value' => function()
                        {
                            return $GLOBALS['USER']->GetID();
                        }
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE', [
                    'title' => 'Дата создания',
                    'required' => false,
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),
            new Fields\DatetimeField(
                'DATE_MODIFIED', [
                    'title' => 'Дата последнего изменения',
                    'required' => false,
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),

            new Fields\EnumField(
                'VERIFIED', [
                    'title' => 'Верифицирована',
                    'required' => true,
                    'values' => self::getEnumFieldValues('VERIFIED'),
                    'default_value' => 'N'
                ]
            ),
            new Fields\TextField(
                'VERIFIED_NOTE', [
                    'title' => 'Комментарий верификации, выводится в случае ошибки верификации',
                    'required' => false,
                ]
            ),

            new Fields\StringField(
                'COMPANY_INN', [
                    'title' => 'ИНН',
                    'required' => true,
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/[\d]+/')
                            ];
                        }
                ]
            ),
            new Fields\TextField(
                'COMPANY_NAME_SHORT', [
                    'title' => 'Сокращенное наименование организации',
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMPANY_NAME_FULL', [
                    'title' => 'Полное наименование организации',
                    'required' => true,
                ]
            ),
            new Fields\StringField(
                'COMPANY_OGRN', [
                    'title' => 'ОГРН/ОГРНИП',
                    'required' => true,
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/[\d]+/')
                            ];
                        }
                ]
            ),
            new Fields\StringField(
                'COMPANY_KPP', [
                    'title' => 'КПП',
                    'required' => false,
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+|\-?)/')
                            ];
                        }
                ]
            ),
            new Fields\StringField(
                'COMPANY_OKPO', [
                    'title' => 'ОКПО',
                    'required' => false,
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/[\d]+/')
                            ];
                        }
                ]
            ),
            new Fields\StringField(
                'COMPANY_OKTMO', [
                    'title' => 'ОКТМО',
                    'required' => false,
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]
            ),
            new Fields\TextField(
                'COMPANY_DIRECTOR', [
                    'title' => 'Ген. директор',
                    'required' => false,
                ]
            ),
            new Fields\TextField(
                'COMPANY_ACCOUNTANT', [
                    'title' => 'Гл. бухгалтер',
                    'required' => false,
                ]
            ),

            new Fields\TextField(
                'COMPANY_ADDRESS_COUNTRY', [
                    'title' => 'Страна',
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMPANY_ADDRESS_REGION', [
                    'title' => 'Область',
                    'required' => false,
                ]
            ),
            new Fields\TextField(
                'COMPANY_ADDRESS_AREA', [
                    'title' => 'Район',
                    'required' => false,
                ]
            ),
            new Fields\TextField(
                'COMPANY_ADDRESS_CITY', [
                    'title' => 'Город',
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMPANY_ADDRESS_ADDRESS', [
                    'title' => 'Улица, дом, корпус, строение',
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'COMPANY_ADDRESS_OFFICE', [
                    'title' => 'Квартира / офис',
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'COMPANY_ADDRESS_ZIP', [
                    'title' => 'Почтовый индекс',
                    'required' => true,
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]
            ),

            ( new Fields\Relations\OneToMany(
                'STORES', \Local\Core\Model\Data\StoreTable::class, 'COMPANY'
            ) )
        ];
    }

    /**
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function OnAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    /**
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function OnDelete(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }


    /**
     * @inheritdoc
     */
    public static function clearComponentsCache($arFields)
    {
        // Скинем кэш списка компаний текущего владельца
        \Local\Core\Inner\Cache::deleteComponentCache(
            ['personal.company.list'],
            ['user_id='.$arFields['USER_OWN_ID']]
        );

        // Скинем кэш деталки компании
        \Local\Core\Inner\Cache::deleteComponentCache(
            ['personal.company.detail'],
            ['company_id='.$arFields['ID']]
        );

    }
}