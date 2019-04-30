<?

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Скрытое поле
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class InputHidden extends AbstractField
{
    /** @inheritDoc */
    protected function execute()
    {
        if ($this->getIsMultiple()) {
            if (!is_array($this->getValue())) {
                $this->setValue([$this->getValue()]);
            }

            for ($i = 0; $i < sizeof($this->getValue()); $i++) {
                $strInput = '<input type="hidden" name="'.$this->getName().'[]"';
                $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
                $strInput .= ' value="'.htmlspecialchars($this->getValue()[$i]).'"';
                $strInput .= ' />';

                $this->addToRender($strInput);
            }
        } else {
            if (is_array($this->getValue())) {
                $this->setValue(implode(', ', $this->getValue()));
            }

            $strInput = '<input type="hidden" name="'.$this->getName().'"';

            $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
            $strInput .= ' value="'.htmlspecialchars($this->getValue()).'"';

            $strInput .= ' />';
            $this->addToRender($strInput);
        }
    }

    /** @inheritDoc */
    public function getRow($htmlInputRender)
    {
        return $htmlInputRender;
    }

    /** @inheritDoc */
    public function isValueFilled($mixData)
    {
        $boolRes = false;
        if( is_array($mixData) )
        {
            $mixData = array_diff($mixData, ['']);
            if( !empty( $mixData ) && sizeof($mixData) > 0 )
            {
                $boolRes = true;
                foreach ($mixData as $strVal)
                {
                    if( !(bool)strlen( trim( $strVal ) ) ){
                        $boolRes = false;
                        break;
                    }
                }
            }
        }
        else
        {
            $boolRes = (bool)strlen( trim( $mixData ) );
        }

        return $boolRes;
    }

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        $mixExtract = null;
        if( is_array($mixData) )
        {
            foreach ($mixData as &$str)
            {
                $str = (string)(trim($str));
            }
            unset($str);
            $mixExtract = array_diff($mixData, ['']);
        }
        elseif( is_scalar($mixData) )
        {
            $mixExtract = (string)(trim($mixData));
        }

        return $mixExtract;
    }
}