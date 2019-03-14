<?php

namespace Local\Core\Model\Data;


use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

/**
 * Класс для работа с ORM счета
 *
 * @package Local\Core\Model\Data
 */
class BillTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_bill';
    }

    /** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
    public static $arEnumFieldsValues = [
    ];

    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID', [
                    'primary'      => true,
                    'autocomplete' => true,
                    'title'        => 'ID'
                ]
            ),
            new Fields\EnumField(
                'ACTIVE', [
                    'title'         => 'Активность',
                    'required'      => false,
                    'values'        => self::getEnumFieldValues('ACTIVE'),
                    'default_value' => 'Y'
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE', [
                    'title'         => 'Дата создания',
                    'required'      => false,
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),
            new Fields\DatetimeField(
                'DATE_MODIFIED', [
                    'title'         => 'Дата последнего изменения',
                    'required'      => false,
                    'default_value' => function()
                        {
                            return new \Bitrix\Main\Type\DateTime();
                        }
                ]
            ),
            new Fields\TextField(
                'ACCOUNT_NUMBER', [
                    'title'      => 'Номер счета',
                    'validation' => function()
                        {
                            return [
                                new \Bitrix\Main\ORM\Fields\Validators\UniqueValidator()
                            ];
                        }
                ]
            ),
            new Fields\IntegerField(
                'COMPANY_ID', [
                    'required' => true,
                    'title'    => 'ID компании'
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
            new Fields\Relations\Reference(
                'BILL_BASKET', \Local\Core\Model\Data\BillBasketTable::class, \Bitrix\Main\ORM\Query\Join::on(
                'this.ID',
                'ref.BILL_ID'
            ), [
                    'title' => 'ORM: Корзины счета'
                ]
            ),
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

    public static function OnAfterUpdate(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }

    public static function OnDelete(\Bitrix\Main\ORM\Event $event)
    {
        # Вызывается строго в конце
        self::_initClearComponentCache($event, []);
    }
}