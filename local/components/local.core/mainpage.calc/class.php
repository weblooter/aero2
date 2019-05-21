<?

class MainpageCalcComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->fillResult();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['SHOW_TARIFFS']) || !is_array($arParams['SHOW_TARIFFS'])) {
            $arParams['SHOW_TARIFFS'] = [];
        }
        return $arParams;
    }

    private function fillResult()
    {
        $arNewResult = [];
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        if (
        $obCache->startDataCache(
            (60 * 60 * 3),
            "mainpage_calc",
            \Local\Core\Inner\Cache::getComponentCachePath(['mainpage.calc'])
        )
        ) {
            try {
                if (empty($this->arParams['SHOW_TARIFFS'])) {
                    throw new \Exception();
                }

                $rs = \Local\Core\Model\Data\TariffTable::getList([
                    'filter' => [
                        'CODE' => $this->arParams['SHOW_TARIFFS'],
                        'IS_DEFAULT' => 'N',
                        'ACTIVE' => 'Y',
                        'TYPE' => 'PUB',
                        '>PRICE_PER_TRADING_PLATFORM' => 0,
                        [
                            'LOGIC' => 'OR',
                            ['DATE_ACTIVE_TO' => false],
                            ['>DATE_ACTIVE_TO' => new \Bitrix\Main\Type\DateTime()]
                        ],
                        [
                            'LOGIC' => 'OR',
                            ['DATE_ACTIVE_FROM' => false],
                            ['<DATE_ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime()]
                        ]
                    ],
                    'order' => ['PRICE_PER_TRADING_PLATFORM' => 'ASC', 'IS_ACTION' => 'DESC'],
                    'select' => [
                        'CODE',
                        'NAME',
                        'PRICE' => 'PRICE_PER_TRADING_PLATFORM',
                        'LIMIT' => 'LIMIT_IMPORT_PRODUCTS',
                        'SWITCH_AFTER_ACTIVE_TO',
                        'SWITCH_TARIFF',
                        'PRICE_OLD' => 'LOCAL_CORE_MODEL_DATA_TARIFF_SWITCH_TARIFF_PRICE_PER_TRADING_PLATFORM',
                    ],
                    'runtime' => [
                        (new \Bitrix\Main\ORM\Fields\Relations\Reference(
                            'SWITCH_TARIFF',
                            \Local\Core\Model\Data\TariffTable::class,
                            \Bitrix\Main\ORM\Query\Join::on('this.SWITCH_AFTER_ACTIVE_TO', 'ref.CODE')
                        ))
                    ]
                ]);

                if ($rs->getSelectedRowsCount() < 1) {
                    throw new \Exception();
                }

                while ($ar = $rs->fetch()) {
                    $arAddItem = [
                        'NAME' => $ar['NAME'],
                        'CODE' => $ar['CODE'],
                        'PRICE' => $ar['PRICE'],
                        'LIMIT' => $ar['LIMIT'],
                    ];

                    if ($ar['PRICE_OLD'] > 0) {
                        $arAddItem['PRICE_OLD'] = $ar['PRICE_OLD'];
                    }

                    $arNewResult['ITEMS'][$ar['PRICE']] = $arAddItem;
                    $arNewResult['VALUES'][$ar['PRICE']] = $ar['LIMIT'];
                }

                asort($arNewResult['VALUES']);
                ksort($arNewResult['ITEMS']);
                $arNewResult['VALUES'] = array_values($arNewResult['VALUES']);
                $arNewResult['VALUES'][] = '> '.$arNewResult['VALUES'][sizeof($arNewResult['VALUES']) - 1];
                $arNewResult['VALUES'] = implode(',', $arNewResult['VALUES']);
                $arNewResult['ITEMS'] = array_values($arNewResult['ITEMS']);
                $arNewResult['START_ELEM'] = $arNewResult['ITEMS'][0];
                $arNewResult['START_ELEM']['ITERATOR'] = 0;


                $obCache->endDataCache($arNewResult);
            } catch (\Exception $e) {
                $obCache->abortDataCache();
            }
        } else {
            $arNewResult = $obCache->getVars();
        }
        $this->arResult = $arNewResult;
    }
}