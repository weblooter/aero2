<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetPageProperty("TITLE", "Конвертер");
$APPLICATION->SetTitle("Конвертер");
?>
<div class="col-12">
    <? $APPLICATION->IncludeComponent(
        "local.core:robofeed.convert.list",
        ".default",
        array(
        ),
        false
    ); ?>
</div>
<div class="col-12">
    <h4>Загрузить новый файл</h4>
    <? $APPLICATION->IncludeComponent(
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
    ); ?>
</div>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>