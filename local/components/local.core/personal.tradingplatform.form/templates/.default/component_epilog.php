<?
/**
 * @global CMain                   $APPLICATION
 * @var array                      $arParams
 * @var array                      $arResult
 * @var \PersonalTradingPlatformFormComponent $component
 * @var CBitrixComponentTemplate   $this
 * @var string                     $templateName
 * @var string                     $componentPath
 * @var string                     $templateFolder
 */

\Local\Core\Inner\Route::fillRouteBreadcrumbs('tradingplatform', 'add', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]);