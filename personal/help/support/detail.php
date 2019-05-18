<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Поддержка");
?>
<?$GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.support', '.default', [
    'SUPPORT_ID' => \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('SUPPORT_ID')
])?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>