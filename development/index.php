<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("TITLE", "Разработчикам");
$APPLICATION->SetTitle("Разработчикам");
?>

<h1>Разработчкам</h1>
<p>
    <b><a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>">Robofeed XML</a></b> - XML, который описывает товары магазина, способы и условия доставки, самовывоза. Импортируется в настройках магазина в личном кабинете. Иные форматы, кроме Robofeed XML, системой не принимаются. Так же Robofeed XML можно получить путем <a href="<?=\Local\Core\Inner\Route::getRouteTo('tools', 'converter')?>" target="_blank">конвертации</a> из некоторых сторонних форматов, но стоит понимать, что конвертер делает приблизительный Robofeed XML, предназначен для демонстрационной версии и не дает гарантий полной работоспособности. <a href="<?=\Local\Core\Inner\Route::getRouteTo('tools', 'converter')?>" target="_blank">Конвертер</a> находится в личном кабинете.
</p>
<br/>

<p>
    <b><a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'references')?>">Справочники</a></b> - коды справочников единиц измерений, стран и валют, которые используются в Robofeed XML.
</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>