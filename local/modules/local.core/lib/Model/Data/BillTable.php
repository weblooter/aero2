<?php

namespace Local\Core\Model\Data;


use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

/**
 * Класс для работа с ORM счета
 *
 * @package Local\Core\Model\Data
 */
class BillTable extends \Bitrix\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_bill';
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
            new Fields\TextField('ACCOUNT_NUMBER', [
                'title' => 'Номер счета',
                'validation' => function()
                    {
                        return [
                            new \Bitrix\Main\ORM\Fields\Validators\UniqueValidator()
                        ];
                    }
            ]),
            new Fields\IntegerField('COMPANY_ID', [
                'required' => true,
                'title' => 'ID компании'
            ]),

            new Fields\Relations\Reference('COMPANY', \Local\Core\Model\Data\CompanyTable::class, \Bitrix\Main\ORM\Query\Join::on('this.COMPANY_ID', 'ref.ID'), [
                'title' => 'ORM: Компания'
            ]),
            new Fields\Relations\Reference('BILL_BASKET', \Local\Core\Model\Data\BillBasketTable::class, \Bitrix\Main\ORM\Query\Join::on('this.ID', 'ref.BILL_ID'), [
                'title' => 'ORM: Корзины счета'
            ]),
        ];
    }

    /**
     * Зададим поле с номером счета
     *
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @return \Bitrix\Main\ORM\EventResult
     */
    public static function OnBeforeAdd(\Bitrix\Main\ORM\Event $event)
    {
        $arModifiedFields = [];

        /** @var \Bitrix\Main\ORM\Event $event */
        $arFields = $event->getParameter('fields');

        if( !empty($arFields) )
        {
            $arConfig = \Bitrix\Main\Config\Configuration::getInstance()->get('bill');
            if( !empty($arConfig['bill_account_number_constructor_class']) && !empty($arConfig['bill_account_number_constructor_method']) && class_exists($arConfig['bill_account_number_constructor_class']) && method_exists($arConfig['bill_account_number_constructor_class'], $arConfig['bill_account_number_constructor_method']) )
            {
                $strClass = $arConfig['bill_account_number_constructor_class'];
                $strMethod = $arConfig['bill_account_number_constructor_method'];
                $arModifiedFields['ACCOUNT_NUMBER'] = $strClass::$strMethod($arFields);
            }

        }

        $arFields = array_merge($arFields, $arModifiedFields);
        $event->setParameter('fields', $arFields);

        /** @var \Bitrix\Main\ORM\EventResult $result */
        $result = new \Bitrix\Main\ORM\EventResult;
        $result->modifyFields($arModifiedFields);

        return $result;
    }

    /**
     * Обновим поле DATE_MODIFIED
     *
     * @param \Bitrix\Main\ORM\Event $event
     *
     * @return \Bitrix\Main\ORM\EventResult|void
     * @throws \Bitrix\Main\ObjectException
     */
    public static function OnBeforeUpdate(\Bitrix\Main\ORM\Event $event)
    {
        $arModifiedFields = [];

        /** @var \Bitrix\Main\ORM\Event $event */
        $arFields = $event->getParameter('fields');

        if( !empty($arFields) )
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
        if( !empty($arEventParams['primary']['ID']) )
        {
            $ar = self::getById($arEventParams['primary']['ID'])->fetchRaw();
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
        if( !empty($arEventParams['primary']['ID']) )
        {
            $ar = self::getById($arEventParams['primary']['ID'])->fetchRaw();
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
        //        \Local\Core\Inner\Cache::deleteComponentCache(['personal.company.list'], [ 'user_id='.$arFields['USER_OWN_ID'] ]);
    }
}