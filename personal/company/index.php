<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мои компании");
?>
    <div class="col-12">

        <?$APPLICATION->IncludeComponent('local.core:personal.company.list', '.default', ['ELEM_COUNT' => 1])?>

    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>