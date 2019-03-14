<?
\Local\Core\Inner\Route::fillRouteBreadcrumbs(
    'company',
    'edit',
    ['COMPANY_ID' => $arParams['COMPANY_ID']]
);

if( !empty($arResult['FIELDS']['COMPANY_NAME_SHORT']['VALUE']) )
{
    $GLOBALS['APPLICATION']->SetTitle('Редактирование компании '.$arResult['FIELDS']['COMPANY_NAME_SHORT']['VALUE']);
    $GLOBALS['APPLICATION']->SetPageProperty(
        'title',
        'Редактирование компании '.$arResult['FIELDS']['COMPANY_NAME_SHORT']['VALUE']
    );
}