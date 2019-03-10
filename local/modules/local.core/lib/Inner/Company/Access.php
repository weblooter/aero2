<?

namespace Local\Core\Inner\Company;

/**
 * Класс для работы с правами компаний
 *
 * @package Local\Core\Inner\Company
 */
class Access
{
    const ACCESS_COMPANY_IS_MINE = 0x001;
    const ACCESS_COMPANY_NOT_FOUND = 0x002;
    const ACCESS_COMPANY_NOT_MINE = 0x003;

    private static $__registerCheckCompanyAccess = [];

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
     * @param integer $intUserId ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkCompanyAccess( $intCompanyId, $intUserId = 0 )
    {
        if ( $intUserId < 1 )
        {
            $intUserId = $GLOBALS[ 'USER' ]->GetID();
        }

        if ( is_null( self::$__registerCheckCompanyAccess[ $intCompanyId ][ $intUserId ] ) )
        {
            $rs = \Local\Core\Model\Data\CompanyTable::getList( [
                'filter' => [
                    'ID' => $intCompanyId
                ],
                'select' => ['ID', 'USER_OWN_ID']
            ] );
            $ar = $rs->fetchRaw();

            if ( empty( $ar ) )
            {
                self::$__registerCheckCompanyAccess[ $intCompanyId ][ $intUserId ] = self::ACCESS_COMPANY_NOT_FOUND;
            }
            elseif ( $ar[ 'USER_OWN_ID' ] != $intUserId )
            {
                self::$__registerCheckCompanyAccess[ $intCompanyId ][ $intUserId ] = self::ACCESS_COMPANY_NOT_MINE;
            }
            elseif ( $ar[ 'USER_OWN_ID' ] == $intUserId )
            {
                self::$__registerCheckCompanyAccess[ $intCompanyId ][ $intUserId ] = self::ACCESS_COMPANY_IS_MINE;
            }
        }

        return self::$__registerCheckCompanyAccess[ $intCompanyId ][ $intUserId ];
    }
}