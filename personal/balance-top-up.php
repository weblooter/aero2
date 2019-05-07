<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Баланс");
?>
<?
$APPLICATION->IncludeComponent('local.core:personal.balance.top-up', '.default');
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>