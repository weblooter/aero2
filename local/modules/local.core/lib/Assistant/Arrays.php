<?php

namespace Local\Core\Assistant;

/**
 * Помощник по типам данных array
 * Class Arrays
 * @package Local\Core\Assistant\Scalar
 */
class Arrays
{
    /**
     * Очистить массив от ключей начинающихся знаком тильда (~)
     *
     * @param array $array \
     */
    public static function clearKeyTilda(array &$array)
    {
        $array = array_filter($array, function ($k)
            {
                return substr($k, 0, 1) != '~';
            }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Сдампить массив данных в строку предварительно упорядочив его по ключам.
     * Удобно использовать для обработки массивов параматеров для создания хеша.
     *
     * @param $data
     *
     * @return array|Strings
     */
    public static function dump($data)
    {
        if (!is_array($data)) {
            return json_encode($data);
        }

        ksort($data);

        return implode(':', array_keys($data)).'|'.implode(':', array_map(array(self, 'dump'), $data));
    }

    /**
     * Рекурсивно рреобразовать элементы массива в html-сущности
     *
     * @param $res
     *
     * @return array|mixed|Strings
     */
    public static function recursive_htmlspecialcharsbx($res)
    {
        if (is_array($res)) {
            foreach ($res as $key => $val) {
                $res[$key] = self::recursive_htmlspecialcharsbx($val);
            }
        } else {
            if (is_string($res)) {
                $res = htmlspecialcharsbx($res);
            }
        }
        return $res;
    }
}
