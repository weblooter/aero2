<?/** @var array $arResult */?>

<?
$strAlertClass = '';
$strIcon = '';
switch ($arResult['COMPANY']['VERIFIED'])
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
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'edit', [ '#COMPANY_ID#' => $arResult['COMPANY']['ID'] ])?>" title="Редактировать"><ion-icon name="create"></ion-icon></a>
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'delete', [ '#COMPANY_ID#' => $arResult['COMPANY']['ID'] ])?>" title="Удалить"><ion-icon name="trash"></ion-icon></a>
        </div>
        <?=$strIcon?> <b>[<?=$arResult['COMPANY']['ID']?>] <?=$arResult['COMPANY']['COMPANY_NAME_SHORT']?> (ИНН: <?=$arResult['COMPANY']['COMPANY_INN']?>)</b><br/>
        Дата создания: <?=date( 'Y.m.d H:i:s', $arResult['COMPANY']['DATE_CREATE']->getTimestamp() )?><br/>
        Активность: <?=$arResult['COMPANY']['ACTIVE'] == 'Y' ? 'Активна' : 'Деактивирована'?><br/>
        Верификация:
        <?
        switch ($arResult['COMPANY']['VERIFIED'])
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
                Во время проверки компании возникли проблемы: <?=$arResult['COMPANY']['VERIFIED_NOTE']?><br/>
                До тех пор, пока ошибки не будут исправлены, компания и ее сайты не будут работать.
                <?
                break;
        }
        ?>
    </div>