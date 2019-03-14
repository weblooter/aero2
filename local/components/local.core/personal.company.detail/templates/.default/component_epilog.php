<?
\Local\Core\Inner\Route::fillRouteBreadcrumbs(
    'company',
    'detail',
    ['COMPANY_ID' => $arParams['COMPANY_ID']]
);

if( !empty($arResult['COMPANY']['COMPANY_NAME_SHORT']) )
{
    $GLOBALS['APPLICATION']->SetTitle($arResult['COMPANY']['COMPANY_NAME_SHORT']);
    $GLOBALS['APPLICATION']->SetPageProperty(
        'title',
        $arResult['COMPANY']['COMPANY_NAME_SHORT']
    );
}