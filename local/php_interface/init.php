<?
date_default_timezone_set("Europe/Moscow");
include( $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/vendor/autoload.php' );

if( \Bitrix\Main\Loader::includeModule("local.core") )
{
    \Local\Core\EventHandlers\Base::register();
}