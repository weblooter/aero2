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

<div class="col-12 mb-3">
    <?
    $strAlertClass = '';
    $strIcon = '';
    switch( $arResult['COMPANY']['VERIFIED'] )
    {
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
            <a href="<?=\Local\Core\Inner\Route::getRouteTo(
                'company',
                'edit',
                ['#COMPANY_ID#' => $arResult['COMPANY']['ID']]
            )?>" title="Редактировать">
                <ion-icon name="create"></ion-icon>
            </a>
            <a href="#" title="Удалить">
                <ion-icon name="trash"></ion-icon>
            </a>
        </div>
        <?=$strIcon?> <b>[<?=$arResult['COMPANY']['ID']?>] <?=$arResult['COMPANY']['COMPANY_NAME_SHORT']?>
            (ИНН: <?=$arResult['COMPANY']['COMPANY_INN']?>)</b><br />
        Дата создания: <?=date(
            'Y.m.d H:i:s',
            $arResult['COMPANY']['DATE_CREATE']->getTimestamp()
        )?><br />
        Активность: <?=$arResult['COMPANY']['ACTIVE'] == 'Y' ? 'Активна' : 'Деактивирована'?><br />
        Верификация:
        <?
        switch( $arResult['COMPANY']['VERIFIED'] )
        {
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

<div class="col-6 mb-3">
    // TODO Магазины компании
    <ul class="list-group mb-3">
        <li class="list-group-item"><a href="#">https://example.com</a></li>
        <li class="list-group-item"><a href="#">https://example.com</a></li>
        <li class="list-group-item"><a href="#">https://example.com</a></li>
        <li class="list-group-item"><a href="#">https://example.com</a></li>
    </ul>
    <a href="<?=\Local\Core\Inner\Route::getRouteTo(
        'store',
        'list',
        ['#COMPANY_ID#' => $arResult['COMPANY']['ID']]
    )?>" class="btn btn-warning">
        <ion-icon name="reorder"></ion-icon>
        Магазины компании</a>
    <a href="<?=\Local\Core\Inner\Route::getRouteTo(
        'store',
        'add',
        ['#COMPANY_ID#' => $arResult['COMPANY']['ID']]
    )?>" class="btn btn-warning">
        <ion-icon name="add-circle-outline"></ion-icon>
        Добавить магазин</a>
</div>
<div class="col-6 mb-3">

    <div class="alert alert-warning" role="alert">
        // TODO нотификации, типа новый инвойс или сайт прошел проверку
    </div>
</div>

<div class="col-6 mb-3">
    // TODO Счета компании
    <ul class="list-group mb-3">
        <li class="list-group-item"><a href="#">№123456_1 от 2019.01.29</a></li>
        <li class="list-group-item"><a href="#">№123456_1 от 2019.01.29</a></li>
        <li class="list-group-item"><a href="#">№123456_1 от 2019.01.29</a></li>
        <li class="list-group-item"><a href="#">№123456_1 от 2019.01.29</a></li>
    </ul>
    <a href="<?=\Local\Core\Inner\Route::getRouteTo(
        'bill',
        'list',
        ['#COMPANY_ID#' => $arResult['COMPANY']['ID']]
    )?>" class="btn btn-warning">
        <ion-icon name="wallet"></ion-icon>
        Счета компании</a>
</div>
