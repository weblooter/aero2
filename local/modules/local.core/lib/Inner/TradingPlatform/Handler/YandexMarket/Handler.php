<?php

namespace Local\Core\Inner\TradingPlatform\Handler\YandexMarket;


class Handler extends \Local\Core\Inner\TradingPlatform\Handler\AbstractHandler
{
    /** @inheritDoc */
    public static function getCode()
    {
        return 'yandex_market';
    }

    /** @inheritDoc */
    public static function getTitle()
    {
        return 'Яндекс маркет';
    }

    /** @inheritDoc */
    protected function getHandlerFields()
    {
        return [];
    }
}