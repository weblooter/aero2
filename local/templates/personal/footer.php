<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?if( !defined('ERROR_404') && $GLOBALS['USER']->IsAuthorized() ):?>
</section>

    <aside class="sidebar">
        <div class="scrollbar-inner">

            <div class="user">
                <div class="user__info" data-toggle="dropdown">
                    <?$GLOBALS['APPLICATION']->IncludeFile('include/sideUserInfo.php', false, ['MODE' => 'PHP'])?>
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

</main>
<?endif;?>


<!-- Older IE warning message -->
<!--[if IE]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade to any of the following web browsers to access this website.</p>

    <div class="ie-warning__downloads">
        <a href="http://www.google.com/chrome">
            <img src="img/browsers/chrome.png" alt="">
        </a>

        <a href="https://www.mozilla.org/en-US/firefox/new">
            <img src="img/browsers/firefox.png" alt="">
        </a>

        <a href="http://www.opera.com">
            <img src="img/browsers/opera.png" alt="">
        </a>

        <a href="https://support.apple.com/downloads/safari">
            <img src="img/browsers/safari.png" alt="">
        </a>

        <a href="https://www.microsoft.com/en-us/windows/microsoft-edge">
            <img src="img/browsers/edge.png" alt="">
        </a>

        <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
            <img src="img/browsers/ie.png" alt="">
        </a>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->

</body>
</html>