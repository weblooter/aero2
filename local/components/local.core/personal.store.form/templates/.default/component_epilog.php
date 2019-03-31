<?
/**
 * @global CMain                   $APPLICATION
 * @var array                      $arParams
 * @var array                      $arResult
 * @var \PersonalStoreFormComponent $component
 * @var CBitrixComponentTemplate   $this
 * @var string                     $templateName
 * @var string                     $componentPath
 * @var string                     $templateFolder
 */

if( $arParams['STORE_ID'] > 0 )
{

    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
        'store',
        'edit',
        ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]
    );

    $APPLICATION->SetTitle('Редактирование магазина');
    $APPLICATION->SetPageProperty(
        'title',
        'Редактирование магазина'
    );
}
else
{
    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
        'store',
        'add',
        ['COMPANY_ID' => $arParams['COMPANY_ID']]
    );

    $APPLICATION->SetTitle('Создание магазина');
    $APPLICATION->SetPageProperty(
        'title',
        'Создание магазина'
    );
}