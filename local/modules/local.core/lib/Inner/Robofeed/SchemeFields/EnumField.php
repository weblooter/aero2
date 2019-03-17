<?php

namespace Local\Core\Inner\Robofeed\SchemeFields;


//class EnumField extends \Bitrix\Main\ORM\Fields\EnumField
class EnumField extends ScalarField
{
    protected $values;
    protected $xml_expected_type;

    function __construct($name, $parameters = array())
    {
        parent::__construct($name, $parameters);

        if( !empty($parameters['values']) )
        {
            $this->values = $parameters['values'];
            $this->xml_expected_type = 'один из вариантов ('.implode(', ', $parameters['values']).')';
        }
    }

    public function getValidators()
    {
        $validators = parent::getValidators();

        if( $this->validation === null )
        {
            /** @var \Local\Core\Inner\Robofeed\SchemeFields\EnumField $obField */
            $validators[] = function($value, $primary, $row, $obField)
                {
                    $isError = true;

                    $arFieldsVariantValues = $obField->getValues();
                    $arFieldsVariantValues = array_map('mb_strtoupper', $arFieldsVariantValues);

                    if( $this->isRequired() && in_array(mb_strtoupper($value), $arFieldsVariantValues) )
                    {
                        $isError = false;
                    }
                    else if( !$this->isRequired() && ( in_array(mb_strtoupper($value), $arFieldsVariantValues) || trim($value) == '' || is_null($value) ) )
                    {
                        $isError = false;
                    }

                    if( $isError )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                    }
                    else
                    {
                        return true;
                    }
                };
        }

        return $validators;
    }

    public function getValidValue($mixEnterValue)
    {
        if( $mixEnterValue == '' )
        {
            $mixEnterValue = null;
        }

        return $mixEnterValue;
    }

    public function getValues()
    {
        return $this->values;
    }
}