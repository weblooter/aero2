<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Рабочий стол");
$APPLICATION->SetPageProperty('title', "Рабочий стол");
?>

<div class="card">
    <div class="card-body">
        Раздел в процессе программирования.
    </div>
</div>

<?include $_SERVER['DOCUMENT_ROOT'].'/personal/_index_info.php';?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>