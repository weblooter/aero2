<?
/**
 * @global CMain                                $APPLICATION
 * @var array                                   $arParams
 * @var array                                   $arResult
 * @var \PersonalTradingPlatformDetailComponent $component
 * @var CBitrixComponentTemplate                $this
 * @var string                                  $templateName
 * @var string                                  $componentPath
 * @var string                                  $templateFolder
 */

\Local\Core\Inner\Route::fillRouteBreadcrumbs('tradingplatform', 'detail', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID'], 'TP_ID' => $arParams['TP_ID']]);

$GLOBALS['APPLICATION']->SetTitle($arResult['ITEM']['NAME']);
$GLOBALS['APPLICATION']->SetPageProperty('title', $arResult['ITEM']['NAME']);