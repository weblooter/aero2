<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );

$intCompanyId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('COMPANY_ID');
?>
<?
$APPLICATION->IncludeComponent(
    "local.core:personal.store.form",
    ".default",
    array(
        "COMPANY_ID" => $intCompanyId,
        "AJAX_MODE" => "Y",
        "COMPONENT_TEMPLATE" => ".default",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => ""
    ),
    false
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>