<?

namespace Local\Core\Inner\Store;

use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Robofeed\ImportLogTable;

/**
 * Класс для работы с магазинами
 *
 * @package Local\Core\Inner\Store
 */
class Base
{
    /** @var array $__register Регистр магазинов */
    private static $__register = [];

    /**
     * Заполняет регистр сайта
     *
     * @param integer $intStoreId ID магазина
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillStoreRegister($intStoreId)
    {
        if( is_null(self::$__register[$intStoreId]) )
        {
            $arTmp = \Local\Core\Model\Data\StoreTable::getList(
                [
                    'filter' => ['ID' => $intStoreId],
                    'select' => [
                        'ID',
                        'NAME',
                        'DOMAIN',
                        'COMPANY_ID',
                        'COMPANY_DATA_' => 'COMPANY'
                    ]
                ]
            )
                ->fetch();

            $ar = [
                'ID' => $arTmp['ID'],
                'NAME' => $arTmp['NAME'],
                'DOMAIN' => $arTmp['DOMAIN'],
                'COMPANY_ID' => $arTmp['COMPANY_ID'],
                'COMPANY_USER_OWN_ID' => $arTmp['COMPANY_DATA_USER_OWN_ID'],
            ];

            self::$__register[$intStoreId] = $ar;
        }
    }

    /**
     * Возвращает регистр магазинов
     *
     * @param integer $intStoreId ID магазина
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getStoreRegister($intStoreId)
    {
        self::__fillStoreRegister($intStoreId);
        return self::$__register[$intStoreId];
    }


    const ACCESS_STORE_IS_MINE = 0x001;
    const ACCESS_STORE_NOT_FOUND = 0x002;
    const ACCESS_STORE_NOT_MINE = 0x003;

    /**
     * Проверяет права на магазин.<br/>
     * Результат необходимо сравнить с константами класса:<br/>
     * <ul>
     * <li>ACCESS_STORE_IS_MINE</li>
     * <li>ACCESS_STORE_NOT_FOUND</li>
     * <li>ACCESS_STORE_NOT_MINE</li>
     * </ul>
     *
     * @param integer $intStoreId ID компании
     * @param integer $intUserId ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUserAccess($intStoreId, $intUserId = 0)
    {
        if( $intUserId < 1 )
        {
            $intUserId = $GLOBALS['USER']->GetID();
        }

        $ar = self::__getStoreRegister($intStoreId);

        if( !empty($ar) )
        {
            if( $ar['COMPANY_USER_OWN_ID'] == $intUserId )
            {
                return self::ACCESS_STORE_IS_MINE;
            }
            else
            {
                return self::ACCESS_STORE_NOT_MINE;
            }
        }
        else
        {
            return self::ACCESS_STORE_NOT_FOUND;
        }

    }

    /**
     * Получить домен магазина
     *
     * @param integer $intStoreId ID компании
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStoreDomain($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        return $ar['DOMAIN'];
    }

    /**
     * Получить название сайта
     *
     * @param integer $intStoreId ID компании
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStoreName($intStoreId)
    {
        $ar = self::__getStoreRegister($intStoreId);
        return $ar['NAME'];
    }
}