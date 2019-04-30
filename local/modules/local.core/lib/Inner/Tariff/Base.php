<?php

namespace Local\Core\Inner\Tariff;


/**
 * Класс для работы с тарифами
 * @package Local\Core\Inner\Tariff
 */
class Base
{
    /** @var array $__register Регистр тариф */
    private static $__register = [];

    /**
     * Заполняет регистр тарифов
     *
     * @param integer $strTariffCode ID магазина
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillTariffRegister($strTariffCode)
    {
        if (is_null(self::$__register[$strTariffCode])) {
            $arTmp = \Local\Core\Model\Data\TariffTable::getList([
                'filter' => ['CODE' => $strTariffCode],
            ])
                ->fetch();

            self::$__register[$strTariffCode] = $arTmp;
        }
    }

    /**
     * Возвращает регистр тарифов
     *
     * @param integer $strTariffCode ID магазина
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getTariffRegister($strTariffCode)
    {
        self::__fillTariffRegister($strTariffCode);
        return self::$__register[$strTariffCode];
    }


    /**
     * Получить тариф по умолчанию
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getDefaultTariff()
    {
        if (is_null(self::$__register['!DEFAULT_TARIFF'])) {
            $arTmp = \Local\Core\Model\Data\TariffTable::getList([
                'filter' => ['IS_DEFAULT' => 'Y']
            ])
                ->fetch();
            self::$__register['!DEFAULT_TARIFF'] = $arTmp;
        }
        return self::$__register['!DEFAULT_TARIFF'];
    }

    /**
     * Получить тариф по коду
     *
     * @param $strTariffCode
     *
     * @return array|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getTariffByCode($strTariffCode)
    {
        return self::__getTariffRegister($strTariffCode);
    }

    /**
     * Получить стоимость тарифа
     *
     * @param $strTariffCode
     *
     * @return null|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getPrice($strTariffCode)
    {
        return self::__getTariffRegister($strTariffCode)['PRICE_PER_TRADING_PLATFORM'];
    }

    /**
     * Получить статус активности
     *
     * @param $strTariffCode
     *
     * @return null|Y|N
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getActiveStatus($strTariffCode)
    {
        return self::__getTariffRegister($strTariffCode)['ACTIVE'];
    }
}