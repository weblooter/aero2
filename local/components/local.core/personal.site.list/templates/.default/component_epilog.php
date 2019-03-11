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

$APPLICATION->AddChainItem("Мои компании", \Local\Core\Inner\Route::getRouteTo('company', 'list') );
$APPLICATION->AddChainItem(\Local\Core\Inner\Company\Base::getCompanyName( $arParams['COMPANY_ID'] ), \Local\Core\Inner\Route::getRouteTo('company', 'detail', ['#COMPANY_ID#' => $arParams['COMPANY_ID']] ) );
$APPLICATION->AddChainItem("Список сайтов", \Local\Core\Inner\Route::getRouteTo('site', 'list', ['#COMPANY_ID#' => $arParams['COMPANY_ID']] ) );