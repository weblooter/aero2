<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class SelectMultiple extends Base
{
    private $variants = [];

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        $variants = [
            "reference"    => array_values($this->variants),
            "reference_id" => array_keys($this->variants),
        ];

        return SelectBoxMFromArray($this->getCode() . "[]", $variants, $this->getValue(), "", "");
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        $result = "";

        if (!empty($this->getValue())) {

            foreach ($this->getValue() as $value) {
                if (isset($this->variants[$value])) {
                    $result .= "{$this->variants[$value]}<br/>";
                }
            }
        }

        return $result;
    }

    /**
     * Устанавливает варианты значений
     *
     * @param array $variants
     *
     * @return $this
     */
    public function setVariants(array $variants)
    {
        $this->variants = $variants;

        return $this;
    }

}
