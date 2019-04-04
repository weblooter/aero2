<?
namespace Local\Core\Inner\Payment;

/**
 * Фабрика платежных систем
 *
 * @package Local\Core\Inner\Payment
 */
class Factory
{
    /**
     * Фабрика
     * @param $str
     *
     * @return PaymentInterface
     */
    public static function factory($str)
    {
        switch ($str)
        {
            case 'yandex-money':
                return new YandexMoney();
                break;
            case 'bill':
                return new Bill();
                break;
        }
    }

    public static function getHandlersList()
    {
        $ar = [];
        $ar[ Bill::getCode() ] = Bill::getTitle();
        $ar[ YandexMoney::getCode() ] = YandexMoney::getTitle();
        return $ar;
    }
}