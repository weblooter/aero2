<?
namespace Local\Core\Inner\Payment;


interface PaymentInterface
{
    /**
     * Возвращает символьный код обработчика
     *
     * @return string
     */
    public static function getCode();

    /**
     * Возвращает название обработчика
     *
     * @return string
     */
    public static function getTitle();

    /**
     * Вывод на страницу форму оплаты
     */
    public function printPaymentForm();

    /**
     * Вывод на страницу информации об успешности процесса оплаты, ести таковой есть
     */
    public function printPaymentSuccess();
}