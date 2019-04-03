<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arAllowFieldsListValues = [];
/** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
foreach (\Local\Core\Model\Data\CompanyTable::getMap() as $obField) {
    $arAllowFieldsListValues[$obField->getColumnName()] = '['.$obField->getColumnName().'] '.$obField->getTitle();
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "AJAX_MODE" => [],
        'COMPANY_ID' => [
            'PARENT' => 'BASE',
            'NAME' => 'ID компании',
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => 0
        ]
    )
);
?>
