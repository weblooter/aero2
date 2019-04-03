<?php

namespace Local\Core\Inner\Robofeed\SchemaFields;


class StringField extends ScalarField
{
    /**
     * Shortcut for Regexp validator
     * @var null|string
     */

    private $arDisableASCIIChars = [
        "\x00",
        "\x01",
        "\x02",
        "\x03",
        "\x04",
        "\x05",
        "\x06",
        "\x07",
        "\x08",
        "\v",
        "\f",
        "\x0E",
        "\x0F",
        "\x10",
        "\x11",
        "\x12",
        "\x13",
        "\x14",
        "\x15",
        "\x16",
        "\x17",
        "\x18",
        "\x19",
        "\x1A",
        "\e",
        "\x1C",
        "\x1D",
        "\x1E",
        "\x1F",
    ];

    protected $format = null;

    /** @var int|null */
    protected $size = 255;

    protected $htmlAccess = false;

    function __construct($name, $parameters = array())
    {

        if (!empty($parameters['format'])) {
            $this->format = $parameters['format'];
        }

        if (isset($parameters['size']) && intval($parameters['size']) > 0) {
            $this->size = intval($parameters['size']);
        }

        if (is_bool($parameters['html'])) {
            $this->htmlAccess = $parameters['html'];
        }

        $this->xml_expected_type = 'строка длиной не более '.$this->size.' символов';
        if (!is_null($this->format)) {
            $this->xml_expected_type .= ' и соответствующее регулярному выражению '.$this->format;
        }

        parent::__construct($name, $parameters);
    }


    public function getValidators()
    {
        $validators[] = function ($value, $primary, $row, $obField)
            {
                if ($value === '') {
                    if ($this->isRequired()) {
                        return new \Bitrix\Main\ORM\Fields\FieldError($this, '', 'LOCAL_CORE_FIELD_IS_REQUIRED');
                    }
                } else {
                    if (strlen($value) > $this->size) {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                    }

                    if (!is_null($this->format)) {
                        if (preg_match($this->format, $value) !== 1) {
                            return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE');
                        }
                    }

                    if (
                        strlen($value) != strlen(str_replace($this->arDisableASCIIChars, '', $value))
                    ) {
                        return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE_TABOO_ASCII_CHARS');
                    }

                    if (!$this->htmlAccess) {
                        if (strlen($value) != strlen(strip_tags($value))) {
                            return new \Bitrix\Main\ORM\Fields\FieldError($obField, '', 'LOCAL_CORE_INVALID_VALUE_CANT_HTML');
                        }
                    }
                }

                return true;
            };

        return $validators;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getValidValue($mixEnterValue)
    {
        if ($mixEnterValue === '') {
            $mixEnterValue = null;
        } else {
            $mixEnterValue = substr($mixEnterValue, 0, $this->size);
        }

        $mixEnterValue = htmlspecialchars_decode($mixEnterValue, ENT_NOQUOTES);

        return $mixEnterValue;
    }
}