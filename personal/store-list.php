<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$intCompanyId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('COMPANY_ID');

$APPLICATION->SetTitle("Список магазинов");
$APPLICATION->SetPageProperty(
    'title',
    "Список магазинов"
);
?>
<?
$GLOBALS['APPLICATION']->IncludeComponent(
    'local.core:personal.store.list',
    '.default',
    [
        'COMPANY_ID' => $intCompanyId,
        'ELEM_COUNT' => 1
    ]
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>