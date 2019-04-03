<?php

namespace Local\Core\Assistant;


class Strings
{
    /**
     * Склонение существительных с числительными
     * int $n число
     * string $form1 Единственная форма: 1 секунда
     * string $form2 Двойственная форма: 2 секунды
     * string $form5 Множественная форма: 5 секунд
     * string Правильная форма
     */
    public static function pluralForm($n, $form1, $form2, $form5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) {
            return $form5;
        }
        if ($n1 > 1 && $n1 < 5) {
            return $form2;
        }
        if ($n1 == 1) {
            return $form1;
        }
        return $form5;
    }

}