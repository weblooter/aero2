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
    /* *** */
    /* CSS */
    /* *** */
    $obAsset = \Bitrix\Main\Page\Asset::getInstance();
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/personal.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/fonts/montseratt.css');

    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/animate.css/animate.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery.scrollbar/jquery.scrollbar.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/sweetalert2/dist/sweetalert2.min.css');

    /* **** */
    /* FORM */
    /* **** */
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/select2/dist/css/select2.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/dropzone/dist/dropzone.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/flatpickr/dist/flatpickr.min.css" ');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/nouislider/distribute/nouislider.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/trumbowyg/dist/ui/trumbowyg.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/rateYo/min/jquery.rateyo.min.css');
    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/bower_components/fileinput/fileinput.min.css');

    $obAsset->addCss(SITE_TEMPLATE_PATH.'/assets/css/app.min.css');

    /* ** */
    /* JS */
    /* ** */
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/localcore.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/axios.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/qs.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery/dist/jquery.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/popper.js/dist/umd/popper.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/bootstrap/dist/js/bootstrap.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery.scrollbar/jquery.scrollbar.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery-scrollLock/jquery-scrollLock.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/sweetalert2/dist/sweetalert2.min.js');

    /* **** */
    /* FORM */
    /* **** */
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/fileinput/fileinput.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery-mask-plugin/dist/jquery.mask.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/select2/dist/js/select2.full.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/select2/dist/js/ru.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/dropzone/dist/min/dropzone.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/moment/min/moment.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/flatpickr/dist/flatpickr.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/nouislider/distribute/nouislider.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/trumbowyg/dist/trumbowyg.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/rateYo/min/jquery.rateyo.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery-text-counter/textcounter.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/autosize/dist/autosize.min.js');

    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/js/app.min.js');
    ?>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/x-icon" href=​"/favicon.ico" />
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
    <script type="text/javascript">
        LocalCore.setRecaptchaSiteKey('<?=\Bitrix\Main\Config\Configuration::getInstance()->get('recaptcha')['site_key']?>');
        axios.defaults.data = {sessid: '<?=bitrix_sessid()?>'};
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['Content-Type'] = 'application/json';
        var qs = Qs;
    </script>
</head>
<body data-sa-theme>
<?if( $GLOBALS['USER']->IsAdmin() ):?>
    <div id="panel">
        <? $APPLICATION->ShowPanel(); ?>
    </div>
<?endif;?>
<?if( !defined('ERROR_404') && $GLOBALS['USER']->IsAuthorized() ):?>
    <main class="main">

    <header class="header">
        <div class="navigation-trigger hidden-xl-up" data-sa-action="aside-open" data-sa-target=".sidebar">
            <i class="zmdi zmdi-menu"></i>
        </div>

        <div class="logo hidden-sm-down">
            <a href="/personal/">ROBOFEED</a>
        </div>

        <!--
        <form class="search">
            <div class="search__inner">
                <input type="text" class="search__text" placeholder="Поиск пр справочной документации">
                <i class="zmdi zmdi-search search__helper" data-sa-action="search-close"></i>
            </div>
        </form>
        -->

        <ul class="top-nav">

            <li class="dropdown">
                <?$GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.support.notification', '.default', []);?>
            </li>

            <li class="dropdown hidden-xs-down">
                <a href="javascript:void(0)" data-toggle="dropdown"><i class="zmdi zmdi-apps"></i></a>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu--block" role="menu">
                    <?$APPLICATION->IncludeComponent('local.core:personal.asidemenu', 'header-menu', []);?>
                </div>
            </li>
        </ul>

        <div class="clock dropdown">
            <a href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="time text-secondary">
                <i class="zmdi zmdi-balance-wallet zmdi-hc-fw"></i> <?=number_format(\Local\Core\Inner\Balance\Base::getUserBalance($GLOBALS['USER']->GetId()), 0, '.', ' ');?> руб.
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu--icon">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => '']);?>" class="dropdown-item"><i class="zmdi zmdi-plus"></i> Пополнить баланс</a>
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('balance', 'list');?>" class="dropdown-item"><i class="zmdi zmdi-receipt"></i> Посмотреть историю</a>
            </div>
        </div>
    </header>

    <section class="content">
    <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", Array(
        "PATH" => "",
        "SITE_ID" => "s1",
        "START_FROM" => 1
    )); ?>

    <header class="content__title">
        <h1><?$APPLICATION->ShowTitle(false)?></h1>
    </header>
<?endif;?>