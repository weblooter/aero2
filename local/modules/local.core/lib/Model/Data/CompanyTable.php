<?

namespace Local\Core\Model\Data;

use \Bitrix\Main\ORM\Fields,
    \Bitrix\Main\Entity;

/**
 * Класс ORM компаний.
 *
 * @package Local\Core\Model\Data
 */
class CompanyTable extends \Bitrix\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_company';
    }

    public static function getMap()
    {
        return [
            new Fields\IntegerField( 'ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ] ),
            new Fields\EnumField( 'ACTIVE', [
                'title' => 'Активность',
                'required' => false,
                'values' => ['Y', 'N'],
                'default_value' => 'Y'
            ] ),
            new Fields\IntegerField( 'USER_OWN_ID', [
                'title' => 'Владелец компании',
                'required' => false,
                'default_value' => function () {
                    return $GLOBALS[ 'USER' ]->GetID();
                }
            ] ),
            new Fields\DatetimeField( 'DATE_CREATE', [
                'title' => 'Дата создания',
                'required' => false,
                'default_value' => function () {
                    return new \Bitrix\Main\Type\DateTime();
                }
            ] ),
            new Fields\DatetimeField( 'DATE_MODIFIED', [
                'title' => 'Дата последнего изменения',
                'required' => false,
                'default_value' => function () {
                    return new \Bitrix\Main\Type\DateTime();
                }
            ] ),

            new Fields\EnumField( 'VERIFIED', [
                'title' => 'Верифицирована',
                'required' => false,
                'values' => ['Y', 'N', 'E'], // E - Error
                'default_value' => 'N'
            ] ),
            new Fields\TextField( 'VERIFIED_NOTE', [
                'title' => 'Комментарий верификации, выводится в случае ошибки верификации',
                'required' => false,
            ] ),

            new Fields\StringField( 'COMPANY_INN', [
                'title' => 'ИНН',
                'required' => true,
                'validation' => function () {
                    return [
                        new Entity\Validator\RegExp( '/[\d]+/' )
                    ];
                }
            ] ),
            new Fields\TextField( 'COMPANY_NAME_SHORT', [
                'title' => 'Сокращенное наименование организации',
                'required' => true,
            ] ),
            new Fields\TextField( 'COMPANY_NAME_FULL', [
                'title' => 'Полное наименование организации',
                'required' => true,
            ] ),
            new Fields\StringField( 'COMPANY_OGRN', [
                'title' => 'ОГРН/ОГРНИП',
                'required' => true,
                'validation' => function () {
                    return [
                        new Entity\Validator\RegExp( '/[\d]+/' )
                    ];
                }
            ] ),
            new Fields\StringField( 'COMPANY_KPP', [
                'title' => 'КПП',
                'required' => false,
                'validation' => function () {
                    return [
                        new Entity\Validator\RegExp( '/([\d]+|\-?)/' )
                    ];
                }
            ] ),
            new Fields\StringField( 'COMPANY_OKPO', [
                'title' => 'ОКПО',
                'required' => false,
                'validation' => function () {
                    return [
                        new Entity\Validator\RegExp( '/[\d]+/' )
                    ];
                }
            ] ),
            new Fields\StringField( 'COMPANY_OKTMO', [
                'title' => 'ОКТМО',
                'required' => false,
                'validation' => function () {
                    return [
                        new Entity\Validator\RegExp( '/([\d]+)?/' )
                    ];
                }
            ] ),
            new Fields\TextField( 'COMPANY_DIRECTOR', [
                'title' => 'Ген. директор',
                'required' => false,
            ] ),
            new Fields\TextField( 'COMPANY_ACCOUNTANT', [
                'title' => 'Гл. бухгалтер',
                'required' => false,
            ] ),

            new Fields\TextField( 'COMPANY_ADDRESS_COUNTRY', [
                'title' => 'Страна',
                'required' => true,
            ] ),
            new Fields\TextField( 'COMPANY_ADDRESS_REGION', [
                'title' => 'Область',
                'required' => false,
            ] ),
            new Fields\TextField( 'COMPANY_ADDRESS_AREA', [
                'title' => 'Район',
                'required' => false,
            ] ),
            new Fields\TextField( 'COMPANY_ADDRESS_CITY', [
                'title' => 'Город',
                'required' => true,
            ] ),
            new Fields\TextField( 'COMPANY_ADDRESS_ADDRESS', [
                'title' => 'Улица, дом, корпус, строение',
                'required' => true,
            ] ),
            new Fields\TextField( 'COMPANY_ADDRESS_OFFICE', [
                'title' => 'Квартира / офис',
                'required' => true,
            ] ),
            new Fields\IntegerField( 'COMPANY_ADDRESS_ZIP', [
                'title' => 'Почтовый индекс',
                'required' => true,
                'validation' => function () {
                    return [
                        new Entity\Validator\RegExp( '/([\d]+)?/' )
                    ];
                }
            ] ),
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
    public static function OnBeforeUpdate( \Bitrix\Main\ORM\Event $event )
    {
        $arModifiedFields = [];

        /** @var \Bitrix\Main\ORM\Event $event */
        $arFields = $event->getParameter( 'fields' );

        if ( !empty( $arFields ) )
        {
            $arModifiedFields[ 'DATE_MODIFIED' ] = new \Bitrix\Main\Type\DateTime();
        }

        $arFields = array_merge( $arFields, $arModifiedFields );
        $event->setParameter( 'fields', $arFields );

        /** @var \Bitrix\Main\ORM\EventResult $result */
        $result = new \Bitrix\Main\ORM\EventResult;
        $result->modifyFields( $arModifiedFields );

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
    public static function OnAfterUpdate( \Bitrix\Main\ORM\Event $event )
    {
        /** @var \Bitrix\Main\ORM\Event $event */
        $arEventParams = $event->getParameters();
        if ( !empty( $arEventParams[ 'primary' ][ 'ID' ] ) )
        {
            $ar = self::getById( $arEventParams[ 'primary' ][ 'ID' ] )->fetchRaw();
            self::clearComponentsCache( $ar );
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
    public static function OnDelete( \Bitrix\Main\ORM\Event $event )
    {
        /** @var \Bitrix\Main\ORM\Event $event */
        $arEventParams = $event->getParameters();
        if ( !empty( $arEventParams[ 'primary' ][ 'ID' ] ) )
        {
            $ar = self::getById( $arEventParams[ 'primary' ][ 'ID' ] )->fetchRaw();
            self::clearComponentsCache( $ar );
        }
    }

    /**
     * Метод чистит кэши компонентов, в которых используется данный класс ORM
     *
     * @param $arFields
     */
    public static function clearComponentsCache( $arFields )
    {
        \Local\Core\Inner\Cache::deleteComponentCache( ['personal.company.list'], ['user_id='.$arFields[ 'USER_OWN_ID' ]] );
        \Local\Core\Inner\Cache::deleteComponentCache( ['personal.company.detail'], ['company_id='.$arFields[ 'ID' ]] );
    }
}