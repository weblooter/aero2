<?php

namespace Local\Core\Inner;

/**
 * Простой класс дебаг с трейсом
 *
 * @package Local\Core\Inner
 */
class Debug
{
    /** @var array $arDebug Регистр дебага */
    private $arDebug = [];
    /** @var bool $boolSaveArgs Сохранять в дебаг аргументы или нет */
    public $boolSaveArgs;

    /**
     * Debug constructor.
     *
     * @param bool $boolSaveArgs Сохранять в дебаг аргументы или нет
     */
    public function __construct($boolSaveArgs = false)
    {
        $this->boolSaveArgs = $boolSaveArgs;
    }

    /**
     * Добавить в регистр запись
     *
     * @param string $strMessage Сообщение в дебаг
     * @param int    $intDepth   Глубина трейста с обратной стороны
     */
    public function add($strMessage, $intDepth = 0)
    {

        $ar = debug_backtrace();
        $ar = $ar[sizeof($ar) - 1 - $intDepth];

        $strCall = '';
        if( !empty($ar['class']) )
        {
            $strCall .= $ar['class'].$ar['type'];
        }
        if( !empty($ar['function']) )
        {
            $strCall .= $ar['function'].'()';
        }

        $arFields = [
            'MESS' => $strMessage,
            'CALL' => $strCall,
            //            'FILE' => $ar['file'].':'.$ar['line']
        ];

        if( $this->boolSaveArgs )
        {
            $arFields['ARGS'] = $ar['args'];
        }

        $this->arDebug[] = $arFields;
    }

    /**
     * Возвращает регистр дебага
     *
     * @return array
     */
    public function getDebug()
    {
        return $this->arDebug;
    }

    /**
     * Выводит регистр дебага
     */
    public function dump()
    {
        dump($this->arDebug);
    }

    /**
     * Возвращает используемую память в mb
     *
     * @return float
     */
    public static function getMemoryUsed()
    {
        return round(memory_get_usage() / 1024 / 1024, 3);
    }
}