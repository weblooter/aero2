<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Checkbox extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return InputType(
            "checkbox",
            $this->getCode(),
            "Y",
            htmlspecialcharsbx($this->getValue()),
            false
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return $this->getValue() == "Y" ? "Да" : "Нет";
    }

}
