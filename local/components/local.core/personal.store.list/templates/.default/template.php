<?
/**
 * @global CMain                    $APPLICATION
 * @var array                       $arParams
 * @var array                       $arResult
 * @var \PersonalStoreListComponent $component
 * @var CBitrixComponentTemplate    $this
 * @var string                      $templateName
 * @var string                      $componentPath
 * @var string                      $templateFolder
 */
?>
<div class="col-xs-12">

    <? if (empty($arResult['ITEMS'])): ?>
        <p>К компании не привязан ни один сайт</p>
    <? else: ?>
        <? foreach ($arResult['ITEMS'] as $arItem): ?>

            <div class="media">
                <div class="media-body">
                    <div class="pull-right">
                        <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'edit', [
                            '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                            '#STORE_ID#' => $arItem['ID']
                        ])?>" title="Редактировать">
                            <ion-icon name="create"></ion-icon>
                        </a>
                        <a href="javascript:void(0)" onclick="wblDeleteStore(<?=$arItem['ID']?>)" title="Удалить">
                            <ion-icon name="trash"></ion-icon>
                        </a>
                    </div>
                    <h5 class="mt-0">
                        <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail', [
                            '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                            '#STORE_ID#' => $arItem['ID']
                        ])?>">[<?=$arItem['ID']?>] <?=$arItem['NAME']?></a>
                    </h5>
                    Активность: <?=($arItem['ACTIVE'] == 'Y') ? 'Активен' : 'Деактивирован'?><br />
                    Дата создания: <?=date('Y-m-d H:i:s', $arItem['DATE_CREATE']->getTimestamp())?><br />
                    Источник
                    данных: <?=($arItem['RESOURCE_TYPE'] == 'LINK') ? 'Ссылка на файл' : 'Загружен файл'?>
                </div>
            </div>
            <hr />

        <? endforeach; ?>
    <? endif; ?>

    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'add', ['#COMPANY_ID#' => $arParams['COMPANY_ID']])?>" class="btn btn-warning">+
        Добавить магазин</a>

    <?
    $APPLICATION->IncludeComponent("bitrix:main.pagenavigation", "", array(
        "NAV_OBJECT" => $arResult['NAV_OBJ'],
        "SEF_MODE" => "N", // ЧПУ пагинация или нет, Y|N
        "SHOW_COUNT" => "N",
    ), false);
    ?>

</div>

<script type="text/javascript">
    function wblDeleteStore(intId) {
        if (confirm('Удалить?')) {
            axios.post('/ajax/store/delete/' + intId + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {
                        alert('OK!');
                    } else {
                        alert(response.data['error_text'])
                    }
                })
        }
    }
</script>