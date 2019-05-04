<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetPageProperty("TITLE", "Конвертер");
$APPLICATION->SetTitle("Конвертер");
?>
<? $APPLICATION->IncludeComponent(
        "local.core:robofeed.convert.list",
        ".default",
        array(
        ),
        false
); ?>

    <h3 class="bold">Загрузить новый файл</h3>
    <?$APPLICATION->IncludeComponent(
        "local.core:robofeed.convert.form",
        ".default",
        array(
            "AJAX_MODE" => "Y",
            "COMPONENT_TEMPLATE" => ".default",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_ADDITIONAL" => ""
        ),
        false
    );?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>