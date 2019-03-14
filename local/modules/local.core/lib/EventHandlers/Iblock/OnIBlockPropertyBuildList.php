<?php

namespace Local\Core\EventHandlers\Iblock;


use Local\Core\Inner\Iblock\UserProperty;

/**
 * Событие вызывается при построении списка пользовательских свойств
 * Class OnIBlockPropertyBuildList
 * @package Local\Core\EventHandlers\Iblock
 * @see     https://dev.1c-bitrix.ru/api_help/iblock/events/OnIBlockPropertyBuildList.php
 */
class OnIBlockPropertyBuildList
{
    /**
     * Возвращает массив описывающий поведение пользовательского свойства<br>
     * Свойство для элемента "Связь с ORM таблицей"
     * @return array
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetUserTypeDescription.php
     */
    public static function getLinkToORM()
    {
        return [
            "PROPERTY_TYPE"             => "S",
            "USER_TYPE"                 => "orm",
            "DESCRIPTION"               => "Связь с ORM таблицей",
            "GetSettingsHTML"           => [UserProperty\Orm::class, "GetSettingsHTML"],
            "GetPropertyFieldHtml"      => [UserProperty\Orm::class, "GetPropertyFieldHtml"],
            "GetPropertyFieldHtmlMulty" => [UserProperty\Orm::class, "GetPropertyFieldHtmlMultiple"],
            "PrepareSettings"           => [UserProperty\Orm::class, "PrepareSettings"],
            "GetAdminListViewHTML"      => [UserProperty\Orm::class, "GetAdminListViewHTML"],
            "GetPublicViewHTML"         => [UserProperty\Orm::class, "GetPublicViewHTML"],
            "GetPublicEditHTML"         => [UserProperty\Orm::class, "GetPublicEditHTML"],
            "GetPublicEditHTMLMulty"    => [UserProperty\Orm::class, "GetPublicEditHTMLMultiple"],
            "GetAdminFilterHTML"        => [UserProperty\Orm::class, "GetAdminFilterHTML"],
            "GetExtendedValue"          => [UserProperty\Orm::class, "GetExtendedValue"],
            "GetSearchContent"          => [UserProperty\Orm::class, "GetSearchContent"],
            "AddFilterFields"           => [UserProperty\Orm::class, "AddFilterFields"]
        ];
    }
}
