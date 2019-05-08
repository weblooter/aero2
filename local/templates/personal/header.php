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
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/fileinput/fileinput.min.js');

    /* **** */
    /* FORM */
    /* **** */
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/jquery-mask-plugin/dist/jquery.mask.min.js');
    $obAsset->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/select2/dist/js/select2.full.min.js');
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

    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
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

        <form class="search">
            <div class="search__inner">
                <input type="text" class="search__text" placeholder="Search for people, files, documents...">
                <i class="zmdi zmdi-search search__helper" data-sa-action="search-close"></i>
            </div>
        </form>

        <ul class="top-nav">
            <li class="hidden-xl-up"><a href="empty.html#" data-sa-action="search-open"><i class="zmdi zmdi-search"></i></a></li>

            <li class="dropdown">
                <a href="empty.html#" data-toggle="dropdown" class="top-nav__notify"><i class="zmdi zmdi-email"></i></a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu--block">
                    <div class="dropdown-header">
                        Messages

                        <div class="actions">
                            <a href="messages.html" class="actions__item zmdi zmdi-plus"></a>
                        </div>
                    </div>

                    <div class="listview listview--hover">
                        <a href="empty.html#" class="listview__item">
                            <img src="demo/img/profile-pics/1.jpg" class="listview__img" alt="">

                            <div class="listview__content">
                                <div class="listview__heading">
                                    David Belle <small>12:01 PM</small>
                                </div>
                                <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <img src="demo/img/profile-pics/2.jpg" class="listview__img" alt="">

                            <div class="listview__content">
                                <div class="listview__heading">
                                    Jonathan Morris
                                    <small>02:45 PM</small>
                                </div>
                                <p>Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</p>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <img src="demo/img/profile-pics/3.jpg" class="listview__img" alt="">

                            <div class="listview__content">
                                <div class="listview__heading">
                                    Fredric Mitchell Jr.
                                    <small>08:21 PM</small>
                                </div>
                                <p>Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</p>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <img src="demo/img/profile-pics/4.jpg" class="listview__img" alt="">

                            <div class="listview__content">
                                <div class="listview__heading">
                                    Glenn Jecobs
                                    <small>08:43 PM</small>
                                </div>
                                <p>Ut vitae lacus sem ellentesque maximus, nunc sit amet varius dignissim, dui est consectetur neque</p>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <img src="demo/img/profile-pics/5.jpg" class="listview__img" alt="">

                            <div class="listview__content">
                                <div class="listview__heading">
                                    Bill Phillips
                                    <small>11:32 PM</small>
                                </div>
                                <p>Proin laoreet commodo eros id faucibus. Donec ligula quam, imperdiet vel ante placerat</p>
                            </div>
                        </a>

                        <a href="empty.html#" class="view-more">View all messages</a>
                    </div>
                </div>
            </li>

            <li class="dropdown top-nav__notifications">
                <a href="empty.html#" data-toggle="dropdown" class="top-nav__notify">
                    <i class="zmdi zmdi-notifications"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu--block">
                    <div class="dropdown-header">
                        Notifications

                        <div class="actions">
                            <a href="empty.html#" class="actions__item zmdi zmdi-check-all" data-sa-action="notifications-clear"></a>
                        </div>
                    </div>

                    <div class="listview listview--hover">
                        <div class="listview__scroll scrollbar-inner">
                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/1.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">David Belle</div>
                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/2.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">Jonathan Morris</div>
                                    <p>Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/3.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">Fredric Mitchell Jr.</div>
                                    <p>Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/4.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">Glenn Jecobs</div>
                                    <p>Ut vitae lacus sem ellentesque maximus, nunc sit amet varius dignissim, dui est consectetur neque</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/5.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">Bill Phillips</div>
                                    <p>Proin laoreet commodo eros id faucibus. Donec ligula quam, imperdiet vel ante placerat</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/1.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">David Belle</div>
                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/2.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">Jonathan Morris</div>
                                    <p>Nunc quis diam diamurabitur at dolor elementum, dictum turpis vel</p>
                                </div>
                            </a>

                            <a href="empty.html#" class="listview__item">
                                <img src="demo/img/profile-pics/3.jpg" class="listview__img" alt="">

                                <div class="listview__content">
                                    <div class="listview__heading">Fredric Mitchell Jr.</div>
                                    <p>Phasellus a ante et est ornare accumsan at vel magnauis blandit turpis at augue ultricies</p>
                                </div>
                            </a>
                        </div>

                        <div class="p-1"></div>
                    </div>
                </div>
            </li>

            <li class="dropdown hidden-xs-down">
                <a href="empty.html#" data-toggle="dropdown"><i class="zmdi zmdi-check-circle"></i></a>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu--block" role="menu">
                    <div class="dropdown-header">Tasks</div>

                    <div class="listview listview--hover">
                        <a href="empty.html#" class="listview__item">
                            <div class="listview__content">
                                <div class="listview__heading">HTML5 Validation Report</div>

                                <div class="progress mt-1">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <div class="listview__content">
                                <div class="listview__heading">Google Chrome Extension</div>

                                <div class="progress mt-1">
                                    <div class="progress-bar bg-warning" style="width: 43%" aria-valuenow="43" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <div class="listview__content">
                                <div class="listview__heading">Social Intranet Projects</div>

                                <div class="progress mt-1">
                                    <div class="progress-bar bg-success" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <div class="listview__content">
                                <div class="listview__heading">Bootstrap Admin Template</div>

                                <div class="progress mt-1">
                                    <div class="progress-bar bg-info" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </a>

                        <a href="empty.html#" class="listview__item">
                            <div class="listview__content">
                                <div class="listview__heading">Youtube Client App</div>

                                <div class="progress mt-1">
                                    <div class="progress-bar bg-danger" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </a>

                        <a href="empty.html#" class="view-more">View all Tasks</a>
                    </div>
                </div>
            </li>

            <li class="dropdown hidden-xs-down">
                <a href="empty.html#" data-toggle="dropdown"><i class="zmdi zmdi-apps"></i></a>

                <div class="dropdown-menu dropdown-menu-right dropdown-menu--block" role="menu">
                    <div class="row app-shortcuts">
                        <a class="col-4 app-shortcuts__item" href="empty.html#">
                            <i class="zmdi zmdi-calendar"></i>
                            <small class="">Calendar</small>
                        </a>
                        <a class="col-4 app-shortcuts__item" href="empty.html#">
                            <i class="zmdi zmdi-file-text"></i>
                            <small class="">Files</small>
                        </a>
                        <a class="col-4 app-shortcuts__item" href="empty.html#">
                            <i class="zmdi zmdi-email"></i>
                            <small class="">Email</small>
                        </a>
                        <a class="col-4 app-shortcuts__item" href="empty.html#">
                            <i class="zmdi zmdi-trending-up"></i>
                            <small class="">Reports</small>
                        </a>
                        <a class="col-4 app-shortcuts__item" href="empty.html#">
                            <i class="zmdi zmdi-view-headline"></i>
                            <small class="">News</small>
                        </a>
                        <a class="col-4 app-shortcuts__item" href="empty.html#">
                            <i class="zmdi zmdi-image"></i>
                            <small class="">Gallery</small>
                        </a>
                    </div>
                </div>
            </li>

            <li class="dropdown hidden-xs-down">
                <a href="empty.html#" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"></i></a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="empty.html#" class="dropdown-item" data-sa-action="fullscreen">Fullscreen</a>
                    <a href="empty.html#" class="dropdown-item">Clear Local Storage</a>
                    <a href="empty.html#" class="dropdown-item">Settings</a>
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

    <aside class="sidebar">
        <div class="scrollbar-inner">

            <div class="user">
                <div class="user__info" data-toggle="dropdown">
                    <img class="user__img" src="<?=SITE_TEMPLATE_PATH.'/assets/img/user-image.png'?>" />
                    <div class="user__wrapper">
                        <div class="user__name"><?=$GLOBALS['USER']->getFullName()?></div>
                        <div class="user__email"><?=$GLOBALS['USER']->getEmail()?></div>
                    </div>
                </div>

                <div class="dropdown-menu dropdown-menu--icon">
                    <a class="dropdown-item" href="<?=\Local\Core\Inner\Route::getRouteTo('personal', 'settings')?>"><i class="zmdi zmdi-shield-security"></i> Настойки</a>
                    <a class="dropdown-item" href="?logout=yes"><i class="zmdi zmdi-power"></i> Выйти</a>
                </div>
            </div>

            <?
            $GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.asidemenu', '.default', []);
            ?>
        </div>
    </aside>

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