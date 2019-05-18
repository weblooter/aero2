<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Поддержка");
?>
<?$GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.support', '.default', [])?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>