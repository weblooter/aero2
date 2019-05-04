<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$intCompanyId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('COMPANY_ID');

$APPLICATION->SetTitle("Магазины");
$APPLICATION->SetPageProperty(
    'title',
    "Магазины"
);
?>
<?
$GLOBALS['APPLICATION']->IncludeComponent(
    'local.core:personal.store.list',
    '.default',
    [
        'COMPANY_ID' => $intCompanyId,
    ]
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>