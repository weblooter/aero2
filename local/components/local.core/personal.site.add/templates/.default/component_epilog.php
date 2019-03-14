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
    'add',
    ['COMPANY_ID' => $arParams['COMPANY_ID']]
);

$APPLICATION->SetTitle('Добавление сайта');
$APPLICATION->SetPageProperty(
    'title',
    'Добавление сайта'
);