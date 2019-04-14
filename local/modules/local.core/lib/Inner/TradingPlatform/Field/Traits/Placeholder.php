<?php
namespace Local\Core\Inner\TradingPlatform\Field\Traits;

trait Placeholder
{

    /** @var string $_fieldPlaceholder Placeholder поля, если таковой есть у поля */
    protected $_fieldPlaceholder;

    /**
     * Задать плейсхолдер поля
     *
     * @param string $str
     *
     * @return $this
     */
    public function setPlaceholder($str)
    {
        $this->_fieldPlaceholder = $str;
        return $this;
    }

    /**
     * Получить плейсхолдер поля
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->_fieldPlaceholder;
    }
}