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
     * Получить дополнительные данные, приспособленные к выводу в админке
     *
     * @param $strAdditionalData
     *
     * @return mixed
     */
    public static function getAdditionalDataInAdmin($strAdditionalData);
}