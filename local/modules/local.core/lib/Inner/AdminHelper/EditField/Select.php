<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Select extends Base
{
    protected $variants = [];

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        $variants = [
            "reference" => array_values($this->variants),
            "reference_id" => array_keys($this->variants),
        ];

        return SelectBoxFromArray($this->getCode(), $variants, $this->getValue(), "Не выбрано", "");
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        $result = "";

        if (!empty($this->getValue())) {

            if (isset($this->variants[$this->getValue()])) {

                $result = "{$this->variants[$this->getValue()]}[{$this->getValue()}]";
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
