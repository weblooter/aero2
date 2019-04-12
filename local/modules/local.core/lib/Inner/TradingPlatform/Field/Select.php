<?

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Селектор
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class Select extends AbstractField
{
    use Traits\Size;

    /** @inheritDoc */
    protected function execute()
    {
        if (!is_array($this->getValue()) && !is_null($this->getValue())) {
            $this->setValue([$this->getValue()]);
        } elseif (is_null($this->getValue())) {
            $this->setValue([]);
        }

        $this->addToRender($this->makeSelect());
    }

    private function makeSelect()
    {
        $strInput = '<select class="form-control" name="'.$this->getName().($this->getIsMultiple() ? '[]' : '').'"';
        $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
        $strInput .= ($this->getIsMultiple()) ? ' multiple' : '';
        $strInput .= ($this->getIsReadOnly()) ? ' readonly' : '';
        $strInput .= ($this->getIsRequired()) ? ' required' : '';
        $strInput .= ($this->getIsMultiple() ? ' size="'.$this->getSize().'"' : '');
        $strInput .= '>';

        if (!empty($this->getDefaultOption()) && !$this->getIsMultiple()) {
            $strInput .= '<option '.(empty($this->getValue()) ? 'selected' : '').' disabled>'.$this->getDefaultOption().'</option>';
        }

        foreach ($this->getOptions() as $k => $v) {
            if( is_array($v) )
            {
                $strInput .= '<optgroup label="'.htmlspecialchars($k).'">';
                foreach ($v as $k1 => $v1)
                {
                    $strInput .= $this->makeOption($k1, $v1);
                }
                $strInput .= '</optgroup>';
            }
            else{
                $strInput .= $this->makeOption($k, $v);
            }
        }

        $strInput .= '</select>';
        return $strInput;
    }

    protected function makeOption($v, $t)
    {
        return '<option '.(in_array($v, $this->getValue()) ? 'selected' : '').' value="'.htmlspecialchars($v).'">'.$t.'</option>';
    }

    /** @var array $_fieldOptions Варианты значений */
    protected $_fieldOptions = [];

    /**
     * Задать варианты значений.<br/>
     * Пример заполнения:<br/>
     * <pre>
     * [
     *  'value1' => 'text value 1',
     *  'group_name_1' => [
     *     'value in group 1' => 'text value',
     *     'value in group 2' => 'text value',
     *   ]
     * ]
     * </pre>
     *
     * @param array $ar
     *
     * @return $this
     */
    public function setOptions(array $ar)
    {
        $this->_fieldOptions = $ar;
        return $this;
    }

    /**
     * Получить варианты значений
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->_fieldOptions;
    }

    /** @var string $_fieldDefaultOption Текст, выводимый в значении по умолчанию */
    protected $_fieldDefaultOption;

    /**
     * Задать текст значения по умолчанию
     *
     * @param $str
     *
     * @return $this
     */
    public function setDefaultOption($str)
    {
        $this->_fieldDefaultOption = $str;
        return $this;
    }

    /**
     * Получить текст значения по умолчанию
     *
     * @return string
     */
    protected function getDefaultOption()
    {
        return $this->_fieldDefaultOption;
    }

}