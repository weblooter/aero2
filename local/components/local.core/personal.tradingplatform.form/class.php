<?php

class PersonalTradingPlatformFormComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
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

        if ($arParams['TP_ID'] < 1) {
            $arParams['TP_ID'] = 0;
        }

        return $arParams;
    }

    private function __fillResult()
    {
        $arResult = [];

        $obTp = ( new Local\Core\Inner\TradingPlatform\Base );
        try
        {
            if( $this->arParams['TP_ID'] < 1 && !empty( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('handler') ) )
            {
                $arResult['OB_HANDLER'] = $obTp->getHandler(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('handler'));
            }
            elseif( $this->arParams['TP_ID'] >= 1 )
            {
                $obTp->load($this->arParams['TP_ID']);
                $arResult['OB_HANDLER'] = $obTp->getHandler();
            }
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\TradingPlatformNotFoundException $e)
        {
            $arResult['STATUS'] = 'TP_NOT_FOUNT';
            $arResult['ERROR_TEXT'] = 'Не удалось загрузить торговую прощадку';
        }
        catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e)
        {
            $arResult['STATUS'] = 'HANDLER_NOT_FOUND';
            $arResult['ERROR_TEXT'] = 'Не удалось загрузить обработчик. Попробуйте позже.';
        }
        catch (\Throwable $e)
        {
            $arResult['STATUS'] = 'ERROR';
            $arResult['ERROR_TEXT'] = $e->getMessage();
        }


        $this->arResult = $arResult;
    }
}