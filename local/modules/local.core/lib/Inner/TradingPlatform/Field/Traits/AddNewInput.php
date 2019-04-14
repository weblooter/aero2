<?
namespace Local\Core\Inner\TradingPlatform\Field\Traits;

trait AddNewInput
{
    /** @var bool $_fieldIsCanAddNewInput Признак возможности добавить еще одно поле */
    protected $_fieldIsCanAddNewInput = false;

    /**
     * Задает признак возможности добавить еще одно поле.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsCanAddNewInput(bool $bool)
    {
        $this->_fieldIsCanAddNewInput = $bool;
        return $this;
    }

    /**
     * Получить признак возможности добавить еще одно поле.
     *
     * @return bool
     */
    public function getIsCanAddNewInput()
    {
        return $this->_fieldIsCanAddNewInput;
    }

}