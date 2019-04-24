<?php

namespace Local\Core\Inner\TradingPlatform\Field;


class Condition extends AbstractField
{
    use Traits\StoreId;

    protected function execute()
    {
        if (!empty($this->getValue()) && is_array($this->getValue())) {
            if (empty($this->getValue()['CLASS_ID'])) {
                $this->setValue(\Local\Core\Inner\Condition\Base::parseCondition($this->getValue(), $this->getStoreId()));
            }
        } else {
            $this->setValue([]);
        }

        $this->addToRender(\Local\Core\Inner\Condition\Base::getConditionBlock($this->getStoreId(), $this->getFormName(), $this->getRowHash().sha1(serialize($this->getValue())), $this->getName(),
            $this->getValue()));
    }

    private $_fieldFormName = 'tradingplatformform';

    public function setFormName($str)
    {
        $this->_fieldFormName = $str;
        return $this;
    }

    public function getFormName()
    {
        return $this->_fieldFormName;
    }

    /** @inheritDoc */
    public function isValueFilled($mixData){
        return true;
    }
}