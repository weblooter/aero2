<?
/**
 * @global CMain                  $APPLICATION
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalBalanceComponent $component
 * @var CBitrixComponentTemplate  $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 */
?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-2">Текущий баланс</h4>
        <h2 class="mb-4 text-warning"><?=number_format(\Local\Core\Inner\Balance\Base::getUserBalance($GLOBALS['USER']->GetId()), 0, '.', ' ')?> руб.</h2>

        <h4 class="card-title">Пополнить баланс</h4>
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

        <h4 class="card-title">Последние операции по балансу</h4>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Операция</th>
                <th>Описание</th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($arResult['BALANCE_LOG'] as $arLog): ?>
                <tr>
                    <td>
                        <nobr><?=( ( $arLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime ) ? $arLog['DATE_CREATE']->format('Y-m-d H:i:s') : '-' )?></nobr>
                    </td>
                    <td><span class="badge badge-<?=$arLog['OPERATION'] > 0 ? 'success' : 'danger'?>"><?=$arLog['OPERATION']?></span></td>
                    <td><?=$arLog['NOTE']?></td>
                </tr>
            <? endforeach; ?>
            </tbody>
        </table>

    </div>
</div>