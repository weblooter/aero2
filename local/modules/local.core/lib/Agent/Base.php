<?php
namespace Local\Core\Agent;


abstract class Base
{
    abstract protected static function execute();

    final public static function init()
    {
        try {
            static::execute(...func_get_args());
        } catch (\Throwable $e) {
            \Local\Core\Assistant\Throwable::registerShutdown($e);
        }

        return static::return(...func_get_args());
    }

    static function return()
    {
        $arParams = func_get_args();
        $strParams = '';
        $arParams = array_map(function($val){
            if( is_scalar($val) )
            {
                if( is_string($val) )
                {
                    $val = '\''.str_replace(['\\', '\''], ['\\\\', '\\\''], $val).'\'';
                }
                elseif( is_bool($val) )
                {
                    $val = ($val)?'true':'false';
                }
                return $val;
            }
            else
                return null;
        }, $arParams);
        $arParams = array_diff($arParams, [null]);
        if( !empty($arParams) )
        {
            $strParams = implode(", ", $arParams);
        }
        return static::class.'::init('.$strParams.');';
    }
}
