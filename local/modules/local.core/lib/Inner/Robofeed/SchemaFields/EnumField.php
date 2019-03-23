<?php

namespace Local\Core\Inner\Robofeed\SchemaFields;


//class EnumField extends \Bitrix\Main\ORM\Fields\EnumField
class EnumField extends ScalarField
{
    protected $values;
    protected $xml_expected_type;

    function __construct($name, $parameters = array())
    {

        if( !empty($parameters['values']) )
        {
            $this->values = $parameters['values'];
            $this->xml_expected_type = 'один из вариантов ('.implode(', ', $parameters['values']).')';
        }

        parent::__construct($name, $parameters);
    }

    public function getValidators()
    {
        /** @var \Local\Core\Inner\Robofeed\SchemaFields\EnumField $obField */
        $validators[] = function($value, $primary, $row, $obField)
            {
                if( $value === '' )
                {
                    if( $this->isRequired() )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($this, '', 'LOCAL_CORE_FIELD_IS_REQUIRED');
                    }
                }
                else
                {
                    $arFieldsVariantValues = $obField->getValues();
                    $arFieldsVariantValues = array_map('mb_strtoupper', $arFieldsVariantValues);

                    if( !in_array(mb_strtoupper($value), $arFieldsVariantValues) )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                    }
                }

                return true;
            };

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