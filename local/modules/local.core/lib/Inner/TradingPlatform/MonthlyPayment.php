<?php


namespace Local\Core\Inner\TradingPlatform;


use Bitrix\Main\UserTable;

/**
 * Класс ежемесячной оплаты торговых площадок
 *
 * @package Local\Core\Inner\TradingPlatform
 */
class MonthlyPayment
{
    public static function execute()
    {
        $rsTp = \Local\Core\Model\Data\TradingPlatformTable::getList([
            'filter' => [
                'ACTIVE' => 'Y',
                '<=PAYED_TO' => new \Bitrix\Main\Type\DateTime()
            ],
            'select' => ['ID', 'STORE_ID', 'NAME']
        ]);
        while ($arTp = $rsTp->fetch()) {
            $obPayResult = \Local\Core\Inner\Balance\Base::payTradingPlatform($arTp['ID']);
            if (!$obPayResult->isSuccess()) {
                \Local\Core\Model\Data\TradingPlatformTable::update(
                    $arTp['ID'],
                    ['ACTIVE' => 'N']
                );

                \Local\Core\Model\Data\TradingPlatformExportLogTable::add([
                    'STORE_ID' => $arTp['STORE_ID'],
                    'TP_ID' => $arTp['ID'],
                    'RESULT' => 'ER',
                    'PRODUCTS_TOTAL' => '0',
                    'PRODUCTS_EXPORTED' => '0',
                    'ERROR_TEXT' => implode('<br/>', $obPayResult->getErrorMessages())
                ]);

                $arUser = \Bitrix\Main\UserTable::getByPrimary(\Local\Core\Inner\Store\Base::getOwnUserId($arTp['STORE_ID']))->fetch();

                \Local\Core\Inner\TriggerMail\TradingPlatform\MonthlyPayment::payTradingPlatformError([
                    'EMAIL' => $arUser['EMAIL'],
                    'STORE_NAME' => \Local\Core\Inner\Store\Base::getStoreName($arTp['STORE_ID']),
                    'TP_NAME' => $arTp['NAME'],
                    'ERROR_TEXT' => implode('<br/>', $obPayResult->getErrorMessages())
                ]);

            } else {

                $strAddTime = 'now + 1 month';
                if( \Local\Core\Inner\Tariff\Base::getDefaultTariff()['CODE'] == \Local\Core\Inner\Store\Base::getTariffCode($arTp['STORE_ID']) )
                {
                    $strAddTime = 'now + 7 day';
                }

                // Списание произошло, активация
                \Local\Core\Model\Data\TradingPlatformTable::update(
                    $arTp['ID'],
                    [
                        'ACTIVE' => 'Y',
                        'PAYED_FROM' => new \Bitrix\Main\Type\DateTime(date('Y.m.d'), 'Y.m.d'),
                        'PAYED_TO' => new \Bitrix\Main\Type\DateTime(date('Y.m.d', strtotime($strAddTime)), 'Y.m.d'),
                    ]
                );
            }
        }
    }
}