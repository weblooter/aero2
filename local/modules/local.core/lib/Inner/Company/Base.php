<?

namespace Local\Core\Inner\Company;

/**
 * Класс для работы с компаниями
 *
 * @package Local\Core\Inner\Company
 */
class Base
{
    /** @var array $__register Регистр компаний */
    private static $__register = [];

    /**
     * Заполняет регистр компании
     *
     * @param integer $intCompanyId ID компании
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillCompanyRegister($intCompanyId)
    {
        if( is_null(self::$__register[$intCompanyId]) )
        {
            $ar = \Local\Core\Model\Data\CompanyTable::getList(
                [
                    'filter' => ['ID' => $intCompanyId],
                    'select' => [
                        'ID',
                        'COMPANY_NAME_SHORT',
                        'USER_OWN_ID',
                        'VERIFIED'
                    ]
                ]
            )->fetch();

            self::$__register[$intCompanyId] = $ar;
        }
    }

    /**
     * Возвращает регистр компании
     *
     * @param integer $intCompanyId ID компании
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getCompanyRegister($intCompanyId)
    {
        self::__fillCompanyRegister($intCompanyId);
        return self::$__register[$intCompanyId];
    }


    /**
     * Метод проверяет, верифицированали компания
     *
     * @param integer $intCompanyId ID компании
     *
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function isVerified($intCompanyId)
    {
        $ar = self::__getCompanyRegister($intCompanyId);

        return ( $ar['VERIFIED'] == 'Y' );
    }

    /**
     * Метод получает статус верификации компании
     *
     * @param integer $intCompanyId ID компании
     *
     * @return string Y|N|Y
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getCompanyVerifiedStatus($intCompanyId)
    {
        $ar = self::__getCompanyRegister($intCompanyId);
        return $ar['VERIFIED'];
    }


    const ACCESS_COMPANY_IS_MINE = 0x001;
    const ACCESS_COMPANY_NOT_FOUND = 0x002;
    const ACCESS_COMPANY_NOT_MINE = 0x003;

    /**
     * Проверяет права на компанию.<br/>
     * Результат необходимо сравнить с константами класса:<br/>
     * <ul>
     * <li>ACCESS_COMPANY_IS_MINE</li>
     * <li>ACCESS_COMPANY_NOT_FOUND</li>
     * <li>ACCESS_COMPANY_NOT_MINE</li>
     * </ul>
     *
     * @param integer $intCompanyId ID компании
     * @param integer $intUserId    ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUserAccess($intCompanyId, $intUserId = 0)
    {
        if( $intUserId < 1 )
        {
            $intUserId = $GLOBALS['USER']->GetID();
        }

        $ar = self::__getCompanyRegister($intCompanyId);

        if( !empty($ar) )
        {
            if( $ar['USER_OWN_ID'] == $intUserId )
            {
                return self::ACCESS_COMPANY_IS_MINE;
            }
            else
            {
                return self::ACCESS_COMPANY_NOT_MINE;
            }
        }
        else
        {
            return self::ACCESS_COMPANY_NOT_FOUND;
        }
    }

    /**
     * Получить короткое название компании
     *
     * @param integer $intCompanyId ID компании
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getCompanyName($intCompanyId)
    {
        $ar = self::__getCompanyRegister($intCompanyId);
        return $ar['COMPANY_NAME_SHORT'];
    }
}