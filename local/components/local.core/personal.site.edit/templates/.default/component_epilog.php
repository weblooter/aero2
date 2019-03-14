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
    'site',
    'edit',
    ['COMPANY_ID' => $arParams['COMPANY_ID'], 'SITE_ID' => $arParams['SITE_ID']]
);

$APPLICATION->SetTitle('Редактирование сайта');
$APPLICATION->SetPageProperty(
    'title',
    'Редактирование сайта'
);