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
    <div class="row mb-4">
        <div class="col-sm-6 col-md-3 mb-4">
            <a class="quick-stats__item d-block mb-0 bg-secondary" href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => 'yandex-money'])?>">
                <div class="quick-stats__info text-center">
                    <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/payment-icons/payment-card-icon.svg" class="d-block m-auto mb-3 tradingplatform__img" />
                    <h5 class="mt-3 mb-0 text-dark tradingplatform__title">Оплата картой (комиссия 3%)</h5>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-3 mb-4">
            <a class="quick-stats__item d-block mb-0 bg-secondary" href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => 'bill'])?>">
                <div class="quick-stats__info text-center">
                    <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/payment-icons/payment-bill-icon.svg" class="d-block m-auto mb-3 tradingplatform__img" />
                    <h5 class="mt-3 mb-0 text-dark tradingplatform__title">Оплата по счету</h5>
                </div>
            </a>
        </div>
    </div>
    <?
}
?>