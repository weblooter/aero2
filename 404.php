<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");?>
<?
switch (SITE_TEMPLATE_ID)
{
    case 'personal':
        ?>
        <section class="error">
            <div class="error__inner">
                <h1>404</h1>
                <h2>Такой страницы не существует!</h2>
                <a href="/personal/" class="btn btn-light">Вернуться на рабочий стол</a>
            </div>
        </section>
        <?
        break;
    default:
        ?>
        <h1>Такой страницы не существует!</h1>
        <a href="/">Вернуться на главную</a>
        <?
        break;
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>