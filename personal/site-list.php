<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$intCompanyId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID');

$APPLICATION->SetTitle("Список сайтов");
$APPLICATION->SetPageProperty('title', "Список сайтов");
?>
<?
$GLOBALS['APPLICATION']->IncludeComponent(
    'local.core:personal.site.list',
    '.default',
    [
        'COMPANY_ID' => $intCompanyId,
        'ELEM_COUNT' => 10
    ]
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>