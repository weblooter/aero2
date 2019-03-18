<?php

class PersonalSiteDetailComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkCompanyAccess(
            $this->arParams['COMPANY_ID'],
            $GLOBALS['USER']->GetID()
        );

        $this->_checkStoreAccess(
            $this->arParams['STORE_ID'],
            $GLOBALS['USER']->GetID()
        );

        $this->__getAndSetResult();

        $this->includeComponentTemplate();
    }

    private function __getAndSetResult()
    {
        $arResult = [];
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();

        if(
        $obCache->startDataCache(
            ( 60 * 60 * 24 * 7 ),
            md5(
                __METHOD__.'_store_id='.$this->arParams['STORE_ID']
            ),
            \Local\Core\Inner\Cache::getComponentCachePath(
                ['personal.store.detail'],
                [
                    'store_id='.$this->arParams['STORE_ID']
                ]
            )
        )
        )
        {
            $rs = \Local\Core\Model\Data\StoreTable::getList(
                [
                    'filter' => [
                        'COMPANY_ID' => $this->arParams['COMPANY_ID'],
                        'ID' => $this->arParams['STORE_ID']
                    ],
                    'select' => [
                        '*'
                    ],
                ]
            );

            if( $rs->getSelectedRowsCount() < 1 )
            {
                $obCache->abortDataCache();
                $arResult['ITEM'] = [];
            }
            else
            {
                while( $ar = $rs->fetch() )
                {
                    $arResult['ITEM'] = $ar;
                }

                $obCache->endDataCache($arResult);
            }
        }
        else
        {
            $arResult = $obCache->getVars();
        }

        $this->arResult = $arResult;
    }
}