<?
class CompanyPersonalDetailComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult = $this->__getResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams( $arParams )
    {
        if( $arParams['ELEM_COUNT'] < 1 )
            $arParams['ELEM_COUNT'] = 10;

        return $arParams;
    }

    private function __getResult()
    {
        $obCache = \Bitrix\Main\Application::getInstance()->getCache();
        $arResult = [];

        $intCompanyId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID');

        if( $obCache->startDataCache(
                (3),
                md5(__METHOD__.'_company_id='.$intCompanyId.'_user_id='.$GLOBALS['USER']->GetID() ),
                \Local\Core\Assistant\Cache::getComponentCachePath('personal.company.detail', [ 'company_id='.$intCompanyId, 'user_id='.$GLOBALS['USER']->GetID() ] )
            )
        )
        {

            switch ( Local\Core\Inner\Company\Access::checkCompanyAccess( $intCompanyId, $GLOBALS['USER']->GetID() ) )
            {
                case \Local\Core\Inner\Company\Access::ACCESS_COMPANY_NOT_FOUND:
                case \Local\Core\Inner\Company\Access::ACCESS_COMPANY_NOT_MINE:

                    $obCache->abortDataCache();

                    if( \Bitrix\Main\Loader::includeModule('iblock') )
                        \Bitrix\Iblock\Component\Tools::process404("", true, true, true, "");

                    break;

                case \Local\Core\Inner\Company\Access::ACCESS_COMPANY_IS_MINE:

                    $arResult['RULE_STATUS'] = 'SUCCESS';

                    $rsCompany = \Local\Core\Model\Data\CompanyTable::getList([
                        'filter' => [
                            'ID' => $intCompanyId,
                            'USER_OWN_ID' => $GLOBALS['USER']->GetID()
                        ],
                        'order' => ['DATE_CREATE' => 'DESC'],
                        'select' => [
                            'ID',
                            'ACTIVE',
                            'DATE_CREATE',
                            'VERIFIED',
                            'VERIFIED_NOTE',
                            'COMPANY_INN',
                            'COMPANY_NAME_SHORT',
                        ]
                    ]);

                    $arResult['COMPANY'] = $rsCompany->fetch();

                    $obCache->endDataCache($arResult);

                    break;
            }
        }
        else
        {
            $arResult = $obCache->getVars();
        }

        return $arResult;
    }
}