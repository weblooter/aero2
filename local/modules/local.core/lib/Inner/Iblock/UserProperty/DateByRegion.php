<?php

namespace Local\Core\Inner\Iblock\UserProperty;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Type\RandomSequence;
use Local\Core\Model\Reference\RegionTable;

class DateByRegion
{
    protected static $dataCache = [];
    protected static $entityMapCache = array();
    protected static $arItemCache = array();

    public static function getDescriptionArrayForRegister()
    {
        return [
            "PROPERTY_TYPE" => "N",
            "USER_TYPE" => "dateregion",
            "DESCRIPTION" => "Дата по регионам",
            "GetPropertyFieldHtml" => [self::class, "GetPropertyFieldHtml"],
            "GetPropertyFieldHtmlMulty" => [self::class, "GetPropertyFieldHtmlMultiple"],
            //"GetAdminListViewHTML" => [self::class, "GetAdminListViewHTML"],
            //"GetAdminFilterHTML" => [self::class, "GetAdminFilterHTML"],
        ];
    }

    protected static function decodeValue($val)
    {
        $val = (int)$val;

        if ($val <= 10000000000) {
            return [0, 0];
        }

        $region_id = (int)floor($val * 0.0000000001);

        $time = $val - $region_id * 10000000000;

        return [$region_id, $time];
    }

    protected static function getRegionList(){
        return RegionTable::getList([
            "select" => [
                "ID",
                "NAME",
                "CODE",
            ],
            "filter" => ["=ACTIVE" => "Y"],
            "cache" => ["ttl" => 86400]
        ])->fetchAll();
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
    public static function GetPropertyFieldHtml($property, $value, $HTMLControlName)
    {
        \Bitrix\Main\UI\Extension::load('local-core.vue.components.admin.dateregion');

        $app_id = 'fld_' . md5($HTMLControlName['VALUE']);

        $json_regions = json_encode(self::getRegionList(), JSON_UNESCAPED_UNICODE);
        $json_property = json_encode($property, JSON_UNESCAPED_UNICODE ^ JSON_FORCE_OBJECT);
        $json_value = json_encode($value, JSON_UNESCAPED_UNICODE ^ JSON_FORCE_OBJECT);
        $json_control_name = json_encode($HTMLControlName, JSON_UNESCAPED_UNICODE ^ JSON_FORCE_OBJECT);

        $html = <<<JAVASCRIPT
        <div id="{$app_id}">
            <bx-admin-date-region :regions="regions" :property="property" :value="value" :control="control"></bx-admin-date-region>
        </div>
        
        <script>
        BX.Vue.create({
            el: '#{$app_id}',
            data(){
                return {
                    regions: {$json_regions},
                    property: {$json_property},
                    value: {$json_value},
                    control: {$json_control_name}
                }
            }
        });
        </script>
JAVASCRIPT;

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
    public static function GetPropertyFieldHtmlMultiple($property, $value, $HTMLControlName)
    {
        \Bitrix\Main\UI\Extension::load('local-core.vue.components.admin.dateregionmulti');

        $app_id = 'fld_' . md5($HTMLControlName['VALUE']);

        $json_regions = json_encode(self::getRegionList(), JSON_UNESCAPED_UNICODE);
        $json_property = json_encode($property, JSON_UNESCAPED_UNICODE ^ JSON_FORCE_OBJECT);
        $json_value = json_encode($value, JSON_UNESCAPED_UNICODE ^ JSON_FORCE_OBJECT);
        $json_control_name = json_encode($HTMLControlName, JSON_UNESCAPED_UNICODE ^ JSON_FORCE_OBJECT);

        $html = <<<JAVASCRIPT
        <div id="{$app_id}">
            <bx-admin-date-region-multiple :regions="regions" :property="property" :value="value" :control="control"></bx-admin-date-region-multiple>
        </div>
        
        <script>
        BX.Vue.create({
            el: '#{$app_id}',
            data(){
                return {
                    regions: {$json_regions},
                    property: {$json_property},
                    value: {$json_value},
                    control: {$json_control_name}
                }
            }
        });
        </script>
JAVASCRIPT;

        return $html;
    }
}
