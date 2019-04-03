<?

namespace Local\Core\Model\Data;

use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

// TODO сделать OnBeforeDelete проверку на наличие сайтов у компании и стрелять Exception если их более 0. Пусть удалять сайты сначала!

/**
 * Класс ORM компаний.
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [31.03.2019 21:07:59] |
 * Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [31.03.2019 21:07:59] | Fields\DatetimeField</li><li>USER_OWN_ID - Владелец компании [1] | Fields\IntegerField</li><li>TYPE -
 * Тип компании [FI] | Fields\EnumField<br/>&emsp;FI => Физ. лицо<br/>&emsp;UR => Юр. лицо<br/></li><li>NAME - Название | Fields\StringField</li><li>VERIFIED - Верифицирована [N] |
 * Fields\EnumField<br/>&emsp;N => Верификация не проводилась<br/>&emsp;Y => Верификация успешно пройдена<br/>&emsp;E => Ошибка верификации<br/></li><li>VERIFIED_NOTE - Комментарий верификации,
 * выводится в случае ошибки верификации | Fields\TextField</li><li>COMPANY_INN - ИНН | Fields\StringField</li><li>COMPANY_NAME_SHORT - Сокращенное наименование организации |
 * Fields\TextField</li><li>COMPANY_NAME_FULL - Полное наименование организации | Fields\TextField</li><li>COMPANY_OGRN - ОГРН/ОГРНИП | Fields\StringField</li><li>COMPANY_KPP - КПП |
 * Fields\StringField</li><li>COMPANY_OKPO - ОКПО | Fields\StringField</li><li>COMPANY_OKTMO - ОКТМО | Fields\StringField</li><li>COMPANY_DIRECTOR - Ген. директор |
 * Fields\TextField</li><li>COMPANY_ACCOUNTANT - Гл. бухгалтер | Fields\TextField</li><li>COMPANY_ADDRESS_COUNTRY - Страна | Fields\TextField</li><li>COMPANY_ADDRESS_REGION - Область |
 * Fields\TextField</li><li>COMPANY_ADDRESS_AREA - Район | Fields\TextField</li><li>COMPANY_ADDRESS_CITY - Город | Fields\TextField</li><li>COMPANY_ADDRESS_ADDRESS - Улица, дом, корпус, строение |
 * Fields\TextField</li><li>COMPANY_ADDRESS_OFFICE - Квартира / офис | Fields\TextField</li><li>COMPANY_ADDRESS_ZIP - Почтовый индекс | Fields\IntegerField</li><li>STORES -
 * \Local\Core\Model\Data\Store | Fields\Relations\OneToMany</li><li>USER - \Bitrix\Main\User | Fields\Relations\Reference</li></ul>
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
        ],
        'TYPE' => [
            'FI' => 'Физическое лицо',
            'UR' => 'Юридическое лицо',
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

            new Fields\IntegerField('USER_OWN_ID', [
                    'title' => 'Владелец компании',
                    'default_value' => function ()
                        {
                            return $GLOBALS['USER']->GetID();
                        }
                ]),
            new Fields\EnumField('TYPE', [
                    'title' => 'Тип компании',
                    'required' => true,
                    'values' => self::getEnumFieldValues('TYPE'),
                    'default_value' => 'FI'
                ]),
            new Fields\StringField('NAME', [
                    'title' => 'Название',
                    'required' => true,
                ]),
            new Fields\EnumField('VERIFIED', [
                    'title' => 'Верифицирована',
                    'values' => self::getEnumFieldValues('VERIFIED'),
                    'default_value' => 'N'
                ]),
            new Fields\TextField('VERIFIED_NOTE', [
                    'title' => 'Комментарий верификации, выводится в случае ошибки верификации',
                ]),

            new Fields\StringField('COMPANY_INN', [
                    'title' => 'ИНН',
                    'validation' => function ()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]),
            new Fields\TextField('COMPANY_NAME_SHORT', [
                    'title' => 'Сокращенное наименование организации',
                ]),
            new Fields\TextField('COMPANY_NAME_FULL', [
                    'title' => 'Полное наименование организации',
                ]),
            new Fields\StringField('COMPANY_OGRN', [
                    'title' => 'ОГРН/ОГРНИП',
                    'validation' => function ()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]),
            new Fields\StringField('COMPANY_KPP', [
                    'title' => 'КПП',
                    'validation' => function ()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+|\-?)?/')
                            ];
                        }
                ]),
            new Fields\StringField('COMPANY_OKPO', [
                    'title' => 'ОКПО',
                    'validation' => function ()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]),
            new Fields\StringField('COMPANY_OKTMO', [
                    'title' => 'ОКТМО',
                    'validation' => function ()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]),
            new Fields\TextField('COMPANY_DIRECTOR', [
                    'title' => 'Ген. директор',
                ]),
            new Fields\TextField('COMPANY_ACCOUNTANT', [
                    'title' => 'Гл. бухгалтер',
                ]),

            new Fields\TextField('COMPANY_ADDRESS_COUNTRY', [
                    'title' => 'Страна',
                ]),
            new Fields\TextField('COMPANY_ADDRESS_REGION', [
                    'title' => 'Область',
                ]),
            new Fields\TextField('COMPANY_ADDRESS_AREA', [
                    'title' => 'Район',
                ]),
            new Fields\TextField('COMPANY_ADDRESS_CITY', [
                    'title' => 'Город',
                ]),
            new Fields\TextField('COMPANY_ADDRESS_ADDRESS', [
                    'title' => 'Улица, дом, корпус, строение',
                ]),
            new Fields\TextField('COMPANY_ADDRESS_OFFICE', [
                    'title' => 'Квартира / офис',
                ]),
            new Fields\IntegerField('COMPANY_ADDRESS_ZIP', [
                    'title' => 'Почтовый индекс',
                    'validation' => function ()
                        {
                            return [
                                new Entity\Validator\RegExp('/([\d]+)?/')
                            ];
                        }
                ]),

            (new Fields\Relations\OneToMany('STORES', \Local\Core\Model\Data\StoreTable::class, 'COMPANY')),
            new Fields\Relations\Reference('USER', \Bitrix\Main\UserTable::class, \Bitrix\Main\ORM\Query\Join::on('this.USER_OWN_ID', 'ref.ID'))
        ];
    }

    public static function onBeforeAdd(Event $event)
    {
        $obResult = new \Bitrix\Main\ORM\EventResult();
        $arFields = $event->getParameter('fields');

        switch ($arFields['TYPE']) {
            case 'FI':
                // Нет притензий
                break;
            case 'UR':
                self::checkRequiredUrFields($event, $obResult);
                break;
        }

        return $obResult;
    }

    public static function onBeforeUpdate(Event $event)
    {
        $obResult = new \Bitrix\Main\ORM\EventResult();
        $arFields = $event->getParameter('fields');

        $arModified = [];

        if (!empty($arFields['TYPE'])) {
            switch ($arFields['TYPE']) {
                case 'FI':
                    $arModified = [
                        'COMPANY_INN' => '',
                        'COMPANY_NAME_SHORT' => '',
                        'COMPANY_NAME_FULL' => '',
                        'COMPANY_OGRN' => '',
                        'COMPANY_KPP' => '',
                        'COMPANY_OKPO' => '',
                        'COMPANY_OKTMO' => '',
                        'COMPANY_DIRECTOR' => '',
                        'COMPANY_ACCOUNTANT' => '',
                        'COMPANY_ADDRESS_COUNTRY' => '',
                        'COMPANY_ADDRESS_REGION' => '',
                        'COMPANY_ADDRESS_AREA' => '',
                        'COMPANY_ADDRESS_CITY' => '',
                        'COMPANY_ADDRESS_ADDRESS' => '',
                        'COMPANY_ADDRESS_OFFICE' => '',
                        'COMPANY_ADDRESS_ZIP' => '',
                    ];
                    break;
                case 'UR':
                    self::checkRequiredUrFields($event, $obResult);
                    break;
            }
        }

        return $obResult;
    }

    private static function checkRequiredUrFields($event, &$obResult)
    {
        $arFields = $event->getParameter('fields');
        $funCheckRequiredField = function ($obField) use ($arFields, $obResult)
            {
                if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                    if (empty($arFields[$obField->getName()])) {
                        $obResult->addError(new \Bitrix\Main\ORM\Fields\FieldError($obField, 'У юр. лиц поле "'.$obField->getTitle().'" должно быть заполнено.'));
                    }
                }
            };

        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_INN'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_NAME_SHORT'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_NAME_FULL'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_OGRN'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_ADDRESS_COUNTRY'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_ADDRESS_CITY'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_ADDRESS_ADDRESS'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_ADDRESS_OFFICE'));
        $funCheckRequiredField($event->getEntity()
            ->getField('COMPANY_ADDRESS_ZIP'));
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
        \Local\Core\Inner\Cache::deleteComponentCache(['personal.company.list'], ['user_id='.$arFields['USER_OWN_ID']]);

        // Скинем кэш деталки компании
        \Local\Core\Inner\Cache::deleteComponentCache(['personal.company.detail'], ['company_id='.$arFields['ID']]);

    }
}