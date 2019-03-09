<?

namespace Local\Core\Inner\Admin\Tabs;

use Bitrix\Main\Application;

abstract class Base
{
    const UF_PROPERTY_ID = null;
    const PAGES = [];
    const REQUEST_PARAM_NAME = "ID";

    abstract protected static function Init();

    protected static function Initialize()
    {
        global $APPLICATION, $USER_FIELD_MANAGER;

        if (in_array(static::GetCurPage(), static::PAGES)) {
            AddEventHandler("main", "OnAdminTabControlBegin", array(get_called_class(), 'OnAdminTabControlBegin'));
            AddEventHandler("main", "OnEndBufferContent", array(get_called_class(), 'OnEndBufferContent'));

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                AddEventHandler("main", "OnBeforeLocalRedirect", array(get_called_class(), 'OnBeforeLocalRedirect'));

                if ($ENTITY_ID = static::GetEntityId($_REQUEST[static::REQUEST_PARAM_NAME])) {

                    $arUfFields = $_REQUEST;
                    $USER_FIELD_MANAGER->EditFormAddFields(static::UF_PROPERTY_ID, $arUfFields);
                    $arFields = $USER_FIELD_MANAGER->GetUserFields(static::UF_PROPERTY_ID);

                    foreach ($arFields as $fieldName => $arField) {
                        if ($arField["USER_TYPE"]["BASE_TYPE"] == "file") {
                            if (is_array($arUfFields[$fieldName . "_descr"])) {
                                foreach ($arUfFields[$fieldName] as $k => $v) {
                                    if (!empty($arUfFields[$fieldName . "_descr"][$k])) {
                                        $arUfFields[$fieldName][$k]["description"] = $arUfFields[$fieldName . "_descr"][$k];
                                    } else {
                                        $arUfFields[$fieldName][$k]["description"] = $v["name"];
                                    }

                                }
                            }
                        }
                    }

                    if ($USER_FIELD_MANAGER->CheckFields(static::UF_PROPERTY_ID, $ENTITY_ID, $arUfFields)) {
                        $USER_FIELD_MANAGER->Update(static::UF_PROPERTY_ID, $ENTITY_ID, $arUfFields);
                    } else {
                        $obError = $APPLICATION->GetException();
                        \CAdminMessage::ShowMessage($obError->GetString());

                        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
                        die();
                    }
                }
            }
        }
    }

    public static function GetEntityId($value)
    {
        return $value;
    }

    public static function OnEndBufferContent(&$content)
    {
        if (in_array(static::GetCurPage(), static::PAGES)) {
            foreach (static::PAGES as $page) {
                $content = preg_replace("#(\<form.*?action=\"{$page}.*?\")#is", "\\1 enctype=\"multipart/form-data\"", $content);
            }
        }
    }

    public static function OnBeforeLocalRedirect(&$url, $skip_security_check, $bExternal)
    {
        foreach ($_REQUEST as $k => $v) {
            if (strpos($k, "active_tab") > 0 && strpos($v, "user_fields") === 0) {
                foreach (static::PAGES as $page) {
                    if (strpos($url, $page) === 0) {
                        $url .= ((strpos($url, "?") > 0 ? "&" : "?")) . "{$k}={$v}";
                        break 2;
                    }
                }

            }
        }
    }

    public static function OnAdminTabControlBegin(&$form)
    {
        global $USER_FIELD_MANAGER;

        if (in_array(static::GetCurPage(), static::PAGES)) {
            if ($ENTITY_ID = static::GetEntityId($_REQUEST[static::REQUEST_PARAM_NAME])) {
                $arTab = $USER_FIELD_MANAGER->EditFormTab(static::UF_PROPERTY_ID);

                ob_start();
                $USER_FIELD_MANAGER->EditFormShowTab(static::UF_PROPERTY_ID, false, $ENTITY_ID);
                $arTab["CONTENT"] = ob_get_clean();

                $form->tabs[] = $arTab;
            }
        }
    }

    protected static function GetCurPage()
    {
        return Application::getInstance()->getContext()->getRequest()->getPhpSelf();
    }
}