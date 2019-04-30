<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Главная");
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_firstslide.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_about.php');
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_platforms.php');
$APPLICATION->IncludeComponent( "local.core:mainpage.calc", ".default", Array() );
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH.'/include/mainpage_faqblock.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>