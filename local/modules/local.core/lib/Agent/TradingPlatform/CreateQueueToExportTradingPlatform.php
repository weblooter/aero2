<?php


namespace Local\Core\Agent\TradingPlatform;

/**
 * Анегт создания очереди экспорта торговых площадок
 *
 * @package Local\Core\Agent\TradingPlatform
 */
class CreateQueueToExportTradingPlatform extends \Local\Core\Agent\Base
{
    protected static function execute()
    {
        \Local\Core\Inner\TradingPlatform\Export::createQueue();
    }
}