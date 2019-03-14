<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$intCompanyId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID');
$intSiteId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('SITE_ID');
$APPLICATION->SetTitle("Title");
?>
<?
$GLOBALS['APPLICATION']->IncludeComponent(
    'local.core:personal.site.detail',
    '.default',
    [
        'COMPANY_ID' => $intCompanyId,
        'SITE_ID'    => $intSiteId,
    ]
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>