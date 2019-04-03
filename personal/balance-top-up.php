<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Баланс");
?>
    <div class="col-12">
        <?
        $APPLICATION->IncludeComponent('local.core:personal.balance.top-up', '.default');
        ?>
    </div>

<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>