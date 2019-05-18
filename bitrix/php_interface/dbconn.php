<?
//@set_time_limit(php_sapi_name() == "cli" ? 600 : 60);

if(!(defined("CHK_EVENT") && CHK_EVENT===true))
    define("BX_CRONTAB_SUPPORT", true);

define("SHORT_INSTALL_CHECK", true);
define("DBPersistent", false);

$arConnection = require($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/connection_data.php');
$arConnection = $arConnection[ ( !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : end(array_diff(explode('/', $_SERVER['DOCUMENT_ROOT']), [''])) ) ];

$DBType = "mysql";
$DBHost = $arConnection['host'];
$DBLogin = $arConnection['username'];
$DBPassword = $arConnection['password'];
$DBName = $arConnection['dbname'];
$DBDebug = false;
$DBDebugToFile = false;

define("DELAY_DB_CONNECT", true);
define("CACHED_b_file", 3600);
define("CACHED_b_file_bucket_size", 10);
define("CACHED_b_lang", 3600);
define("CACHED_b_option", 3600);
define("CACHED_b_lang_domain", 3600);
define("CACHED_b_site_template", 3600);
define("CACHED_b_event", 3600);
define("CACHED_b_agent", 3660);
define("CACHED_menu", 3600);

define("BX_FILE_PERMISSIONS", 0664);
define("BX_DIR_PERMISSIONS", 0775);
@umask(~BX_DIR_PERMISSIONS);

define("MYSQL_TABLE_TYPE", "INNODB");
define("SHORT_INSTALL", true);
define("VM_INSTALL", true);

define("BX_UTF", true);

define("BX_DISABLE_INDEX_PAGE", true);
define("BX_COMPRESSION_DISABLED", true);
define("BX_USE_MYSQLI", true);

?>
