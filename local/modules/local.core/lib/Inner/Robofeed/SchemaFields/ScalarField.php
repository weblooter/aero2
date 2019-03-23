<?php

namespace Local\Core\Inner\Robofeed\SchemaFields;


abstract class ScalarField extends \Bitrix\Main\ORM\Fields\ScalarField implements \Local\Core\Inner\Robofeed\Interfaces\ScalarField
{
    protected $xml_path;
    protected $xml_expected_type;

    public function __construct($name, array $parameters = array())
    {
        parent::__construct($name, $parameters);

        if( !empty($parameters['xml_path']) )
        {
            $this->xml_path = $parameters['xml_path'];
        }

        if( !empty($parameters['xml_description']) )
        {
            $this->xml_description = $parameters['xml_description'];
        }

        if( !empty($parameters['xml_expected_type']) )
        {
            $this->xml_expected_type = $parameters['xml_expected_type'];
        }
    }

    public function getXmlPath()
    {
        return $this->xml_path;
    }

    public function getXmlExpectedType()
    {
        return $this->xml_expected_type;
    }

    /**
     * @return array|Validators\Validator[]|callback[]
     * @throws \Bitrix\Main\ArgumentTypeException
     * @throws \Bitrix\Main\SystemException
     */
    public function getValidators()
    {
        $validators = parent::getValidators();
        return $validators;
    }

    public function getValidValue($mixEnterValue)
    {

        // TODO вернуть проверку на isRequired() по дефолту в этот класс.
        if( trim($mixEnterValue) === '' )
        {
            $mixEnterValue = null;
        }

        return $mixEnterValue;
    }

    public function cast($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convertValueFromDb($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     * @throws SystemException
     */
    public function convertValueToDb($value)
    {
        return $this->getConnection()
            ->getSqlHelper()
            ->convertToDbString($value);
    }
}