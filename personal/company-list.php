<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мои компании");
$APPLICATION->AddChainItem("Мои компании", \Local\Core\Inner\Route::getRouteTo('company', 'list'));
?>
<?
$APPLICATION->IncludeComponent('local.core:personal.company.list', '.default', ['ELEM_COUNT' => 1]);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>