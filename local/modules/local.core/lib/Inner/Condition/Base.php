<?php

namespace Local\Core\Inner\Condition;

/**
 * Базовый класс для работы с условиями, заточенными под структуру робофида V1
 *
 * @package Local\Core\Inner\Condition
 */
class Base
{

    /**
     * Получить html блока условия
     *
     * @param string $intStoreId     ID Магазина
     * @param string $strFormId      ID формы
     * @param string $strConditionId ID блока
     * @param string $strInputName   name инпута
     * @param array  $arValue        Преобразованное значение плавила ( ::parseCondition() )
     *
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getConditionBlock($intStoreId, $strFormId, $strConditionId, $strInputName, $arValue = [])
    {
        $strResult = '';

        $obCond = new \Local\Core\Inner\Condition\CondTree();
        $obCond->setStoreId($intStoreId);
        $obCond->setRobofeedVersion( \Local\Core\Inner\Store\Base::getLastSuccessImportVersion($intStoreId) );
        $boolCond = $obCond->Init(LOCAL_CORE_CONDITION_MODE_DEFAULT, LOCAL_CORE_CONDITION_BUILD_CATALOG, [
            "FORM_NAME" => $strFormId, // ID формы в которую будет выводится
            "CONT_ID" => $strConditionId,
            "JS_NAME" => "JSCatCond",
            "PREFIX" => $strInputName
        ]);

        if (!$boolCond) {
            if ($ex = $GLOBALS['APPLICATION']->GetException()) {
                $strResult .= $ex->GetString()."<br>";
            }
        } else {

            $strResult .= $obCond->Show($arValue);
        }

        # Блок с правилами
        $strResult .= "<div id='".$strConditionId."' style='position: relative; z-index: 1;'></div>";

        return $strResult;
    }

    /**
     * Преобразует значение, которое поялвяется в результате отправки блока из формы.<br/>
     * В начальном варианте структура похожа на [0 => ... , 0_0 => ...] .<br/>
     * Преобразованный имеет вид ['CLASS_ID' => ..., 'DATA' => ...]
     *
     * @param $arCondition
     * @param $intStoreId
     *
     * @return array|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function parseCondition($arCondition, $intStoreId)
    {
        $obCond = new \Local\Core\Inner\Condition\CondTree();
        $obCond->setStoreId($intStoreId);
        $obCond->setRobofeedVersion( \Local\Core\Inner\Store\Base::getLastSuccessImportVersion($intStoreId) );
        $obCond->Init(LOCAL_CORE_CONDITION_MODE_GENERATE, LOCAL_CORE_CONDITION_BUILD_CATALOG);
        return $obCond->Parse($arCondition);
    }

    /**
     * Формирует PHP правило проверки из преобразованного значения условия
     *
     * @param array $arParsedCondition
     * @param int $intStoreId
     * @package string $strVariableName Название переменной
     *
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function generatePhp($arParsedCondition, $intStoreId, $strVariableName = '#VARIABLE_NAME#')
    {
        $obCond = new \Local\Core\Inner\Condition\CondTree();
        $obCond->setStoreId($intStoreId);
        $obCond->setRobofeedVersion( \Local\Core\Inner\Store\Base::getLastSuccessImportVersion($intStoreId) );
        $obCond->Init(LOCAL_CORE_CONDITION_MODE_GENERATE, LOCAL_CORE_CONDITION_BUILD_CATALOG);
        return $obCond->Generate($arParsedCondition, ['FIELD' => $strVariableName]);
    }
}