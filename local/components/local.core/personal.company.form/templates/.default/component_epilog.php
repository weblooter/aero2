<?
if ($arParams['COMPANY_ID'] > 0) {
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'edit', ['COMPANY_ID' => $arParams['COMPANY_ID']]);

    $GLOBALS['APPLICATION']->SetTitle('Редактирование компании');
    $GLOBALS['APPLICATION']->SetPageProperty('title', 'Редактирование компании');
} else {
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'add');
    $GLOBALS['APPLICATION']->SetTitle('Добавить компанию');
    $GLOBALS['APPLICATION']->SetPageProperty('title', 'Добавить компанию');
}