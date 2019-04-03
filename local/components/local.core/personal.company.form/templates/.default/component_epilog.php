<?
if ($arParams['COMPANY_ID'] > 0) {
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'edit', ['COMPANY_ID' => $arParams['COMPANY_ID']]);

    if (!empty($arResult['FIELDS']['NAME']['VALUE'])) {
        $GLOBALS['APPLICATION']->SetTitle('Редактирование компании "'.$arResult['FIELDS']['NAME']['VALUE'].'"');
        $GLOBALS['APPLICATION']->SetPageProperty('title', 'Редактирование компании "'.$arResult['FIELDS']['NAME']['VALUE'].'"');
    }
} else {
    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'add');
    if (!empty($arResult['FIELDS']['COMPANY_NAME_SHORT']['VALUE'])) {
        $GLOBALS['APPLICATION']->SetTitle('Создание компании');
        $GLOBALS['APPLICATION']->SetPageProperty('title', 'Создание компании');
    }
}