<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Robofeed.ru - Быстрая интеграция с торговыми площадками для интернет магазинов");
$APPLICATION->SetPageProperty('title', "Robofeed.ru - Быстрая интеграция с торговыми площадками для интернет магазинов");
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_firstslide.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_about.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_platforms.php');
$APPLICATION->IncludeComponent(
	"local.core:mainpage.calc", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SHOW_TARIFFS" => array(
			1 => "20180330_BEF_5000_DIS",
			3 => "20190330_BEF_10000_DIS",
			5 => "20190330_BEF_20000_DIS",
			7 => "20190330_BEF_30000_DIS",
			9 => "20190330_BEF_40000_DIS",
			10 => "20190330_BEF_50000_DIS",
		)
	),
	false
);
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_faqblock.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>