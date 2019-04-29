<?php

namespace Local\Core\EventHandlers\Main;


class OnBeforeEventSend
{
    /**
     * Замена логических выраженией в шаблрне письма
     *
     * @param $arFields
     * @param $arTemplate
     */
    public static function executeCondition($arFields, &$arTemplate)
    {
        $arTemplate['MESSAGE'] = str_replace("\n", '__@rn@__', $arTemplate['MESSAGE']);
        preg_match_all('/(\{\{\{IF(.*?)\}\}\}(.*?)\{\{\{ENDIF\}\}\})/ux', $arTemplate['MESSAGE'], $arMatches);
        if (!empty($arMatches[0])) {
            $funGetValue = function ($v) use ($arFields)
                {
                    if (substr($v, 0, 1) == '#' && substr($v, -1, 1) == '#') {
                        $v = $arFields[substr(substr($v, 1), 0, -1)];
                    }

                    return $v;
                };

            for ($i = 0; $i < sizeof($arMatches[0]); $i++) {
                list($v1, $operator, $v2) = array_map('trim', str_getcsv($arMatches[2][$i], ';'));
                $v1 = $funGetValue($v1);
                $v2 = $funGetValue($v2);

                $boolCondition = null;
                switch ($operator) {
                    case '=':
                    case '==':
                        $boolCondition = ($v1 == $v2);
                        break;
                    case '>':
                        $boolCondition = ($v1 > $v2);
                        break;
                    case '>=':
                        $boolCondition = ($v1 >= $v2);
                        break;
                    case '<':
                        $boolCondition = ($v1 < $v2);
                        break;
                    case '<=':
                        $boolCondition = ($v1 <= $v2);
                        break;
                    case '!=':
                        $boolCondition = ($v1 != $v2);
                        break;
                }

                if ($boolCondition) {
                    $arTemplate['MESSAGE'] = str_replace($arMatches[0][$i], $arMatches[3][$i], $arTemplate['MESSAGE']);
                } else {
                    $arTemplate['MESSAGE'] = str_replace($arMatches[0][$i], '', $arTemplate['MESSAGE']);
                }
            }
        }

        $arTemplate['MESSAGE_PHP'] = null;
        $arTemplate['MESSAGE'] = str_replace('__@rn@__', "\n", $arTemplate['MESSAGE']);
    }
}