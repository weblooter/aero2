<?php


namespace Local\Core\Inner\TradingPlatform\Field\Traits;


trait AdditionalInputsCount
{
    /** @var int $_intAdditionalInputsCount Кол-во дополнительных полей, если поле помечено как множественное */
    protected $_intAdditionalInputsCount = 2;

    /**
     * Задать кол-во дополнительных полей. Применяется если поле множественное
     *
     * @param $int
     *
     * @return $this
     */
    public function setAdditionalInputsCount($int)
    {
        $this->_intAdditionalInputsCount = $int;
        return $this;
    }

    /**
     * Получить кол-во дополнительных полей
     *
     * @return int
     */
    protected function getAdditionalInputsCount()
    {
        return $this->_intAdditionalInputsCount;
    }
}