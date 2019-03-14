<?php

namespace Local\Core\Assistant;

use Bitrix\Main;

/**
 * Всё для \Exception и \Error и прочих Result
 * Class Throwable
 * @package Local\Core\Assistant
 */
class Throwable
{
    /**
     * Регистрирует битриксовый ExceptionHandler,<br>
     * который выполнится после завершения работы скрипта или при вызове функции exit().
     *
     * @param \Throwable|\Error|\Exception $e
     */
    public static function registerShutdown(\Throwable $e)
    {
        register_shutdown_function(
            function(\Throwable $e)
                {
                    \Bitrix\Main\Application::getInstance()->getExceptionHandler()->handleException($e);
                },
            $e
        );
    }

    /**
     * @param Main\Result $result
     * @param Main\Error[]
     * @param array       $arCustomData
     * @param null|string $file
     * @param null|int    $line
     */
    public static function addError(Main\Result $result, $arErrorCollection, $arCustomData = [], $file = null, $line = null)
    {
        if( $arErrorCollection instanceof Main\Error )
        {
            $arErrorCollection = [$arErrorCollection];
        }
        if( is_null($file) || is_null($line) )
        {
            $d = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $arCustomData['__info']['file'] = $file ?? $d[0]['file'];
        $arCustomData['__info']['line'] = $line ?? $d[0]['line'];
        $arCustomData['__info']['__:'] = $arCustomData['__info']['file'].':'.$arCustomData['__info']['line'];
        $arCustomData['__info']['__::'] = chr(13).str_replace(
                $_SERVER['DOCUMENT_ROOT'],
                '',
                $arCustomData['__info']['file'].':'.$arCustomData['__info']['line']
            );

        foreach( $arErrorCollection as $error )
        {
            /** @var Main\Error $error */
            $custom = $error->getCustomData();
            if( !is_null($custom) )
            {
                if( is_array($custom) )
                {
                    $arCustomData = array_merge(
                        $error->getCustomData() ?? [],
                        $arCustomData
                    );
                }
                else
                {
                    $arCustomData['__data'] = $custom;
                }
            }
            if( $error instanceof Main\Error )
            {
                $result->addError(
                    new Main\Error(
                        $error->getMessage(), $error->getCode(), $arCustomData
                    )
                );
            }
            else
            {

                p([$arErrorCollection, $arCustomData]);
            }
        }
    }

    /**
     * @internal
     * Метод будет меняться, пока не использовать
     *
     * @param Main\Result          $result
     * @param Main\ErrorCollection $errorCollection
     */
    public static function mergeErrorCollection(Main\Result $result, Main\ErrorCollection $errorCollection)
    {
        foreach( $errorCollection as $error )
        {
            /** @var Main\Error $error */
            $result->addError(
                new Main\Error(
                    $error->getMessage(), $error->getCode(), $error->getCustomData()
                )
            );
        }
    }
}
