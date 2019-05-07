<?php

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Текстовое поле
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class InputText extends AbstractField
{
    use Traits\Placeholder;
    use Traits\AddNewInput;
    use Traits\AdditionalInputsCount;

    /** @inheritDoc */
    protected function execute()
    {

        if ($this->getIsMultiple()) {
            if (!is_array($this->getValue()) && !is_null($this->getValue())) {
                $this->setValue([$this->getValue()]);
            }

            for ($i = 0; $i < (sizeof($this->getValue()) + $this->getAdditionalInputsCount()); $i++) {
                $strInput = '<div class="input-group mb-3">';
                $strInput .= $this->makeInput($this->getValue()[$i]);

                if ($this->getIsCanAddNewInput()) {
                    $strInput .= '<div class="input-group-append"><a href="javascript:void(0)" class="btn btn-warning">+</a></div>';
                }
                if ($i > 0) {
                    $strInput .= '<div class="input-group-append"><a href="javascript:void(0)" class="btn btn-danger" onclick="PersonalTradingplatformFormComponent.removeMultipleRow(this)">-</a></div>';
                }
                $strInput .= '</div>';

                $this->addToRender($strInput);
            }
        } else {
            if (is_array($this->getValue())) {
                $this->setValue(implode(', ', $this->getValue()));
            }

            $this->addToRender($this->makeInput($this->getValue()));
        }
    }

    private function makeInput($value)
    {
        $strInput = '<input type="text" class="form-control'.( $this->getIsMultiple() ? '' : ' mb-3' ).'" name="'.$this->getName().($this->getIsMultiple() ? '[]' : '').'"';

        $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
        $strInput .= (!empty($this->getPlaceholder())) ? ' placeholder="'.htmlspecialchars($this->getPlaceholder()).'"' : '';
//        $strInput .= ($this->getIsRequired()) ? ' required' : '';
        $strInput .= ($this->getIsReadOnly()) ? ' readonly' : '';
        $strInput .= ' value="'.htmlspecialchars($value).'"';

        $strInput .= ' />';

        return $strInput;
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