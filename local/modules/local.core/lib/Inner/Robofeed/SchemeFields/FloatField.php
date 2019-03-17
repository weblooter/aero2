<?php

namespace Local\Core\Inner\Robofeed\SchemeFields;


class FloatField extends ScalarField
{
    protected $scale;
    protected $xml_expected_type = 'число с плавующей точкой (float)';
    protected $size = 11;

    public function __construct($name, $parameters = array())
    {
        parent::__construct($name, $parameters);

        if( isset($parameters['scale']) )
        {
            $this->scale = intval($parameters['scale']);
        }

        if( !is_null($parameters['size']) )
        {
            $this->size = $parameters['size'];
        }

        $this->xml_expected_type .= ' длиной до '.$this->size.' символов';
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
                    if( preg_match('/^[\d]{1,}\.[\d]{1,}$/', $value, $matches) !== 1 )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                    }

                    $value = floatval($value);

                    if( !is_float($value) )
                    {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                    }

                    if( !is_null($this->scale) )
                    {
                        if( strlen(round($value, $this->scale)) > $this->size )
                        {
                            return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                        }
                    }
                    else
                    {
                        if( strlen($matches[1]) > $this->size )
                        {
                            return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                        }
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
        else
        {
            $mixEnterValue = floatval($mixEnterValue);

            if( !is_null($this->scale) )
            {
                $mixEnterValue = round($mixEnterValue, $this->scale);
            }
        }

        return $mixEnterValue;
    }
}