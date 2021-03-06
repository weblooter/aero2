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
    /**
     * Инициализация процедуры ежемесячной оплаты
     *
     * @param array $arFilter
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function execute($arFilter = [])
    {
        $arrFilter = [
            'ACTIVE' => 'Y',
            '<=PAYED_TO' => new \Bitrix\Main\Type\DateTime()
        ];

        if( !empty( $arFilter ) )
        {
            $arrFilter = array_merge($arrFilter, $arFilter);
        }

        $rsTp = \Local\Core\Model\Data\TradingPlatformTable::getList([
            'filter' => $arrFilter,
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