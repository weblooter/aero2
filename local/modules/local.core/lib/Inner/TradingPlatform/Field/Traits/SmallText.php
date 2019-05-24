<?php
namespace Local\Core\Inner\TradingPlatform\Field\Traits;

trait SmallText
{

    /** @var string $_fieldPlaceholder Placeholder поля, если таковой есть у поля */
    protected $_fieldSmallText;

    /**
     * Задать маленький текст для поля, используется для эпилогов
     *
     * @param string $str
     *
     * @return $this
     */
    public function setSmallText($str)
    {
        $this->_fieldSmallText = $str;
        return $this;
    }

    /**
     * Получить маленький текст для поля
     *
     * @return string
     */
    public function getSmallText()
    {
        return $this->_fieldSmallText;
    }
}