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
}