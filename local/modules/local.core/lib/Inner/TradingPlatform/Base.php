<?php

namespace Local\Core\Inner\TradingPlatform;

/**
 * Базовый класс для работы с торговыми площадками
 *
 * @package Local\Core\Inner\TradingPlatform
 */
class Base
{
    /* ****** */
    /* ACCESS */
    /* ****** */

    /** @var array $__register Регистр ТП */
    private static $__register = [];

    /**
     * Заполняет регистр ТП
     *
     * @param integer $intTradingPlatformId ID ТП
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillTpRegister($intTradingPlatformId)
    {
        if (is_null(self::$__register[$intTradingPlatformId])) {
            $arTmp = \Local\Core\Model\Data\TradingPlatformTable::getList([
                'filter' => ['ID' => $intTradingPlatformId],
                'select' => [
                    'ID',
                    'NAME',
                    'STORE_ID',
                    'HANDLER',
                    'STORE_DATA_' => 'STORE'
                ]
            ])
                ->fetch();

            $ar = [
                'ID' => $arTmp['ID'],
                'NAME' => $arTmp['NAME'],
                'DOMAIN' => $arTmp['DOMAIN'],
                'STORE_ID' => $arTmp['STORE_ID'],
                'HANDLER' => $arTmp['HANDLER'],
                'STORE_DATA_COMPANY_ID' => $arTmp['STORE_DATA_COMPANY_ID']
            ];

            self::$__register[$intTradingPlatformId] = $ar;
        }
    }

    /**
     * Возвращает регистр магазинов
     *
     * @param integer $intTradingPlatformId ID магазина
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getTpRegister($intTradingPlatformId)
    {
        self::__fillTpRegister($intTradingPlatformId);
        return self::$__register[$intTradingPlatformId];
    }


    const ACCESS_TP_IS_MINE = 0x001;
    const ACCESS_TP_NOT_FOUND = 0x002;
    const ACCESS_TP_NOT_MINE = 0x003;

    /**
     * Проверяет права на ТП.<br/>
     * Результат необходимо сравнить с константами класса:<br/>
     * <ul>
     * <li>ACCESS_TP_IS_MINE</li>
     * <li>ACCESS_TP_NOT_FOUND</li>
     * <li>ACCESS_TP_NOT_MINE</li>
     * </ul>
     *
     * @param integer $intTradingPlatformId ID ТП
     * @param integer $intUserId  ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUserAccess($intTradingPlatformId, $intUserId = 0)
    {
        if ($intUserId < 1) {
            $intUserId = $GLOBALS['USER']->GetID();
        }

        $ar = self::__getTpRegister($intTradingPlatformId);

        if (!empty($ar)) {

            switch ( \Local\Core\Inner\Company\Base::checkUserAccess($ar['STORE_DATA_COMPANY_ID'], $intUserId) )
            {
                case \Local\Core\Inner\Company\Base::ACCESS_COMPANY_IS_MINE:
                    return self::ACCESS_TP_IS_MINE;
                    break;
                default:
                    return self::ACCESS_TP_NOT_MINE;
                    break;
            }
        } else {
            return self::ACCESS_TP_NOT_FOUND;
        }

    }

    /* ****** */
    /* GETTER */
    /* ****** */


    /**
     * Получить название ТП
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getName($intTradingPlatformId)
    {
        return self::__getTpRegister($intTradingPlatformId)['NAME'];
    }

    /**
     * Получить код обработчика
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHandlerCode($intTradingPlatformId)
    {
        return self::__getTpRegister($intTradingPlatformId)['HANDLER'];
    }

    /**
     * Получить название обработчика
     *
     * @param $intTradingPlatformId
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHandlerTitle($intTradingPlatformId)
    {
        return \Local\Core\Inner\TradingPlatform\Factory::getFactoryList()[ self::__getTpRegister($intTradingPlatformId)['HANDLER'] ];
    }
}