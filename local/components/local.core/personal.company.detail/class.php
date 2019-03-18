<?

class PersonalCompanyDetailComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkCompanyAccess(
            $this->arParams['COMPANY_ID'],
            $GLOBALS['USER']->GetID()
        );

        $this->arResult = $this->__getResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if( $arParams['ELEM_COUNT'] < 1 )
        {
            $arParams['ELEM_COUNT'] = 10;
        }

        if( $arParams['COMPANY_ID'] < 1 )
        {
            $this->_show404Page();
        }

        return $arParams;
    }

    private function __getResult()
    {
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        $arResult = [];

        if(
        $obCache->startDataCache(
            ( 60 * 60 * 24 * 7 ),
            md5(__METHOD__.'_company_id='.$this->arParams['COMPANY_ID']),
            \Local\Core\Inner\Cache::getComponentCachePath(
                ['personal.company.detail'],
                [
                    'company_id='.$this->arParams['COMPANY_ID']
                ]
            )
        )
        )
        {
            $rsCompany = \Local\Core\Model\Data\CompanyTable::getList(
                [
                    'filter' => [
                        'ID' => $this->arParams['COMPANY_ID'],
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
                ]
            );

            $arResult['COMPANY'] = $rsCompany->fetch();

            $obCache->endDataCache($arResult);
        }
        else
        {
            $arResult = $obCache->getVars();
        }

        return $arResult;
    }
}