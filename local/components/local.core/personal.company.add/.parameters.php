<?
if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true )
{
    die();
}

$arAllowFieldsListValues = [];
/** @var \Bitrix\Main\ORM\Fields\ScalarField $obField */
foreach( \Local\Core\Model\Data\CompanyTable::getMap() as $obField )
{
    $arAllowFieldsListValues[$obField->getColumnName()] = '['.$obField->getColumnName().'] '.$obField->getTitle();
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "AJAX_MODE"         => [],
        'ALLOW_FIELDS_LIST' => [
            'PARENT'   => 'BASE',
            'NAME'     => 'Список выводимых параметров',
            'TYPE'     => 'LIST',
            'SIZE'     => 10,
            'MULTIPLE' => 'Y',
            'VALUES'   => $arAllowFieldsListValues
        ]
    )
);
?>
