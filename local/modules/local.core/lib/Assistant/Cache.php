<?
namespace Local\Core\Assistant;

/**
 * Класс ассистент по работе с кэшем
 * @package Local\Core\Assistant
 */
class Cache
{
    /**
     * Возвращает путь для сохранения кэша компонента
     *
     * @param string $componentName Название компонента
     * @param array  $arParams Параметры, от которых зависит путь. К примеру в personal.company.list это $GLOBALS['USER']->GetID(). Последовательность параметров важно союблюдать.
     *
     * @return string
     */
    public static function getComponentCachePath(string $componentName, array $arParams = [])
    {

        $strPath = '/'.implode('/', array_merge( [ 'local.core', 'components', $componentName, ], $arParams ) ).'/';

        return $strPath;
    }

    /**
     * Удаляет кэш компонента
     *
     * @param string $componentName Название компонента
     * @param array  $arParams Параметры, от которых зависит путь. К примеру в personal.company.list это $GLOBALS['USER']->GetID(). Последовательность параметров важно союблюдать.
     */
    public static function deleteComponentCache(string $componentName, array $arParams = [])
    {
        $strPath = self::getComponentCachePath($componentName, $arParams);

        \BXClearCache(true, $strPath);
    }
}