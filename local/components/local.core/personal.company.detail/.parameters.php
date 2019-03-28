<?
if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true )
{
    die();
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        'COMPANY_ID' => [
            'PARENT' => 'BASE',
            'NAME' => 'ID компании',
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => 0
        ],
        'STORES_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => 'Кол-во магазинов компании на странице',
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => 5
        ]
    )
);
?>
