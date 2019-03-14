<?php

namespace Local\Core\Assistant\Scalar;

use Bitrix\Main;
use Bitrix\Sale;

/**
 * Помощник по типам данных строка
 * Class Strings
 * @package Local\Core\Assistant\Scalar
 */
class Strings
{
    /**
     * <b>camelCase</b> to  <b>ALL_UPPER_CASE_WITH_UNDERSCORE_SEPARATORS</b>
     *
     * @param $str
     *
     * @return string
     */
    public static function comfort2required($str)
    {
        return strtoupper(Main\ORM\Entity::camel2snake($str));
    }

    /**
     * <b>ALL_UPPER_CASE_WITH_UNDERSCORE_SEPARATORS</b> to <b>camelCase</b>
     *
     * @param $str
     *
     * @return mixed
     */
    public static function required2comfort($str)
    {
        return Main\ORM\Entity::snake2Camel($str);
    }

    /**
     * 'SomeClass' => '\SomeClass'<br>
     * '\SomeClass' => '\SomeClass'<br>
     *
     * @param string $className
     *
     * @return string
     */
    public static function getAbsoluteClassName(string $className)
    {
        $className = trim($className);
        if(
            substr(
                $className,
                0,
                1
            ) !== '\\'
        )
        {
            $className = '\\'.$className;
        }
        return $className;
    }

    /**
     * Возвращает строку, идентичную функции mysql PASSWORD()
     *
     * @param $string
     *
     * @return string
     */
    public static function getMysqlPassword($string)
    {
        return '*'.strtoupper(
                sha1(
                    sha1(
                        $string,
                        true
                    )
                )
            );
    }

    /**
     * Проверяет e-mail на корректность по правилам Битрика.<br>
     * <b>Никак</b> не связано с проверкой уникальность e-mail.
     *
     * @param $emailString
     *
     * @return bool
     */
    public static function checkEmailByBitrixRules($emailString)
    {
        if( strlen($emailString) < 3 )
        {
            return false;
        }

        if(
        !check_email(
            $emailString,
            true
        )
        )
        {
            return false;
        }

        return true;

    }

    /**
     * Явлется ли логин номером телефона.
     *
     * @param $login
     *
     * @return bool
     */
    public static function loginIsNotPhoneNumber($login)
    {
        return !self::loginIsPhoneNumber($login);
    }

    /**
     * Явлется ли логин номером телефона.<br>
     * Есть же телефоны вида "7+7909XXXXXXX"...
     * @TODO Возможно потребуется усложнение алгоритма
     *
     * @param $login
     *
     * @return bool
     */
    public static function loginIsPhoneNumber($login)
    {
        $login = str_replace(
            '+',
            '',
            trim($login)
        );

        if(
            substr(
                $login,
                0,
                1
            ) == '8'
            && strlen($login) == 11
            && is_numeric($login)
        )
        {
            return true;
        }

        if(
            substr(
                $login,
                0,
                1
            ) == '7'
            && strlen($login) == 11
            && is_numeric($login)
        )
        {
            return true;
        }

        if(
            substr(
                $login,
                0,
                1
            ) == '375'
            && strlen($login) == 12
            && is_numeric($login)
        )
        {
            return true;
        }

        return false;
    }

    /**
     * Приводит телефонные номера к виду:
     * <ul>
     * <li>7XXXYYYWWZZ - Россия</li>
     * <li>375XXYYYWWZZ - Беларусь</li>
     * </ul>
     *
     * @param string $string
     *
     * @return null|string
     */
    public static function formatPhoneNumber(string $string): ?string
    {
        $formatted = trim($string);
        if(
            substr(
                $formatted,
                0,
                1
            ) == '+'
        )
        {
            $formatted = substr(
                $formatted,
                1
            );
        }

        if(
            substr(
                $formatted,
                0,
                1
            ) == '8'
        )
        {
            $formatted = '7'.substr(
                    $formatted,
                    1
                );
        }

        if(
            substr(
                $formatted,
                0,
                1
            ) == '7'
            && strlen($formatted) == 11
        )
        {
            return $formatted;
        }

        if(
            substr(
                $formatted,
                0,
                3
            ) == '375'
            && strlen($formatted) == 12
        )
        {
            return $formatted;
        }

        return null;
    }

    /**
     * Нормализует значение внутренних баллов
     *
     * @param $value
     *
     * @return float
     * @throws Main\ArgumentNullException
     */
    public static function normalizeInnerBonusValue($value)
    {
        $value = str_replace(
            ',',
            '.',
            trim($value)
        );
        return Sale\PriceMaths::roundPrecision($value);
    }

    /**
     * Соединяет все переменные и преобразовывает в вид "/аргумент1/аргумент2/.../аргументN/"
     *
     * @param mixed ...$items
     *
     * @return string
     */
    public static function splitAsUri(...$items)
    {
        $string = "";

        if( !empty($items) )
        {
            $data = [];

            foreach( $items as $v )
            {
                if( strlen(trim($v)) > 0 )
                {
                    $data[] = $v;
                }
                else
                {
                    break;
                }
            }
            $string = '/'.join(
                    '/',
                    $data
                ).'/';
        }

        return $string;
    }
}
