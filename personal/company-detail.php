<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Мои компании");

$intCompanyId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('COMPANY_ID');
?>
<?
$APPLICATION->IncludeComponent(
    'local.core:personal.company.detail',
    '.default',
    [
        'ELEM_COUNT' => 1,
        'COMPANY_ID' => $intCompanyId
    ]
);
?>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>