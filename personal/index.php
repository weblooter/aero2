<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Рабочий стол");
$APPLICATION->AddChainItem('Рабочий стол');
?>
    <div class="col-12">

        <a href="<?=\Local\Core\Inner\Route::getRouteTo(
            'company',
            'add'
        )?>" class="btn btn-warning">+ Добавить компанию</a>

    </div>

<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>