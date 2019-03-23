<?php

namespace Local\Core\Inner\Robofeed\SchemaFields;


class TextField extends StringField
{
    protected $size = 3000;
    protected $xml_expected_type = null;

    public function getValidators()
    {
        $validators = parent::getValidators();
        $validators[] = function($value, $primary, $row, $obField)
            {
                if( $this->htmlAccess )
                {
                    if( strlen($value) != strlen(strip_tags($value, '<h3><ul><li><p><br>')) )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE_CDATA_LIMIT');
                    }
                }

                return true;
            };

        return $validators;
    }

    public function getValidValue($mixEnterValue)
    {
        if( trim($mixEnterValue) === '' || is_null($mixEnterValue) )
        {
            $mixEnterValue = null;
        }
        else
        {
            $mixEnterValue = substr($mixEnterValue, 0, $this->size);
        }

        return $mixEnterValue;
    }
}