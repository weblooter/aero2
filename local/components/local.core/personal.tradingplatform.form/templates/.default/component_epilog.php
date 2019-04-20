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

if( $arParams['TP_ID'] > 0 )
{
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('tradingplatform', 'edit', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID'], 'TP_ID' => $arParams['TP_ID']]);

    $GLOBALS['APPLICATION']->setTitle('Редактирование "'.\Local\Core\Inner\TradingPlatform\Base::getName($arParams['TP_ID']).'"');
    $GLOBALS['APPLICATION']->setPageProperty('title', 'Редактирование "'.\Local\Core\Inner\TradingPlatform\Base::getName($arParams['TP_ID']).'"');
}
else
{
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('tradingplatform', 'add', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]);
}