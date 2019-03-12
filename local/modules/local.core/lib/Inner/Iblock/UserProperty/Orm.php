<?php

namespace Local\Core\Inner\Iblock\UserProperty;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

class Orm
{
    protected static $dataCache = [];
    protected static $entityMapCache = array();
    protected static $arItemCache = array();

    /**
     * Возвращает массив пользовательских настроек свойства
     *
     * @param array $property Описание свойства
     *
     * @return array
     */
    public static function PrepareSettings( $property )
    {
        $data = [
            "ORM_ENTITY" => null,
            "ENTITY_ID_COLUMN" => null,
            "ENTITY_NAME_COLUMN" => null,
            "MODULES" => [],
        ];

        if ( !empty( $property[ "USER_TYPE_SETTINGS" ] ) && is_array( $property[ "USER_TYPE_SETTINGS" ] ) )
        {
            if ( isset( $property[ "USER_TYPE_SETTINGS" ][ "ORM_ENTITY" ] ) )
            {
                $data[ "ORM_ENTITY" ] = (string)$property[ "USER_TYPE_SETTINGS" ][ "ORM_ENTITY" ];
            }
            if ( isset( $property[ "USER_TYPE_SETTINGS" ][ "ENTITY_ID_COLUMN" ] ) )
            {
                $data[ "ENTITY_ID_COLUMN" ] = (string)$property[ "USER_TYPE_SETTINGS" ][ "ENTITY_ID_COLUMN" ];
            }
            if ( isset( $property[ "USER_TYPE_SETTINGS" ][ "ENTITY_NAME_COLUMN" ] ) )
            {
                $data[ "ENTITY_NAME_COLUMN" ] = (string)$property[ "USER_TYPE_SETTINGS" ][ "ENTITY_NAME_COLUMN" ];
            }
            if ( isset( $property[ "USER_TYPE_SETTINGS" ][ "MODULES" ] ) )
            {

                $includedModules = [];
                if ( isset( $property[ "USER_TYPE_SETTINGS" ][ "MODULES" ] ) && !empty( $property[ "USER_TYPE_SETTINGS" ][ "MODULES" ] ) )
                {
                    if ( is_array( $property[ "USER_TYPE_SETTINGS" ][ "MODULES" ] ) )
                    {
                        $includedModules = $property[ "USER_TYPE_SETTINGS" ][ "MODULES" ];
                    }
                    else
                    {
                        $includedModules = explode( ",", $property[ "USER_TYPE_SETTINGS" ][ "MODULES" ] );
                    }
                }
                if ( !empty( $includedModules ) )
                {
                    foreach ( $includedModules as $module )
                    {
                        $data[ "MODULES" ][] = trim( $module );
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Возвращает HTML для редактирования настроек свойства
     *
     * @param array $property Описание свойства
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     * @param array $propertyFields Поля свойства для формы редактирования
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetSettingsHTML.php
     */
    public static function GetSettingsHTML( $property, $HTMLControlName, &$propertyFields )
    {
        $propertyFields = [
            "HIDE" => [
                "ROW_COUNT", #Размер поля для ввода значения Строк
                "COL_COUNT", #Размер поля для ввода значения Столбцов
                "MULTIPLE_CNT", #Количество полей для ввода новых множественных значений
                "DEFAULT_VALUE", #Значение по умолчанию
                "WITH_DESCRIPTION" #Выводить поле для описания значения
            ],
        ];

        $settings = self::PrepareSettings( $property );
        $modules = implode( ",", $settings[ "MODULES" ] );

        return <<<"HIBSELECT"
<tr>
	<td>Подключаемые модули (через запятую)</td>
	<td>
		<input type="text" name="{$HTMLControlName["NAME"]}[MODULES]" size="30" value="{$modules}" >
	</td>
</tr>
<tr class="adm-detail-required-field">
	<td>Сущность ORM</td>
	<td>
		<input type="text" name="{$HTMLControlName["NAME"]}[ORM_ENTITY]" size="30" value="{$settings["ORM_ENTITY"]}" >
	</td>
</tr>
<tr class="adm-detail-required-field">
	<td>Колонка идентификатор</td>
	<td>
		<input type="text" name="{$HTMLControlName["NAME"]}[ENTITY_ID_COLUMN]" size="30" value="{$settings["ENTITY_ID_COLUMN"]}" >
	</td>
</tr>
<tr class="adm-detail-required-field">
	<td>Колонка названия</td>
	<td>
		<input type="text" name="{$HTMLControlName["NAME"]}[ENTITY_NAME_COLUMN]" size="30" value="{$settings["ENTITY_NAME_COLUMN"]}" >
	</td>
</tr>
HIBSELECT;
    }

    /**
     * Возвращает HTML для редактирования единичного значения
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetPropertyFieldHtml.php
     */
    public static function GetPropertyFieldHtml( $property, $value, $HTMLControlName )
    {

        $html = "<select name=\"{$HTMLControlName["VALUE"]}\">";
        $html .= self::GetOptionsHtml( $property, [$value[ "VALUE" ]] );
        $html .= "</select>";

        return $html;
    }

    /**
     * Вывод формы редактирования множественного свойства
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/getpropertyfieldhtmlmulty.php
     */
    public static function GetPropertyFieldHtmlMultiple( $property, $value, $HTMLControlName )
    {
        $max_n = 0;
        $values = [];
        if ( is_array( $value ) )
        {
            $match = [];
            foreach ( $value as $valueId => $arValue )
            {
                $values[ $valueId ] = $arValue[ "VALUE" ];
                if ( preg_match( "/^n(\\d+)$/", $valueId, $match ) )
                {
                    if ( $match[ 1 ] > $max_n )
                    {
                        $max_n = intval( $match[ 1 ] );
                    }
                }
            }
        }
        if ( end( $values ) != "" || substr( key( $values ), 0, 1 ) != "n" )
        {
            $values[ "n".( $max_n + 1 ) ] = "";
        }


        $name = $HTMLControlName[ "VALUE" ]."VALUE";
        $html = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"nopadding\" width=\"100%\" id=\"tb".md5( $name )."\">";
        foreach ( $values as $valueId => $value )
        {
            $html .= "<tr><td>";
            $html .= "<select name=\"{$HTMLControlName["VALUE"]}[{$valueId}][VALUE]\" >";
            $html .= self::GetOptionsHtml( $property, [$value] );
            $html .= "</select>";
            $html .= "</td></tr>";
        }
        $html .= "</table>";
        $html .= "<input type=\"button\" value=\"Ещё\" onclick=\"if(window.addNewRow){addNewRow('tb".md5( $name )."', -1)}else{addNewTableRow('tb".md5( $name )."', 1, /\[(n)([0-9]*)\]/g, 2)}\">";

        return $html;
    }

    /**
     * Метод должен вернуть HTML отображения элемента управления для редактирования значений свойства в публичной части
     * сайта.
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetPublicEditHTML.php
     */
    public static function GetPublicEditHTML( $property, $value, $HTMLControlName )
    {
        $multi = ( isset( $property[ "MULTIPLE" ] ) && $property[ "MULTIPLE" ] == "Y" );

        $html = "<select ".( $multi ? "multiple" : "" )." name=\"{$HTMLControlName["VALUE"]}".( $multi ? "[]" : "" )."\">";
        $html .= self::GetOptionsHtml( $property, $value );
        $html .= "</select>";

        return $html;
    }

    /**
     * Аналог GetPublicEditHTML, но работает с множественными свойствами.
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetPublicEditHTMLMulty.php
     */
    public static function GetPublicEditHTMLMultiple( $property, $value, $HTMLControlName )
    {
        $html = "<select multiple name=\"{$HTMLControlName["VALUE"]}[]\">";
        $html .= self::GetOptionsHtml( $property, self::normalizeValue( $value ) );
        $html .= "</select>";

        return $html;
    }

    /**
     * Возвращаем список option'ов для таблицы
     *
     * @param array $property Описание свойства
     * @param array $values Текущее значение
     *
     * @return string
     */
    public static function GetOptionsHtml( $property, $values )
    {
        $settings = self::PrepareSettings( $property );

        #Подключаем модули при необходимости
        $modulesIncluded = true;
        if ( !empty( $settings[ "MODULES" ] ) )
        {
            foreach ( $settings[ "MODULES" ] as $module )
            {
                try
                {
                    if ( !Loader::includeModule( trim( $module ) ) )
                    {
                        $modulesIncluded = false;
                        break;
                    }
                }
                catch ( LoaderException $e )
                {
                    $modulesIncluded = false;
                    break;
                }
            }
        }

        if ( $modulesIncluded === false )
        {
            return "";
        }

        $options = [];

        #Название сущности ORM
        $ormEntityName = $settings[ "ORM_ENTITY" ];
        $ormEntityColumnID = $settings[ "ENTITY_ID_COLUMN" ];
        $ormEntityColumnName = $settings[ "ENTITY_NAME_COLUMN" ];

        if (
            !empty( $ormEntityName )
            && class_exists( $ormEntityName )
            && new $ormEntityName instanceof \Bitrix\Main\Entity\DataManager
            && !empty( $ormEntityColumnID )
            && !empty( $ormEntityColumnName )
        )
        {
            #Получаем все записи из таблицы
            if ( empty( self::$dataCache[ $ormEntityName ] ) )
            {
                self::$dataCache[ $ormEntityName ] = self::getEntityFieldsByFilter(
                    $ormEntityName,
                    $ormEntityColumnID,
                    $ormEntityColumnName,
                    [
                        "select" => [$ormEntityColumnID, $ormEntityColumnName]
                    ]
                );
            }

            #Формируем option'ы
            $ormOptions = [];
            $selectedValue = false;
            foreach ( self::$dataCache[ $ormEntityName ] as $data )
            {
                $selected = "";
                if ( in_array( $data[ $ormEntityColumnID ], $values ) )
                {
                    $selected = "selected";
                    $selectedValue = true;
                }
                $ormOptions[] = "<option ".$selected." value=\"".htmlspecialcharsbx( $data[ $ormEntityColumnID ] )."\">".htmlspecialcharsEx( $data[ $ormEntityColumnName ]." [".$data[ $ormEntityColumnID ] )."]</option>";
            }

            $options = array_merge( [
                "<option value=\"\" ".( $selectedValue ? "" : " selected" ).">(не установлено)</option>"
            ], $ormOptions );

        }
        else
        {
            $options[] = "<option value=\"\" selected>(не установлено)</option>";
        }

        return implode( "", $options );
    }

    /**
     * Возвращает данные для умного фильтра
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     *
     * @return false|array
     */
    public static function GetExtendedValue( $property, $value )
    {
        if ( !isset( $value[ "VALUE" ] ) || empty( $value[ "VALUE" ] ) )
        {
            return false;
        }

        $settings = self::PrepareSettings( $property );

        #Подключаем модули при необходимости
        $modulesIncluded = true;
        if ( !empty( $settings[ "MODULES" ] ) )
        {
            foreach ( $settings[ "MODULES" ] as $module )
            {
                try
                {
                    if ( !Loader::includeModule( trim( $module ) ) )
                    {
                        $modulesIncluded = false;
                        break;
                    }
                }
                catch ( LoaderException $e )
                {
                    $modulesIncluded = false;
                    break;
                }
            }
        }

        if ( $modulesIncluded === false )
        {
            return false;
        }

        $ormEntityName = $settings[ "ORM_ENTITY" ];
        $ormEntityColumnID = $settings[ "ENTITY_ID_COLUMN" ];
        $ormEntityColumnName = $settings[ "ENTITY_NAME_COLUMN" ];

        if (
            !empty( $ormEntityName )
            && class_exists( $ormEntityName )
            && new $ormEntityName instanceof \Bitrix\Main\Entity\DataManager
            && !empty( $ormEntityColumnID )
            && !empty( $ormEntityColumnName )
        )
        {

            if ( !isset( self::$arItemCache[ $ormEntityName ] ) )
            {
                self::$arItemCache[ $ormEntityName ] = [];
            }

            #Если нет кеша или множественное значение, то получаем значения
            if ( is_array( $value[ "VALUE" ] ) || !isset( self::$arItemCache[ $ormEntityName ][ $value[ "VALUE" ] ] ) )
            {
                $data = self::getEntityFieldsByFilter(
                    $ormEntityName,
                    $ormEntityColumnID,
                    $ormEntityColumnName,
                    [
                        "select" => array($ormEntityColumnID, $ormEntityColumnName),
                        "filter" => array("={$ormEntityColumnID}" => $value[ "VALUE" ])
                    ]
                );
                if ( !empty( $data ) )
                {
                    foreach ( $data as $item )
                    {
                        if ( isset( $item[ $ormEntityColumnName ] ) )
                        {
                            $item[ "VALUE" ] = $item[ $ormEntityColumnName ];
                            self::$arItemCache[ $ormEntityName ][ $item[ $ormEntityColumnID ] ] = $item;
                        }
                    }
                }
            }

            # Если множественное значение
            if ( is_array( $value[ "VALUE" ] ) )
            {

                $result = [];
                foreach ( $value[ "VALUE" ] as $prop )
                {
                    if ( isset( self::$arItemCache[ $ormEntityName ][ $prop ] ) )
                    {
                        $result[ $prop ] = self::$arItemCache[ $ormEntityName ][ $prop ];
                    }
                    else
                    {
                        $result[ $prop ] = false;
                    }
                }

                return $result;
            }
            #Если единичное значение
            else
            {
                if ( isset( self::$arItemCache[ $ormEntityName ][ $value[ "VALUE" ] ] ) )
                {
                    return self::$arItemCache[ $ormEntityName ][ $value[ "VALUE" ] ];
                }
            }
        }
        else
        {
            return false;
        }

        return false;
    }

    /**
     * Метод должен вернуть безопасный HTML отображения значения свойства в списке элементов административной части
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetAdminListViewHTML.php
     */
    public static function GetAdminListViewHTML( $property, $value, $HTMLControlName )
    {
        $settings = self::PrepareSettings( $property );
        $data = self::GetExtendedValue( $property, $value );
        if ( $data )
        {
            return htmlspecialcharsbx( $data[ $settings[ "ENTITY_NAME_COLUMN" ] ] );
        }

        return "";
    }

    /**
     * Метод должна вернуть безопасный HTML отображения значения свойства в публичной части сайта.
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetPublicViewHTML.php
     */
    public static function GetPublicViewHTML( $property, $value, $HTMLControlName )
    {
        $settings = self::PrepareSettings( $property );
        $data = self::GetExtendedValue( $property, $value );

        if ( !empty( $data ) )
        {
            switch ( $HTMLControlName[ "MODE" ] ?? "" )
            {
                case "CSV_EXPORT":
                    return $data[ $settings[ "ENTITY_ID_COLUMN" ] ];
                    break;

                case "SIMPLE_TEXT":
                case "ELEMENT_TEMPLATE":
                    return $data[ $settings[ "ENTITY_NAME_COLUMN" ] ];
                    break;

                default:
                    return htmlspecialcharsbx( $data[ $settings[ "ENTITY_NAME_COLUMN" ] ] );
                    break;
            }
        }

        return "";
    }

    /**
     * Выводит html для фильтра по свойству на административной странице списка элементов инфоблока.
     *
     * @param array $property Описание свойства
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/getadminfilterhtml.php
     */
    public static function GetAdminFilterHTML( $property, $HTMLControlName )
    {
        $lAdmin = new \CAdminList( $HTMLControlName[ "TABLE_ID" ] );
        $lAdmin->InitFilter( [$HTMLControlName[ "VALUE" ]] );
        $filterValue = $GLOBALS[ $HTMLControlName[ "VALUE" ] ];

        if ( isset( $filterValue ) && is_array( $filterValue ) )
        {
            $values = $filterValue;
        }
        else
        {
            $values = [];
        }

        $options = self::GetOptionsHtml( $property, $values );
        $html = "<select name=\"{$HTMLControlName["VALUE"]}[]\" multiple>";
        $html .= $options;
        $html .= "</select>";

        return $html;
    }

    /**
     * Возвращает представление значения свойства для модуля поиска.
     *
     * @param array $property Описание свойства
     * @param array $value Текущее значение
     * @param array $HTMLControlName Имя элемента управления для заполнения настроек свойства.
     *
     * @return string
     *
     * @see https://dev.1c-bitrix.ru/api_help/iblock/classes/user_properties/GetSearchContent.php
     */
    public static function GetSearchContent( $property, $value, $HTMLControlName )
    {
        $settings = self::PrepareSettings( $property );
        $data = self::GetExtendedValue( $property, $value );

        if ( !empty( $data ) )
        {
            return $data[ $settings[ "ENTITY_NAME_COLUMN" ] ];
        }

        return "";
    }

    /**
     * Добавляет значения в фильтр
     *
     * @param array  $property
     * @param array  $HTMLControlName
     * @param array &$arFilter
     * @param bool & $filtered
     *
     * @return void
     */
    public static function AddFilterFields( $property, $HTMLControlName, &$arFilter, &$filtered )
    {
        $filtered = false;
        $values = array();

        if ( isset( $_REQUEST[ $HTMLControlName[ "VALUE" ] ] ) )
        {
            $values = ( is_array( $_REQUEST[ $HTMLControlName[ "VALUE" ] ] ) ? $_REQUEST[ $HTMLControlName[ "VALUE" ] ] : array($_REQUEST[ $HTMLControlName[ "VALUE" ] ]) );
        }
        elseif ( isset( $GLOBALS[ $HTMLControlName[ "VALUE" ] ] ) )
        {
            $values = ( is_array( $GLOBALS[ $HTMLControlName[ "VALUE" ] ] ) ? $GLOBALS[ $HTMLControlName[ "VALUE" ] ] : array($GLOBALS[ $HTMLControlName[ "VALUE" ] ]) );
        }

        if ( !empty( $values ) )
        {
            $clearValues = array();
            foreach ( $values as $oneValue )
            {
                $oneValue = (string)$oneValue;
                if ( $oneValue != "" )
                {
                    $clearValues[] = $oneValue;
                }
            }
            $values = $clearValues;
            unset( $oneValue, $clearValues );
        }
        if ( !empty( $values ) )
        {
            $filtered = true;
            $arFilter[ "=PROPERTY_".$property[ "ID" ] ] = $values;
        }
    }


    /**
     * Получаем данные таблицы
     *
     * @param string $ormEntityName
     * @param string $ormEntityColumnID
     * @param string $ormEntityColumnName
     * @param array  $getListParams
     *
     * @return array
     */
    private static function getEntityFieldsByFilter( string $ormEntityName, string $ormEntityColumnID, string $ormEntityColumnName, $getListParams = [] )
    {
        $arResult = [];

        if (
            !empty( $ormEntityName )
            && class_exists( $ormEntityName )
            && new $ormEntityName instanceof \Bitrix\Main\Entity\DataManager
            && !empty( $ormEntityColumnID )
            && !empty( $ormEntityColumnName )
        )
        {

            if ( !isset( self::$entityMapCache[ $ormEntityName ] ) )
            {
                /** @var $ormEntityName \Bitrix\Main\Entity\DataManager */
                self::$entityMapCache[ $ormEntityName ] = $ormEntityName::getMap();
            }

            $getListParams[ "order" ] = [];
            if ( isset( self::$entityMapCache[ $ormEntityName ][ "SORT" ] ) )
            {
                $getListParams[ "order" ][ "SORT" ] = "ASC";
                $getListParams[ "select" ][] = "SORT";
            }

            if ( isset( self::$entityMapCache[ $ormEntityName ][ $ormEntityColumnName ] ) )
            {
                $getListParams[ "order" ][ $ormEntityColumnName ] = "ASC";
            }

            if ( isset( self::$entityMapCache[ $ormEntityName ][ $ormEntityColumnID ] ) )
            {
                $getListParams[ "order" ][ $ormEntityColumnID ] = "ASC";
            }

            try
            {
                $rsData = $ormEntityName::getList( $getListParams );
                while ( $arData = $rsData->fetch() )
                {
                    $arResult[] = $arData;
                }
                unset( $arData, $rsData );

            }
            catch ( \Exception $e )
            {
                return $arResult;
            }
        }

        return $arResult;
    }

    private static function normalizeValue( $value )
    {
        $result = [];

        if ( !is_array( $value ) )
        {
            $value = (string)$value;
            if ( $value !== "" )
            {
                $result[] = $value;
            }
        }
        else
        {
            if ( !empty( $value ) )
            {
                foreach ( $value as $row )
                {
                    $oneValue = "";
                    if ( is_array( $row ) )
                    {
                        if ( isset( $row[ "VALUE" ] ) )
                        {
                            $oneValue = (string)$row[ "VALUE" ];
                        }
                    }
                    else
                    {
                        $oneValue = (string)$row;
                    }

                    if ( $oneValue !== "" )
                    {
                        $result[] = $oneValue;
                    }
                }
                unset( $oneValue, $row );
            }
        }

        return $result;
    }
}
