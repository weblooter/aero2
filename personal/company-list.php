<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Мои компании");
?>
<?
$APPLICATION->IncludeComponent(
    'local.core:personal.company.list',
    '.default',
    []
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>