<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Мои компании");

LocalRedirect(\Local\Core\Inner\Route::getRouteTo('company', 'list'))
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>