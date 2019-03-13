<?
IncludeModuleLangFile(__FILE__);
if( class_exists("local_core") )
{
    return;
}

Class local_core extends CModule
{
    const MODULE_ID = "local.core";
    var $MODULE_ID = "local.core";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError  = "";

    function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include( $path."/version.php" );

        if( is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion) )
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = getMessage("LOCAL_CORE_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("LOCAL_CORE_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("LOCAL_CORE_MODULE_PARTNER_NAME");
    }


    function doInstall()
    {
        $this->installDB();
    }

    function installDB()
    {
        $this->errors = false;

        if( $this->errors !== false )
        {
            $GLOBALS["APPLICATION"]->throwException(implode("", $this->errors));

            return false;
        }

        registerModule($this->MODULE_ID);

        return true;
    }

    function doUninstall()
    {
        $this->uninstallDB();
    }

    function uninstallDB()
    {
        $this->errors = false;

        if( $this->errors !== false )
        {
            $GLOBALS["APPLICATION"]->throwException(implode("", $this->errors));

            return false;
        }
        unregisterModule($this->MODULE_ID);

        return true;
    }
}

?>
