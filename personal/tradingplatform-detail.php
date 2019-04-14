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

$APPLICATION->SetTitle("Торговая площадка");
$APPLICATION->SetPageProperty(
    'title',
    "Торговая площадка"
);
?>
<div class="col-12">
    <?
    $GLOBALS['APPLICATION']->IncludeComponent(
        'local.core:personal.tradingplatform.detail',
        '.default',
        [
            'COMPANY_ID' => $intCompanyId,
            'STORE_ID' => $intStoreId,
            'TP_ID' => $intTradingPlatformId
        ]
    );
    ?>
</div>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>