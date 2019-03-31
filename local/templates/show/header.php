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
    <?
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs('https://unpkg.com/ionicons@4.4.4/dist/ionicons.js');

    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs('https://unpkg.com/axios/dist/axios.min.js');
    ?>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <script type="text/javascript">
        axios.defaults.data = {sessid: '<?=bitrix_sessid()?>'};
    </script>
</head>
<body>
<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<img src="http://redmedusa.ru/sites/all/themes/mytheme/animations/site_dungeon_mouse.gif" class="pull-left" height="60" />
<? $APPLICATION->IncludeComponent(
    "bitrix:menu",
    ".default",
    array(
        "ALLOW_MULTI_SELECT" => "N",
        "CHILD_MENU_TYPE" => "left",
        "DELAY" => "N",
        "MAX_LEVEL" => "1",
        "MENU_CACHE_GET_VARS" => array(),
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_TYPE" => "N",
        "MENU_CACHE_USE_GROUPS" => "N",
        "MENU_THEME" => "site",
        "ROOT_MENU_TYPE" => "top",
        "USE_EXT" => "N",
        "COMPONENT_TEMPLATE" => "horizontal_multilevel"
    ),
    false
); ?>
<h4 class="pull-right mt-3 mr-3">// TODO Ваш баланс - 1 488 руб.</h4>
<div class="clearfix"></div>
<hr />
<div class="container" style="min-height: 70vh;">
    <div class="row">
						