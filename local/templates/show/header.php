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
        ->addJs('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs('https://unpkg.com/ionicons@4.4.4/dist/ionicons.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs('https://unpkg.com/axios/dist/axios.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/jquery-1.12.1.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/bootstrap.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/bootstrap-select.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/slick.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/ion.rangeSlider.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/jquery.sticky-kit.min.js');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addJs(SITE_TEMPLATE_PATH.'/js/script.js');

    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss('https://fonts.googleapis.com/css?family=Montserrat:400,400i,600,700,900&amp;subset=cyrillic');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/simple-line-icons.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/bootstrap.min.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/bootstrap-select.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/slick.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/ion.rangeSlider.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/github.min.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/ui.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/style.css');
    \Bitrix\Main\Page\Asset::getInstance()
        ->addCss(SITE_TEMPLATE_PATH.'/css/responsive.css');
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