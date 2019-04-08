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

        $nav = new \Bitrix\Main\UI\PageNavigation("store-nav");
        $nav->allowAllRecords(true)
            ->setPageSize($this->arParams['ELEM_COUNT'])
            ->initFromUri();

        if (
        $obCache->startDataCache((60 * 60 * 24 * 7),
            md5(__METHOD__.'_company_id='.$this->arParams['COMPANY_ID'].'_elem_count='.$this->arParams['ELEM_COUNT'].'_page='.$nav->getCurrentPage().'&offset='.$nav->getOffset()),
            \Local\Core\Inner\Cache::getComponentCachePath(['personal.store.list'], [
                'company_id='.$this->arParams['COMPANY_ID'],
                'elem_count='.$this->arParams['ELEM_COUNT'],
                'page='.$nav->getCurrentPage().'&offset='.$nav->getOffset()
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
                "offset" => $nav->getOffset(),
                "limit" => $nav->getLimit(),
            ]);
            if ($rs->getSelectedRowsCount() < 1) {
                $obCache->abortDataCache();
                $arResult['ITEMS'] = [];
            } else {
                $nav->setRecordCount($rs->getCount());

                while ($ar = $rs->fetch()) {
                    $arResult['ITEMS'][$ar['ID']] = $ar;
                }
                $arResult['NAV_OBJ'] = $nav;

                $obCache->endDataCache($arResult);
            }
        } else {
            $arResult = $obCache->getVars();
        }

        $this->arResult = $arResult;
    }
}