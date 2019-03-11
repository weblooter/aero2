<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var \PersonalSiteListComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */
?>

<a href="<?=\Local\Core\Inner\Route::getRouteTo('site', 'list', ['#COMPANY_ID#' => $arParams['COMPANY_ID']])?>" class="btn btn-dark"><ion-icon name="arrow-round-back"></ion-icon> Вернуться к сайтам</a>
<a href="<?=\Local\Core\Inner\Route::getRouteTo('site', 'list', ['#COMPANY_ID#' => $arParams['COMPANY_ID']])?>" class="btn btn-warning"><ion-icon name="add-circle-outline"></ion-icon> Добавить сайт</a>
