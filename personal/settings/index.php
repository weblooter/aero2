<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки");
?>
<? $GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.setting', '.default', []);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>