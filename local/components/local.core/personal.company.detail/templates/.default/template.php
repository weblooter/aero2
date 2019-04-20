<?
/**
 * @global CMain                        $APPLICATION
 * @var array                           $arParams
 * @var array                           $arResult
 * @var \PersonalCompanyDetailComponent $component
 * @var CBitrixComponentTemplate        $this
 * @var string                          $templateName
 * @var string                          $componentPath
 * @var string                          $templateFolder
 */
?>

<div class="col-xs-12 mb-3">
    <?
    $strAlertClass = '';
    $strIcon = '';
    switch ($arResult['COMPANY']['VERIFIED']) {
        case 'Y':
            $strAlertClass = 'alert-success';
            $strIcon = '<ion-icon name="done-all"></ion-icon>';
            break;
        case 'N':
            $strAlertClass = 'alert-dark';
            $strIcon = '<ion-icon name="hourglass"></ion-icon>';
            break;
        case 'E':
            $strAlertClass = 'alert-danger';
            $strIcon = '<ion-icon name="warning"></ion-icon>';
            break;
    }
    ?>
    <div class="alert <?=$strAlertClass?>">
        <div class="pull-right">
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'edit', ['#COMPANY_ID#' => $arResult['COMPANY']['ID']])?>" title="Редактировать">
                <ion-icon name="create"></ion-icon>
            </a>
            <a href="javascript:void(0)" onclick="wblDeleteCompany(<?=$arResult['COMPANY']['ID']?>)" title="Удалить">
                <ion-icon name="trash"></ion-icon>
            </a>
        </div>
        <?=$strIcon?>
        <?
        switch ($arResult['COMPANY']['TYPE']) {
            case 'FI':
                ?>
                <b><?=$arResult['COMPANY']['NAME']?></b>
                <?
                break;
            case 'UR':
                ?>
                <b><?=$arResult['COMPANY']['NAME']?></b><br />
                Сокращеное название огранизации: <?=$arResult['COMPANY']['COMPANY_NAME_SHORT']?><br />
                ИНН: <?=$arResult['COMPANY']['COMPANY_INN']?>
                <?
                break;
        }
        ?>
        <br />
        Дата создания: <?=date('Y.m.d H:i:s', $arResult['COMPANY']['DATE_CREATE']->getTimestamp())?><br />
        Активность: <?=$arResult['COMPANY']['ACTIVE'] == 'Y' ? 'Активна' : 'Деактивирована'?><br />
        Верификация:
        <?
        switch ($arResult['COMPANY']['VERIFIED']) {
            case 'Y':
                ?>
                Компания успешно верифицированна!
                <?
                break;
            case 'N':
                ?>
                Компания еще не проходила проверку.
                <?
                break;
            case 'E':
                ?>
                Во время проверки компании возникли проблемы: <?=$arResult['COMPANY']['VERIFIED_NOTE']?><br />
                До тех пор, пока ошибки не будут исправлены, компания и ее магазины не будут работать.
                <?
                break;
        }
        ?>
    </div>

</div>

<div class="col-xs-6 mb-3">
    <b>Магазины компании</b>
    <? if (!empty($arResult['COMPANY']['STORES'])): ?>
        <ul class="list-group mb-3">
            <? foreach ($arResult['COMPANY']['STORES'] as $arStore): ?>
                <li class="list-group-item <?=($arStore['ACTIVE'] == 'Y' ? 'list-group-item-success' : 'list-group-item-dark')?>">
                    <ion-icon name="<?=$arStore['ACTIVE'] == 'Y' ? 'done-all' : 'close'?>" title="<?=$arStore['ACTIVE'] == 'Y' ? 'Активе' : 'Деактивирован'?>"></ion-icon>
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $arResult['COMPANY']['ID'], '#STORE_ID#' => $arStore['ID']])?>">
                        <?=$arStore['NAME'].((!empty($arStore['DOMAIN']) && $arStore['DOMAIN'] != $arStore['NAME']) ? ' ('.$arStore['DOMAIN'].')' : '')?>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>
        <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'list', ['#COMPANY_ID#' => $arResult['COMPANY']['ID']])?>" class="btn btn-warning">
            <ion-icon name="reorder"></ion-icon>
            Все магазины</a>
    <? endif; ?>
    <br />
    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'add', ['#COMPANY_ID#' => $arResult['COMPANY']['ID']])?>" class="btn btn-warning">
        <ion-icon name="add-circle-outline"></ion-icon>
        Добавить магазин</a>
</div>
<div class="col-xs-6 mb-3">

    <div class="alert alert-warning" role="alert">
        // TODO нотификации, типа новый инвойс или сайт прошел проверку
    </div>
</div>

<script type="text/javascript">
    function wblDeleteCompany(intId) {
        if (confirm('Удалить?')) {
            axios.post('/ajax/company/delete/' + intId + '/')
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