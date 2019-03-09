<?php

namespace Local\Core\Inner\Iblock\UserProperty;

use \Bitrix\Highloadblock as HL;

/*
 * В отличии от базового класса, метод GetExtendedValue возвращает все колонки таблицы
 */

class CIBlockPropertyDirectoryExt extends \CIBlockPropertyDirectory
{
    public static function GetExtendedValue($arProperty, $value)
    {
        if (!isset($value['VALUE'])) {
            return false;
        }

        if (is_array($value['VALUE']) && count($value['VALUE']) == 0) {
            return false;
        }

        if (empty($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'])) {
            return false;
        }

        $tableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];
        if (!isset(self::$arItemCache[$tableName])) {
            self::$arItemCache[$tableName] = array();
        }

        if (is_array($value['VALUE']) || !isset(self::$arItemCache[$tableName][$value['VALUE']])) {
            $data = self::getEntityFieldsByFilter(
                $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'],
                array(
                    'select' => array('*'),
                    'filter' => array('=UF_XML_ID' => $value['VALUE'])
                )
            );

            if (!empty($data)) {
                foreach ($data as $item) {
                    if (isset($item['UF_XML_ID'])) {
                        $item['VALUE'] = $item['UF_NAME'];
                        if (isset($item['UF_FILE'])) {
                            $item['FILE_ID'] = $item['UF_FILE'];
                        }
                        self::$arItemCache[$tableName][$item['UF_XML_ID']] = $item;
                    }
                }
            }
        }

        if (is_array($value['VALUE'])) {
            $result = array();
            foreach ($value['VALUE'] as $prop) {
                if (isset(self::$arItemCache[$tableName][$prop])) {
                    $result[$prop] = self::$arItemCache[$tableName][$prop];
                } else {
                    $result[$prop] = false;
                }
            }
            return $result;
        } else {
            if (isset(self::$arItemCache[$tableName][$value['VALUE']])) {
                return self::$arItemCache[$tableName][$value['VALUE']];
            }
        }
        return false;
    }

    private static function getEntityFieldsByFilter($tableName, $listDescr = array())
    {
        $arResult = array();
        $tableName = (string)$tableName;
        if (!is_array($listDescr)) {
            $listDescr = array();
        }
        if (!empty($tableName)) {
            if (!isset(self::$hlblockCache[$tableName])) {
                self::$hlblockCache[$tableName] = HL\HighloadBlockTable::getList(
                    array(
                        'select' => array('TABLE_NAME', 'NAME', 'ID'),
                        'filter' => array('=TABLE_NAME' => $tableName)
                    )
                )->fetch();
            }
            if (!empty(self::$hlblockCache[$tableName])) {
                if (!isset(self::$directoryMap[$tableName])) {
                    $entity = HL\HighloadBlockTable::compileEntity(self::$hlblockCache[$tableName]);
                    self::$hlblockClassNameCache[$tableName] = $entity->getDataClass();
                    self::$directoryMap[$tableName] = $entity->getFields();
                    unset($entity);
                }
                if (!isset(self::$directoryMap[$tableName]['UF_XML_ID'])) {
                    return $arResult;
                }
                $entityDataClass = self::$hlblockClassNameCache[$tableName];

                $nameExist = isset(self::$directoryMap[$tableName]['UF_NAME']);
                if (!$nameExist) {
                    $listDescr['select'] = array('UF_XML_ID', 'ID');
                }
                $fileExists = isset(self::$directoryMap[$tableName]['UF_FILE']);
                if ($fileExists) {
                    $listDescr['select'][] = 'UF_FILE';
                }

                $sortExist = isset(self::$directoryMap[$tableName]['UF_SORT']);
                $listDescr['order'] = array();
                if ($sortExist) {
                    $listDescr['order']['UF_SORT'] = 'ASC';
                    $listDescr['select'][] = 'UF_SORT';
                }
                if ($nameExist) {
                    $listDescr['order']['UF_NAME'] = 'ASC';
                } else {
                    $listDescr['order']['UF_XML_ID'] = 'ASC';
                }
                $listDescr['order']['ID'] = 'ASC';
                /** @var \Bitrix\Main\DB\Result $rsData */
                $rsData = $entityDataClass::getList($listDescr);
                while ($arData = $rsData->fetch()) {
                    if (!$nameExist) {
                        $arData['UF_NAME'] = $arData['UF_XML_ID'];
                    }
                    $arData['SORT'] = ($sortExist ? $arData['UF_SORT'] : $arData['ID']);
                    $arResult[] = $arData;
                }
                unset($arData, $rsData);
            }
        }
        return $arResult;
    }
}
