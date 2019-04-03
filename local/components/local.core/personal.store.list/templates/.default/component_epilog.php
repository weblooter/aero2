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

\Local\Core\Inner\Route::fillRouteBreadcrumbs('store', 'list', ['COMPANY_ID' => $arParams['COMPANY_ID']]);