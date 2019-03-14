<?
if( !defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true )
{
    die();
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <? $APPLICATION->ShowHead(); ?>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    </head>
<body>
<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<? $APPLICATION->IncludeComponent(
    "bitrix:menu",
    ".default",
    array(
        "ALLOW_MULTI_SELECT"    => "N",
        "CHILD_MENU_TYPE"       => "left",
        "DELAY"                 => "N",
        "MAX_LEVEL"             => "1",
        "MENU_CACHE_GET_VARS"   => array(),
        "MENU_CACHE_TIME"       => "3600",
        "MENU_CACHE_TYPE"       => "N",
        "MENU_CACHE_USE_GROUPS" => "N",
        "MENU_THEME"            => "site",
        "ROOT_MENU_TYPE"        => "top",
        "USE_EXT"               => "N",
        "COMPONENT_TEMPLATE"    => "horizontal_multilevel"
    ),
    false
); ?>