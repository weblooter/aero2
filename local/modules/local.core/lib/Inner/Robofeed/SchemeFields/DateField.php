<?php

namespace Local\Core\Inner\Robofeed\SchemeFields;


class DateField extends ScalarField
{
    protected $xml_expected_type = 'дата в формате YYYY-MM-DD';

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
                    if( preg_match('/^((19[0-9]{2,2}|2[0-9]{3,3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01]))$/', trim($value)) !== 1 )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE_FORMAT_DATE');
                    }

                    if( $value != date_format(date_create($value), 'Y-m-d') )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE_FORMAT_DATE');
                    }
                }
                return true;
            };

        return $validators;
    }

    public function getValidValue($mixEnterValue)
    {
        if( $mixEnterValue === '' )
        {
            $mixEnterValue = null;
        }

        return $mixEnterValue;
    }
}