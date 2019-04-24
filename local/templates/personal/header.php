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

//    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap-4-connect.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/bootstrap-select.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/ui.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/simple-line-icons.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/fonts/montseratt.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/personal.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/custom.css');

    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery-1.12.1.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/axios.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/bootstrap.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/popper.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/ionicons.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/bootstrap-select.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/qs.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/personal.js');

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
    <header>
        <div class="container">
            <a href="/" class="logo">ROBOFEED</a>
            <div class="rightblock">
                <div class="companies">
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('company','list');?>" class="companieslist" title="Список компаний"><i class="icon-list"></i><span>Черешнев Е.С.</span></a>
                </div>
                <div class="balance">
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('balance','list');?>" title="Биллинг"><i class="icon-wallet"></i><?=number_format(\Local\Core\Inner\Balance\Base::getUserBalance($GLOBALS['USER']->GetId()), 0, '.', ' ')?> Р</a>
                    <a href="#" class="addbalance" title="Пополнить баланс"><i class="icon-plus"></i></a>
                </div>
                <div class="settings">
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('settings','list');?>" title="Настройки"><i class="icon-settings"></i></a>
                </div>
            </div>
        </div>
    </header>
    <div class="breadcrumbs">
        <div class="container">
            <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "universal", Array(
                "PATH" => "",
                "SITE_ID" => "s1",
                "START_FROM" => "0"
            )); ?>
            <ul class="menu">
                <li><a href="/personal/"><i class="icon-screen-desktop"></i><span>Рабочий стол</span></a></li>
                <li><a href="<?=\Local\Core\Inner\Route::getRouteTo('development','convert');?>"><i class="icon-magic-wand"></i><span>Инструменты</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="min-height: 70vh;">
        <div class="row">
            <div class="col-12">

                <h1><? $APPLICATION->ShowTitle(false) ?></h1>
            </div>
<?}?>