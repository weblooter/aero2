<?
define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'].'/zz.log');

include($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/vendor/autoload.php');

if (\Bitrix\Main\Loader::includeModule("local.core")) {
    \Local\Core\EventHandlers\Base::register();
}

function custom_mail($to, $subject, $message, $additional_headers = "", $additional_parameters = "", \Bitrix\Main\Mail\Context $context = null)
{
    return \Local\Core\Inner\Mail::send($to, $subject, $message, $additional_headers, $additional_parameters, $context);
}