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
$intTradingPlatformId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('TP_ID');

$APPLICATION->SetTitle("Редактирование торговой площадки");
$APPLICATION->SetPageProperty(
    'title',
    "Редактирование торговой площадки"
);
?>
<?
$GLOBALS['APPLICATION']->IncludeComponent(
    'local.core:personal.tradingplatform.form',
    '.default',
    [
        'COMPANY_ID' => $intCompanyId,
        'STORE_ID' => $intStoreId,
        'TP_ID' => $intTradingPlatformId
    ]
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>