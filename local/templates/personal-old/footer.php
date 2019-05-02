<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?if($USER->isAuthorized()){?>
        </div>
    </section>
    <footer>
        <div class="container">
            <p class="copy">Copyright ROBOFEED <?=date("Y")?></p>
        </div>
    </footer>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        PersonalTemplate.init();
    })
</script>
<?}?>
</body>
</html>