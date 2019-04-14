<?php

class PersonalTradingPlatformListComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkStoreAccess($this->arParams['STORE_ID'], $GLOBALS['USER']->GetID());

        $this->__fillResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if ($arParams['COMPANY_ID'] < 1) {
            $arParams['COMPANY_ID'] = 0;
        }

        if ($arParams['STORE_ID'] < 1) {
            $arParams['STORE_ID'] = 0;
        }

        return $arParams;
    }

    private function __fillResult()
    {
        $arResult = [];

        $rs = \Local\Core\Model\Data\TradingPlatformTable::getList([
            'filter' => [
                'STORE_ID' => $this->arParams['STORE_ID']
            ],
            'select' => [
                'ID',
                'NAME',
                'HANDLER',
                'ACTIVE',
                'PAYED_TO'
            ]
        ]);
        $arResult['ITEMS'] = $rs->fetchAll();

        $this->arResult = $arResult;
    }
}