<?php

namespace Local\Core\Inner\AdminHelper\EditField;

class Date extends Base
{

    /**
     * {@inheritdoc}
     */
    public function getEditFieldHtml()
    {
        return CalendarDate( $this->getCode(), htmlspecialcharsbx( $this->getValue() ), "post_form", "20" );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFieldHtml()
    {
        return $this->getValue();
    }
}
