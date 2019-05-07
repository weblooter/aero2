<?

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Селектор
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class Select extends AbstractField
{
    use Traits\Size;
    use Traits\Options;

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
        $strInput = '<div class="mb-4"><select class="select2" name="'.$this->getName().($this->getIsMultiple() ? '[]' : '').'"';
        $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
        $strInput .= ($this->getIsMultiple()) ? ' multiple' : '';
        $strInput .= ($this->getIsReadOnly()) ? ' readonly' : '';
        $strInput .= ($this->getIsMultiple() ? ' size="'.$this->getSize().'"  data-size="'.$this->getSize().'"' : '');

        // INFO Сделали в рамках нашей библиотеки
        $strInput .= ( !empty($this->getDefaultOption()) ) ? ' data-placeholder="'.htmlspecialchars($this->getDefaultOption()).'"' : '';
        $strInput .= ( !empty($this->getIsCanSearch()) && sizeof($this->getOptions()) > 5 ) ? '' : ' data-minimum-results-for-search="Infinity"';
        // / INFO Сделали в рамках нашей библиотеки

        $strInput .= '>';

        if( !empty($this->getDefaultOption()) && !$this->getIsMultiple() )
        {
            $strInput .= '<option value="">'.$this->getDefaultOption().'</option>';
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

        $strInput .= '</select></div>';
        return $strInput;
    }

    protected function makeOption($v, $t)
    {
        return '<option '.(in_array($v, $this->getValue()) ? 'selected' : '').' value="'.htmlspecialchars($v).'">'.$t.'</option>';
    }

    /** @inheritDoc */
    public function isValueFilled($mixData)
    {
        $arOptionValues = [];
        foreach ($this->getOptions() as $k => $v)
        {
            if(is_array($v))
            {
                $arOptionValues = array_merge(array_keys($v), $arOptionValues);
            }
            else
            {
                $arOptionValues[] = $k;
            }
        }

        $boolRes = false;
        if( is_array($mixData) )
        {
            $mixData = array_diff($mixData, ['']);
            $boolRes = true;
            foreach ($mixData as $val)
            {
                if( !in_array($val, $arOptionValues) )
                {
                    $boolRes = false;
                    break;
                }
            }
        }
        else
        {
            $boolRes = in_array($mixData, $arOptionValues);
        }
        unset($arOptionValues);

        return $boolRes;
    }

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        $arOptionValues = [];
        foreach ($this->getOptions() as $k => $v)
        {
            if(is_array($v))
            {
                $arOptionValues = array_merge(array_keys($v), $arOptionValues);
            }
            else
            {
                $arOptionValues[] = $k;
            }
        }

        $mixExtra = null;
        if( is_array($mixData) )
        {
            $mixData = array_diff($mixData, ['']);
            foreach ($mixData as &$val)
            {
                $val = in_array($val, $arOptionValues) ? $val : '';
            }
            unset($val);
            $mixExtra = array_diff($mixData, ['']);
        }
        else
        {
            $mixExtra = in_array($mixData, $arOptionValues) ? $mixData : null;
        }
        unset($arOptionValues);

        return $mixExtra;
    }


    protected $_fieldIsCanSearch = true;

    /**
     * Разрешить поиск по списку.<br/>
     * Реализовано в рамках текущей библиотеки.
     *
     * @param $bool
     *
     * @return  $this
     */
    public function setIsCanSearch(bool $bool)
    {
        $this->_fieldIsCanSearch = $bool;
        return $this;
    }

    /**
     * Возвращает признак разрешения поиска по списку
     *
     * @return bool
     */
    public function getIsCanSearch()
    {
        return $this->_fieldIsCanSearch;
    }
}