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

    static $extractDataPhpCache = [];

    /** @inheritDoc */
    public function extractValue($mixData, $mixAdditionalData = null)
    {
        $boolRes = false;
        $phpRule = null;
        $strHash = md5(serialize($mixData).'#'.$this->getStoreId());

        if( is_null( self::$extractDataPhpCache[ $strHash ] ) )
        {
            $arTmp = [];
            if (!empty($mixData) && is_array($mixData)) {
                if (empty($mixData['CLASS_ID'])) {
                    $arTmp = \Local\Core\Inner\Condition\Base::parseCondition($mixData, $this->getStoreId());
                }
                else{
                    $arTmp = $mixData;
                }
            } else {
                $arTmp = [];
            }

            self::$extractDataPhpCache[ $strHash ] = \Local\Core\Inner\Condition\Base::generatePhp($arTmp, $this->getStoreId(), '$mixAdditionalData');
        }

        return eval('return '.self::$extractDataPhpCache[ $strHash ].';');
    }
}