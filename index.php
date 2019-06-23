<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Робофид.ру - Быстрая интеграция с торговыми площадками для интернет магазинов");
$APPLICATION->SetPageProperty('title', "Робофид.ру - Быстрая интеграция с торговыми площадками для интернет магазинов");
$APPLICATION->SetPageProperty('description', "Робофид.ру это сервис, созданный чтобы упростить процесс выхода ваших товарных предложений на торговые площадки для интернет магазинов. Мы помогаем интернет-магазинам генерировать специальные прайс-листы, которые сервисы торговых площадок используют для актуализации информации о ценах и наличие товаров. Единожды интегрировавшись с Robofeed Вы получаете возможность автоматически генерировать и актуализировать feed-файлы для постоянно обновляющегося и расширяющегося списка торговых площадок, снимая с себя все заботы об интеграции с каждой из них отдельно. Вся работа по генерации производится на наших мощных серверах, что так же облегчает жизнь вашему интернет-магазину и позволяет снизить Ваши затраты на аренду серверных мощностей. Даже есть Ваш сайт временно перестанет быть доступным - мы продолжим работу, а значит Вы не потеряете заказы.");
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_firstslide.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_about.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_platforms.php');
$APPLICATION->IncludeComponent(
	"local.core:mainpage.calc", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SHOW_TARIFFS" => array(
			1 => "FREE",
			2 => "20180330_BEF_5000",
			3 => "20190330_BEF_10000",
			5 => "20190330_BEF_20000",
			7 => "20190330_BEF_30000",
			9 => "20190330_BEF_40000",
			10 => "20190330_BEF_50000",
		)
	),
	false
);
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_faqblock.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>