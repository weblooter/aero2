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

\Local\Core\Inner\Route::fillRouteBreadcrumbs('store', 'detail', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]);

$APPLICATION->SetTitle(\Local\Core\Inner\Store\Base::getStoreName($arParams['STORE_ID']));
$APPLICATION->SetPageProperty('title', \Local\Core\Inner\Store\Base::getStoreName($arParams['STORE_ID']));