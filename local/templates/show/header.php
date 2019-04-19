<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<!DOCTYPE html>
<html>
<head>
    <? $APPLICATION->ShowHead(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <?
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/popper.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/ionicons.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/axios.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery-1.12.1.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/bootstrap.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/bootstrap-select.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/slick.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/ion.rangeSlider.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery.sticky-kit.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/assets/js/script.js');

    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/fonts/montseratt.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/simple-line-icons.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap.min.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap-select.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/slick.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/ion.rangeSlider.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/github.min.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/ui.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/style.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/assets/css/responsive.css');
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

</head>

<body>
<header>
    <div class="container">
        <div class="inner">
            <a href="/" class="logo">ROBOFEED</a>
            <div class="righted">
                <div class="mobilemenu_toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "mainmenu",
                    Array(
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "left",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "1",
                        "MENU_CACHE_GET_VARS" => array(""),
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "N",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "ROOT_MENU_TYPE" => "top",
                        "USE_EXT" => "N"
                    )
                );?>
                <a href="/personal/" class="btn mini white cabinet icon-user">Личный кабинет</a>
            </div>
        </div>
    </div>
</header>
<div class="content">