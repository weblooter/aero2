<?

namespace Local\Core\Inner\Site;

/**
 * Класс для работы с сайтам
 *
 * @package Local\Core\Inner\Site
 */
class Base
{
    /** @var array $__register Регистр сайтов */
    private static $__register = [];

    /**
     * Заполняет регистр сайта
     *
     * @param integer $intSiteId ID сайта
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __fillSiteRegister($intSiteId)
    {
        if( is_null(self::$__register[$intSiteId]) )
        {
            $arTmp = \Local\Core\Model\Data\SiteTable::getList([
                'filter' => ['ID' => $intSiteId],
                'select' => [
                    'ID',
                    'DOMAIN',
                    'COMPANY_ID',
                    'COMPANY_DATA_' => 'COMPANY'
                ]
            ])->fetch();

            $ar = [
                'ID' => $arTmp['ID'],
                'DOMAIN' => $arTmp['DOMAIN'],
                'COMPANY_ID' => $arTmp['COMPANY_ID'],
                'COMPANY_USER_OWN_ID' => $arTmp['COMPANY_DATA_USER_OWN_ID'],
            ];

            self::$__register[$intSiteId] = $ar;
        }
    }

    /**
     * Возвращает регистр сайтов
     *
     * @param integer $intSiteId ID сайта
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function __getSiteRegister($intSiteId)
    {
        self::__fillSiteRegister($intSiteId);
        return self::$__register[$intSiteId];
    }


    const ACCESS_SITE_IS_MINE = 0x001;
    const ACCESS_SITE_NOT_FOUND = 0x002;
    const ACCESS_SITE_NOT_MINE = 0x003;

    /**
     * Проверяет права на сайт.<br/>
     * Результат необходимо сравнить с константами класса:<br/>
     * <ul>
     * <li>ACCESS_SITE_IS_MINE</li>
     * <li>ACCESS_SITE_NOT_FOUND</li>
     * <li>ACCESS_SITE_NOT_MINE</li>
     * </ul>
     *
     * @param integer $intSiteId ID компании
     * @param integer $intUserId ID пользователя
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function checkUserAccess($intSiteId, $intUserId = 0)
    {
        if( $intUserId < 1 )
        {
            $intUserId = $GLOBALS['USER']->GetID();
        }

        $ar = self::__getSiteRegister($intSiteId);

        if( !empty( $ar )  )
        {
            if( $ar['COMPANY_USER_OWN_ID'] == $intUserId )
            {
                return self::ACCESS_SITE_IS_MINE;
            }
            else
            {
                return self::ACCESS_SITE_NOT_MINE;
            }
        }
        else
        {
            return self::ACCESS_SITE_NOT_FOUND;
        }

    }

    /**
     * Получить домен сайта
     *
     * @param integer $intSiteId ID компании
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getSiteDomain($intSiteId)
    {
        $ar = self::__getSiteRegister($intSiteId);
        return $ar['DOMAIN'];
    }
}