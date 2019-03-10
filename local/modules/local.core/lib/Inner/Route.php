<?

namespace Local\Core\Inner;

/**
 *
 * Класс рассчитан на создание путей по единому шаблону.<br/>
 * Для корректной работы в корне сайта требуется создать файл <b>localroutes.php</b> и объявить внутри массив
 * <b>$arLocalRoutes</b>.<br/> Структура массива и пример его реализации:<br/>
 * <pre>
 * $arLocalRoutes = [
 *   'company' => [
 *     'list' => '/personal/company/',
 *     'add' => '/personal/company/add/',
 *     'edit' => '/personal/company/#COMPANY_ID#/edit/',
 *     'delete' => '/personal/company/#COMPANY_ID#/delete/',
 *   ],
 * ];
 * </pre>
 *
 * @package Local\Core\Inner
 */
class Route
{
    /** @var array $__arLocalRoutes Хранилицище лоутров из файла */
    private static $__arLocalRoutes;

    /**
     * Извлекает роуты из файла и записывает в локальное хранилище.
     *
     * @return array
     */
    private static function __getLocalRoutes()
    {
        if ( is_null( self::$__arLocalRoutes ) )
        {
            require $_SERVER[ 'DOCUMENT_ROOT' ].'/localroutes.php';
            self::$__arLocalRoutes = $arLocalRoutes ?? [];
        }

        return self::$__arLocalRoutes;
    }

    /**
     * Получить путь по роуту
     *
     * @param string $key Ключ
     * @param string $action Действие
     * @param array  $arParams Массив параметров, которые должны заменить плейсзолдеры
     *
     * @return false|string
     */
    public static function getRouteTo( $key, $action, $arParams = [] )
    {
        $arLocalRoutes = self::__getLocalRoutes();

        $strReturn = str_replace( array_keys( $arParams ), array_values( $arParams ),
            $arLocalRoutes[ $key ][ $action ] );
        if ( strlen( $strReturn ) < 1 )
        {
            $strReturn = false;
        }

        return $strReturn;
    }
}