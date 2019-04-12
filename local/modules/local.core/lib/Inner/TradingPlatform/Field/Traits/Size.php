<?php

namespace Local\Core\Inner\TradingPlatform\Field\Traits;


trait Size
{
    /** @var int $_fieldSize Аттрибует size при множественном */
    protected $_fieldSize = 3;

    /**
     * Задать аттрибует size
     *
     * @param $int
     *
     * @return $this
     */
    public function setSize($int)
    {
        $this->_fieldSize = $int;
        return $this;
    }

    /**
     * Получить аттрибут size
     *
     * @return int
     */
    protected function getSize()
    {
        return $this->_fieldSize;
    }
}