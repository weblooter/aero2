<?

namespace Local\Core\Model\Data;

use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;
use Bitrix\Seo\LeadAds\Field;
use Local\Core\Inner\Company\Base;

// TODO сделать OnAfterAdd OnAfterUpdate, которое будет ставить очередь на выполнение проверки файла
// TODO добавить в getMap данные по последней проверке (подключать orm логов сайтов)

/**
 * Класс ORM магазинов компаний.
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [15.04.2019 19:00:49] |
 * Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [15.04.2019 19:00:49] | Fields\DatetimeField</li><li>COMPANY_ID - ID компании | Fields\IntegerField</li><li>NAME - Название |
 * Fields\StringField</li><li>DOMAIN - Домен | Fields\StringField</li><li>RESOURCE_TYPE - Источник данных | Fields\EnumField<br/>&emsp;LINK => Ссылка на файл<br/>&emsp;FILE => Загрузить
 * файл<br/></li><li>FILE_ID - Загруженный файл XML | Fields\IntegerField</li><li>FILE_LINK - Ссылка на файл XML | Fields\StringField</li><li>HTTP_AUTH - Для доступа нужен логин и пароль [N] |
 * Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>HTTP_AUTH_LOGIN - Логин для авторизации | Fields\StringField</li><li>HTTP_AUTH_PASS - Пароль для авторизации |
 * Fields\StringField</li><li>BEHAVIOR_IMPORT_ERROR - Поведение импорта при ошибке [STOP_IMPORT] | Fields\EnumField<br/>&emsp;STOP_IMPORT => Не актуализировать данные<br/>&emsp;IMPORT_ONLY_VALID =>
 * Актуализировать только валидные<br/></li><li>ALERT_IF_XML_NOT_MODIFIED - Информировать о не изменившемся Robofeed XML? [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N =>
 * Нет<br/></li><li>DATE_LAST_IMPORT - Дата последнего импорта | Fields\DatetimeField</li><li>LAST_IMPORT_RESULT - Фактический результат последнего импорта | Fields\EnumField<br/>&emsp;SU =>
 * Успешен<br/>&emsp;ER => Ошибочный<br/></li><li>LAST_IMPORT_VERSION - Версия Robofeed в последнем импорте | Fields\IntegerField</li><li>DATE_LAST_SUCCESS_IMPORT - Дата последнего успешного импорта
 * | Fields\DatetimeField</li><li>LAST_SUCCESS_IMPORT_VERSION - Версия Robofeed в последнем успешном импорте | Fields\IntegerField</li><li>PRODUCT_TOTAL_COUNT - Общее кол-во заявленных товаров в
 * Robofeed XML в последней успешной выгрузке | Fields\IntegerField</li><li>PRODUCT_SUCCESS_IMPORT - Кол-во валидных импортированных товаров в последней успешной выгрузке |
 * Fields\IntegerField</li><li>TARIFF_CODE - Тариф [TRIAL_7_DAYS] | Fields\StringField</li><li>COMPANY - \Local\Core\Model\Data\Company | Fields\Relations\Reference</li><li>TARIFF -
 * \Local\Core\Model\Data\Tariff | Fields\Relations\Reference</li><li>IMPORT_LOGS - \Local\Core\Model\Robofeed\ImportLog | Fields\Relations\OneToMany</li><li>TARIFF_LOGS -
 * \Local\Core\Model\Data\StoreTariffChangeLog | Fields\Relations\OneToMany</li></ul>
 *
 *
 * @package Local\Core\Model\Data
 */
class StoreTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    const BEHAVIOR_IMPORT_ERROR_STOP_IMPORT = 'STOP_IMPORT';
    const BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID = 'IMPORT_ONLY_VALID';

    const ALERT_IF_XML_NOT_MODIFIED_Y = 'Y';
    const ALERT_IF_XML_NOT_MODIFIED_N = 'N';

    public static function getTableName()
    {
        return 'a_model_data_store';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
        'RESOURCE_TYPE' => [
            'LINK' => 'Ссылка на файл',
            'FILE' => 'Загрузить файл',
        ],
        'HTTP_AUTH' => [
            'Y' => 'Да',
            'N' => 'Нет'
        ],
        'BEHAVIOR_IMPORT_ERROR' => [
            'STOP_IMPORT' => 'Не актуализировать данные',
            'IMPORT_ONLY_VALID' => 'Актуализировать только валидные',
        ],
        'ALERT_IF_XML_NOT_MODIFIED' => [
            'Y' => 'Да',
            'N' => 'Нет',
        ],
        'LAST_IMPORT_RESULT' => [
            'SU' => 'Успешен',
            'ER' => 'Ошибочный',
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
            new Fields\IntegerField('COMPANY_ID', [
                'required' => true,
                'title' => 'ID компании'
            ]),
            new Fields\StringField('NAME', [
                'required' => true,
                'title' => 'Название',
            ]),
            new Fields\StringField('DOMAIN', [
                'title' => 'Домен',
                'validation' => function ()
                    {
                        return [
                            new Entity\Validator\RegExp('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+))?/')
                        ];
                    },
                'save_data_modification' => function ()
                    {
                        return [
                            function ($value)
                                {
                                    preg_match('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+))/', $value, $arMatches);
                                    return $arMatches[1];
                                }
                        ];
                    }
            ]),
            new Fields\EnumField('RESOURCE_TYPE', [
                'required' => true,
                'title' => 'Источник данных',
                'values' => self::getEnumFieldValues('RESOURCE_TYPE')
            ]),

            new Fields\IntegerField('FILE_ID', [
                'title' => 'Загруженный файл XML',
            ]),

            new Fields\StringField('FILE_LINK', [
                'title' => 'Ссылка на файл XML',
                'validation' => function ()
                    {
                        return [
                            new Entity\Validator\RegExp('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+)\/.*?\.xml)?$/')
                        ];
                    },
                'save_data_modification' => function ()
                    {
                        return [
                            function ($value)
                                {
                                    preg_match('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+)\/.*?\.xml)$/', $value, $arMatches);
                                    return $arMatches[1];
                                }
                        ];
                    }
            ]),
            new Fields\EnumField('HTTP_AUTH', [
                'title' => 'Для доступа нужен логин и пароль',
                'values' => self::getEnumFieldValues('HTTP_AUTH'),
                'default_value' => 'N'
            ]),
            new Fields\StringField('HTTP_AUTH_LOGIN', [
                'title' => 'Логин для авторизации',
            ]),
            new Fields\StringField('HTTP_AUTH_PASS', [
                'title' => 'Пароль для авторизации',
            ]),

            new Fields\EnumField('BEHAVIOR_IMPORT_ERROR', [
                'required' => true,
                'title' => 'Поведение импорта при ошибке',
                'values' => self::getEnumFieldValues('BEHAVIOR_IMPORT_ERROR'),
                'default_value' => 'STOP_IMPORT'
            ]),
            new Fields\EnumField('ALERT_IF_XML_NOT_MODIFIED', [
                'title' => 'Информировать о не изменившемся Robofeed XML?',
                'values' => self::getEnumFieldValues('ALERT_IF_XML_NOT_MODIFIED'),
                'default_value' => 'Y'
            ]),

            new Fields\DatetimeField('DATE_LAST_IMPORT', [
                'title' => 'Дата последнего импорта',
            ]),
            new Fields\EnumField('LAST_IMPORT_RESULT', [
                'title' => 'Фактический результат последнего импорта',
                'values' => self::getEnumFieldValues('LAST_IMPORT_RESULT')
            ]),
            new Fields\IntegerField('LAST_IMPORT_VERSION', [
                'title' => 'Версия Robofeed в последнем импорте'
            ]),
            new Fields\DatetimeField('DATE_LAST_SUCCESS_IMPORT', [
                'title' => 'Дата последнего успешного импорта',
            ]),
            new Fields\IntegerField('LAST_SUCCESS_IMPORT_VERSION', [
                'title' => 'Версия Robofeed в последнем успешном импорте'
            ]),
            new Fields\IntegerField('PRODUCT_TOTAL_COUNT', [
                'required' => false,
                'title' => 'Общее кол-во заявленных товаров в Robofeed XML в последней успешной выгрузке'
            ]),
            new Fields\IntegerField('PRODUCT_SUCCESS_IMPORT', [
                'required' => false,
                'title' => 'Кол-во валидных импортированных товаров в последней успешной выгрузке'
            ]),
            new Fields\StringField('TARIFF_CODE', [
                'title' => 'Тариф',
                'required' => true,
                'validation' => function ()
                    {
                        return array(
                            function ($value, $primary, $row, $field)
                                {
                                    $intCount = TariffTable::getList([
                                        'filter' => [
                                            'CODE' => $value
                                        ],
                                        'select' => ['ID']
                                    ])
                                        ->getSelectedRowsCount();
                                    if ($intCount < 1) {
                                        return 'Тарифа с кодом "'.$value.'" не существует!';
                                    } else {
                                        return true;
                                    }
                                }
                        );
                    },
                'default_value' => function ()
                    {
                        $arDefaultVal = TariffTable::getList([
                            'filter' => [
                                'IS_DEFAULT' => 'Y'
                            ],
                            'select' => ['CODE']
                        ])
                            ->fetch();
                        return (!empty($arDefaultVal['CODE'])) ? $arDefaultVal['CODE'] : null;
                    }
            ]),

            new Fields\Relations\Reference('COMPANY', \Local\Core\Model\Data\CompanyTable::class, \Bitrix\Main\ORM\Query\Join::on('this.COMPANY_ID', 'ref.ID'), [
                'title' => 'ORM: Компания'
            ]),

            new Fields\Relations\Reference('TARIFF', \Local\Core\Model\Data\TariffTable::class, \Bitrix\Main\ORM\Query\Join::on('this.TARIFF_CODE', 'ref.CODE'), [
                'title' => 'ORM: Тариф'
            ]),
            new Fields\Relations\OneToMany('IMPORT_LOGS', \Local\Core\Model\Robofeed\ImportLogTable::class, 'STORE_DATA'),
            new Fields\Relations\OneToMany('TARIFF_LOGS', \Local\Core\Model\Data\StoreTariffChangeLogTable::class, 'STORE_DATA'),
        ];
    }

    /**
     * Проверяет данные
     *
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @return \Bitrix\Main\ORM\EventResult|void
     */
    public static function onBeforeAdd(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        $arFields = $event->getParameter('fields');
        $arModifiedFields = [];
        try {
            switch ($arFields['RESOURCE_TYPE']) {
                case 'LINK':
                    // Ссылка на файл

                    if (empty($arFields['FILE_LINK'])) {
                        throw new \Exception('Необходимо указать ссылку на файл XML');
                    }

                    if ($arFields['HTTP_AUTH'] == 'Y') {
                        if (empty($arFields['HTTP_AUTH_LOGIN'])) {
                            throw new \Exception('Необходимо указать логин для авторизации');
                        }

                        if (empty($arFields['HTTP_AUTH_PASS'])) {
                            throw new \Exception('Необходимо указать пароль для авторизации');
                        }
                    }

                    $arModifiedFields['FILE_ID'] = '';

                    break;

                case 'FILE':
                    // Загрузить файл

                    if (empty($arFields['FILE_ID'])) {
                        throw new \Exception('Необходимо загрузить файл XML');
                    }

                    $arFields['FILE_LINK'] = '';
                    $arFields['HTTP_AUTH'] = '';
                    $arFields['HTTP_AUTH_LOGIN'] = '';
                    $arFields['HTTP_AUTH_PASS'] = '';

                    break;
            }
        } catch (\Exception $e) {
            $result->addError(new \Bitrix\Main\ORM\EntityError($e->getMessage()));
        }

        if (!empty($arModifiedFields)) {
            $arFields = array_merge($arFields, $arModifiedFields);
            $event->setParameter('fields', $arFields);
            $result->modifyFields($arModifiedFields);
        }

        return $result;
    }


    /**
     * @param Event $event
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function onAfterAdd(\Bitrix\Main\ORM\Event $event)
    {
        $arFields = $event->getParameter('fields');
        if (!empty($arFields['TARIFF_CODE'])) {
            StoreTariffChangeLogTable::add([
                'STORE_ID' => $event->getParameter('primary')['ID'],
                'TARIFF_CODE' => $arFields['TARIFF_CODE']
            ]);
        }

        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    /**
     * Регистр файлов на удаление.<br/>
     * Записывает при OnBeforeUpdate(), если меняется ресурс с "Загрузить файл" на "Ссылка на файл"<br/>
     * и OnDelete() , а удаляется при OnAfterUpdate() и OnAfterDelete()<br/>
     * Является ассоциативном массивом <b>SITE_TABLE_ID => FILE_ID</b>
     *
     * @var array $__NeedDeleteFileID
     */
    private static $__NeedDeleteFileID = [];

    /**
     * Регистр старых компаний.<br/>
     * Записывается при OnBeforeUpdate() и OnDelete(), что бы очистить кэш<br/>
     * Является ассоциативном массивом <b>SITE_TABLE_ID => OLD_COMPANY_ID</b>
     *
     * @var array $__arStoreIdToOldCompanyId
     */
    private static $__arStoreIdToOldCompanyId = [];

    /**
     * Обновим поле DATE_MODIFIED
     *
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @return \Bitrix\Main\ORM\EventResult
     * @throws \Bitrix\Main\ObjectException
     */
    public static function OnBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $result = new \Bitrix\Main\ORM\EventResult();
        $arModifiedFields = [];

        $arFields = $event->getParameter('fields');

        /*
         * Чистака полей, которые не соответсвуют логике работы ORM
         */
        if (!empty($arFields)) {

            try {

                switch ($arFields['RESOURCE_TYPE']) {
                    case 'LINK':
                        // Ссылка на файл

                        if ($arFields['HTTP_AUTH'] == 'Y') {
                            if (empty($arFields['HTTP_AUTH_LOGIN'])) {
                                throw new \Exception('Необходимо указать логин для авторизации');
                            }

                            if (empty($arFields['HTTP_AUTH_PASS'])) {
                                throw new \Exception('Необходимо указать пароль для авторизации');
                            }
                        }

                        $arModifiedFields['FILE_ID'] = '';
                        self::$__NeedDeleteFileID[$event->getParameter('primary')['ID']] = $arFields['FILE_ID'];

                        break;

                    case 'FILE':
                        // Загрузить файл

                        $arModifiedFields['FILE_LINK'] = '';
                        $arModifiedFields['HTTP_AUTH'] = '';
                        $arModifiedFields['HTTP_AUTH_LOGIN'] = '';
                        $arModifiedFields['HTTP_AUTH_PASS'] = '';

                        break;
                }

            } catch (\Exception $e) {
                $result->addError(new \Bitrix\Main\ORM\EntityError($e->getMessage()));
            }

        }

        /*
         * Проверка на смену компании сайта
         */
        $ar = self::getById($event->getParameter('primary')['ID'])
            ->fetch();
        self::$__arStoreIdToOldCompanyId[$ar['ID']] = $ar['COMPANY_ID'];


        $arFields = array_merge($arFields, $arModifiedFields);
        $event->setParameter('fields', $arFields);

        $result->modifyFields($arModifiedFields);

        # Вызывается строго в конце
        self::_OnBeforeUpdateBase($event, $result, $arModifiedFields);

        return $result;
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
        /** @var \Bitrix\Main\ORM\Event $event */
        $arEventParams = $event->getParameters();
        if (!empty($arEventParams['primary']['ID'])) {
            if (self::$__NeedDeleteFileID[$arEventParams['primary']['ID']] > 0) {
                \CFile::Delete(self::$__NeedDeleteFileID[$arEventParams['primary']['ID']]);
                $arModifiedFields['FILE_ID'] = null;
            }
        }

        $arFields = $event->getParameter('fields');
        if (!empty($arFields['TARIFF_CODE'])) {
            StoreTariffChangeLogTable::add([
                'STORE_ID' => $event->getParameter('primary')['ID'],
                'TARIFF_CODE' => $arFields['TARIFF_CODE']
            ]);
        }

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
        /** @var \Bitrix\Main\ORM\Event $event */
        $arEventParams = $event->getParameters();
        if (!empty($arEventParams['primary']['ID'])) {
            $ar = self::getById($arEventParams['primary']['ID'])
                ->fetchRaw();

            self::$__arStoreIdToOldCompanyId[$ar['ID']] = $ar['COMPANY_ID'];

            if ($ar['RESOURCE_TYPE'] == 'FILE') {
                self::$__NeedDeleteFileID[$arEventParams['primary']['ID']] = $ar['FILE_ID'];
            }
        }

        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    /**
     * Удалим файл.<br/>
     * Удалим все ТП магазина.<br/>
     * Удалим все логи смены тарифов магазина.<br/>
     *
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function OnAfterDelete(\Bitrix\Main\ORM\Event $event)
    {
        /** @var \Bitrix\Main\ORM\Event $event */
        $arEventParams = $event->getParameters();
        if (!empty($arEventParams['primary'])) {

            /* *********** */
            /* Удалим файл */
            /* *********** */

            if (self::$__NeedDeleteFileID[$arEventParams['primary']['ID']] > 0) {
                \Local\Core\Inner\BxModified\CFile::Delete(self::$__NeedDeleteFileID[$arEventParams['primary']['ID']]);
            }

            $rsStoreTariffLogs = StoreTariffChangeLogTable::getList([
                'filter' => [
                    'STORE_ID' => $arEventParams['primary']['ID']
                ],
                'select' => [
                    'ID'
                ]
            ]);
            while ($ar = $rsStoreTariffLogs->fetch()) {
                StoreTariffChangeLogTable::delete($ar['ID']);
            }

            /* ********************** */
            /* Удалим все ТП магазина */
            /* ********************** */

            $rsTp = \Local\Core\Model\Data\TradingPlatformTable::getList([
                'filter' => ['STORE_ID' => $arEventParams['primary']],
                'select' => ['ID']
            ]);
            while ($ar = $rsTp->fetch()) {
                \Local\Core\Model\Data\TradingPlatformTable::delete($ar['ID']);
            }

            /* ************************************* */
            /* Удалим все логи смены тарифов магазина*/
            /* ************************************* */

            $rsTp = \Local\Core\Model\Data\StoreTariffChangeLogTable::getList([
                'filter' => ['STORE_ID' => $arEventParams['primary']],
                'select' => ['ID']
            ]);
            while ($ar = $rsTp->fetch()) {
                \Local\Core\Model\Data\StoreTariffChangeLogTable::delete($ar['ID']);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function clearComponentsCache($arFields)
    {
        if (!empty($arFields['COMPANY_ID'])) {
            // Удаляет кэш списка у компании на странице списка магазинов
            \Local\Core\Inner\Cache::deleteComponentCache(['personal.store.list'], ['company_id='.$arFields['COMPANY_ID']]);

            // Удаляет кэш деталки компании
            \Local\Core\Inner\Cache::deleteComponentCache(['personal.company.detail'], [
                'company_id='.$arFields['COMPANY_ID']
            ]);

            // Скинем кэш меню текущего владельца
            $intOwnId = \Local\Core\Inner\Company\Base::getCompanyOwn($arFields['COMPANY_ID']);
            if ($intOwnId > 0) {
                \Local\Core\Inner\Cache::deleteComponentCache(['personal.asidemenu'], ['userId='.$intOwnId]);
            }
            \Local\Core\Inner\Cache::deleteComponentCache(['personal.asidemenu'], ['userId='.$GLOBALS['USER']->GetId()]);
        }

        // Удаляет кэш деталки магазина
        \Local\Core\Inner\Cache::deleteComponentCache(['personal.store.detail'], [
            'store_id='.$arFields['ID']
        ]);

        if (self::$__arStoreIdToOldCompanyId[$arFields['ID']] != $arFields['COMPANY_ID']) {
            /*
             * Сменилась компания магазина
             */

            // Удаляет кэш списка у старой компании, если сменился владелец
            \Local\Core\Inner\Cache::deleteComponentCache(['personal.store.list'], ['company_id='.self::$__arStoreIdToOldCompanyId[$arFields['ID']]]);

            // Удаляет кэш деталки старой компании
            \Local\Core\Inner\Cache::deleteComponentCache(['personal.company.detail'], [
                'company_id='.self::$__arStoreIdToOldCompanyId[$arFields['ID']]
            ]);

            // Скинем кэш меню старого владельца
            $intOwnId = \Local\Core\Inner\Company\Base::getCompanyOwn(self::$__arStoreIdToOldCompanyId[$arFields['ID']]);
            if ($intOwnId > 0) {
                \Local\Core\Inner\Cache::deleteComponentCache(['personal.asidemenu'], ['userId='.$intOwnId]);
            }
        }
    }

    /**
     * Метод возвращает объект подготовленный \Bitrix\Main\ORM\Query\Result
     *
     * @return \Bitrix\Main\ORM\Query\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOrmFiles()
    {
        return self::getList([
            'filter' => [
                '!FILE_ID' => false,
            ],
            'select' => ['FILE_ID']
        ]);
    }
}