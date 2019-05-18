<?php


namespace Local\Core\Inner\Support;

/**
 * Базовый класс для работы с поддержкой
 *
 * @package Local\Core\Inner\Support
 */
class Base
{
    /** @var array $__register Регистр */
    private static $__register = [];

    /**
     * Заполняет регистр
     *
     * @param integer $intId ID
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillRegister($intId)
    {
        if (is_null(self::$__register[$intId])) {
            $arTmp = \Local\Core\Model\Data\StoreTable::getList([
                'filter' => ['ID' => $intId],
                'select' => [
                    'ID',
                    'NAME',
                    'DOMAIN',
                    'COMPANY_ID',
                    'COMPANY_DATA_' => 'COMPANY',
                    'TARIFF_CODE',
                    'DATE_LAST_SUCCESS_IMPORT',
                    'PRODUCT_SUCCESS_IMPORT',
                    'LAST_IMPORT_VERSION',
                    'LAST_SUCCESS_IMPORT_VERSION',
                ]
            ])
                ->fetch();

            $ar = [
                'ID' => $arTmp['ID'],
                'NAME' => $arTmp['NAME'],
                'DOMAIN' => $arTmp['DOMAIN'],
                'COMPANY_ID' => $arTmp['COMPANY_ID'],
                'COMPANY_USER_OWN_ID' => $arTmp['COMPANY_DATA_USER_OWN_ID'],
                'TARIFF_CODE' => $arTmp['TARIFF_CODE'],
                'LAST_IMPORT_RESULT' => $arTmp['LAST_IMPORT_RESULT'],
                'DATE_LAST_SUCCESS_IMPORT' => $arTmp['DATE_LAST_SUCCESS_IMPORT'],
                'PRODUCT_SUCCESS_IMPORT' => $arTmp['PRODUCT_SUCCESS_IMPORT'],
                'LAST_IMPORT_VERSION' => $arTmp['LAST_IMPORT_VERSION'],
                'LAST_SUCCESS_IMPORT_VERSION' => $arTmp['LAST_SUCCESS_IMPORT_VERSION'],
            ];

            self::$__register[$intStoreId] = $ar;
        }
    }

    /**
     * Возвращает регистр
     *
     * @param integer $intId ID
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getRegister($intId)
    {
        self::__fillRegister($intId);
        return self::$__register[$intId];
    }

    /**
     * Очищает регистр
     *
     * @param $intId
     */
    public static function clearRegister($intId)
    {
        unset(self::$__register[$intId]);
    }
}