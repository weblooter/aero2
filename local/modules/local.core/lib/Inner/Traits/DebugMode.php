<?php

namespace Local\Core\Inner\Traits;


/**
 * Трейд дебага
 *
 * @package Local\Core\Inner\Traits
 */
trait DebugMode
{
    /**
     * Объект дебага
     * @var \Local\Core\Inner\Debug $obDebug
     */
    protected $obDebug;

    /** @var bool $isDebugMode Признак редима дебага */
    protected $isDebugMode = false;

    /**
     * Включает или отключает режим дебага
     *
     * @param bool $boolSaveArgs Сохранять в дебаг аргументы или нет
     */
    public function initDebugMode($boolSaveArgs = false)
    {
        $this->isDebugMode = true;
        $this->obDebug = new \Local\Core\Inner\Debug($boolSaveArgs);
    }

    /**
     * Возвращает объект дебага
     *
     * @return \Local\Core\Inner\Debug
     */
    public function getDebug()
    {
        return $this->obDebug;
    }

    /**
     * Добавить в регистр запись
     *
     * @param string $strMess  Сообщение в дебаг
     * @param int    $intDepth Глубина трейста с обратной стороны
     */
    protected function addDebug($strMess, $intDepth = 0)
    {
        if ($this->isDebugMode) {
            $this->obDebug->add($strMess, $intDepth);
        }
    }

    /**
     * Выкидывает ошибку, записывая ее в дебаг
     *
     * @param $strExceptionClass
     * @param $strMessage
     */
    protected function throwException($strExceptionClass, $strMessage)
    {
        $this->addDebug($strMessage);
        throw new $strExceptionClass($strMessage);
    }
}