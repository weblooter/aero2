<?
/**
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalSupportComponent $component
 * @var CBitrixComponentTemplate  $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 * @global CMain                  $APPLICATION
 */

if( $arParams['SUPPORT_ID'] < 0 )
{
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('support', 'list');
    $GLOBALS['APPLICATION']->SetTitle('Поддержка');
    $GLOBALS['APPLICATION']->SetPageProperty('title', 'Поддержка');
}
elseif( $arParams['SUPPORT_ID'] == 0 )
{
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('support', 'list');
    $GLOBALS['APPLICATION']->AddChainItem('Новое обращение');
    $GLOBALS['APPLICATION']->SetTitle('Новое обращение');
    $GLOBALS['APPLICATION']->SetPageProperty('title', 'Новое обращение');
}
elseif( $arParams['SUPPORT_ID'] > 0 )
{
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('support', 'detail', ['SUPPORT_ID' => $arParams['SUPPORT_ID']]);
    $GLOBALS['APPLICATION']->SetTitle('Обращение #'.$arParams['SUPPORT_ID']);
    $GLOBALS['APPLICATION']->SetPageProperty('title', 'Обращение #'.$arParams['SUPPORT_ID']);
}