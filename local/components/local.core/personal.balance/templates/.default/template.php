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

<h5>Текущий баланс</h5>
<?=number_format(\Local\Core\Inner\Balance\Base::getUserBalance($GLOBALS['USER']->GetId()), 0, '.', ' ')?> руб.<br/>

<div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Пополнить баланс
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
        <a class="dropdown-item" href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => 'yandex-money'])?>">Оплата картой (комиссия 3%)</a>
        <a class="dropdown-item" href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => 'bill'])?>">Оплата по счету</a>
    </div>
</div>
<br/>
<h5>Ближайшие операции по списанию</h5>
// TODO<br/>
<ul class="list-group">
    <li class="list-group-item">2019-03-02, Яндекс Маркет, ЛИД, 1 700 руб.</li>
    <li class="list-group-item">2019-03-02, Яндекс Маркет, ЛИД, 1 700 руб.</li>
    <li class="list-group-item">2019-03-02, Яндекс Маркет, ЛИД, 1 700 руб.</li>
    <li class="list-group-item">2019-03-02, Яндекс Маркет, ЛИД, 1 700 руб.</li>
    <li class="list-group-item">2019-03-02, Яндекс Маркет, ЛИД, 1 700 руб.</li>
</ul>
<br/>
<h5>Последние операции по балансу</h5>
<table class="table">
    <thead>
    <tr>
        <th>Дата</th>
        <th>Операция</th>
        <th>Описание</th>
    </tr>
    </thead>
    <tbody>
    <?foreach ($arResult['BALANCE_LOG'] as $arLog):?>
    <tr class="table-<?=$arLog['OPERATION'] > 0 ? 'success' : 'danger'?>">
        <td><?=$arLog['DATE_CREATE']->format('Y-m-d H:i:s')?></td>
        <td><?=$arLog['OPERATION']?></td>
        <td><?=$arLog['NOTE']?></td>
    </tr>
    <?endforeach;?>
    </tbody>
</table>