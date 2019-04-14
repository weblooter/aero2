<?php

class PersonalTradingPlatformFormComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkStoreAccess($this->arParams['STORE_ID'], $GLOBALS['USER']->GetID());
        if( $this->arParams['TP_ID'] > 0 )
        {
            $this->_checkTradingPlatformAccess($this->arParams['TP_ID'], $GLOBALS['USER']->GetID());
        }

        $this->_checkRequest();

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

    private function _checkRequest()
    {
        $arResult = [];
        if( check_bitrix_sessid() )
        {
            $obRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

            $arUpdateFields = [];

            $arUpdateFields['NAME'] = $obRequest->getPost('TP_DATA')['NAME'];
            $arUpdateFields['HANDLER_RULES'] = $obRequest->getPost('HANDLER_RULES') ;

            if( $this->arParams['TP_ID'] > 0 )
            {

            }
            else
            {
                $arUpdateFields['HANDLER'] = $obRequest->getPost('TP_DATA')['HANDLER'];
                $arUpdateFields['STORE_ID'] = $this->arParams['STORE_ID'];

                $obRes = \Local\Core\Model\Data\TradingPlatformTable::add($arUpdateFields);
                if( $obRes->isSuccess() )
                {
                    $arResult['STATUS'] = 'ADD_SUCCESS';
                    $arResult['ADD_ID'] = $obRes->getId();
                }
                else
                {
                    $arResult['STATUS'] = 'ERROR';
                    $arResult['ERROR_TEXT'] = implode('<br/>', $obRes->getErrorMessages());
                }
            }
        }

        $this->arResult = $arResult;
    }

    private function __fillResult()
    {
        $arResult = [];

        $obTp = ( new Local\Core\Inner\TradingPlatform\TradingPlatform() );
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
                $arResult['TP_DATA'] = $obTp->getData();
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


        $this->arResult = array_merge($this->arResult, $arResult);
    }
}