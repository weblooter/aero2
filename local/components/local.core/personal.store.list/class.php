<?php

class PersonalStoreListComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkCompanyAccess($this->arParams['COMPANY_ID'], $GLOBALS['USER']->GetID());

        $this->__getAndSetResult();

        $this->includeComponentTemplate();
    }

    private function __getAndSetResult()
    {
        $arResult = [];
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        if (
        $obCache->startDataCache((60 * 60 * 24 * 7),
            md5(__METHOD__.'_company_id='.$this->arParams['COMPANY_ID']),
            \Local\Core\Inner\Cache::getComponentCachePath(['personal.store.list'], [
                'company_id='.$this->arParams['COMPANY_ID']
            ]))
        ) {
            $rs = \Local\Core\Model\Data\StoreTable::getList([
                'filter' => [
                    'COMPANY_ID' => $this->arParams['COMPANY_ID']
                ],
                'order' => ['DATE_CREATE' => 'DESC'],
                'select' => [
                    'ID',
                    'NAME',
                    'DOMAIN',
                    'ACTIVE',
                    'DATE_CREATE',
                    'RESOURCE_TYPE'
                ],
                "count_total" => true,
            ]);
            if ($rs->getSelectedRowsCount() < 1) {
                $obCache->abortDataCache();
                $arResult['ITEMS'] = [];
            } else {

                while ($ar = $rs->fetch()) {
                    $arResult['ITEMS'][$ar['ID']] = $ar;
                }

                $obCache->endDataCache($arResult);
            }
        } else {
            $arResult = $obCache->getVars();
        }

        $this->arResult = $arResult;
    }
}