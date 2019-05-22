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
<div class="ie-warning" style="background: #222;">
    <h1>Внимание!</h1>
    <p>Наш функционал в Вашем браузере может некорректно работать. Что бы этого избежать скачайте один из браузеров, приведенных ниже.</p>

    <div class="ie-warning__downloads alert alert-warning" style="background: #222;">
        <a href="http://www.google.com/chrome" target="_blank">Google Chrome</a>

        <a href="https://www.mozilla.org/en-US/firefox/new" target="_blank">Firefox</a>

        <a href="http://www.opera.com" target="_blank">Opera</a>

        <a href="https://support.apple.com/downloads/safari" target="_blank">Safari</a>
    </div>
    <p>Нам очень жаль за доставленные неудобства.</p>
</div>
<![endif]-->

<?if( !$GLOBALS['USER']->IsAdmin() ):?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(53708524, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/53708524" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?endif;?>

<!-- Chatra {literal} -->
<script>
    (function(d, w, c) {
        w.ChatraID = 'rgB3MMwY7wyccYtzQ';
        var s = d.createElement('script');
        w[c] = w[c] || function() {
            (w[c].q = w[c].q || []).push(arguments);
        };
        s.async = true;
        s.src = 'https://call.chatra.io/chatra.js';
        if (d.head) d.head.appendChild(s);
    })(document, window, 'Chatra');
</script>
<!-- /Chatra {/literal} -->

</body>
</html>