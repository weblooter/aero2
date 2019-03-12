<?
/**
 * @global CMain                   $APPLICATION
 * @var array                      $arParams
 * @var array                      $arResult
 * @var \PersonalSiteListComponent $component
 * @var CBitrixComponentTemplate   $this
 * @var string                     $templateName
 * @var string                     $componentPath
 * @var string                     $templateFolder
 */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <? if ( empty( $arResult[ 'ITEMS' ] ) ): ?>
                <p>К компании не привязан ни один сайт</p>
            <? else: ?>
                // TODO выводить список сайтов
            <? endif; ?>
            <a href="<?= \Local\Core\Inner\Route::getRouteTo( 'site', 'add',
                ['#COMPANY_ID#' => $arParams[ 'COMPANY_ID' ]] ) ?>" class="btn btn-warning">+ Добавить сайт</a>

        </div>
    </div>
</div>
