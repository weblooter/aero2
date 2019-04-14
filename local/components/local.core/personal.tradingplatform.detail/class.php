<?php

class PersonalTradingPlatformDetailComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkTradingPlatformAccess($this->arParams['TP_ID'], $GLOBALS['USER']->GetID());

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

        if ($arParams['TP_ID'] < 1) {
            $arParams['TP_ID'] = 0;
        }

        return $arParams;
    }

    private function __fillResult()
    {
        $arResult = [];

        $arResult['ITEM'] = (new \Local\Core\Inner\TradingPlatform\TradingPlatform())->load($this->arParams['TP_ID'])
            ->getData();

        $this->arResult = $arResult;
    }
}