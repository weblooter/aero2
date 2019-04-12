<?php
namespace Local\Core\Inner\TradingPlatform;

use Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException;

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
     * @throws HandlerNotFoundException
     */
    public static function factory($factory)
    {
        switch ($factory)
        {
            case 'yandex_market':
                return new Handler\YandexMarket\Handler();
                break;

            default:
                throw new \Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException();
                break;
        }
    }

    /**
     * Получить список доступных фабрик.<br/>
     * Для контроля публикации необходимо дополнять вручную
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