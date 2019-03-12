<?

namespace Local\Core\Inner\BxModified;

class CBitrixComponent extends \CBitrixComponent
{

    /**
     * Проверка прав пользователя на компанию
     *
     * @param int  $intCompanyId ID компании
     * @param int  $intUserId ID пользователя
     * @param bool $init404Process Запустить процесс 404й (true) или просто вернуть boolean (false), по умолчанию true
     *
     * @return bool
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function _checkCompanyAccess( $intCompanyId, $intUserId = 0, $init404Process = true )
    {
        $isSuccessAccess = true;

        switch ( \Local\Core\Inner\Company\Base::checkUserAccess( $intCompanyId, $intUserId ) )
        {
            case \Local\Core\Inner\Company\Base::ACCESS_COMPANY_NOT_FOUND:
            case \Local\Core\Inner\Company\Base::ACCESS_COMPANY_NOT_MINE:
                $isSuccessAccess = false;
                break;
        }

        if ( !$isSuccessAccess && $init404Process )
        {
            $this->_show404Page();
        }

        return $isSuccessAccess;
    }

    /**
     * Выводит страницу 404
     *
     * @param string $strMessage Сообщение
     *
     * @throws \Bitrix\Main\LoaderException
     */
    protected function _show404Page( $strMessage = '' )
    {
        \Bitrix\Main\Loader::includeModule( 'iblock' );
        \Bitrix\Iblock\Component\Tools::process404( $strMessage, true, true, true, "" );
    }
}