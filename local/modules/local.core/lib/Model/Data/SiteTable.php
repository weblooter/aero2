<?
namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields,
    \Bitrix\Main\Entity;

/**
 * Класс ORM сайтов компаний.
 *
 * @package Local\Core\Model\Data
 */
class SiteTable extends \Bitrix\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_site';
    }

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
                'required' => false,
                'values' => ['Y', 'N'],
                'default_value' => 'Y'
            ]),
            new Fields\DatetimeField('DATE_CREATE', [
                'title' => 'Дата создания',
                'required' => false,
                'default_value' => function()
                {
                    return new \Bitrix\Main\Type\DateTime();
                }
            ]),
            new Fields\DatetimeField('DATE_MODIFIED', [
                'title' => 'Дата последнего изменения',
                'required' => false,
                'default_value' => function()
                {
                    return new \Bitrix\Main\Type\DateTime();
                }
            ]),
            new Fields\IntegerField('COMPANY_ID', [
                'required' => true,
                'title' => 'ID компании'
            ]),
            new Fields\StringField('DOMAIN', [
                'required' => true,
                'title' => 'Ссылка на сайт',
                'validation' => function()
                {
                    return [
                        new Entity\Validator\RegExp('/(https?\:\/\/([a-z0-9\-\_]+\.){0,}([a-z0-9\-\_]+\.[a-z]+))/')
                    ];
                },
                'save_data_modification' => function()
                {
                    return [
                        function($value)
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
                'values' => ['LINK', 'FILE']
            ]),
            new Fields\IntegerField('FILE_ID', [
                'required' => false,
                'title' => 'ID загруженного файла (если источник - файл)',
            ]),
            new Fields\DatetimeField('DATE_FILE_UPLOAD', [
                'title' => 'Дата загрузки файла',
                'required' => false
            ]),

            new Fields\StringField('FILE_LINK', [
                'required' => false,
                'title' => 'Ссылка на файл (если источник - ссылка)',
            ]),
            new Fields\EnumField('HTTP_AUTH', [
                'required' => false,
                'title' => 'Для доступа нужен логин и пароль',
                'values' => ['N', 'Y']
            ]),
            new Fields\StringField('HTTP_AUTH_LOGIN', [
                'required' => false,
                'title' => 'Логин для http авторизации',
            ]),
            new Fields\StringField('HTTP_AUTH_PASS', [
                'required' => false,
                'title' => 'Пароль для http авторизации',
            ]),
            new Fields\DatetimeField('DATE_FILE_DOWNLOAD', [
                'title' => 'Дата скачивания файла',
                'required' => false
            ]),

            new Fields\Relations\Reference(
                'COMPANY',
                \Local\Core\Model\Data\CompanyTable::class,
                \Bitrix\Main\ORM\Query\Join::on( 'this.COMPANY_ID', 'ref.ID' ),
                [
                    'title' => 'ORM: Компания'
                ]
            )
        ];
    }

    /**
     * Обновим поле DATE_MODIFIED
     *
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @return \Bitrix\Main\ORM\EventResult
     * @throws \Bitrix\Main\ObjectException
     */
    public static function onBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $arModifiedFields = [];

        /** @var \Bitrix\Main\ORM\Event $event */
        $arFields = $event->getParameter('fields');

        if( !empty( $arFields ) )
        {
            $arModifiedFields['DATE_MODIFIED'] = new \Bitrix\Main\Type\DateTime();
        }

        $arFields = array_merge($arFields, $arModifiedFields);
        $event->setParameter('fields', $arFields);

        /** @var \Bitrix\Main\ORM\EventResult $result */
        $result = new \Bitrix\Main\ORM\EventResult;
        $result->modifyFields($arModifiedFields);

        return $result;
    }

    /**
     * Скинем кэши компонентов
     *
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
        if( !empty( $arEventParams['primary'] ) )
        {
            $ar = self::getById($arEventParams['primary'])->fetchRaw();
            self::clearComponentsCache($ar);
        }
    }

    /**
     * Скинем кэши компонентов
     *
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
        if( !empty( $arEventParams['primary'] ) )
        {
            $ar = self::getById($arEventParams['primary'])->fetchRaw();
            self::clearComponentsCache($ar);
        }
    }

    /**
     * Метод чистит кэши компонентов, в которых используется данный класс ORM
     *
     * @param $arFields
     */
    public static function clearComponentsCache($arFields)
    {
//        \Local\Core\Assistant\Cache::deleteComponentCache('personal.company.list', [ 'user_id='.$arFields['USER_OWN_ID'] ]);
//        \Local\Core\Assistant\Cache::deleteComponentCache('personal.company.detail', [ 'company_id='.$arFields['ID'] ]);
    }
}