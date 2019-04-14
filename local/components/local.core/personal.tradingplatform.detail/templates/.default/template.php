<?
/**
 * @global CMain                                $APPLICATION
 * @var array                                   $arParams
 * @var array                                   $arResult
 * @var \PersonalTradingPlatformDetailComponent $component
 * @var CBitrixComponentTemplate                $this
 * @var string                                  $templateName
 * @var string                                  $componentPath
 * @var string                                  $templateFolder
 */

?>
    <div class="pull-right">
        <a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit',
            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arResult['ITEM']['ID']])?>" title="Редактировать">
            <ion-icon name="create" role="img" class="hydrated" aria-label="create"></ion-icon>
        </a>
        <a href="javascript:void(0)" onclick="wblDeleteTradingPlatform(<?=$arResult['ITEM']['ID']?>)" title="Удалить">
            <ion-icon name="trash" role="img" class="hydrated" aria-label="trash"></ion-icon>
        </a>
    </div>

    Активность:
<?
switch ($arResult['ITEM']['ACTIVE']) {
    case 'Y':
        ?>
        <span class="badge badge-success">Активен</span>
        <?
        break;

    case 'N':
        ?>
        <span class="badge badge-danger">Деактивирован</span>
        <?
        break;
}
?>
    <br />
    Оплачен до:
<?
if ($arResult['ITEM']['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime) {
    if ($arResult['ITEM']['PAYED_TO']->getTimestamp() <= time()) {
        ?>
        <span class="badge badge-danger"><?=$arResult['ITEM']['PAYED_TO']->format('Y-m-d')?></span>
        <?
    } else {
        ?>
        <span class="badge badge-success"><?=$arResult['ITEM']['PAYED_TO']->format('Y-m-d')?></span>
        <?
    }
} else {
    ?>
    <span class="badge badge-warning">Еще ни разу не активировался</span>
    <?
}
?>
    <br />
    Дата создания: <?=$arResult['ITEM']['DATE_CREATE']->format('Y-m-d')?><br />


<script type="text/javascript">
    function wblDeleteTradingPlatform(intId) {
        if (confirm('Удалить?')) {
            axios.post('/ajax/trading-platform/delete/' + intId + '/')
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