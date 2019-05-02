<?php

class PersonalStoreDetailComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->_checkStoreAccess($this->arParams['STORE_ID'], $GLOBALS['USER']->GetID());

        $this->__checkDownloadQuery();

        $this->__fillResult();

        $this->includeComponentTemplate();
    }

    private function __fillResult()
    {
        $arResult = [];
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();

        if (
        $obCache->startDataCache((60 * 60 * 24 * 7), md5(__METHOD__.'_store_id='.$this->arParams['STORE_ID']), \Local\Core\Inner\Cache::getComponentCachePath(['personal.store.detail'], [
            'store_id='.$this->arParams['STORE_ID']
        ]))
        ) {
            $rs = \Local\Core\Model\Data\StoreTable::getList([
                'filter' => [
                    'COMPANY_ID' => $this->arParams['COMPANY_ID'],
                    'ID' => $this->arParams['STORE_ID']
                ],
                'select' => [
                    '*',
                    'IMPORT_LOGS',
                    'TARIFF'
                ],
                'order' => ['LOCAL_CORE_MODEL_DATA_STORE_IMPORT_LOGS_DATE_CREATE' => 'DESC'],
                'limit' => 10,
                'offset' => 0
            ]);

            if ($rs->getSelectedRowsCount() < 1) {
                $obCache->abortDataCache();
                $arResult['ITEM'] = [];
            } else {
                $rs = $rs->fetchObject();

                foreach (\Local\Core\Model\Data\StoreTable::getMap() as $obField) {
                    if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                        $arResult['ITEM'][$obField->getName()] = $rs->get($obField->getName());
                    }
                }

                $arResult['TARIFF'] = $this->__fillTariff($rs);

                $arMapLog = [];
                foreach (\Local\Core\Model\Robofeed\ImportLogTable::getMap() as $obField) {
                    if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                        $arMapLog[] = $obField->getName();
                    }
                }

                foreach ($rs['IMPORT_LOGS'] as $obLog) {
                    if ($obLog->getId() > 0) {
                        $arTmp = [];

                        foreach ($arMapLog as $strField) {
                            $arTmp[$strField] = $obLog->get($strField);
                        }

                        $arResult['LOG'][$arTmp['ID']] = $arTmp;
                    }
                }

                $arResult['LOG'] = array_reverse($arResult['LOG'], true);

                $obCache->endDataCache($arResult);
            }
        } else {
            $arResult = $obCache->getVars();
        }

        $this->arResult = $arResult;
    }

    private function __checkDownloadQuery()
    {
        if (
        !empty(\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->get('getRobofeedXml'))
        ) {
            if (
            file_exists(\Bitrix\Main\Application::getDocumentRoot().\Bitrix\Main\Application::getInstance()
                    ->getContext()
                    ->getRequest()
                    ->get('getRobofeedXml'))
            ) {
                $GLOBALS['APPLICATION']->RestartBuffer();
                $file = \Bitrix\Main\Application::getDocumentRoot().\Bitrix\Main\Application::getInstance()
                        ->getContext()
                        ->getRequest()
                        ->get('getRobofeedXml');
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: '.filesize($file));
                ob_clean();
                flush();
                readfile($file);
                die();
            }
        }
    }

    private function __fillTariff($obStore)
    {
        foreach (\Local\Core\Model\Data\TariffTable::getMap() as $obField) {
            if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                $arResult['CURRENT'][$obField->getName()] = $obStore['TARIFF']->get($obField->getName());
            }
        }

        if (!is_null($arResult['CURRENT']['DATE_ACTIVE_TO'])) {
            $arResult['NEXT'] = (!empty($arResult['CURRENT']['SWITCH_AFTER_ACTIVE_TO'])) ? \Local\Core\Inner\Tariff\Base::getTariffByCode($arResult['CURRENT']['SWITCH_AFTER_ACTIVE_TO']) : \Local\Core\Inner\Tariff\Base::getDefaultTariff();
        }

        $arResult['CHANGED_DATE'] = \Local\Core\Model\Data\StoreTariffChangeLogTable::getList([
            'filter' => [
                'STORE_ID' => $obStore->get('ID'),
            ],
            'order' => ['ID' => 'DESC'],
            'select' => ['DATE_CREATE']
        ])
            ->fetch()['DATE_CREATE'];

        if ($obStore->get('PRODUCT_TOTAL_COUNT') > 0) {
            $arResult['RECOMMEND_TARIFF'] = \Local\Core\Inner\Store\Base::recommendTariff($obStore->get('ID'));
            if (!is_null($arResult['RECOMMEND_TARIFF']['DATE_ACTIVE_TO'])) {
                $arResult['RECOMMEND_TARIFF']['NEXT'] = (!empty($arResult['RECOMMEND_TARIFF']['SWITCH_AFTER_ACTIVE_TO'])) ? \Local\Core\Inner\Tariff\Base::getTariffByCode($arResult['RECOMMEND_TARIFF']['SWITCH_AFTER_ACTIVE_TO']) : \Local\Core\Inner\Tariff\Base::getDefaultTariff();
            }
        }

        return $arResult;
    }

    public function printTariffListHtml()
    {
        $rs = \Local\Core\Model\Data\TariffTable::getList([
            'filter' => [
                'ACTIVE' => 'Y',
                'IS_DEFAULT' => 'N',
                [
                    'LOGIC' => 'OR',
                    ['TYPE' => 'PUB'],
                    ['TYPE' => 'PER', 'PERSONAL_BY_STORE' => $this->arResult['ITEM']['ID']],
                ]
            ],
            'order' => [
                'SORT' => 'ASC',
                'TYPE' => 'ASC'
            ],
            'select' => [
                'NAME',
                'CODE',
                'LIMIT_IMPORT_PRODUCTS',
                'PRICE_PER_TRADING_PLATFORM',
                'DATE_ACTIVE_TO',
            ]
        ]);
        while ($ar = $rs->fetch()) {
            $isSelected = ($ar['CODE'] == $this->arResult['TARIFF']['CURRENT']['CODE']);
            ?>
            <li>
                <a href="javascript:void(0)" <?=!$isSelected ? 'onclick="changeTariff('.$this->arResult['ITEM']['ID'].', \''.$ar['CODE'].'\', \''.($ar['PRICE_PER_TRADING_PLATFORM']
                                                                                                                          > $this->arResult['TARIFF']['CURRENT']['PRICE_PER_TRADING_PLATFORM'] ? 'up' : 'down')
                                      .'\')"' : ''?>
                ><?=$ar['NAME']?>,
                    <?=number_format($ar['PRICE_PER_TRADING_PLATFORM'], 0, '.', ' ')?> руб./мес. за ТП,
                    до <?=number_format($ar['LIMIT_IMPORT_PRODUCTS'], 0, '.', ' ')?> товаров
                    <?=(!is_null($ar['DATE_ACTIVE_TO']) ? ' (Доступен до '.$ar['DATE_ACTIVE_TO']->format('Y-m-d').')' : '')?></a>
            </li>
            <?
        }
    }
}