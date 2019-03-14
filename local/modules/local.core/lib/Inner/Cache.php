<?php

namespace Local\Core\Inner;

/**
 * Класс по работе с кэшем
 *
 * @package Local\Core\Inner
 */
class Cache
{
    /**
     * Возвращает путь для сохранения кэша
     *
     * @param array $arDirPath Массив <b>последовательного</b> пути. Порядок ВАЖЕН!
     * @param array $arParams  Параметры, от которых зависит путь. К примеру в personal.company.list это
     *                         $GLOBALS['USER']->GetID(). Последовательность параметров важно соблюдать.
     *
     * @return string
     */
    public static function getCachePath(array $arDirPath, array $arParams = [])
    {

        $strPath = '/'.implode(
                '/',
                array_merge(
                    ['local.core'],
                    $arDirPath,
                    $arParams
                )
            ).'/';

        return $strPath;
    }

    /**
     * Удаляет кэш
     *
     * @param array $arDirPath Массив <b>последовательного</b> пути. Порядок ВАЖЕН!
     * @param array $arParams  Параметры, от которых зависит путь. К примеру в personal.company.list это
     *                         $GLOBALS['USER']->GetID(). Последовательность параметров важно соблюдать.
     */
    public static function deleteCache(array $arDirPath, array $arParams = [])
    {

        \BXClearCache(
            true,
            self::getCachePath(
                $arDirPath,
                $arParams
            )
        );
    }

    /**
     * Оберка \Local\Core\Inner\Cache::getCachePath() для компонента<br/>
     * Автоматически дописывает в начало $arDir <b>components</b>.<br/>
     * Подробное описание читай в \Local\Core\Inner\Cache::getCachePath()
     *
     * @param array $arDirPath
     * @param array $arParams
     *
     * @see \Local\Core\Inner\Cache::getCachePath()
     *
     * @return string
     */
    public static function getComponentCachePath(array $arDirPath, array $arParams = [])
    {

        return self::getCachePath(
            array_merge(
                ['components'],
                $arDirPath
            ),
            $arParams
        );
    }

    /**
     * Оберка \Local\Core\Inner\Cache::deleteCache() для компонента<br/>
     * Автоматически дописывает в начало $arDir <b>components</b>.<br/>
     * Подробное описание читай в \Local\Core\Inner\Cache::deleteCache()
     *
     * @param array $arDirPath
     * @param array $arParams
     *
     * @see \Local\Core\Inner\Cache::deleteCache()
     */
    public static function deleteComponentCache(array $arDirPath, array $arParams = [])
    {
        \BXClearCache(
            true,
            self::getComponentCachePath(
                $arDirPath,
                $arParams
            )
        );
    }

}