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
        if( $arParams['STORES_COUNT'] < 1 )
            $arParams['STORES_COUNT'] = 5;

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
                        '*',
                        'STORES'
                    ]
                ]
            );

            $rsCompany = $rsCompany->fetchObject();

            $arCompany = [
                'STORES' => [],
            ];

            foreach(\Local\Core\Model\Data\CompanyTable::getMap() as $obField)
            {
                if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
                {
                    $arCompany[ $obField->getName() ] = $rsCompany->get( $obField->getName() );
                }
            }

            foreach($rsCompany['STORES'] as $obStore)
            {
                if( $obStore->getId() > 0 )
                {
                    $arTmp = [
                        'ID' => $obStore->getId(),
                        'NAME' => $obStore->getName(),
                        'DOMAIN' => $obStore->getDomain(),
                        'ACTIVE' => $obStore->getActive(),
                    ];
                    $arCompany['STORES'][ $arTmp['ID'] ] = $arTmp;
                    if( sizeof($arCompany['STORES']) >= $this->arParams['STORES_COUNT'] )
                        break;
                }
            }

            $arResult['COMPANY'] = $arCompany;

            $obCache->endDataCache($arResult);
        }
        else
        {
            $arResult = $obCache->getVars();
        }

        return $arResult;
    }
}