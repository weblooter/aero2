<?
if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true )
{
    die();
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        'ELEM_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => 'Кол-во сайтов на странице',
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => 10
        ],
        'COMPANY_ID' => [
            'PARENT' => 'BASE',
            'NAME' => 'ID компании',
            'TYPE' => 'STRING',
            'DEFAULT_VALUE' => 0
        ]
    )
);
?>
