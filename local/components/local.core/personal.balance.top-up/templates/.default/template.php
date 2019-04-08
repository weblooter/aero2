<?
/**
 * @global CMain                       $APPLICATION
 * @var array                          $arParams
 * @var array                          $arResult
 * @var \PersonalBalanceTopUpComponent $component
 * @var CBitrixComponentTemplate       $this
 * @var string                         $templateName
 * @var string                         $componentPath
 * @var string                         $templateFolder
 */
?>

<?
if (!empty($arResult['HANDLER'])) {
    /** @var \Local\Core\Inner\Payment\PaymentInterface $obHandler */
    $obHandler = $arResult['HANDLER'];
    $obHandler->printPaymentForm();
} else {
    ?>
    <h4>Выбери способ оплаты:</h4>
    <ul>
        <li>
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => 'yandex-money'])?>">Оплата картой (комиссия 3%)</a>
        </li>
        <li>
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => 'bill'])?>">Оплата по счету</a>
        </li>
    </ul>
    <?
}
?>