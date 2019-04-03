<?
\Local\Core\Inner\Route::fillRouteBreadcrumbs('balance', 'top-up');

if (!empty($arResult['HANDLER'])) {
    $GLOBALS['APPLICATION']->SetTitle($arResult['HANDLER']::getTitle());
    $GLOBALS['APPLICATION']->SetPageProperty('title', $arResult['HANDLER']::getTitle());
    $GLOBALS['APPLICATION']->AddChainItem($arResult['HANDLER']::getTitle());
} else {
    $GLOBALS['APPLICATION']->SetTitle('Пополнить баланс');
    $GLOBALS['APPLICATION']->SetPageProperty('title', 'Пополнить баланс');
}