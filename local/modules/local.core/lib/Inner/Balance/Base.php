<?php

namespace Local\Core\Inner\Balance;

use Bitrix\Main\UserTable;

/**
 * Базовый класс по работе с балансом
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
        $obCache->startDataCache(60 * 60 * 24 * 7, 'user_id='.$intUserId, \Local\Core\Inner\Cache::getCachePath(['balance'], ['user_id='.$intUserId]))
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
                throw new \Exception('Баланс должен быть больше нуля.');
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
                throw new \Exception('Баланс должен быть больше нуля.');
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
}