<?php


namespace Local\Core\Agent\TradingPlatform;


/**
 * Агент ежемесячный оплаты
 *
 * @package Local\Core\Agent\TradingPlatform
 */
class MonthlyPayment extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\TradingPlatform\MonthlyPayment::execute();
    }
}