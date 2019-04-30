<?php

namespace Local\Core\Inner\Balance;

use Bitrix\Main\UserTable;

/**
 * Базовый класс по работе с балансом, списанием и пополнением
 *
 * @package Local\Core\Inner\Balance
 */
class Base
{
    /**
     * Получить баланс пользователя
     *
     * @param $intUserId
     *
     * @return int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getUserBalance($intUserId)
    {
        $intBalance = 0;
        $obCache = \Bitrix\Main\Application::getInstance()
            ->getCache();
        if (
        $obCache->startDataCache(60 * 60 * 24 * 7, 'user_id='.$intUserId, \Local\Core\Inner\Cache::getCachePath(['Model', 'Data', 'BalanceLogTable', 'UserBalance' ], ['userId='.$intUserId]))
        ) {

            $ar = \Local\Core\Model\Data\BalanceLogTable::getList([
                'filter' => [
                    'USER_ID' => $intUserId
                ],
                'select' => [
                    'CURRENT_BALANCE'
                ],
                'runtime' => [
                    new \Bitrix\Main\ORM\Fields\ExpressionField('CURRENT_BALANCE', 'SUM(OPERATION)')
                ]
            ])
                ->fetch();

            $intBalance = $ar['CURRENT_BALANCE'] ?? 0;
            $obCache->endDataCache($intBalance);
        } else {
            $intBalance = $obCache->getVars();
        }


        return $intBalance;
    }

    /**
     * Пополенение баланса
     *
     * @param string $intBalance Сумма
     * @param string $intUserId  ID пользовалея
     * @param string $strNote    Заметка
     *
     * @return \Bitrix\Main\Result
     */
    public static function payToAccount($intBalance, $intUserId, $strNote)
    {
        $obResult = new \Bitrix\Main\Result();

        try {
            if (floor($intBalance) < 1) {
                throw new \Exception('Сумма пополнения должна быть больше нуля.');
            }

            $rr = \Local\Core\Model\Data\BalanceLogTable::add([
                'USER_ID' => $intUserId,
                'OPERATION' => floor($intBalance),
                'NOTE' => $strNote
            ]);
            if (!$rr->isSuccess()) {
                $obResult->addErrors($rr->getErrors());
            } else {
                $arUser = \Bitrix\Main\UserTable::getByPrimary($intUserId, ['select' => ['EMAIL']])
                    ->fetch();
                \Local\Core\Inner\TriggerMail\Balance::balanceTopUpped([
                    'EMAIL' => $arUser['EMAIL'],
                    'SUMM_FORMAT' => number_format(floor($intBalance), 0, '.', ' '),
                    'TOTAL_SUMM_FORMAT' => number_format(static::getUserBalance($intUserId), 0, '.', ' '),
                    'NOTE' => $strNote
                ]);
            }
        } catch (\Exception $e) {
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
        }

        return $obResult;
    }

    /**
     * Списение баланса
     *
     * @param string $intBalance Сумма
     * @param string $intUserId  ID пользовалея
     * @param string $strNote    Заметка
     *
     * @return \Bitrix\Main\Result
     */
    public static function payFromAccount($intBalance, $intUserId, $strNote)
    {
        $obResult = new \Bitrix\Main\Result();

        try {
            if (floor($intBalance) < 1) {
                throw new \Exception('Сумма списания должна быть больше нуля.');
            }

            $rr = \Local\Core\Model\Data\BalanceLogTable::add([
                'USER_ID' => $intUserId,
                'OPERATION' => -floor($intBalance),
                'NOTE' => $strNote
            ]);
            if (!$rr->isSuccess()) {
                $obResult->addErrors($rr->getErrors());
            } else {
                $arUser = \Bitrix\Main\UserTable::getByPrimary($intUserId, ['select' => ['EMAIL']])
                    ->fetch();
                \Local\Core\Inner\TriggerMail\Balance::balancePayedFromAccount([
                    'EMAIL' => $arUser['EMAIL'],
                    'SUMM_FORMAT' => number_format(floor($intBalance), 0, '.', ' '),
                    'TOTAL_SUMM_FORMAT' => number_format(static::getUserBalance($intUserId), 0, '.', ' '),
                    'NOTE' => $strNote
                ]);
            }
        } catch (\Exception $e) {
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
        }

        return $obResult;
    }

    /**
     * Списание денег со счета за олату торговой площадки
     *
     * @param $intTradingPlatformId
     *
     * @return \Bitrix\Main\Result
     */
    public static function payTradingPlatform($intTradingPlatformId)
    {
        $obResult = new \Bitrix\Main\Result();

        try
        {
            $intStoreId = \Local\Core\Inner\TradingPlatform\Base::getStoreIdByTpId($intTradingPlatformId);

            if( empty( $intStoreId ) )
            {
                throw new \Exception('Не удалось получить идентификатор магазина по идентификатору торговой площадки.');
            }
            $strTariffCode = \Local\Core\Inner\Store\Base::getTariffCode($intStoreId);

            if( empty($strTariffCode) )
            {
                throw new \Exception('Не удалось получить тариф магазина.');
            }

            if( \Local\Core\Inner\Tariff\Base::getActiveStatus($strTariffCode) != 'Y' )
            {
                throw new \Exception('Текущий тариф деактивирован, оплатить его нельзя. Смените тариф.');
            }

            $intTariffPrice = \Local\Core\Inner\Tariff\Base::getPrice($strTariffCode);

            $intUserId = \Local\Core\Inner\Store\Base::getOwnUserId($intStoreId);
            if( empty($intUserId) )
            {
                throw new \Exception('Не удалось определить владельца магазина.');
            }

            if( self::getUserBalance($intUserId) < $intTariffPrice )
            {
                throw new \Exception('Оплатить торговую площадку невозможно, на счете недостаточно средств. Пополните баланс и активируйте торговую площадку вручную.');
            }

            $obPayRes = self::payFromAccount($intTariffPrice, $intUserId, 'Оплата торговой площадки "'.\Local\Core\Inner\TradingPlatform\Base::getName($intTradingPlatformId).'" магазина "'.\Local\Core\Inner\Store\Base::getStoreName( \Local\Core\Inner\TradingPlatform\Base::getStoreIdByTpId($intTradingPlatformId) ).'".');
            if( !$obPayRes->isSuccess() )
            {
                $obResult->addErrors( $obPayRes->getErrors() );
            }
        }
        catch (\Exception $e)
        {
            if( !empty( $e->getMessage() ) )
            {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
        }

        return $obResult;
    }
}