<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Text extends Base
{

    private $_placeholder = null;

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return "<input type=\"text\" name=\"{$this->getCode()}\" value=\"" . htmlspecialcharsbx($this->getValue()) . "\" size=\"40\" placeholder=\"{$this->getPlaceholder()}\" />";
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return htmlspecialcharsbx($this->getValue());
    }

    public function setPlaceholder($placeholder){
        $this->_placeholder = $placeholder;
        return $this;
    }

    public function getPlaceholder(){
        return $this->_placeholder;
    }
}
