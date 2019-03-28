<?php

class PersonalStoreDetailComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
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

        $this->__fillResult();

        $this->includeComponentTemplate();
    }

    private function __fillResult()
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
                        '*',
                        'LOGS'
                    ],
                    'order' => ['LOCAL_CORE_MODEL_DATA_STORE_LOGS_DATE_CREATE' => 'DESC'],
                    'limit' => 10,
                    'offset' => 0
                ]
            );

            if( $rs->getSelectedRowsCount() < 1 )
            {
                $obCache->abortDataCache();
                $arResult['ITEM'] = [];
            }
            else
            {
                $rs = $rs->fetchObject();

                $arMapFields = [];
                foreach(\Local\Core\Model\Data\StoreTable::getMap() as $obField)
                {
                    if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
                    {
                        $arResult['ITEM'][ $obField->getName() ] = $rs->get( $obField->getName() );
                    }
                }

                $arMapLog = [];
                foreach(\Local\Core\Model\Robofeed\ImportLogTable::getMap() as $obField)
                {
                    if( $obField instanceof \Bitrix\Main\ORM\Fields\ScalarField )
                    {
                        $arMapLog[] = $obField->getName();
                    }
                }

                foreach($rs['LOGS'] as $obLog)
                {
                    if( $obLog->getId() > 0 )
                    {
                        $arTmp = [];

                        foreach($arMapLog as $strField)
                        {
                            $arTmp[ $strField ] = $obLog->get($strField);
                        }

                        $arResult['LOG'][$arTmp['ID']] = $arTmp;
                    }
                }

                $arResult['LOG'] = array_reverse($arResult['LOG'], true);

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