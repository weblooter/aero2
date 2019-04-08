<?php
namespace Local\Core\Inner\TradingPlatform;

/**
 * Фабрика ТП
 *
 * @package Local\Core\Inner\TradingPlatform
 */
class Factory
{

    /**
     * Получить обработчик ТП
     *
     * @param $factory
     *
     * @return Handler\AbstractHandler|null
     */
    public static function factory($factory)
    {
        switch ($factory)
        {
            case 'yandex_market':
                return new Handler\YandexMarket\Handler();
                break;

            default:
                return null;
                break;
        }
    }

    /**
     * Получить список доступных фабрик
     *
     * @return array
     */
    public static function getFactoryList()
    {
        return [
            Handler\YandexMarket\Handler::getCode() => Handler\YandexMarket\Handler::getTitle()
        ];
    }
}