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
        'STORE_ID' => [
            'PARENT' => 'BASE',
            'NAME' => 'ID магазина',
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => 0
        ],
        'AJAX_MODE' => []
    )
);
?>
