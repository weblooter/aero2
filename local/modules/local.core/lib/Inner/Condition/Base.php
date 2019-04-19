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
     */
    public static function getConditionBlock($intStoreId, $strFormId, $strConditionId, $strInputName, $arValue = [])
    {
        $strResult = '';

        $obCond = new \Local\Core\Inner\Condition\CondTree();
        $obCond->setStoreId($intStoreId);
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
     *
     * @return array|string
     */
    public static function parseCondition($arCondition)
    {
        $obCond = new \Local\Core\Inner\Condition\CondTree();
        $obCond->Init(LOCAL_CORE_CONDITION_MODE_GENERATE, LOCAL_CORE_CONDITION_BUILD_CATALOG);
        return $obCond->Parse($arCondition);
    }

    /**
     * Формирует PHP правило проверки из преобразованного значения условия
     *
     * @param $arParsedCondition
     *
     * @return mixed|string
     */
    public static function generatePhp($arParsedCondition)
    {
        $obCond = new \Local\Core\Inner\Condition\CondTree();
        $obCond->Init(LOCAL_CORE_CONDITION_MODE_GENERATE, LOCAL_CORE_CONDITION_BUILD_CATALOG);
        return $obCond->Generate($arParsedCondition, ['FIELD' => '#VARIABLE_NAME#']);
    }
}