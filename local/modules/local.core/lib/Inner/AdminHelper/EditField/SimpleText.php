<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class SimpleText extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return $this->getValue();
    }
}
