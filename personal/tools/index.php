<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetPageProperty("TITLE", "Инструменты");
$APPLICATION->SetTitle("Инструменты");
?>
<div class="card">
    <div class="card-body">
        <a href="<?=\Local\Core\Inner\Route::getRouteTo('tools', 'converter')?>">Конвертер</a><br/>
    </div>
</div>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>