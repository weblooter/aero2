<?php

namespace Local\Core\Inner\TradingPlatform\Field;

/**
 * Текстовое поле
 *
 * @package Local\Core\Inner\TradingPlatform\Field
 */
class InputText extends AbstractField
{
    protected function execute()
    {
        if ($this->getIsMultiple()) {
            if (!is_array($this->getValue())) {
                $this->setValue([$this->getValue()]);
            }

            for ($i = 0; $i < (sizeof($this->getValue()) + $this->getAdditionalInputsCount()); $i++) {
                $strInput = '<div class="input-group mb-3"><input class="form-control" name="'.$this->getName().'[]"';

                $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
                $strInput .= (!empty($this->getPlaceholder())) ? ' placeholder="'.htmlspecialchars($this->getPlaceholder()).'"' : '';
                $strInput .= ($this->getIsRequired()) ? ' required' : '';
                $strInput .= ' value="'.htmlspecialchars($this->getValue()[$i]).'"';

                $strInput .= ' />';

                if ($this->getIsCanAddNewInput()) {
                    $strInput .= '<div class="input-group-append"><a href="javascript:void(0)" class="btn btn-warning">+</a></div>';
                }
                if( $i > 0 )
                {
                    $strInput .= '<div class="input-group-append"><a href="javascript:void(0)" class="btn btn-danger">-</a></div>';
                }
                $strInput .= '</div>';

                $this->addToRender($strInput);
            }
        } else {
            if (is_array($this->getValue())) {
                $this->setValue(implode(', ', $this->getValue()));
            }

            $strInput = '<input class="form-control" name="'.$this->getName().'"';

            $strInput .= (!empty($this->getEvent())) ? ' '.$this->getEventCollected() : '';
            $strInput .= (!empty($this->getPlaceholder())) ? ' placeholder="'.htmlspecialchars($this->getPlaceholder()).'"' : '';
            $strInput .= ($this->getIsRequired()) ? ' required' : '';
            $strInput .= ' value="'.htmlspecialchars($this->getValue()).'"';

            $strInput .= ' />';
            $this->addToRender($strInput);
        }
    }
}