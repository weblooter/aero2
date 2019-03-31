<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
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
            </div>
        </div>
    </div>
    <br><? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>