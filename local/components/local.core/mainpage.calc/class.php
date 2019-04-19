<?
class MainpageCalcComponent extends \Local\Core\Inner\BxModified\CBitrixComponent
{
    public function executeComponent()
    {
        $this->fillResult();

        $this->includeComponentTemplate();
    }

    private function fillResult(){
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        if ($obCache->startDataCache((60 * 60 * 24 * 7), "mainpage_calc", \Local\Core\Inner\Cache::getComponentCachePath(['mainpage.calc'])))
        {
            $arPreresult = [];
            $items = [];
            $rs = \Local\Core\Model\Data\TariffTable::getList( [
                'select' => array(
                    "ACTIVE",
                    "TYPE",
                    "CODE",
                    "NAME",
                    "PRICE_PER_TRADING_PLATFORM",
                    "LIMIT_IMPORT_PRODUCTS",
                    "IS_ACTION",
                    "SWITCH_AFTER_ACTIVE_TO"
                ),
                'filter' => array(
                    "ACTIVE" => "Y",
                    "TYPE" => "PUB",
                    "!CODE" => "TRIAL_7_DAYS"
                ),
                'order' => array(
                    "LIMIT_IMPORT_PRODUCTS" => "ASC"
                )
            ] );
            while ( $res = $rs->fetch() )
            {
                if ( $res[ "IS_ACTION" ] == "Y" )
                {
                    $arPreresult[ "ACTION_ITEMS" ][ $res[ "CODE" ] ] = $res;
                }
                else
                {
                    $arPreresult[ "REGULAR_ITEMS" ][ $res[ "CODE" ] ] = $res;
                }
            }

            if ( count( $arPreresult[ "ACTION_ITEMS" ] ) > 0 )
            {
                foreach ( $arPreresult[ "ACTION_ITEMS" ] as $arItem )
                {
                    $origin = $arPreresult[ "REGULAR_ITEMS" ][ $arItem[ "SWITCH_AFTER_ACTIVE_TO" ] ];
                    $items[ $origin[ "CODE" ] ] = array(
                        "NAME" => $origin[ "NAME" ],
                        "CODE" => $origin[ "CODE" ],
                        "PRICE" => $arItem[ "PRICE_PER_TRADING_PLATFORM" ],
                        "PRICE_OLD" => $origin[ "PRICE_PER_TRADING_PLATFORM" ],
                        "LIMIT" => $arItem[ "LIMIT_IMPORT_PRODUCTS" ]
                    );
                    $arResult[ "VALUES" ] .= $arItem[ "LIMIT_IMPORT_PRODUCTS" ].',';
                }
            }

            foreach ( $arPreresult[ "REGULAR_ITEMS" ] as $arItem )
            {
                if ( !$items[ $arItem[ "CODE" ] ] )
                {
                    $items[ $arItem[ "CODE" ] ] = array(
                        "NAME" => $arItem[ "NAME" ],
                        "CODE" => $arItem[ "CODE" ],
                        "PRICE" => $arItem[ "PRICE_PER_TRADING_PLATFORM" ],
                        "LIMIT" => $arItem[ "LIMIT_IMPORT_PRODUCTS" ]
                    );
                    $arResult[ "VALUES" ] .= $arItem[ "LIMIT_IMPORT_PRODUCTS" ].',';
                }
            }

            ksort( $items );

            $arResult[ "ITEMS" ] = array_values( $items );

            $arResult[ "VALUES" ] .= '> '.$arResult[ "ITEMS" ][ count( $arResult[ "ITEMS" ] ) - 1 ][ "LIMIT" ];

            $arResult[ "START_ELEM" ] = $arResult[ "ITEMS" ][ round( count( $arResult[ "ITEMS" ] ) / 2 ) ];
            $arResult[ "START_ELEM" ][ "LIMIT_FROM" ] = $arResult[ "ITEMS" ][ round( count( $arResult[ "ITEMS" ] ) / 2 ) - 1 ][ "LIMIT" ];
            $arResult[ "START_ELEM" ][ "ITERATOR" ] = round( count( $arResult[ "ITEMS" ] ) / 2 );
            $obCache->endDataCache($arResult);
        } else {
            $arResult = $obCache->getVars();
        }
        $this->arResult = $arResult;
    }
}