<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Главная");
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_firstslide.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_about.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_platforms.php');
$APPLICATION->IncludeComponent(
	"local.core:mainpage.calc", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SHOW_TARIFFS" => array(
			0 => "20180330_BEF_5000",
			1 => "20180330_BEF_5000_DIS",
			2 => "20190330_BEF_10000",
			3 => "20190330_BEF_10000_DIS",
			4 => "20190330_BEF_20000",
			5 => "20190330_BEF_20000_DIS",
			6 => "20190330_BEF_30000",
			7 => "20190330_BEF_30000_DIS",
			8 => "20190330_BEF_40000",
			9 => "20190330_BEF_40000_DIS",
			10 => "20190330_BEF_50000",
		)
	),
	false
);
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_faqblock.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>