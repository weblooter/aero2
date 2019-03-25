<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Robofeed v1");
?>
<?
\Bitrix\Main\Page\Asset::getInstance()->addCss('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/themes/github.min.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/js/rainbow.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/js/language/html.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/js/language/scheme.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addString(<<<DOCHERE
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
<div class="col-12">
    <h2>Robofeed XML v1</h2>
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
    <h4>Пример Robofeed XML с комментариями</h4>
    <p>
        Обязательные поля помечены звездочкой *.<br/>
        Если у поля есть аттрибуты - они описываются и обязательные тоже помечаются звездочкой *.
    </p>
    <pre><code data-language="html"><?=htmlspecialchars(\Local\Core\Inner\Robofeed\Schema\Factory::factory(1)->getXmlExample());?></code></pre>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>