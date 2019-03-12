<?php

namespace Local\Core\Inner\Iblock\UserProperty;

class CondTree
{
    public static function OnBeforeSave( $arUserField, $value )
    {
        if ( is_array( $value ) )
        {
            $obCond = new \CCatalogCondTree();
            $boolCond = $obCond->Init( BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, array() );
            if ( !$boolCond )
            {
                return "";
            }
            else
            {
                $value = $obCond->Parse( $value, [] );
                if ( !empty( $value ) )
                {
                    $value = serialize( $value );
                }
            }
        }

        return $value;
    }

    public static function GetDBColumnType( $arUserField )
    {
        global $DB;
        switch ( strtolower( $DB->type ) )
        {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

    public static function GetFilterData( $arUserField, $arHtmlControl )
    {
        return array(
            "id" => $arHtmlControl[ "ID" ],
            "name" => $arHtmlControl[ "NAME" ],
            "type" => "string",
            "filterable" => ""
        );
    }

    public static function OnSearchIndex( $arUserField )
    {
        return null;
    }

    public static function GetFilterHTML( $arUserField, $arHtmlControl )
    {
        return null;
    }

    public static function GetAdminListViewHTML( $arUserField, $arHtmlControl )
    {
        return null;
    }

    public static function GetEditFormHTML( $arUserField, $arHtmlControl )
    {
        global $APPLICATION;

        $result = "";

        if ( isset( $arUserField[ "ENTITY_ID" ] ) )
        {
            $hlId = str_replace( "HLBLOCK_", "", $arUserField[ "ENTITY_ID" ] );
            $formName = "hlrow_edit_{$hlId}_form";

            # Формирует js форму дл япостроения правил
            $obCond = new \CCatalogCondTree();
            $boolCond = $obCond->Init( BT_COND_MODE_DEFAULT, BT_COND_BUILD_CATALOG, [
                "FORM_NAME" => $formName,
                "CONT_ID" => $arUserField[ "FIELD_NAME" ],
                "JS_NAME" => "JSCatCond",
                "PREFIX" => $arUserField[ "FIELD_NAME" ]
            ] );
            if ( !$boolCond )
            {
                if ( $ex = $APPLICATION->GetException() )
                {
                    $result .= $ex->GetString()."<br>";
                }
            }
            else
            {
                $result .= $obCond->Show( $arUserField[ "VALUE" ] );
            }

            # Блок с правилами
            $result .= "<div id='{$arUserField["FIELD_NAME"]}' style='position: relative; z-index: 1;'></div>";
        }

        return $result;
    }
}
