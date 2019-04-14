<?

namespace Local\Core\Inner\TradingPlatform\Field\Traits;



trait IsReadOnly
{
    /** @var bool $_fieldIsReadOnly Признак доступа только на чтение поля */
    protected $_fieldIsReadOnly = false;

    /**
     * Задать признак "только для чтения"
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setIsReadOnly(bool $bool = true)
    {
        $this->_fieldIsReadOnly = $bool;
        return $this;
    }

    /**
     * Получить признак "только для чтения"
     *
     * @return bool
     */
    public function getIsReadOnly()
    {
        return $this->_fieldIsReadOnly;
    }
}