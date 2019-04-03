<?php

/**
 * Явдяется ли текущая площадка для разработчиков
 *
 * @return bool
 */
function isDev()
{
    return true;
}

/**
 * Выводит содержимое $value в более удобном виде.
 * Некий аналог print_r / print / echo
 *
 * @param      $value - Данные
 * @param bool $bHtml - Преобразование в HTML
 * @param bool $die   - Прервать выполнение сраницы
 */
function p($value, $bHtml = false, $die = false)
{
    if (is_bool($value)) {
        $value = 'bool: '.($value == true ? 'true' : 'false');
    }

    $sReturn = print_r($value, true);
    $debug_backtrace = debug_backtrace();

    /* php-cli */
    if (defined("STDIN")) {

        if (
            substr(ltrim($sReturn), 0, 1) === "*"
        ) {
            $pos = strpos($sReturn, "*");
            echo "\r".str_repeat(" ", 40);
            echo "\r".substr($sReturn, 0, $pos).substr($sReturn, $pos + 1);
        } else {
            echo "\r\n".$sReturn;
        }

        ob_flush();

    } else {

        if ($bHtml) {
            $sReturn = htmlspecialchars($sReturn);
        }

        echo "<pre data-source=\"".substr($debug_backtrace[0]["file"], strlen($_SERVER["DOCUMENT_ROOT"])).":".$debug_backtrace[0]["line"]
             ."\" style=\"text-align:left;overflow:auto; color: #000; background-color: white; border: 1px solid #CCC; padding: 5px; font-size: 12px; line-height: 18px; font-family: monospace;\">"
             .$sReturn."</pre>";

    }

    if ($die) {
        ob_get_flush();
        die();
    }
}