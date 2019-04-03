<?
namespace Local\Core\Inner\Payment;


class Bill implements PaymentInterface
{
    /** @inheritdoc */
    public static function getCode()
    {
        return 'yandex-money';
    }

    /** @inheritdoc */
    public static function getTitle()
    {
        return 'Оплата по счету';
    }

    /** @inheritdoc */
    public function printPaymentForm()
    {
        // TODO: Implement printPaymentForm() method.
    }

    /** @inheritdoc */
    public function printPaymentSuccess()
    {
        // TODO: Implement printPaymentSuccess() method.
    }
}