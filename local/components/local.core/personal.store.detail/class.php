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
                'TYPE',
                'LIMIT_IMPORT_PRODUCTS',
                'PRICE_PER_TRADING_PLATFORM',
                'DATE_ACTIVE_TO',
                'SWITCH_AFTER_ACTIVE_TO',
                'SWITCHED_TARIFF_DATA_' => 'SWITCHED_TARIFF'
            ],
            'runtime' => [
                ( new \Bitrix\Main\ORM\Fields\Relations\Reference(
                        'SWITCHED_TARIFF',
                    \Local\Core\Model\Data\TariffTable::class,
                    \Bitrix\Main\ORM\Query\Join::on('this.SWITCH_AFTER_ACTIVE_TO', 'ref.CODE')
                ) )
            ]
        ]);
        ?>
        <div class="modal fade" id="change-tariff-modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title pull-left">Список тарифных планов</h5>
                    </div>
                    <div class="modal-body">
                        <?
                        if ($rs->getSelectedRowsCount() < 1):?>
                            <p>
                                Смена тарифного плана временно недоступна.
                            </p>
                        <? else:?>
                            <div class="listview">

                                <?
                                while ($ar = $rs->fetch()) {
                                    $isSelected = ($ar['CODE'] == $this->arResult['TARIFF']['CURRENT']['CODE']);
                                    ?>
                                    <div class="listview__item <?=($isSelected) ? 'bg-warning text-dark' : ''?> border border-secondary border-left-0 border-right-0 border-top-0">
                                        <div class="listview__content">
                                            <?if( $ar['TYPE'] == 'PER' ):?>
                                            <span class="badge badge-warning <?=($isSelected) ? 'border border-dark' : ''?>">Персонализированный тариф</span>
                                            <?endif;?>
                                            <div class="lead <?=($isSelected) ? ' text-dark' : ' text-warning'?>"><?=$ar['NAME']?></div>
                                            <p class="mb-3 <?=($isSelected) ? ' text-dark' : ''?>">
                                                <b>Стоимость:</b> <?=number_format($ar['PRICE_PER_TRADING_PLATFORM'], 0, '.', ' ')?> руб./мес. за площаду<br />
                                                <b>Максимум товаров:</b> <?=number_format($ar['LIMIT_IMPORT_PRODUCTS'], 0, '.', ' ')?><br/>
                                                <?
                                                if ($ar['DATE_ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime): ?>
                                                    <b>Тариф действует до:</b> <?=$ar['DATE_ACTIVE_TO']->format('Y-m-d H:i')?><br/>
                                                    <b>Будет переключен на тариф:</b> <?=( !empty( $ar['SWITCHED_TARIFF_DATA_NAME'] ) ? $ar['SWITCHED_TARIFF_DATA_NAME'] : \Local\Core\Inner\Tariff\Base::getDefaultTariff()['NAME'] )?>
                                                <?endif; ?>
                                            </p>
                                            <?if( !$isSelected ):?>
                                                <a href="javascript:void(0)" class="btn btn-outline-secondary" onclick="PersonalStoreDetailComponent.changeTariff('<?=$this->arResult['ITEM']['ID']?>', '<?=$ar['CODE']?>', '<?=($ar['PRICE_PER_TRADING_PLATFORM'] > $this->arResult['TARIFF']['CURRENT']['PRICE_PER_TRADING_PLATFORM'] ? 'up' : 'down')?>')">Выбрать тариф</a>
                                            <?endif;?>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>

                                <div class="clearfix mb-3"></div>
                            </div>
                        <?endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

        <?
    }
}