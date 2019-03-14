<?php

namespace Local\Core\Model\Data;


use \Bitrix\Main\ORM\Fields, \Bitrix\Main\Entity;

/**
 * Класс для работа с ORM корзины счета
 *
 * @package Local\Core\Model\Data
 */
class BillProductTable extends \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager
{
    public static function getTableName()
    {
        return 'a_model_data_bill_product';
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
            new Fields\IntegerField(
                'BILL_BASKET_ID', [
                    'title' => 'ID корзины счета'
                ]
            ),

            new Fields\TextField(
                'NAME', [
                    'title'    => 'Название товара',
                    'required' => true,
                ]
            ),

            new Fields\FloatField(
                'PRICE', [
                    'title'    => 'Стоимость',
                    'required' => true,
                ]
            ),

            new Fields\FloatField(
                'QUANTITY', [
                    'title'    => 'Кол-во',
                    'required' => true,
                ]
            ),

            new Fields\EnumField(
                'UNIT', [
                    'title'    => 'Единица измерения',
                    'required' => true,
                    'values'   => ['PIECE', 'HOUR', 'MONTH']
                ]
            ),

            new Fields\EnumField(
                'CURRENCY', [
                    'title'         => 'Валюта',
                    'required'      => true,
                    'values'        => ['RUB'],
                    'default_value' => 'RUB'
                ]
            ),

            new Fields\Relations\Reference(
                'BILL_BASKET', \Local\Core\Model\Data\BillBasketTable::class, \Bitrix\Main\ORM\Query\Join::on(
                'this.BILL_BASKET_ID',
                'ref.ID'
            ), [
                    'title' => 'ORM: Корзина товара'
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