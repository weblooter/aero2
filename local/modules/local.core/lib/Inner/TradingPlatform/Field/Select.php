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
        $strInput = '<select class="form-control mb-3" name="'.$this->getName().($this->getIsMultiple() ? '[]' : '').'"';
        $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
        $strInput .= ($this->getIsMultiple()) ? ' multiple' : '';
        $strInput .= ($this->getIsReadOnly()) ? ' readonly' : '';
//        $strInput .= ($this->getIsRequired()) ? ' required' : '';
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

    /** @inheritDoc */
    public function isValueFilled($mixData)
    {
        $arOptionValues = array_keys($this->getOptions());

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
}