<?

namespace Local\Core\Model\Data;

use Bitrix\Main\ORM\EntityError;
use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

// TODO сделать OnAfterAdd OnAfterUpdate, которое будет ставить очередь на выполнение проверки файла
// TODO добавить в getMap данные по последней проверке (подключать orm логов сайтов)

/**
 * Класс ORM магазинов компаний.
 *
 * <ul><li>ID - ID | Fields\IntegerField</li><li>ACTIVE - Активность [Y] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N => Нет<br/></li><li>DATE_CREATE - Дата создания [14.03.2019 20:35:58] |
 * Fields\DatetimeField</li><li>DATE_MODIFIED - Дата последнего изменения [14.03.2019 20:35:58] | Fields\DatetimeField</li><li>COMPANY_ID - ID компании | Fields\IntegerField</li><li>DOMAIN - Ссылка
 * на сайт | Fields\StringField</li><li>RESOURCE_TYPE - Источник данных | Fields\EnumField<br/>&emsp;LINK => Ссылка на файл<br/>&emsp;FILE => Загрузить файл<br/></li><li>FILE_ID - Загруженный файл
 * XML | Fields\IntegerField</li><li>FILE_LINK - Ссылка на файл XML | Fields\StringField</li><li>HTTP_AUTH - Для доступа нужен логин и пароль [N] | Fields\EnumField<br/>&emsp;Y => Да<br/>&emsp;N =>
 * Нет<br/></li><li>HTTP_AUTH_LOGIN - Логин для авторизации | Fields\StringField</li><li>HTTP_AUTH_PASS - Пароль для авторизации | Fields\StringField</li><li>COMPANY - \Local\Core\Model\Data\Company
 * | Fields\Relations\Reference</li></ul>
 *
 *
 * @package Local\Core\Model\Data
 */
class StoreTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    const BEHAVIOR_IMPORT_ERROR_STOP_IMPORT = 'STOP_IMPORT';
    const BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID = 'IMPORT_ONLY_VALID';

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
            new Fields\IntegerField(
                'COMPANY_ID', [
                    'required' => true,
                    'title' => 'ID компании'
                ]
            ),
            new Fields\StringField(
                'NAME', [
                    'required' => true,
                    'title' => 'Название',
                ]
            ),
            new Fields\StringField(
                'DOMAIN', [
                    'required' => false,
                    'title' => 'Домен',
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+))?/')
                            ];
                        },
                    'save_data_modification' => function()
                        {
                            return [
                                function($value)
                                    {
                                        preg_match(
                                            '/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+))/',
                                            $value,
                                            $arMatches
                                        );
                                        return $arMatches[1];
                                    }
                            ];
                        }
                ]
            ),
            new Fields\EnumField(
                'RESOURCE_TYPE', [
                    'required' => true,
                    'title' => 'Источник данных',
                    'values' => self::getEnumFieldValues('RESOURCE_TYPE')
                ]
            ),

            new Fields\IntegerField(
                'FILE_ID', [
                    'required' => false,
                    'title' => 'Загруженный файл XML',
                ]
            ),

            new Fields\StringField(
                'FILE_LINK', [
                    'required' => false,
                    'title' => 'Ссылка на файл XML',
                    'validation' => function()
                        {
                            return [
                                new Entity\Validator\RegExp('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+)\/.*?\.xml)?$/')
                            ];
                        },
                    'save_data_modification' => function()
                        {
                            return [
                                function($value)
                                    {
                                        preg_match(
                                            '/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+)\/.*?\.xml)$/',
                                            $value,
                                            $arMatches
                                        );
                                        return $arMatches[1];
                                    }
                            ];
                        }
                ]
            ),
            new Fields\EnumField(
                'HTTP_AUTH', [
                    'required' => false,
                    'title' => 'Для доступа нужен логин и пароль',
                    'values' => self::getEnumFieldValues('HTTP_AUTH'),
                    'default_value' => 'N'
                ]
            ),
            new Fields\StringField(
                'HTTP_AUTH_LOGIN', [
                    'required' => false,
                    'title' => 'Логин для авторизации',
                ]
            ),
            new Fields\StringField(
                'HTTP_AUTH_PASS', [
                    'required' => false,
                    'title' => 'Пароль для авторизации',
                ]
            ),

            new Fields\EnumField(
                'BEHAVIOR_IMPORT_ERROR',
                [
                    'required' => true,
                    'title' => 'Поведение импорта при ошибке',
                    'values' => self::getEnumFieldValues('BEHAVIOR_IMPORT_ERROR'),
                    'default_value' => 'STOP_IMPORT'
                ]
            ),

            new Fields\Relations\Reference(
                'COMPANY', \Local\Core\Model\Data\CompanyTable::class, \Bitrix\Main\ORM\Query\Join::on(
                'this.COMPANY_ID',
                'ref.ID'
            ), [
                    'title' => 'ORM: Компания'
                ]
            ),
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

        try
        {
            switch( $arFields['RESOURCE_TYPE'] )
            {
                case 'LINK':
                    // Ссылка на файл

                    if( empty($arFields['FILE_LINK']) )
                    {
                        throw new \Exception('Необходимо указать ссылку на файл XML');
                    }

                    if( $arFields['HTTP_AUTH'] == 'Y' )
                    {
                        if( empty($arFields['HTTP_AUTH_LOGIN']) )
                        {
                            throw new \Exception('Необходимо указать логин для авторизации');
                        }

                        if( empty($arFields['HTTP_AUTH_PASS']) )
                        {
                            throw new \Exception('Необходимо указать пароль для авторизации');
                        }
                    }

                    $arModifiedFields['FILE_ID'] = '';

                    break;

                case 'FILE':
                    // Загрузить файл

                    if( empty($arFields['FILE_ID']) )
                    {
                        throw new \Exception('Необходимо загрузить файл XML');
                    }

                    $arFields['FILE_LINK'] = '';
                    $arFields['HTTP_AUTH'] = '';
                    $arFields['HTTP_AUTH_LOGIN'] = '';
                    $arFields['HTTP_AUTH_PASS'] = '';

                    break;
            }
        }
        catch( \Exception $e )
        {
            $result->addError(new \Bitrix\Main\ORM\EntityError($e->getMessage()));
        }

        if( !empty($arModifiedFields) )
        {
            $arFields = array_merge(
                $arFields,
                $arModifiedFields
            );
            $event->setParameter(
                'fields',
                $arFields
            );
            $result->modifyFields($arModifiedFields);
        }

        return $result;
    }


    public static function onAfterAdd(\Bitrix\Main\ORM\Event $event)
    {
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
        if( !empty($arFields) )
        {

            try
            {

                switch( $arFields['RESOURCE_TYPE'] )
                {
                    case 'LINK':
                        // Ссылка на файл

                        if( $arFields['HTTP_AUTH'] == 'Y' )
                        {
                            if( empty($arFields['HTTP_AUTH_LOGIN']) )
                            {
                                throw new \Exception('Необходимо указать логин для авторизации');
                            }

                            if( empty($arFields['HTTP_AUTH_PASS']) )
                            {
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

            }
            catch( \Exception $e )
            {
                $result->addError(new \Bitrix\Main\ORM\EntityError($e->getMessage()));
            }

        }

        /*
         * Проверка на смену компании сайта
         */
        $ar = self::getById($event->getParameter('primary')['ID'])
            ->fetch();
        self::$__arStoreIdToOldCompanyId[$ar['ID']] = $ar['COMPANY_ID'];


        $arFields = array_merge(
            $arFields,
            $arModifiedFields
        );
        $event->setParameter(
            'fields',
            $arFields
        );

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
        if( !empty($arEventParams['primary']['ID']) )
        {
            if( self::$__NeedDeleteFileID[$arEventParams['primary']['ID']] > 0 )
            {
                \CFile::Delete(self::$__NeedDeleteFileID[$arEventParams['primary']['ID']]);
                $arModifiedFields['FILE_ID'] = null;
            }
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
        if( !empty($arEventParams['primary']['ID']) )
        {
            $ar = self::getById($arEventParams['primary']['ID'])
                ->fetchRaw();

            self::$__arStoreIdToOldCompanyId[$ar['ID']] = $ar['COMPANY_ID'];

            if( $ar['RESOURCE_TYPE'] == 'FILE' )
            {
                self::$__NeedDeleteFileID[$arEventParams['primary']['ID']] = $ar['FILE_ID'];
            }
        }

        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    /**
     * Удалим файл
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
        if( !empty($arEventParams['primary']) )
        {
            if( self::$__NeedDeleteFileID[$arEventParams['primary']['ID']] > 0 )
            {
                \Local\Core\Inner\BxModified\CFile::Delete(self::$__NeedDeleteFileID[$arEventParams['primary']['ID']]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function clearComponentsCache($arFields)
    {
        // Удаляет кэш списка у текущей компании
        \Local\Core\Inner\Cache::deleteComponentCache(
            ['personal.store.list'],
            ['company_id='.$arFields['COMPANY_ID']]
        );

        // Удаляет кэш деталки сайта
        \Local\Core\Inner\Cache::deleteComponentCache(
            ['personal.store.detail'],
            [
                'store_id='.$arFields['ID']
            ]
        );

        if( self::$__arStoreIdToOldCompanyId[$arFields['ID']] != $arFields['COMPANY_ID'] )
        {
            /*
             * Сменилась компания сайта
             */

            // Удаляет кэш списка у старой компании, если сменился владелец
            \Local\Core\Inner\Cache::deleteComponentCache(
                ['personal.store.list'],
                ['company_id='.self::$__arStoreIdToOldCompanyId[$arFields['ID']]]
            );
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
        return self::getList(
            [
                'filter' => [
                    '!FILE_ID' => false,
                ],
                'select' => ['FILE_ID']
            ]
        );
    }
}