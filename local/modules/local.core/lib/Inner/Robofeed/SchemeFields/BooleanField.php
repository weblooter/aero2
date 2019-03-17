<?php

namespace Local\Core\Inner\Robofeed\SchemeFields;


class BooleanField extends ScalarField
{
    /**
     * Value (false, true) equivalent map
     * @var array
     */

    protected $xml_expected_type = 'true, или false';

    protected $values = [
        'true',
        'false',
        'TRUE',
        'FALSE',
    ];

    protected $valuesConnection = [
        'true' => 'Y',
        'false' => 'N',
        'TRUE' => 'Y',
        'FALSE' => 'N',
    ];

    function __construct($name, $parameters = array())
    {
        parent::__construct($name, $parameters);
    }

    public function getValidators()
    {
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
                    if( !in_array($value, $this->values) )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($this, '', 'LOCAL_CORE_INVALID_VALUE');
                    }
                }

                return true;
            };
        return $validators;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getValidValue($mixEnterValue)
    {
        return $this->valuesConnection[$mixEnterValue];
    }
}