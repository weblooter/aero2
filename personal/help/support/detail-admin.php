<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Поддержка");
$intSupportId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('SUPPORT_ID');
?>
<?$GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.support.admin', '.default', [
    'SUPPORT_ID' => ( $intSupportId >= -1 ) ? $intSupportId : null
])?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>