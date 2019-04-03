<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Hidden extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getRowHtml()
    {
        return ($this->isEditable === true) ? $this->getEditFieldHtml() : $this->getViewFieldHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return InputType("hidden", $this->getCode(), htmlspecialcharsbx($this->getValue()), "", false);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return "";
    }
}
