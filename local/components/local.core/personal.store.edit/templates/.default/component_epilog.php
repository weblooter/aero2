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

\Local\Core\Inner\Route::fillRouteBreadcrumbs(
    'store',
    'edit',
    ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]
);

$APPLICATION->SetTitle('Редактирование магазина');
$APPLICATION->SetPageProperty(
    'title',
    'Редактирование магазина'
);