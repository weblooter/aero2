<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $USER;
?>
<!DOCTYPE html>
<html>
<head>
    <? $APPLICATION->ShowHead(); ?>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <?
    $obAsset = \Bitrix\Main\Page\Asset::getInstance();

    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap-4-connect.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/ui.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/simple-line-icons.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/fonts/montseratt.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/custom.css');

    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery-1.12.1.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/bootstrap.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/popper.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/ionicons.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/axios.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/qs.js');

    // Временная необходимость
    $obAsset->addJs('https://unpkg.com/ionicons@4.5.5/dist/ionicons.js');
    ?>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <script type="text/javascript">
        axios.defaults.data = {sessid: '<?=bitrix_sessid()?>'};
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['Content-Type'] = 'application/json';
        var qs = Qs;
    </script>
</head>
<body class="inner">
<div id="panel">
    <? $APPLICATION->ShowPanel(); ?>
</div>
<?if($USER->isAuthorized()){?>
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
<?}?>