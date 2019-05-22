<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Робофид.ру - Robofeed XML v1");
$APPLICATION->SetPageProperty('title', "Робофид.ру - Robofeed XML v1");
$APPLICATION->SetPageProperty('description', "Робофид.ру - Описание создания Робофид XML, его требований и пример.");
?>
<?
$obAssets = \Bitrix\Main\Page\Asset::getInstance();
$obAssets->addCss('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/themes/github.min.css');
$obAssets->addJs('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/js/rainbow.min.js');
$obAssets->addJs('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/js/language/html.min.js');
$obAssets->addJs('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/js/language/scheme.min.js');
$obAssets->addString(<<<DOCHERE
<style type="text/css">
.close {
     float: inherit !important; 
     font-size: inherit !important; 
     font-weight: inherit !important; 
     line-height: inherit !important; 
     color: inherit; 
     text-shadow: inherit !important; 
     opacity: inherit !important; 
}
</style>
DOCHERE
);
?>
<?
if( \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('downloadExample') == 'Y')
{
    $GLOBALS['APPLICATION']->RestartBuffer();
    $strXml = \Local\Core\Inner\Robofeed\Schema\Factory::factory(1)->getXmlExample();
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=RobofeedXMLExample.xml');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.strlen($strXml));
    ob_clean();
    flush();
    echo $strXml;
    die();
}
?>
<h1>Robofeed XML</h1>
<h4>Требования к Robofeed XML</h4>
<ol>
    <li>
        Допустимые кодировки Robofeed-файла: UTF-8, windows-1251.
    </li>
    <li>
        В Robofeed xml нельзя использовать непечатаемые символы с ASCII-кодами от 0 до 31 (за исключением символов с кодами 9, 10, 13 — табуляция, перевод строки, возврат каретки).
    </li>
    <li>
        Символы <code>'</code> <code>"</code> <code>&</code> <code><</code><code>></code>  нужно заменять на эквивалентные коды:<br/>
        <table class="table table-bordered" style="width: auto;">
            <thead>
            <tr>
                <td>Символ в тексте</td>
                <td>Код для Robofeed</td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><code>'</code></td>
                <td><code><?=htmlspecialchars('&apos;');?></code></td>
            </tr>
            <tr>
                <td><code>"</code></td>
                <td><code><?=htmlspecialchars('&quot;')?></code></td>
            </tr>
            <tr>
                <td><code>&</code></td>
                <td><code><?=htmlspecialchars('&amp;');?></code></td>
            </tr>
            <tr>
                <td><code><</code></td>
                <td><code><?=htmlspecialchars('&lt;');?></code></td>
            </tr>
            <tr>
                <td><code>></code></td>
                <td><code><?=htmlspecialchars('&gt;');?></code></td>
            </tr>
            </tbody>
        </table>
        <ion-icon name="information-circle"></ion-icon> Примечание. Вы можете использовать символы <code>'</code> <code>"</code> <code>&</code> <code><</code><code>></code> в блоке CDATA в описании предложения.
    </li>
    <li>
        CDATA должен быть не длинее 3 000 символов в сумме.<br/>
        Так же в CDATA разрешены только теги <code>&lt;h3&gt;</code>, <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;br/&gt;</code>.<br/>
        Иные теги, а так же любые аттрибуты будут удалены!
    </li>
</ol>
<h4>Справочники Robofeed XML</h4>
<ul>
    <li><a href="/development/references/#measure">Справочник единиц измерений</a></li>
    <li><a href="/development/references/#currency">Справочник валют</a></li>
    <li><a href="/development/references/#country">Справочник стран</a></li>
</ul>
<h4>Структуа Robofeed XML с комментариями</h4>
<p>
    Обязательные поля помечены звездочкой *.<br/>
    Если у поля есть аттрибуты - они описываются и обязательные тоже помечаются звездочкой *.
</p>
<a href="?downloadExample=Y" target="_blank" class="btn orange">Скачать этот пример Robofeed XML</a>
<pre><code data-language="html"><?=htmlspecialchars(\Local\Core\Inner\Robofeed\Schema\Factory::factory(1)->getXmlExample());?></code></pre>

<a href="?downloadExample=Y" target="_blank" class="btn orange">Скачать этот пример Robofeed XML</a>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>