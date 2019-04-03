<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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
        ->addJs('https://code.jquery.com/jquery-3.3.1.slim.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js');
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
<? $APPLICATION->IncludeComponent("bitrix:menu", ".default", array(
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
    ), false); ?>
<h4 class="pull-right mt-3 mr-3">Баланс - <?=number_format(\Local\Core\Inner\Balance\Base::getUserBalance($GLOBALS['USER']->GetId()), 0, '.', ' ')?> руб.</h4>
<div class="clearfix"></div>
<hr />
<div class="container" style="min-height: 70vh;">
    <div class="row">
        <? $APPLICATION->IncludeComponent("bitrix:menu", "tabs", array(
                "ALLOW_MULTI_SELECT" => "N",
                "CHILD_MENU_TYPE" => "left",
                "DELAY" => "N",
                "MAX_LEVEL" => "1",
                "MENU_CACHE_GET_VARS" => array(),
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_TYPE" => "N",
                "MENU_CACHE_USE_GROUPS" => "N",
                "MENU_THEME" => "site",
                "ROOT_MENU_TYPE" => "personal",
                "USE_EXT" => "N",
            ), false); ?>
        <div class="col-12">
            <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "universal", Array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0"
                )); ?>
            <h1><? $APPLICATION->ShowTitle(false) ?></h1>
        </div>
