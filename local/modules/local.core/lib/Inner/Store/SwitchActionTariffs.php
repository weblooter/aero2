<?php


namespace Local\Core\Inner\Store;

/**
 * Класс для переключения тарифов по дате их завершения
 *
 * @package Local\Core\Inner\Store
 */
class SwitchActionTariffs
{
    /**
     * Метод пробегается по активным магазинам, проверяет срок действия их тарифов и переключает, если тариф кончился
     */
    public static function execute()
    {
        $rsStoreList = \Local\Core\Model\Data\StoreTable::getList([
            'filter' => [
                'ACTIVE' => 'Y',
                '!TARIFF_DATA_DATE_ACTIVE_TO' => false
            ],
            'select' => ['ID', 'TARIFF_CODE', 'TARIFF_DATA_' => 'TARIFF']
        ]);
        while ($arStore = $rsStoreList->fetch())
        {
            dump($arStore);
            if(
                $arStore['TARIFF_DATA_DATE_ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime
                && $arStore['TARIFF_DATA_DATE_ACTIVE_TO']->getTimestamp() < strtotime('now')
            )
            {
                $strCodeNewTariff = $arStore['TARIFF_DATA_SWITCH_AFTER_ACTIVE_TO'];
                if( empty( $strCodeNewTariff ) )
                {
                    $strCodeNewTariff = \Local\Core\Inner\Tariff\Base::getDefaultTariff()['CODE'];
                }

                $obRes = \Local\Core\Inner\Store\Base::changeStoreTariff($arStore['ID'], $strCodeNewTariff, true, true);
            }
        }
    }
}