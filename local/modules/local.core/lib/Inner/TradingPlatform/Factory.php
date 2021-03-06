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

            case 'autoru_parts':
                return new Handler\AutoruParts\Handler();
                break;

            case 'beru':
                return new Handler\Beru\Handler();
                break;

            case 'priceru':
                return new Handler\PriceRu\Handler();
                break;

            case 'ekatalog':
                return new Handler\Ekatalog\Handler();
                break;

            case 'magazilla':
                return new Handler\MagaZilla\Handler();
                break;

            case 'dromru':
                return new Handler\Dromru\Handler();
                break;

            case 'google_merchant':
                return new Handler\GoogleMerchant\Handler();
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
            Handler\YandexMarket\Handler::getCode() => Handler\YandexMarket\Handler::getTitle(),
            Handler\AutoruParts\Handler::getCode() => Handler\AutoruParts\Handler::getTitle(),
            Handler\Beru\Handler::getCode() => Handler\Beru\Handler::getTitle(),
            Handler\PriceRu\Handler::getCode() => Handler\PriceRu\Handler::getTitle(),
            Handler\Ekatalog\Handler::getCode() => Handler\Ekatalog\Handler::getTitle(),
            Handler\MagaZilla\Handler::getCode() => Handler\MagaZilla\Handler::getTitle(),
            Handler\Dromru\Handler::getCode() => Handler\Dromru\Handler::getTitle(),
            Handler\GoogleMerchant\Handler::getCode() => Handler\GoogleMerchant\Handler::getTitle(),
        ];
    }
}