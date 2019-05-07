<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$intCompanyId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('COMPANY_ID');
$intStoreId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('STORE_ID');

$APPLICATION->SetTitle("Добавить торговую площадку");
$APPLICATION->SetPageProperty(
    'title',
    "Добавить торговую площадку"
);
?>
<?
$GLOBALS['APPLICATION']->IncludeComponent(
    'local.core:personal.tradingplatform.form',
    '.default',
    [
        'COMPANY_ID' => $intCompanyId,
        'STORE_ID' => $intStoreId
    ]
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>