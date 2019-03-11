<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование компании");
$APPLICATION->AddChainItem("Мои компании", \Local\Core\Inner\Route::getRouteTo('company', 'list'));

$intCompanyId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID');
?>
    <div class="container-fluid">
	<div class="row">
		<div class="col-12">
			 <?$APPLICATION->IncludeComponent(
	"local.core:personal.company.form.edit",
	"",
	Array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"ALLOW_FIELDS_LIST" => array("COMPANY_INN","COMPANY_NAME_SHORT","COMPANY_NAME_FULL","COMPANY_OGRN","COMPANY_KPP","COMPANY_OKPO","COMPANY_OKTMO","COMPANY_DIRECTOR","COMPANY_ACCOUNTANT","COMPANY_ADDRESS_ADDRESS","COMPANY_ADDRESS_OFFICE","COMPANY_ADDRESS_CITY","COMPANY_ADDRESS_AREA","COMPANY_ADDRESS_REGION","COMPANY_ADDRESS_ZIP","COMPANY_ADDRESS_COUNTRY"),
        'COMPANY_ID' => $intCompanyId
	)
);?>
		</div>
	</div>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>