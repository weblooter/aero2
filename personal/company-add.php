<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
?>
<? $APPLICATION->IncludeComponent(
    "local.core:personal.company.form",
    "",
    Array(
        "AJAX_MODE" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
    )
); ?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>