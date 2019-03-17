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
    'detail',
    ['COMPANY_ID' => $arParams['COMPANY_ID'], 'SITE_ID' => $arParams['SITE_ID']]
);

$APPLICATION->SetTitle(\Local\Core\Inner\Site\Base::getSiteName($arParams['SITE_ID']));
$APPLICATION->SetPageProperty(
    'title',
    \Local\Core\Inner\Site\Base::getSiteName($arParams['SITE_ID'])
);