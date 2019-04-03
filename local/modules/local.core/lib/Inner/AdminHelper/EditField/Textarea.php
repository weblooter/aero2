<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Textarea extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        $value = htmlentities($this->getValue(), ENT_QUOTES, "UTF-8");
        $value = htmlspecialcharsbx($value, ENT_QUOTES);

        return "<textarea name=\"{$this->getCode()}\" cols=\"75\" rows=\"8\" wrap=\"VIRTUAL\" >{$value}</textarea>";
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return (new \CBXSanitizer())->SanitizeHtml($this->getValue());
    }
}
