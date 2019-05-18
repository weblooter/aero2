<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?if( $_SERVER['PHP_SELF'] != '/index.php' ):?>
    </div>
<?endif;?>
</div>
<footer>
    <div class="container">
        <a href="/" class="logo">ROBOFEED</a>
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "footermenu",
            Array(
                "ALLOW_MULTI_SELECT" => "N",
                "CHILD_MENU_TYPE" => "left",
                "DELAY" => "N",
                "MAX_LEVEL" => "1",
                "MENU_CACHE_GET_VARS" => array(""),
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_TYPE" => "N",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "ROOT_MENU_TYPE" => "bottom",
                "USE_EXT" => "N"
            )
        );?>
        <p class="copyright">© ROBOFEED <?=date('Y')?></p>
    </div>
</footer>

</body>

</html>