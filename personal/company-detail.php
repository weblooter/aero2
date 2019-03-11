<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Мои компании");
$APPLICATION->AddChainItem("Мои компании", \Local\Core\Inner\Route::getRouteTo('company', 'list'));

$intCompanyId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID');
?>
    <div class="col-12">

        <?
        $APPLICATION->IncludeComponent('local.core:personal.company.detail', '.default', [
            'ELEM_COUNT' => 1,
            'COMPANY_ID' => $intCompanyId
        ]);
        ?>

    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>